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
 * TestCenter\ModelBundle\Entity\Document
 *
 * @ORM\Table(name="t_documents")
 * @ORM\Entity
 * 
 * @author Paulo Ferreira
 */
class Document {
  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var integer $owner
   *
   * @ORM\Column(name="id_owner", type="integer")
   * */
  private $owner;

  /**
   * @var integer $owner_type
   *
   * @ORM\Column(name="ownertype", type="integer")
   * */
  private $owner_type;

  /**
   * @var name $name
   *
   * @ORM\Column(name="name", type="string", length=40)
   */
  private $name;

  /**
   * @var string $path
   *
   * @ORM\Column(name="path", type="string", length=255)
   */
  private $path;

  /**
   * @var string $application_type
   *
   * @ORM\Column(name="apptype", type="string", length=255)
   */
  private $application_type;

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
     * Set owner
     *
     * @param integer $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return integer 
     */
    public function getOwner()
    {
        return $this->owner;
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
     * Set path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set application_type
     *
     * @param string $applicationType
     */
    public function setApplicationType($applicationType)
    {
        $this->application_type = $applicationType;
    }

    /**
     * Get application_type
     *
     * @return string 
     */
    public function getApplicationType()
    {
        return $this->application_type;
    }

    /**
     * Set owner_type
     *
     * @param integer $ownerType
     */
    public function setOwnerType($ownerType)
    {
        $this->owner_type = $ownerType;
    }

    /**
     * Get owner_type
     *
     * @return integer 
     */
    public function getOwnerType()
    {
        return $this->owner_type;
    }
}