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
use TestCenter\ModelBundle\Entity\UserProject;

/**
 * Description of UserProjectRepository
 *
 * @author Paulo Ferreira
 */
class UserProjectRepository
  extends EntityRepository {

  /**
   * @param $user
   * @return null
   */
  public function removeUser($user) {
    $query = $this->getEntityManager()->createQuery('DELETE FROM TestCenterModelBundle:UserProject up' .
      ' WHERE up.user = ?1');
    $query->setParameter(1, $user);
    $links = $query->getResult();
    return $links;
  }

  /**
   * @param $project
   * @return null
   */
  public function removeProject($project) {
    $query = $this->getEntityManager()->createQuery('DELETE FROM TestCenterModelBundle:UserProject up' .
      ' WHERE up.project = ?1');
    $query->setParameter(1, $project);
    $links = $query->getResult();
    return $links;
  }

  /**
   * @param $user
   * @param $project
   * @return object
   */
  public function findLink($user, $project) {
    return $this->findOneBy(array('user' => $user, 'project' => $project));
  }

  /**
   * @param $user
   * @param $project
   * @param $permissions
   * @return object|UserProject
   */
  public function addLink($user, $project, $permissions) {
    $link = $this->findLink($user, $project);

    // See if a Link Exists (If so -just update the permissions)
    if (!isset($link)) {
      $link = new UserProject();
      $link->setUser($user);
      $link->setProject($project);

      $this->getEntityManager()->persist($link);
    }

    // Update Link Permissions
    $link->setPermissions($permissions);

    // Persist and Flush the Changes
    // TODO Consider wether we should flush the changes or leave it for the controller to do so
    $this->getEntityManager()->flush();
    return $link;
  }

  /**
   * @param $user
   * @param $org
   * @return object
   */
  public function removeLink($user, $org) {
    $link = $this->findLink($user, $org);
    if (isset($link)) {
      $this->getEntityManager()->remove($link);
      $this->getEntityManager()->flush();
    }

    return $link;
  }

  /**
   * @param $project
   * @return array
   */
  public function listUsers($project) {
    $query = $this->getEntityManager()->createQuery('SELECT up, u' .
      ' FROM TestCenterModelBundle:UserProject up ' .
      ' JOIN up.user u' .
      ' WHERE up.project = ?1');
    $query->setParameter(1, $project);
    $links = $query->getResult();
    return $links;
  }

  /**
   * @param $project
   * @return int
   */
  public function countUsers($project) {
    $query = $this->getEntityManager()->createQuery('SELECT count(up) ' .
      ' FROM TestCenterModelBundle:UserProject up ' .
      ' WHERE up.project = ?1');
    $query->setParameter(1, $project);
    $result = $query->getScalarResult();
    return (integer) $result[0][1];
  }

  /**
   * @param $user
   * @return array
   */
  public function listProjects($user) {
    $query = $this->getEntityManager()->createQuery('SELECT up, o' .
      ' FROM TestCenterModelBundle:UserProject up' .
      ' JOIN up.project o' .
      ' WHERE up.user = ?1');
    $query->setParameter(1, $user);
    $links = $query->getResult();
    return $links;
  }

  /**
   * @param $user
   * @return int
   */
  public function countProjects($user) {
    $query = $this->getEntityManager()->createQuery('SELECT count(up)' .
      ' FROM TestCenterModelBundle:UserProject up' .
      ' WHERE up.user = ?1');
    $query->setParameter(1, $user);
    $result = $query->getScalarResult();
    return (integer) $result[0][1];
  }

}