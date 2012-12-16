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

namespace TestCenter\ServiceBundle\API;

use Library\StringUtilities;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Description of BaseController
 *
 * @author Paulo Ferreira
 */
class BaseController
  extends Controller {

  /**
   * 
   * @param type $action
   * @return type
   */
  public function getActionName($action) {
    assert('isset($action) && is_string($action)');

    $action = StringUtilities::nullOnEmpty($action);
    $action = explode('_', $action);
    $action = array_map('strtolower', $action);
    $action = array_map('ucfirst', $action);
    $action = implode($action);
    return $action;
  }

  /**
   * @param $action
   * @param null $parameters
   * @return null
   */
  protected function doAction($action, $parameters = null) {
    assert('!isset($parameters) || is_array($parameters)');

    // Clean-up Action
    $action = $this->getActionName($action);

    // Create an Empty Container to Pass On (Just for Consistency)
    if (!isset($parameters)) {
      $parameters = array();
    }

    try {
      // Initialize Action/Checks
      $parameters = $this->do_initAction($action, $parameters);

      // Pre-Action Validation/Transformation
      $parameters = $this->do_preAction($action, $parameters);

      // Action
      $results = $this->do_call($action, $parameters);

      // Post-Action Validation/Transformation
      $results = $this->do_postAction($action, $parameters, $results);

      // Render Results
      $results = $this->do_render($action, $results);

      // Mark Action as Complete
      $this->do_success($action);
    } catch (\Exception $e) {
      // Render Exception
      $results = $this->do_renderException($action, $parameters, $e);

      // Mark Action as Failed
      $this->do_failed($action);
    }

    return $results;
  }

  /**
   * @param $action
   * @param $parameters
   * @return null
   */
  protected function do_initAction($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    /* Function: initAction[{Action}]
     * Called to Initialize Action and Perform Startup Checks
     * - Parameters
     * -- For Default (Global initAction) Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * -- Action Specific Function (initAction{Action})
     * --- (1st) $parameters - parameters array (map)
     *
     * - Return
     * -- array (map) - representing the parameters, OR
     * -- null - in the case of no changes to the parameters
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'initAction';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, $action, $parameters);
    return isset($return) ? $return : $parameters;
  }

  /**
   * @param $action
   * @param $parameters
   * @return null
   */
  protected function do_preAction($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    /* Function: preAction[{Action}]
     * Called before Action Function is invoked
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $parameters - parameters array (map)
     *
     * - Return
     * -- array (map) - representing the parameters, OR
     * -- null - in the case of no changes to the parameters
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'preAction';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, $action, $parameters);
    return isset($return) ? $return : $parameters;
  }

  /**
   * @param $action
   * @param null $parameters
   * @return null
   */
  protected function do_call($action, $parameters = null) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    /* Function: do{Action}Action
     * Action to be Invoked
     * - Parameters
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $parameters - parameters array (map)
     *
     * - Return
     * -- array / object / value - representing the results of the action, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $method = 'do' . ucfirst($action) . 'Action';

    // Call the Function
    return $this->callMethod($method, null, $action, $parameters, null,
                             new \Exception("Missing or Invalid Action Method [$method].", 1));
  }

  /**
   * @param $action
   * @param $parameters
   * @param $results
   * @return null
   */
  protected function do_postAction($action, $parameters, $results) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    /* Function: postAction[{Action}]
     * Called after Action Function invoked
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * --- (3rd) $results - results of action call
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $parameters - parameters array (map)
     * --- (2nd) $results - results of action call
     *
     * - Return
     * -- array / object / value - representing the modified results, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $defaultMethod = 'postAction';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, $action, $parameters,
                                $results);
    return isset($return) ? $return : $results;
  }

  /**
   * @param $action
   * @param $results
   * @param string $format
   * @return null
   */
  protected function do_render($action, $results, $format = 'json') {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($format) && is_string($format)');

    /* Function: preRender[{Action}]
     * Called to Prepare Results for Rendering
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action  - the name of the action (all-lower-case)
     * --- (2nd) $results - results of action call
     * --- (3rd) $format  - output format for rendered results
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $results - results of action call
     * --- (2nd) $format  - output format for rendered results
     *
     * - Return
     * -- array / object / value - representing the modified results, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $defaultMethod = 'preRender';
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    $return = $this->callMethod($method, $defaultMethod, $action, $results,
                                $format);
    if (isset($return)) {
      $results = $return;
    }

    /* Function: render{Format}[{Action}]
     * Called to Prepare Results for Rendering
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $results - prepared results of action call
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $results - prepared results of action call
     *
     * - Return
     * -- array / object / value - representing the modified results, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $defaultMethod = 'render' . ucfirst($format);
    $method = $defaultMethod . ucfirst($action);

    // Call the Function
    return $this->callMethod($method, $defaultMethod, $action, $results);
  }

  /**
   * @param $action
   * @param $parameters
   * @param $exception
   * @param string $format
   * @return null
   */
  protected function do_renderException($action, $parameters, $exception,
                                        $format = 'json') {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($format) && is_string($format)');

    /* Function: render{Format}[{Action}]
     * Called to Prepare Results for Rendering
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action     - the name of the action (all-lower-case)
     * --- (2nd) $parameters - the parameters passed to the action
     * --- (3rd) $exception  - exception to render
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $parameters - the parameters passed to the action
     * --- (2nd) $exception  - exception to render
     *
     * - Return
     * -- array / object / value - representing the modified results, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $defaultMethod = 'renderException' . ucfirst($format);

    // Call the Function
    return $this->callMethod(null, $defaultMethod, $action, $parameters,
                             $exception);
  }

  /**
   * @param $action
   * @param $parameters
   * @param $results
   * @return null
   */
  protected function do_success($action) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');

    /* Function: {action}Success (Default: successAction )
     * Called after Action Function invoked
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * --- (3rd) $results - results of action call
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $parameters - parameters array (map)
     * --- (2nd) $results - results of action call
     *
     * - Return
     * -- array / object / value - representing the modified results, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $defaultMethod = 'successAction';
    $method = strtolower($action) . 'Success';

    // Call the Function
    return $this->callMethod($method, $defaultMethod, $action);
  }

  /**
   * @param $action
   * @param $parameters
   * @param $results
   * @return null
   */
  protected function do_failed($action) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');

    /* Function: {action}Failed (Default: failedAction )
      /* Function: failed[{Action}]
     * Called after Action Function invoked
     * - Parameters
     * -- For Default (Global preAction) Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * --- (3rd) $results - results of action call
     * -- Action Specific Function (preAction{Action})
     * --- (1st) $parameters - parameters array (map)
     * --- (2nd) $results - results of action call
     *
     * - Return
     * -- array / object / value - representing the modified results, OR
     *    null in the case of no results
     *
     * - Exceptions
     * -- Raise an exception on any fail state
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    // Define Methods to Call
    $defaultMethod = 'failedAction';
    $method = strtolower($action) . 'Failed';

    // Call the Function
    return $this->callMethod($method, $defaultMethod, $action);
  }

  /**
   * @param $action
   * @param $parameters
   * @param $in_actions
   * @param $function
   * @return array
   */
  protected function processParameter($action, $parameters, $in_actions,
                                      $function) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');
    assert('isset($in_actions) && is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    if (is_array($in_actions)) {
      if (array_search($action, $in_actions) !== FALSE) {
        return $function($this, $parameters);
      }
    } else if (is_string($in_actions)) {
      if ($action === $in_actions) {
        return $function($this, $parameters);
      }
    }

    return $parameters;
  }

  /**
   * @param $action
   * @param $results
   * @param $in_actions
   * @param $function
   * @return mixed
   */
  protected function processResults($action, $results, $in_actions, $function) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($in_actions) && is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    if (is_array($in_actions)) {
      if (array_search($action, $in_actions) !== FALSE) {
        return $function($this, $results);
      }
    } else if (is_string($in_actions)) {
      if ($action === $in_actions) {
        return $function($this, $results);
      }
    }

    return $results;
  }

  /**
   * @param $action
   * @param $in_actions
   * @param $parameters
   * @param $function
   * @return bool
   */
  protected function processChecks($action, $in_actions, $parameters, $function) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('!isset($in_actions) || is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    $call_function = false;
    if (!isset($in_actions)) {
      $call_function = true;
    } else if (is_array($in_actions)) {
      if (array_search($action, $in_actions) !== FALSE) {
        $call_function = true;
      }
    } else if (is_string($in_actions)) {
      if ($action === $in_actions) {
        $call_function = true;
      }
    }

    return $call_function ? $function($this, $action, $parameters) : $parameters;
  }

  /**
   * @param $method
   * @param $defaultMethod
   * @param $action
   * @param null $parameter1
   * @param null $parameter2
   * @param null $exception
   * @return null
   * @throws null
   */
  protected function callMethod($method, $defaultMethod, $action,
                                $parameter1 = null, $parameter2 = null,
                                $exception = null) {
    // Discover Method to Call
    if (isset($method) && is_callable(array($this, $method))) {
      // Method is Defined for Action
      if (isset($parameter2)) {
        return $this->$method($parameter1, $parameter2);
      }
      return $this->$method($parameter1);
    } else if (isset($defaultMethod) && is_callable(array($this, $defaultMethod))) {
      // Use Default Handler for Action
      if (isset($parameter2)) {
        return $this->$defaultMethod($action, $parameter1, $parameter2);
      } else if (isset($parameter1)) {
        return $this->$defaultMethod($action, $parameter1);
      } else {
        return $this->$defaultMethod($action);
      }

      // TODO Need a Better Way to Identify Parameters (i.e. if the action requires none, 1 or 2 parameters
    }

    // Exception Set - In case we have no method to call
    if (isset($exception)) {
      throw $exception;
    }

    return null;
  }

  /**
   * Default JSON Render Results
   *
   * @param $action
   * @param $results
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function renderJson($action, $results = null) {
    if (isset($results)) {
      $response = $this->render('TestCenterServiceBundle::service-base.json.twig',
                                array('results' => $results));
    } else {
      $response = $this->render('TestCenterServiceBundle::service-base.json.twig');
    }

    // Set Content Type to JSON
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * Default JSON Render Exception
   *
   * @param $action
   * @param $parameters
   * @param $exception
   * @return \Symfony\Component\HttpFoundation\Response
   */
  protected function renderExceptionJson($action, $parameters, $exception) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');
    assert('isset($exception)');

    $error_code = $exception->getCode();
    $error_code = $error_code != 0 ? $error_code : -1;

    $response = $this->render('TestCenterServiceBundle::service-base.json.twig',
                              array(
      'error' => array('code' => $error_code, 'message' => $exception->getMessage()),
      'results' => array('action' => $action, 'parameters' => $parameters)));

    // Set Content Type to JSON
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

}

?>
