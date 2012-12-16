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
 * Description of SessionManager
 *
 * @author Paulo Ferreira
 */
class SessionManager {

  /**
   * @static
   * @return bool
   */
  public static function isSudoActive() {
    return isset($_SESSION['tc_sudo_user']);
  }

  /**
   * @static
   * @param $user_id
   * @param null $org_id
   * @param null $project_id
   * @return bool
   */
  public static function enterSudo($user_id, $org_id = null, $project_id = null) {
    // Verify Required Parameters
    assert('isset($user_id) && is_integer($user_id)');

    // If Sudo Session is Active - Leave it Before Entering New Sudo Session
    if (self::isSudoActive()) {
      self::leaveSudo();
    }

    // Save Current User State
    $_SESSION['tc_sudo_user'] = $_SESSION['tc_user'];
    $_SESSION['tc_sudo_organization'] = $_SESSION['tc_organization'];
    $_SESSION['tc_sudo_project'] = $_SESSION['tc_project'];

    return true;
  }

  /**
   * @static
   * @return bool
   */
  public static function leaveSudo() {

    // Leave Sudo Session
    if (self::isSudoActive()) {
      // Restore User State
      $_SESSION['tc_user'] = $_SESSION['tc_sudo_user'];
      $_SESSION['tc_organization'] = $_SESSION['tc_sudo_organization'];
      $_SESSION['tc_project'] = $_SESSION['tc_sudo_project'];

      // Clear Sudo Variables
      unset($_SESSION['tc_sudo_user']);
      unset($_SESSION['tc_sudo_organization']);
      unset($_SESSION['tc_sudo_project']);

      /* TODO
       * Create Generic Functions
       * 1. Clear TC Session Variables (unsets all variables that start with a specific prefix, i.e. tc_)
       * 2. Save TC Session Variables (copies all variables, that start with a specific prefix i.e. tc, to a set of variables with another prefix, i.e. tc_sudo)
       * 3. Restore TC Session Variables (Does the inverse of 2, and then unsets the saved variables)
       */
      return true;
    }

    return false;
  }

  /**
   * @static
   * @return bool
   */
  public static function hasUser() {
    return isset($_SESSION['tc_user']);
  }

  /**
   * @static
   * @return null
   */
  public static function getUser() {
    return isset($_SESSION['tc_user']) ? $_SESSION['tc_user'] : null;
  }

  /**
   * @static
   * @param $user_id
   * @param null $org_id
   * @param null $project_id
   * @return bool
   */
  public static function login($user_id, $org_id = null, $project_id = null) {
    // Verify Required Parameters
    assert('isset($user_id) && is_integer($user_id)');

    // If user already logged in, log him out before continuing
    if (self::hasUser()) {
      self::logout();
    }

    // Set the User and Modify the Organization or Clear it
    $_SESSION['tc_user'] = $user_id;
    if (isset($org_id)) {
      // Set the New Organization
      self::setOrganization($org_id, $project_id);
    } else {
      self::clearOrganization();
    }

    return true;
  }

  /**
   * @static
   * @return bool
   */
  public static function logout() {

    // Leave Sudo, before logging out, if it is active
    if (self::isSudoActive()) {
      self::leaveSudo();
    }

    // Clear User and Organization Variable
    unset($_SESSION['tc_user']);
    self::clearOrganization();

    return true;
  }

  /**
   * @static
   * @return bool
   */
  public static function hasOrganization() {
    return isset($_SESSION['tc_organization']);
  }

  /**
   * @static
   * @return null
   */
  public static function getOrganization() {
    return isset($_SESSION['tc_organization']) ? $_SESSION['tc_organization'] : null;
  }

  /**
   * @static
   * @param $org_id
   * @param null $project_id
   * @param null $container_id
   * @return null
   */
  public static function setOrganization($org_id, $project_id = null,
                                         $container_id = null) {
    // Verify Required Parameters
    assert('isset($org_id) && is_integer($org_id)');

    // Save the Current Organization ID and then Set it to the New Value
    $old_org = self::getOrganization();
    $_SESSION['tc_organization'] = $org_id;

    // Either Modify the Project ID or Clear it (as Projects are Organization Dependent)
    if (isset($project_id)) {
      // Set the New Organization
      self::setProject($project_id, $container_id);
    } else {
      self::clearProject();
    }

    return $old_org;
  }

  /**
   * @static
   * @return null
   */
  public static function clearOrganization() {
    // Save the Current Project ID and then Clear Session Variable
    $old_org = self::getOrganization();
    unset($_SESSION['tc_organization']);

    // Clear the Current Project (as Projects are Organization Dependent)
    self::clearProject();

    return $old_org;
  }

  /**
   * @static
   * @return bool
   */
  public static function hasProject() {
    return isset($_SESSION['tc_project']);
  }

  /**
   * @static
   * @return null
   */
  public static function getProject() {
    return isset($_SESSION['tc_project']) ? $_SESSION['tc_project'] : null;
  }

  /**
   * @static
   * @param $project_id
   * @return null
   */
  public static function setProject($project_id, $container_id = null) {
    // Verify Required Parameters
    assert('isset($project_id) && is_integer($project_id)');

    // Save the Current Project ID and then Set it to the new value
    $old_project = self::getProject();
    $_SESSION['tc_project'] = $project_id;
    if (isset($container_id)) {
      self::setContainer($container_id);
    } else {
      self::clearContainer();
    }

    return $old_project;
  }

  /**
   * @static
   * @return null
   */
  public static function clearProject() {
    // Save the Current Project ID and then Clear Session Variable
    $old_project = self::getProject();
    unset($_SESSION['tc_project']);
    self::clearContainer();

    return $old_project;
  }

  /**
   * @static
   * @return bool
   */
  public static function hasContainer() {
    return isset($_SESSION['tc_container']);
  }

  /**
   * @static
   * @return null
   */
  public static function getContainer() {
    return isset($_SESSION['tc_container']) ? $_SESSION['tc_container'] : null;
  }

  /**
   * @static
   * @param $container_id
   * @return null
   */
  public static function setContainer($container_id) {
    // Verify Required Parameters
    assert('isset($container_id) && is_integer($container_id)');

    // Save the Current Working Container and then Modify it
    $old_value = self::getContainer();
    $_SESSION['tc_container'] = $container_id;

    return $old_value;
  }

  /**
   * @static
   * @return null
   */
  public static function clearContainer() {
    // Save the Current Project ID and then Clear Session Variable
    $old_value = self::getContainer();
    unset($_SESSION['tc_container']);

    return $old_value;
  }

  /**
   * 
   * @param type $variable
   * @return type
   */
  public static function getVariable($variable) {
    return isset($_SESSION[$variable]) ? $_SESSION[$variable] : null;
  }

  /**
   * 
   * @param type $variable
   * @param type $value
   * @return type
   */
  public static function setVariable($variable, $value) {
    // Verify Required Parameters
    assert('isset($variable) && is_string($variable)');
    assert('isset($value)');

    // Save the Current Variable Value and then Set it to the new value
    $old_value = self::getVariable($variable);
    $_SESSION[$variable] = $value;

    return $old_value;
  }

  /**
   * 
   * @param type $variable
   * @return type
   */
  public static function clearVariable($variable) {
    // Save the Current Project ID and then Clear Session Variable
    $old_value = self::getVariable($variable);
    unset($_SESSION[$variable]);

    return $old_value;
  }

  /**
   * 
   * @param type $variable
   * @return type
   */
  public static function issetVaraible($variable) {
    return isset($_SESSION[$variable]);
  }

}

?>