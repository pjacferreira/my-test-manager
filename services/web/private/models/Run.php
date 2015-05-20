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
  public $container;

  /**
   *
   * @var integer
   */
  public $current_ple;

  /**
   *
   * @var integer
   */
  public $state;

  /**
   *
   * @var integer
   */
  public $run_code;

  /**
   *
   * @var string
   */
  public $comment;

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
    $this->hasMany("creator", "models\User", "id");
    // A Single User can Be the Modifier for Many Other Users
    $this->hasMany("modifier", "models\User", "id");
    // A Single User can Be the Owner for Many Runs
    $this->hasMany("owner", "models\User", "id");
    // A Run Belongs To a Single Project
    $this->belongsTo("project", "models\Project", "id");
    // A Single Set has a Single Container
    $this->hasOne("container", "models\Container", "id");
    // A Single Test Set can be Runned Many Times
    $this->hasMany("set", "models\Set", "id");
    // A Single Run can have Point at a Single Play Entry
    $this->hasOne("current_ple", "models\PlayEntry", "id");
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
      'name' => 'name',
      'description' => 'description',
      'id_container' => 'container',
      'id_current_ple' => 'current_ple',
      'state' => 'state',
      'run_code' => 'run_code',
      'comment' => 'comment',
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
    $this->project = (integer) $this->project;
    $this->set = (integer) $this->set;
    $this->container = (integer) $this->container;
    $this->current_ple = isset($this->current_ple) ? (integer) $this->current_ple : null;
    $this->state = (integer) $this->state;
    $this->run_code = (integer) $this->run_code;
    $this->creator = (integer) $this->creator;
    $this->modifier = isset($this->modifier) ? (integer) $this->modifier : null;
    $this->owner = (integer) $this->owner;
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
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Map of field <--> value tuplets
   */
  public function toArray($header = true) {
    $array = parent::toArray($header);

    $array = $this->addKeyProperty($array, 'id', $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'project', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'set', null, $header);
    $array = $this->addProperty($array, 'name', null, $header);
    $array = $this->setDisplayField($array, 'name', $header);
    $array = $this->addPropertyIfNotNull($array, 'description', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'container', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'current_ple', null, $header);
    $array = $this->addProperty($array, 'state', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'run_code', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'comment', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'creator', null, $header);
    $array = $this->addProperty($array, 'date_created', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'modifier', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'date_modified', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'owner', null, $header);
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
  public static function extractID($run) {
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
    $project_id = \models\Project::extractID($project);
    if (!isset($project_id)) { // NO
      throw new \Exception("Project Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => 'project = :project:',
      "bind" => ['project' => $project_id],
    ];

    // Is the Name/ID Parameters an Integer?
    if (is_int($nameid) && ($nameid >= 0)) { // YES
      $params['conditions'] .= ' and id = :id:';
      $params['bind']['id'] = $nameid;
    } else if (is_string($nameid)) { // NO: It's a String
      $nameid = Strings::nullOnEmpty($nameid);
      // Does it have a value?
      if (!isset($nameid)) { // NO
        throw new \Exception("Name/ID Parameter is invalid.", 2);
      }
      $params['conditions'] .= ' and name = :name:';
      $params['bind']['name'] = $nameid;
    } else { // UNKNOWN TYPE
      throw new \Exception("Name/ID Parameter is invalid.", 3);
    }

    $set = self::findFirst($params);
    return $set !== FALSE ? $set : null;
  }

  /**
   * List the Runs Related to the Specified Project
   * 
   * @param mixed $project Project ID or Project Entity
   * @param array $filter OPTIONAL Filter Condition
   * @param array $order OPTIONAL Sort Order Condition
   * @return \models\Run[] Runs in Project
   * @throws \Exception On Any Failure
   */
  public static function listInProject($project, $filter = null, $order = null) {
    assert('isset($project)');
    assert('($filter === null) || is_array($filter)');
    assert('($order === null) || is_string($order)');

    // Are we able to extract the Project ID from the Parameter?
    $id = \models\Project::extractID($project);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => 'project = :id:',
      'bind' => ['id' => $id],
      'order' => isset($order) ? $order : 'id'
    ];

    // Merge in Filter Conditions
    if (isset($filter)) {
      $params['conditions'].=' and (' . $filter['conditions'] . ')';
      $params['bind'] = array_merge($filter['bind'], $params['bind']);
    }

    // Search for Matching Projects
    $runs = self::find($params);
    return $runs !== FALSE ? $runs : [];
  }

  /**
   * Count the Number of Runs Related to the Specified Project
   * 
   * @param mixed $project Project ID or Project Entity
   * @param array $filter Extra Filter conditions to use
   * @return integer Number of Related Runs
   * @throws \Exception On Any Failure
   */
  public static function countInProject($project, $filter = null) {
    assert('isset($project)');
    assert('($filter === null) || is_array($filter)');

    // Are we able to extract the Project ID from the Parameter?
    $id = \models\Project::extractID($project);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => 'project = :id:',
      'bind' => ['id' => $id]
    ];

    // Merge in Filter Conditions
    if (isset($filter)) {
      $params['conditions'].=' and (' . $filter['conditions'] . ')';
      $params['bind'] = array_merge($filter['bind'], $params['bind']);
    }

    // Find Child Entries
    $count = self::count($params);

    // Return Result Set
    return (integer) $count;
  }

  /**
   * List the Runs Related to the Specified Container
   * 
   * @param mixed $container Container ID or Container Entity
   * @return \models\Run[] Runs in Container
   * @throws \Exception On Any Failure
   */
  public static function listInFolder($container) {
    assert('isset($container)');

    // Are we able to extract the Container ID from the Parameter?
    $id = \models\Container::extractContainerID($container);
    if (!isset($id)) { // NO
      throw new \Exception("Container Parameter is invalid.", 1);
    }

    // Instantiate the Query
    /* NOTE: The choice of the Entity Used with FROM is important, as it
     * represents the type of entity that will be created, on rehydration.
     */
    $pqhl = 'SELECT r.*
               FROM models\Run r
               JOIN models\Container c ON c.link = r.id
               WHERE c.parent = :id: and c.type = :type:
               ORDER BY s.id';

    // Execute Query and Return Results
    $runs = self::selectQuery($pqhl, [
        'id' => $id,
        'type' => 'R'
    ]);
    return $runs !== FALSE ? $runs : [];
  }

  /**
   * Count the Number of Runs Related to the Specified Container
   * 
   * @param mixed $project Container ID or Container Entity
   * @return integer Number of Runs under Given Conditions
   * @throws \Exception On Any Failure
   */
  public static function countInFolder($container) {
    assert('isset($container)');

    // Are we able to extract the Container ID from the Parameter?
    $id = \models\Container::extractContainerID($container);
    if (!isset($id)) { // NO
      throw new \Exception("Container Parameter is invalid.", 1);
    }

    // Find Child Entries
    $count = \models\Container::count([
        'conditions' => 'parent = :id: and type = :type:',
        'bind' => [ 'id' => $id, 'type' => 'R']
    ]);

    // Return Result Set
    return (integer) $count;
  }

  /**
   * List the Tests Related to the Specified Run in Play Order
   * 
   * @param mixed $run Run ID or Run Entity
   * @return \models\Test[] Tests in Container
   * @throws \Exception On Any Failure
   */
  public static function listTestsInRun($run) {
    assert('isset($run)');

    // Are we able to extract the Run ID from the Parameter?
    $id = self::extractID($run);
    if (!isset($id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Instantiate the Query
    /* NOTE: The choice of the Entity Used with FROM is important, as it
     * represents the type of entity that will be created, on rehydration.
     */
    $pqhl = 'SELECT t.*
               FROM models\Test t
               JOIN models\SetTest s ON s.test = t.id
               JOIN models\Run r ON r.[set] = s.[set]
               WHERE r.id = :id: 
               ORDER BY s.sequence';

    // Execute Query and Return Results
    $tests = self::selectQuery($pqhl, [
        'id' => $id
    ]);
    return $tests !== FALSE ? $tests : [];
  }

  /**
   * Count the Number of Test Related to the Specified Run
   * 
   * @param mixed $run Run ID or Run Entity
   * @return integer Number of Test used in a Given Run
   * @throws \Exception On Any Failure
   */
  public static function countTestsInRun($run) {
    assert('isset($run)');

    // Are we able to extract the Run ID from the Parameter?
    $id = self::extractID($run);
    if (!isset($id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*)
               FROM models\SetTest s
               JOIN models\Run r ON r.[set] = s.[set]
               WHERE r.id = :id:';

    // Execute Query and Return Results
    $count = self::countQuery($pqhl, [
        'id' => $id
    ]);

    // Return Result Set
    return  $count;
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
