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
 * Test Step Entity (Encompasses all the Information of a Single Test Step).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class TestStep extends \api\model\AbstractEntity {

  const SEQUENCE_STEP = 100;
  const MAX_SEQUENCE = 100000;

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
  public $title;

  /**
   *
   * @var string
   */
  public $description;

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
    $this->hasMany("test", "models\Test", "id");
    // A Single User can Be the Creator for Many Test Steps
    $this->hasMany("creator", "models\User", "id");
    // A Single User can Be the Modifier for Many Test Steps
    $this->hasMany("modifier", "models\User", "id");
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
      'title' => 'title',
      'description' => 'description',
      'id_creator' => 'creator',
      'dt_creation' => 'date_created',
      'id_modifier' => 'modifier',
      'dt_modified' => 'date_modified',
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
    $this->test = (integer) $this->test;
    $this->sequence = (integer) $this->sequence;
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
    return "step";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'test', null, $header);
    $array = $this->addProperty($array, 'sequence', null, $header);
    $array = $this->addProperty($array, 'title', null, $header);
    $array = $this->setDisplayField($array, 'title', $header);
    $array = $this->addPropertyIfNotNull($array, 'description', null, $header);
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
   * Try to Extract a Test Step ID from the incoming parameter
   * 
   * @param mixed $step Test Step Entity (object) or Test Step ID (integer)
   * @return mixed Returns the Test ID or 'null' on failure;
   */
  public static function extractStepID($step) {
    assert('isset($step)');

    // Is the parameter an Test Object?
    if (is_object($step) && is_a($step, __CLASS__)) { // YES
      return $step->id;
    } else if (is_integer($step) && ($step >= 0)) { // NO: It's a Positive Integer
      return $step;
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
   * Find the First Step in the Test 
   * 
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function firstStep($test) {
    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractTestID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => 'test = :id:',
      "bind" => ['id' => $id],
      "order" => 'sequence'
    ];

    $step = self::findFirst($params);
    return $step !== FALSE ? $step : null;
  }

  /**
   * Find the Last Step in the Test 
   * 
   * @param mixed $test Test ID or Test Entity
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function lastStep($test) {
    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractTestID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => 'test = :id:',
      "bind" => ['id' => $id],
      "order" => 'sequence DESC'
    ];

    $step = self::findFirst($params);
    return $step !== FALSE ? $step : null;
  }

  /**
   * Find the Step in the Test with the Given Sequence
   * 
   * @param mixed $test Test ID or Test Entity
   * @param integer $sequence Step Sequence Number
   * @return mixed Returns Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findStep($test, $sequence) {
    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractTestID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    // Have we been provided with a Valid Sequence?
    if (!is_integer($sequence) || ($sequence <= 0)) { // NO:
      throw new \Exception("Sequence Parameter is invalid.", 2);
    }


    $params = [
      "conditions" => 'test = :id: and sequence = :sequence:',
      "bind" => ['id' => $id, 'sequence' => $sequence],
      "order" => 'sequence'
    ];

    $step = self::findFirst($params);
    return $step !== FALSE ? $step : null;
  }

  /**
   * Find the Previous Step before the Given Step
   * 
   * @param TestStep $step Test ID or Test Entity
   * @return TestStep Previous Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function previousStep(TestStep $step) {
    assert('isset($step)');

    // Build Query Parameters
    $params = [
      "conditions" => 'test = :test: and sequence < :sequence:',
      "bind" => ['test' => $step->test, 'sequence' => $step->sequence],
      "order" => 'sequence DESC'
    ];

    $previous = self::findFirst($params);
    return $previous !== FALSE ? $previous : null;
  }

  /**
   * Find the Next Step after the Given Step
   * 
   * @param TestStep $step Test ID or Test Entity
   * @return TestStep Next Step or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function nextStep(TestStep $step) {
    assert('isset($step)');

    // Build Query Parameters
    $params = [
      "conditions" => 'test = :test: and sequence > :sequence:',
      "bind" => ['test' => $step->test, 'sequence' => $step->sequence],
      "order" => 'sequence'
    ];

    $next = self::findFirst($params);
    return $next !== FALSE ? $next : null;
  }

  public static function sequenceRange($test, $contains) {
    assert('isset($test)');
    assert('isset($test) && is_integer($contains)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractTestID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 1);
    }

    // Start and End of Range
    $start = $contains;
    $end = $contains;

    // Do we want the 1st range area?
    if ($contains <= 0) { // YES
      $start = 0;
      $next = self::firstStep($test);
    } else { // NO : Possibly
      // Find Closest, with smaller or equal to the given sequence
      $params = [
        "conditions" => 'test = :id: and sequence <= :sequence:',
        "bind" => ['id' => $id, 'sequence' => $contains],
        "order" => 'sequence DESC'
      ];
      $step = self::findFirst($params);
      // Find Closest, with sequence higher to the given      
      $params = [
        "conditions" => 'test = :id: and sequence > :sequence:',
        "bind" => ['id' => $id, 'sequence' => $contains],
        "order" => 'sequence'
      ];
      $next = self::findFirst($params);
      $start = isset($step) ? $step->sequence : 0;
    }

    $end = isset($next) ? $next->sequence : $start;
    return [$start, $end];
  }

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
  public static function newStep($test, $title, $sequence = null) {
    assert('isset($test)');
    assert('isset($title) && is_string($title)');
    assert('!isset($sequence) || is_integer($sequence)');

    $renumber = false;
    if (!isset($sequence) || ($sequence >= self::MAX_SEQUENCE)) {
      $last = self::lastStep($test);
      $sequence = isset($last) ? $last->sequence + self::SEQUENCE_STEP : self::SEQUENCE_STEP;
    } else {
      list($start, $end) = self::sequenceRange($test, $sequence);
      if ($start === $end) {
        $sequence = $start + self::SEQUENCE_STEP;
      } else {
        $difference = $end - $start;
        if (($end - $start) > 1) {
          $sequence = $start + floor($difference / 2);
        } else {
          throw new \Exception("Unable to Insert Step at after [{$sequence}]. Please renumber the Steps and try again", 1);
        }
      }
    }

    // Create the Step
    $step = new TestStep();
    $step->test = $test->id;
    $step->title = $title;
    $step->sequence = $sequence;

    return $step;
  }

  /**
   * Move Step Up One Place in the List or Before the Specified Step
   * 
   * @param TestStep $step Current Step 
   * @param integer $before OPTIONAL Before Step Sequence
   * @return TestStep Modified Test Step, or null, if no change
   * @throws \Exception On Any Failure
   */
  public static function moveUp(TestStep $step, $before = null) {
    assert('isset($step)');
    assert('!isset($before) || is_integer($before)');

    // Did we specify a before step?
    $previous = null;
    if (!isset($before) || !is_integer($before)) { // NO: So Move Up one Position
      $previous = self::previousStep($step);
    } else
    // Are we really moving up the list?
    if ($step->sequence > $before) { // YES
      $previous = self::findStep($step->test, $before);
    }

    // Do we have a Before Step?
    if (isset($previous)) { // YES: Find the Pre-Before Step
      $pre_previous = self::previousStep($previous);
      $start = isset($pre_previous) ? $pre_previous->sequence : 0;
      $end = $previous->sequence;

      // New Sequence
      $difference = $end - $start;
      if ($difference > 1) {
        $sequence = $start + floor($difference / 2);

        // Were we able to re-position the step?
        $step->sequence = $sequence;
        if ($step->save() === FALSE) { // No
          throw new \Exception("Failed to Save the Test Step.", 1);
        }
        return $step;
      } else {
        throw new \Exception("No position to insert the step into. Please renumber the Steps and try again.", 1);
      }
    }

    return null;
  }

  /**
   * Move Step Down One Place in the List or After the Specified Step
   * 
   * @param TestStep $step Current Step 
   * @param integer $after OPTIONAL After Step Sequence
   * @return TestStep Modified Test Step, or null, if no change
   * @throws \Exception On Any Failure
   */
  public static function moveDown(TestStep $step, $after = null) {
    assert('isset($step)');
    assert('!isset($after) || is_integer($after)');

    // Did we specify a before step?
    $next = null;
    if (!isset($after) || !is_integer($after)) { // NO: So Move Down One Position
      $next = self::nextStep($step);
    } else
    // Are we really moving down the list?
    if ($step->sequence < $after) { // YES
      $next = self::findStep($step->test, $after);
    }

    // Do we have an After Step?
    if (isset($next)) { // YES: Find the Pre-After Step
      $post_next = self::nextStep($next);
      $start = $next->sequence;
      $end = isset($post_next) ? $post_next->sequence : $start + self::SEQUENCE_STEP * 2;

      // New Sequence
      $difference = $end - $start;
      if ($difference > 1) {
        $sequence = $start + floor($difference / 2);

        // Were we able to re-position the step?
        $step->sequence = $sequence;
        if ($step->save() === FALSE) { // No
          throw new \Exception("Failed to Save the Test Step.", 1);
        }
        return $step;
      } else {
        throw new \Exception("No position to insert the step into. Please renumber the Steps and try again.", 1);
      }
    }

    return null;
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
  public static function moveStep($test, TestStep $step, $after) {
    assert('isset($test)');
    assert('isset($step)');
    assert('isset($after) && is_integer($after)');

    list($start, $end) = self::sequenceRange($test, $after <= 0 ? null : $after);
    if ($start === $end) {
      $sequence = $start + self::SEQUENCE_STEP;
    } else {
      $difference = $end - $start;
      if (($end - $start) > 1) {
        $sequence = $start + floor($difference / 2);
      } else {
        throw new \Exception("Unable to Insert Step at after [{$sequence}]. Please renumber the Steps and try again", 1);
      }
    }

    $step->sequence = $sequence;
    // Were we able to flush the changes?
    if ($step->save() === FALSE) { // No
      throw new \Exception("Failed to Save the Test Step.", 1);
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
  public static function renumberSteps($test) {
    assert('is_integer($step) && ($step > 0)');

    // Get the List of Links for the Set
    $steps = self::listInTest($test, true);
    $count = count($steps);
    if (isset($steps) && $count) {
      // Re-sequence the links
      $next_seq = self::SEQUENCE_STEP * $count;
      $modified = [];
      foreach ($steps as $step) {
        // Is the New Sequence Number Different than the old?
        if ($next_seq !== $step->sequence) { // YES: Update
          $step->sequence = $next_seq;
          $modified[] = $step;
        }
        $next_seq-=self::SEQUENCE_STEP;
      }

      // Save Modified Steps
      foreach ($modified as $step) {
        // Were we able to flush the changes?
        if ($step->save() === FALSE) { // NO
          throw new \Exception("Failed to Save the Test Step.", 1);
        }
      }
      
      // Return Renumbered List
      return self::listInTest($test);
    }
    return $steps;
  }

  /**
   * List the Test Steps Related to the Specified Test
   * 
   * @param mixed $test Test ID or Test Entity
   * @param boolean $descending [DEFAULT false] Sort in Descending Order by Sequence
   * @return TestStep[] Related Test Steps
   * @throws \Exception On Any Failure
   */
  public static function listInTest($test, $descending = false) {
    assert('isset($test)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractTestID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => 'test = :id:',
      'bind' => ['id' => $id],
      'order' => !!$descending ? 'sequence DESC' : 'sequence'
    ];

    // Search for Matching Projects
    $steps = self::find($params);
    return $steps === FALSE ? null : $steps;
  }

  /**
   * Count the Number of Test Steps Related to the Specified Test
   * 
   * @param mixed $test Test ID or Test Entity
   * @return integer Number of Related Tests
   * @throws \Exception On Any Failure
   */
  public static function countInTest($test) {
    assert('isset($test)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractTestID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => 'test = :id:',
      'bind' => ['id' => $id]
    ];

    // Find Child Entries
    $count = self::count($params);

    // Return Result Set
    return (integer) $count;
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
