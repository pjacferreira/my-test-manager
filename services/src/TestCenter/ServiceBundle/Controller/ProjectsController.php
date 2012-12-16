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
use TestCenter\ServiceBundle\API\CrudServiceController;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of ProjectsController
 *
 * @author Paulo Ferreira
 */
class ProjectsController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\Project');
  }

  /**
   * Create a Project, within an Organization
   *
   * @param $org_id Organization within which we will be creating the Project
   * @param $name Project Name (Unique within the Organization it belongs to)
   * @param $fv_settings Optional Project Object Settings
   * @return Response Object or throw Exception
   */
  public function createInOrgAction($org_id, $name, $fv_settings = null) {
    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);

    // Add Organization ID and Name
    $array['org_id'] = (integer) $org_id;
    $array['name'] = StringUtilities::nullOnEmpty($name);

    // Call the Function
    return $this->doAction('create', $array);
  }

  /**
   * Create a Project, within Current Session Organization
   *
   * @param $name Project Name (Unique within the Organization it belongs to)
   * @param $fv_settings Optional Project Object Settings
   * @return Response Object or throw Exception
   */
  public function createAction($name, $fv_settings = null) {
    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);

    $array['name'] = StringUtilities::nullOnEmpty($name);

    // Call the Function
    return $this->doAction('create', $array);
  }

  /**
   * @param $id
   * @return null
   */
  public function readAction($id) {
    return $this->doAction('read', array('id' => (integer) $id));
  }

  /**
   * @param $id
   * @param $fields
   * @param $values
   * @return null
   */
  public function updateAction($id, $fv_settings) {
    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);
    $array['id'] = (integer) $id;

    return $this->doAction('update', $array);
  }

  /**
   * @param $id
   * @return null
   */
  public function deleteAction($id) {
    return $this->doAction('delete', array('id' => (integer) $id));
  }

  /**
   * @param null $org_id
   * @return null
   */
  public function listAction($org_id = null) {
    /* Allowing a NULL org_id is a win-win situation
     * 1. Allows us to override the CrudService method (which has no parameters)
     * 2. Allows for the scenario that the service is listing the project for the current session organization
     */
    return $this->doAction('list_per_org',
                           isset($org_id) ? array('org_id' => (integer) $org_id) : null);
  }

  /**
   * @param null $org_id
   * @return null
   */
  public function countAction($org_id = null) {
    return $this->doAction('count_per_org',
                           isset($org_id) ? array('org_id' => (integer) $org_id) : null);
  }

  /**
   * @param $parameters
   * @param $project
   * @return mixed
   */
  protected function postActionCreate($parameters, $project) {
    // Create Root Container for Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');
    $container = $repository->createContainer("ROOT Project [{$project->getId()}]",
                                              $project);
    $container->setSingleLevel(0);
    $project->setContainer($container);

    // TODO Remove Project Container on Delete
    // Link the New Organization to the Current User
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserProject');
    $repository->addLink($parameters['user'], $project, '');

    // Flush Changes to the Container and the Project Objects
    $this->getEntityManager()->flush();

    return $project;
  }

  /**
   * @param $parameters
   * @return object
   */
  protected function doDeleteAction($parameters) {
    // Unlink all users from the Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserProject');
    $repository->removeProject($parameters['project']);

    return parent::doDeleteAction($parameters);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListPerOrgAction($parameters) {
    // TODO Create a System by Which the CRUD Controller can also handle the WHERE by way of the parameters
    $org = ArrayUtilities::extract($parameters, 'organization');
    $query = $this->getEntityManager()->createQuery("SELECT p" .
      " FROM " . $this->m_oEntity->getEntity() . " p" .
      " WHERE p.organization = ?1");
    $query->setParameter(1, $org);
    $projects = $query->getResult();
    return $projects;
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountPerOrgAction($parameters) {
    $org = ArrayUtilities::extract($parameters, 'organization');
    $query = $this->getEntityManager()->createQuery("SELECT count(p)" .
      " FROM " . $this->m_oEntity->getEntity() . " p" .
      " WHERE p.organization = ?1");
    $query->setParameter(1, $org);
    $result = $query->getScalarResult();
    return (integer) $result[0][1];
  }

  /**
   * @param $parameters
   * @return array
   * @throws \Exception
   */
  protected function sessionChecksCreate($parameters) {
    // TODO Required Verification that User Has Required Permission against this Organization and/or Project for the Actions
    // Basic Session Checks
    $parameters = $this->sessionChecks('Create', $parameters);

    // Verify Parameters
    $name = ArrayUtilities::extract($parameters, 'name');
    if (!isset($name)) {
      throw new \Exception('Missing Required Action Parameter [name].', 1);
    }

    // Test if the Project Name already exists
    $project = $this->getRepository()->findOneBy(array('organization' => $parameters['organization'], 'name' => $name));
    if (isset($project)) {
      throw new \Exception("Project [$name] already exists.", 2);
    }

    return $parameters;
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($action, $parameters) {
    // TODO Required Verification that User Has Required Permission against this Organization and/or Project for the Actions
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    // Process User
    $id = SessionManager::getUser();

    // Get User for Action
    $user = $this->getRepository('TestCenter\ModelBundle\Entity\User')->find($id);
    if (!isset($user)) {
      throw new \Exception("User not found[$id]", 1);
    }

    $parameters['user'] = $user;

    // Process Organization ID
    $parameters = $this->processChecks($action,
                                       array('Create', 'ListPerOrg', 'CountPerOrg'),
                                       $parameters,
                                       function($controller, $action, $parameters) {
        // Get the Organization  ID (either through parameter or the Current Session Settings)
        $org_id = ArrayUtilities::extract($parameters, 'org_id');
        if (!isset($org_id)) {
          $controller->checkOrganization();

          // Get the Current Session Organization
          $org_id = SessionManager::getOrganization();
        }

        $org = $controller->getRepository('TestCenter\ModelBundle\Entity\Organization')->find($org_id);
        if (!isset($org)) {
          throw new \Exception("Organization[$org_id] not found", 1);
        }

        // Check if we have access to the Organization
        $controller->checkOrganizationAccess($parameters['user'], $org);

        $parameters['organization'] = $org;
        // Set a Filter for the Action
        $parameters['_filter'] = array('organization' => $org);

        return $parameters;
      });

    // Process Project ID
    $parameters = $this->processChecks($action,
                                       array('Read', 'Update', 'Delete'),
                                       $parameters,
                                       function($controller, $action, $parameters) {
        // Get the Identified for the User
        $id = ArrayUtilities::extract($parameters, 'id');
        if (!isset($id)) {
          throw new \Exception('Missing Required Action Parameter [id].', 1);
        }

        // Get the Project
        $project = $controller->getRepository()->find($id);
        if (!isset($project)) {
          throw new \Exception('Project not found', 1);
        }

        if ($action === 'Delete') {
          // User Only Requires Access to Organization
          $controller->checkOrganizationAccess($parameters['user'],
                                               $project->getOrganization());
        } else {
          // Check if User Has Access to Project (and by consequence to the Organization)
          $controller->checkProjectAccess($parameters['user'], $project);
        }

        // Check if we have access to the Organization
        $controller->checkProjectAccess($parameters['user'], $project);

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
      case 'Create':
      case 'Read':
      case 'Update':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'ListPerOrg':
        $return = array();
        foreach ($results as $project) {
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
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserProject');
    $link = $repository->findLink($user, $project);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Project [{$project->getId()}].", 1);
    }

    return true;
  }

}
