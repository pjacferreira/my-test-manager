<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2015 Paulo Ferreira <pf at sourcenotes.org>
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
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
namespace controllers\usermode;

use api\controller\ActionContext;
use api\controller\BaseServiceController;
use \common\utility\Strings;
use \common\utility\ArrayUtilities;

/**
 * Description of MetadataController
 *
 * @author Paulo Ferreira
 */
class MetadataController extends BaseServiceController {

  /**
   * 
   * @param type $name
   * @return type
   */
  public function entity($name) {
    // Create Action Context
    $context = new ActionContext('entity');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('name', Strings::nullOnEmpty($name)));
  }

  protected function doEntityAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Do we have an Entity Name?
    $name = $context->getParameter('name');
    if (isset($name)) { // YES
      // Upper Case Ist Letter
      $name = ucfirst($name);

      // Does the Entity Class Exist?
      if (!class_exists($name, true)) { // NO
        throw new \Exception('Invalid Entity Name.', 1);
      }
    } else {// NO: name not provided
      throw new \Exception('Missing Entity Name.', 2);
    }

    // Create an Instance of the Phalcon Entity
    $entity = new $name;

    // Is the Entity an instance of Phalcon\Mvc\ModelInterface?
    if (!is_subclass_of($entity, 'Phalcon\Mvc\ModelInterface')) { // NO
      throw new \Exception('Invalid Entity Name.', 3);
    }

    // Create Metadata for Phalcon Entity
    $metadata = $this->buildMetadata($entity);

    // Return Metadata For Entity
    return $metadata;
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->sessionManager->checkInSession();
//    $this->sessionManager->checkLoggedIn();

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
   * @param type $entity
   * @return null
   */
  protected function buildMetadata($entity) {
    assert('isset($meta_entity) && is_object($meta_entity)');

    $metadata = array();

    $metadata['identity_column'] = $this->metadata->getIdentityField($entity);
    $metadata['key_columns'] = $this->metadata->getPrimaryKeyAttributes($entity);
    $metadata['non_key_columns'] = $this->metadata->getNonPrimaryKeyAttributes($entity);
    $metadata['all_columns'] = $this->metadata-> getAttributes($entity);
    $metadata['not_null_columns'] = $this->metadata->getNotNullAttributes($entity);
    $metadata['data_types'] = $this->metadata->getDataTypes($entity);
    $metadata['field_column_map'] = $this->metadata->getReverseColumnMap($entity);
    $metadata['column_field_map'] = $this->metadata->getColumnMap($entity);
    $metadata['bind'] = $this->metadata->getBindTypes($entity);
    return $metadata;
  }

  /**
   * 
   * @param type $into
   * @param type $from
   * @return type
   */
  protected function merge($into, $from) {

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
            if (key_exists($key, $into) && is_array($into[$key]) && $this->is_assoc($into[$key]) && is_array($value)) { // Recursive Merge
              $into[$key] = $this->merge($into[$key], $from[$key]);
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
   * @param type $array
   * @return type
   */
  protected function is_assoc($array) {
    return (bool) count(array_filter(array_keys($array), 'is_string'));
  }

  /**
   * 
   * @param type $id
   * @param type $default
   * @return type
   */
  protected function explodeID($id, $default = null) {

    // Validate Incoming Parameters
    if (isset($default)) {
      $default = Strings::nullOnEmpty($default);
    }

    if (isset($default)) {
      $default = strtolower($default);
    } else {
      $default = 'default';
    }

    // Explode the ID (expected format entity[:[variation]])
    if (stripos($id, ':') === FALSE) {
      $entity = Strings::nullOnEmpty($id);
    } else {
      list($entity, $variation) = explode(':', $id, 2);
      $entity = Strings::nullOnEmpty($entity);
      $variation = Strings::nullOnEmpty($variation);
    }

    return array(isset($entity) ? strtolower($entity) : $default, isset($variation) ? strtolower($variation) : $default);
  }

}

?>
