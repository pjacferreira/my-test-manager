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
 * TestCenter\ModelBundle\Entity\PlayEntry
 *
 * @ORM\Table(name="t_run_playentries")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\PlayEntryRepository")
 * 
 * @author Paulo Ferreira
 */
class PlayEntry extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer $run
   *
   * @ORM\ManyToOne(targetEntity="Run")
   * @ORM\JoinColumn(name="id_run", referencedColumnName="id")
   */
  private $run;

  /**
   * @var integer $sequence
   *
   * @ORM\Column(name="sequence", type="integer")
   */
  private $sequence;

  /**
   * @var integer $test
   *
   * @ORM\ManyToOne(targetEntity="Test")
   * @ORM\JoinColumn(name="id_test", referencedColumnName="id")
   */
  private $test;

  /**
   * @var integer $step
   *
   * @ORM\ManyToOne(targetEntity="TestStep")
   * @ORM\JoinColumn(name="id_step", referencedColumnName="id")
   */
  private $step;

  /**
   * @var integer $state
   *
   * @ORM\Column(name="state", type="integer")
   */
  private $state;

  /**
   * @var integer $state_code
   *
   * @ORM\Column(name="state_code", type="integer")
   */
  private $state_code;

  /**
   * @var text $comment
   *
   * @ORM\Column(name="comment", type="text", nullable=true)
   */
  private $comment;

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

  public function __construct() {
    // Initialize Status Code to 0 (Not Run)
    $this->status = 0;
    $this->code = 0;  // Depends on Status
    $this->date_modified = new \DateTime();
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
   * Get run
   *
   * @return TestCenter\ModelBundle\Entity\Run 
   */
  public function getRun() {
    return $this->run;
  }

  /**
   * Set run
   *
   * @param TestCenter\ModelBundle\Entity\Run $run
   */
  public function setRun(\TestCenter\ModelBundle\Entity\Run $run) {
    $this->run = $run;
  }

  /**
   * Get sequence
   *
   * @return integer 
   */
  public function getSequence() {
    return $this->sequence;
  }

  /**
   * Set sequence
   *
   * @param integer $sequence
   */
  public function setSequence($sequence) {
    $this->sequence = $sequence;
  }

  /**
   * Get test
   *
   * @return TestCenter\ModelBundle\Entity\Test 
   */
  public function getTest() {
    return $this->test;
  }

  /**
   * Set test
   *
   * @param TestCenter\ModelBundle\Entity\Test $test
   */
  public function setTest(\TestCenter\ModelBundle\Entity\Test $test) {
    $this->test = $test;
  }

  /**
   * Get step
   *
   * @return TestCenter\ModelBundle\Entity\TestStep 
   */
  public function getStep() {
    return $this->step;
  }

  /**
   * Set step
   *
   * @param integer $step
   */
  public function setStep(\TestCenter\ModelBundle\Entity\TestStep $step) {
    $this->step = $step;
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
   * Get code
   *
   * @return integer 
   */
  public function getStateCode() {
    return $this->state_code;
  }

  /**
   * Set code
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
    $array = $this->addReferencePropertyIfNotNull($array, 'run');
    $array = $this->addProperty($array, 'sequence');
    $array = $this->addReferencePropertyIfNotNull($array, 'test');
    $array = $this->addReferencePropertyIfNotNull($array, 'step');
    $array = $this->addProperty($array, 'state');
    $array = $this->addProperty($array, 'state_code');
    $array = $this->addPropertyIfNotNull($array, 'comment');
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