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
 * TestCenter\ModelBundle\Entity\Run
 *
 * @ORM\Table(name="t_runs")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\RunRepository")
 * 
 * @author Paulo Ferreira
 */
class Run extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Project")
   * @ORM\JoinColumn(name="id_project", referencedColumnName="id")
   * */
  private $project;

  /**
   * @ORM\ManyToOne(targetEntity="Set")
   * @ORM\JoinColumn(name="id_set", referencedColumnName="id")
   * */
  private $set;

  /**
   * @var string $group
   *
   * @ORM\Column(name="run_group", type="string", length=60, nullable=true)
   */
  private $group;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=60)
   */
  private $name;

  /**
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

  /**
   * @var boolean $open
   *
   * @ORM\Column(name="open", type="boolean")
   */
  private $is_open;

  /**
   * @var integer $state
   *
   * @ORM\Column(name="state", type="integer")
   */
  private $state;

  /**
   * @var integer $code
   *
   * @ORM\Column(name="state_code", type="integer")
   */
  private $code;

  /**
   * @var text $comment
   *
   * @ORM\Column(name="comment", type="text", nullable=true)
   */
  private $comment;
  
  /**
   * @ORM\ManyToOne(targetEntity="PlayEntry")
   * @ORM\JoinColumn(name="id_playlist_pos", referencedColumnName="id")
   * */
  private $playlist_position;

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

  /**
   *
   */
  public function __construct() {
    $this->is_open = true;
    $this->state = 0;
    $this->state_code = 0;
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
   * Get testset
   *
   * @return TestCenter\ModelBundle\Entity\Set 
   */
  public function getSet() {
    return $this->set;
  }

  /**
   * Set testset
   *
   * @param TestCenter\ModelBundle\Entity\Set $set
   */
  public function setTestset(\TestCenter\ModelBundle\Entity\Set $set) {
    $this->set = $set;
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
   * Set description
   *
   * @param text $description
   */
  public function setDescription($description) {
    $this->description = $description;
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
   * Get is_open
   *
   * @return boolean 
   */
  public function getIsOpen() {
    return $this->is_open;
  }

  /**
   * Set is_open
   *
   * @param boolean $isOpen
   */
  public function setIsOpen($isOpen) {
    $this->is_open = $isOpen;
  }

  /**
   * Get state
   *
   * @return integer 
   */
  public function getState() {
    return $this->state;
  }

  /**
   * Set state
   *
   * @param integer $state
   */
  public function setState($state) {
    $this->state = $state;
  }

  /**
   * Get State Code
   *
   * @return integer 
   */
  public function getStateCode() {
    return $this->state_code;
  }

  /**
   * Set Statee Code
   *
   * @param integer $code
   */
  public function setStateCode($code) {
    $this->state_code = $code;
  }

  /**
   * Get comment
   *
   * @return text 
   */
  public function getComment() {
    return $this->comment;
  }

  /**
   * Set comment
   *
   * @param text $comment
   */
  public function setComment($comment) {
    $this->comment = $comment;
  }
  
  /**
   * Get Current Play List Position
   * @return TestCenter\ModelBundle\Entity\PlayList 
   */
  public function getPlaylistPosition() {
    return $this->playlist_position;
  }

  /**
   * Set Current Play List Position
   *
   * @param TestCenter\ModelBundle\Entity\PlayList $position
   */
  public function setPlaylistPosition(\TestCenter\ModelBundle\Entity\User $position) {
    $this->playlist_position = $position;
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
   * Set last_modifier
   *
   * @param TestCenter\ModelBundle\Entity\User $lastModifier
   */
  public function setLastModifier(\TestCenter\ModelBundle\Entity\User $lastModifier) {
    $this->last_modifier = $lastModifier;
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
    $array = $this->addReferencePropertyIfNotNull($array, 'set');
    $array = $this->addPropertyIfNotNull($array, 'group');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'description');
    $array = $this->addProperty($array, 'is_open');
    $array = $this->addProperty($array, 'state');
    $array = $this->addProperty($array, 'state_code');
    $array = $this->addReferencePropertyIfNotNull($array, 'playlist_position');
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
     * Set set
     *
     * @param TestCenter\ModelBundle\Entity\Set $set
     */
    public function setSet(\TestCenter\ModelBundle\Entity\Set $set)
    {
        $this->set = $set;
    }
}