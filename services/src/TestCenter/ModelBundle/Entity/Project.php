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
 * TestCenter\ModelBundle\Entity\Project
 *
 * @ORM\Table(name="t_projects")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\ProjectRepository")
 * 
 * @author Paulo Ferreira
 */
class Project {

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
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

  /**
   * @var integer $organization
   *
   * @ORM\ManyToOne(targetEntity="Organization", inversedBy="projects")
   * @ORM\JoinColumn(name="id_organization", referencedColumnName="id")
   * */
  private $organization;

  /**
   * @var integer $container
   *
   * @ORM\OneToOne(targetEntity="Container")
   * @ORM\JoinColumn(name="id_root", referencedColumnName="id")
   * */
  private $container;

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
   * @return array
   */
  public function toArray() {
    $array = array(
      '__entity' => strtolower($this->entityName()),
    );

    $array = $this->addPropertyIfNotNull($array, 'id');
    $array = $this->addPropertyIfNotNull($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'description');
    $array =
      $this->addProperty(
      $this->addProperty($array, 'organization', $this->organization->getID()),
                         'container', $this->container->getID());

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
  protected function addProperty($array, $name, $value) {
    // Get the Entity Name
    $entity = strtolower($this->entityName());
    $array["{$name}"] = $value;
    return $array;
  }

  /**
   * 
   * @param type $array
   * @param type $prop_name
   * @return type
   */
  protected function addPropertyIfNotNull($array, $prop_name) {
    // Get the Entity Name
    // investigate get_called_class(); in order to create bas class function
    return isset($this->$prop_name) ? $this->addProperty($array, $prop_name,
                                                         $this->$prop_name) : $array;
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