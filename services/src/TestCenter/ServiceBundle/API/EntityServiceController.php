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

namespace TestCenter\ServiceBundle\API;

use Library\StringUtilities;

/**
 * Description of EntityServiceController
 *
 * @author Paulo Ferreira
 */
class EntityServiceController
  extends BaseServiceController {

  // Entity Managed by Controller
  protected $m_oEntity;

  /**
   * @param $entity
   */
  public function __construct($entity) {
    $this->m_oEntity = new EntityWrapper($this, $entity);
  }

  /**
   * @return mixed
   */
  public function getEntityManager() {
    return $this->m_oEntity->getEntityManager();
  }

  /**
   * @return mixed
   */
  public function getRepository($entity = null) {
    return $this->m_oEntity->getRepository($entity);
  }

  /**
   * @return mixed
   */
  public function getMetadata() {
    return $this->m_oEntity->getMetadata();
  }

  /**
   * 
   * @return type
   */
  public function getTypeCache() {
    return \TestCenter\ModelBundle\API\TypeCache::getInstance();
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function createEntity($parameters) {
    // Default - Create an Entity Based on the Pre-defined Entity Class
    $entity = $this->m_oEntity->getEntity();
    return new $entity;
  }

  /**
   * @param $entity
   * @param $parameters
   * @param null $metadata
   * @return mixed
   */
  protected function setEntityValues($entity, $parameters, $metadata = null) {
    // Parameter Validation
    assert('isset($entity) && is_object($entity)');
    assert('isset($parameters) && is_array($parameters)');

    // Get Metadata if Required
    if (!isset($metadata)) {
      $metadata = $this->getMetadata();
    }
    assert('isset($metadata)');

    /* TODO Create an Association System
     * A System that would allow, the setting of parameters of associated (referred) elements of the entity
     */
    foreach ($parameters as $key => $value) {
      // Skip Identifier Fields
      if ($metadata->isIdentifier($key)) {
        continue;
      }

      // Allow Non-String Values to Pass-through untouched
      if (isset($value) && is_string($value)) {
        $value = StringUtilities::nullOnEmpty($value);
      }

      if (isset($value)) {
        if ($metadata->hasField($key) || $metadata->hasAssociation($key)) {
          $metadata->setFieldValue($entity, $key, $value);
        }
      }
    }

    return $entity;
  }

  /**
   * 
   * @param type $action
   * @param type $in_actions
   * @param type $opt_actions
   * @param type $parameters
   * @param type $parm_in
   * @param type $parm_out
   * @param type $default_value
   * @param type $function
   * @return type
   * @throws \Exception
   */
  protected function inoutParameters($action, $in_actions, $opt_actions,
                                     $parameters, $parm_in, $parm_out,
                                     $default_value, $function) {
    assert('isset($action) && is_string($action)');
    assert('!isset($in_actions) || (is_string($in_actions) || is_array($in_actions))');
    assert('!isset($opt_actions) || is_string($opt_actions) || is_array($opt_actions)');
    assert('isset($parameters) && is_array($parameters)');
    assert('isset($parm_in) && is_string($parm_in)');
    assert('isset($parm_out) && (is_string($parm_out) || is_array($parm_out))');
    assert('isset($function) && is_callable($function)');

    // Convert Incoming Parameters to Arrays when appropriate
    if (is_string($in_actions)) {
      $in_actions = array($in_actions);
    }

    if (is_string($opt_actions)) {
      $opt_actions = array($opt_actions);
    }

    // See if it Applies to this $action
    if (!isset($in_actions) || // Applies to all actions
      (is_array($in_actions) && (array_search($action, $in_actions) !== FALSE)) // Applies to this action
    ) {
      if (isset($parameters[$parm_in]) || isset($default_value)) {
        $out_value = $function($this, $parameters,
                               isset($parameters[$parm_in]) ? $parameters[$parm_in] : $default_value);
        if (is_string($parm_out)) { // Assign Out Value to Single Parameter
          $parameters[$parm_out] = $out_value;
        } else { // Assign Out Value to Multiple Parameters
          foreach ($parm_out as $parameter) {
            $parameters[$parameter] = $out_value;
          }
        }
      } else
      if (!isset($opt_actions) || // No Optional Actions
        (array_search($action, $opt_actions) === FALSE) // Action NOT in Optional Actions
      ) { // Okay we can skip this parameter
        throw new \Exception("Missing Required Parameter[$parm_in]", 1);
      }
    }

    return $parameters;
  }

}

