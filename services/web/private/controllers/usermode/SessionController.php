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
namespace controllers\usermode;

use common\utility\Strings;
use common\utility\Arrays;
use api\controller\ActionContext;
use api\controller\BaseServiceController;

/**
 * Controller used to Manage all things related to a User Session
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SessionController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Initiate Session.
   * 
   * @return string HTTP Body Response
   */
  public function hello() {
    // Create Action Context
    return $this->doAction(new ActionContext('hello'));
  }

  /**
   * Authenticate the User and Create a Session.
   * 
   * @param string $name User name
   * @param string $hash Session Password Hash
   * @return string HTTP Body Response
   */
  public function login($name, $hash) {
    // Create Action Context
    $context = new ActionContext('login');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setIfNotNull('name', Strings::nullOnEmpty($name))
                            ->setIfNotNull('hash', Strings::nullOnEmpty($hash)));
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
                            ->setIfNotNull('name', Strings::nullOnEmpty($name))
                            ->setIfNotNull('password', Strings::nullOnEmpty($password)));
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
    // Create Action Context
    $context = new ActionContext('get');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'organization'));
  }

  /**
   * Set the Session's Organization.
   * 
   * @param string $id Organization Identifier
   * @return string HTTP Body Response
   */
  public function setOrganization($id) {
    // Create Action Context
    $context = new ActionContext('set');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'organization')
                            ->setParameter('value', (integer) $id));
  }

  /**
   * Clear Session Organization
   * 
   * @return string HTTP Body Response
   */
  public function clearOrganization() {
    // Create Action Context
    $context = new ActionContext('clear');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'organization'));
  }

  /**
   * Test if a Organization is defined for the Session.
   * 
   * @return string HTTP Body Response
   */
  public function isOrganizationSet() {
    // Create Action Context
    $context = new ActionContext('isset');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'organization'));
  }

  /**
   * Return's the Current Project Associated with the Session.
   * 
   * @return string HTTP Body Response
   */
  public function getProject() {
    // Create Action Context
    $context = new ActionContext('get');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'project'));
  }

  /**
   * Set the Session's Project.
   * 
   * @param string $id Project Identifier
   * @return string HTTP Body Response
   */
  public function setProject($id) {
    // Create Action Context
    $context = new ActionContext('set');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'project')
                            ->setParameter('value', (integer) $id));
  }

  /**
   * Clear Session Project
   * 
   * @return string HTTP Body Response
   */
  public function clearProject() {
    // Create Action Context
    $context = new ActionContext('clear');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'project'));
  }

  /**
   * Test if a Project is defined for the Session.
   * 
   * @return string HTTP Body Response
   */
  public function isProjectSet() {
    // Create Action Context
    $context = new ActionContext('isset');
    // Set Parameters for Context and Call Action
    return $this->doAction($context
                            ->setParameter('sub_type', 'project'));
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
                            ->setParameter('sub_type', 'variable')
                            ->setIfNotNull('variable', Strings::nullOnEmpty($variable)));
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
                            ->setParameter('sub_type', 'variable')
                            ->setIfNotNull('variable', Strings::nullOnEmpty($variable))
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
                            ->setParameter('sub_type', 'variable')
                            ->setIfNotNull('variable', Strings::nullOnEmpty($variable)));
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
                            ->setParameter('sub_type', 'variable')
                            ->setIfNotNull('variable', Strings::nullOnEmpty($variable)));
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Attempt to Login User to Session.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return array Hello Answer
   */
  protected function doHelloAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $name = $context->getParameter('name');
    $password = $context->getParameter('password', '');

    $reply = [ 'salt' => $this->sessionManager->getSalt()];
    $mode = $this->sessionManager->getMode();
    if (isset($mode)) {
      $reply['mode'] = $mode;
    }

    if ($this->sessionManager->isLoggedIn()) {
      $user = $this->sessionManager->getUser();
      $reply['user'] = Arrays::filter(['id', 'name', 'first_name', 'last_name'], $user);

      // Do we have an Organization Set in the Session?
      $organization = $this->sessionManager->getOrganization();
      if (isset($organization)) { // YES
        $reply['organization'] = Arrays::filter(['id', 'name'], $organization);

        // Do we have a Project Set for the Session?
        $project = $this->sessionManager->getProject();
        if (isset($project)) { // YES
          $reply['project'] = Arrays::filter(['id', 'name'], $project);
        }
      }
    }

    return$reply;
  }

  /**
   * Attempt to Login User to Session.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return User User Entity of Logged in User
   * @throws \Exception On failure to Login to Session
   */
  protected function doLoginAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $name = $context->getParameter('name');
    $hash = $context->getParameter('hash', '');

    // Is the name parameter set?
    if (!isset($name)) { // NO
      throw new \Exception('Missing Required Parameter [name]', 1);
    }

    // Get the User Information from the Database
    $user = \models\User::findFirstByName($name);
    // Calculate the MD5 Hash of the Password
    $salted_hash = $this->salted_hash($user->password);

    // Do we have a User with a Matching Password?
    if (($user !== FALSE) && ($salted_hash == $hash)) { // YES
      // Is a user logged in to the session?
      if ($this->sessionManager->isLoggedIn()) { // YES
        $current = $this->sessionManager->getUser();

        // Are we trying to login to the current session user?
        if ($current['id'] === $user->id) { // YES
          // Do nothing
          return $user;
        }

        // Log the Current User out of the Session
        $this->sessionManager->logout();
      }

      // Log the user into the current session
      $current = $this->sessionManager->login($user->toArray());
      return $user;
    }

    throw new \Exception('Invalid user name or password', 1);
  }

  /**
   * Attempt to Create SUDO Session for User.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
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
      } else {
        throw new \Exception('Not Logged in.', 1);
      }
    }

    throw new \Exception('Invalid user name or password', 2);
  }

  /**
   * Attempt to Terminate Login Session
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return boolean 'true' Login Session Terminated, 'false' no Login Session or
   *   failed to terminate Login Session
   */
  protected function doLogoutAction($context) {
    return $this->sessionManager->logout();
  }

  /**
   * Attempt to Terminate SUDO Session
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return boolean 'true' left sudo session, 'false' sudo session did not exist, 
   *   or failed to leave session
   */
  protected function doSudoexitAction($context) {
    return $this->sessionManager->leaveSudo();
  }

  /**
   * Attempt to Retrieve Current User for Login Session
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
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
   * Retrieve User's Session Variable
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return mixed Value of Session Variable or 'null' if not set
   */
  protected function doGetAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Default Value
    $result = null;

    // Process ISSET based on SUB_TYPE
    $sub_type = $context->getParameter('sub_type');
    assert('isset($sub_type)');
    switch ($sub_type) {
      case 'organization':
        $result = $this->sessionManager->getOrganization();
        break;
      case 'project':
        $result = $this->sessionManager->getProject();
        break;
      default:
        $variable = $context->getParameter('variable');
        $result = $this->sessionManager->getVariable("user_$variable");
    }

    return $result;
  }

  /**
   * Set User's Session Variable
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return mixed Previous Value of Session Variable or 'null' if not set
   */
  protected function doSetAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Default Value
    $result = null;

    // Process ISSET based on SUB_TYPE
    $sub_type = $context->getParameter('sub_type');
    assert('isset($sub_type)');
    switch ($sub_type) {
      case 'organization':
        $result = $context->getParameter('value');
        // TODO Verify if user has access to the Organization  (i.e. is linked to the organization with the correct permissions)
        $this->sessionManager->setOrganization($result->toArray());
        break;
      case 'project':
        $result = $context->getParameter('value');
        // Change the Organization and Project at the SAME TIME, to maintain consistency
        // TODO Verify if user has access to the Organization and the Project (i.e. is linked to the organization/project with the correct permissions)
        $this->sessionManager->setProject($result->toArray() /* , $project->getContainer()->getId() */);
        break;
      default:
        $variable = $context->getParameter('variable');
        $value = $context->getParameter('value');
        $result = $this->sessionManager->setVariable("user_$variable", $value);
    }

    return $result;
  }

  /**
   * Remove User's Session Variable
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return mixed Previous Value of Session Variable or 'null' if not set
   */
  protected function doClearAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Default Value
    $result = null;

    // Process ISSET based on SUB_TYPE
    $sub_type = $context->getParameter('sub_type');
    assert('isset($sub_type)');
    switch ($sub_type) {
      case 'organization':
        $result = $this->sessionManager->clearOrganization();
        break;
      case 'project':
        $result = $this->sessionManager->clearProject();
        break;
      default:
        $variable = $context->getParameter('variable');
        $result = $this->sessionManager->clearVariable("user_$variable");
    }

    return $result;
  }

  /**
   * Test if a User's Session Variable Exists
   *  
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return boolean 'true' Session Variable is Set, 'false' otherwise
   */
  protected function doIssetAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Default Value
    $result = false;

    // Process ISSET based on SUB_TYPE
    $sub_type = $context->getParameter('sub_type');
    assert('isset($sub_type)');
    switch ($sub_type) {
      case 'organization':
        $result = $this->sessionManager->hasOrganization();
        break;
      case 'project':
        $result = $this->sessionManager->hasProject();
        break;
      default:
        $variable = $context->getParameter('variable');
        $result = $this->sessionManager->isVariableSet("user_$variable");
    }

    return $result;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: STAGE : INIT ACTION
   * ---------------------------------------------------------------------------
   */

  /**
   * Perfom Session Check for the Given Action Context.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
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
      case 'Get':
      case 'Set':
      case 'Clear':
      case 'Isset':
        $this->sessionManager->checkLoggedIn();
        break;
    }

    if ($action === 'Get') {
      $sub_type = $context->getParameter('sub_type');
      switch ($sub_type) {
        case 'organization':
          $this->sessionManager->checkOrganization();
          break;
        case 'project':
          $this->sessionManager->checkOrganization();
          $this->sessionManager->checkProject();
          break;
      }
    }
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: STAGE : DO CALL
   * ---------------------------------------------------------------------------
   */

  /**
   * Perfom Session Check for the Given Action Context.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On failure of any of the Session Checks
   */
  protected function contextChecksSet($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $sub_type = $context->getParameter('sub_type');
    switch ($sub_type) {
      case 'organization':
        // Get the Organization's Identifier
        $id = $context->getParameter('value');

        // Get the Organization's Information from the Database
        $org = \models\Organization::findFirst($id);
        // Did we find the Organization?
        if ($org === FALSE) { // NO
          throw new \Exception("Organization not found[$id]", 1);
        }

        $context->setParameter('value', $org);
        break;
      case 'project':
        // Get the Project's Identifier
        $id = $context->getParameter('value');

        // Get the Project's Information from the Database
        $project = \models\Project::findFirst($id);
        // Did we find the Project?
        if ($project === FALSE) { // NO
          throw new \Exception("Project not found[$id]", 1);
          // Change the Organization and Project at the SAME TIME, to maintain consistency
          // TODO Verify if user has access to the Organization and the Project (i.e. is linked to the organization/project with the correct permissions)
          $this->sessionManager->setOrganization($project->organization, $id /* , $project->getContainer()->getId() */);
          return $project;
        }

        $context->setParameter('value', $project);
    }


    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGE : RENDER
   * ---------------------------------------------------------------------------
   */

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
      case 'Sudo':
      case 'Whoami':
      case 'Login':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'Get':
      case 'Set':
        if (isset($results) && is_array($results)) {
          $return = $results->toArray();
          break;
        }
      default:
        $return = $results;
    }

    return $return;
  }

  /**
   * Calculate the Salted Password Hash for the given content
   * 
   * @param string $value Value to Calculate Salted Hash Against 
   * @return string Salted Password Hash
   */
  protected function salted_hash($value) {
    $salt = $this->sessionManager->getSalt();
    $hash_sha256 = hash("sha256", $salt . $value);
    return $hash_sha256;
  }

}
