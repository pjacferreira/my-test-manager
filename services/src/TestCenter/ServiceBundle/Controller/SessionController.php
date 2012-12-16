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

namespace TestCenter\ServiceBundle\Controller;

use Library\StringUtilities;
use Library\ArrayUtilities;
use TestCenter\ServiceBundle\API\BaseServiceController;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of SessionController
 *
 * @author Paulo Ferreira
 */
class SessionController
  extends BaseServiceController {

  /**
   * @param $name
   * @param $password
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function loginAction($name, $password) {
    return $this->doAction('login',
                           array('name' => StringUtilities::nullOnEmpty($name),
        'password' => StringUtilities::nullOnEmpty($password)));
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function logoutAction() {
    return $this->doAction('logout');
  }

  /**
   * @param $name
   * @param $password
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function sudoAction($name, $password) {
    return $this->doAction('sudo',
                           array('name' => StringUtilities::nullOnEmpty($name),
        'password' => StringUtilities::nullOnEmpty($password)));
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function sudoExitAction() {
    return $this->doAction('sudoexit');
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function whoamiAction() {
    return $this->doAction('whoami');
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getOrganizationAction() {
    return $this->doAction('get_organization');
  }

  /**
   * @param $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function setOrganizationAction($id) {
    return $this->doAction('set_organization', array('id' => (integer) $id));
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getProjectAction() {
    return $this->doAction('get_project');
  }

  /**
   * @param $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function setProjectAction($id) {
    return $this->doAction('set_project', array('id' => (integer) $id));
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getVariableAction($variable) {
    return $this->doAction('get', array('variable' => $variable));
  }

  /**
   * @param $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function setVariableAction($variable, $value) {
    return $this->doAction('set',
                           array('variable' => $variable, 'value' => $value));
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function clearVariableAction($variable) {
    return $this->doAction('clear', array('variable' => $variable));
  }

  /**
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function issetVariableAction($variable) {
    return $this->doAction('isset', array('variable' => $variable));
  }

  /**
   * @param null $parameters
   * @return mixed
   * @throws \Exception
   */
  protected function doLoginAction($parameters = null) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');

    $name = ArrayUtilities::extract($parameters, 'name');
    $password = ArrayUtilities::extract($parameters, 'password', '');

    // Test that we have Required Parameters
    if (!isset($name)) {
      throw new \Exception('Missing Required Parameter [name]', 1);
    }

    // Get the new user
    $user = $this->getDoctrine()->getRepository('TestCenterModelBundle:User')->findOneByName($name);
    if (isset($user) && ($user->getPassword() == md5($password))) {

      // Verify if we are logging in to the current active user (again)
      $current = SessionManager::getUser();
      if (isset($current)) {
        if ($current == $user->getId()) {
          // YES : Do nothing
          return $user;
        }

        // NO; Logout Current User
        $this->doLogoutAction();
      }

      // Login New User
      SessionManager::login($user->getId());
      return $user;
    }

    throw new \Exception('Invalid user name or password', 2);
  }

  /**
   * @param $parameters
   * @return mixed
   * @throws \Exception
   */
  protected function doSudoAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');

    $name = ArrayUtilities::extract($parameters, 'name');
    $password = ArrayUtilities::extract($parameters, 'password', '');

    // Test that we have Required Parameters
    if (!isset($name)) {
      throw new \Exception('Missing Required Parameter [name]', 1);
    }

    // Extract User and Test Password
    $user = $this->getDoctrine()->getRepository('TestCenterModelBundle:User')->findOneByName($name);
    if (isset($user) && ($user->getPassword() == md5($password))) {

      // Verify if we are trying to use the same sudo user (again)
      if (SessionManager::isSudoActive()) {
        $current = SessionManager::getUser();
        assert('isset($current)');

        if ($current == $user->getId()) {
          // YES : Do nothing
          return $user;
        }

        // NO; Leaver Current Sudo Session
        SessionManager::leaveSudo();
      }

      SessionManager::enterSudo($user->getId());
      return $user;
    }

    throw new \Exception('Invalid user name or password', 2);
  }

  /**
   * @return null
   */
  protected function doLogoutAction() {
    SessionManager::logout();
    return true;
  }

  /**
   * @return bool
   */
  protected function doSudoexitAction() {

    if (SessionManager::isSudoActive()) {
      SessionManager::leaveSudo();
    }

    return true;
  }

  /**
   * @return object
   * @throws \Exception
   */
  protected function doWhoamiAction() {
    $id = SessionManager::getUser();

    // Extract User and Test Password
    $user = $this->getDoctrine()->getRepository('TestCenterModelBundle:User')->find($id);
    if (isset($user)) {
      return $user;
    }
    throw new \Exception('Not Logged in.', 1);
  }

  /**
   * @return object
   * @throws \Exception
   */
  protected function doGetOrganizationAction() {
    // Get the Current Organization ID
    $id = SessionManager::getOrganization();

    if (isset($id)) { // Session Organization Set
      // Get the Actual Object
      $org = $this->getDoctrine()->getRepository('TestCenterModelBundle:Organization')->find($id);
      if (isset($org)) {
        return $org;
      }

      // Organization No Longer Valid
      SessionManager::clearOrganization();
    }

    return null;
  }

  /**
   * @param $parameters
   * @return object
   * @throws \Exception
   */
  protected function doSetOrganizationAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');
    $id = ArrayUtilities::extract($parameters, 'id');

    // Get the Organization
    $org = $this->getDoctrine()->getRepository('TestCenterModelBundle:Organization')->find($id);
    if (isset($org)) {
      // TODO Verify if user has access to the Organization and the Project (i.e. is linked to the organization with the correct permissions)
      SessionManager::setOrganization($id);
      return $org;
    }

    throw new \Exception("Organization not found[$id]", 1);
  }

  /**
   * @return object
   * @throws \Exception
   */
  protected function doGetProjectAction() {
    // Get the Current Project ID
    $id = SessionManager::getProject();

    if (isset($id)) { // Session Project Set
      // Get the Actual Object
      $project = $this->getDoctrine()->getRepository('TestCenterModelBundle:Project')->find($id);
      if (isset($project)) {
        return $project;
      }

      // Project ID No Longer Valid
      SessionManager::clearProject();
    }

    return null;
  }

  /**
   * @param $parameters
   * @return object
   * @throws \Exception
   */
  protected function doSetProjectAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');
    $id = ArrayUtilities::extract($parameters, 'id');

    // Get the Project
    $project = $this->getDoctrine()->getRepository('TestCenterModelBundle:Project')->find($id);
    if (isset($project)) {
      // Get the Organization Associated with the Project
      $org = $project->getOrganization();

      // Change the Organization and Project at the SAME TIME, to maintain consistency
      // TODO Verify if user has access to the Organization and the Project (i.e. is linked to the organization/project with the correct permissions)
      SessionManager::setOrganization($org->getId(), $id,
                                      $project->getContainer()->getId());
      return $project;
    }

    throw new \Exception("Project not found[$id]", 1);
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doGetAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');
    $variable = ArrayUtilities::extract($parameters, 'variable');

    return SessionManager::getVariable("user_$variable");
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doSetAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');

    $variable = ArrayUtilities::extract($parameters, 'variable');
    $value = ArrayUtilities::extract($parameters, 'value');

    return SessionManager::setVariable("user_$variable", $value);
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doClearAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');

    $variable = ArrayUtilities::extract($parameters, 'variable');

    return SessionManager::clearVariable("user_$variable");
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function doIssetAction($parameters) {
    // Parameter Validation
    assert('isset($parameters) && is_array($parameters)');

    $variable = ArrayUtilities::extract($parameters, 'variable');

    return SessionManager::issetVaraible("user_$variable");
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    // Need a Session for all the Session Commands
    $this->checkInSession();

    switch ($action) {
      case 'Whoami':
      case 'SetOrganization':
      case 'SetProject':
        $this->checkLoggedIn();
        break;
      case 'GetOrganization':
        $this->checkLoggedIn();
        $this->checkOrganization();
        break;
      case 'GetProject':
        $this->checkLoggedIn();
        $this->checkOrganization();
        $this->checkProject();
        break;
    }

    return $parameters;
  }

  /**
   * @param $action
   * @param $results
   * @param $format
   */
  protected function preRender($action, $results, $format) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($format) && is_string($format)');

    $return = $results;
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
    }

    return $return;
  }

}
