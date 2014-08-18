<?php

/* Test Center - Compliance Testing Application (Services Shared Library)
 * Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
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

namespace shared\session;

use shared\utility\StringUtilities;

/**
 * Unified Session Management Class.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Manager extends \Phalcon\DI\Injectable {

  /**
   * Do we have an active session?
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function isActive() {
    return $this->session->isStarted();
  }

  /**
   * Is a user logged in to the session?
   * 
   * @return boolean 'true' YES, 'false' NO
   */
  public function isLoggedIn() {
    return $this->isActive() && $this->persistent->has('user:id');
  }

  /**
   * Login in to session.
   * 
   * @param integer $user_id User ID
   * @param string $user_name User Name
   * @return boolean 'true' is successfull, 'false' otherwise
   */
  public function login($user_id, $user_name = null) {
    // Verify Required Parameters
    assert('isset($user_id) && is_integer($user_id)');
    $user_name = StringUtilities::nullOnEmpty($user_name);

    // If user already logged in, log him out before continuing
    $this->logout();

    // Save the user information
    $this->persistent->set('user:id', $user_id);
    if (isset($user_name)) {
      $this->persistent->set('user:name', $user_name);
    }

    return true;
  }

  /**
   * Logout of session.
   * 
   * @return boolean 'true' is successfull, 'false' otherwise
   */
  public function logout() {
    // Do we currently have a Login Session?
    if ($this->isLoggedIn()) { // YES
      // Leave Sudo, before logging out, if it is active
      $this->leaveSudo();

      // Clear Session Variables
      $this->persistent->remove('user:id');
      $this->persistent->remove('user:name');

      // TODO: On Logout, we should also clear any variables that have been set
      return true;
    }

    return false;
  }

  /**
   * Has another user, temporarily, taken over the session?
   * 
   * @return boolean 'true' YES, 'false' NO
   */
  public function isSudoActive() {
    return $this->persistent->has('sudoed_user:id');
  }

  /**
   * Login in to session.
   * 
   * @param integer $user User ID
   * @param string $username User Name
   * @return boolean 'true' is successfull, 'false' otherwise
   */
  public function enterSudo($user, $username = null) {
    // Verify Required Parameters
    assert('isset($user_id) && is_integer($user_id)');
    $username = StringUtilities::nullOnEmpty($username);

    // Do we currently have a user logged in?
    if ($this->isLoggedIn()) { // YES
      // Leave Sudo, if it is active
      $this->leaveSudo();

      // Backup Current User
      $this->set('sudoed_user:id', $this->persistent['user:id']);
      if ($this->persistent->has('user:name')) {
        $this->set('sudoed_user:name', $this->persistent['user:name']);
      }

      // Change the Logged In User Information
      $this->persistent->set('user:id', $user);
      if (isset($username)) {
        $this->persistent->set('user:name', $username);
      }

      return true;
    }

    return false;
  }

  /**
   * Leave Sudo Session, if active.
   * 
   * @return boolean 'true' is successfull, 'false' otherwise
   */
  public function leaveSudo() {
    // Are we currently in a sudo session?
    if ($this->isSudoActive()) { // YES
      // Restore User State
      $this->set('user:id', $this->persistent['sudoed_user:id']);
      if ($this->persistent->has('sudoed_user:name')) {
        $this->set('user:name', $this->persistent['sudoed_user:name']);
      }

      // Clear Session Variables
      $this->persistent->remove('sudoed_user:id');
      $this->persistent->remove('sudoed_user:name');

      return true;
    }

    return false;
  }

  /**
   * Get the User ID for the Currently Logged in User (if any)?
   * 
   * return integer User ID
   */
  public function getUser() {
    return $this->isLoggedIn() ? $this->persistent['user:id'] : null;
  }

  /**
   * Get the User Name for the Currently Logged in User (if any)?
   * 
   * return string User Name
   */
  public function getUsername() {
    if ($this->isLoggedIn() && $this->persistent->has('user:name')) {
      return $this->session['user:name'];
    }

    return null;
  }

  /**
   * Get value for current session variable
   * 
   * @param string $variable Variable Name
   * @return mixed Existing Value for Variable, or null, if not set
   */
  public function getVariable($variable) {
    if ($this->isLoggedIn()) {
      $variable = StringUtilities::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');

      return $this->persistent->get($variable, null);
    }

    return null;
  }

  /**
   * Set a value for a session variable
   * 
   * @param string $variable Variable Name
   * @param mixed $value New Value for the Variable
   * @return mixed Existing Value for Variable, or null, if not set
   */
  public static function setVariable($variable, $value) {
    if ($this->isLoggedIn()) {
      $variable = StringUtilities::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');
      assert('isset($value)');

      // Get the current Variable Value
      $old_value = $this->persistent->get($variable, null);

      // Set the new value
      $this->persistent->set($variable, $value);

      return $old_value;
    }

    return null;
  }

  /**
   * Remove the Variable from the Session
   * 
   * @param string $variable Variable Name
   * @return mixed Existing Value for Variable, or null, if not set
   */
  public function clearVariable($variable) {
    if ($this->isLoggedIn()) {
      $variable = StringUtilities::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');

      // Get the current Variable Value
      $old_value = $this->persistent->get($variable, null);

      // Remove the Variable from the Session
      $this->persistent->remove($variable);

      return $old_value;
    }

    return null;
  }

  /**
   * Has a value been set for the variable?
   * 
   * @param string $variable Variable Name
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function isVariableSet($variable) {
    if ($this->isLoggedIn()) {
      $variable = StringUtilities::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');

      return $this->persistent->has($variable);
    }

    return false;
  }

  /**
   * Has an Organization been Set for the Session?
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function hasOrganization() {
    return $this->isVariableSet('__tc_organization');
  }

  /**
   * Get the Current Session Organization
   * 
   * @param string $variable Variable Name
   * @return integer Existing Organization Identifier, or null, if not set
   */
  public static function getOrganization() {
    return $this->getVariable('__tc_organization');
    ;
  }

  /**
   * Set a New Organization for the Session, and optionally, a New Project and
   * Container.
   * 
   * @param integer $org_id Organization Identifier
   * @param integer $project_id Project Identifier
   * @param integer $container_id Container Identifier
   * @return integer Previous Organization Identifier, or null, if not set
   */
  public static function setOrganization($org_id, $project_id = null, $container_id = null) {
    // Verify Required Parameters
    assert('isset($org_id) && is_integer($org_id)');

    // Save the Current Organization ID and then Set it to the New Value
    $old_org = $this->getOrganization();
    $this->setVariable('__tc_organization', $org_id);

    // Either Modify the Project ID or Clear it (as Projects are Organization Dependent)
    if (isset($project_id)) {
      // Set a New Project for the Session
      $this->setProject($project_id, $container_id);
    } else {
      $this->clearProject();
    }

    return $old_org;
  }

  /**
   * Clear the Session Organization (if set)
   * 
   * @return integer Previous Organization Identifier, or null, if not set
   */
  public static function clearOrganization() {
    // Save the Current Organization ID and then Set it to the New Value
    $old_org = $this->getOrganization();
    $this->clearVariable('__tc_organization');

    // Clear the Current Project (as Projects are Organization Dependent)
    $this->clearProject();

    return $old_org;
  }

  /**
   * Has an Project been Set for the Session?
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function hasProject() {
    return $this->isVariableSet('__tc_project');
  }

  /**
   * Get the Current Session Project
   * 
   * @param string $variable Variable Name
   * @return integer Existing Project Identifier, or null, if not set
   */
  public static function getProject() {
    return $this->getVariable('__tc_project');
  }

  /**
   * Set a New Project for the Session, and optionally, a New Container.
   * 
   * @param integer $project_id Project Identifier
   * @param integer $container_id Container Identifier
   * @return integer Previous Project Identifier, or null, if not set
   */
  public static function setProject($project_id, $container_id = null) {
    // Verify Required Parameters
    assert('isset($project_id) && is_integer($project_id)');

    // Save the Current Project ID and then Set it to the New Value
    $old_project = $this->getProject();
    $this->setVariable('__tc_project', $project_id);

    // Either Modify the Project ID or Clear it (as Containers are Organization Dependent)
    if (isset($container_id)) {
      // Set a New Container for the Session
      $this->setContainer($project_id, $container_id);
    } else {
      $this->clearContainer();
    }

    return $old_project;
  }

  /**
   * Clear the Session Project (if set)
   * 
   * @return integer Previous Project Identifier, or null, if not set
   */
  public static function clearProject() {
    // Save the Current Project ID and then Set it to the New Value
    $old_project = $this->getProject();
    $this->clearVariable('__tc_project');

    // Clear the Current Project (as Containers are Organization Dependent)
    $this->clearContainer();

    return $old_project;
  }

  /**
   * Has an Container been Set for the Session?
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function hasContainer() {
    return $this->isVariableSet('__tc_container');
  }

  /**
   * Get the Current Session Project
   * 
   * @return integer Existing Container Identifier, or null, if not set
   */
  public static function getContainer() {
    return $this->getVariable('__tc_container');
  }

  /**
   * Set a New Container for the Session.
   * 
   * @param integer $container_id Container Identifier
   * @return integer Previous Container Identifier, or null, if not set
   */
  public static function setContainer($container_id) {
    // Verify Required Parameters
    assert('isset($container_id) && is_integer($container_id)');

    // Save the Current Container ID and then Set it to the New Value
    $old_container = $this->getContainer();
    $this->setVariable('__tc_container', $container_id);

    return $old_container;
  }

  /**
   * Clear the Session Container (if set)
   * 
   * @return integer Previous Container Identifier, or null, if not set
   */
  public static function clearContainer() {
    // Save the Current Container ID and then Set it to the New Value
    $old_container = $this->getContainer();
    $this->clearVariable('__tc_container');

    return $old_container;
  }

}
