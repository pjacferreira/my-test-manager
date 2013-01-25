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

namespace api\renderers;

/**
 * Render a TestCenter Service JSON Response
 *
 * @author Paulo Ferreira
 */
class RenderTCService
  implements IRenderer {

  protected static $m_oInstance;

  /**
   * Singleton Class - Hide Constructor
   */
  protected function __construct() {
    
  }

  /**
   * Create and Reuse a Single Instance of the class
   * 
   * @return type
   */
  public static function getInstance() {
    if (!isset(self::$m_oInstance)) {
      self::$m_oInstance = new RenderTCService();
    }

    return self::$m_oInstance;
  }

  /**
   * 
   * @param type $response
   * @return type
   */
  public function render($response) {
    assert('isset($response) && ($response->getStatusCode() == 200)');

    if ($response->isContentType('application/json')) {
      $message = $response->getBody(true);
      $json = json_decode($message, true);

      if (isset($json['version']) && isset($json['error'])) {
        if ($json['error']['code'] == 0) {
          return isset($json['return']) ? json_encode($json['return']) : '{"return":null}';
        } else {
          unset($json['version']);
          return json_encode($json);
        }
      }

      return json_encode($json);
    }

    return 'INVALID SERVICE RESPONSE';
  }

}

?>
