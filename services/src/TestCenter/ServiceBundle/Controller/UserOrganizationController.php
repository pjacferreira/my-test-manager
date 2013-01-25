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
   * @return null
   */
  public function listPerUserAction($user_id = null) {
    // Create Action Context
    $context = new ActionContext('list_per_user');

    return $this->doAction($context
          ->setIfNotNull('user_id', isset($user_id) ? (integer) $user_id : null));
  }

  /**
   * @param $user_id
   * @return null
   */
  public function countPerUserAction($user_id = null) {
    // Create Action Context
    $context = new ActionContext('count_per_user');

    return $this->doAction($context
          ->setIfNotNull('user_id', isset($user_id) ? (integer) $user_id : null));
  }

  /**
   * @param $org_id
   * @return null
   */
  public function listPerOrgAction($org_id = null) {
    // Create Action Context
    $context = new ActionContext('list_per_org');

    return $this->doAction($context
          ->setIfNotNull('org_id', isset($org_id) ? (integer) $org_id : null));
  }

  /**
   * @param $org_id
   * @return null
   */
  public function countPerOrgAction($org_id = null) {
    // Create Action Context
    $context = new ActionContext('count_per_org');

    return $this->doAction($context
          ->setIfNotNull('org_id', isset($org_id) ? (integer) $org_id : null));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doLinkAction($context) {
    $repository = $this->getRepository();
    return $repository->addLink(
        $context->getParameter('user'), $context->getParameter('organization'),
                                                               $context->getParameter('permissions',
                                                                                      '')
    );
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doUnlinkAction($context) {
    $user = $context->getParameter('user');
    $organization = $context->getParameter('organization');

    // Find the Link
    $link = $this->getRepository()->removeLink($user, $organization);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Organization [{$organization->getId()}].", 1);
    }

    return $link;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListPerOrgAction($context) {
    $repository = $this->getRepository();
    return $repository->listUsers($context->getParameter('organization'));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountPerOrgAction($context) {
    $repository = $this->getRepository();
    return $repository->countUsers($context->getParameter('organization'));
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
    $user = $context->getParameter('user');
    $organization = $context->getParameter('organization');

    // Find the Link
    $link = $this->getRepository()->findLink($user, $organization);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Organization [{$organization->getId()}].", 1);
    }

    return $link;
  }

  /**
   * @param $context
   * @return mixed
   */
  protected function doSetAction($context) {
    // Add Link will Just Update the Link if it already exists
    return $this->doLinkAction($context);
  }

  /**
   * 
   * @param type $context
   * @return type
   * @throws \Exception
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    // Process 'user_id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'user_id',
                                    function($controller, $context, $action, $value) {
        // Get User for Action
        $user = $controller->getRepository('TestCenter\ModelBundle\Entity\User')->find($value);
        if (!isset($user)) {
          throw new \Exception("User not found[$value]", 1);
        }

        return $context->setParameter('user', $user);
      }, null,
                                      array('Link', 'Unlink', 'Get', 'Set', 'ListPerUser', 'CountPerUser', 'ListPerOrg', 'CountPerOrg'),
                                      function($controller, $context, $action) {
        // Missing User ID, so use the current Session User
        return SessionManager::getUser();
      });


    // Extract Organization ID
    $context = $this->onParameterDo($context, 'org_id',
                                    function($controller, $context, $action, $value) {
        // Get Organization for Action
        $org = $controller->getRepository('TestCenter\ModelBundle\Entity\Organization')->find($value);
        if (!isset($org)) {
          throw new \Exception("Organization not found[$value]", 1);
        }

        // Save the Organization for the Action
        return $context
            ->setParameter('organization', $org);
      }, null,
                           array('Link', 'Unlink', 'Get', 'Set', 'ListPerUser', 'CountPerUser'),
                           function($controller, $context, $action) {
        // Missing Organization ID, so use the current Session Organization
        $controller->checkOrganization();

        // Get the Current Session Organization
        return SessionManager::getOrganization();
      });

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

  /**
   * 
   * @param type $context
   * @return type
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
