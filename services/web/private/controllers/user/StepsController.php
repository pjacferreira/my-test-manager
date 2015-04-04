<?php

/* Test Center - Compliance Testing Application
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
use common\utility\Strings;
use api\controller\CrudServiceController;

/**
 * Controller used to Manage Test Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class StepsController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a Step and at it to the End of the Steps List
   * (Alias of createAfter)
   * 
   * @param integer $test Test ID
   * @param string $title Step Title
   * @return string HTTP Body Response
   */
  public function create($test, $title) {
    return $this->createEOL($test, Strings::nullOnEmpty($title));
  }

  /**
   * Create a Step and at it to the Beginning of the Steps List
   * 
   * @param integer $test Test ID
   * @param string $title Step Title
   * @return string HTTP Body Response
   */
  public function createBOL($test, $title) {
    return $this->_create($test, $title, 0);
  }

  /**
   * Create a Step and at it to the End of the Steps List
   * 
   * @param integer $test Test ID
   * @param string $title Step Title
   * @return string HTTP Body Response
   */
  public function createEOL($test, $title) {
    return $this->_create($test, Strings::nullOnEmpty($title), \models\TestStep::MAX_SEQUENCE + 1);
  }

  /**
   * Create a Step and Add it After the Specified Position
   * 
   * @param integer $test Test ID
   * @param integer $sequence Position to add after
   * @param string $title Step Title
   * @return string HTTP Body Response
   */
  public function createAFTER($test, $sequence, $title) {
    assert('isset($sequence) && is_string($sequence)');
    return $this->_create($test, Strings::nullOnEmpty($title), (integer) $sequence);
  }

  /**
   * Create a Test in the Specified Test at the Specified Position
   * 
   * @param integer $test Test ID
   * @param string $title Step Title
   * @param integer $sequence Position to add after
   * @return string HTTP Body Response
   */
  protected function _create($test, $title, $sequence) {
    assert('isset($test) && is_string($test)');
    assert('isset($title) && is_string($title)');
    assert('isset($sequence) && is_integer($sequence)');

    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test)
      ->setParameter('step:title', $title)
      ->setParameter('position', $sequence);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Read the Step Information for the Given Test and Sequence
   * 
   * @param integer $test Test ID
   * @param integer $sequence Position to add after
   * @return string HTTP Body Response
   */
  public function read($test, $sequence) {
    assert('isset($test) && is_string($test)');
    assert('isset($sequence) && is_string($sequence)');

    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test)
      ->setParameter('step:sequence', (integer) $sequence);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Update the Step Information for the Given Test and Sequence
   * 
   * @param integer $test Test ID
   * @param integer $sequence Step Sequence to Modify
   * @return string HTTP Body Response
   */
  public function update($test, $sequence) {
    assert('isset($test) && is_string($test)');
    assert('isset($sequence) && is_string($sequence)');

    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test)
      ->setParameter('step:sequence', (integer) $sequence);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Delete the Step Information for the Given Test and Sequence
   * 
   * @param integer $test Test ID
   * @param integer $sequence Step Sequence to Delete
   * @return string HTTP Body Response
   */
  public function delete($test, $sequence) {
    assert('isset($test) && is_string($test)');
    assert('isset($sequence) && is_string($sequence)');

    // Create Action Context
    $context = new ActionContext('delete');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test)
      ->setParameter('step:sequence', (integer) $sequence);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Step to the Top of the List or Before the Given Step
   * 
   * @param integer $test Test ID
   * @param integer $sequence Step Sequence to Move
   * @param integer $before OPTIONAL Before this Sequence
   * @return string HTTP Body Response
   */
  public function moveUp($test, $sequence, $before = null) {
    assert('isset($test) && is_string($test)');
    assert('isset($sequence) && is_string($sequence)');

    // Position to Place After
    $before = Strings::nullOnEmpty($before);

    // Create Action Context
    $context = new ActionContext('move_up');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test)
      ->setParameter('step:sequence', (integer) $sequence)
      ->setIfNotNull('position', isset($before) ? (integer) $before : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Step to the Bottom of the List or After the Given Step
   * 
   * @param integer $test Test ID
   * @param integer $sequence Step Sequence to Move
   * @param integer $after OPTIONAL After this Sequence
   * @return string HTTP Body Response
   */
  public function moveDown($test, $sequence, $after = null) {
    assert('isset($test) && is_string($test)');
    assert('isset($sequence) && is_string($sequence)');

    // Position to Place After
    $after = Strings::nullOnEmpty($after);

    // Create Action Context
    $context = new ActionContext('move_down');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test)
      ->setParameter('step:sequence', (integer) $sequence)
      ->setIfNotNull('position', isset($after) ? (integer) $after : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Renumber the Steps in the Test
   * 
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function renumber($test) {
    assert('isset($test) && is_string($test)');

    // Create Action Context
    $context = new ActionContext('renumber');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Steps in the Test
   * 
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function listInTest($test) {
    assert('isset($test) && is_string($test)');

    // Create Action Context
    $context = new ActionContext('list');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count Steps in the Test
   * 
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function countInTest($test) {
    assert('isset($test) && is_string($test)');

    // Create Action Context
    $context = new ActionContext('count');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test);
    // Call Action
    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Move Step Up in the List
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveUpAction($context) {
    $test = $context->getParameter('test');
    $step = $context->getParameter('step');
    $position = $context->getParameter('position');
    if (\models\TestStep::moveUp($step, $position) !== null) {
      /* NOTE: Always Force Renumber since we can't know wether the step
       * lists will correctly numbered.
       */
      // Need to Renumber Test Steps
      $test->renumber = 1;
      $this->_persist($test);
    }
    return $step;
  }

  /**
   * Move Step Down in the List
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveDownAction($context) {
    $test = $context->getParameter('test');
    $step = $context->getParameter('step');
    $position = $context->getParameter('position');
    if (\models\TestStep::moveDown($step, $position) !== null) {
      // Need to Renumber Test Steps
      $test->renumber = 1;
      $this->_persist($test);
    }
    return $step;
  }

  /**
   * Renumber Test Steps (if required)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Renumbered Step Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doRenumberAction($context) {
    /* NOTE:
     * Renumber does not alter modification time on Test or Steps
     */

    /* TODO: Considereation Offline Test Step Renumbering
     * Instead of performing a Test Step Renumbering Online (i.e. as the user
     * is making modifications) we might perform this renumbering in a batch
     * command during the night
     */
    $test = $context->getParameter('test');
    if ($test->renumber) { // YES: Renumber before Returning List
      $steps = \models\TestStep::renumberSteps($test);

      // Clear the Test's Renumber Flag and Save it.
      $test->renumber = 0;
      $this->_persist($test);
    } else { // NO
      $steps = \models\TestStep::listInTest($test);
    }
    return $steps;
  }

  /**
   * List Test Steps
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Step Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    // Does the Test Steps need to be Renumbered?
    $test = $context->getParameter('test');
    return \models\TestStep::listInTest($test);
  }

  /**
   * Count Test Steps
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Test Steps
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    $test = $context->getParameter('test');
    $count = \models\TestStep::countInTest($test);
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
    $this->sessionManager->checkProject();

    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * CREATE ACTION STAGES Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a new Entity Based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \api\model\AbstractEntity Newly Created Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function stageCreateEntity($context) {
    // Parameters
    $test = $context->getParameter('test');
    $title = $context->getParameter('step:title');
    $position = $context->getParameter('position');

    // Create Step
    $step = \models\TestStep::newStep($test, $title, $position);

    // Mark Test as Needing to be Renumbered
    if (($step->sequence % 100) > 0) {
      $test->renumber = 1;
    }

    return $step;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any Post Action tasks.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Test and User
    $test = $context->getParameter('test');
    $user = $context->getParameter('user');

    // Step Properties
    $this->setModifier($test, $user);

    // Save the Changes Back to the Database
    $this->_persist($test);

    return $context;
  }

  /**
   * Perform any Post Action tasks.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function postActionDelete($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Test and User
    $test = $context->getParameter('test');
    $user = $context->getParameter('user');

    // Do we have to force a Renumber?
    $step = $context->getParameter('step');
    if ($step % \models\TestStep::SEQUENCE_STEP) { // YES
      $test->renumber = true;
    }

    // Step Properties
    $this->setModifier($test, $user);

    // Save the Changes Back to the Database
    $this->_persist($test);

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

    // Get the Test to Work On
    $id = $context->getParameter('test:id');
    $test = \models\Test::findInProject($project, $id);
    if ($test === FALSE) { // NO
      throw new \Exception("Test [$id] not found in the Session Project", 3);
    }
    $context = $context->setParameter('test', $test);

    // Get the Step to Work On
    $context = $this->onParameterDo($context, 'step:sequence', function($controller, $context, $action, $value) {
      // Project
      $test = $context->getParameter('test');

      // Does the Organization with the given ID exist?
      $step = \models\TestStep::findStep($test, $value);
      if ($step === FALSE) { // NO
        throw new \Exception("Step [$value] not found in Test [{$test->sequence}]", 4);
      }

      // Save the Organization for the Action
      $context->setParameter('entity', $step)
        ->setParameter('step', $step);

      return $context;
    }, null, ['MoveUp', 'MoveDown', 'Move', 'Read', 'Update', 'Delete']);

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
      case 'MoveUp':
      case 'MoveDown':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'Renumber':
      case 'List':
        $return = [];
        $entities = [];
        $header = true;
        foreach ($results as $step) {
          $entities[] = $step->toArray($header);
          $header = false;
        }

        // Create Base Entity Set Identified
        $return['__type'] = 'entity-set';

        // Do we have entities to display?
        if (count($entities)) { // YES
          // Move the Entity Information to become Result Header
          $this->moveEntityHeader($entities[0], $return);
          $return['entities'] = $entities;
        } else {
          $return['entities'] = [];
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
   * @return \models\TestStep An instance of a Test Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\TestStep();
  }

}
