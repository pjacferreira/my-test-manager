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
   * @param type $locale
   * @return string
   */
  protected static function normalize($locale, $default) {
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
        default: // Can't  Find Normalized Version - use default
          return $default;
      }
    }
    return $locale;
  }

}
