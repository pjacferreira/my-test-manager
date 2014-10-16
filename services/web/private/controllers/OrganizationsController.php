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
use api\controller\CrudServiceController;

/**
 * Controller used to Manage Organization Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class OrganizationsController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Create an Organization (if it doesn't already exist) with the Given Name.
   * If more HTTP Request Parameters are given, then use those, otherwise use
   * default values for the remaining fields.
   * 
   * @param string $name Organization name
   * @return string HTTP Body Response
   */
  public function create($name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Call Action
    return $this->doAction($context->setIfNotNull('organization:name', StringUtilities::nullOnEmpty($name)));
  }

  /**
   * Retrieve the Organization with the Given ID.
   * 
   * @param integer $id Organization's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Call Action
    return $this->doAction($context->setParameter('organization:id', (integer) $id));
  }

  /**
   * Retrieve the Organization with the Given Name.
   * 
   * @param string $name Organization's Unique Name
   * @return string HTTP Body Response
   */
  public function readByName($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Call Action
    return $this->doAction($context->setIfNotNull('organization:name', StringUtilities::nullOnEmpty($name)));
  }

  /**
   * Update the Organization, with the Given ID, information.
   * 
   * @param integer $id Organization's Unique Identifier
   * @return string HTTP Body Response
   */
  public function update($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Call Action
    return $this->doAction($context->setParameter('organization:id', (integer) $id));
  }

  /**
   * Update the Organization, with the Given Name, information.
   * 
   * @param string $name Organization's Unique Name
   * @return string HTTP Body Response
   */
  public function updateByName($name) {
    // Create Action Context
    $context = new ActionContext('update');
    // Call Action
    return $this->doAction($context->setIfNotNull('organization:name', StringUtilities::nullOnEmpty($name)));
  }

  /**
   * Delete the Organization with the Given ID.
   * 
   * @param integer $id Organization's Unique Identifier
   * @return string HTTP Body Response
   */
  public function delete($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setParameter('organization:id', (integer) $id));
  }

  /**
   * Delete the Organization with the Given Name.
   * 
   * @param string $name Organization's Unique Name
   * @return string HTTP Body Response
   */
  public function deleteByName($name) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setIfNotNull('organization:name', StringUtilities::nullOnEmpty($name)));
  }

  /**
   * List Organization Entities in the Database.
   * 
   * Note: We can pass in request parameter to limit and organize the list returned.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Organizations
   * __sort - Used to organize the sort order of the list
   * __limit - Limit the number of entities return in the list
   * 
   * @return string HTTP Body Response
   */
  public function listOrganizations() {
    // Create Action Context
    $context = new ActionContext('list');

    return $this->doAction($context);
  }

  /**
   * Count the Number of Organization Entities in the Database.
   * 
   * Note: We can pass in request parameter to limit the entities being considered.
   * 
   * Request Parameter:
   * __filter - Used to filter the list of Organizations
   * 
   * @return integer Number of Organizations
   */
  public function countOrganizations() {
    // Create Action Context
    $context = new ActionContext('count');

    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform checks that validate the Session State.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Need a Session for all the Session Commands
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();

    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required preparation, before the Delete Action Handler is Called.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preActionDelete($context) {
    // Call the General Handler 1st (to Setup Context)
    $context = $this->preAction($context);

    /* Implementation Notes:
     * Deleting the Organization, requires that we delete all references to the organization, before we can continue
     * Therefor, there are 2 options available:
     * 1. Delete all Projects, All Users Links, before we delete the Organization
     * 2. Don't allow delete of the Organization, until all projects in the Organization have been deleted.
     * 3. Don't delete anything, just mark as delete (and maybe introduce a backup/purge functions, so as we can extract
     *    these deleted entities)
     *
     * Even though we could have implemented Option 1, Option 2 is safer, as it implies a manual confirmation
     * that you really want to delete the organization, by forcing the user to delete all the projects
     * before he can delete the organization. Also, this also makes the code easier to manage, and less like to have bugs.
     * Option 3, is probably the better solution, but will have to be analyzed.
     */

    $organization = $context->getParameter('entity');

    // Do we have any Projects Associated with the Organization?
    $count = \Project::countInOrganization($organization);
    if ($count > 0) { // YES
      throw new \Exception("Organization [{$organization->name}] has [$count] Projects associated. Delete all Projects, before deleting Organization.", 4);
    }

    // Unlink ALL Users from the Organization
    \UserOrganization::deleteRelationsOrganization($organization);

    // TODO Remove Organization Container
    return $context;
  }

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

    // Process 'name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'organization:name', function($controller, $context, $action, $value) {
      // Try to Find the Organization by Name
      $org = \Organization::findFirstByName($value);

      // Are we trying to 'create' a new organization?
      if ($action === 'Create') { // YES
        // Did we find an existing organization with the same name?
        if ($org !== FALSE) { // YES
          throw new \Exception("Organization [$value] already exists.", 1);
        }
      } else { // NO: Some other action
        // Did we find an existing organization?
        if ($org === FALSE) { // NO
          throw new \Exception("Organization [$value] not found", 2);
        }

        // Save the Organization for the Action
        $context->setParameter('entity', $org)
                ->setParameter('organization', $org);
      }

      return $context;
    }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'organization:id', function($controller, $context, $action, $value) {

        // Does the Organization with the given ID exist?
        $org = \Organization::findFirst($value);
        if ($org === FALSE) { // NO
          throw new \Exception("Organization [$value] not found", 3);
        }

        // Save the Organization for the Action
        $context->setParameter('entity', $org)
                ->setParameter('organization', $org);

        return $context;
      }, null, array('Read', 'Update', 'Delete'));
    }

    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 4);
    }

    // Save the User in the Context
    return $context->setParameter('user', $user)
                    ->setParameter('cm_user', $user);
  }

  /**
   * Perform cleanup, after the Action Handler is Called.
   * 
   * @param \shared\controller\ActionContext $context Incoming Context for Action
   * @return \shared\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Organization that was Previously Created
    $organization = $context->getActionResult();
    assert('isset($organization)');

    // Save the Organization for the Action
    $context->setParameter('entity', $organization);
    $context->setParameter('organization', $organization);

    /* TODO    
      // Create the Container for the Organization
      $container = \Container::createContainer("ROOT ORG[{$organization->id}]", $organization);
      $container->setSingleLevel(1);
      if ($container->save() === FALSE) {
      throw new \Exception("Failed to Create Container for Organization [{$organization->name}].", 1);
      }
      $organization->container = $container;
     */

    // Get the User to Associate with the Organization
    $user = $context->getParameter('user');
    assert('isset($user)');

    // Link the New Organization to the Current User (READ-ONLY)
    \UserOrganization::addRelation($user, $organization);

    // No change to the context
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
      case 'UserAdd':
      case 'UserRemove':
      case 'UserGet':
      case 'UserSet':
      case 'Create':
      case 'Read':
      case 'Update':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'List':
        $return = [];
        $entities = [];
        $header = true;
        foreach ($results as $organization) {
          $entities[] = $organization->toArray($header);
          $header = false;
        }

        // Do we have entities to display?
        if (count($entities)) { // YES
          // Move the Entity Information to become Result Header
          $this->moveEntityHeader($entities[0], $return);
          $return['__type'] = 'entity-set';
          $return['entities'] = $entities;
        }
        break;
      case 'UsersList':
        $return = array();
        foreach ($results as $uo) {
          $user = $uo->getUser();
          $id = $user->getId();
          $return[$id] = $user->toArray();
          unset($return[$id]['id']);
        }
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /*
   * ---------------------------------------------------------------------------
   * OVERRIDE : EntityServiceController
   * ---------------------------------------------------------------------------
   */

  /**
   * Apply any required modifications to the incoming Field value.
   * 
   * @param string $field Entity Field Name
   * @param mixed $value Field's Incoming Value
   * @return mixed Field's Outgoing Value
   */
  protected function transformFieldValue($field, $value) {
    // Call Base Class to Apply Initial Transforms
    $value = parent::transformFieldValue($field, $value);

    // Are we trying to set the description?
    if ($field === 'description') { // YES
      $value = StringUtilities::nullOnEmpty($value);
    }

    return $value;
  }

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \Organization An instance of a Organization Entity, managed by this controller
   */
  protected function createEntity() {
    return new \Organization();
  }

}
