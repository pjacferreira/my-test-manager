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
 * Play Entry Entity (Encompasses the State/Result of a Single Play Entry in a Run).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class PlayEntry extends \api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var integer
   */
  public $run;

  /**
   *
   * @var integer
   */
  public $sequence;

  /**
   *
   * @var integer
   */
  public $test;

  /**
   *
   * @var integer
   */
  public $step;

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
   * @var boolean
   */
  public $conditioned;
  
  /**
   *
   * @var string
   */
  public $comment;

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
    // Each Play List Entry belongs to a Single Run
    $this->belongsTo("run", "models\Run", "id");
    // A Single User can Be the Modifier for Many Other Users
    $this->hasMany("modifier", "models\User", "id");
  }

  /**
   * PHALCON per instance Contructor
   */
  public function onConstruct() {
    // DEFAULT: Not Entry has not been Executed
    $this->run_code=0;
    // DEFAULT: No Special Execute Condition
    $this->conditioned=0;
  }
  
  /**
   * Define alternate table name for Play Lists
   * 
   * @return string Play Lists Table Name
   */
  public function getSource() {
    return "t_playlists";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
      'id' => 'id',
      'id_run' => 'run',
      'sequence' => 'sequence',
      'id_test' => 'test',
      'id_step' => 'step',
      'run_code' => 'run_code',
      'conditioned' => 'conditioned',
      'comment' => 'comment',
      'id_modifier' => 'modifier',
      'dt_modified' => 'date_modified'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
    $this->run = (integer) $this->run;
    $this->sequence = (integer) $this->sequence;
    $this->test = (integer) $this->test;
    $this->step = (integer) $this->step;
    $this->run_code = (integer) $this->run_code;
    $this->conditioned = (integer) $this->conditioned;
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
    return "playentry";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'run', null, $header);
    $array = $this->addProperty($array, 'sequence', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'test', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'step', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'run_code', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'comment', null, $header);
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
   * PHALCON Model Extensions
   * ---------------------------------------------------------------------------
   */

  /**
   * Try to Extract a Set<-->Test ID from the incoming parameter
   * 
   * @param mixed $project The Potential Link (object) or ID (integer)
   * @return mixed Returns the ID or 'null' on failure;
   */
  public static function extractID($settest) {
    assert('isset($settest)');

    // Is the parameter an Project Object?
    if (is_object($settest) && is_a($settest, __CLASS__)) { // YES
      return $settest->id;
    } else if (is_integer($settest) && ($settest >= 0)) { // NO: It's a Positive Integer
      return $settest;
    }
    // ELSE: None of the above
    return null;
  }

  /**
   * Create a Play List from a Run
   * 
   * @param models\Run $run Run Entity
   * @throws \Exception On Any Failure
   */
  public static function createPlayList(Run $run) {
    $id_run = $run->id;

    // Instantiate the Query
    $pqhl = 'SELECT s.test, t.name, ts.id as step, ts.title, ts.description
             FROM models\Run r
             JOIN models\SetTest s ON r.[set] = s.[set]
             JOIN models\Test t ON s.test = t.id
             JOIN models\TestStep ts ON t.id = ts.test
             WHERE r.id = :id:
             ORDER BY s.sequence, ts.sequence';
    $query = new \Phalcon\Mvc\Model\Query($pqhl, \Phalcon\Di::getDefault());

    // Did we get any results from the Query?
    $entries = $query->execute(array('id' => $id_run));
    if ($entries === FALSE) { // NO
      throw new \Exception("Invalid Run Definition.", 1);
    }

    // Cycle through entries
    $playlist = [];
    $sequence = 1;
    foreach ($entries as $entry) {
      // Create a Basic Entry
      $playentry = new PlayEntry;
      $playentry->sequence = $sequence++;
      $playentry->run = $id_run;
      $playentry->test = $entry->test;
      $playentry->step = $entry->step;
      // Add to Play List
      $playlist[] = $playentry;
    }

    return $playlist;
  }

  /**
   * Find the First Play Entry in the Previous Test (or 1st Step in the 1st Test)
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function previousTest(PlayEntry $ple) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($ple->run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($ple->test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and test <> :test_id: and sequence < :sequence:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id, 'sequence' => $ple->sequence],
      "order" => 'sequence'
    ];

    $first = self::findFirst($params);
    return ($first === FALSE) ? self::first($ple->run) : $first;
  }

  /**
   * Find the First Play Entry in the Next Test (or return 1st Entry in the Last Test)
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function nextTest(PlayEntry $ple) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($ple->run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($ple->test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and test <> :test_id: and sequence > :sequence:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id, 'sequence' => $ple->sequence],
      "order" => 'sequence'
    ];

    $last = self::findFirst($params);
    return ($last === FALSE) ? self::lastTest($ple->run) : $last;
  }

  /**
   * Find the First Play Entry in the Last Test
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function lastTest($run) {
    // Find the Last PLE    
    $last = self::last($run);

    // Find the 1st Step in the Last Test
    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id:',
      "bind" => ['run_id' => $last->run, 'test_id' => $last->test],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function firstByTest($run, $test) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function previousByTest(PlayEntry $ple) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($ple->run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($ple->test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id: and sequence < :sequence:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id, 'sequence' => $ple->sequence],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function nextByTest(PlayEntry $ple) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($ple->run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($ple->test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id: and sequence > :sequence:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id, 'sequence' => $ple->sequence],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function lastByTest($run, $test) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function first($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id:',
      "bind" => ['run_id' => $run_id],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function previous(PlayEntry $ple) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($ple->run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and sequence < :sequence:',
      "bind" => ['run_id' => $run_id, 'sequence' => $ple->sequence],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function next(PlayEntry $ple) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($ple->run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id: and sequence > :sequence:',
      "bind" => ['run_id' => $run_id, 'sequence' => $ple->sequence],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Play Entry based on the Given Play Entry
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function last($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[run] = :run_id:',
      "bind" => ['run_id' => $run_id],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * List the Test Steps Related to the Specified Test
   * 
   * @param mixed $run Run ID or Run Entity
   * @param boolean $descending [DEFAULT false] Sort in Descending Order by Sequence
   * @return Test[] Related Tests
   * @throws \Exception On Any Failure
   */
  public static function listByRun($run, $descending = false) {
    assert('isset($run)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Run::extractID($run);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => '[run] = :id:',
      'bind' => ['id' => $id],
      'order' => !!$descending ? 'sequence DESC' : 'sequence'
    ];

    // Search for Matching Projects
    $ples = self::find($params);
    return $ples === FALSE ? [] : $ples;
  }

  /**
   * Count the Number of Tests Related to the Specified Set
   * 
   * @param mixed $run Run ID or Run Entity
   * @return integer Number of Related Tests
   * @throws \Exception On Any Failure
   */
  public static function countByRun($run) {
    assert('isset($run)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Run::extractID($run);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => 'run = :id:',
      'bind' => ['id' => $id]
    ];

    // Find Child Entries
    $count = self::count($params);

    // Return Result Set
    return (integer) $count;
  }

  /**
   * List the Steps for the Given Run and Test
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @param boolean $descending [DEFAULT false] Sort in Descending Order by Sequence
   * @return Test[] Related Tests
   * @throws \Exception On Any Failure
   */
  public static function listByTest($run, $test, $descending = false) {
    assert('isset($run)');
    assert('isset($test)');

    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id],
      'order' => !!$descending ? 'sequence DESC' : 'sequence'
    ];

    // Search for Matching Projects
    $ples = self::find($params);
    return $ples === FALSE ? [] : $ples;
  }

  /**
   * Count the Number of Steps in the Given Run and Test
   * 
   * @param mixed $run Run ID or Run Entity
   * @param mixed $test Test ID or Test Entity
   * @return integer Number of Related Steps
   * @throws \Exception On Any Failure
   */
  public static function countByTest($run) {
    assert('isset($run)');
    assert('isset($test)');

    // Are we able to extract the Run ID from the Parameter?
    $run_id = \models\Run::extractID($run);
    if (!isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      "conditions" => '[run] = :run_id: and test = :test_id:',
      "bind" => ['run_id' => $run_id, 'test_id' => $test_id]
    ];

    // Find Child Entries
    $count = self::count($params);

    // Return Result Set
    return (integer) $count;
  }

  /**
   * Find the Play Entry that associates the Run and Test
   * 
   * @param mixed $run Run ID / Entity
   * @param mixed $test Test ID / Entity
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findEntry($run, $test) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    $entry = self::findFirst(array(
        "conditions" => 'run = :run: and test = :test:',
        "bind" => array('run' => $run_id, 'test' => $test_id))
    );
    return $entry !== FALSE ? $entry : null;
  }

  /**
   * Find the Play Entry for the Given Run with the Given Sequence
   * 
   * @param mixed $run Run ID / Entity
   * @param integer $sequence Sequence for the Entry
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function findBySequence($run, $sequence) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Try to Find the Relation
    $entry = self::findFirst(array(
        "conditions" => 'run = :run: and sequence = :sequence:',
        "bind" => array('run' => $run_id, 'sequence' => $sequence))
    );
    return $entry !== FALSE ? $entry : null;
  }

}
