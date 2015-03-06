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
namespace api\yaml;

use common\utility\Strings;

/**
 * YAML Compiler
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class CompilerYAML extends \Phalcon\DI\Injectable {

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
  public function __construct($sYAMLPath) {
    $this->m_sBasePath = Strings::nullOnEmpty($sYAMLPath);

    if ($this->m_sBasePath === NULL) {
      throw 'Missing or Invalid Parameter Value [sYAMLPath]';
    }
  }

  /**
   * Compile YAML Input File to PHP Output File
   * 
   * @param String $source  Source File (YAML Input)
   * @param String $destination Destinantion File (PHP Output of YAML)
   * @return Map Result of YAML File Parse
   * @throws String Exception Message
   */
  public function compile($source, $destination = NULL) {
    // Clean Incoming Parameters
    $source = Strings::nullOnEmpty($source);
    $destination = Strings::nullOnEmpty($destination);

    // Do we have a source file?
    if (isset($source)) { // YES
      $yaml = $this->compileYAML($source);

      // Do we have a destination file?
      if (isset($destination)) { // YES: Save Results
        $this->savePHP($destination, $yaml);
      }
      // ELSE: NO - Just return Parsed YAML
      return $yaml;
    }
    // ELSE: NO - Abort
    throw "Missing or Invalid Function Parameters [source, destination]";
  }

  /**
   * Returns the FULL Path to a YAML File
   * 
   * @param String $file Relative Path (NO LEADING SLASH) to YAML file
   * @return String Full Path to YAML File or NULL if invalid path
   */
  public function pathToFile($file) {
    $file = Strings::nullOnEmpty($file);
    return isset($file) ? $this->m_sBasePath . $file : null;
  }

  /**
   * Compile YAML to a PHP String that can be Outputted toa File
   * 
   * @param String $source  Source File (YAML Input)
   * @return Map Result of YAML File Parse
   * @throws String Exception Message
   */
  protected function compileYAML($source) {
    // Does the File Exist?
    if (file_exists($source)) { // YES
      // Compile FILE to PHP (VARIABLES)
      $yaml = yaml_parse_file($source);

      // Were we able to Parse the File?
      if ($yaml !== FALSE) { // YES
        return $yaml;
      }
      // ELSE: NO - Abort
      throw new \Exception("Failed to Parse YAML File [{$source}]");
    }
    // ELSE: NO - Abort
    throw new \Exception("YAML File Doesn't Exist [{$source}]");
  }

  /**
   * Save YAML Output to a PHP Include File.
   * NOTE:
   * The PHP file returns a MAP of the YAML Input, and therefore should be used
   * like,
   * $yaml = include $destination;
   * 
   * 
   * @param String $destination Destinantion File (PHP Output of YAML)
   * @param Map $yaml Result of YAML File Parse
   * @return String PHP Output
   * @throws String Exception Message
   */
  protected function savePHP($destination, $yaml) {
    // Convert YAML Variables to PHP String
    $php = var_export($yaml, true);

    // Does the Parent Folder Exist?
    $parent = dirname($destination);
    if (!is_dir($dirname)) { // NO: Create it
      mkdir($parent, 0777, true);
    }

    // Were we able to write the file?
    if (file_put_contents($destination, "<?php\n return {$php};\n", LOCK_EX) === FALSE) { // NO
      // Was a file created (even though we failed to successfully write to it)?
      if (file_exists($destination) && (unlink($destination) === FALSE)) { // YES: and We failed to Delete it...
        throw "FAILED to WRITE and DELETE PHP File [{$destination}]";
      }

      throw "FAILED to WRITE PHP File [{$destination}]";
    }

    return $php;
  }

}
