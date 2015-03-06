<?php
/**
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
namespace api\controller;

use \common\utility\Strings;

/**
 * Base Controller for Web Service (Small modifications to the working of the
 * Base Controller, to make it easier to develope web services).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class BaseServiceController extends BaseController {
  /* ---------------------------------------------------------------------------
   * OVERRIDE: Stages
   * ---------------------------------------------------------------------------
   */

  /**
   * STAGE 1: Early Initialization/Setup for Action (Things that don't require any
   * dereferencing of paramaters).
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function do_initAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: {action}Start (Default: startAction )
     * Perform zero or more checks, to validate the Required Session State
     * -- Definition function {name} ($context) {
     *    ...
     *    return null; // Can't Modify Context in Function
     *  }
     *
     * - Exceptions
     * -- All Checks Generate an Exception in Case of Failure
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'startAction';
    $method = lcfirst($action) . 'Start';

    // Call the Function
    $this->callMethod($method, $defaultMethod, $context);

    /* Function: sessionChecks{Action} (Default: sessionChecks)
     * Perform zero or more checks, to validate the Required Session State
     * -- Definition function {name} ($context) {
     *    ...
     *    return $context; // Context can modified during function execution
     *  }
     *
     * - Exceptions
     * -- All Checks Generate an Exception in Case of Failure
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'sessionChecks';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, $context);
    $context = isset($return) ? $return : $context;

    return parent::do_initAction(isset($return) ? $return : $context);
  }

  /**
   * STAGE 3: Execute the Action and Set the Action Result in the Context.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function do_call($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: privilegeChecks{action} (Default: privilegeChecks)
     * Perform zero or more checks, to validate the Privileges/Permissions
     * -- Definition function {name} ($context) {
     *    ...
     *    return $context; // Context can modified during function execution
     *  }
     *
     * - Exceptions
     * -- All Checks Generate an Exception in Case of Failure
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'privilegeChecks';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, $context);

    /* Function: contextChecks{action} (Default: contextChecks)
     * Perform zero or more checks, to validate the Context of the Action,
     * before Calling Action
     * -- Definition function {name} ($context) {
     *    ...
     *    return $context; // Context can modified during function execution
     *  }
     *
     * - Exceptions
     * -- All Checks Generate an Exception in Case of Failure
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'contextChecks';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, isset($return) ? $return : $context);
    
    // Call the Parents Class
    return parent::do_call(isset($return) ? $return : $context);
  }

  /* ---------------------------------------------------------------------------
   * UTILITY FUNCTIONS: Checks
   * ---------------------------------------------------------------------------
   */

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkInSession() {
    if (!$this->sessionManager->isActive()) {
      throw new \Exception('No Active Session.', 2);
    }

    return true;
  }

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkLoggedIn() {
    if (!$this->sessionManager->isLoggedIn()) {
      throw new \Exception('No Active Session.', 3);
    }

    return true;
  }
  
  /**
   * @param $options
   * @return array
   */
  protected function optionsToArray($options) {

    /* $options Format
     * option_1=value_1;option_2=value_2;....;option_n=value_n
     *
     * if value_i is empty, use empty string as value ''
     *
     * Escape Characters
     * escape of ; is ;;
     * escape of = is ==
     */
    $options = Strings::nullOnEmpty($options);
    $return = array();
    if (isset($options)) {
      $array = explode(';', $options);
      foreach ($array as $element) {
        $elements = explode('=', $element);
        $count = count($elements);
        if ($count > 0) {
          $key = Strings::nullOnEmpty($elements[0]);
          if ($count > 1) {
            $value = Strings::nullOnEmpty($elements[1]);
          }

          if (!isset($value)) {
            $value = '';
          }

          $return[$key] = $value;
        }
      }
    }

    return $return;
  }
  
  /**
   * Extract the Entity Header Information and Put it in the destination array
   * 
   * @param array $source Entity to Extract the Information From
   * @param array $destination Destiantion Array for Entity Information
   */
  protected function moveEntityHeader(&$source, &$destination) {
    // Copy Header to Destionation
    $destination['__type'] = $source['__type'];
    $destination['__entity'] = $source['__entity'];
    $destination['__key'] = $source['__key'];
    $destination['__fields'] = $source['__fields'];
    $destination['__display'] = $source['__display'];

    // Remove Header
    unset($source['__type']);
    unset($source['__entity']);
    unset($source['__key']);
    unset($source['__fields']);
    unset($source['__display']);
  }
  
}
