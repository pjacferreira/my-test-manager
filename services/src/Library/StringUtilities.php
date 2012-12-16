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

namespace Library;

/**
 * Description of StringUtilities
 *
 * @author Paulo Ferreira
 */
class StringUtilities {

  /**
   * 
   * @param type $string
   * @return null
   */
  public static function nullOnEmpty($string) {
    if (isset($string) && is_string($string)) {
      $return = trim($string);
      return strlen($return) ? $return : null;
    }

    return null;
  }

}

?>
