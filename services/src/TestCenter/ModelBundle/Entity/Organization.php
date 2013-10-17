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
 * TestCenter\ModelBundle\Entity\Organization
 *
 * @ORM\Table(name="t_organizations")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\OrganizationRepository")
 * 
 * @author Paulo Ferreira
 */
class Organization extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=60)
   */
  protected $name;

  /**
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  protected $description;

  /**
   * @var integer $projects
   *
   * @ORM\OneToMany(targetEntity="Project", mappedBy="organization")
   * */
  protected $projects;

  /**
   * @var integer $container
   *
   * @ORM\OneToOne(targetEntity="Container")
   * @ORM\JoinColumn(name="id_docroot", referencedColumnName="id")
   * */
  protected $container;

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
   *
   */
  public function __construct() {
    $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Add projects
   *
   * @param TestCenter\ModelBundle\Entity\Project $projects
   */
  public function addProject(\TestCenter\ModelBundle\Entity\Project $projects) {
    $this->projects[] = $projects;
  }

  /**
   * Get projects
   *
   * @return Doctrine\Common\Collections\Collection
   */
  public function getProjects() {
    return $this->projects;
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
   * @return array
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'description');
    $array = $this->addReferencePropertyIfNotNull($array, 'creator');
    $array = $this->addProperty($array, 'date_created');
    $array = $this->addReferencePropertyIfNotNull($array, 'last_modifier');
    $array = $this->addPropertyIfNotNull($array, 'date_modified');
    return $array;
  }

  /**
   * 
   * @return type
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
     * Set date_created
     *
     * @param datetime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;
    }

    /**
     * Get date_created
     *
     * @return datetime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_modified
     *
     * @param datetime $dateModified
     */
    public function setDateModified($dateModified)
    {
        $this->date_modified = $dateModified;
    }

    /**
     * Get date_modified
     *
     * @return datetime 
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set creator
     *
     * @param TestCenter\ModelBundle\Entity\User $creator
     */
    public function setCreator(\TestCenter\ModelBundle\Entity\User $creator)
    {
        $this->creator = $creator;
    }

    /**
     * Get creator
     *
     * @return TestCenter\ModelBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set last_modifier
     *
     * @param TestCenter\ModelBundle\Entity\User $lastModifier
     */
    public function setLastModifier(\TestCenter\ModelBundle\Entity\User $lastModifier)
    {
        $this->last_modifier = $lastModifier;
    }

    /**
     * Get last_modifier
     *
     * @return TestCenter\ModelBundle\Entity\User 
     */
    public function getLastModifier()
    {
        return $this->last_modifier;
    }
}