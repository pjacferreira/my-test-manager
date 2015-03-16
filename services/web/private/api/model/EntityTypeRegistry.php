<?php

/**
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

namespace api\model;

use common\utility\Strings;
use common\utility\Arrays;

/**
 * Container Type Registry.
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class EntityRegistry implements \Phalcon\DI\InjectionAwareInterface {

  protected $_registry;
  protected $_di;

  /**
   * 
   */
  public function __construct() {
    $this->_registry = [
      'O' => [ // FOLDER
        'class' => '\models\Organization',
        'delete' => 'deleteOrganization',
        'allowed' => 'allowed'
      ],
      'P' => [ // FOLDER
        'class' => '\models\Project',
        'delete' => 'deleteProject',
        'allowed' => 'allowed'
      ],
      'F' => [ // FOLDER
        'class' => 'models\Container',
        'delete' => 'deleteFolder',
        'deleteInContainer' => 'deleteInFolder',
        'allowed' => 'allowed'
      ],
      'T' => [ // TEST
        'class' => 'models\Test',
        'delete' => 'deleteTest',
        'deleteInContainer' => 'deleteInFolder',
        'allowed' => 'allowed'
      ],
      'S' => [ // TEST SET
        'class' => 'models\TestSet',
        'delete' => 'deleteSet',
        'deleteInContainer' => 'deleteInFolder',
        'allowed' => 'allowed'
      ],
      'R' => [ // RUN
        'class' => 'models\Run',
        'delete' => 'deleteRun',
        'deleteInContainer' => 'deleteInFolder',
        'allowed' => 'allowed'
      ]
    ];
  }

  /*
   * ---------------------------------------------------------------------------
   * InjectionAwareInterface Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param type $di
   */
  public function setDi($di) {
    $this->_di = $di;
  }

  /**
   * 
   * @return type
   */
  public function getDi() {
    return $this->_di;
  }

  /*
   * ---------------------------------------------------------------------------
   * Registry Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Is the TYPE registered in the Registry?
   * 
   * @param string $type Type to Verify Entry for
   * @return boolean 'true' yes, 'false' otherwise
   */
  public function hasType($type) {
    $type = Strings::nullOnEmpty($type);
    return isset($type) ? array_key_exists($type, $this->_registry) : false;
  }

  /**
   * Get the Class for the Type
   * 
   * @param string $type Type to Retrieve Class For
   * @return string Class name or 'null'
   */
  public function typeClass($type) {
    $type = Strings::nullOnEmpty($type);
    return $this->hasType($type) ? $this->_registry[$type]['class'] : null;
  }

  /**
   * Retrieve Action Function name for the Type and Key
   * 
   * @param string $type Type to Retrieve Action Function For
   * @param string $key Type Registry Key Name
   * @return string Function name or 'null' if doesn't exist
   */
  public function actionFunction($type, $key) {
    $type = Strings::nullOnEmpty($type);
    $key = Strings::nullOnEmpty($key);
    // Is Valid Type and Action?
    if ($this->hasType($type) && isset($key)) { // YES
      return array_key_exists($key, $this->_registry[$type]) ? $this->_registry[$type][$key] : null;
    }
    return null;
  }

  /**
   * Execute the Specified on Action for the Given Type
   * 
   * @param string $type Type for Registry Entry
   * @param string $key Type Registry Key Name
   * @param array $params [DEFAULT null] Parameters to pass into the action
   * @param mixed $default [DEFAULT null] Value to return in the case the action execution fails for any reason
   * @return type
   */
  public function executeAction($type, $key, $params = null, $default = null) {
    $type = Strings::nullOnEmpty($type);
    $key = Strings::nullOnEmpty($key);
    $params = Arrays::nullOnEmpty($params);
    // Is Valid Type and Action?
    if ($this->hasType($type) && isset($key)) { // YES
      $type = $this->_registry[$type];
      if (array_key_exists($key, $type)) {
        $class = $type['class'];
        $method = $type[$key];
        if (method_exists($class, $method)) {
          if (isset($params)) {
            $result = call_user_func_array("{$class}::{$method}", $params);
          } else {
            $result = call_user_func("{$class}::{$method}");
          }

          /* TODO PHP Reference for call_user_func return FALSE if the
           * call fails.
           * 1. Under what conditions can it fail?
           * 2. How do we handle situations in which the function call returns
           * 'FALSE' as it's result
           */
          return $result;
        }
      }
    }

    return $default;
  }

}
