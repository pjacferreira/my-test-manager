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
use api\controller\EntityServiceController;

/**
 * Controller used to Manage Run Execution
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class PlayerController extends EntityServiceController {

  protected static $instance = null;

  /**
   * Singleton Pattern - Get Instance of the Controller
   * 
   * @return RunsController Instance of Controller
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new PlayerController();
    }

    return self::$instance;
  }

  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Start/Continue Execution of the Specified Run.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function runOpen($run) {
    // Create Action Context
    $context = new ActionContext('open');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Close Execution of the Specified/Session Run.
   * 
   * @param integer $run Run's Unique Identifier
   * @param integer $code [OPTIONAL: Project Default will be used] Run's Pass/Fail Code
   * @return string HTTP Body Response
   */
  public function runClose($run, $code = null) {
    // Create Action Context
    $context = new ActionContext('close');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run)
      ->setOptionalInteger('run:run_code', $code);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the 1st Test / 1st Step.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testFirst($run) {
    // Create Action Context
    $context = new ActionContext('test_first');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Position to the, Previous Test / 1st Step, if any.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testPrevious($run) {
    // Create Action Context
    $context = new ActionContext('test_previous');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Position to the, Next Test / 1st Step, if any.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testNext($run) {
    // Create Action Context
    $context = new ActionContext('test_next');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the Last Test / 1st Step.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testLast($run) {
    // Create Action Context
    $context = new ActionContext('test_last');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the Current Test / 1st Step.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testStepFirst($run) {
    // Create Action Context
    $context = new ActionContext('test_step_first');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Position to the, Current Test / Previous Step, if any.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testStepPrevious($run) {
    // Create Action Context
    $context = new ActionContext('test_step_previous');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Position to the, Current Test / Next Step, if any.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testStepNext($run) {
    // Create Action Context
    $context = new ActionContext('test_step_next');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the Current Test / Last Step.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function testStepLast($run) {
    // Create Action Context
    $context = new ActionContext('test_step_last');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the 1st Possible Step,
   * even if it is in a different Test.
   * NOTE: If the Current Run has only 1 Test with 1 Step then First Step === Last Step
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function stepFirst($run) {
    // Create Action Context
    $context = new ActionContext('step_first');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the Previous Step, if any,
   * even if it is in a different Test.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function stepPrevious($run) {
    // Create Action Context
    $context = new ActionContext('step_previous');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the Next Possible Step, if any,
   * even if it is in a different Test.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function stepNext($run) {
    // Create Action Context
    $context = new ActionContext('step_next');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move the Run's Current Play Entry Position to the Last Possible Step,
   * even if it is in a different Test.
   * NOTE: If the Current Run has only 1 Test with 1 Step then First Step === Last Step
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function stepLast($run) {
    // Create Action Context
    $context = new ActionContext('step_last');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * List the Play Entry Steps, in order, for a Run
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function listSteps($run) {
    // Create Action Context
    $context = new ActionContext('list_steps');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count the Play Entry Steps for a Run
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function countSteps($run) {
    // Create Action Context
    $context = new ActionContext('count_steps');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Get the Run's Current Play Entry.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function currentEntry($run) {
    // Create Action Context
    $context = new ActionContext('current');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Get the Test for the Run's Current Play Entry.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function currentTest($run) {
    // Create Action Context
    $context = new ActionContext('current_test');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Get the Step for the Run's Current Play Entry.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function currentStep($run) {
    // Create Action Context
    $context = new ActionContext('current_step');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Mark the Current Step as Passed.
   * NOTE: Only Possible Pass Codes [000-099] or Pass With Warning [100-899]
   * will be allowed.
   * 
   * @param integer $run Run's Unique Identifier
   * @param integer $code [OPTIONAL: Project Default will be used] Run's Pass/Fail Code
   * @return string HTTP Body Response
   */
  public function stepPass($run, $code = 1) {
    // Create Action Context
    $context = new ActionContext('step_pass');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run)
      ->setOptionalInteger('player:run_code', $code);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Mark the Current Step as Failed.
   * NOTE: Only Possible Failed Codes will be allowed [900-999]
   * 
   * @param integer $run Run's Unique Identifier
   * @param integer $code [OPTIONAL: Project Default will be used] Run's Pass/Fail Code
   * @return string HTTP Body Response
   */
  public function stepFail($run, $code = 2) {
    // Create Action Context
    $context = new ActionContext('step_fail');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run)
      ->setOptionalInteger('player:run_code', $code);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Add/Remove Comment from Current Step for the given run.
   * 
   * @param integer $run Run's Unique Identifier
   * @return string HTTP Body Response
   */
  public function stepComment($run) {
    // Create Action Context
    $context = new ActionContext('step_comment');

    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setRequiredInteger('run:id', $run)
      ->setOptionalString('player:comment', $this->request->getPost('comment'));

    // Call Action
    return $this->doAction($context);
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
   * @return  \models\Run Run that was Opened
   * @throws \Exception On failure to perform the action
   */
  protected function doOpenAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    // TODO If Audit Log Active - Log Open Attempt
    /*
     * Step-by-Step
     * 1. Is run Open?
     * 1.1. NO: Open Run
     * 1.2. Mark Run for Flush
     */
    $flush = false;
    if ($run->state !== \models\Run::STATE_OPEN) {
      $run->state = \models\Run::STATE_OPEN;
      $flush = true;
    }


    /*
     * 2. Is the Current PLE Set?
     * 2.1. NO: Find the First PLE for the RUN
     * 2.2. Set the Run Current PLE
     * 2.3. Mark Run for Flush
     */
    if (!isset($run->current_ple)) {
      $ple = \models\PlayEntry::first($run);
      if ($ple === FALSE) {
        throw new \Exception("[SYSTEM ERROR] Run[{$run->id}] has an invalid play list.", 1);
      }

      $run->current_ple = $ple->id;
      $flush = true;
    }

    /*
     * 3. Do we have to save changes to the run?
     * 3.1 YES: Flush the Changes
     */
    if ($flush) {
      $this->setModifier($run, $context->getParameter('user'));
      $this->_persist($run);
    }

    /*
     * 4. Return the Open Run
     */
    return $run;
  }

  /**
   * Close Execution of the Specified Run.
   * Note: If the Run has no Run Code Set, it will be marked as incomplete, even
   * if all the Steps have been executed.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\Run Run that was Closed
   * @throws \Exception On failure to perform the action
   */
  protected function doCloseAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $code = $context->getParameter('run:run_code');
    $comment = $context->getParameter('run:comment');

    // TODO If Audit Log Active - Log Close Attempt
    /* Step-by-Step
     * 1. Is Run Open?
     * 1.1. YES: Does the Run have a Run Code Set?
     * 1.1.1 NO: Use Project Settings to Mark the Run as Incomplete
     * 1.1.2 Mark the Run for Flush
     */
    $flush = false;
    if ($run->state === \models\Run::STATE_OPEN) {
      // Is a Code Set?
      if (!isset($code)) { // NO: Use Project Defaults
        $settings = $context->getParameter('project-settings');
        $code = $settings->run_incomplete;
      }

      $run->run_code = $code;

      // Does the Run Already Have a Comment?
      if (isset($run->comment)) { // YES
        if (isset($comment)) {
          if (count($comment) > 0) {
            $run->comment = $comment;
          } else {
            $comment = null;
          }
        }
      } else { // NO
        // Do we want to add a comment?
        if (isset($comment) && count($comment) > 0) { // YES
          $run->comment = $comment;
        }
      }

      $run->state = \models\Run::STATE_CLOSED;
      $flush = true;
    }


    /*
     * 2. Do we have to save changes to the run?
     * 2.1 YES: Flush the Changes
     */
    if ($flush) {
      $this->setModifier($run, $context->getParameter('user'));
      $this->_persist($run);
    }

    /*
     * 3. Return Closed Run
     */
    return $run;
  }

  /**
   * Position the Current PLE to the Run's Current 1st Test, 1st Step
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestFirstAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the First Play Entry for the Run
    $first = \models\PlayEntry::first($ple->run);

    // Do we need to Modify the Run's Current PLE?
    if ($run->current_ple !== $first->id) { // YES
      // Is the Current Test the Last Test?
      if ($ple->test !== $last->test) { // NO
        $run->current_ple = $first->id;
        $this->_persist($run);
        return $first;
      }
      // ELSE: Current Test is First Test - So don't move the ple
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Previous Test, 1st Step (if any)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestPreviousAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the Previous Play Entry for the Run
    $previous = \models\PlayEntry::previousTest($ple);

    // Do we need to Modify the Run's Current PLE?
    if ($previous !== FALSE) { // YES
      // Is the Current Test the Last Test?
      if ($ple->test !== $last->test) { // NO
        $run->current_ple = $previous->id;
        $this->_persist($run);
        return $previous;
      }
      // ELSE: Current Test is First Test - So don't move the ple
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Next Test, 1st Step (if any)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestNextAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Have we assigned a pass/fail code to the current step?
    if (!isset($ple->run_code)) { // NO: Can't move forward then
      throw new \Exception("Current Step has not been terminated.", 1);
    }

    // Get the Next Play Entry for the Run/Test
    $next = \models\PlayEntry::nextTest($ple);

    // Do we need to Modify the Run's Current PLE?
    if ($next !== FALSE) { // YES
      // Is the Current Test the Last Test?
      if ($ple->test !== $last->test) { // NO
        $run->current_ple = $next->id;
        $this->_persist($run);
        return $next;
      }
      // ELSE: Current Test is Last Test - So don't move the ple
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Last Test, 1st Step
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestLastAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the Last Play Entry for the Run/Test
    $last = \models\PlayEntry::lastTest($ple->run, $ple->test);

    // Do we need to Modify the Run's Current PLE?
    if ($run->current_ple !== $last->id) { // YES
      // Is the Current Test the Last Test?
      if ($ple->test !== $last->test) { // NO
        $run->current_ple = $last->id;
        $this->_persist($run);
        return $last;
      }
      // ELSE: Current Test is Last Test - So don't move the ple
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Current Test, 1st Step
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestStepFirstAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the First Play Entry for the Run
    $first = \models\PlayEntry::firstByTest($ple->run, $ple->test);

    // Do we need to Modify the Run's Current PLE?
    if ($run->current_ple !== $first->id) { // YES
      $run->current_ple = $first->id;
      $this->_persist($run);
      return $first;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Current Test, Previous Step (if any)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestStepPreviousAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the Previous Play Entry for the Run
    $previous = \models\PlayEntry::previousByTest($ple);

    // Do we need to Modify the Run's Current PLE?
    if ($previous !== FALSE) { // YES
      $run->current_ple = $previous->id;
      $this->_persist($run);
      return $previous;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Current Test, Next Step (if any)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestStepNextAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Have we assigned a pass/fail code to the current step?
    if (!isset($ple->run_code)) { // NO: Can't move forward then
      throw new \Exception("Current Step has not been terminated.", 1);
    }

    // Get the Next Play Entry for the Run/Test
    $next = \models\PlayEntry::nextByTest($ple);

    // Do we need to Modify the Run's Current PLE?
    if ($next !== FALSE) { // YES
      $run->current_ple = $next->id;
      $this->_persist($run);
      return $next;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Run's Current Test, Last Step
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doTestStepLastAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the Last Play Entry for the Run/Test
    $last = \models\PlayEntry::lastByTest($ple->run, $ple->test);

    // Do we need to Modify the Run's Current PLE?
    if ($run->current_ple !== $last->id) { // YES
      $run->current_ple = $last->id;
      $this->_persist($run);
      return $last;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the 1st Step for the 1st Test of the Run
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepFirstAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the First Play Entry for the Run
    $first = \models\PlayEntry::first($ple->run);

    // Do we need to Modify the Run's Current PLE?
    if ($run->current_ple !== $first->id) { // YES
      $run->current_ple = $first->id;
      $this->_persist($run);
      return $first;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Previous Step in the Run (independently
   * of the test)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepPreviousAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the Previous Play Entry for the Run
    $previous = \models\PlayEntry::previous($ple);

    // Do we need to Modify the Run's Current PLE?
    if ($previous !== FALSE) { // YES
      $run->current_ple = $previous->id;
      $this->_persist($run);
      return $previous;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Next Step in the Run (independently
   * of the test)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepNextAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Have we assigned a pass/fail code to the current step?
    if (!isset($ple->run_code)) { // NO: Can't move forward then
      throw new \Exception("Current Step has not been terminated.", 1);
    }

    // Get the Next Play Entry for the Run
    $next = \models\PlayEntry::next($ple);

    // Do we need to Modify the Run's Current PLE?
    if ($next !== FALSE) { // YES
      $run->current_ple = $next->id;
      $this->_persist($run);
      return $next;
    }

    return $ple;
  }

  /**
   * Position the Current PLE to the Last Step in the Run (independently
   * of the test)
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepLastAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the First Play Entry for the Run
    $last = \models\PlayEntry::last($run);

    // Do we need to Modify the Run's Current PLE?
    if ($run->current_ple !== $last->id) { // YES
      $run->current_ple = $last->id;
      $this->_persist($run);
      return $last;
    }

    return $ple;
  }

  /**
   * List Steps in a Run
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Test[] Test Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListStepsAction($context) {
    $run = $context->getParameter('run');
    return \models\PlayEntry::listByRun($run);
  }

  /**
   * Count Steps in a Run
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Tests
   * @throws \Exception On failure to perform the action
   */
  protected function doCountStepsAction($context) {
    $run = $context->getParameter('run');
    return \models\PlayEntry::countByRun($run);
  }

  /**
   * Get the Test Associated with the Current Play Entry
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\Test Test for Current Play Entry
   * @throws \Exception On failure to perform the action
   */
  protected function doCurrentAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');

    // Get Current Play Entry for a Run
    // Has the run been opened once?
    if (isset($run->current_ple)) { // YES
      $ple = \models\PlayEntry::findFirst($run->current_ple);
    } else { // NO
      $ple = \models\PlayEntry::findFirstByRun($run);
    }

    if ($ple === FALSE) {
      throw new \Exception("[SYSTEM ERROR] Run[{$run->id}] has an invalid play list.", 2);
    }

    return $ple;
  }

  /**
   * Get the Test Associated with the Current Play Entry
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\Test Test for Current Play Entry
   * @throws \Exception On failure to perform the action
   */
  protected function doCurrentTestAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the test associated with the Play entry
    $project = $context->getParameter('project');
    $test = \models\Test::findInProject($project, $ple->test);
    if ($test === FALSE) {
      throw new \Exception("[SYSTEM ERROR] Run[{$run->id}] has an invalid play list.", 2);
    }

    return $test;
  }

  /**
   * Get the Step Associated with the Current Play Entry
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\TestStep Test Step for Current Play Entry
   * @throws \Exception On failure to perform the action
   */
  protected function doCurrentStepAction($context) {
    // Get Required Action Parameters
    $run = $context->getParameter('run');
    $ple = $context->getParameter('ple');

    // Get the test associated with the Play entry
    $project = $context->getParameter('project');
    $step = \models\TestStep::findInProject($project, $ple->step);
    if (!isset($step)) {
      throw new \Exception("[SYSTEM ERROR] Run[{$run->id}] has an invalid play list.", 2);
    }

    return $step;
  }

  /**
   * Mark the Current Play Entry as Passed
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepPassAction($context) {
    // Get Required Action Parameters
    $ple = $context->getParameter('ple');
    $code = $context->getParameter('player:run_code');
    $comment = $context->getParameter('player:comment');

    // Is a Code Set?
    if (!isset($code)) { // NO: Use Project Defaults
      $settings = $context->getParameter('project-settings');
      $code = $settings->step_pass;
    }

    // Are we modifying the Run Code for the Step?
    $flush = false;
    if ($code !== $ple->run_code) { // YES
      $ple->run_code = $code;
      $flush = true;
    }

    // Does the Step Already Have a Comment?
    if (isset($ple->comment)) { // YES
      // Set or Clear the Current Comment
      $ple->comment = isset($comment) && count($comment) ? $comment : null;
      $flush = true;
    } else { // NO
      // Do we want to add a comment?
      if (isset($comment) && count($comment) > 0) { // YES
        $ple->comment = $comment;
        $flush = true;
      }
    }

    // Do we need to Modify the PLE?
    if ($flush) { // YES
      $this->setModifier($ple, $context->getParameter('user'));
      $this->_persist($ple);
    }

    return $ple;
  }

  /**
   * Mark the Current Play Entry as Failed.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepFailAction($context) {
    // Get Required Action Parameters
    $ple = $context->getParameter('ple');
    $code = $context->getParameter('player:run_code');
    $comment = $context->getParameter('player:comment');

    // Is a Code Set?
    if (!isset($code)) { // NO: Use Project Defaults
      $settings = $context->getParameter('project-settings');
      $code = $settings->step_fail;
    }

    // Are we modifying the Run Code for the Step?
    $flush = false;
    if ($code !== $ple->run_code) { // YES
      $ple->run_code = $code;
      $flush = true;
    }

    // Does the Step Already Have a Comment?
    if (isset($ple->comment)) { // YES
      // Set or Clear the Current Comment
      $ple->comment = isset($comment) && count($comment) ? $comment : null;
      $flush = true;
    } else { // NO
      // Do we want to add a comment?
      if (isset($comment) && count($comment) > 0) { // YES
        $ple->comment = $comment;
        $flush = true;
      }
    }

    // Do we need to Modify the PLE?
    if ($flush) { // YES
      $this->setModifier($ple, $context->getParameter('user'));
      $this->_persist($ple);
    }

    return $ple;
  }

  /**
   * Mark the Current Play Entry as Failed.
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  \models\PlayEntry New Current PlayEntry
   * @throws \Exception On failure to perform the action
   */
  protected function doStepCommentAction($context) {
    // Get Required Action Parameters
    $ple = $context->getParameter('ple');
    $comment = $context->getParameter('player:comment');

    // Are we modifying the Run Code for the Step?
    $flush = false;

    // Does the Step Already Have a Comment?
    if (isset($ple->comment)) { // YES
      // Set or Clear the Current Comment
      $ple->comment = isset($comment) && count($comment) ? $comment : null;
      $flush = true;
    } else { // NO
      // Do we want to add a comment?
      if (isset($comment) && count($comment) > 0) { // YES
        $ple->comment = $comment;
        $flush = true;
      }
    }

    // Do we need to Modify the PLE?
    if ($flush) { // YES
      $this->setModifier($ple, $context->getParameter('user'));
      $this->_persist($ple);
    }

    return $ple;
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
    // Do Access Checks
    return $this->onActionDo($context, [
        'TestFirst', 'TestPrevious', 'TestNext', 'TestLast',
        'TestStepFirst', 'TestStepPrevious', 'TestStepNext', 'TestStepLast',
        'StepFirst', 'StepPrevious', 'StepNext', 'StepLast',
        'StepPass', 'StepFail'
        ], function($controller, $context, $action) {
        // Get Required Context Parameters
        $run = $context->getParameter('run');

        // Is the Run Open?
        if ($run->state !== \models\Run::STATE_OPEN) { // NO
          throw new \Exception("Action[{$action}] can only be performed on an Open Run.", 1);
        }

        return null;
      });
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
    return $this->onActionDo($context, '*', function($controller, $context, $action) {
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

    // Get the User for the Active Session
    $user = $this->sessionManager->getUser();
    $user = \models\User::findFirst($user['id']);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 1);
    }
    $context = $context
      ->setParameter('user', $user)
      ->setParameter('cm_user', $user);

    // Get Project for Session
    $project = $this->sessionManager->getProject();
    $project = \models\Project::findFirst($project['id']);
    if ($project === FALSE) { // NO
      throw new \Exception("Session Project [$id] is invalid.", 2);
    }
    $context = $context->setParameter('project', $project);

    // Process 'run:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'run:id', function($controller, $context, $action, $value) {
      // Get Working Project
      $project = $context->getParameter('project');

      // Does the Set with the given ID exist?
      $run = \models\Run::findInProject($project, $value);
      if ($run === FALSE) { // NO
        throw new \Exception("Set [$value] not found in Project [{$project->id}]", 4);
      }

      // Save the Run for the Action
      $context->setParameter('run', $run);

      return $context;
    }, null, '*');

    // Get the Current Play Entry for the Given Run 
    $context = $this->onActionDo($context, [
      'TestFirst', 'TestPrevious', 'TestNext', 'TestLast',
      'TestStepFirst', 'TestStepPrevious', 'TestStepNext', 'TestStepLast',
      'StepPrevious', 'StepNext',
      'CurrentTest', 'CurrentStep', 'StepPass', 'StepFail', 'StepComment'
      ], function($controller, $context, $action) {
      // Get Required Context Parameters
      $run = $context->getParameter('run');

      // Get the Current Play Entry for the Run
      $ple = \models\PlayEntry::findFirst($run->current_ple);
      if ($ple === FALSE) {
        throw new \Exception("[SYSTEM ERROR] Run[{$run->id}] has an invalid current state.", 1);
      }

      // Save the Play Entry for the Action
      $context->setParameter('ple', $ple);

      return $context;
    });

    $context = $this->onActionDo($context, [
      'Close', 'StepPass', 'StepFail'
      ], function($controller, $context, $action) {
      // Get Required Context Parameters
      $project = $context->getParameter('project');

      // Get the Settings for the Session Project
      $settings = \models\ProjectSettings::findFirstByProject($project->id);
      if ($settings === FALSE) {
        throw new \Exception("[SYSTEM ERROR] Project[{$project->id}] does not have default settings.", 1);
      }

      // Save the Play Entry for the Action
      $context->setParameter('project-settings', $settings);

      return $context;
    });

    return $context;
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
      case 'Open':
      case 'Close':
      case 'TestFirst':
      case 'TestPrevious':
      case 'TestNext':
      case 'TestLast':
      case 'StepFirst':
      case 'StepPrevious':
      case 'StepNext':
      case 'StepLast':
      case 'StepPass':
      case 'StepFail':
      case 'StepComment':
      case 'Current':
      case 'CurrentTest':
      case 'CurrentStep':
        if (isset($results)) {
          $return = $results->toArray();
        }
        break;
      case 'ListSteps':
        $return = [];
        $entities = [];
        $header = true;
        foreach ($results as $entity) {
          $entities[] = $entity->toArray($header);
          $header = false;
        }

        // Do we have entities to display?
        if (count($entities)) { // YES
          // Move the Entity Information to become Result Header
          $this->moveEntityHeader($entities[0], $return);
          $return['entities'] = $entities;
        } else {
          $return['entities'] = [];
        }

        // Create Base Entity Set Identified
        $return['__type'] = 'entity-set';
        break;
      default:
        $return = $results;
    }

    return $return;
  }

}
