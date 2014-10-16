<?php

/* Test Center - Compliance Testing Application (Web Services)
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

/**
 * State Code Entity (Represents a Possible Sub-State).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class User extends api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var integer
   */
  public $state;

  /**
   *
   * @var integer
   */
  public $code;

  /**
   *
   * @var string
   */
  public $s_description;

  /**
   *
   * @var string
   */
  public $l_description;

  /**
   *
   * @var integer
   */
  public $creator;

  /**
   *
   * @var string
   */
  public $date_created;

  /**
   *
   * @var integer
   */
  public $last_modifier;

  /**
   *
   * @var string
   */
  public $date_modified;

  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * PHALCON per request Contructor
   */
  public function initialize() {
    // Define Relations
    // A Single User can Be the Creator for Many Other Users
    $this->hasMany("id", "User", "creator");
    // A Single User can Be the Modifier for Many Other Users
    $this->hasMany("id", "User", "last_modifier");
  }

  /**
   * PHALCON per instance Contructor
   */
  public function onConstruct() {
    // Make sure the Creation Date is Set
    $now = new \DateTime();
    $this->date_created = $now->format('Y-m-d H:i:s');
  }

  /**
   * Define alternate table name for state codes
   * 
   * @return string State Codes Table Name
   */
  public function getSource() {
    return "t_statecodes";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'id_state' => 'state',
        'code' => 'code',
        's_description' => 's_description',
        'l_description' => 'l_description',
        'id_creator' => 'creator',
        'dt_creation' => 'date_created',
        'id_modifier' => 'modifier',
        'dt_modified' => 'date_modified'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
  }
  
  /*
   * ---------------------------------------------------------------------------
   * AbstractEntity: Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the name used to reference the entity in Metadata
   * 
   * @return string Name
   */
  public function entityName() {
    return "statecode";
  }

  /*
   * ---------------------------------------------------------------------------
   * PHP Standard Conversions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves a Map representation of the Entities Field Values
   * 
   * @return array Map of field <--> value tuplets
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addReferencePropertyIfNotNull($array, 'state');
    $array = $this->addProperty($array, 's_description');
    $array = $this->addPropertyIfNotNull($array, 'l_description');
    $array = $this->addReferencePropertyIfNotNull($array, 'creator');
    $array = $this->addProperty($array, 'date_created');
    $array = $this->addReferencePropertyIfNotNull($array, 'modifier');
    $array = $this->addPropertyIfNotNull($array, 'date_modified');
    return $array;
  }

  /**
   * String Representation of Entity
   * 
   * @return string Entity Identifier String
   */
  public function __toString() {
    return (string) $this->id;
  }

}
