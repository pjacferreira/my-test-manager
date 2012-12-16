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

namespace TestCenter\ServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\StringUtilities;
use Library\ArrayUtilities;
use TestCenter\ServiceBundle\API\CrudServiceController;

/**
 * Description of UsersController
 *
 * @author Paulo Ferreira
 */
class UsersController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\User');
  }

  /**
   * @param $name
   * @param $password
   * @param null $fv_settings
   * @return null
   */
  public function createAction($name) {
    // Extract Clean (Security) URL Parameters
    $parameters = $this->importRequestParameters();
    
    // Set Name is Specified as a Route Parameter
    $name = StringUtilities::nullOnEmpty($name);
    if(isset($name)) {
      $parameters['name'] = $name;
    }

    // Call the Function
    return $this->doAction('create', $parameters);
  }

  /**
   * @param $id
   * @return null
   */
  public function readAction($id) {
    // Extract Clean (Security) URL Parameters
    $parameters = $this->importRequestParameters();

    // Fill in the ID 
    $parameters['id'] = (integer) $id;

    return $this->doAction('read', $parameters);
  }

  /**
   * @param $name
   * @return null
   */
  public function readByNameAction($name) {
    // Extract Clean (Security) URL Parameters
    $parameters = $this->importRequestParameters();

    // Fill in the Name 
    $parameters['name'] = StringUtilities::nullOnEmpty($name);

    return $this->doAction('read', $parameters);
  }

  /**
   * @param $id
   * @param $fields
   * @param $values
   * @return null
   */
  public function updateAction($id) {
    // Extract Clean (Security) URL Parameters
    $parameters = $this->importRequestParameters();

    // Fill in the ID 
    $parameters['id'] = (integer) $id;

    return $this->doAction('update', $parameters);
  }

  /**
   * @param $id
   * @return null
   */
  public function deleteAction($id) {
    return $this->doAction('delete', array('id' => (integer) $id));
  }

  /**
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param type $filter
   * @param type $sort
   * @param type $limit
   * @return type
   */
  public function listAction(Request $request, $filter = null, $sort = null,
                             $limit = null) {
    // Build Array
    $array = array();
    $array = $this->addIfNotNull($array, '__filter',
                                 $this->oneOf($filter, $request->get('filter')));
    $array = $this->addIfNotNull($array, '__sort',
                                 $this->oneOf($sort, $request->get('sort')));
    $array = $this->addIfNotNull($array, '__limit',
                                 $this->oneOf($limit, $request->get('limit')));

    return $this->doAction('list', count($array) ? $array : null);
  }

  /**
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param type $filter
   * @return type
   */
  public function countAction(Request $request, $filter = null) {
    // Build Array
    $array = array();
    $array = $this->addIfNotNull($array, '__filter',
                                 $this->oneOf($filter, $request->get('filter')));

    return $this->doAction('count', count($array) ? $array : null);
  }

  /**
   * @param $parameters
   * @return object
   */
  protected function doDeleteAction($parameters) {
    // What we have to do
    // Delete Links Between User and Organization (Automatically Forces the Removal of Links with Organization Projects)
    return parent::doDeleteAction($parameters);
  }

  /**
   * @param $parameters
   * @return array
   * @throws \Exception
   */
  protected function sessionChecksCreate($parameters) {
    // Basic Session Checks
    $parameters = $this->sessionChecks('create', $parameters);

    // Verify Parameters
    $name = ArrayUtilities::extract($parameters, 'name');
    if (!isset($name)) {
      throw new \Exception('Missing Required Action Parameter [name].', 1);
    }

    // Test if the user name already exists
    $user = $this->getRepository()->findOneByName($name);
    if (isset($user)) {
      throw new \Exception("User [$name] already exists.", 2);
    }

    return $parameters;
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    // If Password Specified Encode with MD5
    $password = ArrayUtilities::extract($parameters, 'password');
    if (isset($password)) {
      $parameters['password'] = md5($password);
    }

    switch ($action) {
      case 'Update':
      case 'Delete':
        // Get the Identified for the User
        $id = ArrayUtilities::extract($parameters, 'id');
        if (!isset($id)) {
          throw new \Exception('Missing Required Action Parameter [id].', 1);
        }

        // Test if the user name already exists
        $user = $this->getRepository()->find($id);
        if (!isset($user)) {
          throw new \Exception('User not found', 1);
        }

        // Save the User for the Action
        $parameters['entity'] = $user;
        $parameters['user'] = $user;
        break;
    }

    return $parameters;
  }

  /**
   * @param $action
   * @param $results
   * @param $format
   */
  protected function preRender($action, $results, $format) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($format) && is_string($format)');

    $return = $results;
    switch ($action) {
      case 'Create':
      case 'Read':
      case 'Update':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'List':
        $return = array();
        foreach ($results as $user) {
          $return[] = $user->toArray();
        }
        break;
    }

    return $return;
  }

  /**
   * 
   * @param type $value1
   * @param type $value2
   * @return type
   */
  protected function oneOf($value1, $value2) {
    return isset($value1) ? $value1 : $value2;
  }

  /**
   * 
   * @param type $source
   * @param type $merge
   * @return type
   */
  protected function cleanURLParameters($source, $merge = null) {
    // Parameter Validation
    assert('isset($source) && is_array($source)');
    assert('!isset($merge) || is_array($merge)');

    $return = isset($merge) ? $merge : array();

    // Escape 
    foreach ($source as $key => $value) {
      $return[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
    }

    return $return;
  }

  protected function importRequestParameters() {
    // Extract Clean (Security) URL Parameters
    $parameters = $this->cleanURLParameters($_GET);
    $parameters = $this->cleanURLParameters($_POST, $parameters);

    // Get Entity MetaData
    $meta = $this->getMetadata();

    // Pass through only parameters that are valid for the Entity
    $array = array();
    foreach ($parameters as $key => $value) {

      // Check if the Key is Prefixed
      if (stripos($key, ':')) { // Yes
        list($type,$field) = explode(':', $key, 2);
        $key = $field;
      }

      // Skip Identifier Fields
      if ($meta->isIdentifier($key)) {
        continue;
      }

      // Allow Non-String Values to Pass-through untouched
      if (isset($value) && is_string($value)) {
        $value = StringUtilities::nullOnEmpty($value);
      }

      if (isset($value)) {
        if ($meta->hasField($key) || $meta->hasAssociation($key)) {
          $array[$key] = $value;
        }
      }
    }

    return $array;
  }

}
