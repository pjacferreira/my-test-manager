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
 * TestCenter\ModelBundle\Entity\ContainerEntry
 *
 * @ORM\Table(name="t_container_entries")
 * @ORM\Entity
 * 
 * @author Paulo Ferreira
 */
class ContainerEntry {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer $container
   *
   * @ORM\ManyToOne(targetEntity="Container")
   * @ORM\JoinColumn(name="id_container", referencedColumnName="id")
   */
  private $container;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=40)
   */
  private $name;

  /**
   * @var integer $link
   *
   * @ORM\Column(name="id_link", type="integer")
   * */
  private $link;

  /**
   * @var integer $link_type
   *
   * @ORM\Column(name="linktype", type="integer")
   * */
  private $link_type;

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
   * Set owner
   *
   * @param integer $link
   */
  public function setLink($link) {
    $this->link = $link;
  }

  /**
   * Get link
   *
   * @return integer 
   */
  public function getLink() {
    return $this->link;
  }

  /**
   * Set link_type
   *
   * @param integer $linkType
   */
  public function setLinkType($linkType) {
    $this->link_type = $linkType;
  }

  /**
   * Get link_type
   *
   * @return integer 
   */
  public function getLinkType() {
    return $this->link_type;
  }

  /**
   * @return array
   */
  public function toArray() {
    $array = array(
      'id' => $this->id,
      'container' => $this->getContainer()->getId(),
      'name' => $this->name,
      'link' => $this->link,
      'linktype' => $this->link_type
      );

    return $array;
  }

  /**
   * @return string
   */
  public function __toString() {
    return strval($this->id);
  }  
    public function __construct()
    {
        $this->container = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add container
     *
     * @param TestCenter\ModelBundle\Entity\Container $container
     */
    public function addContainer(\TestCenter\ModelBundle\Entity\Container $container)
    {
        $this->container[] = $container;
    }
}