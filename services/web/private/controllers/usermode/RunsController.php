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
namespace controllers\usermode;

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
   * @param integer $set_id Test Set to create Run From
   * @return string HTTP Body Response
   */
  public function create($set_id, $name) {
    // Create Action Context
    $context = new ActionContext('create');
    $context = $context->
            setIfNotNull('run:name', Strings::nullOnEmpty($name))->
            setParameter('set:id', (integer) $id);
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
    // Call Action
    return $this->doAction($context->setParameter('run:id', (integer) $id));
  }

  /**
   * Retrieve the Run with the Given Name.
   * 
   * @param string $name Run's Unique Name
   * @return string HTTP Body Response
   */
  public function readByName($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Call Action
    return $this->doAction($context->setIfNotNull('run:name', Strings::nullOnEmpty($name)));
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
    // Call Action
    return $this->doAction($context->setParameter('run:id', (integer) $id));
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
    // Call Action
    return $this->doAction($context->setParameter('run:id', (integer) $id));
  }

  /**
   * List of Runs belonging to the Specified/Session Project.
   * 
   * @param integer $project_id OPTIONAL Project ID (if not given Session 
   *   Project is Used)
   * @return string HTTP Body Response
   */
  public function listProject($project_id = null) {
    // Create Action Context
    $context = new ActionContext('list_project');
    // Call Action
    return $this->doAction($context->setIfNotNull('project:id', isset($id) ? (integer) $project_id : null));
  }

  /**
   * Count of Runs belonging to the Specified/Session Project.
   * 
   * @param integer $project_id OPTIONAL Project ID (if not given Session 
   *   Project is Used)
   * @return string HTTP Body Response
   */
  public function countProject($project_id = null) {
    // Create Action Context
    $context = new ActionContext('count_project');
    // Call Action
    return $this->doAction($context->setIfNotNull('project:id', isset($id) ? (integer) $project_id : null));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * List the Runs in a Specific Project.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \Run[] Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doListProjectAction($context) {
    return \Run::listRuns($context->getParameter('project'));
  }

  /**
   * Count the Number of Runs in a Specific Project.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return integer Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doCountProjectAction($context) {
    return \Run::countRuns($context->getParameter('project'));
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

    // TODO Required Verification that User Has Required Permission against this Organization and/or Project for the Actions
    // Need a Session for all the Session Commands
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();
    $this->sessionManager->checkProject();

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

    // Remove Relations to Run
    \Run::removeRelations($context->getParameter('entity'));
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

    // Get the Project for the Active Session
    $id = $this->sessionManager->getProject();
    $project = \Project::findFirst($id);

    // Did we find the project?
    if ($user === FALSE) { // NO
      throw new \Exception("Project [$id] not found", 1);
    }
    $context->setParameter('project', $project);

    // Process 'run:name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'run:name', function($controller, $context, $action, $value) {
      // Try to Find the Run by Name
      $run = \Run::findFirstByName($context->getParameter('project'), $value);

      // Are we trying to 'create' a new run?
      if ($action === 'Create') { // YES
        // Did we find an existing run with the same name?
        if ($run !== FALSE) { // YES
          throw new \Exception("Run [$value] already exists.", 2);
        }
      } else { // NO: Some other action
        // Did we find an existing run?
        if ($run === FALSE) { // NO
          throw new \Exception("Run [$value] not found", 3);
        }

        $context->setParameter('entity', $run);
        $context->setParameter('run', $run);
      }

      return $context;
    }, 'Read', 'Create');

    // Process 'set:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'set:id', function($controller, $context, $action, $value) {
      // Try to Find the Test Set
      $set = \Set::findFirstByName($context->getParameter('project'), $value);

      // Did we find an existing Test Set?
      if ($set === FALSE) { // NO
        throw new \Exception("Set [$value] not found", 4);
      }

      $context->setParameter('set', $set);

      return $context;
    }, null, 'Create');

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 5);
    }

    return $context->setParameter('user', $user);
  }

  /**
   * Perform cleanup, after the Action Handler is Called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \Run Modified Run Entity
   * @throws \Exception On any type of failure condition
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Run that was Previously Created
    $run = $context->getActionResult();
    assert('isset($run)');

    // Get the Set from Which we want to build the RunLinks
    \Run::cloneSetLinks($run, $run->set);

    // Create the Container for the Organization
    $container = \Container::createContainer("ROOT ORG[{$run->id}]", $run);
    $container->setSingleLevel(1);
    if ($container->save() === FALSE) {
      throw new \Exception("Failed to Create Container for Run [{$run->name}].", 1);
    }
    $run->container = $container;

    // No change to the context
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
      case 'LinkAdd':
        if (isset($results)) {
          $return = $results->toArray();
        }
        break;
      case 'ListProject':
        $return = array();
        foreach ($results as $set) {
          $id = $set->getId();
          $return[$id] = $set->toArray();
          unset($return[$id]['id']);
        }
        break;
      case 'ListContainer':
        $return = array();
        foreach ($results as $entries) {
          $id = $entries->getId();
          $return[$id] = $entries->toArray();
          unset($return[$id]['id']);
        }
        break;
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
   * @return \Run An instance of a Run Entity, managed by this controller
   */
  protected function createEntity() {
    return new \Run();
  }

}
