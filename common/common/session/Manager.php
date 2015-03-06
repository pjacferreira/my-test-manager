<?php

/* Test Center - Compliance Testing Application (Shared Library)
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

namespace common\session;

use common\utility\Strings;

/**
 * Unified Session Management Class.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Manager extends \Phalcon\DI\Injectable {

  /**
   * Start or Re-use an active session
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function start() {
    // Do we already have a session?
    if (!$this->session->isStarted()) { // NO: Create it
      $this->session->start();
    }
    return true;
  }

  /**
   * Start or Re-use an active session
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function destroy() {
    // Do we have a session?
    if ($this->session->isStarted()) { // YES: Destroy it
      $this->session->destroy();
    }
    return true;
  }

  /**
   * Do we have an active session?
   * 
   * @return boolean 'true' YES, 'false' otherwise
   */
  public function isActive() {
    return $this->session->isStarted();
  }

  /**
   * Get current value for a session variable, or return default if set
   * 
   * @param string $variable Variable Name
   * @return mixed Existing Value for Variable, or null, if not set
   */
  public function getVariable($variable, $default = null) {
    $variable = Strings::nullOnEmpty($variable);

    // Verify Required Parameters
    assert('isset($variable) && is_string($variable)');

    return $this->isVariableSet($variable) ?
            $this->session->get($variable, $default) :
            $default;
  }

  /**
   * Set a value for a session variable
   * 
   * @param string $variable Variable Name
   * @param mixed $value New Value for the Variable
   * @return mixed Existing Value for Variable, or null, if not set
   */
  public function setVariable($variable, $value) {
    if ($this->isActive()) {
      $variable = Strings::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');
      assert('isset($value)');

      // Get the current Variable Value
      $old_value = $this->session->get($variable, null);

      // Set the new value
      $this->session->set($variable, $value);

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
    if ($this->isActive()) {
      $variable = Strings::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');

      // Get the current Variable Value
      $old_value = $this->session->get($variable, null);

      // Remove the Variable from the Session
      $this->session->remove($variable);

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
    if ($this->isActive()) {
      $variable = Strings::nullOnEmpty($variable);

      // Verify Required Parameters
      assert('isset($variable) && is_string($variable)');

      return $this->session->has($variable);
    }

    return false;
  }

  /**
   * Get the SALT Value for the Session
   * 
   * return string Session Salt
   */
  public function getSalt() {
    $salt = null;
    // Do we have an Active Session?
    if (!$this->isActive()) { // NO: Start One
      $this->start();
    } else { // YES: See if salt has already been setup
      $salt = $this->getVariable('__tc_salt');
    }

    // Do we areleady have a Salt Value?
    if (!isset($salt)) { // NO: Create it
      $salt = md5($this->session->getId());
      $this->setVariable('__tc_salt', $salt);
    }
    return $salt;
  }

  /**
   * Is a user logged in to the session?
   * 
   * @return boolean 'true' YES, 'false' NO
   */
  public function isLoggedIn() {
    return $this->isActive() &&
            $this->getVariable('__tc_logged_in', false) &&
            $this->isVariableSet('__tc_user');
  }

  /**
   * Get Current Session Mode
   * 
   * return string Session Salt
   */
  public function getMode() {
    return $this->isLoggedIn() ? $this->getVariable('__tc_mode') : null;
  }

  /**
   * Login in to session.
   * 
   * @param array $user User to Login
   * @return boolean 'true' is successfull, 'false' otherwise
   */
  public function login($user) {
    // Verify Required Parameters
    assert('isset($user) && is_array($user)');

    // Do we already have a user logged in?
    if ($this->isLoggedIn()) { // YES: Log them out
      // If user already logged in, log him out before continuing
      $this->logout();
    }

    // Start a Session
    $this->start();

    // Start a Login Session
    $this->setVariable('__tc_user', $user);
    $this->setVariable('__tc_logged_in', true);
    $this->setVariable('__tc_mode', 'user');
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

      // Destroy the Session
      $this->destroy();
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
    return $this->session->has('__tc_sudoed');
  }

  /**
   * Login in to session.
   * 
   * @param integer $user_id User ID
   * @param string $username User Name
   * @return boolean 'true' is successfull, 'false' otherwise
   */
  public function enterSudo($user_id, $username = null) {
    // Verify Required Parameters
    assert('isset($user_id) && is_integer($user_id)');
    $username = Strings::nullOnEmpty($username);

    // Do we currently have a user logged in?
    if ($this->isLoggedIn()) { // YES
      // Leave Sudo, if it is active
      $this->leaveSudo();

      // Backup Current User
      $this->session->set('sudoed_user:id', $this->persistent['user:id']);
      if ($this->session->has('user:name')) {
        $this->session->set('sudoed_user:name', $this->persistent['user:name']);
      }

      // Change the Logged In User Information
      $this->session->set('user:id', $user_id);
      if (isset($username)) {
        $this->session->set('user:name', $username);
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
      $this->session->set('user:id', $this->persistent['sudoed_user:id']);
      if ($this->session->has('sudoed_user:name')) {
        $this->session->set('user:name', $this->persistent['sudoed_user:name']);
      }

      // Clear Session Variables
      $this->session->remove('sudoed_user:id');
      $this->session->remove('sudoed_user:name');

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
    return $this->getVariable('__tc_user');
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
   * @return array Existing Organization Identifier, or null, if not set
   */
  public function getOrganization() {
    return $this->getVariable('__tc_organization');
  }

  /**
   * Set a New Organization for the Session, and optionally, a New Project and
   * Container.
   * 
   * @param array $org_id Organization Information
   * @param array $project Project Information
   * @param array $container Container Information
   * @return array Previous Organization, or null, if not set
   */
  public function setOrganization($organization, $project = null, $container = null) {
    // Verify Required Parameters
    assert('isset($organization) && is_array($organization)');

    // Save the Current Organization and then Set it to the New Value
    $old_org = $this->getOrganization();
    $this->setVariable('__tc_organization', $organization);

    // Either Modify the Project or Clear it (as Projects are Organization Dependent)
    if (isset($project)) {
      // Set a New Project for the Session
      $this->setProject($project, $container);
    } else {
      $this->clearProject();
    }

    return $old_org;
  }

  /**
   * Clear the Session Organization (if set)
   * 
   * @return array Previous Organization, or null, if not set
   */
  public function clearOrganization() {
    // Clear the Current Organization
    $old_org = $this->clearVariable('__tc_organization');

    // Clear the Current Project (as Projects are Organization Dependency)
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
   * @return array Existing Project, or null, if not set
   */
  public function getProject() {
    return $this->getVariable('__tc_project');
  }

  /**
   * Set a New Project for the Session, and optionally, a New Container.
   * 
   * @param array $project Project Identifier
   * @param array $container Container Identifier
   * @return array Previous Project, or null, if not set
   */
  public function setProject($project, $container = null) {
    // Verify Required Parameters
    assert('isset($project) && is_array($project)');

    // Save the Current Project and then Set it to the New Value
    $old_project = $this->getProject();
    $this->setVariable('__tc_project', $project);

    // Either Modify the Container or Clear it (as Containers are Project Dependent)
    if (isset($container)) {
      // Set a New Container for the Session
      $this->setContainer($container);
    } else {
      $this->clearContainer();
    }

    return $old_project;
  }

  /**
   * Clear the Session Project (if set)
   * 
   * @return array Previous Project, or null, if not set
   */
  public function clearProject() {
    // Save the Current Project and then Set it to the New Value
    $old_project = $this->getProject();
    $this->clearVariable('__tc_project');

    // Clear the Current Project (as Containers are Project Dependent)
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
   * @return array Existing Container, or null, if not set
   */
  public function getContainer() {
    return $this->getVariable('__tc_container');
  }

  /**
   * Set a New Container for the Session.
   * 
   * @param array $container Container Identifier
   * @return array Previous Container, or null, if not set
   */
  public function setContainer($container) {
    // Verify Required Parameters
    assert('isset($container) && is_array($container)');

    // Save the Current Container and then Set it to the New Value
    $old_container = $this->getContainer();
    $this->setVariable('__tc_container', $container);

    return $old_container;
  }

  /**
   * Clear the Session Container (if set)
   * 
   * @return array Previous Container, or null, if not set
   */
  public function clearContainer() {
    // Save the Current Container and then Set it to the New Value
    $old_container = $this->getContainer();
    $this->clearVariable('__tc_container');

    return $old_container;
  }

  /* ---------------------------------------------------------------------------
   * UTILITY FUNCTIONS: Checks
   * ---------------------------------------------------------------------------
   */

  /**
   * Has a Session been Started?
   * 
   * @return bool 'TRUE' If Session Started, Exception otherwise
   * @throws \Exception If no Active Session
   */
  public function checkInSession() {
    if (!$this->isActive()) {
      throw new \Exception('No Active Session.', 1);
    }

    return true;
  }

  /**
   * Has a Session User been Logged in?
   * 
   * @return bool 'TRUE' If User Logged In, Exception otherwise
   * @throws \Exception If no User Logged In
   */
  public function checkLoggedIn() {
    if (!$this->isLoggedIn()) {
      throw new \Exception('No Active Session.', 2);
    }

    return true;
  }

  /**
   * Has a Session Organization been Set?
   * 
   * @return bool 'TRUE' If Session Organization set, Exception otherwise
   * @throws \Exception If no Session Organization Set
   */
  public function checkOrganization() {
    if (!$this->hasOrganization()) {
      throw new \Exception('No Organization has been set for the Session.', 3);
    }

    return true;
  }

  /**
   * Has a Session Project been Set?
   * 
   * @return bool 'TRUE' If Session Project set, Exception otherwise
   * @throws \Exception If no Session Project Set
   */
  public function checkProject() {
    if (!$this->hasProject()) {
      throw new \Exception('No Project has been set for the Session.', 4);
    }

    return true;
  }

  /**
   * Has a Session Container been Set?
   * 
   * @return bool 'TRUE' If Session Container set, Exception otherwise
   * @throws \Exception If no Session Container Set
   */
  public function checkContainer() {
    if (!$this->hasContainer()) {
      throw new \Exception('No Container has been set for the Session.', 5);
    }

    return true;
  }

}
