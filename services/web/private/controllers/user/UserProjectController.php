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
use api\controller\BaseServiceController;
use common\utility\Strings;

/**
 * Controller used to Manage Links between the User<-->Project Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class UserProjectController extends BaseServiceController {
    /*
     * ---------------------------------------------------------------------------
     *  CONTROLLER: Action Entry Points
     * ---------------------------------------------------------------------------
     */

    /**
     * Get the Specified User's Permission relative to the Specified Project.
     *
     * @param integer $user_id Users's ID
     * @param integer $project_id Project's ID
     * @return string HTTP Body Response
     */
    public function get($project_id = null)
    {
        // Create Action Context
        $context = new ActionContext('get');

        // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
        $project_id = Strings::nullOnEmpty($project_id);

        return $this->doAction($context
                ->setParameter('project:id', isset($project_id) ? (integer) $project_id : null)
        );
    }

    /**
     * List of All Projects linked to the Session User.
     *
     * @param integer $org_id [DEFAULT NULL all projects] Organization ID to list Projects For
     * @return string HTTP Body Response
     */
    public function listUserProjects()
    {
        // Create Action Context
        $context = new ActionContext('list_projects');

        // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
        $org_id = Strings::nullOnEmpty($org_id);

        return $this->doAction($context
                ->setParameter('limit_org', false));
    }

    /**
     * List of Projects linked to the Session User / [Session] Organization.
     *
     * @param integer $org_id [DEFAULT NULL all projects] Organization ID to list Projects For
     * @return string HTTP Body Response
     */
    public function listProjects($org_id = null)
    {
        // Create Action Context
        $context = new ActionContext('list_projects');

        // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
        $org_id = Strings::nullOnEmpty($org_id);

        return $this->doAction($context
                ->setParameter('limit_org', true)
                ->setIfNotNull('organization:id', isset($org_id) ? (integer) $org_id : null));
    }

    /**
     * List of Projects and Permissions linked to the Session User.
     *
     * @param integer $org_id [DEFAULT NULL all projects] Organization ID to list Projects For
     * @return string HTTP Body Response
     */
    public function listProjectsPermissions($org_id = null)
    {
        // Create Action Context
        $context = new ActionContext('list_projects_permissions');

        // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
        $org_id = Strings::nullOnEmpty($org_id);

        return $this->doAction($context->
            setIfNotNull('organization:id', isset($org_id) ? (integer) $org_id : null));
    }

    /**
     * Count of All Projects linked to the Session User.
     *
     * @return string HTTP Body Response
     */
    public function countUserProjects()
    {
        // Create Action Context
        $context = new ActionContext('count_projects');

        // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
        $org_id = Strings::nullOnEmpty($org_id);

        return $this->doAction($context
                ->setParameter('limit_org', false));
    }

    /**
     * Count of Projects linked to the Session User / [Session] Organization.
     *
     * @param integer $org_id [DEFAULT NULL all projects] Organization ID to list Projects For
     * @return string HTTP Body Response
     */
    public function countProjects($org_id = null)
    {
        // Create Action Context
        $context = new ActionContext('count_projects');

        // PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
        $org_id = Strings::nullOnEmpty($org_id);

        return $this->doAction($context
                ->setParameter('limit_org', true)
                ->setIfNotNull('organization:id', isset($org_id) ? (integer) $org_id : null));
    }

    /*
     * ---------------------------------------------------------------------------
     * CONTROLLER: Internal Action Handlers
     * ---------------------------------------------------------------------------
     */

    /**
     * List the Projects the User has Access To.
     *
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return \models\UserOrganization[] Action Result
     * @throws \Exception On any type of failure condition
     */
    protected function doListProjectsAction($context)
    {
        $user = $context->getOneOfParameters(['user:id', 'user']);
        $limit = $context->getParameter('limit_org', false);
        $org = $limit ? $context->getOneOfParameters(['organization:id', 'organization']) : null;
        return \models\UserProject::listProjects($user, $org);
    }

    /**
     * List the Projects and Permissions the User has Access To.
     *
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return \models\UserProject[] Action Result
     * @throws \Exception On any type of failure condition
     */
    protected function doListProjectsPermissionsAction($context)
    {
        $user = $context->getOneOfParameters(['user:id', 'user']);
        $limit = $context->getParameter('limit_org', false);
        $org = $limit ? $context->getOneOfParameters(['organization:id', 'organization']) : null;
        return \models\UserProject::listProjectPermissions($user, $org);
    }

    /**
     * Count the Number of Projects the User has Access to.
     *
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return integer Action Result
     * @throws \Exception On any type of failure condition
     */
    protected function doCountProjectsAction($context)
    {
        $user = $context->getOneOfParameters(['user:id', 'user']);
        $limit = $context->getParameter('limit_org', false);
        $org = $limit ? $context->getOneOfParameters(['organization:id', 'organization']) : null;
        return \models\UserProject::countProjects($user, $org);
    }

    /**
     * Get the Permissions Between the User and the Project.
     *
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return \models\UserOrganization Action Result
     * @throws \Exception On any type of failure condition
     */
    protected function doGetAction($context)
    {
        $user = $context->getParameter('user');
        $project = $context->getParameter('project');

        // Does the User Have Access to the Project?
        $link = \models\UserProject::findRelation($user, $project);
        if (!isset($link)) {
            // NO
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
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return \api\controller\ActionContext Outgoing Context for Action
     * @throws \Exception On any type of failure condition
     */
    protected function sessionChecks($context)
    {
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
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return \api\controller\ActionContext Outgoing Context for Action
     * @throws \Exception On any type of failure condition
     */
    protected function preGetAction($context)
    {
        // Call Base Method
        $context = $this->preAction($context);

        // Do we want the information for a Specific Project?
        $project = $context->getParameter('project:id');
        if (!isset($project)) {
            // NO: Use Session Project
            $this->sessionMananger->checkProject();
            $context->setParameter('project:id', $this->sessionMananger->getProject()['id']);
        }

        return $context;
    }

    /**
     * Perform any required setup, before the Action Handler is Called.
     *
     * @param \api\controller\ActionContext $context Incoming Context for Action
     * @return \api\controller\ActionContext Outgoing Context for Action
     * @throws \Exception On any type of failure condition
     */
    protected function preAction($context)
    {
        // Parameter Validation
        assert('isset($context) && is_object($context)');

        // Get User for Action
        $user = $this->sessionManager->getUser();
        /*
        $user = \models\User::findFirst($user['id']);

        // Did we find the user?
        if ($user === FALSE) {
        // NO
        throw new \Exception("User [{$user['id']}] not found", 1);
        }
         */
        $context->setParameter('user:id', $user['id']);

        $this->onActionDo($context, ['ListProjects', 'CountProjects'], function ($controller, $context, $action) {
            $limit = $context->getParameter('limit_org', false);
            if ($limit) {
                $org = $context->getParameter('organization:id');
                if (!isset($org)) {
                    // Missing Organization ID, so use the current Session Organization
                    $session = $controller->getDI()->getShared('sessionManager');
                    $session->checkOrganization();
                    $context->setParameter('organization:id', $session->getOrganization()['id']);
                }
            }
        });
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
    protected function preRender($context)
    {
        // Parameter Validation
        assert('isset($context) && is_object($context)');

        // Get Results
        $results = $context->getActionResult();

        switch ($context->getAction()) {
            case 'Get':
                $return = isset($results) ? $results->toArray() : null;
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
     * @param \models\User $user User Entity
     * @param \models\Project $project Project Entity
     * @param string $required OPTIONAL Required Permissions (in order to pass),
     *   if not specified (ANY Permission will be accepted)
     * @throws \Exception If User DOES NOT Have Access
     */
    public function checkProjectAccess($user, $project, $required = null)
    {
        // TODO Implement Actual Permissions Check (not just link exists)
        $organization = $project->organization;

        // Check Organization Access
        UserOrganizationController::checkOrganizationAccess($user, $organization, $required);

        // Are the user and Project Linked?
        $link = \models\UserProject::findRelation($user, $project);
        if (!isset($link)) {
            // NO
            throw new \Exception("User [{$user->name}] does not have access to Project [{$project->name}].", 1);
        }

        return true;
    }

}
