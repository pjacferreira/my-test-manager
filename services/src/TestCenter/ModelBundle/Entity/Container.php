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
 * TestCenter\ModelBundle\Entity\Container
 *
 * @ORM\Table(name="t_containers")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\ContainerRepository")
 * 
 * @author Paulo Ferreira
 */
class Container {

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
   * @ORM\OneToMany(targetEntity="Container", mappedBy="parent")
   */
  private $children;

  /**
   * @ORM\ManyToOne(targetEntity="Container", inversedBy="children")
   * @ORM\JoinColumn(name="id_parent", referencedColumnName="id")
   */
  private $parent;

  /**
   * @var integer $owner
   *
   * @ORM\Column(name="id_owner", type="integer")
   * */
  private $owner;

  /**
   * @var integer $owner_type
   *
   * @ORM\Column(name="ownertype", type="integer")
   * */
  private $owner_type;

  /**
   * @var integer $single_level
   *
   * @ORM\Column(name="singlelevel", type="boolean")
   * */
  private $single_level = 1;

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
   * Set owner
   *
   * @param integer $owner
   */
  public function setOwner($owner) {
    $this->owner = $owner;
  }

  /**
   * Get owner
   *
   * @return integer
   */
  public function getOwner() {
    return $this->owner;
  }

  /**
   * Set parent
   *
   * @param TestCenter\ModelBundle\Entity\Container $parent
   */
  public function setParent(\TestCenter\ModelBundle\Entity\Container $parent) {
    $this->parent = $parent;
  }

  /**
   * Get parent
   *
   * @return TestCenter\ModelBundle\Entity\Container
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * Set owner_type
   *
   * @param integer $ownerType
   */
  public function setOwnerType($ownerType) {
    $this->owner_type = $ownerType;
  }

  /**
   * Get owner_type
   *
   * @return integer
   */
  public function getOwnerType() {
    return $this->owner_type;
  }

  /**
   * Set single_level
   *
   * @param boolean $singleLevel
   */
  public function setSingleLevel($singleLevel) {
    $this->single_level = $singleLevel;
  }

  /**
   * Get single_level
   *
   * @return boolean
   */
  public function getSingleLevel() {
    return $this->single_level;
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = array(
      'id' => $this->id,
      'name' => $this->name,
      'owner' => $this->owner,
      'ownertype' => $this->owner_type,
      'singlelevel' => $this->single_level ? true : false
    );

    if (isset($this->parent)) { // If Parent Set - Add it
      $array['parent'] = $this->parent->getId();
    }
    return $array;
  }

  /**
   * @return string
   */
  public function __toString() {
    return strval($this->id);
  }

  public function __construct() {
    $this->children = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Add children
   *
   * @param TestCenter\ModelBundle\Entity\Container $children
   */
  public function addContainer(\TestCenter\ModelBundle\Entity\Container $children) {
    $this->children[] = $children;
  }

  /**
   * Get children
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getChildren() {
    return $this->children;
  }

}