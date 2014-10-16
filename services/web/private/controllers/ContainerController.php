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
use shared\utility\StringUtilities;
use shared\controller\BaseServiceController;

/**
 * Controller used to Manage Run Executions
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ContainerController extends BaseServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * @return null
   */
  public function root() {
    // Create Action Context
    $context = new ActionContext('cwd');
    return $this->doAction($context);
  }

  /**
   * @param null $id
   * @return null
   */
  public function cdById($id = null) {
    // Create Action Context
    $context = new ActionContext('cd');

    // Call the Function
    return $this->doAction($context->setIfNotNull('container:id', isset($id) ? array('id' => (integer) $id) : null));
  }

  /**
   * @param $name
   * @return null
   */
  public function cdByName($name) {
    // Create Action Context
    $context = new ActionContext('cd');

    // Call the Function
    return $this->doAction($context->setIfNotNull('container:name', StringUtilities::nullOnEmpty($name)));
  }

  /**
   * @return null
   */
  public function cwd() {
    // Create Action Context
    $context = new ActionContext('cwd');
    return $this->doAction($context);
  }

  /**
   * @return null
   */
  public function mkdir($name) {
    // Create Action Context
    $context = new ActionContext('mkdir');
    // Call the Function
    return $this->doAction($context->setIfNotNull('container:name', StringUtilities::nullOnEmpty($name)));
  }

  /**
   * @return null
   */
  public function ls() {
    // Create Action Context
    $context = new ActionContext('ls');
    return $this->doAction($context);
  }

  /**
   * @return null
   */
  public function count() {
    // Create Action Context
    $context = new ActionContext('count');
    return $this->doAction($context);
  }

  /**
   * @param $action
   * @return bool
   */
  protected function startAction($action) {

    if ($action === 'Mkdir') {
      // Set DOCTRINE to Manual Transaction Commit
      $this->getEntityManager()->getConnection()->beginTransaction();
    }

    return true;
  }

  /**
   * @param $action
   * @return bool
   */
  protected function successAction($action) {

    if ($action === 'Mkdir') {
      // Everything Went OK : Commit Transaction
      $this->getEntityManager()->getConnection()->commit();
    }

    return true;
  }

  /**
   * @param $action
   * @return bool
   */
  protected function failedAction($action) {

    if ($action === 'Mkdir') {
      // Something Went Wrong : Rollback Transaction
      $this->getEntityManager()->getConnection()->rollback();
    }

    return false;
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * @param $context
   * @return object
   */
  protected function doRootAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    // Get Session Project
    $project_id = $current->owner;
    $project = \Project::find($project_id);
    if (!isset($project)) {
      throw new \Exception("Session Project[$project_id] is no longer valid.", 1);
    }

    // Get Root Container
    $root = $project->container;
    assert(isset($root));

    $this->sessionManager->setContainer($root->id);
    return $root;
  }

  /**
   * @param $context
   * @throws \Exception
   */
  protected function doCdAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    $next = null;
    $name = $context->getParameter('container:name');
    if (isset($name)) { // CD by Container Name
      $next = \Container::findChildContainer($current, $name);
      if (!isset($next)) {
        throw new \Exception("Container [$name] does not exist.", 1);
      }
    } else if ($context->hasParameter('container:id')) { // CD by Container ID
      $id = $context->getParameter('container:id');
      $next = \Container::findChildContainer($current, $id);
      if (!isset($next)) {
        throw new \Exception("Container [$id] does not exist.", 1);
      }
    } else { // CD ..
      $parent = $current->parent;
      if (isset($parent)) {
        $next = $parent;
      } else {
        throw new \Exception("Container [{$current->id}] is root.", 1);
      }
    }

    // Set the New Working Container
    $this->sessionManager->setContainer($next->link);
    return $next;
  }

  /**
   * @param $context
   * @return mixed
   */
  protected function doCwdAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    return $current;
  }

  /**
   * @param $context
   * @return mixed
   */
  protected function doMkdirAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    // Create the Child Container
    // TODO Verify if the name already exists
    $container = \Container::createChildContainer($current, $context->getParameter('container:name'));

    return $container;
  }

  /**
   * @param $context
   * @return mixed
   */
  protected function doLsAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    return \Container::listEntries($current);
  }

  /**
   * @param $context
   * @return mixed
   */
  protected function doCountAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    return \Container::countEntries($current);
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param type $context
   * @return type
   * @throws \Exception
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Check we have Basic Requirements
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();
    $this->sessionManager->checkOrganization();
    $this->sessionManager->checkProject();

    // Have active Container
    $this->sessionManager->checkContainer();
    // TODO If no Container Reset to Root Container?

    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required setup, before the Action Handler is Called.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Container for the Active Session
    $id = $this->sessionManager->getContainer();

    // Does the Container Exist?
    $container = \Container::find($id);
    if (!isset($container)) {
      throw new \Exception("Container[$id] not found", 1);
    }

    // Check the Owner is the Current Project
    $owner_id = $container->owner;

    // Is the Container Owned by the Project?
    $project_id = $this->sessionManager->getProject();
    if ($owner_id !== $project_id) { // NO
      throw new \Exception("Container[{$container->id}] does not belong to Current Project[$project_id] not found.", 2);
    }

    // TODO If Container Doesn't Exist - Clear it
    $context->setParameter('container', $container);

    return $context;
  }

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
      case 'Root':
      case 'Cd':
      case 'Cwd':
      case 'Mkdir':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'Ls':
        $return = array();
        foreach ($results as $entries) {
          $id = $entries->getId();
          $return[$id] = $entries->toArray();
          unset($return[$id]['id']);
        }
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /**
   * @return bool
   * @throws \Exception
   */
  public function checkContainer() {
    if (!$this->sessionManager->hasContainer()) {
      throw new \Exception('No Active Container.', 1);
    }

    return true;
  }

}
