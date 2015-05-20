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

use common\utility\Strings;

/**
 * Test Entity (Encompasses Header Information Related to Tests).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Test extends \api\model\AbstractEntity {

  // INITIAL STATE - Test has been Created but Not Modified
  const STATE_CREATED = 0;
  // TEST is STILL BEING WRITTEN
  const STATE_IN_DEVELOPMENT = 1;
  // TEST is READY for PRODUCTION
  const STATE_READY = 9;

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
  public $state;

  /**
   *
   * @var boolean
   */
  public $renumber;

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

  /**
   * Independent Column Mapping.
   */
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
    // A Single Projects can Contain Many Runs
    $this->hasMany("project", "models\Project", "id");
    // A Single Project is Linked to a Single Container
    $this->hasOne("container", "models\Container", "id");
    // A Single Test has a Single Container
    $this->hasOne("container", "models\Container", "id");
  }

  /**
   * PHALCON per instance Contructor
   */
  public function onConstruct() {
    // By Default Single Level
    $this->state = self::STATE_CREATED;
    // Clear Flag
    $this->renumber = 0;
  }

  /**
   * Define alternate table name for tests
   * 
   * @return string Tests Table Name
   */
  public function getSource() {
    return "t_tests";
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
      'name' => 'name',
      'description' => 'description',
      'id_container' => 'container',
      'state' => 'state',
      'renumber' => 'renumber',
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
    $this->container = (integer) $this->container;
    $this->state = (integer) $this->state;
    $this->renumber = (integer) $this->renumber;
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
    return "test";
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
    $array = $this->addProperty($array, 'name', null, $header);
    $array = $this->setDisplayField($array, 'name', $header);
    $array = $this->addPropertyIfNotNull($array, 'description', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'container', null, $header);
    $array = $this->addProperty($array, 'state', null, $header);
    $array = $this->addProperty($array, 'renumber', null, $header);
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
   * Try to Extract a Test ID from the incoming parameter
   * 
   * @param mixed $test Test Entity (object) or Test ID (integer)
   * @return mixed Returns the Test ID or 'null' on failure;
   */
  public static function extractID($test) {
    assert('isset($test)');

    // Is the parameter an Test Object?
    if (is_object($test) && is_a($test, __CLASS__)) { // YES
      return $test->id;
    } else if (is_integer($test) && ($test >= 0)) { // NO: It's a Positive Integer
      return $test;
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
   * Find the Test in the Project with the Given Name/ID
   * 
   * @param mixed $project Project ID or Project Entity
   * @param mixed $nameid Test Name or ID
   * @return mixed Returns Test or 'null' if none found
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

    $test = self::findFirst($params);
    return $test !== FALSE ? $test : null;
  }

  /**
   * List the Tests Related to the Specified Project
   * 
   * @param mixed $project Project ID or Project Entity
   * @param array $filter OPTIONAL Filter Condition
   * @param array $order OPTIONAL Sort Order Condition
   * @return \models\Test[] Test in Project
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
    $tests = self::find($params);
    return $tests !== FALSE ? $tests : [];
  }

  /**
   * Count the Number of Tests Related to the Specified Project
   * 
   * @param mixed $project Project ID or Project Entity
   * @param array $filter Extra Filter conditions to use
   * @return integer Number of Test under Given Conditions
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
   * List the Tests Related to the Specified Container
   * 
   * @param mixed $container Container ID or Container Entity
   * @return \models\Test[] Test in Project
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
    $pqhl = 'SELECT t.*' .
      ' FROM models\Test t' .
      ' JOIN models\Container c' .
      ' WHERE c.parent = :id: and c.type = :type: and c.link = t.id' .
      ' ORDER BY t.id';

    // Execute Query and Return Results
    $tests = self::selectQuery($pqhl, [
        'id' => $id,
        'type' => 'T'
    ]);
    return $tests !== FALSE ? $tests : [];
  }

  /**
   * Count the Number of Tests Related to the Specified Container
   * 
   * @param mixed $project Container ID or Container Entity
   * @return integer Number of Test under Given Conditions
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
        'bind' => [ 'id' => $id, 'type' => 'T']
    ]);

    // Return Result Set
    return (integer) $count;
  }

  /**
   * 
   * TODO: Migrate to PHALCON
   * 
   * @param type $test
   * @return boolean
   */
  public function removeRelations($test) {
    assert('isset($test) && is_object($test)');

    $typemap = TypeCache::getInstance();
    $typeid = $typemap->typeID($this->getEntityName());

    // Remove Links from Containers
    $repository = $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\Container');

    // Remove Links to Test
    $repository->removeLinksTo($test->getId(), $typeid);

    // Remove Containers Owned by Test    
    $repository->removeOwnedBy($test->getId(), $typeid);

    // Remove Steps Associated with Test
    $repository = $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\TestStep');

    // Remove All Steps Associated with the Test
    $repository->removeAllStepsFrom($test);

    return true;
  }

}

/* TODO: Add Test Type
 * Why?
 * Because this would allow us to create different run modules.
 * 
 * Example:
 * We can use test type 0 - Manual
 * and then have a test type 1 - CodeCeption (Automatic)
 * 
 * This would allow us to integrate automated test into a single framework!
 * We could have a user start a Run, with a sequence of manual tests, then
 * have the system continue the play, with automatic test, and return back
 * to the use (notify the user after the auto test are complete) so he can
 * continue with the manual tests.
 */