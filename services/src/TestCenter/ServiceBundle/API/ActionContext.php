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
use Library\ArrayUtilities;

/**
 * Description of ActionContext
 *
 * @author Paulo Ferreira
 */
class ActionContext {

  protected $m_sAction;
  protected $m_arParameters;
  protected $m_vResults;
  protected $m_oResponse;

  /**
   * 
   * @param type $action
   */
  public function __construct($action) {
    // Set the Base Action for the Context
    $this->m_sAction = self::getActionName($action);
    assert('isset($this->m_sAction)');

    $this->m_arParameters = array();
  }

  /**
   * 
   * @return type
   */
  public function getAction() {
    return $this->m_sAction;
  }

  /**
   * 
   * @param type $key
   * @param type $default
   * @return type
   */
  public function getParameter($key, $default = null) {
    $key = StringUtilities::nullOnEmpty($key);
    assert('isset($key)');
    return isset($key) ? ArrayUtilities::extract($this->m_arParameters, $key,
                                                 $default) : $default;
  }

  /**
   * 
   * @param type $key
   * @param type $value
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setParameter($key, $value) {
    $key = StringUtilities::nullOnEmpty($key);
    assert('isset($key)');
    if (isset($key)) {
      $this->m_arParameters[$key] = $value;
    }

    return $this;
  }

  /**
   * 
   * @return type
   */
  public function getParameters() {
    return $this->m_arParameters;
  }

  /**
   * 
   * @param type $values
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setParameters($values) {
    assert('!isset($values) || is_array($values)');
    if (isset($values) && is_array($values) && (count($values) > 0)) {
      foreach ($values as $key => $value) {
        $this->m_arParameters[$key] = $value;
      }
    }

    return $this;
  }

  /**
   * 
   * @param type $value1
   * @param type $value2
   * @return type
   */
  public function setFirstNotNullOf($key, $value1, $value2) {
    $key = StringUtilities::nullOnEmpty($key);
    assert('isset($key)');
    if (isset($key)) {
      if (isset($value1)) {
        $this->m_arParameters[$key] = $value1;
      } else if (isset($value2)) {
        $this->m_arParameters[$key] = $value2;
      }
    }

    return $this;
  }

  /**
   * 
   * @param type $array
   * @param type $key
   * @param type $value
   * @return type
   */
  public function setIfNotNull($key, $value) {
    $key = StringUtilities::nullOnEmpty($key);
    assert('isset($key)');
    if (isset($key) && isset($value)) {
      $this->m_arParameters[$key] = $value;
    }

    return $this;
  }

  /**
   * 
   * @return type
   */
  public function getActionResult() {
    return $this->m_vResults;
  }

  /**
   * 
   * @param type $results
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setActionResult($results) {
    if (isset($results)) {
      $this->m_vResults = $results;
    }

    return $this;
  }

  /**
   * 
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function clearResults() {
    $this->m_vResults = null;
    return $this;
  }

  /**
   * 
   * @return type
   */
  public function getResponse() {
    return $this->m_oResponse;
  }

  /**
   * 
   * @param type $results
   * @return \TestCenter\ServiceBundle\API\ActionContext
   */
  public function setResponse($response) {
    if (isset($response)) {
      $this->m_oResponse = $response;
    }

    return $this;
  }

  /**
   * 
   * @param type $action
   * @return type
   */
  public static function getActionName($action) {
    assert('isset($action) && is_string($action)');

    $action = StringUtilities::nullOnEmpty($action);
    $action = explode('_', $action);
    $action = array_map('strtolower', $action);
    $action = array_map('ucfirst', $action);
    $action = implode($action);
    return $action;
  }

}

?>
