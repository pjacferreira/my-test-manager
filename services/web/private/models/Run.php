<?php

/* Test Center - Compliance Testing Application (Web Services)
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
 * Run Entity (Encompasses the State of a Single Test Set Run).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Run extends \api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var integer
   */
  public $project;

  /**
   *
   * @var integer
   */
  public $set;

  /**
   *
   * @var string
   */
  public $group;

  /**
   *
   * @var string
   */
  public $name;

  /**
   *
   * @var string
   */
  public $description;

  /**
   *
   * @var integer
   */
  public $is_open;

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
  public $comment;

  /**
   *
   * @var integer
   */
  public $playlist_position;

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
  public $modifier;

  /**
   *
   * @var string
   */
  public $date_modified;

  /**
   *
   * @var integer
   */
  public $owner;

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
    // A Single User can Be the Owner for Many Runs
    $this->hasMany("owner", "User", "id");
    // A Single Projects can Contain Many Runs
    $this->hasMany("project", "Project", "id");
    // A Single Test Set can be Runned Many Times
    $this->hasMany("set", "Set", "id");
    // A Single Run can have Point at a Single Play Entry
    $this->hasOne("playlist_position", "PlayEntry", "id");
  }

  /**
   * PHALCON per instance Contructor
   */
  public function onConstruct() {
    // Set the Initial State for the Run
    $this->is_open = true;
    $this->state = 0;
    $this->state_code = 0;

    // Make sure the Creation Date is Set
    $now = new \DateTime();
    $this->date_created = $now->format('Y-m-d H:i:s');
  }

  /**
   * Define alternate table name for runs
   * 
   * @return string Runs Table Name
   */
  public function getSource() {
    return "t_runs";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'id_project' => 'project',
        'id_set' => 'set',
        'run_group' => 'group',
        'name' => 'name',
        'description' => 'description',
        'open' => 'is_open',
        'state' => 'state',
        'state_code' => 'code',
        'comment' => 'comment',
        'id_playlist_pos' => 'playlist_position',
        'id_creator' => 'creator',
        'dt_creation' => 'date_created',
        'id_modifier' => 'modifier',
        'dt_modified' => 'date_modified',
        'id_owner' => 'owner'
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
    return "run";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'project');
    $array = $this->addReferencePropertyIfNotNull($array, 'set');
    $array = $this->addPropertyIfNotNull($array, 'group');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'description');
    $array = $this->addProperty($array, 'is_open');
    $array = $this->addProperty($array, 'state');
    $array = $this->addProperty($array, 'state_code');
    $array = $this->addPropertyIfNotNull($array, 'comment');
    $array = $this->addReferencePropertyIfNotNull($array, 'playlist_position');
    $array = $this->addReferencePropertyIfNotNull($array, 'creator');
    $array = $this->addProperty($array, 'date_created');
    $array = $this->addReferencePropertyIfNotNull($array, 'modifier');
    $array = $this->addPropertyIfNotNull($array, 'date_modified');
    $array = $this->addReferencePropertyIfNotNull($array, 'owner');
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
   * Try to Extract a Run ID from the incoming parameter
   * 
   * @param mixed $run Run Entity (object) / ID (integer)
   * @return mixed Returns the Run ID or 'null' on failure;
   */
  public static function extractRunID($run) {
    // Is the parameter an Test Object?
    if (is_object($run) && is_a($run, __CLASS__)) { // YES
      return $run->id;
    } else if (is_integer($run) && ($run >= 0)) { // NO: It's a Positive Integer
      return $run;
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
   * Find the Run in the Project with the Given Name/ID
   * 
   * @param mixed $project Project ID or Project Entity
   * @param mixed $nameid Run Name or ID
   * @return mixed Returns Run or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findInProject($project, $nameid) {
    // Are we able to extract the Project ID from the Parameter?
    $project_id = \Project::extractID($project);
    if (isset($project_id)) { // NO
      throw new \Exception("Project Parameter is invalid.", 1);
    }

    $conditions = 'project = :project:';
    $parameters = array('project' => $project_id);
    // Is the Name/ID Parameters an Integer?
    if (is_int($nameid) && ($nameid >= 0)) { // YES
      $conditions = 'id = :id: and ' . $conditions;
      $parameters['id'] = (integer) $nameid;
    } else if (is_string($nameid)) { // NO: It's a String
      $nameid = Strings::nullOnEmpty($nameid);
      // Does it have a value?
      if (!isset($nameid)) { // NO
        throw new \Exception("Name/ID Parameter is invalid.", 2);
      }
      $conditions.= 'name = :name: and ' . $conditions;
      $parameters['name'] = $nameid;
    } else { // UNKNOWN TYPE
      throw new \Exception("Name/ID Parameter is invalid.", 3);
    }

    return self::findFirst(array(
                "conditions" => $conditions,
                "bind" => $parameters
    ));
  }

  /**
   * List the Runs Related to the Specified Project
   * 
   * @param mixed $project Project ID or Project Entity
   * @return \Run[] Related Runs
   * @throws \Exception On Any Failure
   */
  public static function listInProject($project) {
    // Are we able to extract the Project ID from the Parameter?
    $project_id = \Project::extractID($project);
    if (isset($project_id)) { // NO
      throw new \Exception("Project Parameter is invalid.", 1);
    }

    return self::find(array(
                "conditions" => 'project = :project:',
                "bind" => array('project' => $project_id),
                "order" => 'id'
    ));
  }

  /**
   * Count the Number of Runs Related to the Specified Project
   * 
   * @param mixed $project Project ID or Project Entity
   * @return integer Number of Related Runs
   * @throws \Exception On Any Failure
   */
  public static function countInProject($project) {
    // Are we able to extract the Project ID from the Parameter?
    $project_id = \Project::extractID($project);
    if (isset($project_id)) { // NO
      throw new \Exception("Project Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) FROM Run WHERE project = :id:';
    $query = new Phalcon\Mvc\Model\Query($pqhl, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $result = $query->execute(array('id' => $project_id))->getFirst();
    return (integer) $result['0'];
  }

  /**
   * Create a Link's Based on a Test Set
   * 
   * @param mixed $run Run Set ID / Entity
   * @param mixed $set Test Set ID / Entity
   * @return integer Number of Links Created
   * @throws \Exception On Any Failure
   */
  public function cloneSetLinks($run, $set) {
    assert('isset($run) && is_object($run)');
    assert('isset($set) && is_object($set)');

    // Get the Set of Links in the Test Set
    $test_links = \SetTest::listInSet($set);

    // Enumerate All the Tests in the Set
    $run_links = array();
    foreach ($test_links as $set_link) {

      // Enumerate all the Steps in the Test
      $test = $set_link->test;

      // Clone Test Set Link Information
      $link = \RunLink::createLink($run, $test, $set_link->sequence);

      // Save the Link
      $run_links[] = $link;
    }

    // Save the Changes Back to the Database
    $this->getEntityManager()->flush();
    return count($run_links);
  }

  /**
   * 
   * @param type $test
   * @return boolean
   */
  public function removeRelations($run) {
    assert('isset($run) && is_object($run)');

    // Remove Container Entries
    $typemap = TypeCache::getInstance();
    $typeid = $typemap->typeID($this->getEntityName());

    // Remove Links from Containers
    $link_count = $this->__repositoryContainers()->removeLinksTo($run->getId(), $typeid);

    // Remove TestSet Links
    $link_count = $this->__repositoryLinks()->removeAllLinksTo($run);

    return true;
  }

}
