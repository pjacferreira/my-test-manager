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
 * TestCenter\ModelBundle\Entity\TestSet
 *
 * @ORM\Table(name="t_testsets")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\TestSetRepository")
 * 
 * @author Paulo Ferreira
 */
class TestSet {

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
   * @return array
   */
  public function toArray() {
    $array = array(
      'id' => $this->id,
      'name' => $this->name,
      'project' => $this->project->getID()
    );

    if (isset($this->description)) { // If Description Set - Add it
      $array['description'] = $this->description;
    }
    return $array;
  }

  /**
   * @return string
   */
  public function __toString() {
    return strval($this->id);
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
   * Set project
   *
   * @param TestCenter\ModelBundle\Entity\Project $project
   */
  public function setProject(\TestCenter\ModelBundle\Entity\Project $project) {
    $this->project = $project;
  }

  /**
   * Get project
   *
   * @return TestCenter\ModelBundle\Entity\Project 
   */
  public function getProject() {
    return $this->project;
  }

}