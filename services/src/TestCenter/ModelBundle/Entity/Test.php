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
 * TestCenter\ModelBundle\Entity\Test
 *
 * @ORM\Table(name="t_tests")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\TestRepository")
 * 
 * @author Paulo Ferreira
 */
class Test extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Project", inversedBy="tests")
   * @ORM\JoinColumn(name="id_project", referencedColumnName="id")
   * */
  private $project;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=60)
   */
  private $name;

  /**
   * @var string $group
   *
   * @ORM\Column(name="test_group", type="string", length=60, nullable=true)
   */
  private $group;

  /**
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

  /**
   * @var integer $state
   *
   * @ORM\Column(name="state", type="integer")
   */
  private $state;
  
  /**
   * @var integer $container
   *
   * @ORM\OneToOne(targetEntity="Container")
   * @ORM\JoinColumn(name="id_docroot", referencedColumnName="id")
   * */
  private $container;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_creator", referencedColumnName="id")
   */
  private $creator;

  /**
   * @var datetime $date_created
   *
   * @ORM\Column(name="dt_creation", type="datetime")
   */
  protected $date_created;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_modifier", referencedColumnName="id", nullable=true)
   */
  private $last_modifier;

  /**
   * @var datetime $date_modified
   *
   * @ORM\Column(name="dt_modified", type="datetime", nullable=true)
   */
  protected $date_modified;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_owner", referencedColumnName="id")
   */
  private $owner;

  public function __construct() {
    $this->date_created = new \DateTime();
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get project
   *
   * @return TestCenter\ModelBundle\Entity\Project 
   */
  public function getProject() {
    return $this->project;
  }

  /**
   * Set project
   *
   * @param TestCenter\ModelBundle\Entity\Project $project
   */
  public function setProject(\TestCenter\ModelBundle\Entity\Project $project) {
    $this->project = $project;
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
   * Set name
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get group
   *
   * @return string 
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * Set group
   *
   * @param string $group
   */
  public function setGroup($group) {
    $this->group = $group;
  }

  /**
   * Get description
   *
   * @return text 
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set description
   *
   * @param text $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * Set container
   *
   * @param TestCenter\ModelBundle\Entity\Container $container
   */
  public function setContainer(\TestCenter\ModelBundle\Entity\Container $container) {
    $this->container = $container;
  }

  /**
   * Get container
   *
   * @return TestCenter\ModelBundle\Entity\Container 
   */
  public function getContainer() {
    return $this->container;
  }

  /**
   * Get creator
   *
   * @return TestCenter\ModelBundle\Entity\User 
   */
  public function getCreator() {
    return $this->creator;
  }

  /**
   * Set creator
   *
   * @param TestCenter\ModelBundle\Entity\User $creator
   */
  public function setCreator(\TestCenter\ModelBundle\Entity\User $creator) {
    $this->creator = $creator;
  }

  /**
   * Get date_created
   *
   * @return datetime 
   */
  public function getDateCreated() {
    return $this->date_created;
  }

  /**
   * Set date_created
   *
   * @param datetime $dateCreated
   */
  public function setDateCreated($dateCreated) {
    $this->date_created = $dateCreated;
  }

  /**
   * Get last_modifier
   *
   * @return TestCenter\ModelBundle\Entity\User 
   */
  public function getLastModifier() {
    return $this->last_modifier;
  }

  /**
   * Get date_modified
   *
   * @return datetime 
   */
  public function getDateModified() {
    return $this->date_modified;
  }

  /**
   * Set last_modifier
   *
   * @param TestCenter\ModelBundle\Entity\User $lastModifier
   */
  public function setLastModifier(\TestCenter\ModelBundle\Entity\User $lastModifier) {
    $this->last_modifier = $lastModifier;
  }

  /**
   * Set date_modified
   *
   * @param datetime $dateModified
   */
  public function setDateModified($dateModified) {
    $this->date_modified = $dateModified;
  }

  /**
   * Get owner
   *
   * @return TestCenter\ModelBundle\Entity\User 
   */
  public function getOwner() {
    return $this->owner;
  }

  /**
   * Set owner
   *
   * @param TestCenter\ModelBundle\Entity\User $owner
   */
  public function setOwner(\TestCenter\ModelBundle\Entity\User $owner) {
    $this->owner = $owner;
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addReferencePropertyIfNotNull($array, 'project');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'group');
    $array = $this->addPropertyIfNotNull($array, 'description');
    $array = $this->addProperty($array, 'state');
    $array = $this->addReferencePropertyIfNotNull($array, 'creator');
    $array = $this->addProperty($array, 'date_created');
    $array = $this->addReferencePropertyIfNotNull($array, 'last_modifier');
    $array = $this->addPropertyIfNotNull($array, 'date_modified');
    $array = $this->addReferencePropertyIfNotNull($array, 'owner');

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
   * @return type
   */
  protected function entityName() {
    $i = strlen(__NAMESPACE__);
    return substr(__CLASS__, $i + 1);
  }


    /**
     * Set state
     *
     * @param integer $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }
}