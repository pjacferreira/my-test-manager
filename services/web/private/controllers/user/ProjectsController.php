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
 * Controller used to Manage Project Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ProjectsController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create an Project (if it doesn't already exist) within the Current Session
   * Organization and with the Given Name.
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   * 
   * @param string $name Project name
   * @return string HTTP Body Response
   */
  public function create($name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('project:name', Strings::nullOnEmpty($name));

    // Call the Function
    return $this->doAction($context);
  }

  /**
   * Create an Project (if it doesn't already exist) within the Organization
   * specified and with the Given Name.
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   * 
   * @param integer $org_id Organization's Unique Identifier
   * @param string $name Project name
   * @return string HTTP Body Response
   */
  public function createInOrg($org_id, $name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setParameter('organization:id', (integer) $org_id)
            ->setIfNotNull('project:name', Strings::nullOnEmpty($name));

    // Call the Function
    return $this->doAction($context);
  }

  /**
   * Retrieve the Project with the Given ID.
   * 
   * @param integer $id Project's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setParameter('project:id', (integer) $id);

    return $this->doAction($context);
  }

  /**
   * Retrieve the Project with the Given Name.
   * 
   * @param string $name Project's Unique Name
   * @return string HTTP Body Response
   */
  public function readByName($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('project:name', Strings::nullOnEmpty($name));

    return $this->doAction($context);
  }

  /**
   * Update the Project, with the Given ID, information.
   * 
   * @param integer $id Project's Unique Identifier
   * @return string HTTP Body Response
   */
  public function update($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setParameter('project:id', (integer) $id);

    return $this->doAction($context);
  }

  /**
   * Update the Project, with the Given Name, information.
   * 
   * @param string $name Project's Unique Name
   * @return string HTTP Body Response
   */
  public function updateByName($name) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
            ->setIfNotNull('project:name', Strings::nullOnEmpty($name));

    return $this->doAction($context);
  }

  /**
   * Delete the Project with the Given ID.
   * 
   * @param integer $id Project's Unique Identifier
   * @return string HTTP Body Response
   */
  public function delete($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setParameter('project:id', (integer) $id));
  }

  /**
   * Delete the Project with the Given Name.
   * 
   * @param string $name Project's Unique Name
   * @return string HTTP Body Response
   */
  public function deleteByName($name) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setIfNotNull('project:name', Strings::nullOnEmpty($name)));
  }

  /**
   * List Project Entities in the Database.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Projects
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @return string HTTP Body Response
   */
  public function listProjects() {
    // Create Action Context
    $context = new ActionContext('list');

    return $this->doAction($context);
  }

  /**
   * Count the Number of Project Entities in the Database.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Projects
   * 
   * @return integer Number of Organizations
   */
  public function countProjects() {
    // Create Action Context
    $context = new ActionContext('count');

    return $this->doAction($context);
  }

  /**
   * List Project Entities in the Database, in the Specified Organization, or if not specified,
   * in the Session Organization.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Projects
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @param integer $org_id OPTIONAL Organization's Unique Identifier
   * @return string HTTP Body Response
   */
  public function listInOrganization($org_id = null) {
    /* Allowing a NULL org_id is a win-win situation
     * 1. Allows for the scenario that the service is listing the project for the current session organization
     */

    // Create Action Context
    $context = new ActionContext('list_in_organization');
    // Build Parameters
    $context = $context
            ->setIfNotNull('organization:id', isset($org_id) ? (integer) $org_id : null);

    return $this->doAction($context);
  }

  /**
   * Number of Project Entities in the Database, in the Specified Organization, or if not specified,
   * in the Session Organization.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Projects
   * 
   * @param integer $org_id OPTIONAL Organization's Unique Identifier
   * @return integer Number of Projects
   */
  public function countInOrganization($org_id = null) {
    // Create Action Context
    $context = new ActionContext('count_in_organization');
    // Build Parameters
    $context = $context
            ->setIfNotNull('organization:id', isset($org_id) ? (integer) $org_id : null);

    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * List Projects Entities that are Part of an Organization in the Database 
   * based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  Phalcon\Mvc\Model\Resultset\Simple Result Set containing List of Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListInOrganizationAction($context) {
    // Get the Organization
    $organization = $context->getParameter('organization');

    if (isset($organization)) {
      // Modify the Filter for the Action
      $__filter = $context->getParameter('__filter');
      if (isset($__filter)) {
        $__filter = "project.organization:={$organization->getId()};{$__filter}";
      } else {
        $__filter = "project.organization:={$organization->getId()}";
      }
      $context->setParameter('__filter', $__filter);
    }

    return $this->doListAction($context);
  }

  /**
   * Count Projects Entities that are Part of an Organization in the Database 
   * based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Entities Matching the Action Context
   * @throws \Exception On failure to perform the action
   */
  protected function doCountInOrganizationAction($context) {
    // Get the Organization
    $organization = $context->getParameter('organization');

    if (isset($organization)) {
      // Modify the Filter for the Action
      $__filter = $context->getParameter('__filter');
      if (isset($__filter)) {
        $__filter = "project.organization:={$organization->getId()};{$__filter}";
      } else {
        $__filter = "project.organization:={$organization->getId()}";
      }
      $context->setParameter('__filter', $__filter);
    }

    return $this->doCountAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: STAGE : INIT ACTION
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
   * BaseController: STAGE : PRE-ACTION
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
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Call the General Handler 1st (to Setup Context)
    $context = $this->preAction($context);
    $project = $context->getParameter('entity');

    // Unlink all users from the Project
    \models\UserProject::deleteRelationsProject($project);

    // TODO Remove Project Container

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

    // Process 'organization:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'organization:id', function($controller, $context, $action, $value) {
      // Did we find the Organization with the Given ID?
      $org = \models\Organization::findFirst($value);
      if ($org === FALSE) { // NO
        throw new \Exception("Organization [$value] not found", 1);
      }

      return $context->setParameter('organization', $org);
    }, null, array('Create', 'Read', 'Update', 'Delete', 'ListOrganization', 'CountOrganization'), function($controller, $context, $action) {
      // Missing Organization ID, so use the current Session Organization
      $session = $controller->getDI()->getShared('sessionManager');
      $session->checkOrganization();
      return $session->getOrganization();
    });

    // Process 'project:name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'project:name', function($controller, $context, $action, $value) {

      // Try to Find the Project by Name
      $project = \models\Project::findFirstByName($value);

      if ($action === 'Create') {
        // Did we find an existing project with the same name?
        if ($project !== FALSE) { // YES
          throw new \Exception("Project [$name] already exists.", 3);
        }
      } else {
        // Did we find an existing project?
        if ($project === FALSE) { // NO
          throw new \Exception("Project [$value] not found", 4);
        }

        // Save the Project for the Action
        $context->setParameter('entity', $project)
                ->setParameter('project', $project);
      }

      return $context;
    }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'project:id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'project:id', function($controller, $context, $action, $value) {
        // Does the Project with the given ID exist?
        $project = \models\Project::findFirst($value);
        if ($project === FALSE) { // NO
          throw new \Exception("Project [{$value}] not found", 5);
        }

        // Save the Project for the Action
        return $context->setParameter('entity', $project)
                        ->setParameter('project', $project);
      }, null, array('Read', 'Update', 'Delete'));
    }

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \models\User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 6);
    }

    // Save the User in the Context
    return $context->setParameter('user', $user)
                    ->setParameter('cm_user', $user);
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: STAGE : DO CALL
   * ---------------------------------------------------------------------------
   */

  /**
   * Check if the user has the required permissions to perform the action.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  public function priviledgeChecks($context) {
    // Do Access Checks
    return $this->onActionDo($context, array('Read', 'Create', 'Update', 'Delete'), function($controller, $context, $action) {
              // Required Parameters
              $user = $context->getParameter('user');
              assert('isset($user)');

              // Other Parameters
              $project = $context->getParameter('project');
              $organization = $context->getParameter('organization');

              switch ($action) {
                case 'Create':
                  assert('isset($organization)');
                  $controller->checkOrganizationAccess($user, $organization);
                  break;
                case 'Delete': // User Only Requires Access to Organization
                  assert('isset($project)');
                  $controller->checkOrganizationAccess($user, $project->getOrganization());
                  break;
                default: // Check if User Has Access to Project (and by consequence to the Organization)
                  assert('isset($project)');
                  $controller->checkProjectAccess($user, $project);
              }

              return null;
            });
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
              // Get the Context Project and Organization
              $organization = $context->getParameter('organization');
              $project = $context->getParameter('entity');

              // Does the Project Belong to the Organization?
              if ($project->organization !== $organization->id) {
                throw new \Exception("Test Set [{$project->name}] is Not Part of the Organization[{$organization->name}]", 1);
              }

              return null;
            });
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGE : POST-ACTION
   * ---------------------------------------------------------------------------
   */

  /**
   * @param $parameters
   * @param $project
   * @return mixed
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Project that was Previously Created
    $project = $context->getActionResult();
    assert('isset($project)');

    // Save the Project for the Action
    $context->setParameter('entity', $project);
    $context->setParameter('project', $project);

    /* TODO
      // Create the Container for the Project
      $container = \Container::addContainer("ROOT PROJECT[{$project->id}]", $project->id, 0);

      // Assign Container to Project
      $project->container = $container;
     */

    // Get the User Associated with the Organization
    $user = $context->getParameter('user');
    assert('isset($user)');

    // Link the New Organization to the Current User (READ-ONLY)
    \models\UserProject::addRelation($user, $project);

    // No change to the context
    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGE : RENDER
   * ---------------------------------------------------------------------------
   */

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
      case 'ListInOrganization':
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
   * HELPER FUNCTIONS
   * ---------------------------------------------------------------------------
   */

  /**
   * @param $user
   * @param $organization
   * @param null $required
   * @return bool
   * @throws \Exception
   */
  public function checkOrganizationAccess($user, $organization, $required = null) {
    // TODO Implement Actual Permissions Check (not just link exists)
    // Get Link Between User and Project
    $link = \models\UserOrganization::findRelation($user, $organization);
    if (!isset($link)) {
      throw new \Exception("User [{$user->name}] does not have access to Organization [{$organization->name}].", 1);
    }

    return true;
  }

  /**
   * @param $user
   * @param $project
   * @param null $required
   * @return bool
   * @throws \Exception
   */
  public function checkProjectAccess($user, $project, $required = null) {
    // TODO Implement Actual Permissions Check (not just link exists)
    $organization = $project->getOrganization();
    $this->checkOrganizationAccess($user, $organization);

    // Get Link Between User and Project
    $link = \models\UserProject::findRelation($user, $project);
    if (!isset($link)) {
      throw new \Exception("User [{$user->name}] does not have access to Project [{$project->name}].", 1);
    }

    return true;
  }

  /*
   * ---------------------------------------------------------------------------
   * OVERRIDE : EntityServiceController
   * ---------------------------------------------------------------------------
   */

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \models\Project An instance of a Project Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\Project();
  }

}
