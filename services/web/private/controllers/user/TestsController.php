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

namespace controllers\user;

use api\controller\ActionContext;
use \common\utility\Strings;
use api\controller\CrudServiceController;

/**
 * Controller used to Manage Test Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class TestsController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a Test (if it doesn't already exist) within the Current Session
   * Project and with the Given Name.
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   * 
   * @param string $name Test name
   * @param integer $folder Folder ID in which to create the test
   * @return string HTTP Body Response
   */
  public function create($name, $folder = null) {
    // Create Action Context
    $context = new ActionContext('create');

    // Clean Up Parameter
    $folder = Strings::nullOnEmpty($folder);

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setIfNotNull('test:name', Strings::nullOnEmpty($name))
      ->setIfNotNull('folder:id', isset($folder) ? (integer) $folder : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Retrieve the Test with the Given ID.
   * 
   * @param integer $id Test's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Update the Test, with the Given ID, information.
   * 
   * @param integer $id Test's Unique Identifier
   * @return string HTTP Body Response
   */
  public function update($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Delete the Test with the Given ID.
   * 
   * @param integer $id Test's Unique Identifier
   * @return string HTTP Body Response
   */
  public function delete($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Test Entities in the Database for the Session Project and, optionallu,
   * in a specific Project Container.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Tests
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @param string $filter OPTIONAL Filter String
   * @param string $sort OPTIONAL Sort String
   * @return string HTTP Body Response
   */
  public function listInProject($filter = null, $sort = null) {
    // Create Action Context
    $context = new ActionContext('list');
    // Build Parameters
    $context = $context
      ->setIfNotNull('filter', Strings::nullOnEmpty($filter))
      ->setIfNotNull('sort', Strings::nullOnEmpty($sort));
    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Test Entities in the Database for the Session Project and, optionallu,
   * in a specific Project Container.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Tests
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @param string $filter OPTIONAL Filter String
   * @param string $sort OPTIONAL Sort String
   * @return string HTTP Body Response
   */
  public function listInFolder($folder, $filter = null, $sort = null) {
    // Create Action Context
    $context = new ActionContext('list');
    // Build Parameters
    $context = $context
      ->setParameter('folder:id', (integer) $folder)
      ->setIfNotNull('filter', Strings::nullOnEmpty($filter))
      ->setIfNotNull('sort', Strings::nullOnEmpty($sort));
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count the Number of Tests for the Session Project and, optionally, in a
   * specific Project Container.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Tests
   * 
   * @param string $filter OPTIONAL Filter String
   * @return string HTTP Body Response
   */
  public function countInProject($filter = null) {
    // Create Action Context
    $context = new ActionContext('count');
    // Build Parameters
    $context = $context
      ->setIfNotNull('filter', Strings::nullOnEmpty($filter));
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count the Number of Tests for the Session Project and, optionally, in a
   * specific Project Container.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Tests
   * 
   * @param integer $folder Container ID to List Tests For
   * @param string $filter OPTIONAL Filter String
   * @return string HTTP Body Response
   */
  public function countInFolder($folder, $filter = null) {
    // Create Action Context
    $context = new ActionContext('count');
    // Build Parameters
    $context = $context
      ->setParameter('folder:id', (integer) $folder)
      ->setIfNotNull('filter', Strings::nullOnEmpty($filter));
    // Call Action
    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * List Test Entities
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    $tests = [];
    if ($context->hasParameter('folder:id')) {
      $container = $context->getParameter('folder');

      // List Tests in Folder
      $tests = \models\Test::listInFolder($container);
    } else {
      $project = $context->getParameter('project');

      // List Tests in Project
      $filter = $this->_buildFilter($context->getParameter('filter'));
      $order = $this->_buildOrderBy($context->getParameter('sort'), 'name');
      $tests = \models\Test::listInProject($project, $filter, $order);
    }

    // Return Result Set
    return $tests;
  }

  /**
   * Count Test Entries
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Tests
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    $count = 0;
    if ($context->hasParameter('folder:id')) {
      $container = $context->getParameter('folder');

      // Count Tests in Folder
      $count = \models\Test::countInFolder($container);
    } else {
      $project = $context->getParameter('project');

      // Count Tests in Project
      $filter = $this->_buildFilter($context->getParameter('filter'));
      $count = \models\Test::countInProject($project, $filter);
    }

    // Return Result Set
    return $count;
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
    $this->sessionManager->checkOrganization();
    $this->sessionManager->checkProject();

    return $context;
  }

  /**
   * Perform checks the Context for the Action Before it is called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function contextChecks($context) {
    // (IF SPECIFIED) Verify that the Folder Belongs to the Project
    $context = $this->onParameterDo($context, 'folder', function($controller, $context, $action, $value) {
      // Is the Test Name Unique for the Project?
      $project = $context->getParameter('project');

      // Does the Folder Belong to the Project?
      if (($value->type_owner != 'P') || ($value->owner !== $project->id)) { // NO
        throw new \Exception("Container [{$value->id}] is invalid", 1);
      }

      return $context;
    }, ['Create', 'List', 'Count'], null);

    // Verify if the Test Name is UNIQUE in the PROJECT and FOLDER
    $context = $this->onParameterDo($context, 'test:name', function($controller, $context, $action, $value) {
      // Is the Test Name Unique for the Project?
      $project = $context->getParameter('project');
      $test = \models\Test::findFirstByName($project, $value);
      // Did we find an existing test with the same name?
      if ($test !== FALSE) { // YES
        throw new \Exception("Test [$name] already exists in Project.", 2);
      }

      // Does the Folder already contain an Entity with the same name?
      $folder = $context->getParameter('folder');
      if (\models\Container::existsName($folder, $value)) { // YES
        throw new \Exception("Duplicate Name [{$value}] in Folder.", 3);
      }

      return $context;
    }, null, 'Create');

    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * CREATE ACTION STAGES Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Initializes the Entity
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @param \api\model\AbstractEntity $entity Entity to be Updated
   * @return \api\model\AbstractEntity Updated Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function stageInitializeEntity($context, $entity) {
    $entity = parent::stageInitializeEntity($context, $entity);

    // Get the Container
    $project = $context->getParameter('project');

    // Get the Container
    $container = $context->getParameter('test:container');

    // Link the Test to the Project and Container
    $entity->project = $project->id;
    $entity->container = $container->id;
    return $entity;
  }

  /**
   * Saves Changes back to the Backend Data Store
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @param \api\model\AbstractEntity $entity Entity to be Saved
   * @return \api\model\AbstractEntity Saved Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function stagePersistEntity($context, $entity) {
    $entity = parent::stagePersistEntity($context, $entity);

    // Are we creating a new test?
    if ($context->getAction() === 'Create') { // YES
      // Get the Container
      $test_container = $context->getParameter('test:container');
      $test_container->setOwner($entity->id, 'T');

      // Save the Container
      $this->_persist($test_container);

      // Get the Parent Folder in Which to Create the Test Link
      $parent = $context->getParameter('folder');

      // Create the Container for the Organization
      $project_link = \models\Container::newContainerEntry($parent, $entity->id, $entity->name, 'T');

      // If the Entity Allows it Set the Creation User and Date
      $this->setCreator($project_link, $context->getParameter('cm_user'));

      // Save the Container
      $this->_persist($project_link);
    }

    return $entity;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required preparation, before the Create Action Handler is Called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Call the General Handler 1st (to Setup Context)
    $context = $this->preAction($context);

    // Name for Container
    $name = $context->getParameter('test:name');

    // Create the Container for the Organization
    $container = \models\Container::newRootContainer($name, 'T', true);

    // If the Entity Allows it Set the Creation User and Date
    $this->setCreator($container, $context->getParameter('user'));

    // Save the Container
    $this->_persist($container);
    $context->setParameter('test:container', $container);
    return $context;
  }

  /**
   * Perform any required preparation, before the Delete Action Handler is Called.
   * 
   * TODO: Convert to PHALCON
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preActionDelete($context) {
    // Call Base Class to Initialize Context
    $context = $this->preAction($context);

    // TODO Verify that the test belongs to the current active project
    // Get the Test
    $test = $context->getParameter('entity');

    // Unlink all Relations tot the Test
    $this->getRepository()->removeRelations($test);
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

    // Get the User for the Active Session
    $user = $this->sessionManager->getUser();
    $user = \models\User::findFirst($user['id']);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 1);
    }
    $context = $context
      ->setParameter('user', $user)
      ->setParameter('cm_user', $user);

    // Get Project for Session
    $project = $this->sessionManager->getProject();
    $project = \models\Project::findFirst($project['id']);
    if ($project === FALSE) { // NO
      throw new \Exception("Session Project [$id] is invalid.", 2);
    }
    $context = $context->setParameter('project', $project);

    // Process 'folder:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'folder:id', function($controller, $context, $action, $value) {
      // Did we find the Container with the Given ID?
      $container = \models\Container::findFirst($value);
      if ($container === FALSE) { // NO
        throw new \Exception("Container [$value] not found", 3);
      }

      // TODO: Verify the Folder belongs the Session Project
      return $context->setParameter('folder', $container);
    }, ['List', 'Count'], ['Create'], function($controller, $context, $action) {
      // Get the ROOT Container for the Context Project
      $project = $context->getParameter('project');
      return $project->container;
    });

    // Process 'test:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'test:id', function($controller, $context, $action, $value) {
        // Project
        $project = $context->getParameter('project');

        // Does the Organization with the given ID exist?
        $test = \models\Test::findFirst([
            'conditions' => 'project = :project: and id = :id:',
            'bind' => [ 'project' => $project->id, 'id' => $value]
        ]);
        if ($test === FALSE) { // NO
          throw new \Exception("Test [$value] not found in Current Session Project", 4);
        }

        // Save the Test for the Action
        $context
          ->setParameter('entity', $test)
          ->setParameter('test', $test);

        return $context;
      }, null, ['Read', 'Update', 'Delete']);
    }

    return $context;
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
        foreach ($results as $project) {
          $entities[] = $project->toArray($header);
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
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \models\Test An instance of a Test Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\Test();
  }

  /*
   * ---------------------------------------------------------------------------
   * HELPER FUNCTIONS: Entity DB Persistance
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param type $filter
   * @return type
   */
  protected function _buildFilter($filter) {
    $condition = null;
    if (isset($filter)) {
      $filter = strtoupper($filter);
      $filter = str_split($filter);
      $filter = array_map(function($type) {
        return "'{$type}'";
      }, $filter);

      return 'type IN (' . implode(',', $filter) . ')';
    }

    return null;
  }

  /**
   * 
   * @param type $sort
   * @param type $default
   * @return type
   */
  protected function _buildOrderBy($sort, $default) {
    // Do we have Sort Condition Set?
    if (!isset($sort)) { // NO: User Default
      return $default;
    }

    // Explode the Sort Condition into Seperate Fields
    $fields = explode(',', $sort);

    // Process Each Field Extracting Sort Condition
    $condition = '';
    $comma = false;
    $descdending = false;
    foreach ($fields as $field) {
      $field = Strings::nullOnEmpty($field);
      if (!isset($field)) {
        continue;
      }

      $descending = false;
      if ($field[0] === '!') {
        $descdending = true;
        $field = Strings::nullOnEmpty(substr($field, 1));
      }

      if (isset($field) && property_exists('\models\Container', $field)) {
        $condition .= $comma ? ", {$field}" : $field;
        if ($descdending) {
          $condition.=' DESC';
        }
      }
    }

    return $condition === '' ? $default : $condition;
  }

}

/* CONTEXT CHECKS
 * ACTION - ALL:
 *   - HAVE AN ACTIVE LOGGIN
 *   - HAVE ORGANIZATION and PROJECT SELECTED
 * ACTION - CREATE:
 *   - IF FOLDER PROVIDED, MAKE SURE THE FOLDER BELONGS TO THE SESSION PROJECT
 * ACTION - READ/UPDATE/DELETE
 *   - VERIFY THAT THE TEST BELONGS TO THE SESSION PROJECT
 * ACTION - LIST/COUNT
 *   - IF FOLDER PROVIDED, MAKE SURE THE FOLDER BELONGS TO THE SESSION PROJECT
 */

/* SECURITY CHECKS
 * ACTION - ALL:
 *   - IMPLIED TRUE, if the USER HAS SUCCESSFULLY DEFINED ORGANIZATIO and PROJECT,
 *     DOES THE USER HAVE ACCESS TO THE PROJECT
 * ACTION - CREATE / UPDATE
 *   - DOES THE USER HAVE (W)rite ACCESS TO THE TESTS in the PROJECT
 * ACTION - DELETE
 *   - DOES THE USER HAVE (D)elete ACCESS TO THE TESTS in the PROJECT
 * 
 * LEVELS FOR TESTS
 * 0 - READ ACCESS (DEFAULT ACCESS, i.e. if a user can access the project
 * he can read the tests, IMPLIED)
 * 1 - WRITE ACCESS (User can Create and Update Tests).
 * 2 - DELETE ACCESS (User can Delete Tests)
 */

/* TODO:
 * 1. If the Test Name is Modified (maybe through the update service) the container
 * link also has to have the name modified.
 * 2. If the Update is Called, but nothing is modified, than don't persist the
 * object, or re-brand the modifier.
 */