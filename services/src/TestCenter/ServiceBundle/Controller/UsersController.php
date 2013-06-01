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
use TestCenter\ServiceBundle\API\CrudServiceController;

/**
 * Description of UsersController
 *
 * @author Paulo Ferreira
 */
class UsersController
  extends CrudServiceController {

  /**
   *
   */
  public function __construct() {
    parent::__construct('TestCenter\ModelBundle\Entity\User');
  }

  /**
   * @param $name
   * @param $password
   * @param null $fv_settings
   * @return null
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
   * @param $id
   * @param $fields
   * @param $values
   * @return null
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
   * @param \Symfony\Component\HttpFoundation\Request $request
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
                                                                   $request->request->get('filter'))
      ->setFirstNotNullOf('__sort', StringUtilities::nullOnEmpty($sort),
                                                                 $request->request->get('sort'))
      ->setFirstNotNullOf('__limit', StringUtilities::nullOnEmpty($limit),
                                                                  $request->request->get('limit'));

    return $this->doAction($context);
  }

  /**
   * 
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param type $filter
   * @return type
   */
  public function countAction(Request $request, $filter = null) {
    // Create Action Context
    $context = new ActionContext('count');
    // Build Parameters
    $context = $context->setFirstNotNullOf('__filter',
                                           StringUtilities::nullOnEmpty($filter),
                                                                        $request->request->get('filter'));

    return $this->doAction($context);
  }

  /**
   * @param $parameters
   * @return object
   */
  protected function doDeleteAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    /* TODO Remove Links to Other Objects
     * Delete Links Between User and Organization (Automatically Forces the 
     * Removal of Links with Organization Projects)
     */
    return parent::doDeleteAction($context);
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

    // If Password Specified Encode with MD5
    $password = $context->getParameter('password');
    if (isset($password)) {
      $context->setParameter('password', md5($password));
    }

    // Process 'name' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'name',
                                    function($controller, $context, $action, $value) {
        // Try to Find the Organization by Name
        $org = $controller->getRepository()->findOneByName($value);
        if ($action === 'Create') {
          if (isset($org)) {
            throw new \Exception("User [$value] already exists.", 2);
          }
        } else {
          if (!isset($org)) {
            throw new \Exception("User [$value] not found", 1);
          }

          // Save the Organization for the Action
          $context->setParameter('entity', $org);
          $context->setParameter('organization', $org);
        }

        return $context;
      }, array('Read', 'Update', 'Delete'), 'Create');

    // Process 'id' Parameter (if it exists)
    if (!$context->hasParameter('entity')) {
      $context = $this->onParameterDo($context, 'id',
                                      function($controller, $context, $action, $value) {

          // Try to Find the User by ID
          $user = $controller->getRepository()->find($value);
          if (!isset($user)) {
            throw new \Exception("User [$value] not found", 1);
          }


          // Save the User for the Action
          $context->setParameter('entity', $user);
          $context->setParameter('user', $user);

          return $context;
        }, null, array('Read', 'Update', 'Delete'));
    }

    return $context;
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
      default:
        $return = $results;
    }

    return $return;
  }

}
