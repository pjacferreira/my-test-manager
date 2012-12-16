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
 * TestCenter\ModelBundle\Entity\TestStep
 *
 * @ORM\Table(name="t_test_steps")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\TestStepRepository")
 * 
 * @author Paulo Ferreira
 */
class TestStep {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

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
  // TODO Add Index that makes sure that $sequence is unique within test

  /**
   * @var string $title
   *
   * @ORM\Column(name="title", type="string", length=80)
   */
  private $title;

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
      'test' => $this->test->getId(),
      'sequence' => $this->sequence,
      'title' => $this->title
    );

    if (isset($this->description)) { // If Description Set - Add it
      $array['description'] = $this->description;
    }

    return $array;
  }

  /**
   * 
   * @return type
   */
  public function toString() {
    return $this->id;
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
   * Get sequence
   *
   * @return integer 
   */
  public function getSequence() {
    return $this->sequence;
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
   * Get test
   *
   * @return TestCenter\ModelBundle\Entity\Test 
   */
  public function getTest() {
    return $this->test;
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
   * Set title
   *
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * Get title
   *
   * @return string 
   */
  public function getTitle() {
    return $this->title;
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

}