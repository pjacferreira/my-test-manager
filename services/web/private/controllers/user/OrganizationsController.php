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

namespace controllers\user;

use api\controller\ActionContext;
use \common\utility\Strings;
use api\controller\CrudServiceController;

/**
 * Controller used to Manage Organization Entities
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class OrganizationsController extends CrudServiceController {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the Organization with the Given ID.
   * 
   * @param integer $id [DEFAULT: null = Session Organiztion] Organization's Unique Identifier
   * @return string HTTP Body Response
   */
  public function read($id = NULL) {
    // Create Action Context
    $context = new ActionContext('read');
    // Call Action
    return $this->doAction($context->setIfNotNull('organization:id', isset($id) ? (integer) $id : null));
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
    return $this->doAction($context->setIfNotNull('organization:name', Strings::nullOnEmpty($name)));
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
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
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
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preActionRead($context) {
    // Call the General Handler 1st (to Setup Context)
    $context = $this->preAction($context);

    // Retrieve the Key to Find the Organization
    $name = false;
    if ($context->hasParameter('organization:name')) {
      $value = $context->getParameter('organization:name');
      $name = true;
    } else if ($context->hasParameter('organization:id')) {
      $value = $context->getParameter('organization:id');
    } else {
      // Get the Organization for the Active Session
      $org = $this->sessionManager->getOrganization();
      if (isset($org)) {
        $value = $org['id'];
      } else {
        throw new \Exception("No Organization set for the Session", 1);
      }
    }

    // Are we find the Organization by Name?
    if ($name) { // YES
      $org = \models\Organization::findFirstByName($value);
    } else { // NO: Using ID
      $org = \models\Organization::findFirst($value);
    }

    // Did we find an existing organization?
    if ($org === FALSE) { // NO
      throw new \Exception("Organization [$value] not found", 2);
    }

    // Save the Organization for the Action
    $context->setParameter('entity', $org)
            ->setParameter('organization', $org);

    return $context;
  }

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
      case 'Read':
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
          $return['entities'] = $entities;
        } else {
          $return['entities'] = [];
        }

        // Create Base Entity Set Identified
        $return['__type'] = 'entity-set';
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
      $value = Strings::nullOnEmpty($value);
    }

    return $value;
  }

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \models\Organization An instance of a Organization Entity, managed by this controller
   */
  protected function createEntity() {
    return new \models\Organization();
  }

}
