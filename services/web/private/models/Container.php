<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2015 Paulo Ferreira <pf at sourcenotes.org>
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

namespace models;

use common\utility\Strings;

/**
 * Container Entity/Entry
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Container extends \api\model\AbstractEntity {

  // Add Complex PHQL Handler
  use \api\model\MixinComplexQueries;

  /**
   *
   * @var integer Container ID
   */
  public $id;

  /**
   *
   * @var integer ID of Root Container
   */
  public $root;

  /**
   *
   * @var string Type of Container Entry
   */
  public $type;

  /**
   *
   * @var string Container Entry's Name
   */
  public $name;

  /**
   *
   * @var integer If Sub-Container, ID of the Parent Container
   */
  public $parent;

  /**
   *
   * @var integer If Container Entry, ID of the Linked Object
   */
  public $link;

  /**
   *
   * @var string Type of the Object that Owns this Container or Entry
   */
  public $type_owner;

  /**
   *
   * @var integer ID of the Owning Object
   */
  public $owner;

  /**
   *
   * @var integer 0 - Allow Child Containers (false), 1 - No Child containers (true)
   */
  public $single_level;

  /**
   * @var integer Identifier of the User that Created the Entity
   */
  public $creator;

  /**
   * @var string Timestamp of Entity Creation
   */
  public $date_created;

  /**
   * @var integer Identifier of Last User to Modify the Entity
   */
  public $modifier;

  /**
   * @var string Timestamp of Last Modification
   */
  public $date_modified;

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
    $this->single_level = 0;
  }

  /**
   * PHALCON per request Contructor
   */
  public function initialize() {
    // Define Relations
    // A Single Container can be the Root for Many Other Containers or Entries
    $this->hasMany("root", "models\Container", "id");
    // A Single Container can be the Parent for Many Child Containers or Entries
    $this->hasMany("parent", "models\Container", "id");
    // A Single User can be the Creator for Many Containers
    $this->hasMany("creator", "models\User", "id");
    // A Single User can be the Modifier for Many Container
    $this->hasMany("modifier", "models\User", "id");
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
      'id_root' => 'root',
      'type' => 'type',
      'name' => 'name',
      'id_parent' => 'parent',
      'id_link' => 'link',
      'type_owner' => 'type_owner',
      'id_owner' => 'owner',
      'singlelevel' => 'single_level',
      'id_creator' => 'creator',
      'dt_creation' => 'date_created',
      'id_modifier' => 'modifier',
      'dt_modified' => 'date_modified'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  protected function afterFetch() {
    $this->id = (integer) $this->id;
    $this->root = isset($this->root) ? (integer) $this->root : null;
    $this->parent = isset($this->parent) ? (integer) $this->parent : null;
    $this->link = isset($this->link) ? (integer) $this->link : null;
    $this->owner = isset($this->owner) ? (integer) $this->owner : null;
    $this->creator = (integer) $this->creator;
    $this->modifier = isset($this->modifier) ? (integer) $this->modifier : null;
    $this->single_level = (integer) $this->single_level;
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
    $array = $this->addReferencePropertyIfNotNull($array, 'root', null, $header);
    $array = $this->addProperty($array, 'type', null, $header);
    $array = $this->addProperty($array, 'name', null, $header);
    $array = $this->setDisplayField($array, 'name', $header);
    $array = $this->addProperty($array, 'parent', null, $header);
    $array = $this->addProperty($array, 'link', null, $header);
    $array = $this->addProperty($array, 'type_owner', null, $header);
    $array = $this->addProperty($array, 'owner', null, $header);
    $array = $this->addProperty($array, 'single_level', !!$this->single_level, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'creator', null, $header);
    $array = $this->addProperty($array, 'date_created', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'modifier', null, $header);
    $array = $this->addPropertyIfNotNull($array, 'date_modified', null, $header);
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
   * Public Helper Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Try to Extract a Container ID from the incoming parameter
   * 
   * @param mixed $organization The Potential Container (object) or Container ID (integer)
   * @return mixed Returns the Container ID or 'null' on failure;
   */
  public static function extractID($container) {
    // Is the parameter an Container Object?
    if (is_object($container) && is_a($container, __CLASS__)) { // YES
      return $container->id;
    } else if (is_integer($container) && ($container >= 0)) { // NO: It's a Positive Integer
      return $container;
    }
    // ELSE: None of the above
    return null;
  }

  /*
   * ---------------------------------------------------------------------------
   * Relation Management Functions
   * ---------------------------------------------------------------------------
   */

  public function setOwner($id, $type) {
    $type = Strings::nullOnEmpty($type);
    if (!is_numeric($id) || !isset($type)) {
      throw new \Exception("Owner Parameters are Invalid.", 1);
    }

    $this->owner = (int) $id;
    $type = strtoupper($type);
    switch ($type[0]) {
      case 'O' : // Organization
      case 'P' : // Project
      case 'T' : // Test
      case 'S' : // Test Set
      case 'R' : // Run
        $this->owner_type = $type[0];
        break;
      default:
        throw new \Exception("Invalid Owner Type.", 2);
    }
  }

  /**
   * Does the Name Exist in the Container
   * 
   * @param \models\Container $container Parent Container
   * @param string $name Entry Name
   * @param string $type [DEFAULT null] Entry Type
   * @return boolean 'true' The name exists, 'false' otherwise
   */
  public static function existsName($container, $name, $type = null) {
    $id = self::extractID($container);
    $name = Strings::nullOnEmpty($name);
    $type = Strings::nullOnEmpty($type);

    $params = [
      'conditions' => 'parent = :id: and name = :name:',
      'bind' => [
        'id' => $id,
        'name' => $name
      ]
    ];

    if (isset($type)) {
      $params['conditions'].=' and type = :type:';
      $params['bind']['type'] = $type[0];
    }

    $count = self::count($params);
    return $count;
  }

  /**
   * Find a Child Entry, in the Container, by Name and Optionally Type.
   * 
   * @param \models\Container $container Parent Container
   * @param string $name Entry Name
   * @param string $type [DEFAULT null] Entry Type
   * @return \models\Container Container Entry or FALSE if none found
   */
  public static function findChildByName($container, $name, $type = null) {
    $id = self::extractID($container);
    $name = Strings::nullOnEmpty($name);
    $type = Strings::nullOnEmpty($type);

    $params = [
      'conditions' => 'parent = :id: and name = :name:',
      'bind' => [
        'id' => $id,
        'name' => $name
      ]
    ];

    if (isset($type)) {
      $params['conditions'].=' and type = :type:';
      $params['bind']['type'] = $type[0];
    }

    return self::findFirst($params);
  }

  /**
   * Basic Creator for a Root Container (Information Missing - owner id)
   * 
   * @param string $name Name of Container
   * @param string $owner_type Type of Owning Object
   * @param boolean $single_level [DEFAULT false] Allow Child Container 
   * @return \models\Container newly create Container
   */
  public static function newRootContainer($name, $owner_type, $single_level = false) {
    $name = Strings::nullOnEmpty($name);
    $owner_type = Strings::nullOnEmpty($owner_type);

    // Create Container
    $container = new Container;
    $container->type = 'F';
    $container->name = $name;
    $container->type_owner = $owner_type;
    $container->single_level = !!$single_level ? 1 : 0;

    return $container;
  }

  /**
   * Basic Creator for Child Container (No More Information Required)
   * 
   * @param \models\Container $parent
   * @param string $name Name of Container
   * @param boolean $single_level [DEFAULT false] Allow Child Container 
   * @return \models\Container newly create Container
   */
  public static function newChildContainer(Container $parent, $name, $single_level = false) {
    $name = Strings::nullOnEmpty($name);

    // Create Container
    $container = new Container;

    $container->root = isset($parent->root) ? $parent->root : $parent->id;
    $container->type = 'F';
    $container->name = $name;
    $container->parent = $parent->id;
    $container->type_owner = $parent->type_owner;
    $container->owner = $parent->owner;
    $container->single_level = !!$single_level ? 1 : 0;

    return $container;
  }

  /**
   * Basic Creator for Child Entry (Information Missing - link id)
   * 
   * @param \models\Container $parent Parent Container
   * @param integer $id Entry ID to Link
   * @param string $name Name of Container Entry
   * @param string $type Type of Container Entry
   * @return \models\Container newly created Container Entry
   */
  public static function newContainerEntry(Container $parent, $id, $name, $type) {
    $name = Strings::nullOnEmpty($name);
    $type = Strings::nullOnEmpty($type);

    // Create Container
    $container = new Container;

    $container->root = isset($parent->root) ? $parent->root : $parent->id;
    $container->type = $type[0];
    $container->name = $name;
    $container->parent = $parent->id;
    $container->link = (integer) $id;
    $container->owner = $parent->owner;
    $container->type_owner = $parent->type_owner;

    return $container;
  }

  /**
   * Delete the Folder
   * 
   * @param \models\Container $folder Folder to Delete
   * @return boolean 'true' on folder deleted
   * @throws \Exception On failure to delete 
   */
  public static function deleteFolder(Container $folder) {
    // Were we able to delete the Entity?
    if ($folder->delete() == false) { // NO
      $exception = '';
      foreach ($entity->getMessages() as $message) {
        $exception+= $message->getMessage() . "\n";
      }

      throw new \Exception($exception, 1);
    }

    return true;
  }

  /**
   * Delete the Folder
   * 
   * @param \models\Container $folder Folder to Delete
   * @param array $filter [DEFAULT null = All Items] Filter Condition
   * @param array $order [DEFAULT null = No Sort Order] Sort Order for Entries
   * @return \models\Container[] Entries Found
   * @throws \Exception On failure to delete 
   */
  public static function listEntries(Container $folder, $filter = null, $order = null) {
    assert('isset($folder)');

    // Create Query
    $phql = 'SELECT *' .
      ' FROM models\Container' .
      ' WHERE parent = :id:';

    // Apply Filter and Order By (if any)
    $phql = self::applyOrder($order, self::applyFilter($filter, $phql));

    // Execute Query and Return Results
    $entries = self::selectQuery($phql, ['id' => $folder->id]);

    // Return Result Set
    return $entries === FALSE ? [] : $entries;
  }

  /**
   * Count the Number of Child Entries
   * 
   * @param \models\Container $folder Folder to Delete
   * @param array $filter [DEFAULT null = All Items] Filter Options
   * @return integer Entries Counted
   * @throws \Exception On failure to delete 
   */
  public static function countEntries(Container $folder, $filter = null) {
    assert('isset($folder)');

    // Create Query
    $phql = 'SELECT COUNT(*) as count' .
      ' FROM models\Container' .
      ' WHERE parent = :id:';

    // Apply Filter and Order By (if any)
    $phql = self::applyFilter($filter, $phql);

    // Execute Query and Return Results
    $count = self::countQuery($phql, ['id' => $folder->id]);

    // Return Result Set
    return $count;
  }

}
