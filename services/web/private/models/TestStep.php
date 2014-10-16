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
 * Test Step Entity (Encompasses all the Information of a Single Test Step).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class TestStep extends api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var integer
   */
  public $test;

  /**
   *
   * @var integer
   */
  public $sequence;

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
    // A Step can only Belong to a Single Test
    $this->belongsTo("test", "Test", "id");
  }

  /**
   * Define alternate table name for test steps
   * 
   * @return string Test Steps Table Name
   */
  public function getSource() {
    return "t_test_steps";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'id_test' => 'test',
        'sequence' => 'sequence',
        'name' => 'name',
        'description' => 'description'
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
    return "teststep";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'test');
    $array = $this->addProperty($array, 'seqeuence');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'description');
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
   * Create a New Test Step, with the Given Title and Sequence.
   * 
   * @param mixed $test Test ID or Test Entity
   * @param string $title Title for Test Step
   * @param integer $sequence OPTIONAL Step Sequence Number (if not given then
   *   the step will given a sequence that places it last in the list)
   * @return \TestStep New Test Step
   * @throws \Exception On Any Failure
   */
  public static function createStep($test, $title, $sequence = null) {
    assert('isset($title) && is_string($title)');

    if (isset($sequence)) {
      $step = self::findStep($test, $sequence);
      if (isset($step)) {
        throw new \Exception("Sequence #[$sequence] already exists in Test [{$step->test}]", 1);
      }
    } else {
      // Get the Last Step if it exists
      $step = self::lastStep($test);
      // Calculate the Next Step Sequence
      $sequence = isset($step) ? $step->sequence + 10 : 10;
    }

    // Create the Step
    $step = new TestStep();
    $step->test = $test;
    $step->title = $title;
    $step->sequence = sequence;

    // Were we able to flush the changes?
    if ($step->save() === FALSE) { // No
      throw new \Exception("Failed to Save the Test Step.", 1);
    }

    return $step;
  }

  /**
   * Delete the Step from the Given Test
   * 
   * @param mixed $test Test ID or Test Entity
   * @param integer $sequence Step Sequence Number
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function deleteStep($test, $sequence) {
    // Find the Step to Remove
    $step = self::findStep($test, $sequence);

    // Did we find the Step?
    if (isset($step)) { // YES
      // Were we able to delete the Step?
      if ($step->delete() === FALSE) { // NO
        throw new \Exception("Failed to Delete Step [{$step->sequence}] for Test [{$step->test}].", 2);
      }
    }

    return $step;
  }

  /**
   * Find the Step in the Test with the Given Sequence
   * 
   * @param mixed $test Test ID or Test Entity
   * @param integer $sequence Step Sequence Number
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function findStep($test, $sequence) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    // Have we been provided with a Valid Sequence?
    if (!is_integer($sequence) || ($sequence <= 0)) { // NO:
      throw new \Exception("Sequence Parameter is invalid.", 1);
    }

    return self::findFirst(array(
                "conditions" => 'test = :id: and sequence = :sequence:',
                "bind" => array(
                    'id' => $test_id,
                    'sequence' => (integer) $sequence
                ))
    );
  }

  /**
   * Find the First Step in the Test 
   * 
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function firstStep($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    return self::findFirst(array(
                "conditions" => 'test = :id:',
                "bind" => array('id' => $test_id),
                "order" => 'sequence')
    );
  }

  /**
   * Find the Next Step in the Test Given the Sequence as Reference
   * 
   * @param mixed $test Test ID or Test Entity
   * @param mixed $sequence Test Step Sequence or Test Step Entity
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function nextStep($test, $sequence = 0) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    // Is the Sequence an Test Step Object?
    if (is_object($stepseq) && is_a($stepseq, __CLASS__)) { // YES
      $sequence = $stepseq->sequence;
    } else if (is_integer($stepseq) && ($stepseq = 0)) { // NO: It's a Positive Integer
      $sequence = (integer) $stepseq;
    } else { // NO: Unknown Type
      throw new \Exception("Sequence Parameter is invalid.", 1);
    }

    return self::findFirst(array(
                "conditions" => 'test = :id: and sequence > :sequence:',
                "bind" => array('id' => $test_id, 'sequence' => (integer) $sequence),
                "order" => 'sequence'
                    )
    );
  }

  /**
   * Find the Last Step in the Test 
   * 
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function lastStep($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    return self::findFirst(array(
                "conditions" => 'test = :id:',
                "bind" => array('id' => $test_id),
                "order" => 'sequence DESC')
    );
  }

  /**
   * Reposition the Test Step in the Test Sequence.
   * 
   * @param mixed $test Test ID or Test Entity
   * @param integer $sequence Current Step Sequence Number
   * @param integer $to New Step Sequence Number
   * @return \TestStep Modified Test Step
   * @throws \Exception On Any Failure
   */
  public static function moveStep($test, $sequence, $to) {
    // Find the Step to Move
    $step = self::findStep($test, $sequence);

    // Throw Exception if We Don't Find the Step
    if (!isset($step)) {
      throw new \Exception("There is no Step with Sequence #[$sequence].", 1);
    }

    // See if Destination Sequence is Occupied
    $destination = self::findStep($test, $to);
    if (isset($destination)) {
      throw new \Exception("Destination Sequence[$to] already exists.", 2);
    }

    // Modify Sequence Number and Flush Changes
    $step->sequence = $to;

    // Were we able to flush the changes?
    if ($step->save() === FALSE) { // No
      throw new \Exception("Failed to Save the Test Step.", 3);
    }

    return $step;
  }

  /**
   * Renumber all the Test Steps.
   * 
   * @param mixed $test Test ID or Test Entity
   * @param integer $step OPTIONAL Spacing Between Step Sequences (DEFAULT to 10)
   * @return integer Number of Steps in the Test
   * @throws \Exception On Any Failure
   */
  public function renumberSteps($test, $step = 10) {
    assert('is_integer($step) && ($step > 0)');

    // Get the List of Links for the Set
    $steps = self::listInTest($test);
    $count = count($steps);
    if (isset($steps) && $count) {

      // TODO: Add Transaction Management
      // Re-sequence the links
      $next_seq = $step;
      foreach ($steps as $step) {
        // Calculate New Sequence Number
        $step->sequence = $next_seq;

        // Were we able to flush the changes?
        if ($step->save() === FALSE) { // No
          throw new \Exception("Failed to Save the Test Step.", 1);
        }
        $next_seq+=$step;
      }
    }

    return $count;
  }

  /**
   * List the Test Steps Related to the Specified Test
   * 
   * @param mixed $test Test ID or Test Entity
   * @return \TestStep[] Related Test Steps
   * @throws \Exception On Any Failure
   */
  public function listInTest($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    return self::find(array(
                "conditions" => 'test = :id:',
                "bind" => array('id' => $test_id),
                "order" => "sequence"
    ));
  }

  /**
   * Count the Number of Test Steps Related to the Specified Test
   * 
   * @param mixed $test Test ID or Test Entity
   * @return integer Number of Related Tests
   * @throws \Exception On Any Failure
   */
  public function countInTest($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) FROM TestStep WHERE test = :id:';
    $query = new Phalcon\Mvc\Model\Query($pqhl, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $result = $query->execute(array('id' => $test_id))->getFirst();
    return (integer) $result['0'];
  }

  /**
   * Delete All Test Steps for the Specified Test
   * 
   * @param mixed $test Test ID or Test Entity
   * @throws \Exception On Any Failure
   */
  public function deleteAllTestSteps($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM TestStep WHERE test = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $test_id)) === FALSE) {
      throw new \Exception("Failed Deleting Steps for Test[{$test_id}].", 1);
    }
  }

}
