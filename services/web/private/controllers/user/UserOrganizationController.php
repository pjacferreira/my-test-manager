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
 * Controller used to Manage Links between the User<-->Organization Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class UserOrganizationController extends BaseServiceController {
	/*
	 * ---------------------------------------------------------------------------
	 *  CONTROLLER: Action Entry Points
	 * ---------------------------------------------------------------------------
	 */

	/**
	 * Get the Specified User's Permission relative to the Specified Organization.
	 *
	 * @param integer $org_id [DEFAULT null = Session Organization] Organization's ID
	 * @return string HTTP Body Response
	 */
	public function get($org_id = null) {
		// Create Action Context
		$context = new ActionContext('get');

		// PHALCON Treats Missing/Optional Parameters as EMPTY STRINGs
		$org_id = Strings::nullOnEmpty($org_id);

		return $this->doAction($context
				->setParameter('organization:id', isset($org_id) ? (integer) $org_id : null));
	}

	/**
	 * List of Organizations linked to the Session User.
	 *
	 * @return string HTTP Body Response
	 */
	public function listOrganizations() {
		// Create Action Context
		$context = new ActionContext('list_orgs');

		return $this->doAction($context);
	}

	/**
	 * List of Organization and Permissions linked to the Session User.
	 *
	 * @param integer $user_id OPTIONAL Users's ID (IF not given Session User is Used)
	 * @return string HTTP Body Response
	 */
	public function listOrganizationPermissions() {
		// Create Action Context
		$context = new ActionContext('list_orgs_permissions');

		return $this->doAction($context);
	}

	/**
	 * Count of Organizations linked to the Session User.
	 *
	 * @return string HTTP Body Response
	 */
	public function countOrganizations() {
		// Create Action Context
		$context = new ActionContext('count_orgs');

		return $this->doAction($context);
	}

	/*
	 * ---------------------------------------------------------------------------
	 * CONTROLLER: Internal Action Handlers
	 * ---------------------------------------------------------------------------
	 */

	/**
	 * List the Organizations the User has Access To.
	 *
	 * @param \api\controller\ActionContext $context Incoming Context for Action
	 * @return \models\UserOrganization[] Action Result
	 * @throws \Exception On any type of failure condition
	 */
	protected function doListOrgsAction($context) {
//    $user = $context->getParameter('user');
		//    $uos = $user->getUserOrganization();
		//    return $uos;
		return \models\UserOrganization::listOrganizations($context->getParameter('user'));
	}

	/**
	 * List the Organizations and Permissions the User has Access To.
	 *
	 * @param \api\controller\ActionContext $context Incoming Context for Action
	 * @return \models\UserOrganization[] Action Result
	 * @throws \Exception On any type of failure condition
	 */
	protected function doListOrgsPermissionsAction($context) {
		return \models\UserOrganization::listOrganizationPermissions($context->getParameter('user'));
	}

	/**
	 * Count the Number of Organizations the User has Access to.
	 *
	 * @param \api\controller\ActionContext $context Incoming Context for Action
	 * @return integer Action Result
	 * @throws \Exception On any type of failure condition
	 */
	protected function doCountOrgsAction($context) {
		return \models\UserOrganization::countOrganizations($context->getParameter('user'));
	}

	/**
	 * Get the Permissions Between the User and the Organization.
	 *
	 * @param \api\controller\ActionContext $context Incoming Context for Action
	 * @return \models\UserOrganization Action Result
	 * @throws \Exception On any type of failure condition
	 */
	protected function doGetAction($context) {
		$user = $context->getParameter('user');
		$organization = $context->getParameter('organization');

		// Does the User Have Access to the Organization?
		$link = \models\UserOrganization::findRelation($user, $organization);
		if (!isset($link)) {
			// NO
			throw new \Exception("User [{$user->name}] does not have access to Organization [{$organization->name}].", 1);
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
		// Do Access Checks
		/*
		$context = $this->onActionDo($context, array('Link'),
		function($controller, $context, $action) {
		// Required Parameters
		$user = $context->getParameter('user');
		$organization = $context->getParameter('organization');
		assert('isset($user) && isset($organization)');

		// Check if user has access to Organization
		$controller->checkOrganizationAccess($user, $organization);

		return null;
		});
		 */

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

		// Get User for Action
		$user = $this->sessionManager->getUser();
		$user = \models\User::findFirst($user['id']);

		// Did we find the user?
		if ($user === FALSE) {
			// NO
			throw new \Exception("User [{$user['id']}] not found", 1);
		}
		$context->setParameter('user', $user);

		// Process 'organization:id' Parameter (if it exists)
		$context = $this->onParameterDo($context, 'organization:id', function ($controller, $context, $action, $value) {
			// Get Organization for Action
			$org = \models\Organization::findFirst($value);

			// Did we find the organization?
			if ($org === FALSE) {
				// NO
				throw new \Exception("User [$value] not found", 1);
			}

			// Save the Organization for the Action
			return $context->setParameter('organization', $org);
		}, null, array('Get'), function ($controller, $context, $action) {
			// Missing Organization ID, so use the current Session Organization
			$session = $controller->getDI()->getShared('sessionManager');
			$session->checkOrganization();
			return $session->getOrganization()['id'];
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
	protected function preRender($context) {
		// Parameter Validation
		assert('isset($context) && is_object($context)');

		// Get Results
		$results = $context->getActionResult();

		// Get the Action Name
		switch ($context->getAction()) {
			case 'Get':
				$return = isset($results) ? $results->toArray() : null;
				break;
			case 'ListOrgs':
				$entities = array();
				$header = true;
				foreach ($results as $org) {
					$entities[] = $org->toArray($header);
					$header = false;
				}
				// Extract Header
				$return = array();
				$this->moveEntityHeader($entities[0], $return);
				$return['__type'] = 'entity-set';
				$return['entities'] = $entities;
				break;
			case 'ListOrgsPermissions':
				$entities = array();
				$uo = null;
				$header = true;
				foreach ($results as $row) {
					$uo = $row->userOrganization;
					$uo->organization = $row->organization;
					$entities[] = $uo->toArray($header);
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
	 * Verify if the Specified User has Access to the Speicifed Organization.
	 *
	 * @param mixed $user User Entity (object) or User ID (integer)
	 * @param midex $organization Organization Entity (object) or Organization ID (integer)
	 * @param string $required OPTIONAL Required Permissions (in order to pass),
	 *   if not specified (ANY Permission will be accepted)
	 * @throws \Exception If User DOES NOT Have Access
	 */
	public static function checkOrganizationAccess($user, $organization, $required = null) {
		// TODO Implement Actual Permissions Check (not just link exists)
		// Get Link Between User and Organization
		$link = \models\UserOrganization::findRelation($user, $organization);

		// Are the user and Organization Linked?
		if (!isset($link)) {
			// NO
			throw new \Exception("User [{$user->name}] does not have access to Organization [{$organization->name}].", 1);
		}
	}

}
