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
 * Container Entity (Definition of a Document Container).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Container extends api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var string
   */
  public $name;

  /**
   *
   * @var integer
   */
  public $parent;

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
   * @var integer
   */
  public $single_level;

  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * PHALCON per instance Contructor
   */
  public function onConstruct() {
    // By Default Single Level
    $this->single_level = true;
  }

  /**
   * Define alternate table name for containers
   * 
   * @return string Containers Table Name
   */
  public function getSource() {
    return "t_containers";
  }

  /**
   * Independent Column Mapping.
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'name' => 'name',
        'id_parent' => 'parent',
        'id_owner' => 'owner',
        'ownertype' => 'owner_type',
        'singlelevel' => 'single_level'
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
    return "container";
  }

  /*
   * ---------------------------------------------------------------------------
   * PHP Standard Conversions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves a Map representation of the Entities Field Values
   * 
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Map of field <--> value tuplets
   */
  public function toArray($header = true) {
    $array = parent::toArray($header);

    $array = $this->addKeyProperty($array, 'id', $header);
    $array = $this->addProperty($array, 'name', null, $header);
    $array = $this->addProperty($array, 'parent', null, $header);
    $array = $this->addProperty($array, 'owner', null, $header);
    $array = $this->addProperty($array, 'owner_type', null, $header);
    $array = $this->addProperty($array, 'single_level', $this->single_level ? true : false, $header);
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

  /*
   * ---------------------------------------------------------------------------
   * Relation Management Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Add a Container to the Database
   * 
   * @param string $name Container Name
   * @param integer $owner_id ID of Container's Owner
   * @param integer $owner_type Type of Container's Owner
   * @param boolean $single_level [OPTIONAL: DEFAULT = true] Is Single Level Container?
   * @return \Container Returns Newly Created Container 
   * @throws \Exception On Any Failure
   */
  public static function addContainer($name, $owner_id, $owner_type, $single_level = true) {
    $container = new \Container();
    $container->name = $name;
    $container->owner = $owner_id;
    $container->owner_type = $owner_type;
    $container->single_level = $single_level;

/*    
    if ($container->save() === FALSE) {
      throw new \Exception("Failed to Create Container [{$name}].", 1);
    }
*/
    
    return $container;
  }

}
