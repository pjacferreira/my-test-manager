<?php

/* Test Center - Compliance Testing Application (Web Services)
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

use \common\utility\Strings;

/**
 * User Project Entity (Links a User with an Project and Sets the
 * permissions for that link).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class UserProject extends \api\model\AbstractEntity {

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
  public $project;

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
    // A Single User can Only Have a Single Set of Permissions with an Project
    $this->belongsTo("user", "models\User", "id");
    $this->belongsTo("project", "models\Project", "id");
  }

  /**
   * Define alternate table name for user project
   *
   * @return string User Project Table Name
   */
  public function getSource() {
    return "t_user_projects";
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
      'id_project' => 'project',
      'permissions' => 'permissions',
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->id = (integer) $this->id;
    $this->user = (integer) $this->user;
    $this->project = (integer) $this->project;
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
    return "userproject";
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
    $array = $this->addReferencePropertyIfNotNull($array, 'project', null, $header);
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
   * Find the Relation between the User and Project
   *
   * @param mixed $user User ID or User Entity
   * @param mixed $project Project ID or Project Entity
   * @return mixed Returns Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function findRelation($user, $project) {
    // Are we able to extract the User ID from the Parameter?
    $user_id = User::extractUserID($user);
    if (!isset($user_id)) { // NO
      throw new \Exception("User Parameter is invalid.", 1);
    }

    // Are we able to extract the Project ID from the Parameter?
    $project_id = Project::extractProjectID($project);
    if (!isset($project_id)) { // NO
      throw new \Exception("Project Parameter is invalid.", 2);
    }

    $link = self::findFirst(array(
        'conditions' => array(
          array(
            'user = :user_id: AND project = :project_id:',
            array('user_id' => $user_id, 'project_id' => $project_id),
          )))
    );
    return $link !== FALSE ? $link : null;
  }

  /**
   * Create/Update the Relation between the User and Organization
   *
   * @param mixed $user User ID or User Entity
   * @param mixed $project Project ID or Project Entity
   * @param string $permissions OPTIONAL Permission for Relation (if not SPECIFIED
   *   default to READ-ONLY)
   * @return \UserProject Returns Relation
   * @throws \Exception On Any Failure
   */
  public static function addRelation($user, $project, $permissions = null) {
    // Cleanup Permissions
    $permissions = Strings::nullOnEmpty($permissions);
    // Are Permissions Set?
    if (!isset($permissions)) { // NO: Default to Read-Only
      $permissions = 'r';
    }

    // See if the Link Exists Already
    $link = self::findRelation($user, $project);

    // Does the Link Exist Already?
    if (!isset($link)) { // NO
      $link = new UserProject();
      $link->user = User::extractUserID($user);
      $link->project = Project::extractProjectID($project);
      $link->permissions = $permissions;
    } else { // YES
      $link->permissions = $permissions;
    }

    // Were we able to flush the changes?
    if ($link->save() === FALSE) {
      // No
      throw new \Exception("Failed to Create/Update User<-->Project Link.", 1);
    }

    // TODO Consider wether we should flush the changes or leave it for the controller to do so
    return $link;
  }

  /**
   * Delete the Relation between the User and Project
   *
   * @param mixed $user User ID or User Entity
   * @param mixed $project Project ID or Project Entity
   * @return mixed Returns Deleted Relation or 'null' if none found
   * @throws \Exception On Any Failure
   */
  public static function deleteRelation($user, $project) {
    // See if the Link Exists Already
    $link = self::findRelation($user, $project);

    // Does the Link Exist Already?
    if (isset($link)) { // YES: Delete It
      // Were we able to delete the link?
      if ($link->delete() === FALSE) { // NO
        throw new \Exception("Failed to Delete User<-->Project Link.", 1);
      }
    }

    return $link;
  }

  /**
   * Delete All Project Relations for the Specified User
   *
   * @param mixed $user User ID or User Entity
   * @throws \Exception On Any Failure
   */
  public static function deleteRelationsUser($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM UserProject WHERE user = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $id)) === FALSE) {
      throw new \Exception("Failed Deleting User<-->Project Relations for User[{$id}].", 1);
    }
  }

  /**
   * Delete All User Relations for the Specified Project
   *
   * @param mixed $project Project ID or Project Entity
   * @throws \Exception On Any Failure
   */
  public static function deleteRelationsProject($project) {
    // Are we able to extract the Project ID from the Parameter?
    $id = Project::extractProjectID($project);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $query = new Phalcon\Mvc\Model\Query('DELETE FROM UserProject WHERE project = :id:', \Phalcon\Di::getDefault());

    // Execute the query returning a result if any
    if ($query->execute(array('id' => $id)) === FALSE) {
      throw new \Exception("Failed Deleting User<-->Project Relations for Project[{$id}].", 1);
    }
  }

  /**
   * List the Users Related to the Specified Project
   *
   * @param mixed $project Project ID or Project Entity
   * @return \User[] Related Users
   * @throws \Exception On Any Failure
   */
  public static function listUsers($project) {
    // Are we able to extract the Project ID from the Parameter?
    $id = Project::extractProjectID($project);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT u.*' .
      ' FROM models\User u' .
      ' JOIN models\UserProject up' .
      ' WHERE up.project = :id:';
    return self::selectQuery($pqhl, array('id' => $id));
  }

  /**
   * Count the Number of Users Related to the Specified Project
   *
   * @param mixed $project Project ID or Project Entity
   * @return integer Number of Related Users
   * @throws \Exception On Any Failure
   */
  public static function countUsers($project) {
    // Are we able to extract the Project ID from the Parameter?
    $id = Project::extractProjectID($project);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) AS count' .
      ' FROM models\UserProject up' .
      ' JOIN models\User u' .
      ' WHERE up.project = :id:';
    return self::countQuery($pqhl, array('id' => $id));
  }

  /**
   * List the Project Related to the Specified User
   *
   * @param mixed $user User ID or User Entity
   * @param mixed $organization [DEFAULT null = In All Organizations] Organization ID or Organization Entity
   * @return \Project[] Related Users
   * @throws \Exception On Any Failure
   */
  public static function listProjects($user, $organization = null) {
    // Are we able to extract the User ID from the Parameter?
    $id = User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Do we have an Organization to Filter for?
    $org_id = null;
    if (isset($organization)) { // YES
      $org_id = Organization::extractOrganizationID($organization);
      // Is the Organization ID Valid?
      if (!isset($org_id)) { // NO
        throw new \Exception("Parameter is invalid.", 2);
      }
    }

    // Instantiate the Query
    $pqhl = 'SELECT p.*' .
      ' FROM models\Project p' .
      ' JOIN models\UserProject up' .
      ' WHERE up.user = :user:';
    $parameters = array('user' => $id);
    if (isset($org_id)) {
      $pqhl.='AND p.organization = :org:';
      $parameters['org'] = $org_id;
    }
    return self::selectQuery($pqhl, $parameters);
  }

  /**
   * List the Project and Permissions Related to the Specified User
   *
   * @param mixed $user User ID or User Entity
   * @return \Project[] Related Users
   * @throws \Exception On Any Failure
   */
  public static function listProjectPermissions($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT up.*, p.*' .
      ' FROM models\UserProject up' .
      ' JOIN models\Project p' .
      ' WHERE up.user = :id:';
    return self::selectQuery($pqhl, array('id' => $id));
  }

  /**
   * Count the Number of Projects Related to the Specified User
   *
   * @param mixed $user User ID or User Entity
   * @return integer Number of Related Users
   * @throws \Exception On Any Failure
   */
  public static function countProjects($user) {
    // Are we able to extract the User ID from the Parameter?
    $id = User::extractUserID($user);
    if (!isset($id)) { // NO
      throw new \Exception("Parameter is invalid.", 1);
    }

    // Instantiate the Query
    $pqhl = 'SELECT COUNT(*) AS count' .
      ' FROM models\Project p' .
      ' JOIN models\UserProject up' .
      ' WHERE up.user = :id:';
    return self::countQuery($pqhl, array('id' => $id));
  }

}
