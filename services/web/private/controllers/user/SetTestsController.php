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
use api\controller\EntityServiceController;
use common\utility\Strings;

/**
 * Controller used to Manage Relationship Betweet Set and Tests
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SetTestsController extends EntityServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a Step and at it to the End of the Steps List
   * (Alias of createAfter)
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function link($set, $test) {
    return $this->linkEOL((integer) $set, (integer) $test);
  }

  /**
   * Create a Step and at it to the Beginning of the Steps List
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function linkBOL($set, $test) {
    return $this->_link((integer) $set, (integer) $test, 0);
  }

  /**
   * Create a Step and at it to the End of the Steps List
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function linkEOL($set, $test) {
    return $this->_link((integer) $set, (integer) $test, \models\SetTest::MAX_SEQUENCE + 1);
  }

  /**
   * Link the Set and Test and Add it After the Specified Position
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @param integer $sequence Position to add after
   * @return string HTTP Body Response
   */
  public function linkAFTER($set, $test, $sequence) {
    assert('isset($sequence) && is_string($sequence)');
    return $this->_link((integer) $set, (integer) $test, (integer) $sequence);
  }

  /**
   * Create a Test in the Specified Test at the Specified Position
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @param integer $sequence Position to add after
   * @return string HTTP Body Response
   */
  protected function _link($set, $test, $sequence) {
    assert('isset($set) && is_integer($set)');
    assert('isset($test) && is_integer($test)');
    assert('isset($sequence) && is_integer($sequence)');

    // Create Action Context
    $context = new ActionContext('link');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('test:id', (integer) $test)
      ->setParameter('position', $sequence);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Delete the Link Between the Set and the Test
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function unlink($set, $test) {
    assert('isset($set) && is_string($set)');
    assert('isset($sequence) && is_string($test)');

    // Create Action Context
    $context = new ActionContext('unlink');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('test:id', (integer) $test);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Delete the Link with the Given Sequence
   * 
   * @param integer $set Set ID
   * @param integer $sequence Sequence to Delete
   * @return string HTTP Body Response
   */
  public function delete($set, $sequence) {
    assert('isset($set) && is_string($set)');
    assert('isset($sequence) && is_string($sequence)');

    // Create Action Context
    $context = new ActionContext('delete');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('link:sequence', (integer) $sequence);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Test Up one Spot, or Before the Given Test
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @param integer $before OPTIONAL Before this Test
   * @return string HTTP Body Response
   */
  public function moveTestUp($set, $test, $before = null) {
    assert('isset($set) && is_string($set)');
    assert('isset($test) && is_string($test)');

    // Position to Place After
    $before = Strings::nullOnEmpty($before);

    // Create Action Context
    $context = new ActionContext('move_test_up');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('test:id', (integer) $test)
      ->setIfNotNull('position', isset($before) ? (integer) $before : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Test Down one Spot, or Before the Given Test
   * 
   * @param integer $set Set ID
   * @param integer $test Test ID
   * @param integer $after OPTIONAL After this Test
   * @return string HTTP Body Response
   */
  public function moveTestDown($set, $test, $after = null) {
    assert('isset($set) && is_string($set)');
    assert('isset($test) && is_string($test)');

    // Position to Place After
    $after = Strings::nullOnEmpty($after);

    // Create Action Context
    $context = new ActionContext('move_test_down');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('test:id', (integer) $test)
      ->setIfNotNull('position', isset($after) ? (integer) $after : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Test, at the Given Sequence, UP one Spot, or Before the Given Sequence
   * 
   * @param integer $set Set ID
   * @param integer $sequence Sequence to Move
   * @param integer $before OPTIONAL Before this Sequence
   * @return string HTTP Body Response
   */
  public function moveUp($set, $sequence, $before = null) {
    assert('isset($set) && is_string($set)');
    assert('isset($sequence) && is_string($sequence)');

    // Position to Place After
    $before = Strings::nullOnEmpty($before);

    // Create Action Context
    $context = new ActionContext('move_up');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('link:sequence', (integer) $sequence)
      ->setIfNotNull('position', isset($before) ? (integer) $before : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Test, at the Given Sequence, DOWN one Spot, or After the Given Sequence
   * 
   * @param integer $set Set ID
   * @param integer $sequence Sequence to Move
   * @param integer $after OPTIONAL After this Sequence
   * @return string HTTP Body Response
   */
  public function moveDown($set, $sequence, $after = null) {
    assert('isset($set) && is_string($set)');
    assert('isset($sequence) && is_string($sequence)');

    // Position to Place After
    $after = Strings::nullOnEmpty($after);

    // Create Action Context
    $context = new ActionContext('move_down');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('link:sequence', (integer) $sequence)
      ->setIfNotNull('position', isset($after) ? (integer) $after : null);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Renumber the Tests in a Set
   * 
   * @param integer $set Set ID
   * @return string HTTP Body Response
   */
  public function renumber($set) {
    assert('isset($set) && is_string($set)');

    // Create Action Context
    $context = new ActionContext('renumber');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Renumber the Tests in a Set
   * 
   * @param integer $set Set ID
   * @return string HTTP Body Response
   */
  public function renumberTests($set) {
    assert('isset($set) && is_string($set)');

    // Create Action Context
    $context = new ActionContext('renumber');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set)
      ->setParameter('list_as_test', true);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Links Between a Set and Tests
   * 
   * @param integer $set Set ID
   * @return string HTTP Body Response
   */
  public function listLinks($set) {
    assert('isset($set) && is_string($set)');

    // Create Action Context
    $context = new ActionContext('list');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count Tests in the Set
   * 
   * @param integer $set Set ID
   * @return string HTTP Body Response
   */
  public function count($set) {
    assert('isset($set) && is_string($set)');

    // Create Action Context
    $context = new ActionContext('count');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Tests in the Set
   * 
   * @param integer $set Set ID
   * @return string HTTP Body Response
   */
  public function listTests($set) {
    assert('isset($set) && is_string($set)');

    // Create Action Context
    $context = new ActionContext('list_tests');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('set:id', (integer) $set);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Tests in the Set
   * 
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function listSets($test) {
    assert('isset($test) && is_string($test)');

    // Create Action Context
    $context = new ActionContext('list_sets');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameter('test:id', (integer) $test);
    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count Tests in the Set
   * 
   * @param integer $test Test ID
   * @return string HTTP Body Response
   */
  public function countSets($test) {
    assert('isset($test) && is_string($test)');

    // Create Action Context
    $context = new ActionContext('count_sets');
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
   * Delete Link Between Set and Test
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doLinkAction($context) {
    $set = $context->getParameter('set');
    $test = $context->getParameter('test');
    $position = $context->getParameter('position');

    // Create the Link and then Persist it.
    $link = \models\SetTest::newLink($set, $test, $position);
    $this->_persist($link);

    /* NOTE: Always Force Renumber since we can't know wether the step
     * lists will correctly numbered.
     */
    // Need to Renumber Links
    $set = $context->getParameter('set');
    $set->renumber = 1;
    $this->_persist($set);


    // TODO: Should we update the modifier date/user for the set?
    return isset($link) ? $context->getParameter('test') : null;
  }

  /**
   * Delete Link Between Set and Test
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doUnlinkAction($context) {
    $link = $context->getParameter('link');
    $this->_delete($link);

    /* NOTE: Always Force Renumber since we can't know wether the step
     * lists will correctly numbered.
     */
    // Need to Renumber Links
    $set = $context->getParameter('set');
    $set->renumber = 1;
    $this->_persist($set);

    // TODO: Should we update the modifier date/user for the set?
    return true;
  }

  /**
   * Delete Link Between Set and Test
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doDeleteAction($context) {
    return $this->doUnlinkAction($context);
  }

  /**
   * Move Step Down in the List
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveTestUpAction($context) {
    $link = $this->doMoveUpAction($context);
    return isset($link) ? $context->getParameter('test') : null;
  }

  /**
   * Move Step Up in the List
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveUpAction($context) {
    $link = $context->getParameter('link');
    $position = $context->getParameter('position');
    if (\models\SetTest::moveUp($link, $position) !== null) {
      /* NOTE: Always Force Renumber since we can't know wether the step
       * lists will correctly numbered.
       */
      // Need to Renumber Links
      $set = $context->getParameter('set');
      $set->renumber = 1;
      $this->_persist($set);
    }
    return $link;
  }

  /**
   * Move Step Down in the List
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveTestDownAction($context) {
    $link = $this->doMoveDownAction($context);
    return isset($link) ? $context->getParameter('test') : null;
  }

  /**
   * Move Step Down in the List
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\TestStep Original or Modified Step
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveDownAction($context) {
    $link = $context->getParameter('link');
    $position = $context->getParameter('position');
    if (\models\SetTest::moveDown($link, $position) !== null) {
      // Need to Renumber Links
      $set = $context->getParameter('set');
      $set->renumber = 1;
      $this->_persist($set);
    }
    return $link;
  }

  /**
   * Renumber Tests in a Set (if required)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Renumbered Test Entities
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
    $set = $context->getParameter('set');
    $list_test = $context->getParameter('list_as_test', false);
    if ($set->renumber) { // YES: Renumber before Returning List
      $list = $list_test ? \models\SetTest::renumberTests($set) : \models\SetTest::renumber($set);

      // Clear the Test's Renumber Flag and Save it.
      $set->renumber = 0;
      $this->_persist($set);
    } else { // NO
      $list = $list_test ? \models\SetTest::listTestsInSet($set) : \models\SetTest::listLinks($set);
    }
    return $list;
  }

  /**
   * List Links between Set and Tests
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Test Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    // Does the Test Steps need to be Renumbered?
    $set = $context->getParameter('set');
    return \models\SetTest::listLinks($set);
  }

  /**
   * Count Tests in a Set
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Tests
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    $set = $context->getParameter('set');
    return \models\SetTest::countTestsInSet($set);
  }

  /**
   * List Tests in a Set
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Test Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListTestsAction($context) {
    // Does the Test Steps need to be Renumbered?
    $set = $context->getParameter('set');
    return \models\SetTest::listTestsInSet($set);
  }

  /**
   * List Sets a Test is a Part Of
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Test Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListSetsAction($context) {
    // Does the Test Steps need to be Renumbered?
    $test = $context->getParameter('test');
    return \models\SetTest::listSetsForTest($test);
  }

  /**
   * Count Number of Sets a Test is a Part of
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Tests
   * @throws \Exception On failure to perform the action
   */
  protected function doCountSetsAction($context) {
    $test = $context->getParameter('test');
    return \models\SetTest::countSetsForTest($test);
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
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

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

    // Get the Step to Work On
    $context = $this->onParameterDo($context, 'set:id', function($controller, $context, $action, $value) {
      // Get Working Project
      $project = $context->getParameter('project');

      // Does the Set with the given ID exist?
      $set = \models\Set::findInProject($project, $value);
      if ($set === FALSE) { // NO
        throw new \Exception("Set [$value] not found in Project [{$project->id}]", 4);
      }

      // Save the Set for the Action
      $context->setParameter('set', $set);

      return $context;
    }, null, ['Link', 'Unlink', 'Delete', 'MoveTestUp', 'MoveTestDown', 'MoveUp', 'MoveDown', 'Renumber', 'List', 'ListTests', 'Count']);

    // Get the Test to Work On
    $context = $this->onParameterDo($context, 'test:id', function($controller, $context, $action, $value) {
      // Get Working Project
      $project = $context->getParameter('project');

      // Does the Test with the given ID exist?
      $test = \models\Test::findInProject($project, $value);
      if ($test === FALSE) { // NO
        throw new \Exception("Test [$value] not found in Project [{$project->id}]", 4);
      }

      return $context->setParameter('test', $test);
    }, null, ['Link', 'ListSets', 'CountSets']);

    // Get the Test and Link to Work On
    $context = $this->onParameterDo($context, 'test:id', function($controller, $context, $action, $value) {
      // Get Working Project
      $project = $context->getParameter('project');

      // Does the Test with the given ID exist?
      $test = \models\Test::findInProject($project, $value);
      if ($test === FALSE) { // NO
        throw new \Exception("Test [$value] not found in Project [{$project->id}]", 4);
      }

      // Save the Test Information
      $context->setParameter('test', $test);

      //Get the 'set' for the action
      $set = $context->getParameter('set');

      // Does the Set Test with the given ID exist?
      $link = \models\SetTest::findLinkByTest($set, $test);
      if (!isset($link)) { // NO
        throw new \Exception("Test [{$test->id}] not found in Set [{$set->id}]", 4);
      }

      // Save the Link for the Action
      return $context->setParameter('link', $link);
    }, null, ['Unlink', 'MoveTestUp', 'MoveTestDown']);

    // Get the Position to Move Before
    $context = $this->onParameterDo($context, 'position', function($controller, $context, $action, $value) {
      //Get the 'set' for the action
      $set = $context->getParameter('set');

      // Does the Set Test with the given ID exist?
      $link = \models\SetTest::findLinkByTest($set, $value);
      if ($link === FALSE) { // NO
        throw new \Exception("Test [{$value}] not found in Set [{$set->id}]", 4);
      }

      return $context->setParameter('position', $link->sequence);
    }, ['MoveTestUp', 'MoveTestDown']);

    // Get the Link to Work On
    $context = $this->onParameterDo($context, 'link:sequence', function($controller, $context, $action, $value) {
      //Get the 'set' for the action
      $set = $context->getParameter('set');

      // Does the Link with the given ID exist?
      $link = \models\SetTest::findLink($set, $value);
      if ($link === FALSE) { // NO
        throw new \Exception("Sequence [$value] not found in Set [{$set->id}]", 4);
      }

      // Save the Link for the Action
      return $context->setParameter('link', $link);
    }, null, ['Delete', 'MoveUp', 'MoveDown']);

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
      case 'Link':
      case 'MoveUp':
      case 'MoveTestUp':
      case 'MoveDown':
      case 'MoveTestDown':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'Renumber':
      case 'List':
      case 'ListTests':
      case 'ListSets':
        $return = [];
        $entities = [];
        $header = true;
        foreach ($results as $entity) {
          $entities[] = $entity->toArray($header);
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

}
