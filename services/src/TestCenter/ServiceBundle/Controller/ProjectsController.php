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
use TestCenter\ServiceBundle\API\SessionManager;
use TestCenter\ServiceBundle\API\ActionContext;
use TestCenter\ServiceBundle\API\CrudServiceController;

/**
 * Description of ProjectsController
 *
 * @author Paulo Ferreira
 */
class ProjectsController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\Project');
  }

  /**
   * Create a Project, within Current Session Organization
   *
   * @param $name Project Name (Unique within the Organization it belongs to)
   * @return Response Object or throw Exception
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
   * Create a Project, within Current Session Organization
   *
   * @param $name Project Name (Unique within the Organization it belongs to)
   * @return Response Object or throw Exception
   */
  public function createInOrgAction($org_id, $name) {
    // Create Action Context
    $context = new ActionContext('create');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameters($this->serviceParameters())
      ->setParameter('org_id', (integer) $org_id)
      ->setIfNotNull('name', StringUtilities::nullOnEmpty($name));

    // Call the Function
    return $this->doAction($context);
  }

  /**
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
   * @param $name
   * @param $fields
   * @param $values
   * @return null
   */
  public function updateByNameAction($name) {
    // Create Action Context
    $context = new ActionContext('update');
    // Extract Clean (Security) URL Parameters (Overwriting with route parameters where necessary)
    $context = $context
      ->setParameters($this->serviceParameters())
      ->setIfNotNull('name', StringUtilities::nullOnEmpty($name));

    return $this->doAction($context);
  }

  /**
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
   * @param $name
   * @return null
   */
  public function deleteByNameAction($name) {
    // Create Action Context
    $context = new ActionContext('delete');
    // Call Action
    return $this->doAction($context->setIfNotNull('name',
                                                  StringUtilities::nullOnEmpty($name)));
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
   * @param \TestCenter\ServiceBundle\Controller\Request $request
   * @param type $org_id
   * @param type $filter
   * @param type $sort
   * @param type $limit
   * @return type
   */
  public function listPerOrgAction(Request $request, $org_id = null,
                                   $filter = null, $sort = null, $limit = null) {
    /* Allowing a NULL org_id is a win-win situation
     * 1. Allows for the scenario that the service is listing the project for the current session organization
     */

    // Create Action Context
    $context = new ActionContext('list_per_org');
    // Build Parameters
    $context = $context
      ->setFirstNotNullOf('__filter', StringUtilities::nullOnEmpty($filter),
                                                                   $request->get('filter'))
      ->setFirstNotNullOf('__sort', StringUtilities::nullOnEmpty($sort),
                                                                 $request->get('sort'))
      ->setFirstNotNullOf('__limit', StringUtilities::nullOnEmpty($limit),
                                                                  $request->get('limit'))
      ->setIfNotNull('org_id', isset($org_id) ? (integer) $org_id : null);

    return $this->doAction($context);
  }

  /**
   * 
   * @param \TestCenter\ServiceBundle\Controller\Request $request
   * @param type $org_id
   * @param type $filter
   * @return type
   */
  public function countPerOrgAction(Request $request, $org_id = null,
                                    $filter = null) {
    // Create Action Context
    $context = new ActionContext('count_per_org');
    // Build Parameters
    $context = $context
      ->setFirstNotNullOf('__filter', StringUtilities::nullOnEmpty($filter),
                                                                   $request->get('filter'))
      ->setIfNotNull('org_id', isset($org_id) ? (integer) $org_id : null);

    return $this->doAction($context);
  }

  /**
   * @param $parameters
   * @param $project
   * @return mixed
   */
  protected function postActionCreate($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Project that was Previously Created
    $project = $context->getActionResult();
    assert('isset($project)');

    // Save the Project for the Action
    $context->setParameter('entity', $project);
    $context->setParameter('project', $project);

    // Create Root Container for Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Container');
    $container = $repository->createContainer("ROOT Project [{$project->getId()}]",
                                              $project);
    $container->setSingleLevel(0);
    $project->setContainer($container);

    // TODO Remove Project Container on Delete
    // Link the New Organization to the Current User
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserProject');
    $repository->addLink($context->getParameter('user'), $project, '');

    // Flush Changes to the Container and the Project Objects
    $this->getEntityManager()->flush();

    // No change to the context
    return $context;
  }

  /**
   * 
   * @param type $context
   * @return type
   */
  protected function doDeleteAction($context) {
    // Unlink all users from the Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserProject');
    $repository->removeProject($context->getParameter('project'));

    return parent::doDeleteAction($context);
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListPerOrgAction($context) {
    // Get the Organization
    $organization = $context->getParameter('organization');

    // Modify the Filter for the Action
    $__filter = $context->getParameter('__filter');
    if (isset($__filter)) {
      $__filter = "project.organization:={$organization->getId()};{$__filter}";
    } else {
      $__filter = "project.organization:={$organization->getId()}";
    }
    $context->setParameter('__filter', $__filter);

    return $this->doListAction($context);
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountPerOrgAction($context) {
    // Get the Organization
    $organization = $context->getParameter('organization');

    // Modify the Filter for the Action
    $__filter = $context->getParameter('__filter');
    if (isset($__filter)) {
      $__filter = "project.organization:={$organization->getId()};{$__filter}";
    } else {
      $__filter = "project.organization:={$organization->getId()}";
    }
    $context->setParameter('__filter', $__filter);

    return $this->doCountAction($context);
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

    // Process 'org_id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'org_id',
                                    function($controller, $context, $action, $value) {
        // Find the Organization
        $org = $controller->getRepository('TestCenter\ModelBundle\Entity\Organization')->find($value);
        if (!isset($org)) {
          throw new \Exception("Organization[$value] not found", 1);
        }

        return $context->setParameter('organization', $org);
      }, null, array('Create', 'ListPerOrg', 'CountPerOrg'),
                                      function($controller, $context, $action) {
        // Missing Organization ID, so use the current Session Organization
        $controller->checkOrganization();

        // Get the Current Session Organization
        return SessionManager::getOrganization();
      });

    // Process 'name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'name',
                                    function($controller, $context, $action, $value) {

        // Try to Find the Project by Name
        $project = $controller->getRepository()->findOneByName($value);

        if ($action === 'Create') {
          if (isset($project)) {
            throw new \Exception("Project [$name] already exists.", 2);
          }
        } else {
          if (!isset($project)) {
            throw new \Exception("Project [$value] not found", 1);
          }

          // Save the Project for the Action
          $context->setParameter('entity', $project);
          $context->setParameter('project', $project);
        }

        return $context;
      }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'id',
                                      function($controller, $context, $action, $value) {
          // Get the Project
          $project = $controller->getRepository()->find($value);
          if (!isset($project)) {
            throw new \Exception("Project[{$value}] not found", 1);
          }

          // Save the Project for the Action
          return $context->
              setParameter('entity', $project)->
              setParameter('project', $project);
        }, null, array('Read', 'Update', 'Delete'));
    }

    // Do Access Checks
    $context = $this->onActionDo($context,
                                 array('Read', 'Create', 'Update', 'Delete'),
                                 function($controller, $context, $action) {
        // Required Parameters
        $user = $context->getParameter('user');
        assert('isset($user)');

        // Other Parameters
        $project = $context->getParameter('project');
        $organization = $context->getParameter('organization');

        switch ($action) {
          case 'Create':
            assert('isset($organization)');
            $controller->checkOrganizationAccess($user, $organization);
            break;
          case 'Delete': // User Only Requires Access to Organization
            assert('isset($project)');
            $controller->checkOrganizationAccess($user,
                                                 $project->getOrganization());
            break;
          default: // Check if User Has Access to Project (and by consequence to the Organization)
            assert('isset($project)');
            $controller->checkProjectAccess($user, $project);
        }

        return null;
      });

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
      case 'Create':
      case 'Read':
      case 'Update':
        assert('isset($results)');
        $return = $results->toArray();
        break;
      case 'List':
      case 'ListPerOrg':
        $return = array();
        foreach ($results as $project) {
          $return[] = $project->toArray();
        }
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /**
   * @param $user
   * @param $organization
   * @param null $required
   * @return bool
   * @throws \Exception
   */
  public function checkOrganizationAccess($user, $organization, $required = null) {
    // TODO Implement Actual Permissions Check (not just link exists)
    // Get Link Between User and Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserOrganization');
    $link = $repository->findLink($user, $organization);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Organization [{$organization->getId()}].", 1);
    }

    return true;
  }

  /**
   * @param $user
   * @param $project
   * @param null $required
   * @return bool
   * @throws \Exception
   */
  public function checkProjectAccess($user, $project, $required = null) {
    // TODO Implement Actual Permissions Check (not just link exists)
    $organization = $project->getOrganization();
    $this->checkOrganizationAccess($user, $organization);

    // Get Link Between User and Project
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\UserProject');
    $link = $repository->findLink($user, $project);
    if (!isset($link)) {
      throw new \Exception("User [{$user->getId()}] does not have access to Project [{$project->getId()}].", 1);
    }

    return true;
  }

}
