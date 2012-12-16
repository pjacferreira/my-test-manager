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
 * Description of TestsController
 *
 * @author Paulo Ferreira
 */
class TestsController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\Test');
  }

  /**
   * Create a Test, within Current Session Project and Working Container
   *
   * @param $name Test Name (Unique within the Project it belongs to)
   * @param $fv_settings Optional Test Object Settings
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
   * 
   * @param type $name
   * @return type
   */
  public function readByNameAction($name) {
    return $this->doAction('read',
                           array('name' => StringUtilities::nullOnEmpty($name)));
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
   * 
   * @param type $project_id
   * @return type
   */
  public function listProjectAction($project_id = null) {
    return $this->doAction('list_project',
                           isset($project_id) ? array('project_id' => (integer) $project_id) : null);
  }

  /**
   * 
   * @param type $project_id
   * @return type
   */
  public function countProjectAction($project_id = null) {
    return $this->doAction('count_project',
                           isset($project_id) ? array('project_id' => (integer) $project_id) : null);
  }

  /**
   * 
   * @param type $container_id
   * @return type
   */
  public function listContainerAction($container_id = null) {
    return $this->doAction('list_container',
                           isset($container_id) ? array('container_id' => (integer) $container_id) : null);
  }

  /**
   * 
   * @param type $container_id
   * @return type
   */
  public function countContainerAction($container_id = null) {
    return $this->doAction('count_container',
                           isset($container_id) ? array('container_id' => (integer) $container_id) : null);
  }

  /**
   * 
   * @param type $parameters
   * @param type $test
   * @return type
   */
  protected function postActionCreate($parameters, $test) {
    // Create Document Root Container for Test
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');
    $docroot = $repository->createContainer("DOCROOT Test [{$test->getId()}]",
                                            $test);
    $docroot->setSingleLevel(1);
    $test->setContainer($docroot);

    // Add a Test Container Entry
    $repository->createContainerEntry($parameters['container'], $test,
                                      $test->getName());

    // Flush Changes
    $this->getEntityManager()->flush();

    return $test;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListProjectAction($parameters) {
    $repository = $this->getRepository();

    return $repository->listTests($parameters['_filter']['project']);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountProjectAction($parameters) {
    $repository = $this->getRepository();

    return $repository->countTests($parameters['_filter']['project']);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListContainerAction($parameters) {
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');

    return $repository->listEntries($parameters['_filter']['container'],
                                    $parameters['_filter']['link_type']);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountContainerAction($parameters) {
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');

    return $repository->countEntries($parameters['_filter']['container'],
                                     $parameters['_filter']['link_type']);
  }

  /**
   * @param $parameters
   * @return array
   * @throws \Exception
   */
  protected function sessionChecksCreate($parameters) {
    // TODO Required Verification that User Has Required Permission against the Project for the Actions
    // Basic Session Checks
    $parameters = $this->sessionChecks('Create', $parameters);

    // Verify Parameters
    $name = ArrayUtilities::extract($parameters, 'name');
    if (!isset($name)) {
      throw new \Exception('Missing Required Action Parameter [name].', 1);
    }

    // See if the Test Name already exists in the Project
    $repository = $this->getRepository();
    $entry = $repository->findOneTest($parameters['project'], $name);
    if (isset($entry)) {
      throw new \Exception("Test [$name] already exists.", 2);
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
    $this->checkProject();

    // Get User for Action
    $user_id = SessionManager::getUser();

    $user = $this->getRepository('TestCenter\ModelBundle\Entity\User')->find($user_id);
    if (!isset($user)) {
      throw new \Exception("User not found[$user_id]", 1);
    }

    $parameters['user'] = $user;

    // Get Project for Action
    $project_id = SessionManager::getProject();

    $project = $this->getRepository('TestCenter\ModelBundle\Entity\Project')->find($project_id);
    if (!isset($project)) {
      throw new \Exception("Project not found[$project_id]", 1);
    }

    $parameters['project'] = $project;

    // Process Container ID
    $parameters = $this->processChecks($action, null, $parameters,
                                       function($controller, $action, $parameters) {

        if (isset($parameters['container_id'])) {
          // Specific Container Listed
          $id = $parameters['container_id'];
        } else {
          if (!SessionManager::hasContainer()) {
            throw new \Exception('No Active Container.', 1);
          }

          // Get Project for Action
          $id = SessionManager::getContainer();
        }

        // Get Container for Action
        $container = $controller->getRepository('TestCenter\ModelBundle\Entity\Container')->find($id);
        if (!isset($container)) {
          throw new \Exception("Container not found[$id]", 2);
        }

        // Current Project
        $project = $parameters['project'];

        $owner_id = $project->getId();
        $owner_type = $controller->getTypeCache()->typeID($project);
        if (($container->getOwner() != $owner_id) || ($container->getOwnertype() != $owner_type)) {
          throw new \Exception("Container[$id] not found", 1);
        }

        $parameters['container'] = $container;

        // Set a Filter for the Action
        $parameters['_filter'] = array('container' => $container, 'link_type' => $controller->getTypeCache()->typeID('TestCenter\ModelBundle\Entity\Test'));

        return $parameters;
      });

    /* TODO (TD-6) If the id argument, is used, for READ/UPDATE/DELETE Actions, 
     * the container parameter, is not required (optimization), or should be 
     * the container in which the test exists.
     */
    // Find the Test
    $parameters = $this->processChecks($action,
                                       array('Read', 'Update', 'Delete'),
                                       $parameters,
                                       function($controller, $action, $parameters) {
        // Get the Identified for the User
        $id = ArrayUtilities::extract($parameters, 'id');
        $name = ArrayUtilities::extract($parameters, 'name');
        if (!(isset($id) || isset($name))) {
          throw new \Exception('Missing Required Action Parameter [id or name].', 1);
        }

        // Get the Test
        $test = isset($id) ? $controller->getRepository()->find($id) :
          $controller->getRepository()->findOneByName($name);
        if (!isset($test)) {
          throw new \Exception('Test not found', 1);
        }

        //TODO Verify that user has required permissions
        // Save the Project for the Action
        $parameters['entity'] = $test;
        $parameters['test'] = $test;

        return $parameters;
      });

    return $parameters;
  }

  /**
   * 
   * @param type $parameters
   */
  protected function preActionDelete($parameters) {
    assert('isset($parameters) && is_array($parameters)');

    // TODO Verify that the test belongs to the current active project
    
    // Get the Test
    $test = $parameters['test'];

    // Remove Relations to Test
    $this->getRepository()->removeRelations($test);
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
      case 'ListProject':
        $return = array();
        foreach ($results as $test) {
          $id = $test->getId();
          $return[$id] = $test->toArray();
          unset($return[$id]['id']);
        }
        break;
      case 'ListContainer':
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

}
