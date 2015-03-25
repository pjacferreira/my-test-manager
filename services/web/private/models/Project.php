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
 * Project Entity (Encompasses the Concept of a Business Project undergoing
 * Testing)
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Project extends \api\model\AbstractEntity {

  /**
   * @var integer Project Identifier (Unique)
   */
  public $id;

  /**
   *
   * @var integer The Organization (identifier) the Project Belong's to
   */
  public $organization;

  /**
   * @var string Project Name (Unique)
   */
  public $name;

  /**
   * @var string Project Description
   */
  public $description;

  /**
   *
   * @var integer
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
    // A Single User can Be the Creator for Many Other Users
    $this->hasMany("creator", "models\User", "id");
    // A Single User can Be the Modifier for Many Other Users
    $this->hasMany("modifier", "models\User", "id");
    // A Single Organization can Be the Owner of Many Projects
    $this->hasMany("organization", "models\Organization", "id");
    // Relation Between User and Projects
    $this->hasMany("id", "models\UserProject", "project");
    // A Single Project is Linked to a Single Container
    $this->hasOne("container", "models\Container", "id");
  }

  /**
   * Define alternate table name for projects
   * 
   * @return string Projects Table Name
   */
  public function getSource() {
    return "t_projects";
  }

  /**
   * Independent Column Mapping.
   */
  public function columnMap() {
    return array(
      'id' => 'id',
      'id_organization' => 'organization',
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
    $this->organization = (integer) $this->organization;
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
    return "project";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'organization', null, $header);
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
   * Try to Extract a Project ID from the incoming parameter
   * 
   * @param mixed $project The Potential Project (object) or Project ID (integer)
   * @return mixed Returns the Project ID or 'null' on failure;
   */
  public static function extractProjectID($project) {
    assert('isset($test)');

    // Is the parameter an Project Object?
    if (is_object($project) && is_a($project, __CLASS__)) { // YES
      return $project->id;
    } else if (is_integer($project) && ($project >= 0)) { // NO: It's a Positive Integer
      return $project;
    }
    // ELSE: None of the above
    return null;
  }

  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Extensions
   * ---------------------------------------------------------------------------
   */

  /**
   * List the Projects Related to the Specified Organization
   * 
   * @param mixed $org Organization ID or Organization Entity
   * @return \Project[] Related Projects
   * @throws \Exception On Any Failure
   */
  public static function listInOrganization($org) {
    // Are we able to extract the Organization ID from the Parameter?
    $id = \models\Organization::extractOrganizationID($org);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Search for Matching Projects
    $projects = self::find(['organization' => $id]);
    
    // Did we successfully retrieve a list of Projects?
    if ($projects === FALSE) { // NO
      throw new \Exception("Failed to Retrieve Projects List.", 2);
    }

    return $projects;
  }

  /**
   * Count the Number of Projects Related to the Specified Organization
   * 
   * @param mixed $org Organization ID or Organization Entity
   * @return integer Number of Related Projects
   * @throws \Exception On Any Failure
   */
  public static function countInOrganization($org) {
    // Are we able to extract the Organization ID from the Parameter?
    $id = \models\Organization::extractOrganizationID($org);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Find Child Entries
    $count = \models\Container::count(['organization' => $id]);

    // Return Result Set
    return (integer) $count;
  }

}
