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
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function doAction($context) {
    assert('!isset($parameters) || is_array($parameters)');

    try {
      // Initialize Action/Checks
      $context = $this->do_initAction($context);

      // Pre-Action Validation/Transformation
      $context = $this->do_preAction($context);

      // Action
      $context = $this->do_call($context);

      // Post-Action Validation/Transformation
      $context = $this->do_postAction($context);

      // Render Results
      $context = $this->do_render($context);

      // Mark Action as Complete
      $this->do_success($context);
    } catch (\Exception $e) {
      // Render Exception
      $context = $this->do_renderException($context->setActionResult($e));

      // Mark Action as Failed
      $this->do_failed($context);
    }

    return $context->getResponse();
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_initAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: initAction[{Action}]
     * Called to Initialize Action and Perform Startup Checks
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- objext - Modified Action Context, OR
     * -- null - in the case of no changes to the context
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
    $return = $this->callMethod($method, $defaultMethod, $context);
    return isset($return) ? $return : $context;
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_preAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: preAction[{Action}]
     * Called before Action Function is invoked
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- objext (map) - Modified Action Context, OR
     * -- null - in the case of no changes to the context
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
    $return = $this->callMethod($method, $defaultMethod, $context);
    return isset($return) ? $return : $context;
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_call($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: do{Action}Action
     * Action to be Invoked
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- (value or null)
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
    return $context->setActionResult($this->callMethod($method, null, $context,
                                                       new \Exception("Missing or Invalid Action Method [$method].", 1)));
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_postAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: postAction[{Action}]
     * Called after Action Function invoked
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- objext (map) - Modified Action Context, OR
     * -- null - in the case of no changes to the context
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
    $return = $this->callMethod($method, $defaultMethod, $context);
    return isset($return) ? $return : $context;
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_render($context, $format = 'json') {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('isset($format) && is_string($format)');

    $action = $context->getAction();
    assert('isset($action)');

    // Set the Response Format
    $context->setParameter('__format', $format);

    /* Function: preRender[{Action}]
     * Called to Prepare Results for Rendering
     * - Parameters
     * -- (1st) $context - Action Context (Read Only)
     *
     * - Return
     * -- (results) - Modified Action Results
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
    $context->setActionResult($this->callMethod($method, $defaultMethod,
                                                $context));

    /* Function: render{Format}[{Action}]
     * Called to Prepare Results for Rendering
     * - Parameters
     * -- (1st) $context - Action Context (Read Only)
     *
     * - Return
     * -- (response) response object
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
    return $context->setResponse($this->callMethod($method, $defaultMethod,
                                                   $context));
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_renderException($context, $format = 'json') {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('isset($format) && is_string($format)');

    $action = $context->getAction();
    assert('isset($action)');

    // Set the Response Format
    $context->setParameter('__format', $format);

    /* Function: render{Format}[{Action}]
     * Called to Prepare Results for Rendering
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- (response) response object
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
    return $context->setResponse($this->callMethod(null, $defaultMethod,
                                                   $context));
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_success($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: {action}Success (Default: successAction )
     * Called after Action Function invoked
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- (nothing)
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
    $this->callMethod($method, $defaultMethod, $context);
  }

  /**
   * 
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function do_failed($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    /* Function: {action}Failed (Default: failedAction )
      /* Function: failed[{Action}]
     * Called after Action Function invoked
     * - Parameters
     * -- (1st) $context - Action Context
     *
     * - Return
     * -- (nothing)
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
    $this->callMethod($method, $defaultMethod, $context);
  }

  /**
   * @param $action
   * @param $parameters
   * @param $in_actions
   * @param $function
   * @return array
   */
  protected function processParameter($context, $in_actions, $function) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('!isset($in_actions) || is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    $action = $context->getAction();
    assert('isset($action)');

    if (is_array($in_actions)) {
      if (array_search($action, $in_actions) !== FALSE) {
        return $function($this, $context);
      }
    } else if (is_string($in_actions)) {
      if ($action === $in_actions) {
        return $function($this, $context);
      }
    }

    return $context;
  }

  /**
   * @param $action
   * @param $results
   * @param $in_actions
   * @param $function
   * @return mixed
   */
  protected function processResults($context, $in_actions, $function) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('!isset($in_actions) || is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    $action = $context->getAction();
    assert('isset($action)');

    $results = $context->getActionResult();
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
  protected function processChecks($context, $in_actions, $function) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('!isset($in_actions) || is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    $action = $context->getAction();
    assert('isset($action)');

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

    return $call_function ? $function($this, $context) : $context;
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
  protected function callMethod($method, $defaultMethod, $context,
                                $exception = null) {
    // Discover Method to Call
    if (isset($method) && is_callable(array($this, $method))) {
      // Method is Defined for Action
      return $this->$method($context);
    } else if (isset($defaultMethod) && is_callable(array($this, $defaultMethod))) {
      // Use Default Handler for Action
      return $this->$defaultMethod($context);
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
   * @param $context
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function renderJson($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $results = $context->getActionResult();
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
   * @param $context
   * @param $exception
   * @return TestCenter\ServiceBundle\API\ActionContext
   */
  protected function renderExceptionJson($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    $exception = $context->getActionResult();
    assert('isset($exception)');

    $error_code = $exception->getCode();
    $error_code = $error_code != 0 ? $error_code : -1;

    $response = $this->render('TestCenterServiceBundle::service-base.json.twig',
                              array(
      'error' => array('code' => $error_code, 'message' => $exception->getMessage()),
      'results' => array('action' => $action, 'parameters' => isset($parameters) ? $parameters : array())));

    // Set Content Type to JSON
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

}

?>
