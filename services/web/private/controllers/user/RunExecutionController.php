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
use \common\utility\Strings;
use api\controller\BaseServiceController;

/**
 * Controller used to Manage Run Executions
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class RunExecutionController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Start/Continue Execution of the Specified Run.
   * 
   * @param integer $id Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function start($id) {
    // Create Action Context
    $context = new ActionContext('start');
    // Call Action
    return $this->doAction($context->setParameter('run:id', (integer) $id));
  }

  /**
   * Close Execution of the Specified/Session Run.
   * 
   * @param integer $id OPTIONAL Run's Unique Identifier (if not given Session 
   *   Run is Used)
   * @return string HTTP Body Response
   */
  public function close($id = null) {
    // Create Action Context
    $context = new ActionContext('close');
    // Call Action
    return $this->doAction($context->setIfNotNull('run:id', isset($id) ? (integer) $id : null));
  }

  /**
   * Retrieve the Step of the Specified/Session Run.
   * 
   * @param integer $id OPTIONAL Run's Unique Identifier (if not given Session 
   *   Run is Used)
   * @return string HTTP Body Response
   */
  public function current($id = null) {
    // Create Action Context
    $context = new ActionContext('current');
    // Call Action
    return $this->doAction($context->setIfNotNull('run:id', isset($id) ? (integer) $id : null));
  }

  /**
   * Terminate the Current Session's Run, Current Step, with the Given 
   * Status/Code/Comment, and position the Run at the next step.
   * 
   * @param integer $status Step's Final Execution Status
   * @param integer $code OPTIONAL Step's Final Execution Status Code (if not given
   *   0 is used as the DEFAULT)
   * @param string $comment OPTIONAL Step's Execution Comment
   * @return string HTTP Body Response
   */
  public function next($status, $code = null, $comment = null) {
    // Create Action Context
    $context = new ActionContext('next');
    $context = $context->
            setParameter('status', (integer) $status)->
            setIfNotNull('code', isset($code) ? (integer) $code : null)->
            setIfNotNull('comment', Strings::nullOnEmpty($comment));

    return $this->doAction('Next', $parameters);
  }

  /**
   * Re-position the Run at the Specified Sequence.
   * 
   * @param integer $sequence Next Sequence for Run Execution
   * @return string HTTP Body Response
   */
  public function position($sequence) {
    // Create Action Context
    $context = new ActionContext('position');
    // Call Action
    return $this->doAction($context->setIfNotNull('run:sequence', (integer) $sequence));
  }

  /**
   * List of Runs Steps belonging to the Specified/Session Run.
   * 
   * @param integer $id OPTIONAL Run's Unique Identifier (if not given Session 
   *   Run is Used)
   * @return string HTTP Body Response
   */
  public function linkList($id = null) {
    // Create Action Context
    $context = new ActionContext('link_list');
    // Call Action
    return $this->doAction($context->setIfNotNull('run:id', isset($id) ? (integer) $id : null));
  }

  /**
   * Count of Runs Steps belonging to the Specified/Session Run.
   * 
   * @param integer $id OPTIONAL Run's Unique Identifier (if not given Session 
   *   Run is Used)
   * @return string HTTP Body Response
   */
  public function linkCount($id = null) {
    // Create Action Context
    $context = new ActionContext('link_count');
    // Call Action
    return $this->doAction($context->setIfNotNull('run:id', isset($id) ? (integer) $id : null));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Start/Continue Execution of the Specified Run.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \TestStep Next Test Step to Execute
   * @throws \Exception On failure to perform the action
   */
  protected function doStartAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    // TODO If Audit Log Active - Log Start Attempt
    $b_flush = false;
    switch ($run->state) {
      case 0: // Hasn't been Run Before (Initialize State, and let user continue)
        $run->state = 1;
        $b_flush = true;
        break;
      case 1: // Run Opened (Nothing to do, just let the user continue)
        break;
      case 2: // Closed (Mark the Run as Open, and let the user continue)
        $run->state = 1;
        $b_flush = true;
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->state}].", 1);
    }

    // Run Opened - Get the Current Test    
    if ($run->state === 1) {
      $sequence = $run->sequence;
      if ($sequence <= 0) { // No Valid Sequence (Start from the Beginning)
        $entry = \PlayEntry::firstFirstByRun($run);
      } else { // Valid Sequence - Continue
        $entry = \PlayEntry::findBySequence($run, $sequence);
      }

      if (isset($entry)) {
        $run->sequence = $entry->sequence;
      } else {
        throw new \Exception("Run[{$run->id}] has no Tests Associated for Running.", 2);
      }
    }

    // Get the 1st Step in the Test
    $test = $entry->test;
    $step = \TestStep::firstStep($test);
    if (!isset($step)) {
      throw new \Exception("Run[{$run->id}] has no Steps Associated for Running.", 3);
    }

    // Set Session Variables
    $this->sessionManager->setVariable('tc_run', $run->id);
    $this->sessionManager->setVariable('tc_run_test_step', $step->sequence);

    // Did we save changes to the Run?
    if ($b_flush && ($run->save() === FALSE)) { // NO
      throw new \Exception("Failed to save changes to Run[{$run->id}].", 4);
    }

    return $step;
  }

  /**
   * Close Execution of the Specified Run.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \Run Next Run that was closed
   * @throws \Exception On failure to perform the action
   */
  protected function doCloseAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    // TODO If Audit Log Active - Log Close Attempt
    $b_flush = false;
    switch ($run->state) {
      case 0: // Hasn't been Run Before (Mark as closed and Leave)
      case 1: // Being Runned 
        // Mark Run as Closed
        $run->state = 2;
        $b_flush = tru1e;
        break;
      case 2: // Closed (Do Nothing)
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->state}].", 1);
    }

    // Did we save changes to the Run?
    if ($b_flush && ($run->save() === FALSE)) { // NO
      throw new \Exception("Failed to save changes to Run[{$run->id}].", 2);
    }

    return $run;
  }

  /**
   * Current Execution Position in the Specified Run.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \TestStep Next Test Step to Execute
   * @throws \Exception On failure to perform the action
   */
  protected function doCurrentAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    switch ($run->state) {
      case 0: // Hasn't been Run Before (Mark as closed and Leave)
        throw new \Exception("Run[{$run->id}] hasn't been started.", 1);
      case 1: // Being Runned 
      case 2: // Run Closed
        $entry = \PlayEntry::findEntry($run, $run->sequence);
        if (isset($entry)) {
          $run->sequence = $entry->sequence;
        } else {
          throw new \Exception("Run[{$run->id}] has no Tests Associated for Running.", 2);
        }

        // Get the 1st Step in the Test
        $test = $entry = test;

        // Get Current Running Sequence
        $sequence = $this->sessionManager->getVariable('tc_run_test_step');
        if (isset($sequence)) { // Sequence Set - Find that Step
          $step = \TestStep::findStep($test, $sequence);
        }

        if (!isset($step)) { // Failover - If Current Sequence No Longer Exists Start OVer
          $step = \TestStep::firstStep($test);
        }

        if (!isset($step)) {
          throw new \Exception("Run[{$step->id}] has no Steps Associated for Running.", 3);
        }

        // Set Session Variables
        $this->sessionManager->setVariable('tc_run_test_step', $step->sequence);

        break;
      default:
        throw new \Exception("Unknown Run State[{$run->state}].", 3);
    }

    return $step;
  }

  /**
   * Terminate the Execution of the Current Step and Pass to the Next Step
   * in the Specified Run.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \TestStep Next Test Step to Execute
   * @throws \Exception On failure to perform the action
   */
  protected function doNextAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    // TODO If Audit Log Active - Log Close Attempt
    $b_flush = false;
    $next_step = null;
    switch ($run->state) {
      case 0: // Hasn't been Run Before
        throw new \Exception("Run[{$run->id}] hasn't been started.", 1);
      case 1: // Being Runned 
        // TODO - Mark the Current Link as Been Run (With the Appropriate Result Code)
        // TODO - Mark Current Test as Been Runned (With the Appropriate Result Code)
        // Get the Current Running Sequence
        $current_sequence = $run->sequence;

        // Do we have a Current Positon for the Run?
        $entry = \PlayEntry::findEntry($run, $current_sequence);
        if (!isset($entry)) { // NO
          throw new \Exception("Current Sequence #[$current_sequence] Invalid for Run[{$run->id}].", 2);
        }

        // Get the Next Possible Step for the Current Test
        $current_step = $this->sessionManager->getVariable('tc_run_test_step');
        // Do we have a Current Step to Run?
        if (!isset($current_step)) { // NO
          throw new \Exception("No Current Step set for the Active Run[{$run->id}]. Lost Session? Restart the Run.", 3);
        }

        $next_step = \TestStep::nextStep($entry->test, $current_step);
        if (!isset($next_step)) { // Reached End of Test - Close and Move on
          // Update the Link Status and Code (Optional, Default is 0)
          $entry->status = $context->getParameter('status');

          // Do we have a Status Code Given?
          $code = $context->getParameter('code');
          if (isset($code)) { // YES
            $entry->code = $code;
          }

          // Do we have a Execution Comment Given?
          $comment = $context->getParameter('comment');
          if (isset($comment)) { // YES
            $link->comment = $comment;
          }

          // Did we save the changes back to the Database?
          if ($entry->save() === FALSE) {
            throw new \Exception("Failed to save changes to Current Run Sequence[{$entry->id}].", 4);
          }

          // Test if the Run Has Reached it's End
          $next_link = \PlayEntry::nextLink($run);
          if (isset($next_link)) { // Position Run at Next Sequence
            // Get the 1st Step for the next Test in the Sequence
            $next_test = $next_link->test;
            $next_step = \TestStep::firstStep($next_test);

            // Do we have next step?
            if (!isset($next_step)) { // NO
              throw new \Exception("Test[{$next_test->id}] has no Steps Associated for Running.", 5);
            }

            // Change the Run's Current Running Test
            $run->sequence = $next_link->sequence;

            // Save the Next Step (in the Test Sequence)
            $this->sessionManager->setVariable('tc_run_test_step', $next_step->sequence);
          } else { // Close the Run
            $run->state = 2;
            $this->sessionManager->clearVariable('tc_run_next_step');
          }

          // Save Changes back to the Database
          $b_flush = true;
        } else { // Continue Running Test
          $this->sessionManager->setVariable('tc_run_test_step', $next_step->sequence);
        }

        // TODO Need to be able to add Documents to Links (So we can document failures, or even passes, with screenshots)
        // TODO Consider Splitting to Marking a Test/Sequence, in a Particular State, and the Next action, into 2 seperate actions.
        /* TODO Consider the need for changing a comment, on a link (i.e. adding 
         * a seperate command for setting/modyfing the comment. */
        break;
      case 2: // Run Closed
        throw new \Exception("Run[{$run->id}] is closed.", 6);
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->state}].", 7);
    }

    // Did we save changes to the Run?
    if ($b_flush && ($run->save() === FALSE)) { // NO
      throw new \Exception("Failed to save changes to Run[{$run->id}].", 8);
    }

    return $next_step;
  }

  /**
   * Position the Specified Run at the Given Sequence Position.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \Run Run Entity
   * @throws \Exception On failure to perform the action
   */
  protected function doPositionAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    // TODO If Audit Log Active - Log Close Attempt
    $b_flush = false;
    switch ($run->state) {
      case 0: // Hasn't been Run Before
        throw new \Exception("Run[{$run->id}] hasn't been started.", 1);
      case 1: // Being Runned 
        $sequence = $context->getParameter('sequence');

        // Is the New Sequence Position Negative?
        if ($sequence <= 0) { // YES: Rewind to the Beginning (Get 1st Link)
        $entry = \PlayEntry::firstFirstByRun($run);
          // Did we find the Next Sequence?
          if (!isset($entry)) { // NO
            throw new \Exception("Run[{$run->id}] has no Tests Associated for Running.", 2);
          }
        } else { // NO: Get the Link to Hop to
        $entry = \PlayEntry::findBySequence($run, $sequence);
          // Did we find the Next Sequence?
          if (!isset($entry)) { // NO
            throw new \Exception("Sequence #{$sequence} does not Exist in Run[{$run->id}].", 3);
          }
        }

        // Change the Current Sequence
        $run->sequence = $entry->sequence;
        $b_flush = true;
        break;
      case 2: // Closed (Do Nothing)
        throw new \Exception("Run[{$run->id}] is closed.", 3);
        break;
      default:
        throw new \Exception("Unknown Run State[{$run->state}].", 3);
    }

    // Did we save changes to the Run?
    if ($b_flush && ($run->save() === FALSE)) { // NO
      throw new \Exception("Failed to save changes to Run[{$run->id}].", 4);
    }

    return $run;
  }

  /**
   * List of Runs Steps belonging to the Specified/Session Run.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \RunLink[] Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkListAction($context) {
    return \PlayEntry::listInRun($context->getParameter('run'));
  }

  /**
   * Count of Runs Steps belonging to the Specified/Session Project.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return integer Action Result
   * @throws \Exception On any type of failure condition
   */
  protected function doLinkCountAction($context) {
    return \PlayEntry::countInRun($context->getParameter('run'));
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
    // TODO Required Verification that User Has Required Permission against this Organization and/or Project for the Actions
    // Need a Session for all the Session Commands
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();
    $this->sessionManager->checkProject();

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
    $run = $context->getParameter('run');
    $project = $context->getParameter('project');

    // Does the Run Belong to the Project?
    if ($run->project !== $project->id) {
      throw new \Exception("Run[{$run->name}] is Not Part of the Project[{$project->name}]", 1);
    }

    return $context;
  }

  /**
   * Check if the user has the required permissions to perform the action.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  public function priviledgeChecks($context) {
    // Do Access Checks
    return $this->onActionDo($context, array('Start', 'Close', 'Current', 'Next', 'Position'), function($controller, $context, $action) {
              // Get Required Context Parameters
              $user = $context->getParameter('user');
              $run = $context->getParameter('run');

              // Test if the Current User Can Access this Run
              if ($run->owner != $user->id) {
                throw new \Exception("Run[{$run->id}] is owned by User[{$run->owner}]. Only that user can access this Run.", 1);
              }

              return null;
            });
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

    // Process 'run:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'run:id', function($controller, $context, $action, $value) {
      // Get Run for Action
      $run = \Run::find($value);
      if (!isset($run)) {
        throw new \Exception("Run [$value] not found", 1);
      }

      // Save the Run for the Action
      return $context->setParameter('run', $run);
    }, array('Close', 'Current', 'Close', 'Position', 'LinkList', 'LinkCount'), 'Start', function($controller, $context, $action) {
      // Get the Project for the Active Session
      $id = $this->sessionManager->getVariable('tc_run');
      if (!isset($id)) {
        throw new \Exception("Have not Started any Runs", 2);
      }

      // Does the Run Exist?
      $run = \Run::find($id);
      if (!isset($run)) { // NO
        throw new \Exception("Run [$id] not found", 1);
      }

      // Save the Run for the Action
      return $context->setParameter('run', $run);
    });

    // Get the Project for the Active Session
    $id = $this->sessionManager->getProject();
    $project = \Project::findFirst($id);

    // Did we find the project?
    if ($user === FALSE) { // NO
      throw new \Exception("Project [$id] not found", 6);
    }
    $context->setParameter('project', $project);

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 6);
    }

    return $context->setParameter('user', $user);
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
    $action = $context->getAction();
    assert('isset($action)');
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
          $id = $steps->sequence;
          $return[$id] = $steps->toArray();
        }
        break;
    }

    return $return;
  }

}
