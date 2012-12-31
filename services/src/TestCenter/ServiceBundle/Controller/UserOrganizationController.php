<?php

/* Test Center - Compliance Testing Application
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
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

namespace TestCenter\ServiceBundle\Controller;

use Library\StringUtilities;
use Library\ArrayUtilities;
use TestCenter\ServiceBundle\API\ActionContext;
use TestCenter\ServiceBundle\API\EntityWrapper;
use TestCenter\ServiceBundle\API\SessionManager;
use TestCenter\ServiceBundle\API\BaseServiceController;

/**
 * Description of UserOrganizationController
 *
 * @author Paulo Ferreira
 */
class UserOrganizationController
  extends BaseServiceController {

  /* TODO Integrate this as a CRUD Service, by converting all these action
   * to a more standard CRUD actions
   * i.e.
   * link == create
   * get == read
   * set == update
   * unlink == delete
   */ 
  // Entity Managed
  protected $m_oEntity;

  /**
   * @param $entity
   */
  public function __construct() {
    $this->m_oEntity = new EntityWrapper($this, 'TestCenter\ModelBundle\Entity\UserOrganization');
  }

  /**
   * @return mixed
   */
  public function getEntityManager() {
    return $this->m_oEntity->getEntityManager();
  }

  /**
   * @return mixed
   */
  public function getRepository($entity = null) {
    return $this->m_oEntity->getRepository($entity);
  }

  /**
   * @return mixed
   */
  public function getMetadata() {
    return $this->m_oEntity->getMetadata();
  }

  /**
   * @param $user_id
   * @param null $permissions
   * @return null
   * @throws \Exception
   */
  public function userLinkAction($user_id, $permissions = null) {
    // Create Action Context
    $context = new ActionContext('link');

    $context = $context
      ->setParameter('user_id', (integer) $user_id)
      ->setIfNotNull('permissions', StringUtilities::nullOnEmpty($permissions));

    return $this->doAction($context);
  }

  /**
   * @param $user_id
   * @param $org_id
   * @param null $permissions
   * @return null
   */
  public function linkAction($user_id, $org_id, $permissions = null) {
    // Create Action Context
    $context = new ActionContext('link');

    $context = $context
      ->setParameter('user_id', (integer) $user_id)
      ->setParameter('org_id', (integer) $org_id)
      ->setIfNotNull('permissions', StringUtilities::nullOnEmpty($permissions));

    return $this->doAction($context);
  }

  /**
   * @param $user_id
   * @return null
   * @throws \Exception
   */
  public function userUnlinkAction($user_id) {
    // Create Action Context
    $context = new ActionContext('unlink');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @return null
   */
  public function unlinkAction($user_id, $org_id) {
    // Create Action Context
    $context = new ActionContext('unlink');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id)
          ->setParameter('org_id', (integer) $org_id));
  }

  /**
   * @return null
   * @throws \Exception
   */
  public function userListAction() {
    // Create Action Context
    $context = new ActionContext('list_users');

    return $this->doAction($context);
  }

  /**
   * @return null
   * @throws \Exception
   */
  public function userCountAction() {
    // Create Action Context
    $context = new ActionContext('count_users');

    return $this->doAction($context);
  }

  /**
   * @param $user_id
   * @return null
   */
  public function listPerUserAction($user_id) {
    // Create Action Context
    $context = new ActionContext('list_per_user');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id));
  }

  /**
   * @param $user_id
   * @return null
   */
  public function countPerUserAction($user_id) {
    // Create Action Context
    $context = new ActionContext('count_per_user');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id));
  }

  /**
   * @param $org_id
   * @return null
   */
  public function listPerOrgAction($org_id) {
    // Create Action Context
    $context = new ActionContext('list_per_org');

    return $this->doAction($context
          ->setParameter('org_id', (integer) $org_id));
  }

  /**
   * @param $org_id
   * @return null
   */
  public function countPerOrgAction($org_id) {
    // Create Action Context
    $context = new ActionContext('count_per_org');

    return $this->doAction($context
          ->setParameter('org_id', (integer) $org_id));
  }

  /**
   * @param $user_id
   * @return null
   * @throws \Exception
   */
  public function userGetAction($user_id) {
    // Create Action Context
    $context = new ActionContext('get');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @return null
   */
  public function getAction($user_id, $org_id) {
    // Create Action Context
    $context = new ActionContext('get');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id)
          ->setParameter('org_id', (integer) $org_id));
  }

  /**
   * @param $user_id
   * @param $permissions
   * @return null
   * @throws \Exception
   */
  public function userSetAction($user_id, $permissions) {
    // Create Action Context
    $context = new ActionContext('set');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id)
          ->setIfNotNull('permissions',
                         StringUtilities::nullOnEmpty($permissions)));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @param $permissions
   * @return null
   */
  public function setAction($user_id, $org_id, $permissions) {
    // Create Action Context
    $context = new ActionContext('set');

    return $this->doAction($context
          ->setParameter('user_id', (integer) $user_id)
          ->setParameter('org_id', (integer) $org_id)
          ->setIfNotNull('permissions',
                         StringUtilities::nullOnEmpty($permissions)));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doLinkAction($context) {
    $repository = $this->getRepository();
    return $repository->addLink(
        $context->getParameter('user'), $context->getParameter('org'),
                                                               $context->getParameter('permissions',
                                                                                      '')
    );
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doUnlinkAction($context) {
    $repository = $this->getRepository();
    return $repository->removeLink(
        $context->getParameter('user'), $context->getParameter('org')
    );
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListPerOrgAction($context) {
    $repository = $this->getRepository();
    return $repository->listUsers($context->getParameter('org'));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountPerOrgAction($context) {
    $repository = $this->getRepository();
    return $repository->countUsers($context->getParameter('org'));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListPerUserAction($context) {
    $repository = $this->getRepository();
    return $repository->listOrganizations($context->getParameter('user'));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountPerUserAction($context) {
    $repository = $this->getRepository();
    return $repository->countOrganizations($context->getParameter('user'));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doGetAction($context) {
    return $this->getRepository()->findLink($context->getParameter('user'),
                                                                   $context->getParameter('org'));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doSetAction($context) {
    // Add Link will Just Update the Link if it already exists
    return $this->doLinkAction($context);
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    // Extract User ID
    $parameters = $this->processChecks($context,
                                       array('Link', 'Unlink', 'Get', 'Set', 'ListUsers', 'CountUsers', 'ListOrgs', 'CountOrgs'),
                                       function($controller, $context) {
        // Get the Identified for the User
        $id = $context->getParameter('user_id');
        if (!isset($id)) {
          $id = SessionManager::getUser();
          if (!isset($id)) {
            throw new \Exception('Missing Required Action Parameter [user_id].', 1);
          }
        }

        // Get User for Action
        $user = $controller->getRepository('TestCenter\ModelBundle\Entity\User')->find($id);
        if (!isset($user)) {
          throw new \Exception("User not found[$id]", 1);
        }

        return $context->setParameter('user', $user);
      });


    // Extract Organization ID
    $parameters = $this->processChecks($action,
                                       array('Link', 'Unlink', 'Get', 'Set', 'ListUsers', 'CountUsers'),
                                       $parameters,
                                       function($controller, $action, $parameters) {
        // Get the Identifier for the User
        $id = $context->getParameter('org_id');
        if (!isset($id)) {
          $id = SessionManager::getOrganization();
          if (!isset($id)) {
            throw new \Exception('Missing Required Action Parameter [org_id].', 1);
          }
        }

        // Get Organization for Action
        $org = $controller->getRepository('TestCenter\ModelBundle\Entity\Organization')->find($id);
        if (!isset($org)) {
          throw new \Exception("Organization not found[$id]", 1);
        }

        if ($context->getAction() !== 'Link') {
          // Check if user has access to Organization
          $controller->checkOrganizationAccess($context->getParameter('user'),
                                                                      $org);
        }

        // Save the Organization for the Action
        return $context
            ->setParameter('entity', $org)
            ->setParameter('org', $org);
      });

    return $parameters;
  }

  /**
   * @param $action
   * @param $results
   * @param $format
   */
  protected function preRender($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get Results
    $results = $context->getActionResult();

    // Get the Action Name
    switch ($context->getAction()) {
      case 'Set':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'Link':
      case 'Unlink':
      case 'Get':
      case 'Set':
        $return = isset($results) ? $results->toArray() : null;
        break;
      case 'ListPerOrg':
        $return = array();
        foreach ($results as $uo) {
          $user = $uo->getUser();
          $id = $user->getId();
          $return[$id] = $user->toArray();
          unset($return[$id]['id']);
        }
        break;
      case 'ListPerUser':
        $return = array();
        foreach ($results as $uo) {
          $org = $uo->getOrganization();
          $id = $org->getId();
          $return[$id] = $org->toArray();
          unset($return[$id]['id']);
        }
        break;
      default:
        $return = $results;
    }

    return $return;
  }

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
    $repository = $this->getRepository();
    $link = $repository->findLink($user, $organization);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Organization [{$organization->getId()}].", 1);
    }

    return true;
  }

}
