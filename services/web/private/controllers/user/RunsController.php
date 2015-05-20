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
 * Controller used to Manage Test Run Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class RunsController extends CrudServiceController {

  protected static $instance = null;

  /**
   * Singleton Pattern - Get Instance of the Controller
   * 
   * @return RunsController Instance of Controller
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new RunsController();
    }

    return self::$instance;
  }

  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a Run, based on a TestSet, within Current Session Project and Working Container
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   *
   * @param string $name Run Name (Unique within the Project it belongs to)
   * @param integer $set Test Set on which to base Run
   * @param integer $folder [OPTIONAL] Folder ID in which to create Run
   * @return string HTTP Body Response
   */
  public function create($name, $set, $folder = NULL) {
    // Create Action Context
    $context = new ActionContext('create');

    // Clean Up Parameter
    $folder = Strings::nullOnEmpty($folder);

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setIfNotNull('run:name', Strings::nullOnEmpty($name))
      ->setIfNotNull('set:id', (integer) $set)
      ->setIfNotNull('folder:id', isset($folder) ? (integer) $folder : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Retrieve the Run with the Given ID.
   * 
   * @param integer $id Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('run:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Update the Run, with the Given ID, information.
   * 
   * @param integer $id Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function update($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('run:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Delete the Run with the Given ID.
   * 
   * @param integer $id Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function delete($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('run:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Run Entities in the Database, in the Specified Session Project.
   * 
   * @param string $filter OPTIONAL Used to filter the list of Runs
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
   * List Run Entities in the Database for the Session Project and in a specific 
   * Project Container.
   * 
   * @param integer $folder Folder ID for Which we want to list the Contained Runs
   * @param string $filter OPTIONAL Used to filter the list of Runs
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
   * List Tests Entities in the Database, for the Specified Run.
   * 
   * @param integer $id Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function listTests($id) {
    // Create Action Context
    $context = new ActionContext('list_tests');
    // Build Parameters
    $context = $context
      ->setParameter('run:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count the Number of Runs Entities in the Database, in the Session Project.
   * 
   * @param string $filter OPTIONAL Used to filter the list of Runs
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
   * Count the Number of Runs for the Session Project and in a specific Project 
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

  /**
   * Count the Number of Tests for the Given Run.
   * 
   * @param integer $id Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function countTests($id) {
    // Create Action Context
    $context = new ActionContext('count_tests');
    // Build Parameters
    $context = $context
      ->setParameter('run:id', (integer) $id);
    // Call Action
    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * List Run Entities
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Set[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    $runs = [];
    if ($context->hasParameter('folder:id')) {
      $container = $context->getParameter('folder');

      // List Runs in Folder
      $runs = \models\Run::listInFolder($container);
    } else {
      $project = $context->getParameter('project');

      // List Runs in Project
      $filter = $this->_buildFilter($context->getParameter('filter'));
      $order = $this->_buildOrderBy($context->getParameter('sort'), 'name');
      $runs = \models\Run::listInProject($project, $filter, $order);
    }

    // Return Result Set
    return $runs;
  }

  /**
   * List Run Entities
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Set[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListTestsAction($context) {
    // Get the Run to List the Tests for
    $run = $context->getParameter('run');

    // List Tests in Folder
    $tests = \models\Run::listTestsInRun($run);

    // Return Result Set
    return $tests;
  }

  /**
   * Count Run Entries
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of TestsSets
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    $count = 0;
    if ($context->hasParameter('folder:id')) {
      $container = $context->getParameter('folder');

      // Count Runs in Folder
      $count = \models\Run::countInFolder($container);
    } else {
      $project = $context->getParameter('project');

      // Count Runs in Project
      $filter = $this->_buildFilter($context->getParameter('filter'));
      $count = \models\Run::countInProject($project, $filter);
    }

    // Return Result Set
    return $count;
  }

  /**
   * Count Run Entries
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of TestsSets
   * @throws \Exception On failure to perform the action
   */
  protected function doCountTestsAction($context) {
    // Get the Run to List the Tests for
    $run = $context->getParameter('run');

    // Countr Number of Unique Tests in a Run
    $tests = \models\Run::countTestsInRun($run);

    // Return Result Set
    return $tests;
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
    $context = $this->onParameterDo($context, 'run:name', function($controller, $context, $action, $value) {
      // Is the Run Name Unique for the Project?
      $project = $context->getParameter('project');
      $run = \models\Run::findFirstByName($project, $value);
      // Did we find an existing Test Set with the same name?
      if ($run !== FALSE) { // YES
        throw new \Exception("Run [$name] already exists in Project.", 2);
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

    // Get the Project
    $project = $context->getParameter('project');

    // Get the Set to Base On
    $set = $context->getParameter('set');

    // Get the Project Settings
    $settings = $context->getParameter('project-settings');

    // Get the Container
    $container = $context->getParameter('run:container');

    // Link the Run to the Project/Set/Container
    $entity->project = $project->id;
    $entity->set = $set->id;
    $entity->container = $container->id;

    // Run set Default State and Run Codes
    $entity->state = $settings->run_state_create;
    $entity->run_code = $settings->run_code_create;
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
    // Set the Owner for the Run
    $this->setOwner($entity, $context->getParameter('cm_user'));

    $entity = parent::stagePersistEntity($context, $entity);

    // Are we creating a new Run?
    if ($context->getAction() === 'Create') { // YES
      // Create Play List for Run
      $playlist = \models\PlayEntry::createPlayList($entity);
      if (count($playlist)) {
        // Get the Project Settings
        $settings = $context->getParameter('project-settings');

        // Initializa and Save each Play List Entry
        foreach ($playlist as $entry) {
          $entry->run_code = $settings->run_code_create;
          $this->_persist($entry);
        }
      } else {
        throw new \Exception("Invalid Test Set [{$id->set}].", 1);
      }

      // Get the Container
      $run_container = $context->getParameter('run:container');
      $run_container->setOwner($entity->id, 'R');

      // Save the Container
      $this->_persist($run_container);

      // Get the Parent Folder in Which to Create the Run Link
      $parent = $context->getParameter('folder');

      // Create Folder Link to the Run
      $run_link = \models\Container::newContainerEntry($parent, $entity->id, $entity->name, 'R');

      // If the Entity Allows it Set the Creation User and Date
      $this->setCreator($run_link, $context->getParameter('cm_user'));

      // Save the Container
      $this->_persist($run_link);
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
    $name = $context->getParameter('run:name');

    // Create the Container for the Organization
    $container = \models\Container::newRootContainer($name, 'R', true);

    // If the Entity Allows it Set the Creation User and Date
    $this->setCreator($container, $context->getParameter('user'));

    // Save the Container
    $this->_persist($container);
    $context->setParameter('run:container', $container);
    return $context;
  }

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

    // Remove Relations to Run
    \models\Run::removeRelations($context->getParameter('entity'));
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

    // Are we performing a Create Action?
    if ($context->getAction() === 'Create') { // YES: Get the Project Settings
      $settings = \models\ProjectSettings::findFirstByProject($project->id);
      $context = $context->setParameter('project-settings', $settings);
    }

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
    $context = $this->onParameterDo($context, 'set:id', function($controller, $context, $action, $value) {
      // Project
      $project = $context->getParameter('project');

      // Does the Run with the given ID exist?
      $set = \models\Set::findFirst([
          'conditions' => 'project = :project: and id = :id:',
          'bind' => [ 'project' => $project->id, 'id' => $value]
      ]);
      if ($set === FALSE) { // NO
        throw new \Exception("Set [$value] not found in Current Session Project", 4);
      }

      return $context->setParameter('set', $set);
    }, null, ['Create']);

    // Process 'run:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'run:id', function($controller, $context, $action, $value) {
        // Project
        $project = $context->getParameter('project');

        // Does the Run with the given ID exist?
        $run = \models\Run::findFirst([
            'conditions' => 'project = :project: and id = :id:',
            'bind' => [ 'project' => $project->id, 'id' => $value]
        ]);
        if ($run === FALSE) { // NO
          throw new \Exception("Run [$value] not found in Current Session Project", 4);
        }

        // Save the Run for the Action
        $context
          ->setParameter('entity', $run)
          ->setParameter('run', $run);

        return $context;
      }, null, ['Read', 'Update', 'Delete', 'ListTests', 'CountTests']);
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
      case 'ListTests':
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
          $return['entities'] = $entities;
        } else {
          $return['entities'] = [];
        }

        // Create Base Entity Set Identified
        $return['__type'] = 'entity-set';
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /*
   * ---------------------------------------------------------------------------
   * OVERRIDE : CrudServiceController
   * ---------------------------------------------------------------------------
   */

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \Run An instance of a Run Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\Run();
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
