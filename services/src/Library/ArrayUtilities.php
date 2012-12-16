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
 * Description of ArrayUtilities
 *
 * @author Paulo Ferreira
 */
class ArrayUtilities {

  public static function deepExtract($array, $keys, $default = null,
                                     $types = null) {
    assert('isset($array) && is_array($array)');
    assert('isset($keys) && (is_string($keys) || is_array($keys))');
    assert('!isset($types) || is_string($types)');

    if (is_string($keys)) {
      return self::extract($array, $keys, $default, $types);
    } else if (is_array($keys)) {
      $parent = $array;
      for ($i = 0; $i < (count($keys) - 1); $i++) {
        $key = StringUtilities::nullOnEmpty($keys[$i]);
        if (!isset($key) || !array_key_exists($key, $parent) || !is_array($parent[$key])) {
          $parent = null;
          break;
        }

        $parent = $parent[$key];
      }

      if (isset($parent)) {
        return self::extract($parent, $keys[$i], $default, $types);
      }
    }

    return $default;
  }

  public static function extract($array, $key, $default = null, $types = null) {
    assert('isset($array) && is_array($array)');
    assert('isset($key) && is_string($key)');
    assert('!isset($types) || is_string($types)');

    // Extract Value from Array or Use Default
    $value = $default;
    if (array_key_exists($key, $array)) {
      $value = $array[$key];
    }

    // See if we have to test the values, against pre-defined types
    if (isset($types)) {
      // Array of Types to Test
      $ar_types = explode(',', $types);

      // Test Each Type
      foreach ($types as $type) {
        $type = trim($type);

        // Test the Value Against the Function
        if (function_exists($type) && $type($value)) {
          return $value;
        }
      }

      return $default;
    }

    return $value;
  }

}
