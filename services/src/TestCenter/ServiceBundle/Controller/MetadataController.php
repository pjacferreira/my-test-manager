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

  protected static $FIELD_DEFAULTS = array(
      /* 'type' - specifies the type of data contained in the field
       * Possible Values:
       * 'integer' - integer value
       * 'decimal' - deciman point value
       * 'text' - single line of text (DEFAULT)
       * 'password' - like text, except keystrokes are masked
       * 'html' - HTML text
       * 'time' - time value
       * 'date' - date value
       * 'datetime' - combination of date and time
       * 'boolean' - true or false values
       */
      'type' => 'text',
      /* 'virtual' - Specified that the field is virtual (i.e. no equivalent field
       * in the backend data store. Only used for scratch/temporary fields)
       * Possible Values:
       * 'true' - virtual
       * 'false' - not virtual (DEFAULT)
       */
      'virtual' => false,
      /* 'data-direction' - specifies in which direction data is transmitted (relative to the data store)
       * Possible Values:
       * 'out'  - outcoming (read-only field)
       * 'both' - bidirectional (read-write field) (DEFAULT)
       * 'none' - not used in data comunication
       */
      /* TODO How to handle a situation in which we don't want the field
       * Editable, and don't want to synchronize (example a Virtual Field
       * just to display a value (maybe even modifiable, through dependency on
       * other fields), but is not editable by the user
       * Possible Solutions (split read / write, and data synchronization, into
       * different properties)
       */
      'data-direction' => 'both',
      /* 'max-length' - Maximum Number of Characters contained in the field (DEFAULT : 0)
       */
      'max-length' => 0,
      /* 'default' - Default Value to Use
       */
      'default' => null,
      /* 'trim' - trim the value before validation
       * Possible Values:
       * 'true' - yes (trim both right and left)
       * 'false' - no
       * 'right' - trim only leading whitespaces
       * 'left' - trim only trailing whitespaces
       */
      'trim' => true,
      /* 'empty' - how to handle empty strings?
       * Possible Values:
       * 'as-empty' - treat as empty string
       * 'as-null' - treat as null
       */
      'empty' => 'as-null',
      /* 'nullable' - can contain NULL values
       * Possible Values:
       * 'true'  - yes
       * 'false' - no
       */
      'nullable' => true,
      /* 'label' - Default Label to be used (if no other provided)
       */
      'label' => null,
      /* 'description' - Default Description to be used (if no other provided)
       */
      'description' => null,
      /* 'key' - Can field be used as a KEY for the record
       */
      'key' => false
  );

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
  public function fieldAction($id) {
    // Create Action Context
    $context = new ActionContext('field');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
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
    $context = new ActionContext('service');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
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
  public function actionAction($id) {
    // Create Action Context
    $context = new ActionContext('action_meta');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param type $list
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function actionsAction(Request $request, $list = null) {
    // Create Action Context
    $context = new ActionContext('actions_meta');

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

    $metadata = $this->buildTableMetadata($id);
    if (isset($metadata)) {
      list($table, $variation) = $this->explodeTableID($id);
      return array("{$table}:{$variation}" => $metadata);
    }

    return null;
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
    return isset($id) && is_string($id) ? $this->buildFormMetadata($id) : null;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doFieldAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Fields Metadata
    $id = $context->getParameter('id');
    assert('isset($id)');

    list($field, $metadata) = $this->getFieldMetadata($parameters['id']);

    // Resolve any Inheritance Issues
    if (isset($metadata) && array_key_exists('inherit', $metadata)) {
      $inherit = $this->resolveFieldInheritance($metadata['inherit']);
      if (isset($inherit)) { // Mixin the Inherited Values
        $metadata = $this->mixin($inherit, $metadata);
        unset($metadata['inherit']);
      } else { // Remove the Field from the Metadata
        $metadata = null;
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doFieldsAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Fields Metadata
    $list = $context->getParameter('list');
    assert('isset($list)');

    /**
     * How to Build MetaModel for an Entity
     * 1. Entity Manager, build the basic MetaModel fields (length, data directions, etc.)
     * 2. Search for a YAML file, for the entity, so as to suplement or modify the
     *    the previously built metadata.
     * 3. (TODO) Search for languange specific YAML, to allow for i18n of the model.
     * 4. (CACHE) The previously built Metadata, so as to not to have to go through this process again, 
     *    unless a change has been made.
     */
    // 1st Load Only Metadata for Entities
    $fields = array();
    $entities = array();
    for ($i = 0; $i < count($list); $i++) {
      $entry = $list[$i];

      // Is the Entry a Field
      if (stripos($entry, ':') !== FALSE) { // Field (Save it for Later and Just Continue)
        $fields[] = $entry;
        continue;
      }

      // Entry is an Entity (So Load it's Data and Continue)
      $data = $this->getEntityMetadata($entry);
      if (isset($data)) {
        $entities[$entry] = $data;
      }
    }

    /* Squash the entity name, and field name together to make it easier to work
     * with, and to return those values
     */
    $metadata = count($entities) ? $this->squashSingleLevel($entities) : array();

    // 2nd Load Data for Fields and Append it
    for ($i = 0; $i < count($fields); $i++) {
      $entry = $fields[$i];

      if (array_key_exists($entry, $metadata)) { // Information Already Loaded
        continue;
      }

      // Entry is an Entity (So Load it's Data and Continue)
      list($entry, $data) = $this->getFieldMetadata($entry);
      if (isset($data)) {
        $metadata[$entry] = $data;
      }
    }

    // 3rd Dereference Fields with 'inherit'
    // Build List of Fields that have inheritance dependencies
    $field = null;
    $with_inherit = array();
    foreach ($metadata as $key => $value) {
      $field = $metadata[$key];
      if (array_key_exists('inherit', $field)) {
        $with_inherit[$key] = $field['inherit'];
      }
    }

    // Resolve Multi Depth Field Inheritance
    foreach ($with_inherit as $key => $from) {
      // TODO Improve Performance by Removing Recursive Descent
      $inherit = $this->resolveFieldInheritance($from);
      if (isset($inherit)) { // Mixin the Inherited Values
        $metadata[$key] = $this->mixin($inherit, $metadata[$key]);
        unset($metadata[$key]['inherit']);
      } else { // Remove the Field from the Metadata
        unset($metadata[$key]);
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $context
   * @return null
   */
  protected function doServiceAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Service ID
    $id = $context->getParameter('id');

    // Build the Service Metadata
    return isset($id) && is_string($id) ? $this->buildServiceMetadata($id) : null;
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
      $metadata = isset($id) && is_string($id) ? $this->buildServiceMetadata($id) : null;

      if (isset($metadata)) {
        $services[$id] = $metadata;
      }
    }

    return count($services) > 0 ? $services : null;
  }

  /**
   * 
   * @param type $id
   * @return null
   */
  protected function buildServiceMetadata($id) {
    assert('isset($id) && is_string($id)');

    // Get the Service Metadata
    list($entity, $action) = $this->explodeID($id);
    if (isset($entity)) {
      $metadata = $this->getServiceMetadata($entity, $action);
    }

    // Resolve any Inheritance Issues
    if (isset($metadata) && array_key_exists('inherit', $metadata)) {
      $inherit = $this->buildServiceMetadata($metadata['inherit']);
      if (isset($inherit)) { // Mixin the Inherited Values
        $metadata = $this->mixin($inherit, $metadata);
        unset($metadata['inherit']);
      } else { // Invalid Inherit (Just Ignore it)
        $metadata = null;
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doActionMetaAction($context) {

    return array(
        'user:create' => array(
            'label' => 'New User',
            'description' => 'Create New User',
            'display' => array(
                'form' => 'user:1'
            )
        )
    );
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doActionsMetaAction($context) {

    return array(
        'user:create' => array(
            'label' => 'New User',
            'description' => 'Create New User',
            'display' => array(
                'form' => 'user:form.1'
            ),
        ),
        'user:read' => array(
            'label' => 'Detail',
            'description' => 'Detailed User Information',
            'datasource' => array(
                'url' => array(
                    'base' => 'user/read',
                    'positional' => array('user:id')
                ),
            ),
            'display' => array(
                'form' => 'user:form.1'
            ),
        ),
        'user:update' => array(
            'label' => 'Update',
            'description' => 'Modify User Information',
            'datasource' => array(
                'url' => array(
                    'base' => 'user/update',
                    'positional' => array('user:id')
                ),
            ),
            'display' => array(
                'form' => 'user:form.1'
            ),
        ),
        'user:delete' => array(
            'label' => 'Delete',
            'description' => 'Delete User',
            'datasource' => array(
                'url' => array(
                    'base' => 'user/delete',
                    'positional' => array('user:id'),
                ),
            ),
            'display' => array(
                'message' => array(
                    'success' => 'Deleted {1} Users',
                    'failure' => 'Failed to delete User(s)'
                )
            ),
        )
    );
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
   * @param type $id
   * @return null
   */
  protected function buildTableMetadata($id) {
    assert('isset($id) && is_string($id)');

    // Get the Table Metadata
    list($table, $variation) = $this->explodeTableID($id);
    if (isset($variation)) {
      $metadata = $this->getTableMetadata($table, $variation);

      $table = isset($table) ? $table : 'table';
      $metadata = ArrayUtilities::deepExtract($metadata, array($table, $variation));
    }

    // Resolve any Inheritance Issues
    if (isset($metadata) && array_key_exists('inherit', $metadata)) {
      $inherit = $this->buildTableMetadata($metadata['inherit']);
      if (isset($inherit)) { // Mixin the Inherited Values
        $metadata = $this->mixin($inherit, $metadata);
        unset($metadata['inherit']);
      } else { // Invalid Inherit (Just Ignore it)
        $metadata = null;
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $id
   * @return null
   */
  protected function buildFormMetadata($id) {
    assert('isset($id) && is_string($id)');

    // Get the Form Metadata
    list($form, $action) = $this->explodeID($id);
    if (isset($action)) {
      $metadata = $this->getFormMetadata($form, $action);
    }

    // Resolve any Inheritance Issues
    if (isset($metadata) && array_key_exists('inherit', $metadata)) {
      $inherit = $this->buildFormMetadata($metadata['inherit']);
      if (isset($inherit)) { // Mixin the Inherited Values
        $metadata = $this->mixin($inherit, $metadata);
        unset($metadata['inherit']);
      } else { // Invalid Inherit (Just Ignore it)
        $metadata = null;
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $field
   * @return null
   */
  protected function resolveFieldInheritance($field) {

    // Get the Metadata for the Field in the Inheritance Chain
    list($field, $metadata) = $this->getFieldMetadata($field);
    if (isset($metadata) && array_key_exists('inherit', $metadata)) {
      // If the Inherited Field also has a Dependency, than Resolve That
      $dependency = $this->resolveFieldInheritance($metadata['inherit']);
      if (isset($dependency)) { // Found Dependency
        $metadata = $this->mixin($dependency, $metadata);
        unset($metadata['inherit']);
      } else { // Missing Dependency
        $metadata = null;
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $service
   * @return null
   */
  protected function resolveServiceInheritance($service) {

    // Get the Metadata for the Field in the Inheritance Chain
    list($service, $metadata) = $this->getServiceMetadata($service);
    if (isset($metadata) && array_key_exists('inherit', $metadata)) {
      // If the Inherited Field also has a Dependency, than Resolve That
      $dependency = $this->resolveServiceInheritance($metadata['inherit']);
      if (isset($dependency)) { // Found Dependency
        $metadata = $this->mixin($dependency, $metadata);
        unset($metadata['inherit']);
      } else { // Missing Dependency
        $metadata = null;
      }
    }

    return $metadata;
  }

  /**
   * 
   * @param type $field
   * @return null
   */
  protected function getFieldMetadata($field) {
    assert('isset($field) && is_string($field)');

    // Split the field into entity and name
    list($entity, $name) = explode(':', $field, 2);
    $entity = StringUtilities::nullOnEmpty($entity);
    $name = StringUtilities::nullOnEmpty($name);

    if (isset($entity) && isset($name)) {
      $metadata = $this->getEntityMetadata($entity);

      if (isset($metadata) && array_key_exists($name, $metadata)) {
        return array("{$entity}:{$name}", $metadata[$name]);
      }
    }

    return null;
  }

  /**
   * 
   * @param type $table
   * @param type $variation
   * @return type
   */
  protected function getTableMetadata($table, $variation) {
    $metadata = $this->entityMetadata('form', $form);
    return isset($metadata) && array_key_exists($variation, $metadata) ?
            $metadata[$variation] : null;
  }

  /**
   * 
   * @param type $form
   * @param type $action
   * @return type
   */
  protected function getFormMetadata($form, $action) {
    $metadata = $this->entityMetadata('form', $form);
    return isset($metadata) && array_key_exists($action, $metadata) ?
            $metadata[$action] : null;
  }

  /**
   * 
   * @param type $service
   * @param type $action
   * @return type
   */
  protected function getServiceMetadata($service, $action) {
    $metadata = $this->entityMetadata('service', $service);
    return isset($metadata) && array_key_exists($action, $metadata) ?
            $metadata[$action] : null;
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
      $metadataORM = isset($ormEntity) ? $this->extractORMMetadata($ormEntity) : null;

      // Build the Metadata from the YAML Files
      $metadataYAML = $this->parseYAML($basename);
      if (array_key_exists($metaEntity, $metadataYAML)) { // Found an Entry
        $metadataYAML = $metadataYAML[$metaEntity];
      }

      // Join the Metadata's
      $metadata = $this->mixin($metadataORM, $metadataYAML);

      // Make sure we have Defaults Setup
      foreach ($metadata as $key => $value) {
        if (array_key_exists('inherit', $value)) {
          // Don't add default to fields that inherit, as they will get them from inherited fields values
          continue;
        }

        $metadata[$key] = $this->mixin(self::$FIELD_DEFAULTS, $value);
      }

      // Cache the Information
      $this->cacheMetadata($basename, $metadata);
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
    // Get Entity Manager
    $em = $this->getDoctrine()->getEntityManager();
    assert('isset($em)');

    // Get ORM Metadata for Entity
    $ormMetadata = $em->getClassMetadata($ormEntity);
    if (isset($ormMetadata)) {
      $metadata = array();
      $fields = $ormMetadata->getFieldNames();
      for ($i = 0; $i < count($fields); $i++) { // Run through Fields
        $definition = array();
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
              $definition['max-length'] = $mapping['length'];
            }
        }

        // Nullable Flag
        $definition['nullable'] = $mapping['nullable'] ? true : false;

        // For Generated Fields the Data Direction is 'out' (read-only)
        if ($ormMetadata->isIdentifier($field) && !$ormMetadata->isIdentifierNatural()) {
          $definition['data-direction'] = 'out';
        }

        $metadata[$field] = $definition;
      }

      return count($metadata) ? $metadata : null;
    }

    return null;
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
          if (key_exists($key, $into) && is_array($into[$key]) && is_array($value)) { // Recursive Merge
            $into[$key] = $this->mixin($into[$key], $from[$key]);
          } else { // Just Append / Overwrite
            $into[$key] = $value;
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

    return array($entity, isset($variation) ? $variation : 'default');
  }

  protected function explodeTableID($id) {
    // Explode the ID (expected format [[table]:]variation)
    if (stripos($id, ':') === FALSE) {
      $table = null;
      $variation = StringUtilities::nullOnEmpty($id);
    } else {
      list($table, $variation) = explode(':', $id, 2);
      $table = StringUtilities::nullOnEmpty($table);
      $variation = StringUtilities::nullOnEmpty($variation);
    }

    return array($table, isset($variation) ? $variation : 'default');
  }

  protected function explodeFormID($id) {
    // Explode the ID (expected format [[form]:]action)
    if (stripos($id, ':') === FALSE) {
      $form = null;
      $action = StringUtilities::nullOnEmpty($id);
    } else {
      list($form, $action) = explode(':', $id, 2);
      $form = StringUtilities::nullOnEmpty($form);
      $action = StringUtilities::nullOnEmpty($action);
    }

    return array($form, isset($action) ? $action : 'default');
  }

}

?>
