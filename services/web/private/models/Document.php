<?php

/* Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
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

/**
 * Document Entity (All Properties Related to a Singl Document).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Document extends api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var integer
   */
  public $owner;

  /**
   *
   * @var integer
   */
  public $owner_type;

  /**
   *
   * @var string
   */
  public $name;

  /**
   *
   * @var string
   */
  public $path;

  /**
   *
   * @var string
   */
  public $application_type;

  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * Define alternate table name for documents
   * 
   * @return string Documents Table Name
   */
  public function getSource() {
    return "t_documents";
  }

  /**
   * Independent Column Mapping.
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'id_owner' => 'owner',
        'ownertype' => 'owner_type',
        'name' => 'name',
        'path' => 'path',
        'apptype' => 'application_type'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
  }
  
  /*
   * ---------------------------------------------------------------------------
   * AbstractEntity: Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the name used to reference the entity in Metadata
   * 
   * @return string Name
   */
  public function entityName() {
    return "document";
  }

  /*
   * ---------------------------------------------------------------------------
   * PHP Standard Conversions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves a Map representation of the Entities Field Values
   * 
   * @return array Map of field <--> value tuplets
   */
  public function toArray() {
    $array = parent::toArray();

    $array = $this->addKeyProperty($array, 'id');
    $array = $this->addProperty($array, 'owner');
    $array = $this->addProperty($array, 'owner_type');
    $array = $this->addProperty($array, 'name');
    $array = $this->addPropertyIfNotNull($array, 'path');
    $array = $this->addPropertyIfNotNull($array, 'application_type');
    return $array;
  }

  /**
   * String Representation of Entity
   * 
   * @return string Entity Identifier String
   */
  public function __toString() {
    return (string) $this->id;
  }

}
