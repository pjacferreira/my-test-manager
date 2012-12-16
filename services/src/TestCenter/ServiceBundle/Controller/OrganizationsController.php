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
use TestCenter\ServiceBundle\API\CrudServiceController;
use TestCenter\ModelBundle\Entity\Organization;
use TestCenter\ModelBundle\Entity\Container;
use TestCenter\ModelBundle\Entity\TCType;
use TestCenter\ServiceBundle\API\SessionManager;

/**
 * Description of OrganizationsController
 *
 * @author Paulo Ferreira
 */
class OrganizationsController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\Organization');
  }

  /**
   * @param $name
   * @param $password
   * @param null $fv_settings
   * @return null
   */
  public function createAction($name, $fv_settings = null) {
    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);

    // Add Name
    $array['name'] = StringUtilities::nullOnEmpty($name);

    // Call the Function
    return $this->doAction('create', $array);
  }

  /**
   * @param $id
   * @return null
   */
  public function readAction($id) {
    return $this->doAction('read', array('id' => (integer) $id));
  }

  /**
   * @param $name
   * @return null
   */
  public function readByNameAction($name) {
    return $this->doAction('read',
                           array('name' => StringUtilities::nullOnEmpty($name)));
  }

  /**
   * @param $id
   * @param $fields
   * @param $values
   * @return null
   */
  public function updateAction($id, $fv_settings) {
    // Expand Options to Array
    $array = $this->optionsToArray($fv_settings);
    $array['id'] = (integer) $id;

    return $this->doAction('update', $array);
  }

  /**
   * @param $id
   * @return null
   */
  public function deleteAction($id) {
    return $this->doAction('delete', array('id' => (integer) $id));
  }

  /**
   * @return null
   */
  public function listAction() {
    return $this->doAction('list', null);
  }

  /**
   * @return null
   */
  public function countAction() {
    return $this->doAction('count', null);
  }

  protected function doDeleteAction($parameters) {
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

    // Unlink ALL Users from the Organization
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserOrganization');
    $repository->removeOrganization($parameters['organization']);

    // What we have to do
    // Delete Projects Associated with the Project
    // Delete User Organization Links
    return parent::doDeleteAction($parameters);
  }

  /**
   * @param $parameters
   * @return array
   * @throws \Exception
   */
  protected function sessionChecksCreate($parameters) {
    // Basic Session Checks
    $parameters = $this->sessionChecks('Create', $parameters);

    // TODO Create Transaction, so in case of failure, we can rollback the system.
    // Verify Parameters
    $name = ArrayUtilities::extract($parameters, 'name');
    if (!isset($name)) {
      throw new \Exception('Missing Required Action Parameter [name].', 1);
    }

    // Test if the user name already exists
    $user = $this->getRepository()->findOneByName($name);
    if (isset($user)) {
      throw new \Exception("Organization [$name] already exists.", 2);
    }

    return $parameters;
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
    $this->checkLoggedIn();

    // Process User
    $id = SessionManager::getUser();

    // Get User for Action
    $user = $this->getRepository('TestCenter\ModelBundle\Entity\User')->find($id);
    if (!isset($user)) {
      throw new \Exception("User not found[$id]", 1);
    }

    $parameters['user'] = $user;

    // Process Organization ID
    $parameters = $this->processChecks($action, array('Update', 'Delete'),
                                       $parameters,
                                       function($controller, $action, $parameters) {
        // Get the Identifier for the User
        $id = ArrayUtilities::extract($parameters, 'id');
        if (!isset($id)) {
          throw new \Exception('Missing Required Action Parameter [id].', 1);
        }

        // Get Organization for Action
        $org = $controller->getRepository()->find($id);
        if (!isset($org)) {
          throw new \Exception('Organization not found', 1);
        }

        // Save the Organization for the Action
        $parameters['entity'] = $org;
        $parameters['organization'] = $org;
        return $parameters;
      });

    if ($action === 'Delete') {
      // Check that we have no projects linked to the organization before we delete it
      $repository = $this->getRepository();
      $organization = $parameters['organization'];
      $count = $repository->countProjects($organization);
      if ($count != 0) {
        throw new \Exception("Organization [{$organization->getId()}] has [$count] Projects associated. Delete all Projects, before deleting Organization.", 1);
      }

      // TODO Remove Organization Container
    }

    return $parameters;
  }

  /**
   * @param $parameters
   * @param $organization
   * @return mixed
   */
  protected function postActionCreate($parameters, $organization) {
    // Link the New Organization to the Current User
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserOrganization');
    $repository->addLink($parameters['user'], $organization, '');

    // Create the Container for the Organization
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');
    $container = $repository->createContainer("ROOT ORG[{$organization->getID()}]",
                                              $organization);
    $container->setSingleLevel(1);
    $organization->setContainer($container);

    // Save the Container and Changes to the Database
    $this->getEntityManager()->persist($container);
    $this->getEntityManager()->flush();

    return $organization;
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
        $return = array();
        foreach ($results as $organization) {
          $id = $organization->getId();
          $return[$id] = $organization->toArray();
          unset($return[$id]['id']);
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
    }

    return $return;
  }

}

