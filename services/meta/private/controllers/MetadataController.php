<?php

/* Test Center - Compliance Testing Application (Metadata Service)
 * Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
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

use \shared\controller\ActionContext;
use \shared\controller\BaseServiceController;
use \shared\utility\StringUtilities;
use \shared\utility\ArrayUtilities;

/**
 * Controller that Provides Access to Metadata
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class MetadataController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Return a Single Field's Metadata.
   * 
   * @param string $id Field ID
   * @return string HTTP Body Response
   */
  public function field($id) {
    // Create Action Context
    $context = new ActionContext('field');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * Return a Multiple Fields' Metadata.
   * 
   * @param string $list OPTIONAL Comma Seperated List of Field IDs
   * @return string HTTP Body Response
   */
  public function fields($list = null) {
    // Create Action Context
    $context = new ActionContext('fields');

    // Is the 'list' parameter set?
    /* NOTE: $list is optional route parameter's 
     * (in PHALCON if the route parameter is not given, it shows up as empty string "")
     */
    $list = StringUtilities::nullOnEmpty($list);
    if (!isset($list)) { // NO: Try to extract it from the request parameters
      $list = $this->requestParameter('list');
    }

    if (isset($list)) { // If we have a Field List (expand it to an array)
      $list = explode(',', $list);
    }

    return $this->doAction($context
                            ->setIfNotNull('list', $list));
  }

  /**
   * Return a Single Service's Metadata.
   * 
   * @param string $id Service ID
   * @return string HTTP Body Response
   */
  public function service($id) {
    // Create Action Context
    $context = new ActionContext('service');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * Return a Multiple Services' Metadata.
   * 
   * @param string $list OPTIONAL Comma Seperated List of Service IDs
   * @return string HTTP Body Response
   */
  public function services($list = null) {
    // Create Action Context
    $context = new ActionContext('services');

    // Is the 'list' parameter set?
    $list = StringUtilities::nullOnEmpty($list);
    if (!isset($list)) { // NO: Try to extract it from the request parameters
      $list = $this->requestParameter('list');
    }

    if (isset($list)) { // If we have a Field List (expand it to an array)
      $list = explode(',', $list);
    }

    return $this->doAction($context
                            ->setIfNotNull('list', $list));
  }

  /**
   * Return a Single Form's Metadata.
   * 
   * @param string $id Form ID
   * @return string HTTP Body Response
   */
  public function form($id) {
    // Create Action Context
    $context = new ActionContext('form');

    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * Return a Multiple Forms' Metadata.
   * 
   * @param string $list OPTIONAL Comma Seperated List of Form IDs
   * @return string HTTP Body Response
   */
  public function forms($list = null) {
    // Create Action Context
    $context = new ActionContext('forms');

    // Is the 'list' parameter set?
    $list = StringUtilities::nullOnEmpty($list);
    if (!isset($list)) { // NO: Try to extract it from the request parameters
      $list = $this->requestParameter('list');
    }

    if (isset($list)) { // If we have a Field List (expand it to an array)
      $list = explode(',', $list);
    }

    return $this->doAction($context
                            ->setIfNotNull('list', $list));
  }

  /**
   * Return a Single Dataset's Metadata.
   * 
   * @param string $id Dataset ID
   * @return string HTTP Body Response
   */
  public function dataset($id) {
    // Create Action Context
    $context = new ActionContext('dataset');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * Return a Multiple Datasets' Metadata.
   * 
   * @param string $list OPTIONAL Comma Seperated List of Dataset IDs
   * @return string HTTP Body Response
   */
  public function datasets($list = null) {
    // Create Action Context
    $context = new ActionContext('datasets');

    // Is the 'list' parameter set?
    $list = StringUtilities::nullOnEmpty($list);
    if (!isset($list)) { // NO: Try to extract it from the request parameters
      $list = $this->requestParameter('list');
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
   * @return type
   */
  public function table($id) {
    // Create Action Context
    $context = new ActionContext('table_model');

    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function listModel($id) {
    // Create Action Context
    $context = new ActionContext('list_model');

    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('id', StringUtilities::nullOnEmpty($id)));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Default Handler for All Actions
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return array Array of Metadate Maps for Elements Requested
   * @throws \Exception On any type of failure condition
   */
  protected function doActionDefault($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Metadata Type to Retrieve
    $type = null;

    // Elements to Extract
    $list = null;

    // Get the Action Name
    $action = $context->getAction();
    assert('isset($action)');
    switch ($action) {
      case 'Field':
        $type = 'field';
        $list = $this->arrayOfString($context->getParameter('id'));
        break;
      case 'Fields':
        $type = 'field';
        $list = $context->getParameter('list');
        break;
      case 'Service':
        $type = 'service';
        $list = $this->arrayOfString($context->getParameter('id'));
        break;
      case 'Services':
        $type = 'service';
        $list = $context->getParameter('list');
        break;
      case 'Dataset':
        $type = 'dataset';
        $list = $this->arrayOfString($context->getParameter('id'));
        break;
      case 'Datasets':
        $type = 'dataset';
        $list = $context->getParameter('list');
        break;
      case 'Form':
        $type = 'form';
        $list = $this->arrayOfString($context->getParameter('id'));
        break;
      case 'Forms':
        $type = 'form';
        $list = $context->getParameter('list');
    }

    // POST Conditions
    assert('isset($type)');
    assert('isset($list)');

    // Build an Array Containing the Elements Requested
    $elements = array();
    for ($i = 0; $i < count($list); $i++) {
      // Get the Service ID
      $id = $list[$i];

      // Get the Metadata for the service
      $metadata = isset($id) && is_string($id) ? $this->buildMetadata($type, $id) : null;

      if (isset($metadata)) {
        $elements[$id] = $metadata;
      }
    }

    return count($elements) > 0 ? $elements : null;
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

    // Build the Table Metadata
    return isset($id) && is_string($id) ? $this->buildMetadata('table', $id) : null;
  }

  /**
   * 
   * @param type $parameters
   * @param type $testset
   * @return type
   */
  protected function doListModelAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the List Model
    $id = $context->getParameter('id');
    assert('isset($id)');

    // Build the List Metadata
    return isset($id) && is_string($id) ? $this->buildMetadata('list', $id) : null;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform checks that validate the Session State.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
//    $this->checkLoggedIn();

    return null;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required setup, before we perform final rendering of the Action's
   * Result.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return mixed Action Output that is to be Rendered
   * @throws \Exception On any type of failure condition
   */
  protected function preRender($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    return $context->getActionResult();
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Load's and Expands the Metadata.
   * 
   * @param string $type Type of Metadata Entity
   * @param string $id ID of Metadata Entity
   * @return array Fully contructerd Metadata Entity or null on failure
   */
  protected function buildMetadata($type, $id) {
    assert('isset($type) && is_string($type)');
    assert('isset($id) && is_string($id)');

    // Get the Metadata
    list($entity, $variation) = $this->explodeID($id);

    $metadata = $this->loadEntity($type, $entity);
    return isset($metadata) ? $this->expandInheritance($metadata, $type, $entity, $variation) : null;
  }

  /**
   * Retrieve's an Entity's Metadata from Backend Store.
   * 
   * @param string $type Type of Metadata Entity
   * @param string $id ID of Metadata Entity
   * @return array Metadata for Entity or null on failure
   */
  protected function loadEntity($type, $entity) {
    assert('isset($type) && is_string($type)');
    assert('isset($entity) && is_string($entity)');

    // Get the Metadata
    return $this->metadata->get($type, $entity);
  }

  /**
   * Expand's any inheritance references in the Metadata Provided
   * 
   * @param array $metadata Complete Metadata for an Entity (ex. form,service,etc.)
   * @param string $type Type of Metadata (ex: form,service,etc.)
   * @param string $entity Metadata Entity (ex: user, organization, etc.)
   * @param string $variant Variation of the Entity (ex: read, update, etc.)
   * @return array Metadata for Entity or null on failure
   */
  protected function expandInheritance($metadata, $type, $entity, $variant) {
    assert('isset($metadata) && is_array($metadata)');
    assert('isset($type) && is_string($type)');
    assert('isset($entity) && is_string($entity)');
    assert('isset($variant) && is_string($variant)');

    // Get the Element to Process
    $element = array_key_exists($variant, $metadata) ? $metadata[$variant] : null;

    // Extract the Element to Expand
    if (isset($element) &&
            array_key_exists('inherit', $element) &&
            isset($element['inherit'])) {
      // Explode the Inheritance ID
      list($v_entity, $v_variation) = $this->explodeID($element['inherit']);

      // Were we able to retrieve the Metadata for the Inherited Entity?
      $v_metadata = ($v_entity === $entity) ? $metadata : $this->loadEntity($type, $v_entity);
      if (isset($v_metadata)) { // YES
        // Were we able to retrieve the inherited element?
        $inheritance = $this->expandInheritance($v_metadata, $type, $v_entity, $v_variation);
        if (isset($inheritance)) { // YES: Merge the Element into Inheritance
          $element = $this->merge($inheritance, $element);
        }
      }

      // Remove Inheritance Link
      unset($element['inherit']);
    }

    return $element;
  }

  /**
   * Do a DEEP Merge of one array, into the other.
   * 
   * @param array $into the Array to Merge Into
   * @param array $from the Array to Merge From
   * @return array Resultant Array
   */
  protected function merge($into = null, $from = null) {

    // Was the destination array given?
    if (isset($into)) { // YES
      // Was the source array given?
      if (isset($from)) { // YES
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
    } else if (isset($from)) { // NO: Only the source was provided
      return $from;
    }
    //ELSE: Return Nothing
    return null;
  }

  /**
   * Test if an Array is Associative (A Map of key<-->value tuplets, in
   * which the key, is not numeric).
   * 
   * @param array $array Array to Test
   * @return boolean 'true' if array is considered associative, 'false' otherwise 
   */
  protected function is_assoc($array) {
    return (bool) count(array_filter(array_keys($array), 'is_string'));
  }

  /**
   * Decomposes a Metadata ID into entity and variation components
   * 
   * @param string $id ID to decompose
   * @param string $default OPTIONAL value to use in case of a missing component
   * @return array containing 2 elements, the 1st is the entity name, the second
   *   is the variation for the entity.
   */
  protected function explodeID($id, $default = null) {

    // Validate Incoming Parameters
    if (isset($default)) {
      $default = StringUtilities::nullOnEmpty($default);
    }

    if (isset($default)) {
      $default = strtolower($default);
    } else {
      $default = 'default';
    }

    // Explode the ID (expected format entity[:[variation]])
    if (stripos($id, ':') === FALSE) {
      $entity = StringUtilities::nullOnEmpty($id);
    } else {
      list($entity, $variation) = explode(':', $id, 2);
      $entity = StringUtilities::nullOnEmpty($entity);
      $variation = StringUtilities::nullOnEmpty($variation);
    }

    return array(isset($entity) ? strtolower($entity) : $default, isset($variation) ? strtolower($variation) : $default);
  }

}
