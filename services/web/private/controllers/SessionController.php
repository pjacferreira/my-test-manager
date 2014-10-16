<?php

/*
 * Test Center - Compliance Testing Application (Web Services)
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

use shared\controller\ActionContext;
use shared\controller\BaseServiceController;
use shared\utility\StringUtilities;

/**
 * Controller used to Manage all things related to a User Session
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SessionController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Authenticate the User and Create a Session.
   * 
   * @param string $name User name
   * @param string $password User's Password 
   * @return string HTTP Body Response
   */
  public function login($name, $password) {
    // Create Action Context
    $context = new ActionContext('login');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('name', StringUtilities::nullOnEmpty($name))
                            ->setIfNotNull('password', StringUtilities::nullOnEmpty($password)));
  }

  /**
   * Terminate the current session
   * 
   * @return string HTTP Body Response
   */
  public function logout() {
    return $this->doAction(new ActionContext('logout'));
  }

  /**
   * Allows Another User to Temporarily take over an EXISTING Session.
   * 
   * @param string $name User name
   * @param string $password User's Password 
   * @return string HTTP Body Response
   */
  public function sudo($name, $password) {
    // Create Action Context
    $context = new ActionContext('sudo');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('name', StringUtilities::nullOnEmpty($name))
                            ->setIfNotNull('password', StringUtilities::nullOnEmpty($password)));
  }

  /**
   * Terminate the Sudo User's Session. Reverts back to the Original Session
   * User
   * 
   * @return string HTTP Body Response
   */
  public function sudoExit() {
    return $this->doAction(new ActionContext('sudoexit'));
  }

  /**
   * Return's the User for the Current Session
   * 
   * @return string HTTP Body Response
   */
  public function whoami() {
    return $this->doAction(new ActionContext('whoami'));
  }

  /**
   * Return's the Current Organization Associated with the Session.
   * 
   * @return string HTTP Body Response
   */
  public function getOrganization() {
    return $this->doAction(new ActionContext('get_organization'));
  }

  /**
   * Set the Session's Organization.
   * 
   * @param string $id Organization Identifier
   * @return string HTTP Body Response
   */
  public function setOrganization($id) {
    // Create Action Context
    $context = new ActionContext('set_organization');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('id', (integer) $id));
  }

  /**
   * Return's the Current Project Associated with the Session.
   * 
   * @return string HTTP Body Response
   */
  public function getProject() {
    return $this->doAction(new ActionContext('get_project'));
  }

  /**
   * Set the Session's Project.
   * 
   * @param string $id Project Identifier
   * @return string HTTP Body Response
   */
  public function setProject($id) {
    // Create Action Context
    $context = new ActionContext('set_project');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('id', (integer) $id));
  }

  /**
   * Retrieves the Value of a Session Variable.
   * 
   * @param string $variable Variable Name
   * @return string HTTP Body Response
   */
  public function getVariable($variable) {
    // Create Action Context
    $context = new ActionContext('get');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('variable', StringUtilities::nullOnEmpty($variable)));
  }

  /**
   * Sets the Value of a Session Variable.
   * 
   * @param string $variable Variable Name
   * @param string $value Variable's Value
   * @return string HTTP Body Response
   */
  public function setVariable($variable, $value) {
    // Create Action Context
    $context = new ActionContext('set');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('variable', StringUtilities::nullOnEmpty($variable))
                            ->setParameter('value', $value));
  }

  /**
   * Remove a Variable, and it's Value, from the Session
   * 
   * @param string $variable Variable Name
   * @return string HTTP Body Response
   */
  public function clearVariable($variable) {
    // Create Action Context
    $context = new ActionContext('clear');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('variable', StringUtilities::nullOnEmpty($variable)));
  }

  /**
   * Test if a Variable is defined for the Session.
   * 
   * @param string $variable Variable Name
   * @return string HTTP Body Response
   */
  public function isVariableSet($variable) {
    // Create Action Context
    $context = new ActionContext('isset');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('variable', StringUtilities::nullOnEmpty($variable)));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Attempt to Login User to Session.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return User User Entity of Logged in User
   * @throws \Exception On failure to Login to Session
   */
  protected function doLoginAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $name = $context->getParameter('name');
    $password = $context->getParameter('password', '');

    // Is the name parameter set?
    if (!isset($name)) { // NO
      throw new \Exception('Missing Required Parameter [name]', 1);
    }

    // Get the User Information from the Database
    $user = User::findFirstByName($name);
    // Calculate the MD5 Hash of the Password
    $password = md5($password);

    // Do we have a User with a Matching Password?
    if (($user !== FALSE) && ($user->password == $password)) { // YES
      // Is a user logged in to the session?
      if ($this->sessionManager->isLoggedIn()) { // YES
        $current = $this->sessionManager->getUser();

        // Are we trying to login to the current session user?
        if ($current === $user->id) { // YES
          // Do nothing
          return $user;
        }

        // Log the Current User out of the Session
        $this->sessionManager->logout();
      }

      // Log the user into the current session
      $current = $this->sessionManager->login($user->id, $user->name);
      return $user;
    }

    throw new \Exception('Invalid user name or password', 1);
  }

  /**
   * Attempt to Create SUDO Session for User.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return User User Entity of SUDO User
   * @throws \Exception On failure to Create SUDO Session
   */
  protected function doSudoAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $name = $context->getParameter('name');
    $password = $context->getParameter('password', '');

    // Is the name parameter set?
    if (!isset($name)) { // NO
      throw new \Exception('Missing Required Parameter [name]', 1);
    }

    // Get the User Information from the Database
    $user = User::findFirstByName($name);
    // Calculate the MD5 Hash of the Password
    $password = md5($password);

    // Do we have a User with a Matching Password?
    if (isset($user) && ($user->password == $password)) { // YES
      // Is a user logged in to the session?
      if ($this->sessionManager->isLoggedIn()) { // YES
        $current = $this->sessionManager->getUser();

        // Are we trying to sudo to the current session user?
        if ($current !== $user->id) { // NO
          // Are we already in a Sudo Session
          if ($this->sessionManager->isSudoActive()) { // YES
            // Leave Current Sudo Session
            $this->sessionManager->leaveSudo();
          }

          // Re-test Current Active User
          $current = $this->sessionManager->getUser();

          // Are we trying to sudo to the current session user?
          if ($current !== $user->id) { // NO
            $this->sessionManager->enterSudo($user->id, $user->name);
          }
        }

        return $user;
      }
    }

    throw new \Exception('Invalid user name or password', 1);
  }

  /**
   * Attempt to Terminate Login Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return boolean 'true' Login Session Terminated, 'false' no Login Session or
   *   failed to terminate Login Session
   */
  protected function doLogoutAction($context) {
    return $this->sessionManager->logout();
  }

  /**
   * Attempt to Terminate SUDO Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return boolean 'true' left sudo session, 'false' sudo session did not exist, 
   *   or failed to leave session
   */
  protected function doSudoexitAction($context) {
    return $this->sessionManager->leaveSudo();
  }

  /**
   * Attempt to Retrieve Current User for Login Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return User User Entity of Current Logged in User
   * @throws \Exception On No Login Session or Invalid Session User
   */
  protected function doWhoamiAction($context) {
    // Get the Current Session User's ID
    $id = $this->sessionManager->getUser();

    // Do we currently have a user logged in?
    if (isset($id)) { // YES
      // Get the User's Information from the Database
      $user = User::findFirst($id);
      // Did we find the information for the session user?
      if (isset($user)) { // YES
        return $user;
      }

      throw new \Exception('Invalid Session User!?.', 1);
    }

    throw new \Exception('Not Logged in.', 2);
  }

  /**
   * Attempt to Retrieve Current Organization for Login Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return Organization Organization Entity Currently Set for the Session or
   *   'null' if no Session Organization
   * @throws \Exception On Failure to Retrieve Organization Entity
   */
  protected function doGetOrganizationAction($context) {
    // Get the Current Session Organization's ID
    $id = $this->sessionManager->getOrganization();

    // Do we currently have a organization set?
    if (isset($id)) { // YES
      // Get the Organization's Information from the Database
      $org = Organization::findFirst($id);
      // Did we find the Organization?
      if ($org !== FALSE) { // YES
        return $org;
      }

      // Current Session Organization's is not Valid
      $this->sessionManager->clearOrganization();
    }

    return null;
  }

  /**
   * Attempt to Default Organization for Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return Organization Organization Entity Set for the Session
   * @throws \Exception On Failure
   */
  protected function doSetOrganizationAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Organization's Identifier
    $id = $context->getParameter('id');
    // Get the Organization's Information from the Database
    $org = Organization::findFirst($id);
    // Did we find the Organization?
    if ($org !== FALSE) { // YES
      // TODO Verify if user has access to the Organization and the Project (i.e. is linked to the organization with the correct permissions)
      $this->sessionManager->setOrganization($id);
      return $org;
    }

    throw new \Exception("Organization not found[$id]", 1);
  }

  /**
   * Attempt to Retrieve Current Project for Login Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return Project Project Entity Currently Set for the Session or
   *   'null' if no Session Project
   * @throws \Exception On Failure to Retrieve Project Entity
   */
  protected function doGetProjectAction($context) {
    // Get the Current Session Project's ID
    $id = $this->sessionManager->getProject();

    // Do we currently have a project set?
    if (isset($id)) { // YES
      // Get the Project's Information from the Database
      $project = Project::findFirst($id);
      // Did we find the Project?
      if ($project !== FALSE) { // YES
        return $project;
      }

      // Current Session Project's is not Valid
      $this->sessionManager->clearProject();
    }

    return null;
  }

  /**
   * Attempt to Default Project for Session
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return Organization Project Entity Set for the Session
   * @throws \Exception On Failure
   */
  protected function doSetProjectAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Project's Identifier
    $id = $context->getParameter('id');
    // Get the Project's Information from the Database
    $project = Project::findFirst($id);
    // Did we find the Project?
    if ($project !== FALSE) { // YES
      // Change the Organization and Project at the SAME TIME, to maintain consistency
      // TODO Verify if user has access to the Organization and the Project (i.e. is linked to the organization/project with the correct permissions)
      $this->sessionManager->setOrganization($project->organization, $id /* , $project->getContainer()->getId() */);
      return $project;
    }

    throw new \Exception("Project not found[$id]", 1);
  }

  /**
   * Retrieve User's Session Variable
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return mixed Value of Session Variable or 'null' if not set
   */
  protected function doGetAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $variable = $context->getParameter('variable');
    return $this->sessionManager->getVariable("user_$variable");
  }

  /**
   * Set User's Session Variable
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return mixed Previous Value of Session Variable or 'null' if not set
   */
  protected function doSetAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $variable = $context->getParameter('variable');
    $value = $context->getParameter('value');

    return $this->sessionManager->setVariable("user_$variable", $value);
  }

  /**
   * Remove User's Session Variable
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return mixed Previous Value of Session Variable or 'null' if not set
   */
  protected function doClearAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $variable = $context->getParameter('variable');
    return $this->sessionManager->clearVariable("user_$variable");
  }

  /**
   * Test if a User's Session Variable Exists
   *  
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return boolean 'true' Session Variable is Set, 'false' otherwise
   */
  protected function doIssetAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $variable = $context->getParameter('variable');
    return $this->sessionManager->isVariableSet("user_$variable");
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: do_initAction - Checks
   * ---------------------------------------------------------------------------
   */

  /**
   * Perfom Session Check for the Given Action Context.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On failure of any of the Session Checks
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->sessionManager->checkInSession();

    // Get the Action Name
    $action = $context->getAction();
    assert('isset($action)');
    switch ($action) {
      case 'Whoami':
      case 'SetOrganization':
      case 'SetProject':
        $this->sessionManager->checkLoggedIn();
        break;
      case 'GetOrganization':
        $this->sessionManager->checkLoggedIn();
        $this->sessionManager->checkOrganization();
        break;
      case 'GetProject':
        $this->sessionManager->checkLoggedIn();
        $this->sessionManager->checkOrganization();
        $this->sessionManager->checkProject();
        break;
    }

    // Nothing Changed
    return null;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: Phases
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required setup, before we perform final rendering of the Action's
   * Result.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
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
      case 'Sudo':
      case 'Whoami':
      case 'Login':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'GetOrganization':
      case 'SetOrganization':
      case 'GetProject':
      case 'SetProject':
        $return = isset($results) ? $results->toArray() : null;
        break;
      default:
        $return = $results;
    }

    return $return;
  }

}
