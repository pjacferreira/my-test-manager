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
 * TestCenter\ModelBundle\Entity\RunLink
 *
 * @ORM\Table(name="t_run_playlists")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\RunLinkRepository")
 * 
 * @author Paulo Ferreira
 */
class PlayList extends AbstractEntity {

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
   * @var integer $sequence
   *
   * @ORM\Column(name="step", type="integer")
   */
  private $step;

  /**
   * @var integer $status
   *
   * @ORM\Column(name="status", type="integer")
   */
  private $status;

  /**
   * @var integer $code
   *
   * @ORM\Column(name="code", type="integer")
   */
  private $code;

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
   * Get status
   *
   * @return integer 
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Set status
   *
   * @param integer $status
   */
  public function setStatus($status) {
    $this->status = $status;
  }

  /**
   * Get code
   *
   * @return integer 
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * Set code
   *
   * @param integer $code
   */
  public function setCode($code) {
    $this->code = $code;
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
    $array = $this->addReferencePropertyIfNotNull($array, 'testset');
    $array = $this->addProperty($array, 'sequence');
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


    /**
     * Set step
     *
     * @param integer $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * Get step
     *
     * @return integer 
     */
    public function getStep()
    {
        return $this->step;
    }
}