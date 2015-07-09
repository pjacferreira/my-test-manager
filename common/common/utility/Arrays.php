<?php

/* Test Center - Compliance Testing Application (Shared Library)
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

namespace common\utility;

/**
 * Array Utility Classs
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Arrays {

  /**
   * Test if the given is an empty array.
   *
   * @param array $array Parameter to test
   * @return array NULL if not array or empty array, '$array' otherwise
   */
  public static function nullOnEmpty($array) {
    if (is_array($array)) {
      return (bool) $array ? $array : null;
    }

    return null;
  }

  /**
   * Test if an Array is Map (An Associative Array of key<-->value tuplets, in
   * which the key, is not numeric).
   *
   * @param array $array Array to Test
   * @return boolean 'true' if array is considered associative, 'false' otherwise
   */
  public static function is_map($array) {
    $array = self::nullOnEmpty($array);
    return ($array !== null) &&
    ((bool) count(array_filter(array_keys($array), 'is_string')));
  }

  /**
   * Do a DEEP Merge of one array, into the other. This is different from
   * array_merge_recursive, in that, if the key exists in both arrays, then
   * the key in 'from' will overrite the key in 'into' according to the following
   * rules (i.e. associative arrar are treated like javascript objects/maps
   * and the values are merged).
   *
   * @param array $into the Array to Merge Into
   * @param array $from the Array to Merge From
   * @return array Resultant Array
   */
  public static function mixin($into = null, $from = null) {

    // Was the destination array given?
    if (isset($into)) { // YES
      // Was the source array given?
      if (isset($from)) { // YES
        foreach ($from as $key => $value) {
          /* New Deep Merge Process
           * Reason:
           * 1. Allow us to remove keys from destination (the idea being that
           * if $from, contains a $keys, whose value is null then we remove
           * the same $key from $into (if it exists)
           */
          // Does the Key Exist in the Destination?
          if (!key_exists($key, $into)) { // NO: Just Append
            $into[$key] = $value;
            continue;
          }

          // Is the New VALUE === null?
          if (!isset($value)) { // YES: Remove Element from $into
            unset($into[$key]);
            continue;
          }

          // Is the Destination an Array?
          if (is_array($into[$key])) { // YES
            // Is the Destination an Associative Array?
            if (self::is_map($into[$key])) { // YES: We can Only Merge or Overwrite              
              // Is the Source also an associative array?
              if (is_array($value) && self::is_map($value)) { // YES: Merge
                $into[$key] = self::mixin($into[$key], $value);
                continue;
              }
              // ELSE: NO - Default Handling
            } else { // NO: Destination is a Normal Array
              // Is the Source also a Normal Array?
              if (is_array($value) && !self::is_map($value)) { // YES: Merge
                $into[$key] = array_merge($into[$key], $value);
                continue;
              }
              // ELSE: NO - Default Handling              
            }
          }

          // DEFAULT: Just Append / Overwrite
          $into[$key] = $value;
        }
      }
      return $into;
    } else if (isset($from)) { // NO: Only the source was provided
      return $from;
    }
    //ELSE: Return Nothing
    return null;
  }

  /**
   * Deep Extract a value from an associateve array (MAP).
   *
   * @param string $path A key path that points to the value to extract
   * @param array $array Map to search for key
   * @param mixed $default [DEFAULT: null] value to return if the key doesn't exist
   * @param bool $default_on_null [DEFAULT: true] if the key exists and it's value is null,
   *   do we return the default value?
   * @return mixed value result of extract
   */
  public static function get($path, $array, $default = null, $default_on_null = true) {
    $path = self::path_to_array($path);
    $array = self::is_map($array) ? $array : null;
    $value = $default;

    if (isset($path) && isset($array)) {
      // Explode the Path into it's Components
      $parent = $array;
      $key = array_shift($path);
      while (count($path)) {
        if (!key_exists($key, $parent)) {
          $parent = null;
          break;
        }
        $parent = $parent[$key];
        $key = array_shift($path);
      }

      if (isset($parent) && key_exists($key, $parent)) {
        $value = !isset($parent[$key]) && $default_on_null ? $default : $parent[$key];
      } else {
        $value = $default;
      }
    }

    return $value;
  }

  /**
   * Deep Set a value from an associateve array (MAP).
   *
   * @param string $path A key path that points to the value to extract
   * @param array $array Map to search for key
   * @param mixed $default [DEFAULT: null] value to return if the key doesn't exist
   * @param bool $default_on_null [DEFAULT: true] if the key exists and it's value is null,
   *   do we return the default value?
   * @return mixed value result of extract
   */
  public static function set($path, $array, $value) {
    $path = self::path_to_array($path);
    $array = self::is_map($array) ? $array : [];

    if (isset($path) && isset($array)) {
      // Descend as Far as Possible into the Array
      $parent = &$array;
      $key = array_shift($path);
      while (count($path) && key_exists($key, $parent)) {
        $parent = &$parent[$key];
        $key = array_shift($path);
      }

      // Create Nested Arrays till the last Key
      while (count($path)) {
        $parent[$key] = [];
        $parent = &$parent[$key];
        $key = array_shift($path);
      }

      // Create Value Entry
      $parent[$key] = $value;
    }

    return $array;
  }

  /**
   * Extract Only the Required Keys from the Array
   *
   * @param string|string[] $keys Keys to extract from the map
   * @param array $array Map of values to filter
   * @param array $defaults [DEFAULT: Null] Default values to use, if map does not contain the key
   * @return array|null
   */
  public static function filter($keys, $array, $defaults = null) {
    $keys = is_string($keys) ? [$keys] :
    (is_array($keys) ? $keys : null);
    $defaults = is_array($defaults) ? $defaults : null;

    // Look for Matiching Keys in the $array or, if available, in defaults
    $intersect = [];

    // Do we have a set of keys to extract?
    if (isset($keys)) { // YES
      foreach ($array as $key => $value) {
        // Is the Current Key in the incoming array?
        if (array_search($key, $keys) !== FALSE) { // YES
          $intersect[$key] = $array[$key];
        } else // NO: Is it in the Defaults?
        if (isset($defaults) && array_key_exists($key, $defaults) && isset($defaults[$key])) { // YES
          $intersect[$key] = $defaults[$key];
        }
      }
    }

    return count($intersect) ? $intersect : null;
  }

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
        $key = Strings::nullOnEmpty($keys[$i]);
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

  /**
   * Return a Normalized Path Variable (i.e. an array of strings)
   *
   * @param mixed $path Path to normalize
   * @return array return normalized path or 'null'
   */
  protected static function path_to_array($path) {
    if (isset($path)) {
      if (!is_array($path)) {
        $path = Strings::nullOnEmpty($path);
        if (isset($path)) {
          $path = explode('/', $path);
        }
      }
    }

    return $path;
  }

}
