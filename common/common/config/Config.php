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

namespace common\config;

// Are we loading the Config File Ouside the PHALCON App Framework? 
if (!isset($FLAG_PHALCON)) { // NO: Use BASE to Load Utility Class
  require_once __DIR__ . '/../utility/Strings.php';
}

/**
 * Extension of PHALCON Congig
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class Config extends \Phalcon\Config {

  /**
   * Extract a Value given by the Path from the Configuration Settings
   * 
   * @param string $path Path to Setting
   * @param mixed $default Default Value to use if Setting doesn't exist or is null
   * @return mixed Value for Setting
   */
  function extract($path, $default = null) {
    // Explode the path into it's components
    $path = explode('.', $path);

    // Deep Extract the Value
    $value = null;
    $parent = $this;
    for ($count = count($path), $i = 0; $i < $count; $i++) {
      if ($i === ($count - 1)) {
        $value = $parent->get($path[$i]);
      } else {
        $parent = $parent->get($path[$i]);
        if (!(isset($parent) && ($parent instanceof \Phalcon\Config))) {
          break;
        }
      }
    }

    return isset($value) ? $value : $default;
  }

  /**
   * Extract a String Value, given by the path, from the Configuration Settings
   * 
   * @param string $path Path to Setting
   * @param string $default Default Value to use if Setting doesn't exist or is null
   * @return string Value for Setting
   */
  function extract_string($path, $default = null) {
    return \common\utility\Strings::defaultOnEmpty($this->extract($path), $default);
  }

}
