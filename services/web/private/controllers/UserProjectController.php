<?php

/*
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
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

use shared\controller\ActionContext;
use shared\utility\StringUtilities;
use shared\controller\BaseServiceController;

/**
 * Controller used to Manage Links between the User<-->Project Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class UserProjectController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Link the Specified User to the Specified Project with the Specified
   * Access.
   * 
   * @param integer $user_id Users's ID
   * @param integer $project_id Project's ID
   * @param integer $permissions OPTIONAL Permissions (If not Specified then
   *   read-only permission will be set)
   * @return string HTTP Body Response
   */
  public function link($user_id, $project_id, $permissions = null) {
    // Create Action Context
    $context = new ActionContext('link');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id)->
                            setParameter('project:id', (integer) $project_id)->
                            setIfNotNull('permissions', StringUtilities::nullOnEmpty($permissions)));
  }

  /**
   * Link the Specified User to the Session Project with the Specified
   * Access.
   * 
   * @param integer $user_id Users's ID
   * @param integer $permissions OPTIONAL Permissions (If not Specified then
   *   read-only permission will be set)
   * @return string HTTP Body Response
   */
  public function linkUser($user_id, $permissions = null) {
    // Create Action Context
    $context = new ActionContext('link');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id)->
                            setIfNotNull('permissions', StringUtilities::nullOnEmpty($permissions)));
  }

  /**
   * Unlink the Specified User from the Specified Project.
   * 
   * @param integer $user_id Users's ID
   * @param integer $project_id Project's ID
   * @return string HTTP Body Response
   */
  public function unlink($user_id, $project_id) {
    // Create Action Context
    $context = new ActionContext('unlink');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id)->
                            setParameter('project:id', (integer) $project_id)
    );
  }

  /**
   * Unlink the Specified User from the Session Project.
   * 
   * @param integer $user_id Users's ID
   * @return string HTTP Body Response
   */
  public function unlinkUser($user_id) {
    // Create Action Context
    $context = new ActionContext('unlink');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id));
  }

  /**
   * Get the Specified User's Permission relative to the Specified Project.
   * 
   * @param integer $user_id Users's ID
   * @param integer $project_id Project's ID
   * @return string HTTP Body Response
   */
  public function get($user_id, $project_id) {
    // Create Action Context
    $context = new ActionContext('get');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id)->
                            setParameter('project:id', (integer) $project_id)
    );
  }

  /**
   * Get the Specified User's Permission relative to the Session Project.
   * 
   * @param integer $user_id Users's ID
   * @return string HTTP Body Response
   */
  public function getUser($user_id) {
    // Create Action Context
    $context = new ActionContext('get');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id));
  }

  /**
   * Alias for 'link' action.
   * 
   * @param integer $user_id Users's ID
   * @param integer $project_id Project's ID
   * @param integer $permissions New Permissions
   * @return string HTTP Body Response
   */
  public function set($user_id, $project_id, $permissions) {
    // Create Action Context
    $context = new ActionContext('link');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id)->
                            setParameter('project:id', (integer) $project_id)->
                            setIfNotNull('permissions', StringUtilities::nullOnEmpty($permissions)));
  }

  /**
   * Alias for 'linkUser' action.
   * 
   * @param integer $user_id Users's ID
   * @param integer $permissions New Permissions
   * @return string HTTP Body Response
   */
  public function setUser($user_id, $permissions) {
    // Create Action Context
    $context = new ActionContext('link');

    return $this->doAction($context->
                            setParameter('user:id', (integer) $user_id)->
                            setIfNotNull('permissions', StringUtilities::nullOnEmpty($permissions)));
  }

  /**
   * List of Projects linked to the Specified/Session User.
   * 
   * @param integer $user_id OPTIONAL Users's ID (IF not given Session User is Used)
   * @return string HTTP Body Response
   */
  public function listProjects($user_id = null) {
    // Create Action Context
    $context = new ActionContext('list_projects');

    // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
    $user_id = StringUtilities::nullOnEmpty($user_id);
    
    return $this->doAction($context->
                            setIfNotNull('user:id', isset($user_id) ? (integer) $user_id : null));
  }

  /**
   * List of Projects and Permissions linked to the Specified/Session User.
   * 
   * @param integer $user_id OPTIONAL Users's ID (IF not given Session User is Used)
   * @return string HTTP Body Response
   */
  public function listProjectsPermissions($user_id = null) {
    // Create Action Context
    $context = new ActionContext('list_projects_permissions');

    // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
    $user_id = StringUtilities::nullOnEmpty($user_id);

    return $this->doAction($context
                            ->setIfNotNull('user:id', isset($user_id) ? (integer) $user_id : null));
  }

  /**
   * Count of Projects linked to the Specified/Session User.
   * 
   * @param integer $user_id OPTIONAL Users's ID (IF not given Session User is Used)
   * @return string HTTP Body Response
   */
  public function countProjects($user_id = null) {
    // Create Action Context
    $context = new ActionContext('count_projects');

    // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
    $user_id = StringUtilities::nullOnEmpty($user_id);
    
    return $this->doAction($context->
                            setIfNotNull('user:id', isset($user_id) ? (integer) $user_id : null));
  }

  /**
   * List of Users linked to the Specified/Session Project.
   * 
   * @param integer $project_id OPTIONAL Project ID (if not given Session 
   *   Project is Used)
   * @return string HTTP Body Response
   */
  public function listUsers($project_id = null) {
    // Create Action Context
    $context = new ActionContext('list_users');

    // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
    $project_id = StringUtilities::nullOnEmpty($project_id);
    
    return $this->doAction($context->
                            setIfNotNull('project:id', isset($project_id) ? (integer) $project_id : null));
  }

  /**
   * Count of Users linked to the Specified/Session Project.
   * 
   * @param integer $project_id OPTIONAL Project ID (if not given Session 
   *   Project is Used)
   * @return string HTTP Body Response
   */
  public function countUsers($project_id = null) {
    // Create Action Context
    $context = new ActionContext('count_users');

    // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
    $project_id = StringUtilities::nullOnEmpty($project_id);
    
    return $this->doAction($context->
                            setIfNotNull('project:id', isset($project_id) ? (integer) $project_id : null));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Link the User to the Project with the Given Permissions
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \UserProject Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkAction($context) {
    return \UserProject::addRelation(
                    $context->getParameter('user'), $context->getParameter('project'), $context->getParameter('permissions')
    );
  }

  /**
   * Unlink the User from the Project.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \UserProject Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doUnlinkAction($context) {
    $user = $context->getParameter('user');
    $project = $context->getParameter('project');

    // Does the User Have Access to the Project?
    $link = \UserProject::deleteRelation($user, $project);
    if (!isset($link)) { // NO
      throw new \Exception("User [{$user->name}] does not have access to Project [{$project->name}].", 1);
    }
    return $link;
  }

  /**
   * List the Users with Access to the Project.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \UserProject[] Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doListUsersAction($context) {
    return \UserProject::listUsers($context->getParameter('project'));
  }

  /**
   * Count the Number of Users with Access to the Project.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return integer Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doCountUsersAction($context) {
    return \UserProject::countUsers($context->getParameter('project'));
  }

  /**
   * List the Projects the User has Access To.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \UserOrganization[] Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doListProjectsAction($context) {
    return \UserProject::listProjects($context->getParameter('user'));
  }

  /**
   * List the Projects and Permissions the User has Access To.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \UserProject[] Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doListProjectsPermissionsAction($context) {
    return \UserProject::listProjectPermissions($context->getParameter('user'));
  }

  /**
   * Count the Number of Projects the User has Access to.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return integer Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doCountProjectsAction($context) {
    return \UserProject::countProjects($context->getParameter('user'));
  }

  /**
   * Get the Permissions Between the User and the Project.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \UserOrganization Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doGetAction($context) {
    $user = $context->getParameter('user');
    $project = $context->getParameter('project');

    // Does the User Have Access to the Project?
    $link = \UserProject::findRelation($user, $project);
    if (!isset($link)) { // NO
      throw new \Exception("User [{$user->name}] does not have access to Project [{$project->name}].", 1);
    }

    return $link;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform checks that validate the Session State.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
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
   * Perform any required setup, before the Action Handler is Called.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Process 'user:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'user:id', function($controller, $context, $action, $value) {
      // Get User for Action
      $user = \User::findFirst($value);

      // Did we find the user?
      if ($user === FALSE) { // NO
        throw new \Exception("User [$value] not found", 1);
      }

      return $context->setParameter('user', $user);
    }, null, array('Link', 'Unlink', 'Get', 'ListProjects', 'ListProjectsPermissions', 'CountProjects'), function($controller, $context, $action) {
      // Missing User ID, so use the current Session User
      // Get the User for the Active Session
      $session = $controller->getDI()->getShared('sessionManager');
      return $session->getUser();
    });

    // Process 'project:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'project:id', function($controller, $context, $action, $value) {
      // Get Project for Action
      $project = \Project::findFirst($value);

      // Did we find the project?
      if ($project === FALSE) { // NO
        throw new \Exception("Project [$value] not found", 3);
      }

      // Save the Project for the Action
      return $context->setParameter('project', $project);
    }, null, array('Link', 'Unlink', 'Get', 'ListUsers', 'CountUsers'), function($controller, $context, $action) {
      // Missing Project ID, so use the current Session Organization
      $session = $controller->getDI()->getShared('sessionManager');
      $session->checkProject();
      return $session->getProject();
    });

    return $context;
  }

  /**
   * Perform any required setup, before we perform final rendering of the Action's
   * Result.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return mixed Action Output that is to be Rendered
   * @throws \Exception On any type of failure condition
   */
  protected function preRender($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get Results
    $results = $context->getActionResult();

    switch ($context->getAction()) {
      case 'Link':
      case 'Unlink':
      case 'Get':
        $return = isset($results) ? $results->toArray() : null;
        break;
      case 'ListUsers':
        $entities = array();
        $header = true;
        foreach ($results as $user) {
          $entities[] = $user->toArray($header);
          $header = false;
        }
        // Extract Header
        $return = array();
        $this->moveEntityHeader($entities[0], $return);
        $return['__type'] = 'entity-set';
        $return['entities'] = $entities;
        break;
      case 'ListProjects':
        $entities = array();
        $header = true;
        foreach ($results as $project) {
          $entities[] = $project->toArray($header);
          $header = false;
        }
        // Extract Header
        $return = array();
        $this->moveEntityHeader($entities[0], $return);
        $return['__type'] = 'entity-set';
        $return['entities'] = $entities;
        break;
      case 'ListProjectsPermissions':
        $entities = array();
        $up = null;
        $header = true;
        foreach ($results as $row) {
          $up = $row->userProject;
          $up->project = $row->project;
          $entities[] = $up->toArray($header);
          $header = false;
        }
        // Extract Header
        $return = array();
        $this->moveEntityHeader($entities[0], $return);
        $return['__type'] = 'entity-set';
        $return['entities'] = $entities;
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /*
   * ---------------------------------------------------------------------------
   * BASIC CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * Verify if the Specified User has Access to the Specified Project.
   * 
   * @param \User $user User Entity
   * @param \Project $project Project Entity
   * @param string $required OPTIONAL Required Permissions (in order to pass),
   *   if not specified (ANY Permission will be accepted)
   * @throws \Exception If User DOES NOT Have Access 
   */
  public function checkProjectAccess($user, $project, $required = null) {
    // TODO Implement Actual Permissions Check (not just link exists)
    $organization = $project->organization;

    // Check Organization Access
    UserOrganizationController::checkOrganizationAccess($user, $organization, $required);

    // Are the user and Project Linked?
    $link = \UserProject::findRelation($user, $project);
    if (!isset($link)) { // NO
      throw new \Exception("User [{$user->name}] does not have access to Project [{$project->name}].", 1);
    }

    return true;
  }

}
