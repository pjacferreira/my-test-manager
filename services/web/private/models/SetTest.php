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
 * Set Test Relation (Links the Set and Test Entities and Associates a Sequence
 * within the Set).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SetTest extends \api\model\AbstractEntity {

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
  public $set;

  /**
   *
   * @var integer
   */
  public $sequence;

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
    // A Single Set can be the Owner of Many Entries
    $this->hasMany("set", "models\Set", "id");
    // A Single Test can be Used in Many Entries
    $this->hasMany("test", "models\Test", "id");
  }

  /**
   * Define alternate table name for Set Tests
   * 
   * @return string Set Tests Table Name
   */
  public function getSource() {
    return "t_set_tests";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
      'id' => 'id',
      'id_set' => 'set',
      'id_test' => 'test',
      'sequence' => 'sequence'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
    $this->set = (integer) $this->set;
    $this->test = (integer) $this->test;
    $this->sequence = (integer) $this->sequence;
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
    return "settest";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'set', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'test', null, $header);
    $array = $this->addProperty($array, 'sequence', null, $header);
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
   * Find the Link in the Set with the Given Sequence
   * 
   * @param mixed $set Set ID or Set Entity
   * @param integer $sequence Step Sequence Number
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findLink($set, $sequence) {
    // Are we able to extract the Set ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Have we been provided with a Valid Sequence?
    if (!is_integer($sequence) || ($sequence <= 0)) { // NO:
      throw new \Exception("Sequence Parameter is invalid.", 2);
    }


    $params = [
      "conditions" => '[set] = :id: and sequence = :sequence:',
      "bind" => ['id' => $id, 'sequence' => $sequence],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the Link in the Set with the Given Sequence
   * 
   * @param mixed $set Set ID or Set Entity
   * @param integer $sequence Step Sequence Number
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findLinkByTest($set, $test) {
    // Are we able to extract the Set ID from the Parameter?
    $set_id = \models\Set::extractID($set);
    if (!isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Are we able to extract the Set ID from the Parameter?
    $test_id = \models\Test::extractID($test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    $params = [
      "conditions" => '[set] = :set: and test = :test:',
      "bind" => ['set' => $set_id, 'test' => $test_id],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the First Link in the Set 
   * 
   * @param mixed $set Set ID or Set Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function first($set) {
    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[set] = :id:',
      "bind" => ['id' => $id],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the Last Link in the Set 
   * 
   * @param mixed $set Set ID or Set Entity
   * @return mixed Returns Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function last($set) {
    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    $params = [
      "conditions" => '[set] = :id:',
      "bind" => ['id' => $id],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the Previous Link before the Given Link
   * 
   * @param SetTest $link Link Entity
   * @return SetTest Previous Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function previous(SetTest $link) {
    assert('isset($link)');

    // Build Query Parameters
    $params = [
      "conditions" => '[set] = :set: and sequence < :sequence:',
      "bind" => ['set' => $link->set, 'sequence' => $link->sequence],
      "order" => 'sequence DESC'
    ];

    return self::findFirst($params);
  }

  /**
   * Find the Next Link after the Given Link
   * 
   * @param SetTest $link Link Entity
   * @return SetTest Next Link or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function next(SetTest $link) {
    assert('isset($link)');

    // Build Query Parameters
    $params = [
      "conditions" => '[set] = :set: and sequence > :sequence:',
      "bind" => ['set' => $link->set, 'sequence' => $link->sequence],
      "order" => 'sequence'
    ];

    return self::findFirst($params);
  }

  /**
   * Find a Range of Sequence Range that contains the Given Sequence 
   * 
   * @param mixed $set Set ID or Set Entity
   * @param integer $contains Sequence Number
   * @return type Array start and end sequence that contains the given sequence number
   * @throws \Exception On Any Failure
   */
  public static function sequenceRange($set, $contains) {
    assert('isset($set)');
    assert('isset($contains) && is_integer($contains)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Start and End of Range
    $start = $contains;
    $end = $contains;

    // Do we want the 1st range area?
    if ($contains <= 0) { // YES
      $start = 0;
      $next = self::first($set);
    } else { // NO : Possibly
      // Find Closest, with smaller or equal to the given sequence
      $params = [
        "conditions" => '[set] = :id: and sequence <= :sequence:',
        "bind" => ['id' => $id, 'sequence' => $contains],
        "order" => 'sequence DESC'
      ];
      $link = self::findFirst($params);
      // Find Closest, with sequence higher to the given      
      $params = [
        "conditions" => '[set] = :id: and sequence > :sequence:',
        "bind" => ['id' => $id, 'sequence' => $contains],
        "order" => 'sequence'
      ];
      $next = self::findFirst($params);
      $start = $link !== FALSE ? $link->sequence : 0;
    }

    $end = $next !== FALSE ? $next->sequence : $start;
    return [$start, $end];
  }

  /**
   * Create a New Relation, with the Given Sequence.
   * 
   * @param mixed $set Set ID or Entity
   * @param mixed $test Test ID or Entity
   * @param integer $sequence OPTIONAL Step Sequence Number (if not given then
   *   the step will given a sequence that places it last in the list)
   * @return SetTest New Link
   * @throws \Exception On Any Failure
   */
  public static function newLink($set, $test, $sequence = null) {
    assert('isset($set)');
    assert('isset($test)');
    assert('!isset($sequence) || is_integer($sequence)');

    // Are we able to extract the Set ID from the Parameter?
    $set_id = \models\Set::extractID($set);
    if (!isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \models\Test::extractID($test);
    if (!isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    $renumber = false;
    if (!isset($sequence) || ($sequence >= self::MAX_SEQUENCE)) {
      $last = self::last($set);
      $sequence = isset($last) ? $last->sequence + self::SEQUENCE_STEP : self::SEQUENCE_STEP;
    } else {
      list($start, $end) = self::sequenceRange($set, $sequence);
      if ($start === $end) {
        $sequence = $start + self::SEQUENCE_STEP;
      } else {
        $difference = $end - $start;
        if (($end - $start) > 1) {
          $sequence = $start + floor($difference / 2);
        } else {
          throw new \Exception("Unable to Insert Step at after [{$sequence}]. Please renumber the Steps and try again", 3);
        }
      }
    }

    // Create the Link
    $link = new SetTest();
    $link->set = $set_id;
    $link->test = $test_id;
    $link->sequence = $sequence;

    return $link;
  }

  /**
   * Move Step Up One Place in the List or Before the Specified Step
   * 
   * @param SetStep $link Relation to Move
   * @param integer $before OPTIONAL Before Step Sequence
   * @return SetStep Modified Relation, or null, if no change
   * @throws \Exception On Any Failure
   */
  public static function moveUp(SetTest $link, $before = null) {
    assert('isset($link)');
    assert('!isset($before) || is_integer($before)');

    // Did we specify a before step?
    $previous = null;
    if (!isset($before) || !is_integer($before)) { // NO: So Move Up one Position
      $previous = self::previous($link);
    } else
    // Are we really moving up the list?
    if ($link->sequence > $before) { // YES
      $previous = self::findLink($link->set, $before);
    }

    // Do we have a Before Step?
    if ($previous !== FALSE) { // YES: Find the Pre-Before Step
      $pre_previous = self::previous($previous);
      $start = $pre_previous !== FALSE ? $pre_previous->sequence : 0;
      $end = $previous->sequence;

      // New Sequence
      $difference = $end - $start;
      if ($difference > 1) {
        $sequence = $start + floor($difference / 2);

        // Were we able to re-position the step?
        $link->sequence = $sequence;
        if ($link->save() === FALSE) { // No
          throw new \Exception("Failed to Save the Test Step.", 1);
        }
        return $link;
      } else {
        throw new \Exception("No position to insert the step into. Please renumber the Steps and try again.", 1);
      }
    }

    return null;
  }

  /**
   * Move Step Down One Place in the List or After the Specified Step
   * 
   * @param SetStep $link Relation to Move
   * @param integer $after OPTIONAL After Step Sequence
   * @return SetStep Modified Link, or null, if no change
   * @throws \Exception On Any Failure
   */
  public static function moveDown(SetTest $link, $after = null) {
    assert('isset($link)');
    assert('!isset($after) || is_integer($after)');

    // Did we specify a before step?
    $next = null;
    if (!isset($after) || !is_integer($after)) { // NO: So Move Down One Position
      $next = self::next($link);
    } else
    // Are we really moving down the list?
    if ($link->sequence < $after) { // YES
      $next = self::findLink($link->test, $after);
    }

    // Do we have an After Step?
    if ($next !== FALSE) { // YES: Find the Pre-After Step
      $post_next = self::next($next);
      $start = $next->sequence;
      $end = $post_next !== FALSE ? $post_next->sequence : $start + self::SEQUENCE_STEP * 2;

      // New Sequence
      $difference = $end - $start;
      if ($difference > 1) {
        $sequence = $start + floor($difference / 2);

        // Were we able to re-position the step?
        $link->sequence = $sequence;
        if ($link->save() === FALSE) { // No
          throw new \Exception("Failed to Save the Test Step.", 1);
        }
        return $link;
      } else {
        throw new \Exception("No position to insert the step into. Please renumber the Steps and try again.", 1);
      }
    }

    return null;
  }

  /**
   * Reposition the Test Step in the Test Sequence.
   * 
   * @param mixed $set Set ID or Entity
   * @param SetStep $link Relation to Move
   * @param integer $after After this Sequence
   * @return SetStep Modified Relation
   * @throws \Exception On Any Failure
   */
  public static function move($set, SetTest $link, $after) {
    assert('isset($set)');
    assert('isset($link)');
    assert('isset($after) && is_integer($after)');

    list($start, $end) = self::sequenceRange($set, $after <= 0 ? null : $after);
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

    $link->sequence = $sequence;
    // Were we able to flush the changes?
    if ($link->save() === FALSE) { // No
      throw new \Exception("Failed to Save the Test Step.", 1);
    }
    return $link;
  }

  /**
   * Renumber all the Test Steps.
   * 
   * @param mixed $set Set ID or Entity
   * @param integer $step OPTIONAL Spacing Between Step Sequences (DEFAULT to 10)
   * @return integer Number of Steps in the Test
   * @throws \Exception On Any Failure
   */
  public static function renumber($set) {
    assert('isset($set)');

    // Get the List of Links for the Set
    $links = self::listLinks($set, true);
    $count = count($links);
    if ($count) {
      // Re-sequence the links
      $next_seq = self::SEQUENCE_STEP * $count;
      $modified = [];
      foreach ($links as $link) {
        // Is the New Sequence Number Different than the old?
        if ($next_seq !== $link->sequence) { // YES: Update
          $link->sequence = $next_seq;
          $modified[] = $link;
        }
        $next_seq-=self::SEQUENCE_STEP;
      }

      // Save Modified Steps
      foreach ($modified as $link) {
        // Were we able to flush the changes?
        if ($link->save() === FALSE) { // NO
          throw new \Exception("Failed to Save the Relation.", 1);
        }
      }

      // Did we modify anything? 
      if ($modified . count()) { // YES: Return new sorted list of links
        $links = self::listLinks($set);
      }
    }
    return $links;
  }

  /**
   * Renumber all the Test Steps.
   * 
   * @param mixed $set Set ID or Entity
   * @param integer $step OPTIONAL Spacing Between Step Sequences (DEFAULT to 10)
   * @return integer Number of Steps in the Test
   * @throws \Exception On Any Failure
   */
  public static function renumberTests($set) {
    assert('isset($set)');

    // Renumber Links
    self::renumber($set);
    // Return List of Tests
    return self::listTestsInSet($set);
  }

  /**
   * List the Test Steps Related to the Specified Test
   * 
   * @param mixed $set Set ID or Entity
   * @param boolean $descending [DEFAULT false] Sort in Descending Order by Sequence
   * @return Test[] Related Tests
   * @throws \Exception On Any Failure
   */
  public static function listLinks($set, $descending = false) {
    assert('isset($set)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => '[set] = :id:',
      'bind' => ['id' => $id],
      'order' => !!$descending ? 'sequence DESC' : 'sequence'
    ];

    // Search for Matching Projects
    $links = self::find($params);
    return $links === FALSE ? [] : $links;
  }

  /**
   * List the Tests Related to the Specified Set
   * 
   * @param mixed $set Set ID or Entity
   * @param boolean $descending [DEFAULT false] Sort in Descending Order by Sequence
   * @return Test[] Related Tests
   * @throws \Exception On Any Failure
   */
  public static function listTestsInSet($set, $descending = false) {
    assert('isset($set)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    /* NOTE: The choice of the Entity Used with FROM is important, as it
     * represents the type of entity that will be created, on rehydration.
     */
    $pqhl = 'SELECT t.*' .
      ' FROM models\Test t' .
      ' JOIN models\SetTest l' .
      ' WHERE l.test = t.id and l.[set] = :id:' .
      ' ORDER BY ' . (!!$descending ? 'l.sequence DESC' : 'l.sequence');

    // Execute Query and Return Results
    $tests = self::selectQuery($pqhl, [
        'id' => $id
    ]);
    return $tests !== FALSE ? $tests : [];
  }

  /**
   * Count the Number of Tests Related to the Specified Set
   * 
   * @param mixed $set Set ID or Entity
   * @return integer Number of Related Tests
   * @throws \Exception On Any Failure
   */
  public static function countTestsInSet($set) {
    assert('isset($set)');

    // Are we able to extract the Set ID from the Parameter?
    $id = \models\Set::extractID($set);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Build Query Conditions
    $params = [
      'conditions' => '[set] = :id:',
      'bind' => ['id' => $id]
    ];

    // Find Child Entries
    $count = self::count($params);

    // Return Result Set
    return (integer) $count;
  }

  /**
   * List the Tests Related to the Specified Set
   * 
   * @param mixed $set Set ID or Entity
   * @param boolean $descending [DEFAULT false] Sort in Descending Order by Sequence
   * @return Test[] Related Tests
   * @throws \Exception On Any Failure
   */
  public static function listSetsForTest($test, $descending = false) {
    assert('isset($set)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractID($test);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    /* NOTE: The choice of the Entity Used with FROM is important, as it
     * represents the type of entity that will be created, on rehydration.
     */
    $pqhl = 'SELECT s.*' .
      ' FROM models\Set s' .
      ' JOIN models\SetTest l' .
      ' WHERE l.[set] = s.id and l.test = :id:' .
      ' ORDER BY ' . (!!$descending ? 's.id DESC' : 's.id');

    // Execute Query and Return Results
    $sets = self::selectQuery($pqhl, [
        'id' => $id
    ]);
    return $sets !== FALSE ? $sets : [];
  }

  /**
   * Count the Number of Sets Related to the Specified Test
   * 
   * @param mixed $test Tedt ID or Entity
   * @return integer Number of Related Sets
   * @throws \Exception On Any Failure
   */
  public static function countSetsForTest($test) {
    assert('isset($set)');

    // Are we able to extract the Test ID from the Parameter?
    $id = \models\Test::extractID($test);
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
   * Delete All Tests for the Specified Set
   * 
   * @param mixed $set Set ID or Entity
   * @throws \Exception On Any Failure
   */
  public function deleteAllTests($set) {
    // Are we able to extract the Set ID from the Parameter?
    $set_id = \models\Set::extractID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM SetTest WHERE set = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $set_id)) === FALSE) {
      throw new \Exception("Failed Deleting Steps for set[{$set_id}].", 1);
    }
  }

}
