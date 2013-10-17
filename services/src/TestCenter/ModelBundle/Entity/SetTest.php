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
 * TestCenter\ModelBundle\Entity\SetTest
 *
 * @ORM\Table(name="t_set_tests")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\SetTestRepository")
 * 
 * @author Paulo Ferreira
 */
class SetTest extends AbstractEntity {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer $set
   *
   * @ORM\ManyToOne(targetEntity="Set")
   * @ORM\JoinColumn(name="id_set", referencedColumnName="id")
   */
  private $set;

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
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get testset
   *
   * @return TestCenter\ModelBundle\Entity\TestSet 
   */
  public function getSet() {
    return $this->set;
  }

  /**
   * Set testset
   *
   * @param TestCenter\ModelBundle\Entity\TestSet $set
   */
  public function setSet(\TestCenter\ModelBundle\Entity\Set $set) {
    $this->set = $set;
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
   * @return array
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addProperty($array, 'id');
    $array = $this->addReferencePropertyIfNotNull($array, 'set');
    $array = $this->addProperty($array, 'sequence');
    $array = $this->addReferencePropertyIfNotNull($array, 'test');

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