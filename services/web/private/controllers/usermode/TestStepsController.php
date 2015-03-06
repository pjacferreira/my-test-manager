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
namespace controllers\usermode;

use Library\ArrayUtilities;
use TestCenter\ServiceBundle\API\CrudServiceController;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of TestStepsController
 *
 * @author Paulo Ferreira
 */
class TestStepsController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\TestStep');
  }

  /**
   * 
   * @param type $test_id
   * @param type $sequence
   * @param type $fv_settings
   * @return type
   */
  public function createAction($test_id, $title, $sequence = null,
                               $fv_settings = null) {
    assert('isset($test_id) && is_string($test_id)');
    assert('isset($title) && is_string($title)');
    assert('!isset($sequence) || is_string($sequence)');
    assert('!isset($fv_settings) || is_string($fv_settings)');

    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);

    // Save Test ID and Sequence
    $array['test_id'] = (integer) $test_id;
    $array['title'] = $title;
    if (isset($sequence)) {
      $array['sequence'] = (integer) $sequence;
    }

    // Call the Function
    return $this->doAction('create', $array);
  }

  /**
   * 
   * @param type $test_id
   * @param type $sequence
   * @return type
   */
  public function readAction($test_id, $sequence) {
    assert('isset($test_id) && is_string($test_id)');
    assert('isset($sequence) && is_string($sequence)');

    return $this->doAction('read',
                           array(
        'test_id' => (integer) $test_id,
        'sequence' => (integer) $sequence
      ));
  }

  /**
   * 
   * @param type $test_id
   * @param type $sequence
   * @param type $fv_settings
   * @return type
   */
  public function updateAction($test_id, $sequence, $fv_settings) {
    assert('isset($test_id) && is_string($test_id)');
    assert('isset($sequence) && is_string($sequence)');
    assert('isset($fv_settings) && is_string($fv_settings)');

    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);
    $array['test_id'] = (integer) $test_id;
    $array['sequence'] = (integer) $sequence;

    return $this->doAction('update', $array);
  }

  /**
   * 
   * @param type $test_id
   * @param type $sequence
   * @return type
   */
  public function deleteAction($test_id, $sequence) {
    assert('isset($test_id) && is_string($test_id)');
    assert('isset($sequence) && is_string($sequence)');

    return $this->doAction('delete',
                           array(
        'test_id' => (integer) $test_id,
        'sequence' => (integer) $sequence
      ));
  }

  /**
   * 
   * @param type $test_id
   * @param type $step
   * @return type
   */
  public function stepRenumberAction($test_id, $step) {
    assert('isset($test_id) && is_string($test_id)');
    assert('isset($step) && is_string($step)');

    return $this->doAction('renumber_steps',
                           array(
        'test_id' => (integer) $test_id,
        'step' => (integer) $step
      ));
  }

  /**
   * 
   * @param type $test_id
   * @param type $sequence
   * @param type $to
   * @return type
   */
  public function stepMoveAction($test_id, $sequence, $to) {
    assert('isset($test_id) && is_string($test_id)');
    assert('isset($to) && is_string($to)');

    return $this->doAction('move_step',
                           array(
        'test_id' => (integer) $test_id,
        'sequence' => (integer) $sequence,
        'to' => (integer) $to
      ));
  }

  /**
   * 
   * @param type $test_id
   * @return type
   */
  public function stepListAction($test_id) {
    assert('isset($test_id) && is_string($test_id)');

    return $this->doAction('list_steps', array('test_id' => (integer) $test_id));
  }

  /**
   * 
   * @param type $test_id
   * @return type
   */
  public function stepCountAction($test_id) {
    assert('isset($test_id) && is_string($test_id)');

    return $this->doAction('count_steps', array('test_id' => (integer) $test_id));
  }

  /**
   * 
   * @param type $parameters
   * @param type $testset
   * @return type
   */
  protected function doCreateAction($parameters) {
    return $this->getRepository()->createStep($parameters['test'],
                                              $parameters['title'],
                                              ArrayUtilities::extract($parameters,
                                                                      'sequence'));
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doReadAction($parameters) {
    return $parameters['teststep'];
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doDeleteAction($parameters) {
    return $this->getRepository()->removeStep($parameters['test'],
                                              $parameters['sequence']);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doRenumberStepsAction($parameters) {
    return $this->getRepository()->renumberSteps($parameters['test'],
                                                 $parameters['step']);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doMoveStepAction($parameters) {
    return $this->getRepository()->moveStep($parameters['test'],
                                            $parameters['sequence'],
                                            $parameters['to']);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListStepsAction($parameters) {
    $repository = $this->getRepository();

    return $repository->listSteps($parameters['test']);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountStepsAction($parameters) {
    $repository = $this->getRepository();

    return $repository->countSteps($parameters['test']);
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
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();
    $this->sessionManager->checkProject();

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

    // Get the Test for the Action
    $parameters = $this->inoutParameters($action, null, null, $parameters,
                                         'test_id', 'test',
                                         SessionManager::getContainer(),
                                         function($controller, $parameters, $in_value) {
        // No Container ID Given
        if (!isset($in_value)) {
          throw new \Exception("No Test Specified.", 2);
        }

        $test = $controller->getRepository('TestCenter\ModelBundle\Entity\Test')->find($in_value);
        if (!isset($test)) {
          throw new \Exception("Test[$in_value] not found", 2);
        }

        // Check if the Test belong to the Current Project
        if ($test->getProject()->getId() != $parameters['project']->getId()) {
          throw new \Exception("Test[{$test->getId()}] does not belong to the Current Project[" . $parameters['project']->getId() . "]", 2);
        }

        return $test;
      });

    // Get the Step for the Action
    $parameters = $this->inoutParameters($action, array('Read', 'Update'), null,
                                         $parameters, 'sequence',
                                         array('teststep', 'entity'),
                                         SessionManager::getContainer(),
                                         function($controller, $parameters, $in_value) {
        // No Container ID Given
        if (!isset($in_value)) {
          throw new \Exception("No Test Specified.", 2);
        }

        $test = $parameters['test'];
        $step = $controller->getRepository()->findStep($test, $in_value);
        if (!isset($step)) {
          throw new \Exception("Sequence[$in_value] not found, in Test[{$test->getId()}].", 2);
        }

        return $step;
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
      case 'MoveStep':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'ListSteps':
        $return = array();
        // TODO use array_map to convert
        foreach ($results as $set) {
          $id = $set->getId();
          $return[$id] = $set->toArray();
          unset($return[$id]['id']);
        }
        break;
    }

    return $return;
  }

}
