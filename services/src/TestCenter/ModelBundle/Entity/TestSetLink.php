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
 * TestCenter\ModelBundle\Entity\TestSetLink
 *
 * @ORM\Table(name="t_testset_links")
 * @ORM\Entity(repositoryClass="TestCenter\ModelBundle\Repository\TestSetLinkRepository")
 * 
 * @author Paulo Ferreira
 */
class TestSetLink {

  /**
   * @var integer $testset
   *
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="TestSet")
   * @ORM\JoinColumn(name="id_testset", referencedColumnName="id")
   */
  private $testset;

  /**
   * @var integer $test
   *
   * @ORM\Id
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
   * @return array
   */
  public function toArray() {
    return array(
      '$testset' => $this->testset->getId(),
      '$test' => $this->test->getId(),
      'sequence' => $this->sequence,
    );
  }

  /**
   * 
   * @return type
   */
  public function toString() {
    return $this->testset->getId() . ':' . $this->test->getId();
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