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

use shared\utility\StringUtilities;

/**
 * User Organization Entity (Links a User with an Organization and Sets the 
 * permissions for that link).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class UserOrganization extends api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $id;

  /**
   *
   * @var integer
   */
  public $user;

  /**
   *
   * @var integer
   */
  public $organization;

  /**
   *
   * @var string
   */
  public $permissions;

  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * PHALCON per request Contructor
   */
  public function initialize() {
    // Define Relations
    // A Single User can Only Have a Single Set of Permissions with an Organization
    $this->belongsTo("user", "User", "id");
    $this->belongsTo("organization", "Organization", "id");
  }

  /**
   * Define alternate table name for user organization
   * 
   * @return string User Organization Table Name
   */
  public function getSource() {
    return "t_user_orgs";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
        'id' => 'id',
        'id_user' => 'user',
        'id_organization' => 'organization',
        'permissions' => 'permissions'
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
    $this->user = (integer) $this->user;
    $this->organization = (integer) $this->organization;
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
    return "userorganization";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'user', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'organization', null, $header);
    $array = $this->addProperty($array, 'permissions', null, $header);
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
   * Find the Relation between the User and Organization
   * 
   * @param mixed $user User ID or User Entity
   * @param mixed $org Organization ID or Organization Entity
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findRelation($user, $org) {
    // Are we able to extract the User ID from the Parameter?
    $user_id = \User::extractUserID($user);
    if (!isset($user_id)) { // NO
      throw new \Exception("User Parameter is invalid.", 1);
    }

    // Are we able to extract the Organization ID from the Parameter?
    $org_id = \Organization::extractOrganizationID($org);
    if (!isset($org_id)) { // NO
      throw new \Exception("Organization Parameter is invalid.", 2);
    }

    $link = self::findFirst(array(
                'conditions' => array(
                    array(
                        'user = :user_id: AND organization = :org_id:',
                        array('user_id' => $user_id, 'org_id' => $org_id)
                    )))
    );
    return $link !== FALSE ? $link : null;
  }

  /**
   * Create/Update the Relation between the User and Organization
   * 
   * @param mixed $user User ID or User Entity
   * @param mixed $org Organization ID or Organization Entity
   * @param string $permissions OPTIONAL Permission for Relation (if not SPECIFIED
   *   default to READ-ONLY)
   * @return \UserOrganization Returns Relation 
   * @throws \Exception On Any Failure
   */
  public static function addRelation($user, $org, $permissions = null) {
    // Cleanup Permissions
    $permissions = StringUtilities::nullOnEmpty($permissions);
    // Are Permissions Set?
    if (!isset($permissions)) { // NO: Default to Read-Only
      $permissions = 'r';
    }

    // See if the Link Exists Already
    $link = self::findRelation($user, $org);

    // Does the Link Exist Already?
    if (!isset($link)) { // NO
      $link = new UserOrganization();
      $link->user = \User::extractUserID($user);
      $link->organization = \Organization::extractOrganizationID($org);
      $link->permissions = $permissions;
    } else { // YES
      $link->permissions = $permissions;
    }

    // Were we able to flush the changes?
    if ($link->save() === FALSE) { // No
      throw new \Exception("Failed to Create/Update User<-->Organization Link.", 1);
    }

    // TODO Consider wether we should flush the changes or leave it for the controller to do so
    return $link;
  }

  /**
   * Delete the Relation between the User and Organization
   * 
   * @param mixed $user User ID or User Entity
   * @param mixed $org Organization ID or Organization Entity
   * @return mixed Returns Deleted Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function deleteRelation($user, $org) {
    // See if the Link Exists Already
    $link = self::findRelation($user, $org);

    // Does the Link Exist Already?
    if (isset($link)) { // YES: Delete It
      // Were we able to delete the link?
      if ($link->delete() === FALSE) { // NO
        throw new \Exception("Failed to Delete User<-->Organization Link.", 1);
      }
    }

    return $link;
  }

  /**
   * Delete All Organization Relations for the Specified User
   * 
   * @param mixed $user User ID or User Entity
   * @throws \Exception On Any Failure
   */
  public static function deleteRelationsUser($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = \User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM UserOrganization WHERE user = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $id)) === FALSE) {
      throw new \Exception("Failed Deleting User<-->Organization Relations for User[{$id}].", 1);
    }
  }

  /**
   * Delete All User Relations for the Specified Organization
   * 
   * @param mixed $org Organization ID or Organization Entity
   * @throws \Exception On Any Failure
   */
  public static function deleteRelationsOrganization($org) {
    // Are we able to extract the Organization ID from the Parameter?
    $id = \Organization::extractOrganizationID($org);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM UserOrganization WHERE organization = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $id)) === FALSE) {
      throw new \Exception("Failed Deleting User<-->Organization Relations for Organization[{$id}].", 1);
    }
  }

  /**
   * List the Users Related to the Specified Organization
   * 
   * @param mixed $org Organization ID or Organization Entity
   * @return \User[] Related Users
   * @throws \Exception On Any Failure
   */
  public static function listUsers($org) {
    // Are we able to extract the Organization ID from the Parameter?
    $id = \Organization::extractOrganizationID($org);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    /* NOTE: The choice of the Entity Used with FROM is important, as it
     * represents the type of entity that will be created, on rehydration.
     */
    $pqhl = 'SELECT User.*' .
            ' FROM User' .
            ' JOIN UserOrganization' .
            ' WHERE UserOrganization.organization = :id:';
    return self::selectQuery($pqhl, array('id' => $id));
  }

  /**
   * Count the Number of Users Related to the Specified Organization
   * 
   * @param mixed $org Organization ID or Organization Entity
   * @return integer Number of Related Users
   * @throws \Exception On Any Failure
   */
  public static function countUsers($org) {
    // Are we able to extract the Organization ID from the Parameter?
    $id = \Organization::extractOrganizationID($org);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) AS count' .
            ' FROM UserOrganization' .
            ' JOIN User' .
            ' WHERE UserOrganization.organization = :id:';
    return self::countQuery($pqhl, array('id' => $id));
  }

  /**
   * List the Organizations Related to the Specified User
   * 
   * @param mixed $user User ID or User Entity
   * @return \Organization[] Related Users
   * @throws \Exception On Any Failure
   */
  public static function listOrganizations($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = \User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT Organization.*' .
            ' FROM Organization' .
            ' JOIN UserOrganization' .
            ' WHERE UserOrganization.user = :id:';
    return self::selectQuery($pqhl, array('id' => $id));
  }

  /**
   * List the Organizations and Permissions Related to the Specified User
   * 
   * @param mixed $user User ID or User Entity
   * @return \Organization[] Related Users
   * @throws \Exception On Any Failure
   */
  public static function listOrganizationPermissions($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = \User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT UserOrganization.*, Organization.*' .
            ' FROM UserOrganization' .
            ' JOIN Organization' .
            ' WHERE UserOrganization.user = :id:';
    return self::selectQuery($pqhl, array('id' => $id));
  }

  /**
   * Count the Number of Organizations Related to the Specified User
   * 
   * @param mixed $user User ID or User Entity
   * @return integer Number of Related Users
   * @throws \Exception On Any Failure
   */
  public static function countOrganizations($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = \User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) AS count' .
            ' FROM UserOrganization' .
            ' JOIN Organization' .
            ' WHERE UserOrganization.user = :id:';
    return self::countQuery($pqhl, array('id' => $id));
  }

}
