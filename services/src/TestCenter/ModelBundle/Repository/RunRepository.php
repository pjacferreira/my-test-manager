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
use TestCenter\ModelBundle\API\TypeCache;

/**
 * Description of RunRepository
 *
 * @author Paulo Ferreira
 */
class RunRepository
  extends EntityRepository {

  /**
   * 
   * @param type $entity
   * @return type
   */
  protected function __repository($entity) {
    return $this->getEntityManager()->getRepository($entity);
  }

  /**
   * 
   * @return type
   */
  protected function __repositoryLinks() {
    return $this->__repository('TestCenter\ModelBundle\Entity\RunLink');
  }

  /**
   * 
   * @return type
   */
  protected function __repositoryContainers() {
    return $this->__repository('TestCenter\ModelBundle\Entity\Container');
  }

  /**
   * 
   * @param type $project
   * @param type $nameid
   * @return type
   */
  public function findRun($project, $nameid) {

    $criteria = array('project' => $project);
    if (is_int($nameid)) {
      $criteria['id'] = $nameid;
    } else {
      $criteria['name'] = $nameid;
    }

    return parent::findOneBy($criteria);
  }

  /**
   * 
   * @param type $test
   * @return boolean
   */
  public function removeRelations($run) {
    assert('isset($run) && is_object($run)');

    // Remove Container Entries
    $typemap = TypeCache::getInstance();
    $typeid = $typemap->typeID($this->getEntityName());

    // Remove Links from Containers
    $link_count = $this->__repositoryContainers()->removeLinksTo($run->getId(),
                                                                 $typeid);

    // Remove TestSet Links
    $link_count = $this->__repositoryLinks()->removeAllLinksTo($run);

    return true;
  }

  public function cloneSetLinks($run, $set) {
    assert('isset($run) && is_object($run)');
    assert('isset($set) && is_object($set)');

    // Get the Set of Links in the Test Set
    $test_links = $this->__repository('TestCenter\ModelBundle\Entity\TestSetLink')->listLinks($set);

    // Run Link Repository
    $rl_repo = $this->__repository('TestCenter\ModelBundle\Entity\RunLink');

    // Enumerate All the Tests in the Set
    $run_links = array();
    foreach ($test_links as $set_link) {

      // Enumerate all the Steps in the Test
      $test = $set_link->getTest();

      // Clone Test Set Link Information
      $link = $rl_repo->createLink($run, $test, $set_link->getSequence());

      // Save the Link
      $run_links[] = $link;
    }

    // Save the Changes Back to the Database
    $this->getEntityManager()->flush();
    return count($run_links);
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function listRuns($project) {
    assert('isset($project) && is_object($project)');

    // Create Filter Condition
    $_filter = array('project' => $project);

    return $this->findBy($_filter,
                         array('seq_step' => 'ASC', 'seq_step' => 'ASC'));
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function countRuns($project) {
    assert('isset($project) && is_object($project)');

    // Build Query
    $sql = 'SELECT count(e) ' .
      " FROM {$this->getEntityName()} e" .
      ' WHERE ce.project = ?1';
    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $container);

    $count = $query->getScalarResult();
    return (integer) $count[0][1];
  }

}