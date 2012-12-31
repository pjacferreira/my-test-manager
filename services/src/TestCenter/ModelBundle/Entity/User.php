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
 * TestCenter\ModelBundle\Entity\User
 *
 * @ORM\Table(name="t_users")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\UserRepository")
 * 
 * @author Paulo Ferreira
 */
class User {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=40)
   */
  private $name;

  /**
   * @var string $first_name
   *
   * @ORM\Column(name="first_name", type="string", length=40, nullable=true)
   */
  private $first_name;

  /**
   * @var string $last_name
   *
   * @ORM\Column(name="last_name", type="string", length=80, nullable=true)
   */
  private $last_name;

  /**
   * @var string $password
   *
   * @ORM\Column(name="password", type="string", length=64)
   */
  private $password;

  /**
   * @var text $short
   *
   * @ORM\Column(name="s_description", type="string", length=80, nullable=true)
   */
  private $s_description;

  /**
   * @var text $long
   *
   * @ORM\Column(name="l_description", type="text", nullable=true)
   */
  private $l_description;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set password
   *
   * @param string $password
   */
  public function setPassword($password) {
    $this->password = $password;
  }

  /**
   * Get password
   *
   * @return string
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * Set first_name
   *
   * @param string $firstName
   */
  public function setFirstName($firstName) {
    $this->first_name = $firstName;
  }

  /**
   * Get first_name
   *
   * @return string 
   */
  public function getFirstName() {
    return $this->first_name;
  }

  /**
   * Set last_name
   *
   * @param string $lastName
   */
  public function setLastName($lastName) {
    $this->last_name = $lastName;
  }

  /**
   * Get last_name
   *
   * @return string 
   */
  public function getLastName() {
    return $this->last_name;
  }

  /**
   * Set s_description
   *
   * @param string $sDescription
   */
  public function setSDescription($sDescription) {
    $this->s_description = $sDescription;
  }

  /**
   * Get s_description
   *
   * @return string 
   */
  public function getSDescription() {
    return $this->s_description;
  }

  /**
   * Set l_description
   *
   * @param text $lDescription
   */
  public function setLDescription($lDescription) {
    $this->l_description = $lDescription;
  }

  /**
   * Get l_description
   *
   * @return text 
   */
  public function getLDescription() {
    return $this->l_description;
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = array(
      '__entity' => $this->entityName(),
    );

    $array = $this->addPropertyIfNotNull($array, 'id');
    $array = $this->addPropertyIfNotNull($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'first_name');
    $array = $this->addPropertyIfNotNull($array, 'last_name');
    $array = $this->addPropertyIfNotNull($array, 's_description');
    $array = $this->addPropertyIfNotNull($array, 'l_description');
    return $array;
  }

  /**
   * @return string
   */
  public function __toString() {
    return strval($this->id);
  }

  /**
   * 
   * @param type $array
   * @param type $prop_name
   * @return type
   */
  protected function addPropertyIfNotNull($array, $prop_name) {
    // Get the Entity Name
    $entity = strtolower($this->entityName());

    if (isset($this->$prop_name)) { // If Propery Set - Add it
      $array["{$entity}:{$prop_name}"] = $this->$prop_name;
    }
    return $array;
  }
  
  /**
   * 
   * @return type
   */
  protected function entityName() {
    $i = strlen(__NAMESPACE__);
    return substr(__CLASS__, $i + 1);
  }  
}