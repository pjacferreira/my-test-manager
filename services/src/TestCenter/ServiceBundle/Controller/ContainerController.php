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
use TestCenter\ModelBundle\Entity\ContainerEntry;
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
    return $this->doAction('root', null);
  }

  /**
   * @param null $id
   * @return null
   */
  public function cdByIdAction($id = null) {
    return $this->doAction('cd',
                           isset($id) ? array('id' => (integer) $id) : null);
  }

  /**
   * @param $name
   * @return null
   */
  public function cdByNameAction($name) {
    return $this->doAction('cd',
                           array('name' => StringUtilities::nullOnEmpty($name)));
  }

  /**
   * @return null
   */
  public function cwdAction() {
    return $this->doAction('cwd', null);
  }

  /**
   * @return null
   */
  public function mkdirAction($name) {
    return $this->doAction('mkdir',
                           array('name' => StringUtilities::nullOnEmpty($name)));
  }

  /**
   * @return null
   */
  public function lsAction() {
    return $this->doAction('ls', null);
  }

  /**
   * @return null
   */
  public function countAction() {
    return $this->doAction('count', null);
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
   * @param $parameters
   * @return object
   */
  protected function doRootAction($parameters) {
    // Get the Current Container
    $current = $parameters['container'];
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
   * @param $parameters
   * @throws \Exception
   */
  protected function doCdAction($parameters) {
    // Get the Current Container
    $current = $parameters['container'];
    assert(isset($current));

    $next = null;
    $name = $parameters['name'];
    if (isset($name)) { // CD by Container Name
      $next = $this->getRepository()->findChildContainer($current, $name);
      if (!isset($next)) {
        throw new \Exception("Container [$name] does not exist.", 1);
      }
    } else if (isset($parameters['id'])) { // CD by Container ID
      $id = $parameters['id'];
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
   * @param $parameters
   * @return mixed
   */
  protected function doCwdAction($parameters) {
    // Get the Current Container
    $current = $parameters['container'];
    assert(isset($current));

    return $current;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doMkdirAction($parameters) {
    // Get the Current Container
    $current = $parameters['container'];
    assert(isset($current));

    // Create the Child Container
    // TODO Verify if the name already exists
    $repository = $this->getRepository();
    $container = $repository->createChildContainer($current, $parameters['name']);

    return $container;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doLsAction($parameters) {
    // Get the Current Container
    $current = $parameters['container'];
    assert(isset($current));

    return $this->getRepository()->listEntries($current);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doCountAction($parameters) {
    // Get the Current Container
    $current = $parameters['container'];
    assert(isset($current));

    return $this->getRepository()->countEntries($current);
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($action, $parameters) {
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
    $parameters['container_id'] = $id;
    $parameters['container'] = $container;

    return $parameters;
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
    }

    return $return;
  }

}
