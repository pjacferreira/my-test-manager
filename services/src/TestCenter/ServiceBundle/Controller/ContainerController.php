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
use TestCenter\ServiceBundle\API\ActionContext;
use TestCenter\ServiceBundle\API\EntityServiceController;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of ContainerController
 *
 * @author Paulo Ferreira
 */
class ContainerController
  extends EntityServiceController {

  /**
   * @param $entity
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\Container');
  }

  /**
   * @return null
   */
  public function rootAction() {
    // Create Action Context
    $context = new ActionContext('cwd');
    return $this->doAction($context);
  }

  /**
   * @param null $id
   * @return null
   */
  public function cdByIdAction($id = null) {
    // Create Action Context
    $context = new ActionContext('cd');
    $context = $context
      ->setIfNotNull('id', isset($id) ? array('id' => (integer) $id) : null);

    // Call the Function
    return $this->doAction($context);
  }

  /**
   * @param $name
   * @return null
   */
  public function cdByNameAction($name) {
    // Create Action Context
    $context = new ActionContext('cd');
    $context = $context
      ->setIfNotNull('name', StringUtilities::nullOnEmpty($name));

    // Call the Function
    return $this->doAction($context);
  }

  /**
   * @return null
   */
  public function cwdAction() {
    // Create Action Context
    $context = new ActionContext('cwd');
    return $this->doAction($context);
  }

  /**
   * @return null
   */
  public function mkdirAction($name) {
    // Create Action Context
    $context = new ActionContext('mkdir');
    $context = $context
      ->setIfNotNull('name', StringUtilities::nullOnEmpty($name));

    // Call the Function
    return $this->doAction($context);
  }

  /**
   * @return null
   */
  public function lsAction() {
    // Create Action Context
    $context = new ActionContext('ls');
    return $this->doAction($context);
  }

  /**
   * @return null
   */
  public function countAction() {
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

  /**
   * @param $context
   * @return object
   */
  protected function doRootAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    // Get Session Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Project');
    $project_id = $current->getOwner();
    $project = $repository->find($project_id);
    if (!isset($project)) {
      throw new \Exception("Session Project[$project_id] is no longer valid.", 1);
    }

    // Get Root Container
    $root = $project->getContainer();
    assert(isset($root));

    SessionManager::setContainer($root->getId());
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
    $name = $context->getParameter('name');
    if (isset($name)) { // CD by Container Name
      $next = $this->getRepository()->findChildContainer($current, $name);
      if (!isset($next)) {
        throw new \Exception("Container [$name] does not exist.", 1);
      }
    } else if ($context->hasParameter('id')) { // CD by Container ID
      $id = $context->getParameter('id');
      $next = $this->getRepository()->findChildContainer($current, $id);
      if (!isset($next)) {
        throw new \Exception("Container [$id] does not exist.", 1);
      }
    } else { // CD ..
      $parent = $current->getParent();
      if (isset($parent)) {
        $next = $parent;
      } else {
        throw new \Exception("Container [{$current->getId()}] is root.", 1);
      }
    }

    // Set the New Working Container
    SessionManager::setContainer($next->getLink());
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
    $repository = $this->getRepository();
    $container = $repository->createChildContainer($current,
                                                   $context->getParameter('name'));

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

    return $this->getRepository()->listEntries($current);
  }

  /**
   * @param $context
   * @return mixed
   */
  protected function doCountAction($context) {
    // Get the Current Container
    $current = $context->getParameter('container');
    assert(isset($current));

    return $this->getRepository()->countEntries($current);
  }

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
    $this->checkInSession();
    $this->checkLoggedIn();
    $this->checkOrganization();
    $this->checkProject();

    // Have active Container
    $this->checkContainer();
    // TODO If no Container Reset to Root Container?

    $id = SessionManager::getContainer();

    // Get the Current Working Container
    $container = $this->getRepository()->find($id);
    if (!isset($container)) {
      throw new \Exception("Container[$id] not found", 1);
    }

    // Check the Owner is the Current Project
    $owner_id = $container->getOwner();
    $project_id = SessionManager::getProject();
    if ($owner_id !== $project_id) {
      throw new \Exception("Container[{$container->getId()}] does not belong to Current Project[$project_id] not found.", 2);
    }

    // TODO If Container Doesn't Exist - Clear it
    $context->setParameter('container_id', $id);
    $context->setParameter('container', $container);

    return $context;
  }

  /**
   * 
   * @param type $context
   * @return type
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
    if (!SessionManager::hasContainer()) {
      throw new \Exception('No Active Container.', 1);
    }

    return true;
  }

}
