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
namespace api\templates;

use common\utility\Strings;
use common\utility\Arrays;

/**
 * Pages Controller
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class PageRenderer extends \Phalcon\DI\Injectable {

  /**
   *
   * @var String Base Path for Pages
   */
  protected $m_sBasePath;

  /**
   *
   * @var Map Page Settings Map
   */
  protected $m_mapSettings;

  /**
   *
   * @var Map Page Parameter Map
   */
  protected $m_mapParameters;

  /**
   * Constructor
   * 
   * @param String $sPathToPages Base Path to Directory Containing Page Sources
   * @throws \Exception On any type of failure condition
   */
  public function __construct($sPathToPages) {
    $this->m_sBasePath = Strings::nullOnEmpty($sPathToPages);

    if ($this->m_sBasePath === NULL) {
      throw 'Missing or Invalid Parameter Value [sPathToPages]';
    }
  }

  /**
   * Render a Page Based on the Incoming Settings
   * 
   * @param String $sPageID ID for Rendered Page
   * @param Map $mapSettings Page Rendering Settings
   * @param Map $mapParameters Page Parameters passed in as part of the request
   * @return String Rendered Content
   * @throws \Exception On any type of failure condition
   */
  public function render($sPageID, $mapSettings, $mapParameters = null) {
    /* TODO: Throw an Exception in aMore Normalized Format of
     * RESPONSE TYPE: RESPONSE MESSAGE
     * So that this can then be forwarded to the correct replay page
     */

    // Do we have valid parameters?
    $sPageID = Strings::nullOnEmpty($sPageID);
    if (isset($sPageID) && isset($mapSettings) && is_array($mapSettings)) {
      // Save Settings for Page
      $this->m_mapSettings = $mapSettings;
      $this->m_mapParameters = $mapParameters;

      // Does the Page Exist?
      $page = $this->m_sBasePath . $this->setting('page', 'page.php');
      if (file_exists($page)) { // YES    
        // Were we able to turn on Output Buffering?
        if (ob_start()) { // YES
          // Generate Page
          include $page;

          // Can we Retrieve the Buffers Contents?
          $contents = ob_get_contents();
          if ($contents !== FALSE) { // YES
            // Close the Buffer (and clean it)
            ob_end_clean();

            return $contents;
          }

          //ELSE: NO - ERROR
          throw 'Failed to Retrieve Output Buffer Contents';
        }
        // ELSE: NO - ERROR
        throw "Failed to Initiate Output Buffer";
      }
    }
    // ELSE: NO - ERROR
    throw "Invalid Page - Send 404";
  }

  /**
   * 
   * @return type
   */
  public function templatesPath() {
    return $this->m_sBasePath;
  }

  /**
   * Access to Page Settings
   * 
   * @return Map Page Settings
   */
  public function settings() {
    return $this->m_mapSettings;
  }

  /**
   * Access to Page Parameters
   * 
   * @return Map Page Parameters
   */
  public function parameters() {
    return $this->m_mapParams;
  }

  /**
   * 
   * @param Mixed $key
   * @param Mixed $default
   * @return Mixed
   */
  public function setting($key, $default = null, $echo = false) {
    // Set Default Value
    $value = $default;

    // Do we have a search key?
    $key = Strings::nullOnEmpty($key);
    if (isset($key)) { // YES: Extract Value for the Key
      $value = Arrays::get($key, $this->m_mapSettings, $default);
    }

    // Should we echo the value?
    if (!!$echo && isset($value) && is_string($value)) { // YES
      echo $value;
    }

    return $value;
  }

  /**
   * 
   * @param Mixed $key
   * @param Mixed $default
   * @return Mixed
   */
  public function i18n_setting($key, $default = null, $echo = false) {
    // Set Default Value
    $value = $default;

    // Do we have a search key?
    $key = Strings::nullOnEmpty($key);
    if (isset($key)) { // YES: Extract Value for the Key
      $value = Arrays::get($key, $this->m_mapSettings, $default);
    }

    // Do we have an I18N Value for the setting?
    $i18n = _($value);
    if (isset($i18n)) { // YES: Use it
      $value = $i18n;
    }

    // Should we echo the value?
    if (!!$echo && isset($value) && is_string($value)) { // YES
      echo $value;
    }

    return $value;
  }

  /**
   * 
   * @param Mixed $key
   * @param Mixed $default
   * @return Mixed
   */
  public function parameter($key, $default = null, $echo = false) {
    // Set Default Value
    $value = $default;

    // Do we have a search key?
    $key = Strings::nullOnEmpty($key);
    if (isset($key)) { // YES: Extract Value for the Key
      $value = Arrays::get($key, $this->m_mapParameters, $default);
    }

    // Should we echo the value?
    if (!!$echo && isset($value) && is_string($value)) { // YES
      echo $value;
    }

    return $value;
  }

  /**
   * 
   * @param type $pageID
   * @param type $attribute
   * @param type $echo
   * @return string
   */
  public function pageLink($pageID, $attribute = null, $echo = true) {
    // Clean up Input Parameters
    $pageID = Strings::nullOnEmpty($pageID);
    $attribute = Strings::nullOnEmpty($attribute);

    $value = isset($attribute) ? "{$attribute}='" : '';
    if (isset($pageID)) {
      $value.=$this->url->getPage($pageID);
    }

    if (isset($attribute)) {
      $value.="'";
    }

    // Should we echo the value?
    if (!!$echo && isset($value) && is_string($value)) { // YES
      echo $value;
    }

    return $value;
  }

  /**
   * 
   * @param type $image
   * @param type $attribute
   * @param type $echo
   * @return string
   */
  public function imageLink($image, $attribute = null, $echo = true) {
    // Clean up Input Parameters
    $image = Strings::nullOnEmpty($image);
    $attribute = Strings::nullOnEmpty($attribute);

    $value = isset($attribute) ? "{$attribute}='" : '';
    if (isset($image)) {
      $value.=$this->url->getUrlAsset("/img/{$image}");
    }

    if (isset($attribute)) {
      $value.="'";
    }

    // Should we echo the value?
    if (!!$echo && isset($value) && is_string($value)) { // YES
      echo $value;
    }

    return $value;
  }

  /**
   * 
   * @param type $echo
   */
  public function includeCSSFiles($echo = true) {
    $files = $this->setting('css/files');
    for ($i = 0; $i < count($files); $i++) {
      // ASSUMPTION: $files has already been processed
      $file = $this->url->getUrlCSS($files[$i]);
      echo "<link rel='stylesheet' href='{$file}'>";
    }
  }

  /**
   * 
   * @param type $echo
   */
  public function includeJSFiles($echo = true) {
    $files = $this->setting('js/files');
    for ($i = 0; $i < count($files); $i++) {
      $file = $this->url->getUrlJS($files[$i]);
      echo "<script type='application/javascript' src='{$file}'></script>";
    }
  }

  /**
   * 
   * @param type $key
   * @param type $echo
   */
  public function includeJSScripts($key, $echo = true) {
    $key = Strings::nullOnEmpty($key);
    $scripts = isset($key) ? $this->setting("js/scripts/{$key}") : null;
    if (isset($scripts)) {
      switch ($key) {
        case 'on-ready':
          echo "<script type='application/javascript'>";
          echo "$(document).ready(function () {";
          break;
        case 'on-load':
          echo "<script type='application/javascript'>";
          echo "$(window).load(function () {";
          break;
        default:
          echo "<script type='application/javascript'>";
      }

      for ($i = 0; $i < count($scripts); $i++) {
        echo $scripts[$i];
      }

      switch ($key) {
        case 'on-load':
        case 'on-ready':
          echo "});";
        default:
          echo "</script>";
      }
    }
  }

  /**
   * 
   * @param type $key
   * @return type
   */
  public function hasJSScripts($key) {
    $key = Strings::nullOnEmpty($key);
    return isset($key) ? $this->setting("js/scripts/{$key}") !== null : false;
  }

  /**
   * 
   * @param type $name
   * @param type $value
   * @param type $default
   */
  public function html_attribute($name, $value, $default = null) {
    $name = Strings::nullOnEmpty($name);
    $value = Strings::nullOnEmpty($value);
    $default = Strings::nullOnEmpty($default);

    $value = isset($value) ? $value : $default;
    if (isset($name) && isset($value)) {
      echo "{$name}='{$value}'";
    }
  }

}
