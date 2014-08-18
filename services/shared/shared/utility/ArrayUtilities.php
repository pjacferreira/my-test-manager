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
 * Array Utility Classs
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ArrayUtilities {

  /**
   * Search an associative array, multi-level, for a value, with optional type
   * verification and default value in case key was not found, or the value
   * is not valid.
   * 
   * @param array $array Associative Array
   * @param mixed $keys Key (string) or key path (string[]) to extract
   * @param mixed $default (OPTIONAL) default value to return, in case key not found (DEFAULT: NULL)
   * @param mixed $types (OPTIONAL) acceptable type for the value (DEFAULT : ANY)
   * @return type
   */
  public static function deepExtract($array, $keys, $default = null, $types = null) {
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

  /**
   * Search an associative array, single-level, for a value, with optional type
   * verification and default value in case key was not found, or the value
   * is not valid.
   * 
   * @param array $array Associative Array
   * @param string $key Key to extract
   * @param mixed $default (OPTIONAL) default value to return, in case key not found (DEFAULT: NULL)
   * @param mixed $types (OPTIONAL) acceptable type for the value (DEFAULT : ANY)
   * @return mixed Value or NULL
   */
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
