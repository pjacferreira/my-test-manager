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
    $this->sequence = (integer) $this->sequence;
    $this->test = (integer) $this->test;
    $this->step = (integer) $this->step;
    $this->code = (integer) $this->run_code;
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

  /**
   * Create a New Play Entry for the Run, Test, Sequence and Comment.
   * 
   * @param mixed $run Run ID / Entity
   * @param mixed $test Test ID / Entity
   * @param integer $sequence OPTIONAL Step Sequence Number (if not given then
   *   the step will given a sequence that places it last in the list)
   * @param string $comment OPTIONAL Comment associated with Play Entry
   * @return \PlayEntry Newly Created Entity
   * @throws \Exception On Any Failure
   */
  public function createEntry($run, $test, $sequence = null, $comment = null) {
    assert('!isset($sequence) || (is_integer($sequence) && ($sequence > 0))');
    assert('!isset($comment) || is_string($comment)');

    // Are qe creating an Entry with a Specific Sequence?
    if (isset($sequence)) { // YES
      $entry = self::findEntry($run, $sequence);
      if (isset($entry)) {
        throw new \Exception("Sequence #[$sequence] already exists in Run [{$entry->run}]");
      }
    } else { // NO
      // Get the Last Entry, if it exists
      $entry = self::lastEntry($run);
      // Calculate the Next Entry Sequence
      $sequence = isset($entry) ? $entry->sequence + 10 : 10;
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    // Create the Link
    $entry = new PlayEntry();
    $entry->run = $run;
    $entry->test = $test_id;

    // Do we have a comment for the Entry?
    $comment = Strings::nullOnEmpty($comment);
    if (isset($comment)) {
      $entry->comment = $comment;
    }
    $entry->sequence = $sequence;

    // Were we able to flush the changes?
    if ($entry->save() === FALSE) { // No
      throw new \Exception("Failed to Save the Play Entry.", 1);
    }

    return $entry;
  }

  /**
   * Delete a Specific Entry from the Run
   * 
   * @param mixed $run Run Set ID / Entity
   * @param integer $sequence Step Sequence Number
   * @return mixed Returns Play Entry or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function deleteStep($run, $sequence) {
    // Find the Entry to Remove
    $entry = self::findEntry($run, $sequence);

    // Did we find the entry?
    if (isset($entry)) { // YES
      // Were we able to delete the Step?
      if ($entry->delete() === FALSE) { // NO
        throw new \Exception("Failed to Delete Step [{$entry->sequence}] for Run [{$entry->run}].", 2);
      }
    }

    return $entry;
  }

  /**
   * Delete All Run Steps for the Specified Run
   * 
   * @param mixed $run Run Set ID / Entity
   * @throws \Exception On Any Failure
   */
  public function deleteAllRunEntries($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM PlayEntry WHERE run = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $run_id)) === FALSE) {
      throw new \Exception("Failed Deleting Steps for Run[{$run_id}].", 1);
    }
  }

  /**
   * Find the Next Available Sequence Number for the Run
   * 
   * @param mixed $run Run ID / Entity
   * @param integer Next Available Sequence number (Last Sequence Number + 10)
   * @throws \Exception On Any Failure
   */
  public function nextSequence($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('SELECT MAX(sequence) FROM PlayEntry WHERE run = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $run_id)) === FALSE) {
      throw new \Exception("Failed Attempt to Obtain Last Sequence Number for Run [{$run_id}].", 1);
    }

    // Execute the query returning a result if any
    $result = $query->getFirst();

    return $result ? 10 : (integer) $result['0'] + 10;
  }

  /**
   * 
   * @param type $run
   * @param type $sequence
   * @return type
   */
  public function hasLink($run, $sequence) {
    return self::findBySequence($run, $sequence) !== null;
  }

  /**
   * Find the Last Play Entry for the Run
   * 
   * @param mixed $run Run ID / Entity
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function lastEntry($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    return self::findFirst(array(
        "conditions" => 'run = :id:',
        "bind" => array('id' => $run_id),
        "order" => 'sequence DESC')
    );
  }

  /**
   * List the Play Entries for the Run
   * 
   * @param mixed $run Run ID / Entity
   * @return \PlayEntry[] Play Entries for Run
   * @throws \Exception On Any Failure
   */
  public function listInRun($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    return self::find(array(
        "conditions" => 'run = :id:',
        "bind" => array('id' => $test_id),
        "order" => "sequence"
    ));
  }

  /**
   * Count the Number of Play Entries for the Run
   * 
   * @param mixed $run Run ID / Entity
   * @return integer Number of Play Entries
   * @throws \Exception On Any Failure
   */
  public function countInRun($run) {
    // Are we able to extract the Run ID from the Parameter?
    $run_id = \Run::extractID($run);
    if (isset($run_id)) { // NO
      throw new \Exception("Run Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) FROM PlayEntry WHERE run = :id:';
    $query = new Phalcon\Mvc\Model\Query($pqhl, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $result = $query->execute(array('id' => $run_id))->getFirst();
    return (integer) $result['0'];
  }

}
