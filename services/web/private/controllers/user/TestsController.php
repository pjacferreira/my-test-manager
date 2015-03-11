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
   * @return string HTTP Body Response
   */
  public function create($name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('test:name', Strings::nullOnEmpty($name));

    // Call the Function
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

    return $this->doAction($context);
  }

  /**
   * Retrieve the Test with the Given Name.
   * 
   * @param string $name Test's Unique Name
   * @return string HTTP Body Response
   */
  public function readByName($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('test:name', Strings::nullOnEmpty($name));

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
    // Call Action
    return $this->doAction($context->setParameter('test:id', (integer) $id));
  }

  /**
   * List Test Entities in the Database, in the Specified Project, or if not specified,
   * in the Session Project.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Tests
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
   * Number of Test Entities in the Database, in the Specified Project, or if not specified,
   * in the Session Project.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Tests
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

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * List Test Entities that are Part of a Project in the Database 
   * based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  Phalcon\Mvc\Model\Resultset\Simple Result Set containing List of Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListInProjectAction($parameters) {
    $repository = $this->getRepository();

    return $repository->listTests($parameters['_filter']['project']);
  }

  /**
   * Count Test Entities that are Part of an Project in the Database 
   * based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Entities Matching the Action Context
   * @throws \Exception On failure to perform the action
   */
  protected function doCountInProjectAction($parameters) {
    $repository = $this->getRepository();

    return $repository->countTests($parameters['_filter']['project']);
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

  /**
   * Perform checks the Context for the Action Before it is called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function contextChecks($context) {
    // Do Context Checks
    return $this->onActionDo($context, array('Read', 'Update', 'Delete'), function($controller, $context, $action) {
              // Get the Context Project and Test
              $project = $context->getParameter('project');
              $test = $context->getParameter('entity');

              // Does the Test Belong to the Project?
              if ($test->project !== $project->id) {
                throw new \Exception("Test[{$test->name}] is Not Part of the Project[{$project->name}]", 1);
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

    // TODO Verify that the test belongs to the current active project
    // Get the Test
    $test = $context->getParameter('entity');

    // Unlink all Relations tot the Test
    $this->getRepository()->removeRelations($test);
    return $parameters;
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

    // TODO: Implement Container for Tests
    // Process 'container:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'container:id', function($controller, $context, $action, $value) {
      // Did we find the Container with the Given ID?
      $container = \Container::findFirst($value);
      if ($container === FALSE) { // NO
        throw new \Exception("Container [$value] not found", 3);
      }

      return $context->setParameter('container', $container);
    }, null, array('Create'));

    /* TODO (TD-6) If the id argument, is used, for READ/UPDATE/DELETE Actions, 
     * the container parameter, is not required (optimization), or should be 
     * the container in which the test exists.
     */

    // Process 'test:name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'test:name', function($controller, $context, $action, $value) {

      // Try to Find the Test by Name
      $project = $context->getParameter('project');
      $test = \Test::findFirstByName($project, $value);

      if ($action === 'Create') {
        // Did we find an existing test with the same name?
        if ($test !== FALSE) { // YES
          throw new \Exception("Test [$name] already exists.", 5);
        }
      } else {
        // Did we find an existing test?
        if ($test === FALSE) { // NO
          throw new \Exception("Test [$value] not found", 6);
        }

        // Save the Test for the Action
        $context->setParameter('entity', $test)
                ->setParameter('test', $test);
      }

      return $context;
    }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'test:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'test:id', function($controller, $context, $action, $value) {
        // Does the Test with the given ID exist?
        $test = \Test::findFirst($value);
        if ($test === FALSE) { // NO
          throw new \Exception("Test [{$value}] not found", 7);
        }

        // Save the Test for the Action
        return $context->setParameter('entity', $test)
                        ->setParameter('test', $test);
      }, null, array('Read', 'Update', 'Delete'));
    }

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 6);
    }

    // Save the User in the Context
    return $context->setParameter('user', $user);
  }

  /**
   * Perform any required setup, before the Action Handler is Called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Test
    $test = $context->getParameter('entity');

    // Create Document Root Container for Test
    $container = new \Container();
    $container->name = "DOCROOT Test [{$test->id}]";
    $container->owner = $test->id;
    // TODO Set Correct Type for TEST
    $container->owner_type = 0;
    $container->save();

    /* TODO: Implement the Correct Container Structure
     * Problem: 
     * 1. In the current structure, we have a CIRCULAR link (i.e. we have the 
     * container, which references the owning object, in this case the test,
     * and the test which references the container:
     * Put more explicitly we have 2 fields, that form the circular link
     * test:container  -> container:id
     * container:owner -> test:id
     * 
     * The problem is that, when we are creating a new Test, we have to do the
     * following steps:
     * 1. Create Test (Set Properties)
     * 2. Save Test (So that we can obtain the Test ID).
     * 3. Create Container (Set Properties, including owner).
     * 4. Save Container (So that we can obtain the Container ID).
     * 5. Update Previous Test (set test:container -> container:id).
     * 6. Save the Test (AGAIN)
     * 
     * POSSIBLE SOLUTION 1:
     * Implement an intermediate table that links owning objects to their
     * respective root container:
     * 
     * Example:
     * LINK TABLE:
     * - ID : Unqique Entry ID
     * - OWNER: ID (of Owning Object)
     * - OWNER: TYPE (of Object That Owns the Container)
     * - CONTAINER: ID (of Container)
     * 
     * In this scenario, we need to do steps 1-4, but step 5-6 become.
     * 5. Create Link Table Entry (Set Properties OWNER:OWNER TYPE:CONTAINER-
     * 6. Save Link Table Entry
     * 
     * But this also implies that we need a seperate TABLE for Container Entries,
     * for such things as Documents (Associated with a Test) or If we want
     * to Allow Tests to be grouped (in a folder like structure in a project).
     * 
     * POSSIBLE SOLUTION 2:
     * Maintain things, as they are, and create a single table with a more 
     * flexible structure:
     * Example:
     * t_container_entries
     * -------------------------------------------------------------------------
     * BASE      | ID: AUTO_INCREMENT (Container Entry ID)
     * ELEMENTS  | NAME: Container Entry Name (POSSIBLE NULL)
     *           | DESCRIPTION: Container Entry Description (POSSIBLE NULL)
     * -------------------------------------------------------------------------
     * ROOT      | OWNER ID:  ID of OWNING ENTITY (POSSIBLE NULL)
     * NODE      | OWNER TYPE: TYPE of OWNING ENTITY (PROJECT/TEST/etc.) (POSSIBLE NULL)
     * -------------------------------------------------------------------------
     *           | PARENT ID : ID of PARENT CONTAINER ENTRY (POSSIBLE NULL)
     * CHILD     | LINK TYPE : If a Link to an Entity or External Document (Specify TYPE)
     * NODE      | LINK ID   : If Link to Entity (This is the LINKED ENTITIES ID)
     *           | LINK CODE : If Link to Document (This is the MD5 HASH of the Document)
     * -------------------------------------------------------------------------
     * 
     * 
     * Example Set of Container Entries for a File (F1) Stored in a Folder (D1)
     * of a Test (T1)
     * 
     * ROOT ENTRY (R1,NULL,NULL,T1,TEST,NULL,NULL,NULL,NULL)
     * FOLDER ENTRY (D1,Folder Name,Folder Description,NULL,NULL,R1,NULL,NULL,NULL)
     * FILE ENTRY (F1,Real File Name,File Description,NULL,NULL,D1,FILE,NULL,MD5 HASH OF FILE)
     * 
     * Example Set of Container Entries for a Test (T1) in a Group Folder (G1) of
     * a Project (P1)
     * ROOT ENTRY (R1,NULL,NULL,P1,PROJECT,NULL,NULL,NULL,NULL)
     * GROUP ENTRY (G1,Folder Name,Folder Description,NULL,NULL,R1,NULL,NULL,NULL)
     * TEST ENTRY (IDt,TEST NAME,NULL,NULL,NULL,G1,TEST,T1,NULL)
     */
    // Associate Container with Test
    $test->container = $container->id;

    // Add a Test Container Entry
    $repository->createContainerEntry($parameters['container'], $test, $test->getName());

    // Flush Changes
    $this->getEntityManager()->flush();

    return $test;
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
      case 'ListInProject':
        $return = array();
        foreach ($results as $test) {
          $id = $test->getId();
          $return[$id] = $test->toArray();
          unset($return[$id]['id']);
        }
        break;
      case 'ListInContainer':
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
   * @return \Test An instance of a Test Entity, managed by this controller
   */
  protected function createEntity() {
    return new \Test();
  }

}
