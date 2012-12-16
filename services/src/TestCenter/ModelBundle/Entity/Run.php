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
class Run {

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
   * @ORM\Column(name="name", type="string", length=60)
   */
  private $name;

  /**
   * @ORM\ManyToOne(targetEntity="Project")
   * @ORM\JoinColumn(name="id_project", referencedColumnName="id")
   * */
  private $project;

  /**
   * @ORM\ManyToOne(targetEntity="TestSet")
   * @ORM\JoinColumn(name="id_testset", referencedColumnName="id")
   * */
  private $testset;

  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="id_owner", referencedColumnName="id")
   * */
  private $user;

  /**
   * @var integer $state
   *
   * @ORM\Column(name="state", type="integer")
   */
  private $state;

  /**
   * @var integer $sequence
   *
   * @ORM\Column(name="cur_sequence", type="integer")
   */
  private $sequence;

  /**
   * @var text $description
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;

  /**
   *
   */
  public function __construct() {
    $this->state = 0;
    $this->state_code = 0;
    $this->sequence = -1;
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = array(
      'id' => $this->id,
      'name' => $this->name,
      'project' => $this->project->getID(),
      'testset' => $this->testset->getID(),
      'user' => $this->user->getID(),
      'state' => $this->state,
      'sequence' => $this->sequence
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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

    /**
     * Set sequence
     *
     * @param integer $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set project
     *
     * @param TestCenter\ModelBundle\Entity\Project $project
     */
    public function setProject(\TestCenter\ModelBundle\Entity\Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get project
     *
     * @return TestCenter\ModelBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set testset
     *
     * @param TestCenter\ModelBundle\Entity\TestSet $testset
     */
    public function setTestset(\TestCenter\ModelBundle\Entity\TestSet $testset)
    {
        $this->testset = $testset;
    }

    /**
     * Get testset
     *
     * @return TestCenter\ModelBundle\Entity\TestSet 
     */
    public function getTestset()
    {
        return $this->testset;
    }

    /**
     * Set user
     *
     * @param TestCenter\ModelBundle\Entity\User $user
     */
    public function setUser(\TestCenter\ModelBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return TestCenter\ModelBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}