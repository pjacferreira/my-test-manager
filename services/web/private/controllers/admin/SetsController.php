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
   * @return string HTTP Body Response
   */
  public function create($name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('set:name', Strings::nullOnEmpty($name));

    // Call the Function
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
   * Retrieve the Test Set with the Given Name.
   * 
   * @param string $name Test Set's Unique Name
   * @return string HTTP Body Response
   */
  public function readByName($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('set:name', Strings::nullOnEmpty($name));

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
   * List Test Set Entities in the Database, in the Specified Project, or if not specified,
   * in the Session Project.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Test Sets
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @param integer $project_id OPTIONAL Project's Unique Identifier
   * @return string HTTP Body Response
   */
  public function listInProject($project_id = null) {
    /* Allowing a NULL project_id is a win-win situation
     * 1. Allows for the scenario that the service is listing the test for the 
     * current session project_id
     */

    // Create Action Context
    $context = new ActionContext('list_in_project');
    // Build Parameters
    $context = $context
            ->setIfNotNull('project:id', isset($project_id) ? (integer) $project_id : null);

    return $this->doAction($context);
  }

  /**
   * Number of Test Set Entities in the Database, in the Specified Project, or if not specified,
   * in the Session Project.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Test Sets
   * 
   * @param integer $project_id OPTIONAL Project's Unique Identifier
   * @return string HTTP Body Response
   */
  public function countInProject($project_id = null) {
    // Create Action Context
    $context = new ActionContext('count_in_project');
    // Build Parameters
    $context = $context
            ->setIfNotNull('project:id', isset($project_id) ? (integer) $project_id : null);

    return $this->doAction($context);
  }

  /**
   * Link the Specified Test to the Specified Test Set.
   * 
   * @param integer $id Test Set's ID
   * @param integer $test_id Test ID
   * @param integer $sequence OPTIONAL Sequence Number (If not Specified the
   *   Test is added to the End of the List with a Calculated Sequence
   *   Number)
   * @return string HTTP Body Response
   */
  public function linkAdd($id, $test_id, $sequence = null) {
    // Create Action Context
    $context = new ActionContext('link_add');
    // Build Parameters
    $context = $context->
            setParameter('set:id', (integer) $id)->
            setParameter('test:id', (integer) $test_id)->
            setIfNotNull('sequence', isset($sequence) ? (integer) $sequence : null);

    return $this->doAction($context);
  }

  /**
   * Unlink the Specified Test to the Specified Test Set.
   * 
   * @param integer $id Test Set's ID
   * @param integer $test_id Test ID
   * @return string HTTP Body Response
   */
  public function linkRemove($id, $test_id) {
    // Create Action Context
    $context = new ActionContext('link_remove');
    // Build Parameters
    $context = $context->
            setParameter('set:id', (integer) $id)->
            setParameter('test:id', (integer) $test_id);

    return $this->doAction($context);
  }

  /**
   * Renumber the Test Sequence Numbers (Maintaining the Order) for the Tests 
   * in the Specified Test Set.
   * 
   * @param integer $id Test Set's ID
   * @param integer $sequence OPTIONAL Sequence Number (If not Specified a 
   *   Step of 10 will be used)
   * @return string HTTP Body Response
   */
  public function linkRenumber($id, $step = 10) {
    // Create Action Context
    $context = new ActionContext('link_renumber');
    // Build Parameters
    $context = $context->
            setParameter('set:id', (integer) $id)->
            setParameter('sequence', isset($sequence) ? (integer) $sequence : 10);

    return $this->doAction($context);
  }

  /**
   * Re-position a Test in the Specified Test Set.
   * 
   * @param integer $id Test Set's ID
   * @param integer $sequence Sequence/Test to Move
   * @param integer $to New Test's Sequence Number
   * @return string HTTP Body Response
   */
  public function linkMove($id, $sequence, $to) {
    // Create Action Context
    $context = new ActionContext('link_move');
    // Build Parameters
    $context = $context->
            setParameter('set:id', (integer) $id)->
            setParameter('sequence', (integer) $sequence)->
            setParameter('to', (integer) $to);

    return $this->doAction($context);
  }

  /**
   * List of Test Entities, associated with the Specified Test Set.
   * 
   * @param integer $id Test Set's ID
   * @return string HTTP Body Response
   */
  public function linkList($id) {
    // Create Action Context
    $context = new ActionContext('link_list');
    return $this->doAction($context
                            ->setParameter('set:id', (integer) $id));
  }

  /**
   * Number of Test Entities, associated with the Specified Test Set.
   * 
   * @param integer $id Test Set's ID
   * @return string HTTP Body Response
   */
  public function linkCount($id) {
    // Create Action Context
    $context = new ActionContext('link_count');
    return $this->doAction($context
                            ->setParameter('set:id', (integer) $id));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * List Test Set's in a Specific/Session Project
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \Set[] Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function doListInProjectAction($context) {
    return \Set::listInProject($context->getParameter('project'));
  }

  /**
   * Count the Number of Test Set's in a Specific/Session Project
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return integer Number of Test Set's in the Project
   * @throws \Exception On any type of failure condition
   */
  protected function doCountInProjectAction($context) {
    return \Set::countInProject($context->getParameter('project'));
    ;
  }

  /**
   * Add a Test to a Test Set, optionally, at a specific sequence
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \SetTest Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkAddAction($context) {
    return \SetTest::addRelation(
                    $context->getParameter('set'), $context->getParameter('test'), $context->getParameter('sequence')
    );
  }

  /**
   * Remove the Relation Test Set<-->Test defined by the Test or Test Sequence.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \SetTest Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkRemoveAction($context) {
    if (isset($context->getParameter('test'))) {
      return \SetTest::deleteRelation($context->getParameter('set'), $context->getParameter('test'));
    } else if (isset($context->getParameter('sequence'))) {
      return \SetTest::deleteBySequence($context->getParameter('set'), $context->getParameter('sequence'));
    }
    throw new \Exception('Missing Required Action Parameters.', 1);
  }

  /**
   * Re-sequence the Relations Test Set<-->Test.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return integer Number of Relations Affected by the Change
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkRenumberAction($context) {
    return \SetTest::renumberSequence($context->getParameter('set'), $context->getParameter('step'));
  }

  /**
   * Re-position the Relation Test Set<-->Test defined by Test Sequence.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \SetTest Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkMoveAction($context) {
    return \SetTest::renumberSequence($context->getParameter('set'), $context->getParameter('sequence'), $context->getParameter('to'));
  }

  /**
   * List the Test Set<-->Test Relations for a Specific Set.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \SetTest[] Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkListAction($context) {
    return \SetTest::listInSet($context->getParameter('set'));
  }

  /**
   * Count the Number of Test Set<-->Test for a Specific Set.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return integer Number of Relations
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkCountAction($context) {
    return \SetTest::countInSet($context->getParameter('set'));
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
    // TODO: All Actions in This Controller are Limited to Working in a Specific Project 
    // Do Context Checks
    return $this->onActionDo($context, array('Read', 'Update', 'Delete'), function($controller, $context, $action) {
              // Get the Context Project and Test Set
              $project = $context->getParameter('project');
              $testset = $context->getParameter('entity');

              // Does the Test Set Belong to the Project?
              if ($testset->project !== $project->id) {
                throw new \Exception("Test Set [{$testset->name}] is Not Part of the Project[{$project->name}]", 1);
              }

              return null;
            });
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

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

    // Process 'project:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'project:id', function($controller, $context, $action, $value) {
      // Did we find the Project with the Given ID?
      $project = \Project::findFirst($value);
      if ($project === FALSE) { // NO
        throw new \Exception("Project [$value] not found", 1);
      }

      return $context->setParameter('project', $project);
    }, null, array('Create', 'ListInProject', 'CountInProject'), function($controller, $context, $action) {
      // Missing Project ID, so use the current Session Project
      $controller->checkProject();

      // Does the Session Project exist?
      $id = $this->sessionManager->getProject();
      $project = \Project::findFirst($id);
      if ($project === FALSE) { // NO
        throw new \Exception("Session Project [$id] is invalid.", 2);
      }

      // Get the Current Session Organization
      return $context->setParameter('project', $project);
    });

    // Process 'container:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'test:id', function($controller, $context, $action, $value) {
      // Did we find the Container with the Given ID?
      $test = \Test::findFirst($value);
      if ($test === FALSE) { // NO
        throw new \Exception("Test [$value] not found", 3);
      }

      return $context->setParameter('test', $test);
    }, null, array('LinkAdd', 'LinkRemove'));

    // TODO: Implement Container for Test Sets
    // Process 'container:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'container:id', function($controller, $context, $action, $value) {
      // Did we find the Container with the Given ID?
      $container = \Container::findFirst($value);
      if ($container === FALSE) { // NO
        throw new \Exception("Container [$value] not found", 3);
      }

      return $context->setParameter('container', $container);
    }, null, array('Create'));

    // Process 'set:name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'set:name', function($controller, $context, $action, $value) {

      // Try to Find the Test Set by Name
      $project = $context->getParameter('project');
      $set = \Set::findInProject($project, $value);

      if ($action === 'Create') {
        // Did we find an existing test set with the same name?
        if ($set !== FALSE) { // YES
          throw new \Exception("Test Set [$name] already exists.", 4);
        }
      } else {
        // Did we find an existing test set?
        if ($set === FALSE) { // NO
          throw new \Exception("Test Set [$value] not found", 5);
        }

        // Save the Test Set for the Action
        $context->setParameter('entity', $set)
                ->setParameter('testset', $set);
      }

      return $context;
    }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'set:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'set:id', function($controller, $context, $action, $value) {
        // Does the Test Set with the given ID exist?
        $set = \Set::findFirst($value);
        if ($set === FALSE) { // NO
          throw new \Exception("Test [{$value}] not found", 6);
        }

        // Save the Test for the Action
        return $context->setParameter('entity', $set)
                        ->setParameter('testset', $set);
      }, null, array('Read', 'Update', 'Delete'));
    }

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 7);
    }

    // Save the User in the Context
    return $context->setParameter('user', $user);
  }

  /**
   * 
   * @param type $parameters
   * @param type $testset
   * @return type
   */
  protected function postActionCreate($parameters, $set) {
    // Add a TestSet Container Entry
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');
    $repository->createContainerEntry($parameters['container'], $set, $set->getName());

    // Flush Changes
    $this->getEntityManager()->flush();

    return $set;
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
        assert('isset($results)');
        $return = $results->toArray();
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
      case 'LinkList':
        // TODO Arrange for an ID for the TestSet <--> Test Link (so it's easier to manage)
        $return = array();
        foreach ($results as $links) {
          $return[] = $links->toArray();
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
   * @return \TestSet An instance of a Test Set Entity, managed by this controller
   */
  protected function createEntity() {
    return new \TestSet();
  }

}
