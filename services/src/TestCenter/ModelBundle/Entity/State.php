<?php

/* Test Center - Compliance Testing Application
 * Copyright (C) 2013 Paulo Ferreira <pf at sourcenotes.org>
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
 * TestCenter\ModelBundle\Entity\Set
 *
 * @ORM\Table(name="t_states")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\StateRepository")
 * 
 * @author Paulo Ferreira
 */
class State extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer $type
   *
   * @ORM\Column(name="type", type="integer")
   */
  private $type;

  /**
   * @var integer $state
   * 
   * NOTE: state, must be unique, per type (i.e. a unique index is required to
   * link the type and state)
   *
   * @ORM\Column(name="state", type="integer")
   */
  private $state;

  /**
   * @var text $s_description
   *
   * @ORM\Column(name="s_description", type="string", length=80)
   */
  protected $s_description;

  /**
   * @var text $l_description
   *
   * @ORM\Column(name="l_description", type="text", nullable=true)
   */
  protected $l_description;

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
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get type
   *
   * @return integer 
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Set type
   *
   * @param integer $type
   */
  public function setType($type) {
    $this->type = $type;
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
   * Get s_description
   *
   * @return string 
   */
  public function getSDescription() {
    return $this->s_description;
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
   * Get l_description
   *
   * @return text 
   */
  public function getLDescription() {
    return $this->l_description;
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
   * @return array
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addProperty($array, 'type');
    $array = $this->addProperty($array, 'state');
    $array = $this->addProperty($array, 's_description');
    $array = $this->addPropertyIfNotNull($array, 'l_description');
    $array = $this->addReferencePropertyIfNotNull($array, 'creator');
    $array = $this->addProperty($array, 'date_created');
    $array = $this->addReferencePropertyIfNotNull($array, 'last_modifier');
    $array = $this->addPropertyIfNotNull($array, 'date_modified');

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

}