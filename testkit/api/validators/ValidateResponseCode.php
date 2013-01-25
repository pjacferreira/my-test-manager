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

namespace api\validators;

require_once dirname(__FILE__).'/../utility.php';

/**
 * Validate Request Response Code
 *
 * @author Paulo Ferreira
 */
class ValidateResponseCode {

  protected static $m_arInstance;
  protected $m_nCode;

  /**
   * Singleton Class - Hide Constructor
   */
  protected function __construct($code) {
    assert('isset($code) && is_integer($code) && ($code >= 100)');
    $this->m_nCode = $code;
  }

  /**
   * Create and Reuse a Single Instance of the class per Response Code
   * 
   * @return type
   */
  public static function getInstance($code) {
    $code = integer_gt($code, 99, 200);

    if (!isset(self::$m_arInstance)) {
      self::$m_arInstance = array($code => new ValidateResponseCode($code));
    } else if (!array_key_exists($code, self::$m_arInstance)) {
      self::$m_arInstance[$code] = new ValidateResponseCode($code);
    }

    return self::$m_arInstance[$code];
  }

  /**
   * 
   * @param type $response
   * @return type
   */
  public function verify($response) {
    assert('isset($response)');

    return $response->getStatusCode() == $this->m_nCode;
  }

}

?>
