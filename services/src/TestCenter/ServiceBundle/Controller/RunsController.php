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
use TestCenter\ModelBundle\API\TypeCache;
use TestCenter\ServiceBundle\API\CrudServiceController;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of RunsController
 *
 * @author Paulo Ferreira
 */
class RunsController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\Run');
  }

  /**
   * Create a Run, based on a TestSet, within Current Session Project and Working Container
   *
   * @param $set_id Test Set to create Run From
   * @param $name Run Name (Unique within the Project it belongs to)
   * @param $fv_settings Optional TestSet Object Settings
   * @return Response Object or throw Exception
   */
  public function createAction($set_id, $name, $fv_settings = null) {
    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);

    $array['name'] = StringUtilities::nullOnEmpty($name);
    $array['set_id'] = (integer) $set_id;

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
   * @param type $run
   * @return type
   */
  protected function postActionCreate($parameters, $run) {
    // Get the Set from Which we want to build the RunLinks
    $this->getRepository()->cloneSetLinks($run, $run->getTestSet());

    // Add a Run Container Entry
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');
    $repository->createContainerEntry($parameters['container'], $run,
                                      $run->getName());

    // Flush Changes
    $this->getEntityManager()->flush();

    return $run;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListProjectAction($parameters) {
    $repository = $this->getRepository();

    return $repository->listRuns($parameters['project']);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountProjectAction($parameters) {
    $repository = $this->getRepository();

    return $repository->countRuns($parameters['project']);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListContainerAction($parameters) {
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');

    $type = TypeCache::getInstance()->typeID($this->m_oEntity->getEntity());
    return $repository->listEntries($parameters['container'], $type);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountContainerAction($parameters) {
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');

    $type = TypeCache::getInstance()->typeID($this->m_oEntity->getEntity());
    return $repository->countEntries($parameters['container'], $type);
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

    // See if the Run Name already exists in the Project
    $repository = $this->getRepository();
    $entry = $repository->findRun($parameters['project'], $name);
    if (isset($entry)) {
      throw new \Exception("Run [$name] already exists.", 2);
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

    // Get the Container for the Action
    $parameters = $this->inoutParameters($action,
                                         array('Create', 'ListContainer', 'CountContainer'),
                                         null, $parameters, 'container_id',
                                         'container',
                                         SessionManager::getContainer(),
                                         function($controller, $parameters, $in_value) {
        // No Container ID Given
        if (!isset($in_value)) {
          throw new \Exception("No Container Specified and No Session Container Active", 2);
        }

        $container = $controller->getRepository('TestCenter\ModelBundle\Entity\Container')->find($in_value);
        if (!isset($container)) {
          throw new \Exception("Container[$in_value] not found", 2);
        }

        // Check if the Test belong to the Current Project
        if ($container->getOwner() != $parameters['project']->getId()) {
          throw new \Exception("Container[{$container->getId()}] does not belong to the Current Project[" . $parameters['project']->getId() . "]", 2);
        }

        return $container;
      });

    // Get the Test Set for the Action
    $parameters = $this->inoutParameters($action,
                                         array(
      'Read', 'Update', 'Delete'), 'Read', $parameters, 'id',
                                         array('run', 'entity'), null,
                                         function($controller, $parameters, $in_value) {
        assert('isset($in_value) && is_integer($in_value)');

        $run = $controller->getRepository()->find($in_value);
        if (!isset($run)) {
          throw new \Exception("Run[$in_value] not found", 2);
        }

        return $run;
      });

    // Handle Special Case for Read (By Name)
    if (!isset($parameters['run']) && ($action == 'Read')) {
      $name = ArrayUtilities::extract($parameters, 'name');
      if (!isset($name)) {
        throw new \Exception('Missing Required Action Parameter [id or name].', 1);
      }

      // Get the TestSet (by Name)
      $run = $this->getRepository()->findOneByName($name);
      if (!isset($run)) {
        throw new \Exception("Run[$name] not found", 2);
      }

      $parameters['run'] = $run;
    }

    // Check if the Set Belongs to the Project
    if (isset($parameters['run']) && ($parameters['run']->getProject() != $project)) {
      throw new \Exception("Run[" . $parameters['run']->getId() . "] does not belong to the Current Project[{$project->getId()}]", 3);
    }

    // Get the Test Set for the Create
    $parameters = $this->inoutParameters($action, 'Create', null, $parameters,
                                         'set_id', 'testset', null,
                                         function($controller, $parameters, $in_value) {
        assert('isset($in_value) && is_integer($in_value)');

        $set = $controller->getRepository('TestCenter\ModelBundle\Entity\TestSet')->find($in_value);
        if (!isset($set)) {
          throw new \Exception("Test Set[$in_value] not found", 2);
        }

        // Check if the Test Set belong to the Current Project
        if ($set->getProject()->getId() != $parameters['project']->getId()) {
          throw new \Exception("Test Set[{$set->getId()}] does not belong to the Current Project[" . $parameters['project']->getId() . "]", 2);
        }

        return $set;
      });

    return $parameters;
  }

  /**
   * 
   * @param type $parameters
   */
  protected function preActionDelete($parameters) {
    assert('isset($parameters) && is_array($parameters)');

    // Get Run to Delete
    $run = $parameters['run'];

    // Remove Relations to Test
    $this->getRepository()->removeRelations($run);
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
      case 'LinkAdd':
        if (isset($results)) {
          $return = $results->toArray();
        }
        break;
      case 'ListProject':
        $return = array();
        foreach ($results as $set) {
          $id = $set->getId();
          $return[$id] = $set->toArray();
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
