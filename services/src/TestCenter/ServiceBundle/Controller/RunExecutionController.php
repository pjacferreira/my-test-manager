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

use Library\ArrayUtilities;
use TestCenter\ModelBundle\API\TypeCache;
use TestCenter\ServiceBundle\API\EntityServiceController;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of RunExecutionController
 *
 * @author Paulo Ferreira
 */
class RunExecutionController
  extends EntityServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\RunLink');
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function startRunAction($id) {
    return $this->doAction('start',
                           isset($id) ? array('id' => (integer) $id) : null);
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function closeAction($id = null) {
    return $this->doAction('close',
                           isset($id) ? array('id' => (integer) $id) : null);
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function currentAction($id = null) {
    return $this->doAction('current',
                           isset($id) ? array('id' => (integer) $id) : null);
  }

  /**
   * 
   * @param type $status
   * @param type $code
   * @param type $comment
   * @return type
   */
  public function nextAction($status, $code = null, $comment = null) {
    // Create Parameters
    $parameters['status'] = (integer) $status;
    if (isset($code)) {
      $parameters['code'] = (integer) $code;
    }
    if (isset($comment)) {
      $parameters['comment'] = $comment;
    }

    return $this->doAction('Next', $parameters);
  }

  /**
   * 
   * @param type $sequence
   * @return type
   */
  public function positionAction($sequence) {
    return $this->doAction('position',
                           isset($sequence) ? array('sequence' => (integer) $sequence) : null);
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function linkListAction($id = null) {
    return $this->doAction('link_list',
                           isset($id) ? array('id' => (integer) $id) : null);
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function linkCountAction($id = null) {
    return $this->doAction('link_count',
                           isset($id) ? array('id' => (integer) $id) : null);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doStartAction($parameters) {
    $user = $parameters['user'];
    $run = $parameters['run'];
    $owner = $run->getUser();

    // TODO Move this Test to Session Checks, for all Run Actions (start,close,next, etc.)
    // Test if the Current User Can Access this Run
    if ($owner != $user) {
      throw new \Exception("Run[{$run->getId()}] is owned by User[{$owner->getName()}]. Only that user can access this Run.", 3);
    }

    // TODO If Audit Log Active - Log Start Attempt
    $b_flush = false;
    switch ($run->getState()) {
      case 0: // Hasn't been Run Before (Initialize State, and let user continue)
        $run->setState(1);
        $b_flush = true;
        break;
      case 1: // Run Opened (Nothing to do, just let the user continue)
        break;
      case 2: // Closed (Mark the Run as Open, and let the user continue)
        $run->setState(1);
        $b_flush = true;
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->getState()}].", 3);
    }

    // Run Opened - Get the Current Test    
    if ($run->getState() === 1) {
      $sequence = $run->getSequence();
      if ($sequence <= 0) { // No Valid Sequence (Start from the Beginning)
        $link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->firstLink($run);
      } else { // Valid Sequence - Continue
        $link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->findLink($run,
                                                                                        $sequence);
      }

      if (isset($link)) {
        $run->setSequence($link->getSequence());
      } else {
        throw new \Exception("Run[{$run->getId()}] has no Tests Associated for Running.", 3);
      }
    }

    // Get the 1st Step in the Test
    $test = $link->getTest();
    $step = $this->getRepository('TestCenter\ModelBundle\Entity\TestStep')->firstStep($test);
    if (!isset($step)) {
      throw new \Exception("Run[{$step->getId()}] has no Steps Associated for Running.", 3);
    }

    // Set Session Variables
    SessionManager::setVariable('tc_run', $run->getId());
    SessionManager::setVariable('tc_run_test_step', $step->getSequence());

    // Save Changes Back to Database
    if ($b_flush) {
      $this->getEntityManager()->flush();
    }

    return $step;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCloseAction($parameters) {
    $user = $parameters['user'];
    $run = $parameters['run'];
    $owner = $run->getUser();

    // Test if the Current User Can Access this Run
    if ($owner != $user) {
      throw new \Exception("Run[{$run->getId()}] is owned by User[{$owner->getName()}]. Only that user can access this Run.", 3);
    }

    // TODO If Audit Log Active - Log Close Attempt
    $b_flush = false;
    switch ($run->getState()) {
      case 0: // Hasn't been Run Before (Mark as closed and Leave)
      case 1: // Being Runned 
        // Mark Run as Closed
        $run->setState(2);
        $b_flush = true;
        break;
      case 2: // Closed (Do Nothing)
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->getState()}].", 3);
    }

    // Save Changes Back to Database
    if ($b_flush) {
      $this->getEntityManager()->flush();
    }

    return $run;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCurrentAction($parameters) {
    $user = $parameters['user'];
    $run = $parameters['run'];
    $owner = $run->getUser();

    // Test if the Current User Can Access this Run
    if ($owner != $user) {
      throw new \Exception("Run[{$run->getId()}] is owned by User[{$owner->getName()}]. Only that user can access this Run.", 3);
    }

    switch ($run->getState()) {
      case 0: // Hasn't been Run Before (Mark as closed and Leave)
        throw new \Exception("Run[{$run->getId()}] hasn't been started.", 3);
      case 1: // Being Runned 
      case 2: // Run Closed
        $link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->findLink($run,
                                                                                        $run->getSequence());
        if (isset($link)) {
          $run->setSequence($link->getSequence());
        } else {
          throw new \Exception("Run[{$run->getId()}] has no Tests Associated for Running.", 3);
        }

        // Get the 1st Step in the Test
        $test = $link->getTest();

        // Get Current Running Sequence
        $sequence = SessionManager::getVariable('tc_run_test_step');
        if (isset($sequence)) { // Sequence Set - Find that Step
          $step = $this->getRepository('TestCenter\ModelBundle\Entity\TestStep')->findStep($test,
                                                                                           $sequence);
        }

        if (!isset($step)) { // Failover - If Current Sequence No Longer Exists Start OVer
          $step = $this->getRepository('TestCenter\ModelBundle\Entity\TestStep')->firstStep($test);
        }

        if (!isset($step)) {
          throw new \Exception("Run[{$step->getId()}] has no Steps Associated for Running.", 3);
        }

        // Set Session Variables
        SessionManager::setVariable('tc_run_test_step', $step->getSequence());

        break;
      default:
        throw new \Exception("Unknown Run State[{$run->getState()}].", 3);
    }

    return $step;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doNextAction($parameters) {
    $user = $parameters['user'];
    $run = $parameters['run'];
    $owner = $run->getUser();

    // Test if the Current User Can Access this Run
    if ($owner != $user) {
      throw new \Exception("Run[{$run->getId()}] is owned by User[{$owner->getName()}]. Only that user can access this Run.", 3);
    }

    // TODO If Audit Log Active - Log Close Attempt
    $b_flush = false;
    $next_step = null;
    switch ($run->getState()) {
      case 0: // Hasn't been Run Before
        throw new \Exception("Run[{$run->getId()}] hasn't been started.", 3);
      case 1: // Being Runned 
        // TODO - Mark the Current Link as Been Run (With the Appropriate Result Code)
        // TODO - Mark Current Test as Been Runned (With the Appropriate Result Code)
        // Get the Current Running Sequence
        $current_sequence = $run->getSequence();
        // Get the Current Link, so we can update it.
        $link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->findLink($run,
                                                                                        $current_sequence);
        if (!isset($link)) {
          throw new \Exception("Current Sequence #[$current_sequence] Invalid for Run[{$run->getId()}].", 3);
        }

        // Get the Next Possible Step for the Current Test
        $current_step = SessionManager::getVariable('tc_run_test_step');
        if (!isset($current_step)) {
          throw new \Exception("No Current Step set for the Active Run[{$run->getId()}]. Lost Session? Restart the Run.", 3);
        }

        $next_step = $this->getRepository('TestCenter\ModelBundle\Entity\TestStep')->nextStep($link->getTest(),
                                                                                              $current_step);
        if (!isset($next_step)) { // Reached End of Test - Close and Move on
          // Update the Link Status and Code (Optional, Default is 0)
          $link->setStatus($parameters['status']);
          if (isset($parameters['code'])) {
            $link->setCode($parameters['code']);
          }

          // Update the Link Comment (Optional, Default is no comment)
          if (isset($parameters['comment'])) {
            $link->setComment($parameters['comment']);
          }

          // Test if the Run Has Reached it's End
          $next_link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->nextLink($run);
          if (isset($next_link)) { // Position Run at Next Sequence
            // Get the 1st Step for the next Test in the Sequence
            $next_test = $next_link->getTest();
            $next_step = $this->getRepository('TestCenter\ModelBundle\Entity\TestStep')->firstStep($next_test);
            if (!isset($next_step)) {
              throw new \Exception("Test[{$next_test->getId()}] has no Steps Associated for Running.", 3);
            }

            // Change the Run's Current Running Test
            $run->setSequence($next_link->getSequence());

            // Save the Next Step (in the Test Sequence)
            SessionManager::setVariable('tc_run_test_step',
                                        $next_step->getSequence());
          } else { // Close the Run
            $run->setState(2);
            SessionManager::clearVariable('tc_run_next_step');
          }

          // Save Changes back to the Database
          $b_flush = true;
        } else { // Continue Running Test
          SessionManager::setVariable('tc_run_test_step',
                                      $next_step->getSequence());
        }

        // TODO Need to be able to add Documents to Links (So we can document failures, or even passes, with screenshots)
        // TODO Consider Splitting to Marking a Test/Sequence, in a Particular State, and the Next action, into 2 seperate actions.
        /* TODO Consider the need for changing a comment, on a link (i.e. adding 
         * a seperate command for setting/modyfing the comment. */
        break;
      case 2: // Run Closed
        throw new \Exception("Run[{$run->getId()}] is closed.", 3);
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->getState()}].", 3);
    }

    if ($b_flush) { // Save Changes Back to Database
      $this->getEntityManager()->flush();
    }

    return $next_step;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doPositionAction($parameters) {
    $user = $parameters['user'];
    $run = $parameters['run'];
    $owner = $run->getUser();

    // Test if the Current User Can Access this Run
    if ($owner != $user) {
      throw new \Exception("Run[{$run->getId()}] is owned by User[{$owner->getName()}]. Only that user can access this Run.", 3);
    }

    // TODO If Audit Log Active - Log Close Attempt
    $b_flush = false;
    switch ($run->getState()) {
      case 0: // Hasn't been Run Before
        throw new \Exception("Run[{$run->getId()}] hasn't been started.", 3);
      case 1: // Being Runned 
        $sequence = $parameters['sequence'];
        if ($sequence <= 0) { // Rewind to the Beginning (Get 1st Link)
          $link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->firstLink($run);
          if (!isset($link)) {
            throw new \Exception("Run[{$run->getId()}] has no Tests Associated for Running.", 3);
          }
        } else { // Get the Link to Hop to
          $link = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink')->findLink($run,
                                                                                          $sequence);
          if (!isset($link)) {
            throw new \Exception("Sequence #$sequence does not Exist in Run[{$run->getId()}].", 3);
          }
        }

        // Change the Current Sequence
        $run->setSequence($link->getSequence());
        $b_flush = true;
        break;
      case 2: // Closed (Do Nothing)
        throw new \Exception("Run[{$run->getId()}] is closed.", 3);
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->getState()}].", 3);
    }

    if ($b_flush) { // Save Changes Back to Database
      $this->getEntityManager()->flush();
    }

    return $run;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doLinkListAction($parameters) {
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink');

    return $repository->listLinks($parameters['run']);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doLinkCountAction($parameters) {
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\RunLink');

    return $repository->countLinks($parameters['run']);
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

    // For those Actions with Optional Run ID, verify and retrieve (if necessary) from the Session Variable
    switch ($action) {
      case 'Close':
      case 'Current':
      case 'LinkList':
      case 'LinkCount':
        if (!isset($parameters['id'])) {
          $id = SessionManager::getVariable('tc_run');
          if (!isset($id)) {
            throw new \Exception("Missing Required Parameter[id]", 2);
          };
          $parameters['id'] = $id;
        }
        break;
      case 'Next':
      case 'Position':
        $id = SessionManager::getVariable('tc_run');
        if (!isset($id)) {
          throw new \Exception("Have not Started any Runs", 2);
        };
        $parameters['id'] = $id;
        break;
    }

    // Get the Run for the Action
    $parameters = $this->inoutParameters($action,
                                         array(
      'Start', 'Close', 'Current', 'Next', 'Position', 'LinkList', 'LinkCount'
      ), null, $parameters, 'id', array('run', 'entity'), null,
                                         function($controller, $parameters, $in_value) {
        assert('isset($in_value) && is_integer($in_value)');

        $run = $controller->getRepository('TestCenter\ModelBundle\Entity\Run')->find($in_value);
        if (!isset($run)) {
          throw new \Exception("Run[$in_value] not found", 2);
        }

        return $run;
      });

    // Check if the Set Belongs to the Project
    if (isset($parameters['run']) && ($parameters['run']->getProject() != $project)) {
      throw new \Exception("Run[" . $parameters['run']->getId() . "] does not belong to the Current Project[{$project->getId()}]", 3);
    }

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
      case 'Start':
      case 'Close':
      case 'Current':
      case 'Next':
      case 'Position':
        if (isset($results)) {
          $return = $results->toArray();
        }
        break;
      case 'LinkList':
        $return = array();
        foreach ($results as $steps) {
          $id = $steps->getSequence();
          $return[$id] = $steps->toArray();
        }
        break;
    }

    return $return;
  }

}
