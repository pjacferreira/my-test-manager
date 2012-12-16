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
 * Description of TestSetRepository
 *
 * @author Paulo Ferreira
 */
class TestSetRepository
  extends EntityRepository {

  /**
   * 
   * @return type
   */
  protected function __repositoryLinks() {
    return $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\TestSetLink');
  }

  /**
   * 
   * @return type
   */
  protected function __repositoryContainers() {
    return $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\Container');
  }

  /**
   * 
   * @param type $project
   * @param type $nameid
   * @return type
   */
  public function findSet($project, $nameid) {

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
  public function removeRelations($set) {
    assert('isset($set) && is_object($set)');

    // Remove Container Entries
    $typemap = TypeCache::getInstance();
    $typeid = $typemap->typeID($this->getEntityName());

    // Remove Links from Containers
    $this->__repositoryContainers()->removeLinksTo($set->getId(), $typeid);

    // Remove TestSet Links
    $this->__repositoryLinks()->removeAllLinksTo($set);

    return true;
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function listTestSets($project) {
    assert('isset($project) && is_object($project)');

    // Create Filter Condition
    $_filter = array('project' => $project);

    return $this->findBy($_filter, array('id' => 'ASC'));
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function countTestSets($project) {
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