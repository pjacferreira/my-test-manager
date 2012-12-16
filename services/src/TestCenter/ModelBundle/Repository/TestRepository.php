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
 * Description of TestRepository
 *
 * @author Paulo Ferreira
 */
class TestRepository
  extends EntityRepository {

  /**
   * 
   * @param type $project
   * @param type $nameid
   * @return type
   */
  public function findOneTest($project, $nameid) {

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
   * @param type $container
   * @return type
   */
  public function listTests($project) {
    assert('isset($project) && is_object($project)');

    return $this->findBy(array('project' => $project));
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function countTests($project) {
    assert('isset($project) && is_object($project)');

    // Build Query
    $sql = 'SELECT count(t) ' .
      ' FROM TestCenterModelBundle:Test t ' .
      ' WHERE t.project = ?1';

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $project);

    // Extract the number of items
    return $query->getScalarResult();
  }

  /**
   * 
   * @param type $test
   * @return boolean
   */
  public function removeRelations($test) {
    assert('isset($test) && is_object($test)');

    $typemap = TypeCache::getInstance();
    $typeid = $typemap->typeID($this->getEntityName());

    // Remove Links from Containers
    $repository = $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\Container');

    // Remove Links to Test
    $repository->removeLinksTo($test->getId(), $typeid);

    // Remove Containers Owned by Test    
    $repository->removeOwnedBy($test->getId(), $typeid);
    
    // Remove Steps Associated with Test
    $repository = $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\TestStep');
    
    // Remove All Steps Associated with the Test
    $repository->removeAllStepsFrom($test);

    return true;
  }

  /**
   * @return mixed
   */
  public function getClassMetadata($entity = null) {
    if (isset($entity)) {
      $class = $this->getEntityManager()->getClassMetadata($entity);
      assert('isset($class)');
      return $class;
    }

    return parent::getClassMetadata();
  }

}