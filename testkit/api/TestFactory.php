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

use api\Test;

/**
 * Description of TestFactory
 *
 * @author Paulo Ferreira
 */
class TestFactory {

  /**
   * 
   * @param type $group
   * @param type $priority
   * @param type $message
   * @return type
   */
  public static function marker($sGroup, $nPriority, $sMessage = null) {
    $sGroup = string_onEmpty($sGroup);
    $nPriority = integer_gt($nPriority);

    if (isset($sGroup) && isset($nPriority)) {
      return Test::getMarker($sGroup, $nPriority)->setRenderer(new renderers\RenderMessage(isset($sMessage) ? $sMessage : 'MARKER'));
    }

    throw new \Exception("ERROR: Missing or Invalid GROUP and PRIORITY Parameters.");
  }

  /**
   * 
   * @param type $sGroup
   * @param type $nPriority
   * @param type $service
   * @param type $p_route
   * @param type $p_request
   * @param type $r_code
   * @return type
   * @throws \Exception
   */
  public static function serviceTest($sGroup, $nSequence, $service, $p_route, $p_request = null, $r_code = 200) {
    $sGroup = string_onEmpty($sGroup);
    $nSequence = integer_gt($nSequence);
    $service = self::extractService($service);

    if (isset($sGroup) && isset($nSequence)) {
      $p_route = self::explodeRoute($p_route);
      $p_request = self::explodeRequest($p_request);
      $r_code = integer_gt($r_code, 0, 200);

      return Test::getTest($sGroup, $nSequence)
                      ->setService($service)
                      ->setParameters($p_route)
                      ->setParameters($p_request, false)
                      ->setValidator(validators\ValidateResponseCode::getInstance($r_code))
                      ->setRenderer(renderers\RenderNULL::getInstance());
    }

    throw new \Exception("ERROR: Missing or Invalid GROUP and PRIORITY Parameters.");
  }

  /**
   * 
   * @param type $sGroup
   * @param type $nPriority
   * @param type $service
   * @param type $p_route
   * @param type $p_request
   * @param type $bOK
   * @param type $json
   * @return type
   */
  public static function tcServiceTest($sGroup, $nPriority, $service, $p_route = null, $p_request = null, $bOK = true, $json = true) {

    $test = self::serviceTest($sGroup, $nPriority, $service, $p_route, $p_request);
    if ($json) { // Display JSON Result
      $test->setRenderer(renderers\RenderTCService::getInstance());
    }
    return $test->setValidator($bOK ? validators\ValidateTCServiceOK::getInstance() : validators\ValidateTCServiceNOK::getInstance());
  }

  /**
   * 
   * @param type $service
   * @return type
   * @throws \Exception
   */
  protected static function extractService($service) {
    if (isset($service)) {
      if (is_string($service)) {
        $service = self::explodeRoute($service);
      } if (!is_array($service)) {
        unset($service);
      }

      if (isset($service)) {
        return $service;
      }
    }

    throw new \Exception("REQUIRED: Valid Service Parameter.");
  }

  /**
   * 
   * @param type $route
   * @return type
   */
  protected static function explodeRoute($route) {
    if (isset($route)) {
      if (is_array($route)) {
        // TODO use array_map to atleast mark empty strings as null
        return $route;
      } else if (is_string($route)) {
        $route = string_onEmpty($route);
        if (isset($route)) {
          return explode('/', $route);
        }
      } else if (is_integer($route)) {
        return $route;
      }
    }

    return null;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected static function explodeRequest($parameters) {
    if (isset($parameters) && is_array($parameters)) {
      $result = null;
      foreach ($parameters as $name => $value) {
        $value = string_onEmpty($value, '');
        $result = isset($result) ? "{$result}&{$name}={$value}" : "{$name}={$value}";
      }

      return $result;
    }

    return isset($parameters) ? string_onEmpty($parameters) : null;
  }

}

?>
