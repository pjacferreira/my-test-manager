<?php

/*
 * Test Center - Compliance Testing Application (Web Services)
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

namespace api\controller;

use \shared\utility\StringUtilities;
use \shared\controller\BaseServiceController;

/**
 * Controller that Provides Basic Functionality for Controller's that Manage 
 * Database Entities.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
abstract class EntityServiceController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   * Abstract Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \api\model\AbstractEntity An instance of the Entity Managed by the
   *   Controller
   */
  abstract protected function createEntity();

  /*
   * ---------------------------------------------------------------------------
   * Public Static Metadata Related Methods Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves the Primary Key Field Names for the Given Entity
   * 
   * @param \api\model\AbstractEntity $entity Entity to base Metadata On
   * @return string[] Primary Key Field Names
   */
  public function primaryKey($entity) {
    // Map of Column Name -> Field Names
    $mapColumnsFields = $this->metadata->getColumnMap($entity);

    // Transpose Primary Key Column Names -> Primary Key Field Names
    $keyColumns = $this->metadata->getPrimaryKeyAttributes($entity);
    $keyFields = array();
    foreach ($keyColumns as $column) {
      $keyFields[] = $mapColumnsFields[$column];
    }

    return $keyFields;
  }

  /**
   * Tests if the Field Exists in the given Entity.
   * 
   * @param \api\model\AbstractEntity $entity Entity to base Metadata On
   * @param string $field Field Name to Test
   * @return boolean 'true' if the Field Exists in the Entity, 'false' otherwise
   */
  public function isFieldInEntity($entity, $field) {
    // Parameter Validation
    assert('isset($entity) && is_object($entity)');
    assert('isset($field) && is_string($field)');

    return array_key_exists($field, $this->metadata->getReverseColumnMap($entity));
  }

  /*
   * ---------------------------------------------------------------------------
   * Helper Methods:  Metadata Related (Deal with Column's rather than Entity 
   * Fields)
   * ---------------------------------------------------------------------------
   */

  /**
   * Tests if a given Column is an Identity Column (i.e. has it value automatically
   * managed by the Database System).
   * 
   * @param \api\model\AbstractEntity $entity Entity to base Metadata On
   * @param string $column Column Name to Test
   * @return boolean 'true' if the Column is an Identity Column, 'false' otherwise
   */
  protected function isIdentityColumn($entity, $column) {
    // Parameter Validation
    assert('isset($entity) && is_object($entity)');
    assert('isset($column) && is_string($column)');

    return $this->metadata->getIdentityField($entity) === $column;
  }

  /**
   * Tests if a given Column is a Part of the Entities Key.
   * 
   * @param \api\model\AbstractEntity $entity Entity to base Metadata On
   * @param string $column Column Name to Test
   * @return boolean 'true' if the Column is a Key Column, 'false' otherwise
   */
  protected function isKeyColumn($entity, $column) {
    // Parameter Validation
    assert('isset($entity) && is_object($entity)');
    assert('isset($column) && is_string($column)');

    return $this->isFieldInArray($column, $this->metadata->getPrimaryKeyAttributes($entity));
  }

  /**
   * Map's an Entities Field Name to the Respective Column Name
   * 
   * @param \api\model\AbstractEntity $entity Entity to base Metadata On
   * @param string $field Entity Field Name
   * @return string Column Name (if any)
   */
  protected function fieldToColumn($entity, $field) {
    // Parameter Validation
    assert('isset($entity) && is_object($entity)');
    assert('isset($field) && is_string($field)');

    $map = $this->metadata->getReverseColumnMap($entity);
    return array_key_exists($field, $map) ? $map[$field] : null;
  }

  /*
   * ---------------------------------------------------------------------------
   * Helper Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Set an Entities Properties using the given Key<-->Value Map.
   * 
   * @param \api\model\AbstractEntity $entity Incoming Entity
   * @param array $parameters A key<-->value map of incoming parameters (some of
   *   which might be used to set the values of the entities properties)
   * @param boolean $skip_key 'true' don't modify entities key fields, 'false' allows
   *   modification of entities key fields.
   * @return \api\model\AbstractEntity Outgoing (Modified) Entity
   */
  protected function setEntityValues($entity, $parameters, $skip_key = false) {
    // Parameter Validation
    assert('isset($entity) && is_object($entity)');
    assert('isset($parameters) && is_array($parameters)');

    // Get Entity Name
    $ename = $entity->entityName();

    /* TODO Create an Association System
     * A System that would allow, the setting of parameters of associated (referred) elements of the entity
     */
    $column = null;
    foreach ($parameters as $key => $value) {

      // Is the key in format entity-name:field-name?
      if (stripos($key, ':')) { // YES
        list($type, $field) = explode(':', $key, 2);

        // Does the Name of the Entity, match this Entities Name?
        if ($type != $ename) {
          continue;
        }
        $key = $field;
      } else { // NO: Not an Entity Field
        continue;
      }

      // Does a Column for the Field Exists?
      $column = $this->fieldToColumn($entity, $field);
      if (!isset($column)) { // NO: Skip it
        continue;
      }

      // Is this current field an Identity Field (Auto Generated)?
      if ($this->isIdentityColumn($entity, $column)) { // YES: Skip it
        continue;
      }

      // Is the current field a Key Field?
      if ($this->isKeyColumn($entity, $column) && $skip_key) { // YES: Skip it since skip set
        continue;
      }

      // Apply Transforms to Field Values
      $entity->$field = $this->transformFieldValue($field, $value);
    }

    return $entity;
  }

  /**
   * Apply any required modifications to the incoming Field value.
   * 
   * @param string $field Entity Field Name
   * @param mixed $value Field's Incoming Value
   * @return mixed Field's Outgoing Value
   */
  protected function transformFieldValue($field, $value) {
    return isset($value) && is_string($value) ? StringUtilities::nullOnEmpty($value) : $value;
  }

  /**
   * Provides a simple test if a field exists in the given array of fields.
   * 
   * @param string $field Field to test
   * @param string[] $field_list Field List to test against
   * @return boolean 'true' if the field is in the array, 'false' otherwise
   */
  protected function isFieldInArray($field, $field_list) {
    // Parameter Validation
    assert('isset($field) && is_string($field)');
    assert('isset($field_list) && is_array($field_list)');

    if (count($field_list) === 1) {
      return $field === $field_list[0];
    } else {
      return array_search($field, $field_list, true) !== FALSE;
    }
  }

  /**
   * 
   * @param type $action
   * @param type $in_actions
   * @param type $opt_actions
   * @param type $parameters
   * @param type $parm_in
   * @param type $parm_out
   * @param type $default_value
   * @param type $function
   * @return type
   * @throws \Exception
   */
  protected function inoutParameters($action, $in_actions, $opt_actions, $parameters, $parm_in, $parm_out, $default_value, $function) {
    assert('isset($action) && is_string($action)');
    assert('!isset($in_actions) || (is_string($in_actions) || is_array($in_actions))');
    assert('!isset($opt_actions) || is_string($opt_actions) || is_array($opt_actions)');
    assert('isset($parameters) && is_array($parameters)');
    assert('isset($parm_in) && is_string($parm_in)');
    assert('isset($parm_out) && (is_string($parm_out) || is_array($parm_out))');
    assert('isset($function) && is_callable($function)');

    // Convert Incoming Parameters to Arrays when appropriate
    if (is_string($in_actions)) {
      $in_actions = array($in_actions);
    }

    if (is_string($opt_actions)) {
      $opt_actions = array($opt_actions);
    }

    // See if it Applies to this $action
    if (!isset($in_actions) || // Applies to all actions
            (is_array($in_actions) && (array_search($action, $in_actions) !== FALSE)) // Applies to this action
    ) {
      if (isset($parameters[$parm_in]) || isset($default_value)) {
        $out_value = $function($this, $parameters, isset($parameters[$parm_in]) ? $parameters[$parm_in] : $default_value);
        if (is_string($parm_out)) { // Assign Out Value to Single Parameter
          $parameters[$parm_out] = $out_value;
        } else { // Assign Out Value to Multiple Parameters
          foreach ($parm_out as $parameter) {
            $parameters[$parameter] = $out_value;
          }
        }
      } else
      if (!isset($opt_actions) || // No Optional Actions
              (array_search($action, $opt_actions) === FALSE) // Action NOT in Optional Actions
      ) { // Okay we can skip this parameter
        throw new \Exception("Missing Required Parameter[$parm_in]", 1);
      }
    }

    return $parameters;
  }

}
