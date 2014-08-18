<?php

/* Test Center - Compliance Testing Application (Services Shared Library)
 * Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
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

namespace shared\utility;

/**
 * String Utility Classs
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class StringUtilities {

  /**
   * Trims a String and verifies if it is empty. If the string is empty, returns
   * 'null' instead.
   * 
   * @param string $string String Value to trim, validate
   * @return string Value or NULL
   */
  public static function nullOnEmpty($string) {
    if (isset($string) && is_string($string)) {
      $return = trim($string);
      return strlen($return) ? $return : null;
    }

    return null;
  }

}
