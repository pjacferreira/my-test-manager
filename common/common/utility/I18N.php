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
 * Internationalization Utility Class
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class I18N {

  /**
   * 
   * @param type $header
   * @param type $locale_paths
   * @param type $domain
   * @param type $default
   * @return type
   */
  public static function initializeFromHeader($header, $locale_paths, $domain = 'messages', $default = 'en_US') {

    // Accepted Locales in Order of Preference
    $header = Strings::nullOnEmpty($header);
    if (isset($header)) {
      $locales = self::parseHeader($header);
      if (isset($locales)) {
        // Find 1st Matching Locale
        foreach ($locales as $candidate) {
          if (is_dir($locale_paths . DIRECTORY_SEPARATOR . $candidate)) {
            self::initialize($candidate, $locale_paths);
            return;
          }
        }
      }
    }

    self::initialize($default, $locale_paths);
  }

  /**
   * 
   * @param type $locale
   * @param type $locale_paths
   * @param type $domain
   * @param type $default
   */
  public static function initialize($locale, $locale_paths, $domain = 'messages', $default = 'en_US') {
    // Clean Parameters
    $locale = Strings::nullOnEmpty($locale);
    $locale_paths = Strings::nullOnEmpty($locale_paths);
    $domain = Strings::nullOnEmpty($domain);
    $default = Strings::nullOnEmpty($default);

    // Validate Defaults
    $domain = isset($domain) ? $domain : 'messages';
    $default = isset($default) ? $default : 'en_US';

    // Do we have a valid Locale Files Path?
    if (isset($locale_paths) && is_dir($locale_paths)) { // YES
      // Do we have a valid Locale Directory?
      $locale = self::normalize($locale, $default);
      $locale = isset($locale) && is_dir($locale_paths . $locale) ? $locale : $default;
      if (is_dir($locale_paths . $locale)) { // YES
        // Initialize I18N support here
        $language = "{$locale}.utf8";
        putenv("LANG=" . $language);
        setlocale(LC_ALL, $language);

        // Bind the Gettext Domain
        bindtextdomain($domain, $locale_paths);
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
      }
    }
  }

  /**
   * 
   * @param type $header
   * @return type
   */
  protected static function parseHeader($header) {
    // Split the Header
    $accept = explode(',', $header);

    // Split Language Description into Locale : Quality Code
    $accept = array_map(function($value) {
      $value = Strings::nullOnEmpty($value);
      return isset($value) ? self::parseLocale($value) : null;
    }, $accept);

    // Remove Nulls from Accepted Languages
    $accept = array_filter($accept, function($value) {
      return isset($value);
    });

    if (count($accept)) {
      // Sort Locales by Quality Code (Highest Q 1st)
      uasort($accept, function($a, $b) {
        if ($a[1] > $b[1]) {
          return 1;
        } else if ($a[1] > $b[1]) {
          return -1;
        } else {
          return ($a[0] > $b[0]) ? 1 : (($a[0] < $b[0]) ? -1 : 0);
        }
      });

      // Extract Sorted Locales
      return array_map(function($value) {
        return $value[0];
      }, $accept);
    }

    // No Header Set (Use Default)
    return null;
  }

  /**
   * 
   * @param type $locale
   * @param type $q_default
   * @return type
   */
  protected static function parseLocale($locale, $q_default = 1.0) {
    $locale = explode(';', $locale, 2);
    if (count($locale) > 1) {
      $quality = Strings::nullOnEmpty($locale[1]);
      $locale = Strings::nullOnEmpty($locale[0]);
      if (isset($locale)) {
        $locale = str_replace('-', '_', $locale);
        if (isset($quality)) {
          $quality = explode('=', $quality, 2);
          if (count($quality) > 1) {
            $quality[0] = Strings::nullOnEmpty($quality[0]);
            $quality[1] = Strings::nullOnEmpty($quality[1]);
            if ($quality[0] === 'q') {
              $quality = floatval($quality[1]);
              $quality = is_nan($quality) ? null : $quality;
            }
          } else {
            $quality = null;
          }
        }
      }
    } else {
      $locale = Strings::nullOnEmpty($locale[0]);
    }

    if (isset($locale)) {
      $locale = self::normalize($locale);
      return isset($quality) ? [$locale, $quality] : [$locale, $q_default];
    }

    return null;
  }

  /**
   * 
   * @param type $locale
   * @return string
   */
  protected static function normalize($locale) {
    // Is the locale in the language_region format?
    if (isset($locale) && (strpos($locale, '_') === FALSE)) { // NO: Try to normalize it
      switch (strtolower($locale)) {
        case 'pt':
          return 'pt_PT';
        case 'es':
          return 'es_ES';
        case 'de':
          return 'de_DE';
        case 'fr':
          return 'fr_FR';
      }
    }
    return $locale;
  }

}
