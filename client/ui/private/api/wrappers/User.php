<?php
/**
 * Test Center - Compliance Testing Application (Client UI)
 * Copyright (C) 2012 - 2015 Paulo Ferreira <pf at sourcenotes.org>
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
namespace api\wrappers;

use utility\Strings;

/**
 * Users Collection
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class User {

  protected $m_arUser = null;

  /**
   * 
   * @param array $object User Properties Array
   */
  public function __construct($object) {
    $this->m_arUser = $object;
  }

  /*
   * PHP Magic Methods
   */

  /**
   * Retrieve the User Property
   * 
   * @param string $property Property Name
   * @return mixed Property value, if it exists.
   */
  public function __get($property) {
    if ($this->__isset($property)) {
      return $this->m_arUser[$property];
    }
  }

  /**
   * Does the Property Exist in the User?
   * 
   * @param string $property Property Name
   * @return boolean 'true' Property exists, 'false' otherwise
   */
  public function __isset($property) {
    $property = Strings::nullOnEmpty($property);

    return isset($property) && isset($this->m_arUser) && array_key_exists($property, $this->m_arUser);
  }
}
