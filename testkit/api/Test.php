<?php

/* Test Center - Test Kit
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

namespace api;

require_once 'utility.php';

/**
 * Description of Test
 *
 * @author Paulo Ferreira
 */
class Test {

  protected $m_nType;
  protected $m_sGroup;
  protected $m_nSequence;
  protected $m_arDependencies;
  protected $m_arServiceRoute;
  protected $m_arParameterRoute;
  protected $m_arParameterRequest;
  protected $m_oValidator;
  protected $m_oRenderer;

  const STANDARD = 1;
  const MARKER = 2;

  /**
   *
   * @param type $sGroup
   * @param type $nSequence
   * @param type $nType
   */
  protected function __construct($sGroup, $nSequence, $nType) {
    assert('isset($sGroup) && is_string($sGroup)');
    assert('isset($nSequence) && is_integer($nSequence) && ($nSequence > 0)');
    assert('!isset($nType) || is_integer($nType)');

    $this->m_sGroup = strtolower($sGroup);
    $this->m_nSequence = $nSequence;
    $this->m_nType = $nType;
    $this->m_arDependencies = array('before' => array(), 'after' => array());
    $this->m_arServiceRoute = array();
    $this->m_arParameterRoute = array();
    $this->m_arParameterRequest = array();
  }

  /**
   *
   * @param type $sGroup
   * @param type $nSequence
   * @return type
   */
  public static function getTest($sGroup, $nSequence) {
    $sGroup = string_onEmpty($sGroup);
    $nSequence = integer_gt($nSequence, 0);

    return isset($sGroup) && isset($nSequence) ? new Test($sGroup, $nSequence, self::STANDARD) : null;
  }

  /**
   *
   * @param type $sGroup
   * @param type $nSequence
   * @return type
   */
  public static function getMarker($sGroup, $nSequence) {
    $sGroup = string_onEmpty($sGroup);
    $nSequence = integer_gt($nSequence, 0);

    return isset($sGroup) && isset($nSequence) ? new Test($sGroup, $nSequence, self::MARKER) : null;
  }

  /**
   *
   * @return type
   */
  public function getType() {
    return $this->m_nType;
  }

  /**
   *
   * @return type
   */
  public function getGroup() {
    return $this->m_sGroup;
  }

  /**
   *
   * @return type
   */
  public function getSequence() {
    return $this->m_nSequence;
  }

  /**
   *
   * @return type
   */
  public function getKey() {
    return "{$this->m_sGroup}:{$this->m_nSequence}";
  }

  /**
   *
   * @return type
   */
  public function getDependencies($before = true) {
    $arDependencies = $this->m_arDependencies[$before ? 'before' : 'after'];
    if (count($arDependencies)) {
      usort($arDependencies,
        function($a, $b) {
          if ($a['group'] < $b['group']) {
            return -1;
          } else if (($a['group'] === $b['group'])) {
            if ($a['priority'] < $b['priority']) {
              return -1;
            } else if ($a['priority'] > $b['priority']) {
              return 1;
            } else {
              throw new \Exception("Duplicate Dependencies [{$a['group']}:{$a['priority']}].");
            }
          }

          return 1;
        });

      return $arDependencies;
    }

    return null;
  }

  /**
   *
   * @param type $sGroup
   * @param type $nSequence
   * @return type
   */
  public function after($sGroup, $nSequence) {
    // Add only if Valid Parameters
    $sGroup = string_onEmpty($sGroup);
    $nSequence = integer_gt($nSequence);
    if (isset($sGroup) && isset($nSequence)) {
      // Add Element to End of Array
      $this->m_arDependencies['after'][] = array('group' => strtolower($sGroup), 'priority' => $nSequence);
    }

    return $this;
  }

  /**
   *
   * @param type $sGroup
   * @param type $nSequence
   * @return type
   */
  public function before($sGroup, $nSequence) {
    // Add only if Valid Parameters
    $sGroup = string_onEmpty($sGroup);
    $nSequence = integer_gt($nSequence);
    if (isset($sGroup) && isset($sGroup)) {
      // Add Element to End of Array
      $this->m_arDependencies['before'][] = array('group' => strtolower($sGroup), 'priority' => $nSequence);
    }

    return $this;
  }

  /**
   *
   * @return type
   */
  public function getService() {
    return count($this->m_arServiceRoute) ? $this->m_arServiceRoute : null;
  }

  /**
   *
   * @param type $route
   * @return \Test
   */
  public function setService($route) {
    if (isset($route)) {
      // If a String is Given (Convert it to an array)
      if (is_string($route)) {
        $route = array($route);
      }

      // Only Process Array of not-empty Strings
      if (is_array($route)) {
        $value = null;
        for ($i = 0; $i < count($route); ++$i) {
          $value = string_onEmpty($route[$i]);
          if (isset($value)) {
            $this->m_arServiceRoute[] = $value;
          }
        }
      }
    }

    return $this;
  }

  /**
   *
   * @return type
   */
  public function getParameters($route = true) {
    $parameters = $route ? $this->m_arParameterRoute : $this->m_arParameterRequest;
    return count($parameters) ? $parameters : null;
  }

  /**
   *
   * @param type $params
   * @param type $route
   * @return \Test
   */
  public function setParameters($params, $route = true) {
    if (isset($params)) {
      // If a String is Given (Convert it to an array)

      if (!is_array($params)) {
        $params = array((string) $params);
      }

      // Only Process Array of not-empty Strings
      if (is_array($params)) {
        $value = null;
        for ($i = 0; $i < count($params); ++$i) {
          $value = string_onEmpty((string) $params[$i]);
          if (isset($value)) {
            if ($route) {
              $this->m_arParameterRoute[] = $value;
            } else {
              $this->m_arParameterRequest[] = $value;
            }
          }
        }
      }
    }

    return $this;
  }

  /**
   *
   * @return type
   */
  public function getValidator() {
    return $this->m_oValidator;
  }

  /**
   *
   * @param type $validator
   * @return \Test
   */
  public function setValidator($validator) {
    if (isset($validator) && is_object($validator)) {
      $this->m_oValidator = $validator;
    }

    return $this;
  }

  /**
   *
   * @return type
   */
  public function getRenderer() {
    return $this->m_oRenderer;
  }

  /**
   *
   * @param type $renderer
   * @return \Test
   */
  public function setRenderer($renderer) {
    if (isset($renderer) && is_object($renderer)) {
      $this->m_oRenderer = $renderer;
    }

    return $this;
  }

  /**
   *
   * @return type
   */
  public function isValid() {
    return count($this->m_arServiceRoute) && isset($this->m_oValidator) && isset($this->m_oRenderer);
  }

  /**
   *
   * @return null
   */
  public function toUrl() {

    if ($this->isValid()) {
      // Create Basic Service URL
      $url = implode('/', $this->m_arServiceRoute);

      // Add any Service Route Parameters
      if (count($this->m_arParameterRoute)) {
        $url .= '/' . implode('/',
                              array_map(function($p) {
                                return urlencode($p);
                              }, $this->m_arParameterRoute));
      }

      // Add any Extra HTTP GET Parameters
      if (count($this->m_arParameterRequest)) {
        $url .= '?' . implode('&', $this->m_arParameterRequest);
      }

      /* NOTE:
       * Strangely, you have to encode route parameters (converting spaces to +) but,
       * you can't encode the GET request parameters (or they will be double encoded).
       * Guzzle must split the route part, from the GET parameters part, and treat them differently.
       */
      // Escape any special characters
      return $url;
    }

    return null;
  }

}

?>
