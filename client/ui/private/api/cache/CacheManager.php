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
namespace api\cache;

use common\utility\Strings;

/**
 * Cache Manager
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class CacheManager extends \Phalcon\DI\Injectable {

  /**
   * @var String Base Path for Cache (WITH TERMINATING SLASH)
   */
  protected $m_sBasePath;

  /**
   * Constructor
   * 
   * @param String $sCachePath Base Path to the Cache 
   * @throws \Exception On any type of failure condition
   */
  public function __construct($sCachePath) {
    $this->m_sBasePath = Strings::nullOnEmpty($sCachePath);

    if ($this->m_sBasePath === NULL) {
      throw 'Missing or Invalid Parameter Value [sCachePath]';
    }
  }

  /**
   * Test if the 'source' exists in the cache and if the cache entry is
   * up to date?
   * 
   * @param String $source Source File(s) that need to be tested
   * @param String $destination Cached File 
   * @return bool 'true' if a valid cache entry exists, 'false' otherwise
   * @throws String Exception Message on Failure
   */
  public function isValid($source, $destination) {
    // Clean Incoming Parameters
    $source = Strings::nullOnEmpty($source);
    $destination = Strings::nullOnEmpty($destination);

    // Do we have a potentially valid source or destination?
    if (($source !== null) && ($destination !== null)) { // YES
      // Is the Source Newer than the Destination?
      return !$this->newerThan($source, $destination);
    } else {
      // ELSE: NO - Abort
      throw "Missing or Invalid Function Parameters [source, destination]";
    }
  }

  /**
   * Verify a Single Cache Entry, and Update if necessary
   * 
   * @param String $source Source File(s) that need to be tested
   * @param String $destination Cached File 
   * @param Function $callback Function called to update cache entry
   * @throws String Exception Message on Failure
   */
  public function cache($destination, $content, $as_include = true) {
    // Is the Content already a String?
    if (!is_string($content)) { // NO
      // Convert ti PHP String
      $content = var_export($content, true);
    }

    // Convert $content if necessary
    $content = $as_include ? "<?php return {$content};" : $content;

    // Does the Parent Folder Exist?
    $parent = dirname($destination);
    if (!is_dir($parent)) { // NO: Create it
      mkdir($parent, 0777, true);
    }

    // Were we able to write the file?
    if (file_put_contents($destination, $content, LOCK_EX) === FALSE) { // NO
      // Was a file created (even though we failed to successfully write to it)?
      if (file_exists($destination) && (unlink($destination) === FALSE)) { // YES: and We failed to Delete it...
        throw "FAILED to WRITE and DELETE PHP File [{$destination}]";
      }

      throw "FAILED to WRITE PHP File [{$destination}]";
    }

    return $content;
  }

  /**
   * Verify a Single Cache Entry, and Update if necessary
   * 
   * @param String $source Source File(s) that need to be tested
   * @param String $destination Cached File 
   * @param Function $callback Function called to update cache entry
   * @throws String Exception Message on Failure
   */
  public function single($source, $destination, $callback) {
    // Clean Incoming Parameters
    $source = Strings::nullOnEmpty($source);
    $destination = Strings::nullOnEmpty($destination);
    $callback = isset($callback) && is_callable($callback) ? $callback : null;

    // Do we have a potentially valid source or destination?
    if (($source !== null) && ($destination !== null) && ($callback !== null)) {
      // Is the Source Newer than the Destination?
      if ($this->newerThan($source, $destination)) { // YES: Update Cache
        $callback($source, $destination);
      }
    } else {
      // ELSE: NO - Abort
      throw "Missing or Invalid Function Parameters [source, destination]";
    }
  }

  /**
   * Verify a Series of Cache Entries, which have to be in order of dependency
   * (i.e. the files with no dependants, come before those that have dependencies)
   * 
   * 
   * @param Array $map Source File(s) that need to be tested
   * @param Function $callback Function called to update cache entry
   * @throws String Exception Message on Failure
   */
  public function multiple($map, $callback) {
    $map = isset($map) && is_array($map) ? $map : null;
    $callback = isset($callback) && is_callable($callback) ? $callback : null;

    // Are the Parameters, minimily valid?
    if (($map !== null) && ($callback !== null)) { // YES
      // Loop through the Map, making sure the Cache entry is still valid
      $entry = $last_source = $source = $destination = null;
      for ($i = 0; $i < count($map); ++$i) {
        $entry = $map[$i];

        // Do we have a potentially valid entry?
        if (isset($entry) && is_array($entry) && count($entry) >= 2) { // YES
          $source = Strings::nullOnEmpty($entry[0]);
          $destination = Strings::nullOnEmpty($entry[1]);

          // Do we have a Source and Destination?
          if (isset($source) && isset($destination)) { // YES
            // Is the source newer than the destination?
            if ($this->newerThan($source, $destination)) { // YES: Break
              break;
            } else if (isset($last_source) && $this->newerThan($last_source, $source)) {
              // NO: The Last Source, is newer than the current source : Break
              break;
            }

            $last_source = $source;
          }
        }
      }

      // Do we still have any entries in the Map to Verify?
      if ($i < count($map)) { // YES
        // Loop through the Map, updating the remaining entries
        for (; $i < count($map); ++$i) {
          $entry = $map[$i];

          // Do we have a potentially valid entry?
          if (isset($entry) && is_array($entry) && count($entry) >= 2) {
            $source = Strings::nullOnEmpty($entry[0]);
            $destination = Strings::nullOnEmpty($entry[1]);

            // Do we have a potentially valid source or destination?
            if (isset($source) && isset($destination)) { // YES: Update it
              $callback($source, $destination);
            }
          }
        }
      }
    }
    // ELSE: NO - Abort
    throw "Missing or Invalid Function Parameters [map, destination]";
  }

  /**
   * Returns the FULL Path to a Cache File
   * 
   * @param String $file Relative Path (NO LEADING SLASH) to cache file
   * @return String Full Path to Cache File or NULL if invalid path
   */
  public function pathToFile($file) {
    $file = Strings::nullOnEmpty($file);
    return isset($file) ? $this->m_sBasePath . $file : null;
  }

  /**
   * Checks if the Source File is newer than then another file.
   * 
   * @param string $source Soure File
   * @param string $compareTo File to Compare Against
   * @return boolean 'true' if Source is Newer, 'false' toherwise
   */
  protected function newerThan($source, $compareTo) {
    $sourceTime = isset($source) ? filemtime($source) : FALSE;
    // Do we have a Time for the Source File (i.e. does it exists)?
    if ($sourceTime !== FALSE) { // YES
      $compareToTime = isset($compareTo) ? filemtime($compareTo) : FALSE;
      // Do we have a time for the Compare To File?
      if (($compareToTime === FALSE) || // No File to Compare To (therefore it's newer)
              ($sourceTime > $compareToTime)) { // $source is NEWER than $compareTo
        return true;
      }
    }

    return false;
  }

}
