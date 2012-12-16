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
use TestCenter\ModelBundle\Entity\Container;
use TestCenter\ModelBundle\Entity\ContainerEntry;

/**
 * Description of ContainerRepository
 *
 * @author Paulo Ferreira
 */
class ContainerRepository
  extends EntityRepository {

  protected function __repositoryContainerEntry() {
    return $this->getEntityManager()->getRepository('TestCenter\ModelBundle\Entity\ContainerEntry');
  }

  /**
   * 
   * @param type $owner
   * @param type $name
   * @return \TestCenter\ModelBundle\Entity\Containery
   */
  public function createContainer($name, $owner, $type = null) {
    assert('isset($name) && is_string($name)');
    assert('isset($owner) && (is_integer($owner) || is_object($owner))');
    assert('!isset($type) || is_integer($type)');

    // Create Container Entry for Child
    $container = new Container();

    // TODO better type checking (owner typeshould descendant of Doctrine Entity)
    $owner_id = $owner;
    $owner_type = $type;
    if (is_object($owner) && !isset($type)) {
      $owner_id = $owner->getId();

      //Get Type from Object Class
      $types = TypeCache::getInstance();
      $owner_type = $types->typeID($owner);
    }
    assert('isset($owner_type) && is_integer($owner_type)');

    // Set the Values for the Container
    $container->setName($name);
    $container->setOwner($owner_id);
    $container->setOwnerType($owner_type);

    // Mark the Container for Persistance
    $this->getEntityManager()->persist($container);

    return $container;
  }

  /**
   * 
   * @param type $parent
   * @param type $name
   */
  public function createChildContainer($parent, $name) {
    // Create Container Entry for Child
    $container = $this->createContainer($name, $parent->getOwner(),
                                        $parent->getOwnertype());

    // Set the Values for the Container
    $container->setParent($parent);
    $container->setSingleLevel(0);

    // Persist the Container Object (Need the ID)
    $this->getEntityManager()->flush();

    // Create Container Entry for Child
    $entry = $this->createContainerEntry($parent, $container, $name);

    // Flush Changes
    $this->getEntityManager()->flush();

    return $container;
  }

  /**
   * 
   * @param type $parent
   * @param type $child
   * @param type $name
   * @param type $type
   * @return \TestCenter\ModelBundle\Entity\ContainerEntry
   */
  public function createContainerEntry($parent, $child, $name, $type = null) {
    assert('isset($parent)');
    assert('isset($child)');
    assert('isset($name) && is_string($name)');
    assert('!isset($type) || is_integer($type)');

    // Create Container Entry for Child
    $entry = new ContainerEntry();
    $entry->setContainer($parent);
    $entry->setName($name);
    $entry->setLink($child->getId());

    if (isset($type)) {
      $entry->setLinkType($type);
    } else {
      $entry->setLinkType(TypeCache::getInstance()->typeID($child));
    }

    // Mark the Container for Persisting
    $this->getEntityManager()->persist($entry);

    return $entry;
  }

  /**
   * 
   * @param type $container
   * @param type $nameid
   * @return type
   */
  public function findChildContainer($container, $nameid) {
    return $this->findOneEntry($container, $nameid,
                               TypeCache::getInstance()->typeID('TestCenter\ModelBundle\Entity\Container'));
  }

  /**
   * 
   * @param type $container
   * @param type $nameid
   * @param type $type
   * @return type
   */
  public function findOneEntry($container, $nameid, $type = null) {
    assert('isset($container) && is_object($container)');
    assert('isset($nameid) && (is_integer($nameid) || is_string($nameid))');
    assert('!isset($type) || is_integer($type)');

    // Create Filter Condition
    $_filter = array('container' => $container);
    if (is_int($nameid)) {
      $_filter['id'] = $nameid;
    } else {
      $_filter['name'] = $nameid;
    }
    if (isset($type)) {
      $_filter['link_type'] = $type;
    }


    // Find Results
    $repository = $this->__repositoryContainerEntry();
    return $repository->findOneBy($_filter, array('id' => 'ASC'));
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function listEntries($container, $type = null) {
    assert('isset($container) && is_object($container)');
    assert('!isset($type) || is_integer($type)');

    // Create Filter Condition
    $_filter = array('container' => $container);
    if (isset($type)) {
      $_filter['link_type'] = $type;
    }

    // Find Results
    $repository = $this->__repositoryContainerEntry();
    return $repository->findBy($_filter, array('id' => 'ASC'));
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function countEntries($container, $type = null) {
    assert('isset($container) && is_object($container)');
    assert('!isset($type) || is_integer($type)');

    // Build Query
    $sql = 'SELECT count(ce) ' .
      ' FROM TestCenterModelBundle:ContainerEntry ce ' .
      ' WHERE ce.container = ?1';
    if (isset($type)) {
      $sql.=' and ce.link_type = ?2';
    }

    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $container);
    if (isset($type)) {
      $query->setParameter(2, $type);
    }

    $count = $query->getScalarResult();
    return (integer) $count[0][1];
  }

  /**
   * 
   * @param type $id
   * @param type $type
   * @return boolean
   */
  public function removeOwnedBy($id, $type) {
    assert('isset($id) && is_integer($id)');
    assert('isset($type) && is_integer($type)');

    /* Note: We Don't have to do a recursive descent of the containers 
     * (parent <-> child relationship) because, all containers, belonging to a
     * specific owner, have the same owner/owner_type field values
     */
    // Get List of Owned Containers
    $containers = $this->findBy(array('owner' => $id, 'owner_type' => $type));
    foreach ($containers as $container) {
      // Remove All Links in the Container
      $this->removeAllContainerLinks($container);
      // Remove the Container
      $this->getEntityManager()->remove($container);
    }

    return false;
  }

  /**
   * 
   * @param type $container
   * @return type
   */
  public function removeAllContainerLinks($container) {
    assert('isset($container) && is_object($container)');

    // If Passed in a Container Object, all we need is the id
    if (is_object($container)) {
      $container = $container->getId();
    }

    // Build Query
    $sql = 'DELETE FROM TestCenterModelBundle:ContainerEntry ce ' .
      ' WHERE ce.container = ?1';
    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $container);

    $result = $query->getScalarResult();
    return (integer) $result[0][1];
  }

  /**
   * 
   * @param type $id
   * @param type $type
   * @return type
   */
  public function removeLinksTo($id, $type) {
    assert('isset($id) && is_integer($id)');
    assert('isset($type) && is_integer($type)');

    // Build Query
    $sql = 'DELETE FROM TestCenterModelBundle:ContainerEntry ce ' .
      ' WHERE ce.link = ?1' .
      ' and ce.link_type = ?2';
    $query = $this->getEntityManager()->createQuery($sql);

    // Set Query Parameters
    $query->setParameter(1, $id);
    $query->setParameter(2, $type);

    // Returns Number of Records Deleted
    return $query->getScalarResult();
  }

}