<?php

/* Test Center - Compliance Testing Application
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace TestCenter\ServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Library\StringUtilities;
use Library\ArrayUtilities;
use TestCenter\ServiceBundle\API\ActionContext;
use TestCenter\ServiceBundle\API\BaseServiceController;

/**
 * Description of MetadataController
 *
 * @author Paulo Ferreira
 */
class MetadataController extends BaseServiceController {

  /**
   * 
   * @param type $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function fieldAction($id) {
    // Create Action Context
    $context = new ActionContext('fields');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('list', array(StringUtilities::nullOnEmpty($id))));
  }

  /**
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param type $list
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function fieldsAction(Request $request, $list = null) {
    // Create Action Context
    $context = new ActionContext('fields');

    // 1st Use the Parameter
    if (isset($list)) {
      $list = StringUtilities::nullOnEmpty($list);
    }

    // 2nd Try the Request Get/Post Parameters
    if (!isset($list)) {
      $list = StringUtilities::nullOnEmpty($request->request->get('list'));
    }

    if (isset($list)) { // If we have a Field List (expand it to an array)
      $list = explode(',', $list);
    }

    return $this->doAction($context
                            ->setIfNotNull('list', $list));
  }

  /**
   * 
   * @param type $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function serviceAction($id) {
    // Create Action Context
    $context = new ActionContext('services');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('list', array(StringUtilities::nullOnEmpty($id))));
  }

  /**
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param type $list
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function servicesAction(Request $request, $list = null) {
    // Create Action Context
    $context = new ActionContext('services');

    // 1st Use the Parameter
    if (isset($list)) {
      $list = StringUtilities::nullOnEmpty($list);
    }

    // 2nd Try the Request Get/Post Parameters
    if (!isset($list)) {
      $list = StringUtilities::nullOnEmpty($request->request->get('list'));
    }

    if (isset($list)) { // If we have a Field List (expand it to an array)
      $list = explode(',', $list);
    }

    return $this->doAction($context
                            ->setIfNotNull('list', $list));
  }

  /**
   * 
   * @param type $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function formAction($id) {
    // Create Action Context
    $context = new ActionContext('form_model');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * 
   * @param type $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function tableAction($id) {
    // Create Action Context
    $context = new ActionContext('table_model');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doFieldsAction($context) {

    /* How is a fields Definition Built:
     * TOP:
     * - Split the field name into entity_id : field_id pair
     * - is the entity_id == 'virtual' ?
     * -- yes: virtual field : goto EXPAND META:
     * -- no: expected physical field
     * --- find entity in ORM Model
     * ---- found ORM Entity?
     * ----- no: invalid field
     * ----- yes: extract base field value information:
     * EXPAND META:
     * - find file containing entity meta
     * -- found?
     * --- no: invalid entity
     * --- yes:
     * ---- find field in entity meta:
     * ----- found?
     * ------ no: invalid field
     * ------ yes:
     * ------- extract field meta definition
     * ------- merge extracted information back into any previously created
     *         meta information
     * PROCESS INHERITANCE
     * - does field have inheritance?
     * -- no: done
     * -- yes:
     * --- get meta definition of inheritance field (basically goto to TOP
     *     recurse for inherited field)
     * --- have inherited meta data?
     * ---- no: done
     * ---- yes: 
     * ----- merge extracted information back into any previously created
     *       meta information
     */
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Fields Metadata
    $list = $context->getParameter('list');
    assert('isset($list)');

    /* Stage 1: Build a List of Fields and remove any that can't be initially solved
     * Tasks
     * - Split the Field Name into it's components (entity_id : field_id)
     * - Find the Entity in the ORM Model
     */
    $fields = array();
    $entities = array();
    for ($i = 0; $i < count($list); $i++) {
      // Split Field Name into it's 2 components (entity_id : field_id)
      list($entity, $field) = array_map(function ($e) {
                return strtolower(StringUtilities::nullOnEmpty($e));
              }, explode(':', $list[$i], 2));

      // Find Entity in the ORM Model
      if (isset($entity) && isset($field)) {
        if (($entity != 'virtual') &&
                !array_key_exists($entity, $entities)) {
          $data = $this->getEntityMetadata($entity);
          if (isset($data)) {
            $entities[$entity] = $data;
            $fields[] = array($entity, $field);
          } else {
            // TODO Log the fact that the entity does not exits
          }
        } else {
          $fields[] = array($entity, $field);
        }
      } else {
        // TODO Log the fact that the field name has an invalid format
      }
    }

    /* Stage 2: Build Field Metadata
     * Tasks:
     * - Extract Metadata from Files
     * - Merge with ORM Metadata (if not VIRTUAL)
     */
    $meta = array();
    for ($i = 0; $i < count($fields); $i++) {
      $entity_id = $fields[$i][0];
      $field_id = $fields[$i][1];
      $field_name = $entity_id . ':' . $field_id;

      // Retrieve ORM Meta for Non-Virtual Fields
      if ($entity_id != 'virtual') {
        $orm_entity_meta = array_key_exists($field_id, $entities[$entity_id]) ? $entities[$entity_id][$field_id] : null;
        if (!isset($orm_entity_meta)) {
          // TODO Log that the Field does not have an ORM Entry
          continue;
        }
      } else {
        $orm_entity_meta = null;
      }

      $field_meta = $this->buildMetadata('field', $field_name);
      if (isset($field_meta)) {
        $meta[$field_name] = $this->mixin($orm_entity_meta, $field_meta);
      } else {
        // TODO Log the fact that the field has no metadata information
      }
    }

    return $meta;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doServicesAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Services List
    $list = $context->getParameter('list');
    assert('isset($list)');

    // Build an Array Containing the list of the Services
    $services = array();
    for ($i = 0; $i < count($list); $i++) {
      // Get the Service ID
      $id = $list[$i];

      // Get the Metadata for the service
      $metadata = isset($id) && is_string($id) ? $this->buildMetadata('service', $id) : null;

      if (isset($metadata)) {
        $services[$id] = $metadata;
      }
    }

    return count($services) > 0 ? $services : null;
  }

  /**
   * 
   * @param type $parameters
   * @return null
   */
  protected function doFormModelAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Form ID
    $id = $context->getParameter('id');

    // Build the Form Metadata
    return isset($id) && is_string($id) ? $this->buildMetadata('form', $id) : null;
  }

  /**
   * 
   * @param type $parameters
   * @param type $testset
   * @return type
   */
  protected function doTableModelAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Table Model
    $id = $context->getParameter('id');
    assert('isset($id)');

    // Build the Form Metadata
    return isset($id) && is_string($id) ? $this->buildMetadata('table', $id) : null;
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    return null;
  }

  /**
   * @param $action
   * @param $results
   * @param $format
   */
  protected function preRender($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    return $context->getActionResult();
  }

  /**
   * 
   * @param type $type
   * @param type $id
   * @return null
   */
  protected function buildMetadata($type, $id) {
    assert('isset($type) && is_string($type)');
    assert('isset($id) && is_string($id)');

    // Get the Metadata
    list($entity, $variation) = $this->explodeID($id);
    if (isset($entity)) {
      $metadata = $this->getMetadata($type, $entity, $variation);
    }

    // Resolve any Inheritance Issues
    if (isset($metadata) &&
            array_key_exists('inherit', $metadata) &&
            isset($metadata['inherit'])) {
      $inherit = $this->buildMetadata($type, $metadata['inherit']);
      if (isset($inherit)) { // Mixin the Inherited Values
        $metadata = $this->mixin($inherit, $metadata);
        unset($metadata['inherit']);
      } else { // Invalid Inherit (Just Ignore it)
        $metadata = null;
        // TODO Log Invalid Inherit
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $type
   * @param type $entity
   * @param type $variation
   * @return type
   */
  protected function getMetadata($type, $entity, $variation) {
    assert('isset($type) && is_string($type)');
    assert('isset($entity) && is_string($entity)');
    assert('isset($variation) && is_string($variation)');

    $metadata = $this->entityMetadata($type, $entity);
    return isset($metadata) && array_key_exists($variation, $metadata) ?
            $metadata[$variation] : null;
  }

  /**
   * 
   * @param type $file
   * @return type
   */
  protected function loadMetadata($file) {
    $metadata = $this->loadFromCache($file, $this->yamlPath($file));
    if (!isset($metadata)) { // No Cached Data (Rebuild)
      // 2nd Try a Non-Cached File
      $metadata = $this->parseYAML($file);

      if (isset($metadata)) {
        // Cache the Information
        $this->cacheMetadata($file, $metadata);
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $type
   * @param type $entityName
   * @return type
   */
  protected function entityMetadata($type, $entityName) {
    /*
     * Try 1: Load From Specific File
     */
    $file = "meta.{$type}s.{$entityName}";
    $metadata = $this->loadMetadata($file);
    if (isset($metadata) && array_key_exists($entityName, $metadata)) {
      return $metadata[$entityName];
    }

    /*
     * Try 2: Load From a General Entity File
     */
    $file = "meta.{$type}s";
    $metadata = $this->loadMetadata($file);
    if (isset($metadata) &&
            array_key_exists("{$type}s", $metadata) &&
            array_key_exists($entityName, $metadata["{$type}s"])) {
      return $metadata["{$type}s"][$entityName];
    }

    /*
     * Try 3: Load General Metadata File
     */
    $file = "meta.defaults";
    $metadata = $this->loadMetadata($file);
    if (isset($metadata) &&
            array_key_exists("{$type}s", $metadata) &&
            array_key_exists($entityName, $metadata["{$type}s"])) {
      return $metadata["{$type}s"][$entityName];
    }

    return null;
  }

  /**
   * 
   * @param type $metaEntity
   * @return type
   */
  protected function getEntityMetadata($metaEntity) {
    // Try to Load Cached Data
    $basename = $this->entityBaseFilename($metaEntity);
    $metadata = $this->loadFromCache($basename, array(
        $this->yamlPath('routing.meta'),
        $this->yamlPath($basename),
        $this->entityPath($this->mapEntityToORM($metaEntity))
    ));
    if (!isset($metadata)) { // No Cached Data (Rebuild)
      // Get the Associated ORM Entity
      $ormEntity = $this->mapEntityToORM($metaEntity);
      // Has ORM Entity (Extract Basic Data from it)
      $metadata = isset($ormEntity) ? $this->extractORMMetadata($ormEntity) : null;
    }

    return $metadata;
  }

  /**
   * 
   * @param type $metaEntity
   * @return null
   */
  protected function mapEntityToORM($metaEntity) {
    $routing_map = $this->parseYAML('routing.meta');
    if (isset($routing_map) && array_key_exists($metaEntity, $routing_map)) { // Found an Entry
      return $routing_map[$metaEntity];
    }

    return null;
  }

  /**
   * 
   * @param type $ormEntity
   * @return null
   */
  protected function extractORMMetadata($ormEntity) {
    // TODO : Refactor this function to make it cleaner
    // Get Entity Manager
    $em = $this->getDoctrine()->getEntityManager();
    assert('isset($em)');

    // Get ORM Metadata for Entity
    $ormMetadata = $em->getClassMetadata($ormEntity);
    if (isset($ormMetadata)) {
      $metadata = array();
      // Extract Physical Fields
      $fields = $ormMetadata->getFieldNames();
      for ($i = 0; $i < count($fields); $i++) { // Run through Fields
        $definition = array(
            'type' => 'text',
            'length' => 0,
        );
        $field = $fields[$i];
        $mapping = $ormMetadata->getFieldMapping($field);
        if (!isset($mapping)) {
          continue; // Skip Field
        }

        // Extract Field Type (DEFAULT: TEXT)
        switch ($mapping['type']) {
          case 'bigint':
          case 'smallint':
          case 'integer':
            $definition['type'] = 'integer';
            break;
          case 'boolean':
            $definition['type'] = 'boolean';
            break;
          case 'datetime':
          case 'datetimetz':
            $definition['type'] = 'datetime';
            break;
          case 'date';
            $definition['type'] = 'date';
            break;
          case 'time';
            $definition['type'] = 'time';
            break;
          case 'float':
          case 'decimal':
            $definition['type'] = 'decimal';
            break;
          default:
            // Field Length (if Specified)
            if (array_key_exists('length', $mapping) && isset($mapping['length'])) {
              $definition['length'] = $mapping['length'];
            }
        }

        // Nullable Flag
        $definition['nullable'] = $mapping['nullable'] ? true : false;

        // Add Value Definition
        $metadata[$field] = array('value' => $definition);

        // For Generated Fields the Data Direction is 'in' (read-only)
        if ($ormMetadata->isIdentifier($field) && !$ormMetadata->isIdentifierNatural()) {
          $metadata['data-direction'] = 'in';
        } else {
          $metadata['data-direction'] = 'both';
        }
      }

      // Extract Relations
      $fields = $ormMetadata->getAssociationNames();
      for ($i = 0; $i < count($fields); $i++) { // Run through Fields
        $definition = array('type' => 'reference');
        $field = $fields[$i];
        $mapping = $ormMetadata->getAssociationMapping($field);
        if (!isset($mapping)) {
          // TODO LOG PROBLEM
          continue; // Skip Field
        }

        /* TODO: Refactor
         * This test here is too adhoc, we have to see how the associations are
         * setup so as to be able to extract (correctly) the fields involved
         */
        if (isset($mapping['joinColumns'])) { // Skip Over One Sided Relations
          $entity = $this->extractEntity($mapping['targetEntity']);
          if (!isset($entity)) {
            // TODO LOG PROBLEM
            continue; // Skip Field
          }
          $entity = strtolower($entity);

          $link_fields = $mapping['joinColumns'];
          if (count($link_fields) == 1) {
            $link_field = $link_fields[0]['referencedColumnName'];
            $definition['link'] = "{$entity}:{$link_field}";
          } else {
            // TODO : References Currently Only Allowe for a Single Reference Column (SHOULD LOG WARNING)
            continue;
          }

          // Add Value Definition
          $metadata[$field] = array('value' => $definition);
        }
      }

      return count($metadata) ? $metadata : null;
    }

    return null;
  }

  /**
   * 
   * @return type
   */
  protected function extractEntity($reference) {
    $parts = explode('\\', $reference);
    return count($parts) > 0 ? $parts[count($parts) - 1] : null;
  }

  /**
   * 
   * @param type $metaEntity
   * @return null
   */
  protected function parseYAML($basename) {
    // Get the Path for the YAML file
    $path = $this->yamlPath($basename);
    if (isset($path)) { // Have a Path
      // Create a YAML Parser
      $parser = new Parser();
      try {
        // Parse the Data File and Return the Metadata Definition
        return $parser->parse(file_get_contents($path));
      } catch (ParseException $e) {
        // TODO Log Parse Error        
      }
    }

    return null;
  }

  /**
   * 
   * @param type $metaEntity
   * @return null
   */
  protected function loadFromCache($basename, $sources) {
    // Get the Cache Directory
    $cache_dir = $this->getContainer()->getParameter("kernel.cache_dir");

    /* TODO Handle i18n variations fo the Metadata.
     * We can probably do this, adding a language/locale to the file name
     */

    // Build a File Path
    $path = isset($form) ? "{$cache_dir}/{$basename}.php" : "{$cache_dir}/{$basename}.php";
    if (file_exists($path)) {

      // See if We have a Newer Source File
      $newer = true;
      if (is_string($sources)) {
        $newer = $this->newerThan($path, StringUtilities::nullOnEmpty($sources));
      } else if (is_array($sources)) {
        for ($i = 0; $newer && $i < count($sources); $i++) {
          $newer = $this->newerThan($path, StringUtilities::nullOnEmpty($sources[$i]));
        }
      } else {
        $newer = true;
      }

      // If No Newer, than metadata from Cache
      if ($newer) {
        // Clear any Previous Metadata
        unset($__METADATA);

        // Include Cache File
        include $path;
        if (isset($__METADATA)) { // Extract the Cache Information
          return $__METADATA;
        }
      }
    }

    return null;
  }

  /**
   * 
   * @param type $basename
   * @param type $metadata
   * @return type
   */
  protected function cacheMetadata($basename, $metadata) {
    // Get the Cache Directory
    $cache_dir = $this->getContainer()->getParameter("kernel.cache_dir");

    /* TODO Handle i18n variations fo the Metadata.
     * We can probably do this, adding a language/locale to the file name
     */
    // Build a File Path
    $path = isset($form) ? "{$cache_dir}/{$basename}.php" : "{$cache_dir}/{$basename}.php";

    // Export the Metadata to a String
    $php_metadata = var_export($metadata, true);

    // Write the Cache File
    if (file_put_contents($path, "<?php\n \$__METADATA={$php_metadata}\n?>", LOCK_EX) === FALSE) {
      // TODO Log Error (Failed to Create File)
      // If the File Exists (Try to Delete as it might be faulty)
      if (file_exists($path) && (unlink($path) === FALSE)) {
        // TODO Log Error (Failed to Delete File)
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $source
   * @param type $compareTo
   * @return boolean
   */
  protected function newerThan($source, $compareTo) {
    $sourceTime = isset($source) ? filemtime($source) : FALSE;
    if ($sourceTime !== FALSE) {
      $compareToTime = isset($compareTo) ? filemtime($compareTo) : FALSE;
      if (($compareToTime === FALSE) || // No File to Compare To (therefore it's newer)
              ($sourceTime > $compareToTime)) { // $source is NEWER than $compareTo
        return true;
      }
    }

    return false;
  }

  /**
   * 
   * @param type $entity
   * @return null
   */
  protected function entityPath($entity) {
    try {
      $reflection = new \ReflectionClass($entity);
      $classFile = $reflection->getFileName();
      return $classFile;
    } catch (\Exception $e) { // Class Doesn't Exist
      return null;
    }
  }

  /**
   * 
   * @param type $filename
   * @return null
   */
  protected function yamlPath($basename) {
    $kernel = $this->get('kernel');
    try {
      return $kernel->locateResource('@TestCenterServiceBundle/Resources/config/' . "{$basename}.yml", true);
    } catch (\InvalidArgumentException $e) {
      return null;
    }
  }

  protected function getContainer() {
    return $this->get('kernel')->getContainer();
  }

  /**
   * 
   * @param type $value1
   * @param type $value2
   * @return type
   */
  protected function oneOf($value1, $value2) {
    return isset($value1) ? $value1 : $value2;
  }

  /**
   * 
   * @param type $entities
   * @return type
   */
  protected function squashSingleLevel($entities) {

    $result = array();
    if (isset($entities)) {
      foreach ($entities as $entity => $fields) {
        foreach ($fields as $field => $definition) {
          $result["$entity:$field"] = $definition;
        }
      }
    }

    return count($result) ? $result : null;
  }

  /**
   * 
   * @param type $into
   * @param type $from
   * @return type
   */
  protected function mixin($into, $from) {

    if (isset($into)) {
      if (isset($from)) {
        foreach ($from as $key => $value) {
          /* New Deep Merge Process
           * Reason:
           * 1. Allow us to remove keys from destination (the idea being that
           * if $from, contains a $keys, whose value is null then we remove
           * the same $key from $into (if it exists)
           */
          if (!isset($value)) { // Remove Element from $into if it exists
            if (key_exists($key, $into)) {
              unset($into[$key]);
            }
          } else { // Normal Merge Process
            if (key_exists($key, $into) && is_array($into[$key]) && is_array($value)) { // Recursive Merge
              $into[$key] = $this->mixin($into[$key], $from[$key]);
            } else { // Just Append / Overwrite
              $into[$key] = $value;
            }
          }
        }
      }
      return $into;
    } else if (isset($from)) {
      return $from;
    }

    return null;
  }

  /**
   * 
   * @param type $entity
   * @return type
   */
  protected function entityBaseFilename($entity) {
    $entity = StringUtilities::nullOnEmpty($entity);
    return $basename = isset($entity) ? "meta.{$entity}" : null;
  }

  protected function explodeID($id) {
    // Explode the ID (expected format entity[:[variation]])
    if (stripos($id, ':') === FALSE) {
      $entity = StringUtilities::nullOnEmpty($id);
    } else {
      list($entity, $variation) = explode(':', $id, 2);
      $entity = StringUtilities::nullOnEmpty($entity);
      $variation = StringUtilities::nullOnEmpty($variation);
    }

    return array(isset($entity) ? $entity : 'default', isset($variation) ? $variation : 'default');
  }

}

?>
