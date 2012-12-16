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

/**
 * Description of BaseServiceController
 *
 * @author Paulo Ferreira
 */
class BaseServiceController
  extends BaseController {

  /**
   * @param $action
   * @param $parameters
   * @return null
   */
  protected function do_initAction($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    /* Function: {action}Start (Default: startAction )
     * Perform zero or more checks, to validate the Required Session State
     * -- For Default Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * -- Action Specific Function
     * --- (1st) $parameters - parameters array (map)
     *
     * - Return
     * -- array (map) - representing the parameters, OR
     * -- null - in the case of no changes to the parameters
     *
     * - Exceptions
     * -- All Checks Generate an Exception in Case of Failure
     * --- Required Exceptions Parameters
     * --- $code - error code
     * --- $message - error message
     */
    $defaultMethod = 'startAction';
    $method = strtolower($action) . 'Start';

    // Call the Function
    $this->callMethod($method, $defaultMethod, $action);

    /* Function: sessionChecks{Action} (Default: sessionChecks)
     * Perform zero or more checks, to validate the Required Session State
     * -- For Default Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * -- Action Specific Function
     * --- (1st) $parameters - parameters array (map)
     *
     * - Return
     * -- array (map) - representing the parameters, OR
     * -- null - in the case of no changes to the parameters
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
    $return = $this->callMethod($method, $defaultMethod, $action, $parameters);
    $parameters = isset($return) ? $return : $parameters;

    /* Function: {action}PrivilegeChecks (Default: privilegeChecks)
     * Perform zero or more checks, to validate the Privileges/Permissions
     * -- For Default Function
     * --- (1st) $action - the name of the action (all-lower-case)
     * --- (2nd) $parameters - parameters array (map)
     * -- Action Specific Function
     * --- (1st) $parameters - parameters array (map)
     *
     * - Return
     * -- array (map) - representing the parameters, OR
     * -- null - in the case of no changes to the parameters
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
    $return = $this->callMethod($method, $defaultMethod, $action, $parameters);

    return parent::do_initAction($action, isset($return) ? $return : $parameters);
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
    $options = StringUtilities::nullOnEmpty($options);
    $return = array();
    if (isset($options)) {
      $array = explode(';', $options);
      foreach ($array as $element) {
        $elements = explode('=', $element);
        $count = count($elements);
        if ($count > 0) {
          $key = StringUtilities::nullOnEmpty($elements[0]);
          if ($count > 1) {
            $value = StringUtilities::nullOnEmpty($elements[1]);
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
   * @return bool
   * @throws \Exception
   */
  public function checkInSession() {
    if (session_id() === "") {
      throw new \Exception('No Active Session.', 2);
    }

    return true;
  }

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkLoggedIn() {
    if (!SessionManager::hasUser()) {
      throw new \Exception('No Active Session.', 3);
    }

    return true;
  }

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkOrganization() {
    if (!SessionManager::hasOrganization()) {
      throw new \Exception('No Active Organization.', 3);
    }

    return true;
  }

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkProject() {
    if (!SessionManager::hasProject()) {
      throw new \Exception('No Active Project.', 3);
    }

    return true;
  }

}

?>