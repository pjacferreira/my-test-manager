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
namespace api\services;

use common\utility\Strings;

/**
 * URL Builder
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class SiteURL extends \Phalcon\Mvc\Url {

  /**
   * @var String URL Base for Assets (NO LEADING SLASH - WITH TERMINATING SLASH)
   */
  protected $m_sBaseAssets;

  /**
   * @var String URL Base Offset for Javascript Files (NO LEADING SLASH - WITH TERMINATING SLASH)
   */
  protected $m_sBaseJS;

  /**
   * @var String URL Base Offset for CSS Files (NO LEADING SLASH - WITH TERMINATING SLASH)
   */
  protected $m_sBaseCSS;

  /**
   * Constructor
   * 
   * @param String $js URL Base Offset for Javascript Files
   * @param String $css URL Base Offset for CSS Files 
   * @param String $assets URL Base Offset for CSS Files 
   * @throws \Exception On any type of failure condition
   */
  public function __construct($js, $css, $assets = null) {
    $this->m_sBaseAssets = Strings::defaultOnEmpty($assets, '/');
    $this->m_sBaseJS = Strings::nullOnEmpty($js);
    $this->m_sBaseCSS = Strings::nullOnEmpty($css);

    // Is the Base JS Defined?
    if ($this->m_sBaseJS === NULL) { // NO: Error
      throw 'Missing or Invalid Parameter Value [js]';
    }
    // Is the Base CSS Defined?
    if ($this->m_sBaseCSS === NULL) {// NO: Error
      throw 'Missing or Invalid Parameter Value [css]';
    }

    // Is a Base Defined for Assets?
    if ($this->m_sBaseAssets !== null) { // YES
      $this->m_sBaseJS = $this->m_sBaseAssets . $this->m_sBaseJS;
      $this->m_sBaseCSS = $this->m_sBaseAssets . $this->m_sBaseCSS;
    }
  }

  /**
   * 
   * @param type $file
   * @param type $relative
   * @return type
   */
  public function getUrlJS($file, $relative = true) {
    if ($this->isRelativePath($file)) {
      return "{$this->getBaseUri()}{$this->m_sBaseJS}{$file}";
    } else {
      return $file;
    }
  }

  /**
   * 
   * @param type $file
   * @param type $relative
   * @return type
   */
  public function getUrlCSS($file, $relative = true) {
    if ($this->isRelativePath($file)) {
      return "{$this->getBaseUri()}{$this->m_sBaseCSS}{$file}";
    } else {
      return $file;
    }
  }

  /**
   * 
   * @param type $file
   * @param type $relative
   * @return type
   */
  public function getUrlAsset($file, $relative = true) {
    if ($this->isRelativePath($file)) {
      return $this->m_sBaseAssets !== null ? "{$this->getBaseUri()}{$this->m_sBaseAssets}{$file}" : "{$this->getBaseUri()}{$file}";
    } else {
      return $file;
    }
  }

  /**
   * 
   * @param type $id
   * @param type $relative
   * @return type
   */
  public function getPage($id, $relative = false) {
    return "{$this->getBaseUri()}page/{$id}";
  }

  /**
   * 
   * @param type $file
   * @return type
   */
  protected function isRelativePath($file) {
    if ($file[0] === '/') {
      return false;
    }

    return stripos($file, '://') === FALSE;
  }

}
