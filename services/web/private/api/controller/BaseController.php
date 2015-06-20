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

use Phalcon\Mvc\Controller;
use common\utility\Strings;
use common\utility\I18N;

/**
 * Base for all Controllers. Standardizes/Sub-divide Action Processing into 
 * several stages.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class BaseController extends Controller {
  /* ---------------------------------------------------------------------------
   * GENERIC ACTION PROCESSOR
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param $context
   * @return String
   */
  protected function doAction($context) {
    assert('!isset($parameters) || is_array($parameters)');

    // Locale Defaults
    list($path, $default) = $this->getDI()->getShared('locale');

    // Is the Header Requesting a Specific Locale?
    $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    I18N::initialize($locale, $path, null, $default);

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

    // OUTPUT RESPONSE
    echo $context->getResponse();
  }

  /* ---------------------------------------------------------------------------
   * ACTION PROCESSING STAGES
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
   * STAGE 2: Late Initialization/Setup for Action (De-reference any paramaters).
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
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
    $defaultMethod = 'doActionDefault';
    $method = 'do' . ucfirst($action) . 'Action';

    // Call the Function
    return $context->setActionResult($this->callMethod($method, $defaultMethod, $context, new \Exception("Missing or Invalid Action Method [$method].", 1)));
  }

  /**
   * STAGE 4: Perform any Post Action Cleanup.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
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
   * STAGE 5: Render Action Results and Set Response in Context.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
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
    $context->setActionResult($this->callMethod($method, $defaultMethod, $context));

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
    $response = $this->callMethod($method, $defaultMethod, $context);

    // Flush Headers Before Returning
    $this->response->sendHeaders();
    return $context->setResponse($response);
  }

  /**
   * 
   * @param $context
   * @return ActionContext
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
    $response = $this->callMethod(null, $defaultMethod, $context);

    // Flush Headers Before Returning
    $this->response->sendHeaders();
    return $context->setResponse($response);
  }

  /**
   * 
   * @param $context
   * @return ActionContext
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
   * @return ActionContext
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

  /* ---------------------------------------------------------------------------
   * (DEFAULT) JSON RENDER FUNCTIONS
   * ---------------------------------------------------------------------------
   */

  /**
   * Default JSON Render Results
   *
   * @param $context
   * @return ActionContext
   */
  protected function renderJson($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $results = $context->getActionResult();
    if (isset($results)) {
      $response = $this->view->render('service-response.json', array('results' => $results));
    } else {
      $response = $this->view->render('service-response.json');
    }

    // Set Content Type to JSON
    $this->response->setContentType('application/json');
    $this->addAcceptHeaders($context);
    return $response;
  }

  /**
   * Default JSON Render Exception
   *
   * @param $context
   * @param $exception
   * @return ActionContext
   */
  protected function renderExceptionJson($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $action = $context->getAction();
    assert('isset($action)');

    // Extract Context Parameters
    $parameters = $context->getParameters();

    $exception = $context->getActionResult();
    assert('isset($exception)');

    $error_code = $exception->getCode();
    $error_code = $error_code != 0 ? $error_code : -1;

    $response = $this->view->render('service-response.json', array(
      'error' => array('code' => $error_code, 'message' => $exception->getMessage()),
      'results' => array('action' => $action, 'parameters' => isset($parameters) ? $parameters : array())));

    // Set Content Type to JSON
    $this->response->setContentType('application/json');
    $this->addAcceptHeaders($context);
    return $response;
  }

  /*
   * ---------------------------------------------------------------------------
   * HELPER FUNCTIONS: HTTP Request Parameters
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param type $context
   */
  protected function addAcceptHeaders($context) {
    // Do we have an 'Origin' Header in the Request?
    $origin = Strings::nullOnEmpty($this->request->getHeader('Origin'));
    if (isset($origin)) { // YES      
      // Do we have a more or less normal URI?
      $uri = parse_url($origin);
      $host = $uri['host'];
      /* TODO Extra Verifications
       * 1. Verify that the schema is http or https (no using allowing anything else)
       * 2. Since headers can be easily forged, maybe verify other headers for
       * allowed values, before allowing accept (better yet, throw an exception,
       * if the headers do not have permitted values, as that probably means
       * its coming from a hacking source)
       */
      if (($uri !== FALSE) && array_key_exists('host', $uri)) { // YES
        // Get Host component of URI
        $host = $uri['host'];
        // Base Matches
        $matches = ["^127(\.\d{1,3}){3}", "^localhost$"];

        // Add any Application Configuration Matches
        $config = $this->getDI()->getConfig();
        if (isset($config) && array_key_exists('allowed-origins', $config)) {
          $allowed = $config['allowed-origins'];
          if (is_string($allowed)) {
            $matches[] = $allowed;
          } else if (is_array($allowed)) {
            $matches = array_merge($matches, $allowed);
          }
        }

        // Try to Match the Origin against one of the allowed sources
        $ok = false;
        for ($i = 0, $length = count($matches); $i < $length; $i++) {
          if (preg_match('/' . $matches[$i] . '/i', $host)) {
            $ok = true;
            break;
          }
        }

        // Is the origin allowed?
        if ($ok) { // YES
          // TODO TD-042
          $this->response->setHeader('Access-Control-Allow-Origin', $origin);
          $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
        }
      }
    }
  }

  /**
   * Perform Cleanup of a Single Request Parameter
   * 
   * @param string $key Key to Request Parameter
   * @return string Cleaned Request Parameter Value
   */
  protected function requestParameter($key) {
    return htmlentities($this->request->get($key), ENT_QUOTES, "UTF-8");
  }

  /**
   * Perform Cleanup of an Array of HTTP Request Parameters
   * 
   * @param array $source Array to merge FROM
   * @param array $merge OPTIONAL Array to merge INTO
   * @return array Resultant Array
   */
  protected function cleanURLParameters($source, $merge = null) {
    // Parameter Validation
    assert('isset($source) && is_array($source)');
    assert('!isset($merge) || is_array($merge)');

    $return = isset($merge) ? $merge : array();

    // For each URL Parameter, cleanup Escape Special Characters 
    foreach ($source as $key => $value) {
      $return[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
    }

    return $return;
  }

  /**
   * Extract and Clean HTTP Request Parameters
   * 
   * @return array Array of Request Parameters (Depending on Request Type)
   */
  protected function requestParameters() {
    $parameters = null;

    // Is the HTTP REQUEST METHOD a GET?
    if ($this->request->isGet()) { // YES
      $parameters = $this->cleanURLParameters($this->request->getQuery());
    } else if ($this->request->isPost()) { // NO: It's a POST
      $parameters = $this->cleanURLParameters($this->request->getPost());
    }

    return $parameters;
  }

  /* ---------------------------------------------------------------------------
   * UTILITY FUNCTIONS
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param type $context
   * @param type $parameter
   * @param type $function
   * @param type $actions
   * @param type $reqActions
   * @return type
   * @throws \Exception
   */
  protected function onParameterDo($context, $parameter, $function, $actions = null, $reqActions = null, $functionOnMissing = null) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('isset($parameter) && is_string($parameter)');
    assert('isset($function) && is_callable($function)');
    assert('!isset($functionOnMissing) || is_callable($functionOnMissing)');

    // Process Incoming Parameters
    if (isset($actions) && !is_array($actions)) {
      $actions = $this->arrayOfString((string) $actions);
    }
    if (isset($reqActions)) {
      if (is_string($reqActions)) {
        if ($reqActions !== '*') {
          $reqActions = $this->arrayOfString((string) $reqActions);
        }
      } else if (!is_array($reqActions)) {
        $reqActions = null;
      }
    }

    // Get the Context Action
    $action = $context->getAction();
    assert('isset($action)');

    // Is the Value Required for the Action?
    $required = false;
    if ($reqActions === '*') {
      $required = true;
    } else {
      $required = isset($reqActions) && (array_search($action, $reqActions) !== FALSE);
    }

    $process = true;
    if (!$required) {
      if (isset($actions)) {
        $process = array_search($action, $actions) !== FALSE;
      } else { // If BOTH, $actions AND $reqActions (NULL) - Assume we are Processing for All Actions (Otherwise - Only Specifica Actions)
        $process = !isset($reqActions);
      }
    }

    // Handle the Process
    if ($process) {
      $value = $context->getParameter($parameter);

      // If no Value is Set, then see if we have a function that can get an alternative value
      if (!isset($value) && isset($functionOnMissing)) {
        $value = $functionOnMissing($this, $context, $action);
        /* Don't Set the Parameter Value. Why? Because this way we can see if the parameter
         * actually had a value, or a default was used.
          if (isset($value)) {
          $context->setParameter($parameter, $value);
          }
         *
         */
      }

      if (isset($value)) {
        return $function($this, $context, $action, $value);
      } else if ($required) {
        throw new \Exception("Missing Required Action Parameter [{$parameter}].", 1);
      }
    }

    return $context;
  }

  /**
   * @param $action
   * @param $in_actions
   * @param $parameters
   * @param $function
   * @return bool
   */
  protected function onActionDo($context, $in_actions, $function) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');
    assert('!isset($in_actions) || is_array($in_actions)');
    assert('isset($function) && is_callable($function)');

    $action = $context->getAction();
    assert('isset($action)');

    // Process Incoming Parameters
    if (isset($in_actions)) {
      if (is_string($in_actions)) {
        if ($in_actions !== '*') {
          $in_actions = $this->arrayOfString((string) $in_actions);
        }
      } else if (!is_array($in_actions)) {
        $in_actions = null;
      }
    }

    $call_function = false;
    if (!isset($in_actions)) {
      $call_function = false;
    } else {
      $call_function = ($in_actions === '*') || (array_search($action, $in_actions) !== FALSE);
    }

    return $call_function ? $function($this, $context, $action) : $context;
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
   * @param $method
   * @param $defaultMethod
   * @param $action
   * @param null $parameter1
   * @param null $parameter2
   * @param null $exception
   * @return null
   * @throws null
   */
  protected function callMethod($method, $defaultMethod, $context, $exception = null) {
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
   * 
   * @param type $value
   * @return type
   */
  protected function arrayOfString($value) {
    assert('!isset($value) || is_string($value)');
    if (isset($value)) {
      $value = Strings::nullOnEmpty($value);
    }

    return isset($value) ? array($value) : null;
  }

}

?>
