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
 * Controller used to Manage Test Sets Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SetsController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a Test Set (if it doesn't already exist) within the Current Session
   * Project and with the Given Name.
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   * 
   * @param string $name Test Set name
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
      ->setIfNotNull('set:name', Strings::nullOnEmpty($name))
      ->setIfNotNull('folder:id', isset($folder) ? (integer) $folder : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Retrieve the Test Set with the Given ID.
   * 
   * @param integer $id Test Set's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $id);

    return $this->doAction($context);
  }

  /**
   * Update the Test Set, with the Given ID, information.
   * 
   * @param integer $id Test Set's Unique Identifier
   * @return string HTTP Body Response
   */
  public function update($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $id);

    return $this->doAction($context);
  }

  /**
   * Delete the Test Set with the Given ID.
   * 
   * @param integer $id Test Set's Unique Identifier
   * @return string HTTP Body Response
   */
  public function delete($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setParameter('set:id', (integer) $id));
  }

  /**
   * List Test Set Entities in the Database, in the Specified Session Project.
   * 
   * @param string $filter OPTIONAL Used to filter the list of Test Sets
   * @param string $sort OPTIONAL Used to organize the sort order of the list
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
   * List Test Set Entities in the Database for the Session Project and in a specific 
   * Project Container.
   * 
   * @param integer $folder Folder ID for Which we want to list the Contained Test Sets
   * @param string $filter OPTIONAL Used to filter the list of Test Sets
   * @param string $sort OPTIONAL Used to organize the sort order of the list
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
   * Count the Number of Test Set Entities in the Database, in the Session Project.
   * 
   * @param string $filter OPTIONAL Used to filter the list of Test Sets
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
   * Count the Number of Test Setss for the Session Project and in a specific Project 
   * Container.
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
   * List Test Set Entities
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Set[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    $sets = [];
    if ($context->hasParameter('folder:id')) {
      $container = $context->getParameter('folder');

      // List Test Sets in Folder
      $sets = \models\Set::listInFolder($container);
    } else {
      $project = $context->getParameter('project');

      // List Test Sets in Project
      $filter = $this->_buildFilter($context->getParameter('filter'));
      $order = $this->_buildOrderBy($context->getParameter('sort'), 'name');
      $sets = \models\Set::listInProject($project, $filter, $order);
    }

    // Return Result Set
    return $sets;
  }

  /**
   * Count Test Set Entries
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of TestsSets
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    $count = 0;
    if ($context->hasParameter('folder:id')) {
      $container = $context->getParameter('folder');

      // Count Test Sets in Folder
      $count = \models\Set::countInFolder($container);
    } else {
      $project = $context->getParameter('project');

      // Count Test Sets in Project
      $filter = $this->_buildFilter($context->getParameter('filter'));
      $count = \models\Set::countInProject($project, $filter);
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
    // TODO Required Verification that User Has Required Permission against this Organization and/or Project for the Actions
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
      // Get Working Project
      $project = $context->getParameter('project');

      // Does the Folder Belong to the Project?
      if (($value->type_owner != 'P') || ($value->owner !== $project->id)) { // NO
        throw new \Exception("Container [{$value->id}] is invalid", 1);
      }

      return $context;
    }, ['Create', 'List', 'Count'], null);

    // Verify if the Test Set Name is UNIQUE in the PROJECT and FOLDER
    $context = $this->onParameterDo($context, 'set:name', function($controller, $context, $action, $value) {
      // Is the Test Set Name Unique for the Project?
      $project = $context->getParameter('project');
      $set = \models\Set::findFirstByName($project, $value);
      // Did we find an existing Test Set with the same name?
      if ($set !== FALSE) { // YES
        throw new \Exception("Test Set [$name] already exists in Project.", 2);
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
    $container = $context->getParameter('set:container');

    // Link the Test Set to the Project and Container
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

    // Are we creating a new test set?
    if ($context->getAction() === 'Create') { // YES
      // Get the Container
      $set_container = $context->getParameter('set:container');
      $set_container->setOwner($entity->id, 'S');

      // Save the Container
      $this->_persist($set_container);

      // Get the Parent Folder in Which to Create the Test Set Link
      $parent = $context->getParameter('folder');

      // Create the Container for the Organization
      $project_link = \models\Container::newContainerEntry($parent, $entity->id, $entity->name, 'S');

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
    $name = $context->getParameter('set:name');

    // Create the Container for the Organization
    $container = \models\Container::newRootContainer($name, 'S', true);

    // If the Entity Allows it Set the Creation User and Date
    $this->setCreator($container, $context->getParameter('user'));

    // Save the Container
    $this->_persist($container);
    $context->setParameter('set:container', $container);
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

    // Remove All Set<-->Test Relations
    \SetTest::deleteBySet($context->getParameter('entity'));
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

    // Process 'set:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'set:id', function($controller, $context, $action, $value) {
        // Project
        $project = $context->getParameter('project');

        // Does the Organization with the given ID exist?
        $set = \models\Set::findFirst([
            'conditions' => 'project = :project: and id = :id:',
            'bind' => [ 'project' => $project->id, 'id' => $value]
        ]);
        if ($set === FALSE) { // NO
          throw new \Exception("Test Set [$value] not found in Current Session Project", 4);
        }

        // Save the Test for the Action
        $context
          ->setParameter('entity', $set)
          ->setParameter('set', $set);

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
   * @return \models\Set An instance of a Test Set Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\Set();
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
