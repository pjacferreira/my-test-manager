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
use TestCenter\ModelBundle\Entity\UserOrganization;

/**
 * Description of UserOrganizationRepository
 *
 * @author Paulo Ferreira
 */
class UserOrganizationRepository
  extends EntityRepository {

  public function removeUser($user) {
    $query = $this->getEntityManager()->createQuery('DELETE FROM TestCenterModelBundle:UserOrganization uo' .
      ' WHERE uo.user = ?1');
    $query->setParameter(1, $user);
    $links = $query->getResult();
    return $links;
  }

  public function removeOrganization($org) {
    $query = $this->getEntityManager()->createQuery('DELETE FROM TestCenterModelBundle:UserOrganization uo' .
      ' WHERE uo.organization = ?1');
    $query->setParameter(1, $org);
    $links = $query->getResult();
    return $links;
  }

  public function findLink($user, $org) {
    return $this->findOneBy(array('user' => $user, 'organization' => $org));
  }

  public function addLink($user, $org, $permissions) {
    $link = $this->findLink($user, $org);

    // See if a Link Exists (If so -just update the permissions)
    if (!isset($link)) {
      $link = new UserOrganization();
      $link->setUser($user);
      $link->setOrganization($org);

      $this->getEntityManager()->persist($link);
    }

    // Update Link Permissions
    $link->setPermissions($permissions);

    // Persist and Flush the Changes
    // TODO Consider wether we should flush the changes or leave it for the controller to do so
    $this->getEntityManager()->flush();
    return $link;
  }

  public function removeLink($user, $org) {
    $link = $this->findLink($user, $org);
    if (isset($link)) {
      $this->getEntityManager()->remove($link);
      $this->getEntityManager()->flush();
    }

    return $link;
  }

  public function listUsers($org) {
    $query = $this->getEntityManager()->createQuery('SELECT uo, u' .
      ' FROM TestCenterModelBundle:UserOrganization uo' .
      ' JOIN uo.user u' .
      ' WHERE uo.organization = ?1');
    $query->setParameter(1, $org);
    $links = $query->getResult();
    return $links;
  }

  public function countUsers($org) {
    $query = $this->getEntityManager()->createQuery('SELECT count(uo) ' .
      ' FROM TestCenterModelBundle:UserOrganization uo ' .
      ' WHERE uo.organization = ?1');
    $query->setParameter(1, $org);
    $result = $query->getScalarResult();
    return (integer) $result[0][1];
  }

  public function listOrganizations($user) {
    $query = $this->getEntityManager()->createQuery('SELECT uo, o' .
      ' FROM TestCenterModelBundle:UserOrganization uo' .
      ' JOIN uo.organization o' .
      ' WHERE uo.user = ?1');
    $query->setParameter(1, $user);
    $links = $query->getResult();
    return $links;
  }

  public function countOrganizations($user) {
    $query = $this->getEntityManager()->createQuery('SELECT count(uo)' .
      ' FROM TestCenterModelBundle:UserOrganization uo' .
      ' WHERE uo.user = ?1');
    $query->setParameter(1, $user);
    $result = $query->getScalarResult();
    return (integer) $result[0][1];
  }

}