<?php

/*
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

namespace controllers\admin;

use api\controller\ActionContext;
use common\utility\Strings;
use api\controller\CrudServiceController;

/**
 * Controller used to Manage User Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class UsersController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a User (if it doesn't already exist) with the Given Name.
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   * 
   * @param string $name User name
   * @param string $hash [DEFAULT null = No Password] Password Hash
   * @return string HTTP Body Response
   */
  public function create($name, $hash = null) {
    // Create Action Context
    $context = new ActionContext('create');
    // Call Action
    return $this->doAction($context->setIfNotNull('user:name', Strings::nullOnEmpty($name))
                                    ->setIfNotNull('user:password', Strings::nullOnEmpty($hash))
            );
  }

  /**
   * Retrieve the User with the Given ID.
   * 
   * @param string $id User's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Call Action
    return $this->doAction($context->setParameter('user:id', (integer) $id));
  }

  /**
   * Retrieve the User with the Given Name.
   * 
   * @param string $name User's Unique Name
   * @return string HTTP Body Response
   */
  public function readByName($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Call Action
    return $this->doAction($context->setIfNotNull('user:name', Strings::nullOnEmpty($name)));
  }

  /**
   * Update the User, with the Given ID, information.
   * 
   * @param $id User's Unique Identifier
   * @return string HTTP Body Response
   */
  public function update($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Call Action
    return $this->doAction($context->setParameter('user:id', (integer) $id));
  }

  /**
   * Update the User, with the Given Name, information.
   * 
   * @param string $name User's Unique Name
   * @return string HTTP Body Response
   */
  public function updateByName($name) {
    // Create Action Context
    $context = new ActionContext('update');
    // Call Action
    return $this->doAction($context->setIfNotNull('user:name', Strings::nullOnEmpty($name)));
  }

  /**
   * Delete the User with the Given ID.
   * 
   * @param $id User's Unique Identifier
   * @return string HTTP Body Response
   */
  public function delete($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setParameter('user:id', (integer) $id));
  }

  /**
   * Delete the User with the Given Name.
   * 
   * @param string $name User's Unique Name
   * @return string HTTP Body Response
   */
  public function deleteByName($name) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setIfNotNull('user:name', Strings::nullOnEmpty($name)));
  }

  /**
   * List User Entities in the Database.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of users
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @return string HTTP Body Response
   */
  public function listUsers() {
    // Create Action Context
    $context = new ActionContext('list');

    return $this->doAction($context);
  }

  /**
   * Count the Number of User Entities in the Database.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of users
   * 
   * @return integer Number of Users
   */
  public function countUsers() {
    // Create Action Context
    $context = new ActionContext('count');

    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform checks that validate the Session State.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();

    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required preparation, before the Delete Action Handler is Called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preActionDelete($context) {
    // Call the General Handler 1st (to Setup Context)
    $context = $this->preAction($context);

    // Get User Being Managed
    $user = $context->getParameter('entity');

    // Unlink ALL Users from all Projects/Organizations
    \models\UserProject::deleteRelationsUser($user);
    \models\UserOrganization::deleteRelationsUser($user);

    return $context;
  }

  /**
   * Perform any required setup, before the Action Handler is Called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Process 'user:name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'user:name', function($controller, $context, $action, $value) {
      // Try to Find the User by Name
      $user = \models\User::findFirstByName($value);

      // Are we trying to 'create' a new user?
      if ($action === 'Create') { // YES
        // Did we find an existing user with the same name?
        if ($user !== FALSE) { // YES
          throw new \Exception("User [$value] already exists.", 1);
        }
      } else { // NO: Some other action
        // Did we find an existing user?
        if ($user === FALSE) { // NO
          throw new \Exception("User [$value] not found", 2);
        }

        $context->setParameter('entity', $user);
        $context->setParameter('user', $user);
      }

      return $context;
    }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'user:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'user:id', function($controller, $context, $action, $value) {

        // Does the User with the given ID exist?
        $user = \models\User::findFirst($value);
        if ($user === FALSE) { // NO
          throw new \Exception("User [$value] not found", 3);
        }

        // Save the User for the Action
        $context->setParameter('entity', $user);
        $context->setParameter('user', $user);

        return $context;
      }, null, array('Read', 'Update', 'Delete'));
    }

    // Process 'user:password' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'user:password', function($controller, $context, $action, $value) {
      // Extract Trimmed Password
      $password = Strings::nullOnEmpty($value);

      // Has a Password been Defined?
      if (!isset($password)) { // NO: Use Null Password
        $password = '';
      }

      return $context->setParameter('user:password', $password);
    }, 'Update', 'Create', function($controller, $context, $action) {
      // FOR CREATE: We set the password to EMPTY PASSWORD
      return '';
    });

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \models\User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 4);
    }

    // Save the Creator / Modification User in the Context
    return $context->setParameter('cm_user', $user);
  }

  /**
   * Perform any required setup, before we perform final rendering of the Action's
   * Result.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return mixed Action Output that is to be Rendered
   * @throws \Exception On any type of failure condition
   */
  protected function preRender($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get Results
    $results = $context->getActionResult();

    // Get the Action Name
    $action = $context->getAction();
    assert('isset($action)');
    switch ($action) {
      case 'Create':
      case 'Read':
      case 'Update':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'List':
        $return = [];
        $entities = [];
        $header = true;
        foreach ($results as $user) {
          $entities[] = $user->toArray($header);
          $header = false;
        }

        // Do we have entities to display?
        if (count($entities)) { // YES
          // Move the Entity Information to become Result Header
          $this->moveEntityHeader($entities[0], $return);
          $return['__type'] = 'entity-set';
          $return['entities'] = $entities;
        }
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /*
   * ---------------------------------------------------------------------------
   * OVERRIDE : EntityServiceController
   * ---------------------------------------------------------------------------
   */

  /**
   * Apply any required modifications to the incoming Field value.
   * 
   * @param string $field Entity Field Name
   * @param mixed $value Field's Incoming Value
   * @return mixed Field's Outgoing Value
   */
  protected function transformFieldValue($field, $value) {
    // Call Base Class to Apply Initial Transforms
    $value = parent::transformFieldValue($field, $value);

    switch ($field) {
      case 'password' : // Encode Password with MD5
        $value = Strings::nullOnEmpty($value);
        $value = md5(isset($value) ? $value : '');
        break;
      case 'first_name':
      case 'last_name':
      case 's_description':
      case 'l_description':
        $value = Strings::nullOnEmpty($value);
    }

    return $value;
  }

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \models\User An instance of a User Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\User();
  }

}
