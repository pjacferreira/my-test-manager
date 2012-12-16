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
 * Description of RunLinkRepository
 *
 * @author Paulo Ferreira
 */
class RunLinkRepository
  extends EntityRepository {

  /**
   * 
   * @param type $run
   * @return type
   */
  public function removeAllLinksTo($run) {
    assert('isset($run) && is_object($run)');

    // Build Query
    $sql = "DELETE FROM {$this->getEntityName()} e" .
      ' WHERE e.run = ?1';
    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $run->getId());

    // Returns Number of Records Deleted
    return $query->getScalarResult();
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function findLink($run, $testseq) {
    assert('isset($run) && is_object($run)');

    // Set Criteria for Search
    $criteria = array('run' => $run);
    if (is_int($testseq)) {
      $criteria['sequence'] = $testseq;
    } else {
      $criteria['test'] = $testseq;
    }

    return parent::findOneBy($criteria);
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function firstLink($run) {
    assert('isset($run) && is_object($run)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->getEntityName(), 'e')
      ->where('e.run = ?1')
      ->orderBy('e.sequence')
      ->setParameter(1, $run)
      ->setMaxResults(1)
      ->getQuery();

    // Return the 1st Link (if it exists)
    return $query->getOneOrNullResult();
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function nextLink($run) {
    assert('isset($run) && is_object($run)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->getEntityName(), 'e')
      ->where('e.run = ?1 and e.sequence > ?2')
      ->orderBy('e.sequence')
      ->setParameter(1, $run)
      ->setParameter(2, $run->getSequence())
      ->setMaxResults(1)
      ->getQuery();

    // Return the Next Link (if it exists)
    return $query->getOneOrNullResult();
  }

  /**
   * 
   * @param type $run
   * @param type $sequence
   * @return type
   */
  public function hasLink($run, $sequence) {
    assert('isset($run) && is_object($run)');
    assert('isset($sequence) && is_integer($sequence)');

    // Build Query
    $sql = 'SELECT count(e) ' .
      " FROM {$this->getEntityName()} e" .
      ' WHERE e.run = ?1 and e.sequence = ?2';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $run);
    $query->setParameter(2, $sequence);

    $result = $query->getSingleScalarResult();
    return $result ? true : false;
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function lastLink($run) {
    assert('isset($run) && is_object($run)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->getEntityName(), 'e')
      ->where('e.run = :run')
      ->orderBy('e.sequence', 'DESC')
      ->setParameter(':run', $run)
      ->setMaxResults(1)
      ->getQuery();

    // Return the Last Link (if it exists)
    return $query->getOneOrNullResult();
  }

  /**
   * 
   * @param type $run
   * @param type $test
   * @param type $sequence
   * @param type $comment
   * @return \TestCenter\ModelBundle\Entity\RunLink
   * @throws \Exception
   */
  public function createLink($run, $test, $sequence = null, $comment = null) {
    assert('isset($run) && is_object($run)');
    assert('isset($test) && is_object($test)');
    assert('!isset($comment) || is_string($comment)');
    assert('!isset($sequence) || (is_integer($sequence) && ($sequence > 0))');

    if (isset($sequence)) {
      $link = $this->findLink($run, $sequence);
      if (isset($link)) {
        throw new \Exception("Sequence #[$sequence] already exists in Run [{$run->getId()}]");
      }
    } else {
      // Get the Last Link if it exists
      $link = $this->lastLink($run);
      // Calculate the Next Link Sequence
      $sequence = isset($link) ? $link->getSequence() + 1 : 1;
    }

    // Create the Link
    $link = new \TestCenter\ModelBundle\Entity\RunLink();
    $link->setRun($run);
    $link->setTest($test);
    if (isset($comment)) {
      $link->setComment($comment);
    }
    $link->setSequence($sequence);

    // Persist the Link
    $this->getEntityManager()->persist($link);
    $this->getEntityManager()->flush();

    return $link;
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function listLinks($run) {
    assert('isset($run) && is_object($run)');

    return $this->findBy(array('run' => $run), array('sequence' => 'ASC'));
  }

  /**
   * 
   * @param type $run
   * @return type
   */
  public function countLinks($run) {
    assert('isset($run) && is_object($run)');

    // Build Query
    $sql = 'SELECT count(e) ' .
      " FROM {$this->getEntityName()} e  " .
      ' WHERE e.run = ?1';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $run);

    return (integer) $query->getSingleScalarResult();
  }

}