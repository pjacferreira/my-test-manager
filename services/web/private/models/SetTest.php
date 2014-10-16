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
 * Set Test Relation (Links the Set and Test Entities and Associates a Sequence
 * within the Set).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SetTest extends api\model\AbstractEntity {

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
    $this->belongsTo("set", "Set", "id");
    // A Single Test can be Used in Many Entries
    $this->belongsTo("test", "Test", "id");
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
   * @return array Map of field <--> value tuplets
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addReferencePropertyIfNotNull($array, 'set');
    $array = $this->addProperty($array, 'sequence');
    $array = $this->addReferencePropertyIfNotNull($array, 'test');
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
   * Find the Relation between the Test and Test Set
   * 
   * @param mixed $set Test Set ID or Entity
   * @param mixed $test Test ID or Entity
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findRelation($set, $test) {
    // Are we able to extract the Set ID from the Parameter?
    $set_id = \Set::extractSetID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    $link = self::findFirst(array(
                "conditions" => 'set = :set: and test = :test:',
                "bind" => array('set' => $set_id, 'test' => $test_id))
    );
    return $link !== FALSE ? $link : null;
  }

  /**
   * Find the Relation the Relation in the Test Set with the Given Sequence
   * 
   * @param mixed $set Test Set ID or Entity
   * @param integer $sequence OPTIONAL Sequence for the Relation
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function findBySequence($set, $sequence) {
    // Are we able to extract the Set ID from the Parameter?
    $set_id = \Set::extractSetID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Try to Find the Relation
    $link = self::findFirst(array(
                "conditions" => 'set = :set: and sequence = :sequence:',
                "bind" => array('set' => $set_id, 'sequence' => $sequence))
    );
    return $link !== FALSE ? $link : null;
  }

  /**
   * Create the Relation between the Test Set and Set
   * 
   * @param mixed $set Test Set ID or Entity
   * @param mixed $test Test ID or Entity
   * @param integer $sequence OPTIONAL Sequence for the Relation (if not provided
   *   then the Relation will be added to the end of the list)
   * @return \TestSet Returns Relation 
   * @throws \Exception On Any Failure
   */
  public static function addRelation($set, $test, $sequence = null) {
    assert('!isset($sequence) || (is_integer($sequence) && ($sequence > 0))');

    // Do we have Sequence Number Defined?
    if (isset($sequence)) { // YES
      $link = self::findBySequence($set, $sequence);
      // Did we find an existing relation?
      if (isset($link)) { // YES
        throw new \Exception("Sequence #[$sequence] already exists in Test Set [{$link->set}]");
      }
    } else { // NO
      $sequence = self::nextSequence($set);
    }

    // Create the Relation
    $link = new TestSet();
    $link->set = $set;
    $link->test = $test;
    $link->sequence = $sequence;

    // Were we able to flush the changes?
    if ($link->save() === FALSE) { // No
      throw new \Exception("Failed to Create/Update User<-->Organization Link.", 1);
    }

    return $link;
  }

  /**
   * Delete the Relation between the Test Set and Set
   * 
   * @param mixed $set Test Set ID or Entity
   * @param mixed $test Test ID or Entity
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public function deleteRelation($set, $test) {
    // See if the Link Exists Already
    $link = self::findRelation($test, $set);

    // Does the Link Exist Already?
    if (isset($link)) { // YES: Delete It
      // Were we able to delete the link?
      if ($link->delete() === FALSE) { // NO
        throw new \Exception("Failed to Delete Set<-->Test Link.", 1);
      }
    }

    return $link;
  }

  /**
   * Delete the Relation for a Set / Sequence
   * 
   * @param mixed $set Test Set ID or Entity
   * @param integer $sequence Sequence for the Relation
   * @return mixed Returns Deleted Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function deleteBySequence($set, $sequence) {
    // See if the Link Exists Already
    $link = self::findBySequence($set, $sequence);

    // Does the Link Exist Already?
    if (isset($link)) { // YES: Delete It
      // Were we able to delete the link?
      if ($link->delete() === FALSE) { // NO
        throw new \Exception("Failed to Delete User<-->Organization Link.", 1);
      }
    }

    return $link;
  }

  /**
   * Delete All Relations to a Given Test Set
   * 
   * @param mixed $set Test Set ID or Entity
   * @throws \Exception On Any Failure
   */
  public static function deleteBySet($set) {
    // Are we able to extract the Test Set ID from the Parameter?
    $set_id = \Set::extractSetID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM SetTest WHERE set = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $set_id)) === FALSE) {
      throw new \Exception("Failed Deleting Test<-->Set Relations for Set[{$set_id}].", 1);
    }
  }

  /**
   * Delete All Relations to a Given Test
   * 
   * @param mixed $test Test ID or Entity
   * @throws \Exception On Any Failure
   */
  public static function deleteByTest($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM SetTest WHERE test = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $test_id)) === FALSE) {
      throw new \Exception("Failed Deleting Test<-->Set Relations for Test[{$test_id}].", 1);
    }
  }

  /**
   * Find the Next Available Sequence Number for the Test Set
   * 
   * @param mixed $set Test Set ID or Entity
   * @param integer Next Available Sequence number (Last Sequence Number + 10)
   * @throws \Exception On Any Failure
   */
  public function nextSequence($set) {
    // Are we able to extract the Test Set ID from the Parameter?
    $set_id = \Set::extractSetID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('SELECT MAX(sequence) FROM SetTest WHERE set = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $set_id)) === FALSE) {
      throw new \Exception("Failed Attempt to Obtain Last Sequence Number for Set [{$set_id}].", 1);
    }

    // Execute the query returning a result if any
    $result = $query->getFirst();

    return $result ? 10 : (integer) $result['0'] + 10;
  }

  /**
   * Move the Test's Sequence.
   * 
   * @param mixed $set Test Set ID / Entity
   * @param integer $sequence Current Sequence Number
   * @param integer $to New Sequence Number
   * @return \SetTest Modified Relation
   * @throws \Exception On Any Failure
   */
  public static function moveSequence($set, $sequence, $to) {
    // Find the Link to Move
    $source = self::findBySequence($set, $sequence);

    // Throw Exception if We Don't Find the Step
    if (!isset($source)) {
      throw new \Exception("There is no Relation with Sequence #[$sequence].", 1);
    }

    // See if Destination Sequence is Occupied
    $destination = self::findBySequence($set, $to);
    if (isset($destination)) {
      throw new \Exception("Destination Sequence[$to] already exists.", 2);
    }

    // Modify Sequence Number and Flush Changes
    $source->sequence = $to;

    // Were we able to flush the changes?
    if ($source->save() === FALSE) { // No
      throw new \Exception("Failed to Save the Relation.", 3);
    }

    return $source;
  }

  /**
   * Renumber all the Test Sequence in the Specified Test.
   * 
   * @param mixed $set Test Set ID / Entity
   * @param integer $step OPTIONAL Spacing Between Step Sequences (DEFAULT to 10)
   * @return integer Number of Steps in the Test
   * @throws \Exception On Any Failure
   */
  public function renumberSequence($set, $step = 10) {
    assert('is_integer($step) && ($step > 0)');

    // Get the List of Links for the Set
    $links = self::listInSet($set);
    $count = count($links);
    if (isset($links) && $count) {

      // TODO: Add Transaction Management
      // Re-sequence the links
      $next_seq = $step;
      foreach ($links as $link) {
        // Calculate New Sequence Number
        $link->sequence = $next_seq;

        // Were we able to flush the changes?
        if ($link->save() === FALSE) { // No
          throw new \Exception("Failed to Save the Set<-->Test Relation.", 1);
        }
        $next_seq+=$step;
      }
    }

    return $count;
  }

  /**
   * List the Tests in the Specified Set
   * 
   * @param mixed $set Test Set ID / Entity
   * @return \SetTest[] Test<-->Set Relations
   * @throws \Exception On Any Failure
   */
  public function listInSet($set) {
    // Are we able to extract the Test Set ID from the Parameter?
    $set_id = \Set::extractSetID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    return self::find(array(
                "conditions" => 'set = :id:',
                "bind" => array('id' => $set_id),
                "order" => "sequence"
    ));
  }

  /**
   * Count the Number of Tests in the Specified Set
   * 
   * @param mixed $set Test Set ID / Entity
   * @return integer Number of Related Tests
   * @throws \Exception On Any Failure
   */
  public function countInSet($set) {
    // Are we able to extract the Test Set ID from the Parameter?
    $set_id = \Set::extractSetID($set);
    if (isset($set_id)) { // NO
      throw new \Exception("Set Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) FROM SetTest WHERE set = :id:';
    $query = new Phalcon\Mvc\Model\Query($pqhl, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $result = $query->execute(array('id' => $set_id))->getFirst();
    return (integer) $result['0'];
  }

  /**
   * List the Sets the Specified Test Belongs To
   * 
   * @param mixed $test Test ID / Entity
   * @return \SetTest[] Test<-->Set Relations
   * @throws \Exception On Any Failure
   */
  public function listInSets($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    return self::find(array(
                "conditions" => 'test = :id:',
                "bind" => array('id' => $test_id),
                "order" => "set"
    ));
  }

  /**
   * Count the number of Sets the Specified Test Belongs To
   * 
   * @param mixed $test Test ID / Entity
   * @return integer Number of Related Sets
   * @throws \Exception On Any Failure
   */
  public function countSets($test) {
    // Are we able to extract the Test ID from the Parameter?
    $test_id = \Test::extractTestID($test);
    if (isset($test_id)) { // NO
      throw new \Exception("Test Parameter is invalid.", 2);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) FROM SetTest WHERE test = :id:';
    $query = new Phalcon\Mvc\Model\Query($pqhl, \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    $result = $query->execute(array('id' => $test_id))->getFirst();
    return (integer) $result['0'];
  }

}
