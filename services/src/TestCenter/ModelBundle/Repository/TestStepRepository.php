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
 * Description of TestStepRepository
 *
 * @author Paulo Ferreira
 */
class TestStepRepository
  extends EntityRepository {

  /**
   * 
   * @param type $test
   * @param type $sequence
   * @return type
   */
  public function findStep($test, $sequence) {
    assert('isset($test) && is_object($test)');
    assert('isset($sequence) && is_integer($sequence) && ($sequence > 0)');

    return $this->findOneBy(array('test' => $test, 'sequence' => $sequence));
  }

  /**
   * 
   * @param type $test
   * @return type
   */
  public function firstStep($test) {
    assert('isset($test) && is_object($test)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->getEntityName(), 'e')
      ->where('e.test = ?1')
      ->orderBy('e.sequence')
      ->setParameter(1, $test)
      ->setMaxResults(1)
      ->getQuery();

    // Return the 1st Step (if it exists)
    return $query->getOneOrNullResult();
  }

  /**
   * 
   * @param type $test
   * @param type $stepseq
   * @return type
   */
  public function nextStep($test, $stepseq = -1) {
    assert('isset($test) && is_object($test)');
    assert('isset($stepseq) && (is_object($stepseq) || is_integer($stepseq))');

    // Allow for Passing in Sequence Number or a Step (Link Object)
    $sequence = is_object($stepseq) ? $stepseq->getSequence() : $stepseq;

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->getEntityName(), 'e')
      ->where('e.test = ?1 and e.sequence > ?2')
      ->orderBy('e.sequence')
      ->setParameter(1, $test)
      ->setParameter(2, $sequence)
      ->setMaxResults(1)
      ->getQuery();

    // Return the Next Step (if it exists)
    return $query->getOneOrNullResult();
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function lastStep($test) {
    assert('isset($test) && is_object($test)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->getEntityName(), 'e')
      ->where('e.test = ?1')
      ->orderBy('e.sequence', 'DESC')
      ->setParameter(1, $test)
      ->setMaxResults(1)
      ->getQuery();

    // Return the Last Step (if it exists)
    return $query->getOneOrNullResult();
  }

  /**
   * 
   * @param type $test
   * @param type $sequence
   * @return \TestCenter\ModelBundle\Entity\TestStep
   * @throws \Exception
   */
  public function createStep($test, $title, $sequence) {
    assert('isset($test) && is_object($test)');
    assert('isset($title) && is_string($title)');
    assert('!isset($sequence) || (is_integer($sequence) && ($sequence > 0))');

    if (isset($sequence)) {
      $step = $this->findStep($test, $sequence);
      if (isset($step)) {
        throw new \Exception("Sequence #[$sequence] already exists in Test [{$test->getId()}]");
      }
    } else {
      // Get the Last Step if it exists
      $step = $this->lastStep($test);
      // Calculate the Next Step Sequence
      $sequence = isset($step) ? $step->getSequence() + 1 : 1;
    }

    // Create the Step
    $step = new \TestCenter\ModelBundle\Entity\TestStep();
    $step->setTest($test);
    $step->setTitle($title);
    $step->setSequence($sequence);

    // Persist the Step
    $this->getEntityManager()->persist($step);
    $this->getEntityManager()->flush();

    return $step;
  }

  /**
   * 
   * @param type $test
   * @param type $sequence
   * @return boolean
   * @throws \Exception
   */
  public function removeStep($test, $sequence) {
    assert('isset($test) && is_object($test)');
    assert('isset($sequence) && is_integer($sequence) && ($sequence > 0)');

    // Find the Step to Remove
    $step = $this->findStep($test, $sequence);

    // Throw Exception if We Don't Find the Step
    if (!isset($step)) {
      throw new \Exception("There is no Step with Sequence #[$sequence] in the Test [{$test->getId()}].", 3);
    }

    // Remove the Step
    $this->getEntityManager()->remove($step);
    $this->getEntityManager()->flush();

    return true;
  }

  /**
   * 
   * @param type $test
   * @param type $sequence
   * @param type $to
   * @return boolean
   * @throws \Exception
   */
  public function moveStep($test, $sequence, $to) {
    assert('isset($test) && is_object($test)');
    assert('isset($sequence) && is_integer($sequence) && ($sequence > 0)');
    assert('isset($to) && is_integer($to) && ($to > 0)');

    // Find the Step to Move
    $step = $this->findStep($test, $sequence);

    // Throw Exception if We Don't Find the Step
    if (!isset($step)) {
      throw new \Exception("There is no Step with Sequence #[$sequence] in the Test [{$test->getId()}].", 3);
    }

    // See if Destination Sequence is Occupied
    $destination = $this->findStep($test, $to);
    if (isset($destination)) {
      throw new \Exception("Destination Sequence[$to] already exists in the Test [{$test->getId()}].", 3);
    }

    // Modify Sequence Number and Flush Changes
    $step->setSequence($to);
    $this->getEntityManager()->flush();

    return $step;
  }

  /**
   * 
   * @param type $test
   * @param type $step
   * @return boolean
   */
  public function renumberSteps($test, $step) {
    assert('isset($test) && is_object($test)');
    assert('is_integer($step) && ($step > 0)');

    // Get the List of Links for the Set
    $steps = $this->listSteps($test);
    $count = count($steps);
    if (isset($steps) && $count) {

      // Re-sequence the links
      $next_seq = $step;
      foreach ($steps as $link) {
        $link->setSequence($next_seq);
        $next_seq+=$step;
      }

      // Flush Changes back 
      $this->getEntityManager()->flush();
    }

    return $count;
  }

  /**
   * 
   * @param type $test
   * @return type
   */
  public function listSteps($test) {
    assert('isset($test) && is_object($test)');

    return $this->findBy(array('test' => $test), array('sequence' => 'ASC'));
  }

  /**
   * 
   * @param type $test
   * @return type
   */
  public function countSteps($test) {
    assert('isset($test) && is_object($test)');

    // Build Query
    $sql = 'SELECT count(e) ' .
      " FROM {$this->getEntityName()} e  " .
      ' WHERE e.test = ?1';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $test);

    $count = $query->getSingleScalarResult();
    return (integer) $count;
  }

  /**
   * 
   * @param type $set
   * @return type
   */
  public function removeAllStepsFrom($test) {
    assert('isset($test) && is_object($test)');

    // Build Query
    $sql = "DELETE FROM {$this->getEntityName()} e" .
      ' WHERE e.test = ?1';
    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $test);

    // Returns Number of Records Deleted
    return $query->getScalarResult();
  }

}