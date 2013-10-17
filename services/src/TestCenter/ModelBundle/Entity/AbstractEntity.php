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

namespace TestCenter\ModelBundle\Entity;

/**
 * AbstractEntity
 *
 * @author Paulo Ferreira
 */
abstract class AbstractEntity {

  /**
   * @return array
   */
  public function toArray() {
    // Create and Array with Entity Identifier
    return array(
        '__entity' => strtolower($this->entityName()),
    );
  }

  /**
   * 
   * @param type $array
   * @param type $property
   * @return type
   */
  protected function addProperty($array, $property, $value) {
    // Get the Entity Name
    if (!isset($value)) {
      $value = isset($this->$property) ? $this->$property : null;
    }
    $entity = strtolower($this->entityName());
    $array["{$entity}:{$property}"] = $value;
    return $array;
  }

  /**
   * 
   * @param type $array
   * @param type $property
   * @return type
   */
  protected function addPropertyIfNotNull($array, $property, $value) {
    // investigate get_called_class(); in order to create bas class function
    if (!isset($value)) {
      $value = isset($this->$property) ? $this->$property : null;
    }
    return $value !== null ? $this->addProperty($array, $property, $value) : $array;
  }

  /**
   * 
   * @param type $array
   * @param type $property
   * @return type
   */
  protected function addReferencePropertyIfNotNull($array, $property, $value) {
    if (!isset($value)) {
      if (isset($this->$property) && ($this->$property !== null)) {
        $value = $this->$property->toArray();
      } else {
        $value = null;
      }
    }
    return $value !== null ? $this->addProperty($array, $property, $value) : $array;
  }

  /**
   * 
   * @return type
   */
  abstract protected function entityName();
}