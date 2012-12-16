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
 * @ORM\Table(name="t_run_links")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\RunLinkRepository")
 * 
 * @author Paulo Ferreira
 */
class RunLink {

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
   * @var integer $test
   *
   * @ORM\ManyToOne(targetEntity="Test")
   * @ORM\JoinColumn(name="id_test", referencedColumnName="id")
   */
  private $test;

  /**
   * @var integer $sequence
   *
   * @ORM\Column(name="sequence", type="integer")
   */
  private $sequence;

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

  public function __construct() {
    // Initialize Status Code to 0 (Not Run)
    $this->status = 0;
    $this->code = 0;  // Depends on Status
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = array(
      'id' => $this->id,
      'run' => $this->run->getId(),
      'test' => $this->test->getId(),
      'sequence' => $this->sequence,
      'status' => $this->status,
      'code' => $this->code
    );

    if (isset($this->comment)) { // If Comment Set - Add it
      $array['comment'] = $this->comment;
    }

    return $array;
  }

  /**
   * 
   * @return type
   */
  public function toString() {
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
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set code
     *
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set run
     *
     * @param TestCenter\ModelBundle\Entity\Run $run
     */
    public function setRun(\TestCenter\ModelBundle\Entity\Run $run)
    {
        $this->run = $run;
    }

    /**
     * Get run
     *
     * @return TestCenter\ModelBundle\Entity\Run 
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * Set test
     *
     * @param TestCenter\ModelBundle\Entity\Test $test
     */
    public function setTest(\TestCenter\ModelBundle\Entity\Test $test)
    {
        $this->test = $test;
    }

    /**
     * Get test
     *
     * @return TestCenter\ModelBundle\Entity\Test 
     */
    public function getTest()
    {
        return $this->test;
    }
}