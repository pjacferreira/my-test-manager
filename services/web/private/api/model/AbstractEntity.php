<?php
/*
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
 */
namespace api\model;

/**
 * Base class used to provide some common functions to PHALCON Model Entities.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
abstract class AbstractEntity extends \Phalcon\Mvc\Model {
  /*
   * ---------------------------------------------------------------------------
   * Basic Public Entity Services
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the name used to reference the entity in Metadata
   * 
   * @return string Name
   */
  abstract public function entityName();

  /**
   * Retrieves a Map representation of the Entities Field Values.
   * Add's basic META information to all Entities.
   * 
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Map of field <--> value tuplets
   */
  public function toArray($header = true) {
    // Create and Array with Entity Identifier
    return $header ? array(
        '__type' => 'entity',
        '__entity' => strtolower($this->entityName()),
        '__key' => null,
        '__display' => null,
        '__fields' => array()
            ) :
            array();
  }

  /*
   * ---------------------------------------------------------------------------
   * HELPER Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Set Default Display Field for the Entity.
   * 
   * @param array $array Map to insert property into
   * @param string $property Entity Field Name
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Modified Array
   */
  protected function setDisplayField($array, $property, $header = true) {
    // Add Header Information?
    if ($header) { // YES
      $array['__display'] = $property;
    }
    return $array;
  }

  /**
   * Add an Entity Key Field<-->Value tuplet to the Map.
   * 
   * @param array $array Map to insert property into
   * @param string $property Entity Field Name
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Modified Array
   */
  protected function addKeyProperty($array, $property, $header = true) {
    // Add Header Information?
    if ($header) { // YES
      $array['__key'] = $property;
    }
    return $this->addProperty($array, $property, null, $header);
  }

  /**
   * Add an Entity Field<-->Value tuplet to the Map.
   * 
   * @param array $array Map to insert property into
   * @param string $property Entity Field Name
   * @param mixed $value (DEFAULT = null) Value of Property to add
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Modified Array
   */
  protected function addProperty($array, $property, $value = null, $header = true) {
    // Add Header Information?
    if ($header) { // YES
      // Add Field to List of Fields
      $array['__fields'][] = $property;
    }

    // Get the Entity Name
    if (!isset($value)) {
      $value = isset($this->$property) ? $this->$property : null;
    }
    $entity = strtolower($this->entityName());
    $array[$property] = $value;
    return $array;
  }

  /**
   * Add an Entity Field<-->Value tuplet to the Map, IF it's value is not null.
   * 
   * @param array $array Map to insert property into
   * @param string $property Entity Field Name
   * @param mixed $value (DEFAULT = null) Value of Property to add
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Modified Array
   */
  protected function addPropertyIfNotNull($array, $property, $value = null, $header = true) {
    // investigate get_called_class(); in order to create bas class function
    if (!isset($value)) {
      $value = isset($this->$property) ? $this->$property : null;
    }

    // Does the Field Have a Value?
    if ($value !== null) { // YES
      $array = $this->addProperty($array, $property, $value, $header);
    } else { // NO
      // Add Header Information?
      if ($header) { // YES
        // Add Field to List of Fields
        $array['__fields'][] = $property;
      }
    }
    return $array;
  }

  /**
   * Add a Entity Field<-->Entity Reference tuplet to the Map, IF it's value is not null.
   * 
   * @param array $array Map to insert property into
   * @param string $property Entity Field Name
   * @param mixed $value (DEFAULT = null) Value of Property to add
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Modified Array
   */
  protected function addReferencePropertyIfNotNull($array, $property, $value = null, $header = true) {
    if (!isset($value)) {
      if (isset($this->$property) && ($this->$property !== null)) {
        $value = $this->$property;
        // Handle Scenario in which, the Reference is BY ID rather THAN THE ACTUAL REFERRED OBJECT
        $value = is_a($value, "AbstractEntity") ? $value->toArray($header) : $value;
      } else {
        $value = null;
      }
    }

    // Does the Field Have a Value?
    if ($value !== null) { // YES
      $array = $this->addProperty($array, $property, $value, $header);
    } else { // NO
      // Add Header Information?
      if ($header) { // YES
        // Add Field to List of Fields
        $array['__fields'][] = $property;
      }
    }
    return $array;
  }

  /*
   * ---------------------------------------------------------------------------
   * STATIC HELPER Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Wraps the Execution of a Standard PHQL SELECT Query
   * 
   * @param string $phql PHQL Query to execute
   * @param array $parameters (OPTIONAL) Array of Parameters to Pass to PHQL Query
   * @return \Phalcon\Mvc\Model\Resultset Result Set of Query
   */
  protected static function selectQuery($phql, $parameters = null) {
    // Create PHALCON Query
    $query = new \Phalcon\Mvc\Model\Query($phql, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $rows = $query->execute($parameters);
    return $rows;
  }

  /**
   * Wraps the Execution os a Standard SELECT COUNT(*) Query
   * 
   * @param string $phql PHQL Query to execute
   * @param array $parameters (OPTIONAL) Array of Parameters to Pass to PHQL Query
   * @return integer Count of Entries
   */
  protected static function countQuery($phql, $parameters = null) {
    // Create PHALCON Query
    $query = new \Phalcon\Mvc\Model\Query($phql, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $result = $query->execute($parameters)->getFirst();
    return (integer) $result['count'];
  }

}
