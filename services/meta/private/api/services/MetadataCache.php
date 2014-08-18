<?php

/* Test Center - Compliance Testing Application (Metadata Service)
 * Copyright (C) 2014 Paulo Ferreira <pf at sourcenotes.org>
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

use shared\utility\StringUtilities;

/**
 * Class provides a Cache Service for Metadata (i.e. the YAML Files are converted
 * to PHP and stored in the Cache).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class MetadataCache {

  protected $metadataDir = null;
  protected $cacheDir = null;

  /**
   * Class Constructor
   * 
   * @param String $metadataDir Path to Metadata (YAML Files)
   * @param String $cacheDir Path to Cache
   */
  function __construct($metadataDir, $cacheDir) {
    $this->metadataDir = StringUtilities::nullOnEmpty($metadataDir);
    $this->cacheDir = StringUtilities::nullOnEmpty($cacheDir);

    // Make sure we have requirements
    assert('isset($this->metadataDir)');
    assert('isset($this->cacheDir)');
  }

  /**
   * Get Metadata for Specific Type (service, form, etc.) for a 
   * Specific Entity (User, Organization, Project, etc.) for a
   * Specific Variation/Action (read, update, etc.)
   * 
   * @param string $type Metadata Type
   * @param string $entity Metadata Entity
   * @param string $variation Variation or Action
   * @return array Map of Metadata Properties or 'null' if none found
   */
  public function get($type, $entity, $variation = null) {
    assert('isset($type) && is_string($type)');
    assert('isset($entity) && is_string($entity)');
    assert('!isset($variation) || is_string($variation)');

    $metadata = $this->searchForMetadata($type, $entity);
    if (isset($variation)) {
      return isset($metadata) && array_key_exists($variation, $metadata) ?
              $metadata[$variation] : null;
    } else {
      return $metadata;
    }
  }

  /**
   * Search the YAML Files for the Entities Metadata. Start with the most
   * specific, to the least specific.
   * 
   * @param string $type Metadata Type
   * @param string $entity Metadata Entity
   * @return array Map of Metadata Properties or 'null' if none found
   */
  protected function searchForMetadata($type, $entity) {
    /*
     * Try 1: Load From Specific File
     */
    $file = "meta.{$type}s.{$entity}";
    $metadata = $this->loadMetadata($file);
    if (isset($metadata) && array_key_exists($entity, $metadata)) {
      return $metadata[$entity];
    }

    /*
     * Try 2: Load From a General Entity File
     */
    $file = "meta.{$type}s";
    $metadata = $this->loadMetadata($file);
    if (isset($metadata) &&
            array_key_exists("{$type}s", $metadata) &&
            array_key_exists($entity, $metadata["{$type}s"])) {
      return $metadata["{$type}s"][$entity];
    }

    /*
     * Try 3: Load General Metadata File
     */
    $file = "meta.defaults";
    $metadata = $this->loadMetadata($file);
    if (isset($metadata) &&
            array_key_exists("{$type}s", $metadata) &&
            array_key_exists($entity, $metadata["{$type}s"])) {
      return $metadata["{$type}s"][$entity];
    }

    return null;
  }

  /**
   * Load the Metadata from a Specific Source (YAML or PHP Converted File, if 
   * one exists).
   * 
   * @param string $source Base name for FILE that should contain the Metadata
   * @return array Map of Metadata Properties or 'null' if none found
   */
  protected function loadMetadata($source) {
    // 1st Attempt - Load From Cache
    $metadata = $this->loadFromCache($source);

    // Did we load anything from the cache?
    if (!isset($metadata)) { // NO: Try to rebuild metadata
      $path = $this->metadataDir . $source . '.yml';
      // Does the File Exist?
      if (file_exists($path)) { // YES
        // 2nd Attempt - Parse the Original Source File
        $metadata = yaml_parse_file($path);

        // Do we have metadata?
        if (isset($metadata)) { // YES: Cache it
          $this->cacheMetadata($source, $metadata);
        }
      }
    }

    return $metadata;
  }

  /**
   * Attempt to Load Metadata from Cache, if it exists and is newer than the
   * orginal.
   * 
   * @param string $key Base name for the PHP Cache File
   * @return array Map of Metadata Properties or 'null' if none found
   */
  protected function loadFromCache($key) {
    // Create Path to Cache File
    $path = $this->cacheDir . $key . '.php';

    /* TODO Handle i18n variations fo the Metadata.
     * We can probably do this, adding a language/locale to the file name
     */

    // Does the a chache of the file exist?
    if (file_exists($path)) { // YES
      // Is the Cached File Newer than Source File?
      if ($this->newerThan($path, $this->metadataDir . $key . '.yml')) { // YES: Cache File is Valid
        // Return the Data from the File
        return include $path;
      }
    }

    return null;
  }

  /**
   * Save/Update the Metadata in the Cache.
   * 
   * @param string $key Base name for the PHP Cache File
   * @param array $metadata Map of Metadata Properties to Cache
   * @return array Map of Metadata Properties or 'null' if none found
   */
  protected function cacheMetadata($cachekey, $metadata) {
    // Export the Metadata to a String
    $php_metadata = var_export($metadata, true);

    /* TODO Handle i18n variations fo the Metadata.
     * We can probably do this, adding a language/locale to the file name
     */

    // Create Paths to Files
    $path = $this->cacheDir . $cachekey . '.php';

    // Were we able to write the file?
    if (file_put_contents($path, "<?php\n return {$php_metadata};\n", LOCK_EX) === FALSE) { // NO
      // TODO Log Error (Failed to Create File)
      // Was a file created (event though wwe failed to successfully write to it)?
      if (file_exists($path) && (unlink($path) === FALSE)) { // YES: and We failed to Delete it...
        // TODO Log Error (Failed to Delete File)
      }
    }

    return $metadata;
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
