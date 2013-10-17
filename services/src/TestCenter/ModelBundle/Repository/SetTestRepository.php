<?php

/* Test Center - Compliance Testing Application
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
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

namespace TestCenter\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of SetTestRepository
 *
 * @author Paulo Ferreira
 */
class SetTestRepository
  extends EntityRepository {

  /**
   * 
   * @param type $set
   * @param type $sequence
   * @return type
   */
  public function findBySequence($set, $sequence) {
    assert('isset($set) && is_object($set)');
    assert('isset($sequence) && is_integer($sequence) && ($sequence > 0)');

    return $this->findOneBy(array('set' => $set, 'sequence' => $sequence));
  }

  /**
   * 
   * @param type $set
   * @param type $test
   * @return type
   */
  public function findByTest($set, $test) {
    assert('isset($set) && is_object($set)');
    assert('isset($test) && is_object($test)');

    return $this->findOneBy(array('set' => $set, 'test' => $test));
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function nextSequence($set) {
    assert('isset($set) && is_object($set)');

    // Build Query
    $sql = 'SELECT max(e.sequence) ' .
      " FROM {$this->getEntityName()} e" .
      ' WHERE e.testset = ?1';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $set);

    $result = $query->getResult();
    return isset($result[0][1]) ? $result[0][1] + 1 : 1;
  }

  /**
   * 
   * @param type $set
   * @param type $test
   * @param type $sequence
   * @throws \Exception
   */
  public function createLink($set, $test, $sequence) {
    assert('isset($set) && is_object($set)');
    assert('isset($test) && is_object($test)');
    assert('!isset($sequence) || (is_integer($sequence) && ($sequence > 0))');

    if (isset($sequence)) {
      $link = $this->findBySequence($set, $sequence);
      if (isset($link)) {
        throw new \Exception("Sequence #[$sequence] already exists in Test Set [{$set->getId()}]");
      }
    } else {
      $sequence = $this->nextSequence($set);
    }

    // Create the Link
    $link = new \TestCenter\ModelBundle\Entity\TestSetLink();
    $link->setTestset($set);
    $link->setTest($test);
    $link->setSequence($sequence);

    // Persist the Link
    $this->getEntityManager()->persist($link);
    $this->getEntityManager()->flush();

    return $link;
  }

  /**
   * 
   * @param type $set
   * @param type $sequence
   * @return boolean
   * @throws \Exception
   */
  public function removeLink($set, $sequence) {
    assert('isset($set) && is_object($set)');
    assert('isset($sequence) && (is_object($sequence) || (is_integer($sequence) && ($sequence > 0)))');

    // Find the Link to Remove
    if (is_integer($sequence)) {
      $link = $this->findBySequence($set, $sequence);
    } else {
      $link = $this->findByTest($set, $sequence);
    }

    // Throw Exception if We Don't Find the Link
    if (!isset($link)) {
      if (is_integer($sequence)) {
        throw new \Exception("There is no Test with Sequence #[$sequence] in the Test Set [{$set->getId()}].", 3);
      } else {
        throw new \Exception("Test[{$sequence->getId()}] is not part of the Test Set [{$set->getId()}].", 3);
      }
    }

    // Remove the Link
    $this->getEntityManager()->remove($link);
    $this->getEntityManager()->flush();

    return true;
  }

  /**
   * 
   * @param type $set
   * @param type $sequence
   * @param type $to
   * @return boolean
   * @throws \Exception
   */
  public function moveLink($set, $sequence, $to) {
    assert('isset($set) && is_object($set)');
    assert('isset($sequence) && (is_object($sequence) || (is_integer($sequence) && ($sequence > 0)))');
    assert('isset($to) && is_integer($to) && ($to > 0)');

    // Find the Link to Move
    if (is_integer($sequence)) {
      $link = $this->findBySequence($set, $sequence);
    } else {
      $link = $this->findByTest($set, $sequence);
    }

    // Throw Exception if We Don't Find the Link
    if (!isset($link)) {
      if (is_integer($sequence)) {
        throw new \Exception("There is no Test with Sequence #[$sequence] in the Test Set [{$set->getId()}].", 3);
      } else {
        throw new \Exception("Test[{$sequence->getId()}] is not part of the Test Set [{$set->getId()}].", 3);
      }
    }

    // See if Destination Sequence is Occupied
    $destination = $this->findBySequence($set, $to);
    if (isset($destination)) {
      throw new \Exception("Destination Sequence[$to] already exists in the Test Set [{$set->getId()}].", 3);
    }

    // Modify Sequence Number and Flush Changes
    $link->setSequence($to);
    $this->getEntityManager()->flush();

    return true;
  }

  /**
   * 
   * @param type $set
   * @param type $step
   * @return boolean
   */
  public function renumberLinks($set, $step) {
    assert('isset($set) && is_object($set)');
    assert('is_integer($step) && ($step > 0)');

    // Get the List of Links for the Set
    $links = $this->listLinks($set);
    if (isset($links) && count($links)) {

      // Re-sequence the links
      $next_seq = $step;
      foreach ($links as $link) {
        $link->setSequence($next_seq);
        $next_seq+=$step;
      }

      // Flush Changes back 
      $this->getEntityManager()->flush();
    }

    return true;
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function removeAllLinksTo($set) {
    assert('isset($set) && is_object($set)');

    // Build Query
    $sql = "DELETE FROM {$this->getEntityName()} e" .
      ' WHERE e.testset = ?1';
    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $set);

    // Returns Number of Records Deleted
    return $query->getScalarResult();
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function listTests($set) {
    assert('isset($set) && is_object($set)');

    // Create Filter Condition
    $_filter = array('set' => $set);

    // Find Results
    return $this->findBy($_filter, array('id' => 'ASC'));
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function listSets($test) {
    assert('isset($test) && is_object($test)');

    // Create Filter Condition
    $_filter = array('test' => $test);

    // Find Results
    return $this->findBy($_filter, array('id' => 'ASC'));
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function countTests($set) {
    assert('isset($set) && is_object($set)');

    // Build Query
    $sql = 'SELECT count(e) ' .
      " FROM {$this->getEntityName()} e" .
      ' WHERE e.testset = ?1';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $set);

    $count = $query->getScalarResult();
    return (integer) $count[0][1];
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function countSets($test) {
    assert('isset($test) && is_object($test)');

    // Build Query
    $sql = 'SELECT count(e) ' .
      " FROM {$this->getEntityName()} e" .
      ' WHERE e.test = ?1';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $test);

    $count = $query->getScalarResult();
    return (integer) $count[0][1];
  }

}