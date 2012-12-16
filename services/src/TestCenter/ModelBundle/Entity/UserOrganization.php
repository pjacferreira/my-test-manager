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
namespace TestCenter\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TestCenter\ModelBundle\Entity\UserOrganization
 *
 * @ORM\Table(name="t_user_orgs")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\UserOrganizationRepository")
 * 
 * @author Paulo Ferreira
 */
class UserOrganization {
  /**
   * @var integer $user
   *
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
   */
  private $user;

  /**
   * @var integer $organization
   *
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Organization")
   * @ORM\JoinColumn(name="id_organization", referencedColumnName="id")
   */
  private $organization;

  /**
   * @var string $password
   *
   * @ORM\Column(name="permissions", type="string", length=40)
   */
  private $permissions;

  /**
   * @return array
   */
  public function toArray() {
    return array('user' => $this->user->getId(), 'organization' => $this->organization->getId(), 'permissions' => $this->permissions);
  }

  /**
   * Set permissions
   *
   * @param string $permissions
   */
  public function setPermissions($permissions) {
    $this->permissions = $permissions;
  }

  /**
   * Get permissions
   *
   * @return string
   */
  public function getPermissions() {
    return $this->permissions;
  }

  /**
   * Set user
   *
   * @param TestCenter\ModelBundle\Entity\User $user
   */
  public function setUser(\TestCenter\ModelBundle\Entity\User $user) {
    $this->user = $user;
  }

  /**
   * Get user
   *
   * @return TestCenter\ModelBundle\Entity\User
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Set organization
   *
   * @param TestCenter\ModelBundle\Entity\Organization $organization
   */
  public function setOrganization(\TestCenter\ModelBundle\Entity\Organization $organization) {
    $this->organization = $organization;
  }

  /**
   * Get organization
   *
   * @return TestCenter\ModelBundle\Entity\Organization
   */
  public function getOrganization() {
    return $this->organization;
  }
}