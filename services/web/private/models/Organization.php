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

namespace models;

/**
 * Organization Entity (Encompasses the Concept of a Business Entity/Organization)
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Organization extends \api\model\AbstractEntity {

  /**
   * @var integer Organization Identifier (Unique)
   */
  public $id;

  /**
   * @var string Organization Name (Unique)
   */
  public $name;

  /**
   * @var string Organization Description
   */
  public $description;

  /**
   * @var integer Organization Document Container
   */
  public $container;

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
    // A Single User can be the Creator for Many Organizations
    $this->hasMany("creator", "models\User", "id");
    // A Single User can be the Modifier for Many Organizations
    $this->hasMany("modifier", "models\User", "id");
    // Relation Between User and Organizations
    $this->hasMany("id", "models\UserOrganization", "organization");
    // A Single Organization is Linked to a Single Container
    $this->hasOne("container", "models\Containers", "id");
  }

  /**
   * Define alternate table name for organizations
   * 
   * @return string Organizations Table Name
   */
  public function getSource() {
    return "t_organizations";
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
      'description' => 'description',
      'id_container' => 'container',
      'id_creator' => 'creator',
      'dt_creation' => 'date_created',
      'id_modifier' => 'modifier',
      'dt_modified' => 'date_modified'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  protected function afterFetch() {
    $this->id = (integer) $this->id;
    $this->container = (integer) $this->container;
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
    return "organization";
  }

  /*
   * ---------------------------------------------------------------------------
   * PHP Standard Conversions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves a Map representation of the Entities Field Values
   * 
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Map of field <--> value tuplets
   */
  public function toArray($header = true) {
    $array = parent::toArray($header);

    $array = $this->addKeyProperty($array, 'id', $header);
    $array = $this->addProperty($array, 'name', null, $header);
    $array = $this->setDisplayField($array, 'name', $header);
    $array = $this->addPropertyIfNotNull($array, 'description', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'container', null, $header);
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
   * Try to Extract a Organization ID from the incoming parameter
   * 
   * @param mixed $organization The Potential Organization (object) or Organization ID (integer)
   * @return mixed Returns the Organization ID or 'null' on failure;
   */
  public static function extractOrganizationID($organization) {
    // Is the parameter an Organization Object?
    if (is_object($organization) && is_a($organization, __CLASS__)) { // YES
      return $organization->id;
    } else if (is_integer($organization) && ($organization >= 0)) { // NO: It's a Positive Integer
      return $organization;
    }
    // ELSE: None of the above
    return null;
  }

}
