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
use TestCenter\ServiceBundle\API\BaseServiceController;
use TestCenter\ServiceBundle\API\EntityWrapper;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of UserOrganizationController
 *
 * @author Paulo Ferreira
 */
class UserOrganizationController
  extends BaseServiceController {

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
    $permissions = StringUtilities::nullOnEmpty($permissions);
    return $this->doAction('link', array('user_id' => (integer)$user_id, 'permissions' => isset($permissions) ? $permissions : ''));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @param null $permissions
   * @return null
   */
  public function linkAction($user_id, $org_id, $permissions = null) {
    $permissions = StringUtilities::nullOnEmpty($permissions);
    return $this->doAction('link', array('user_id' => (integer)$user_id, 'org_id' => (integer)$org_id, 'permissions' => isset($permissions) ? $permissions : ''));
  }

  /**
   * @param $user_id
   * @return null
   * @throws \Exception
   */
  public function userUnlinkAction($user_id) {
    return $this->doAction('unlink', array('user_id' => (integer)$user_id));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @return null
   */
  public function unlinkAction($user_id, $org_id) {
    return $this->doAction('unlink', array('user_id' => (integer)$user_id, 'org_id' => (integer)$org_id));
  }

  /**
   * @return null
   * @throws \Exception
   */
  public function userListAction() {
    return $this->doAction('list_users');
  }

  /**
   * @return null
   * @throws \Exception
   */
  public function userCountAction() {
    return $this->doAction('count_users');
  }

  /**
   * @param $user_id
   * @return null
   */
  public function orgListAction($user_id) {
    return $this->doAction('list_orgs', array('user_id' => (integer)$user_id));
  }

  /**
   * @param $user_id
   * @return null
   */
  public function orgCountAction($user_id) {
    return $this->doAction('count_orgs', array('user_id' => (integer)$user_id));
  }

  /**
   * @param $user_id
   * @return null
   * @throws \Exception
   */
  public function userGetAction($user_id) {
    return $this->doAction('get', array('user_id' => (integer)$user_id));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @return null
   */
  public function getAction($user_id, $org_id) {
    return $this->doAction('get', array('user_id' => (integer)$user_id, 'org_id' => (integer)$org_id));
  }

  /**
   * @param $user_id
   * @param $permissions
   * @return null
   * @throws \Exception
   */
  public function userSetAction($user_id, $permissions) {
    $permissions = StringUtilities::nullOnEmpty($permissions);
    return $this->doAction('set', array('user_id' => (integer)$user_id, 'permissions' => isset($permissions) ? $permissions : ''));
  }

  /**
   * @param $user_id
   * @param $org_id
   * @param $permissions
   * @return null
   */
  public function setAction($user_id, $org_id, $permissions) {
    $permissions = StringUtilities::nullOnEmpty($permissions);
    return $this->doAction('set', array('user_id' => (integer)$user_id, 'org_id' => (integer)$org_id, 'permissions' => isset($permissions) ? $permissions : ''));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doLinkAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $org = ArrayUtilities::extract($parameters, 'org');
    $permissions = ArrayUtilities::extract($parameters, 'permissions', '');
    $repository = $this->getRepository();
    return $repository->addLink($user, $org, $permissions);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doUnlinkAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository();
    return $repository->removeLink($user, $org);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListUsersAction($parameters) {
    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository();
    return $repository->listUsers($org);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountUsersAction($parameters) {
    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository();
    return $repository->countUsers($org);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListOrgsAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $repository = $this->getRepository();
    return $repository->listOrganizations($user);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountOrgsAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $repository = $this->getRepository();
    return $repository->countOrganizations($user);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doGetAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $org = ArrayUtilities::extract($parameters, 'org');
    return $this->getRepository()->findLink($user, $org);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doSetAction($parameters) {
    // Add Link will Just Update the Link if it already exists
    return $this->doLinkAction($parameters);
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    // Extract User ID
    $parameters = $this->processChecks($action,
                                       array('Link', 'Unlink', 'Get', 'Set', 'ListUsers', 'CountUsers', 'ListOrgs', 'CountOrgs'),
                                       $parameters,
      function($controller, $action, $parameters) {
        // Get the Identified for the User
        $id = ArrayUtilities::extract($parameters, 'user_id');
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

        $parameters['user'] = $user;
        return $parameters;
      });


    // Extract Organization ID
    $parameters = $this->processChecks($action,
                                       array('Link', 'Unlink', 'Get', 'Set', 'ListUsers', 'CountUsers'),
                                       $parameters,
      function($controller, $action, $parameters) {
        // Get the Identifier for the User
        $id = ArrayUtilities::extract($parameters, 'org_id');
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

        if ($action !== 'Link') {
          // Check if user has access to Organization
          $controller->checkOrganizationAccess($parameters['user'], $org);
        }

        // Save the Organization for the Action
        $parameters['entity'] = $org;
        $parameters['org'] = $org;

        return $parameters;
      });

    return $parameters;
  }

  /**
   * @param $action
   * @param $results
   * @param $format
   */
  protected function preRender($action, $results, $format) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($format) && is_string($format)');

    $return = $results;
    switch ($action) {
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
      case 'ListUsers':
        $return = array();
        foreach ($results as $uo) {
          $user = $uo->getUser();
          $id = $user->getId();
          $return[$id] = $user->toArray();
          unset($return[$id]['id']);
        }
        break;
      case 'ListOrgs':
        $return = array();
        foreach ($results as $uo) {
          $org = $uo->getOrganization();
          $id = $org->getId();
          $return[$id] = $org->toArray();
          unset($return[$id]['id']);
        }
        break;
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
