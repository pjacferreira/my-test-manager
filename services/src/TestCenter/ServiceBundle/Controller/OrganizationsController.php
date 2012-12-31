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

use Symfony\Component\HttpFoundation\Request;
use Library\StringUtilities;
use TestCenter\ServiceBundle\API\ActionContext;
use TestCenter\ServiceBundle\API\SessionManager;
use TestCenter\ServiceBundle\API\CrudServiceController;

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
   * 
   * @param type $name
   * @return type
   */
  public function createAction($name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameters($this->serviceParameters())
      ->setIfNotNull('name', StringUtilities::nullOnEmpty($name));

    // Call the Function
    return $this->doAction($context);
  }

  /**
   * 
   * @param $id
   * @return null
   */
  public function readAction($id) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameters($this->serviceParameters())
      ->setParameter('id', (integer) $id);

    return $this->doAction($context);
  }

  /**
   * 
   * @param $name
   * @return null
   */
  public function readByNameAction($name) {
    // Create Action Context
    $context = new ActionContext('read');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameters($this->serviceParameters())
      ->setIfNotNull('name', StringUtilities::nullOnEmpty($name));

    return $this->doAction($context);
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  public function updateAction($id) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameters($this->serviceParameters())
      ->setParameter('id', (integer) $id);

    return $this->doAction($context);
  }

  /**
   * 
   * @param $id
   * @return null
   */
  public function deleteAction($id) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setParameter('id', (integer) $id));
  }

  /**
   * 
   * @param \TestCenter\ServiceBundle\Controller\Request $request
   * @param type $filter
   * @param type $sort
   * @param type $limit
   * @return type
   */
  public function listAction(Request $request, $filter = null, $sort = null,
                             $limit = null) {
    // Create Action Context
    $context = new ActionContext('list');
    // Build Parameters
    $context = $context
      ->setFirstNotNullOf('__filter', StringUtilities::nullOnEmpty($filter),
                                                                   $request->get('filter'))
      ->setFirstNotNullOf('__sort', StringUtilities::nullOnEmpty($sort),
                                                                 $request->get('sort'))
      ->setFirstNotNullOf('__limit', StringUtilities::nullOnEmpty($limit),
                                                                  $request->get('limit'));

    return $this->doAction($context);
  }

  /**
   * 
   * @param \TestCenter\ServiceBundle\Controller\Request $request
   * @param type $filter
   * @return type
   */
  public function countAction(Request $request, $filter = null) {
    // Create Action Context
    $context = new ActionContext('count');
    // Build Parameters
    $context = $context->setFirstNotNullOf('__filter',
                                           StringUtilities::nullOnEmpty($filter),
                                                                        $request->get('filter'));

    return $this->doAction($context);
  }

  /**
   * 
   * @param type $context
   * @return type
   */
  protected function doDeleteAction($context) {
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
    $repository->removeOrganization($context->getParameter('organization'));

    // What we have to do
    // Delete Projects Associated with the Project
    // Delete User Organization Links
    return parent::doDeleteAction($context);
  }

  /**
   * @param $parameters
   * @return array
   * @throws \Exception
   */
  protected function sessionChecksCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Basic Session Checks
    $context = $this->sessionChecks($context);

    // Verify Parameters
    $name = $context->getParameter('name');
    if (!isset($name)) {
      throw new \Exception('Missing Required Action Parameter [name].', 1);
    }

    // Test if the user name already exists
    $user = $this->getRepository()->findOneByName($name);
    if (isset($user)) {
      throw new \Exception("Organization [$name] already exists.", 2);
    }

    return $context;
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

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

    $context->setParameter('user', $user);

    // Process Organization ID
    $context = $this->processChecks($context, array('Update', 'Delete'),
                                    function($controller, $context) {
        // Get the Identifier for the User
        $id = $context->getParameter('id');
        if (!isset($id)) {
          throw new \Exception('Missing Required Action Parameter [id].', 1);
        }

        // Get Organization for Action
        $org = $controller->getRepository()->find($id);
        if (!isset($org)) {
          throw new \Exception('Organization not found', 1);
        }

        // Save the Organization for the Action
        $context->setParameter('entity', $org);
        $context->setParameter('organization', $org);
        return $parameters;
      });

    $action = $context->getAction();
    assert('isset($action)');
      
    if ($action === 'Delete') {
      // Check that we have no projects linked to the organization before we delete it
      $repository = $this->getRepository();
      $organization = $context->getParameter('organization');
      $count = $repository->countProjects($organization);
      if ($count != 0) {
        throw new \Exception("Organization [{$organization->getId()}] has [$count] Projects associated. Delete all Projects, before deleting Organization.", 1);
      }

      // TODO Remove Organization Container
    }

    return $context;
  }

  /**
   * @param $parameters
   * @param $organization
   * @return mixed
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    $organization = $context->getParameter('organization');
    assert('isset($organization)');

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

    // No change to the context
    return null;
  }

  /**
   * @param $action
   * @param $results
   * @param $format
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
        $return = array();
        foreach ($results as $user) {
          $return[] = $user->toArray();
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

}

