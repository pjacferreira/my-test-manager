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
 * Description of UserProjectController
 *
 * @author Paulo Ferreira
 */
class UserProjectController
  extends BaseServiceController {

  // Entity Managed
  protected $m_oEntity;

  /**
   * @param $entity
   */
  public function __construct() {
    $this->m_oEntity = new EntityWrapper($this, 'TestCenter\ModelBundle\Entity\UserProject');
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
   * @param $project_id
   * @param null $permissions
   * @return null
   */
  public function linkAction($user_id, $project_id, $permissions = null) {
    $permissions = StringUtilities::nullOnEmpty($permissions);
    return $this->doAction('link', array('user_id' => (integer)$user_id, 'project_id' => (integer)$project_id, 'permissions' => isset($permissions) ? $permissions : ''));
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
  public function unlinkAction($user_id, $project_id) {
    return $this->doAction('unlink', array('user_id' => (integer)$user_id, 'project_id' => (integer)$project_id));
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
  public function projectListAction($user_id) {
    return $this->doAction('list_projects', array('user_id' => (integer)$user_id));
  }

  /**
   * @param $user_id
   * @return null
   */
  public function projectCountAction($user_id) {
    return $this->doAction('count_projects', array('user_id' => (integer)$user_id));
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
   * @param $project_id
   * @return null
   */
  public function getAction($user_id, $project_id) {
    return $this->doAction('get', array('user_id' => (integer)$user_id, 'project_id' => (integer)$project_id));
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
   * @param $project_id
   * @param $permissions
   * @return null
   */
  public function setAction($user_id, $project_id, $permissions) {
    $permissions = StringUtilities::nullOnEmpty($permissions);
    return $this->doAction('set', array('user_id' => (integer)$user_id, 'project_id' => (integer)$project_id, 'permissions' => isset($permissions) ? $permissions : ''));
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doLinkAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $project = ArrayUtilities::extract($parameters, 'project');
    $permissions = ArrayUtilities::extract($parameters, 'permissions', '');
    $repository = $this->getRepository();
    return $repository->addLink($user, $project, $permissions);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doUnlinkAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $project = ArrayUtilities::extract($parameters, 'project');
    $repository = $this->getRepository();
    return $repository->removeLink($user, $project);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListUsersAction($parameters) {
    $project = ArrayUtilities::extract($parameters, 'project');
    $repository = $this->getRepository();
    return $repository->listUsers($project);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountUsersAction($parameters) {
    $project = ArrayUtilities::extract($parameters, 'project');
    $repository = $this->getRepository();
    return $repository->countUsers($project);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListProjectsAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $repository = $this->getRepository();
    return $repository->listProjects($user);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountProjectsAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $repository = $this->getRepository();
    return $repository->countProjects($user);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doGetAction($parameters) {
    $user = ArrayUtilities::extract($parameters, 'user');
    $project = ArrayUtilities::extract($parameters, 'project');
    return $this->getRepository()->findLink($user, $project);
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

    // Process User ID
    $parameters = $this->processChecks($action,
                                       array('Link', 'Unlink', 'Get', 'Set', 'ListUsers', 'CountUsers', 'ListProjects', 'CountProjects'),
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

    // Process Project ID
    $parameters = $this->processChecks($action,
                                       array('Link', 'Unlink', 'Get', 'Set', 'ListUsers', 'CountUsers'),
                                       $parameters,
      function($controller, $action, $parameters) {
        // Get the Project ID (either through parameter or the Current Session Settings)
        $id = ArrayUtilities::extract($parameters, 'project_id');
        if (!isset($id)) {
          $controller->checkProject();

          // Get the Current Session Project
          $id = SessionManager::getProject();
        }

        // Get Project for Action
        $project = $controller->getRepository('TestCenter\ModelBundle\Entity\Project')->find($id);
        if (!isset($project)) {
          throw new \Exception("Project not found[$id]", 1);
        }

        if ($action === 'Link') {
          // User Only Requires Access to Organization
          $organization = $project->getOrganization();
          $controller->checkOrganizationAccess($parameters['user'], $organization);
        } else {
          // Check if User Has Access to Project (and by consequence to the Organization)
          $controller->checkProjectAccess($parameters['user'], $project);
        }

        // Save the Project for the Action
        $parameters['entity'] = $project;
        $parameters['project'] = $project;
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
      case 'ListProjects':
        $return = array();
        foreach ($results as $uo) {
          $project = $uo->getProject();
          $id = $project->getId();
          $return[$id] = $project->toArray();
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
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserOrganization');
    $link = $repository->findLink($user, $organization);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Organization [{$organization->getId()}].", 1);
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
    $repository = $this->getRepository();
    $link = $repository->findLink($user, $project);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Project [{$project->getId()}].", 1);
    }

    return true;
  }
}
