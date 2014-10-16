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
 * User Entity (Encompasses Properties Required to Manage and Authenticate
 * Users).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class User extends api\model\AbstractEntity {

  /**
   * @var string User Identifier (UNIQUE)
   */
  public $id;

  /**
   * @var string User Name (UNIQUE)
   */
  public $name;

  /**
   * @var string User's First Name
   */
  public $first_name;

  /**
   * @var string User's Last Name
   */
  public $last_name;

  /**
   *
   * @var string User's MD5 Encoded Password
   */
  public $password;

  /**
   * @var string Entity's Short Description
   */
  public $s_description;

  /**
   * @var string Entity's Long Description
   */
  public $l_description;

  /**
   * @var integer Identifier of the User that Created the Entity
   */
  public $creator;

  /**
   * @var string Timestamp of Entity Creation
   */
  public $date_created;

  /**
   * @var integer Identifier of Last User to Modify the Entity
   */
  public $modifier;

  /**
   *
   * @var string Timestamp of Last Modification
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
    $this->hasMany("creator", "User", "id");
    // A Single User can Be the Modifier for Many Other Users
    $this->hasMany("modifier", "User", "id");
    // Relation Between User and Organizations
    $this->hasMany("id", "UserOrganization", "user");
    // Relation Between User and Projects
    $this->hasMany("id", "UserProject", "user");
    // TODO: Model::skipAttributes has any impact or requirement here
  }

  /**
   * Define alternate table name for users
   * 
   * @return string Users Table Name
   */
  public function getSource() {
    return "t_users";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'name' => 'name',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'password' => 'password',
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
    $this->creator = (integer) $this->creator;
    $this->modifier = isset($this->modifier) ? (integer) $this->modifier : null;
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
    return "user";
  }

  /*
   * ---------------------------------------------------------------------------
   * PHP Standard Conversions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves a Map representation of the Entities Field Values.
   * Add's basic META information to all Entities.
   * 
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Map of field <--> value tuplets
   */
  public function toArray($header = true) {
    $array = parent::toArray($header);

    $array = $this->addKeyProperty($array, 'id', $header);
    $array = $this->addProperty($array, 'name', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'first_name', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'last_name', null, $header);
    $array = $this->addPropertyIfNotNull($array, 's_description', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'l_description', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'creator', null, $header);
    $array = $this->addProperty($array, 'date_created', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'modifier', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'date_modified', null, $header);
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

  /*
   * ---------------------------------------------------------------------------
   * Public Helper Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Try to Extract a User ID from the incoming parameter
   * 
   * @param mixed $user The Potential User (object) or User ID (integer)
   * @return mixed Returns the User ID or 'null' on failure;
   */
  public static function extractUserID($user) {
    // Is the parameter an User Object?
    if (is_object($user) && is_a($user, __CLASS__)) { // YES
      return $user->id;
    } else if (is_integer($user) && ($user >= 0)) { // NO: It's a Positive Integer
      return $user;
    }
    // ELSE: None of the above
    return null;
  }

}
