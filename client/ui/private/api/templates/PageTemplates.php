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
class PageTemplates extends \Phalcon\DI\Injectable {

  public function group($group) {
    // Initialization
    $di = $this->getDI();
    $cacheManager = $di->getShared('cacheManager');
    $yamlCompiler = $di->getShared('yamlCompiler');

    // Get Path to Source and Destination Files
    $source = $yamlCompiler->pathToFile("pages/{$group}.yml");
    $destination = $cacheManager->pathToFile("yaml/pages/{$group}.php");

    // Do we have a valid Cache Entry?
    if (!$cacheManager->isValid($source, $destination)) { // NO
      return $this->compile($group, $source, $destination);
    } else { // YES
      // Use Cached Entry
      return include $destination;
    }
  }

  public function page($id) {
    $id = Strings::nullOnEmpty($id);

    if (isset($id)) {
      list($group, $page) = $this->explodeID($id);

      // Get Group Templates
      $templates = $this->group($group);
      $group = isset($templates) && key_exists($group, $templates) ? $templates[$group] : null;

      // Extract Page if it exists
      return isset($group) && key_exists($page, $group) ? $group[$page] : null;
    }

    throw "Missing or Invalid Page ID.";
  }

  /**
   * 
   * @param type $id
   * @return type
   */
  protected function explodeID($id) {
    $page = explode(':', $id, 2);

    if (isset($page)) {
      if (count($page) > 0) {
        return count($page) > 1 ?
          [Strings::nullOnEmpty($page[0]), Strings::nullOnEmpty($page[1])] :
          [Strings::nullOnEmpty($page[0]), 'default'];
      }
    }

    return [null, null];
  }

  protected function compile($group, $source, $destination) {
    // Initialization
    $di = $this->getDI();
    $cacheManager = $di->getShared('cacheManager');
    $yamlCompiler = $di->getShared('yamlCompiler');

    // Compile YAML File
    $templates = $yamlCompiler->compile($source);

    // Is the Result of the Compiler a PHP Map?
    if (Arrays::is_map($templates)) { // YES
      // Does it have the Group's Definition?
      $group_template = key_exists($group, $templates) ? $templates[$group] : null;
      if (isset($group_template)) { // YES
        // Did we Successfully Normalize the Group?
        $group_template = $this->normalize_group($group, $group_template);
        if (isset($group_template)) { // YES
          $templates[$group] = $group_template;

          // Cache Group Templates
          $cacheManager->cache($destination, $templates, true);
        } else {
          unset($templates[$group]);
        }
      }
      // ELSE: No
      return count($templates) ? $templates : null;
    }

    // Do we have a Valid Template Group?
    return null;
  }

  protected function normalize_group($group, $templates) {
    foreach ($templates as $page => $template) {
      // Are we dealing with a PageTemplate, or a Just Simply a Template?
      if (Arrays::get('template', $template)) { // NORMAL TEMPLATE
        $template = $this->normalize_template($group, $page, $templates);
      } else { // PAGE TEMPLATE
        $template = $this->normalize_page($group, $page, $templates);
      }

      $templates[$page] = $template;
    }

    return count($templates) ? $templates : null;
  }

  protected function normalize_page($group, $id, &$templates) {
    // 1st: Do Standard Template Normalization
    $page = $this->normalize_template($group, $id, $templates);

    // 2nd: Expand Requirements
    $page = $this->expand_page($page);

    // 3rd: Cleanup
    // Condense Includes to just File Pointers (Everything Else has been Merged)
    $includes = Arrays::get('includes', $page);
    if (isset($includes)) {
      foreach ($includes as $include => $settings) {
        $includes[$include] = $settings['file'];
      }
      $page = $this->set_or_remove('includes', $page, $includes);
    }

    // Remove Libraries Definitions (if any)
    if (key_exists('libraries', $page)) {
      unset($page['libraries']);
    }

    return $page;
  }

  protected function normalize_template($group, $id, &$templates) {
    $template = $templates[$id];

    // Are we in Debug Mode ?
    $debug = null;
    if ($this->getDI()->get('debug') && key_exists('debug', $template)) { // YES
      // Get the Templates Debug Feature?
      $debug = $template['debug'];
      unset($template['debug']);

      // Normalize Debug Properties
      $debug = $this->normalize_properties($debug);
    }

    // 1st: Normalize Template (Why? Because an Inherited Template should already come normalized)
    $template = $this->normalize_properties($template);

    // 2nd: [OPTIONAL] Merge Debug Parameters
    if (isset($debug)) {
      // TODO Implement this correctly (i.e. we need a special recursive merge function).
      $template = isset($template) ? array_merge($template, $debug) : $debug;
    }

    // 3rd: Expand Inheritance
    $inherit = Strings::nullOnEmpty(Arrays::get('inherit', $template));
    if (isset($inherit)) {
      list($inherit_group, $inherit_page) = $this->explodeID($inherit);

      if ($inherit_group === $group) {
        $inherit_template = $templates[$inherit_page];
        if (key_exists('inherit', $inherit_template)) {
          $templates[$inherit_page] = $inherit_template = $this->normalize_template($group, $inherit_page, $templates);
        }
      } else {
        $inherit_template = $this->page($inherit);
      }
      $template = Arrays::mixin($inherit_template, $template);
      unset($template['inherit']);
    }

    return count($template) ? $template : null;
  }

  protected function normalize_properties($source) {
    // Normalize Page/Template Properties
    $source = $this->normalize_libraries($source);
    $source = $this->normalize_includes($source);
    $source = $this->normalize_css($source);
    $source = $this->normalize_js($source);
    $source = $this->flatten_requirements($source);
    return count($source) ? $source : null;
  }

  protected function normalize_libraries($template) {
    if (isset($template)) {
      $libraries = Arrays::get('libraries', $template);
      if (isset($libraries)) {
        foreach ($libraries as $library => $settings) {
          $settings = $this->normalize_library($settings);
          if (isset($settings)) {
            $libraries[$library] = $settings;
          } else {
            unset($libraries[$library]);
          }
        }
        $template = $this->set_or_remove('libraries', $template, $libraries);
      }
    }

    return isset($template) && count($template) ? $template : null;
  }

  protected function normalize_library($library) {
    // Are we in Debug Mode ?
    $debug = null;
    if ($this->getDI()->getShared('debug') && key_exists('debug', $library)) { // YES
      // Get the Debug Overlay
      $debug = $library['debug'];
      unset($library['debug']);

      // Normalize Debug Properties
      $debug = $this->_normalize_library($debug);
    }

    // Normalize the Library
    $library = $this->_normalize_library($library);

    // Do we have a Debug Overlay?
    if (isset($debug)) { // YES
      $library = isset($library) ? array_merge($library, $debug) : $debug;
    }

    return $library;
  }

  protected function _normalize_library($library) {
    // Normalize 'css'
    $library = $this->normalize_string_array('css', $library);
    if (key_exists('css', $library)) {
      $library['css'] = ['files' => $library['css']];
    }

    // Normalize 'js'
    $js = Arrays::get('js', $library);
    if (isset($js)) {
      if (is_string($js)) {
        $library = $this->normalize_string_array('js', $library);
        if (key_exists('js', $library)) {
          $library['js'] = ['files' => $library['js']];
        }
      } else {
        $library = $this->normalize_js($library);
      }
    } else if (key_exists('js', $library)) {
      unset($library['js']);
    }

    // Normalize 'required'
    $library = $this->normalize_string_array('required', $library);

    return $library;
  }

  protected function normalize_includes($template) {
    if (isset($template)) {
      $includes = Arrays::get('includes', $template);
      if (isset($includes)) {
        // Are we in Debug Mode ?
        $debug = null;
        if ($this->getDI()->getShared('debug') && key_exists('debug', $includes)) { // YES
          // Get the Debug Overlay
          $debug = $includes['debug'];
          unset($includes['debug']);

          // Normalize Debug Properties
          $debug = $this->normalize_include($debug);
        }

        foreach ($includes as $include => $settings) {
          $settings = $this->normalize_include($settings);
          if (isset($settings)) {
            $includes[$include] = $settings;
          } else {
            unset($includes[$include]);
          }
        }

        // Do we have a Debug Overlay?
        if (isset($debug)) { // YES
          $includes = isset($includes) ? array_merge($includes, $debug) : $debug;
        }
      }
    }
    $template = $this->set_or_remove('includes', $template, $includes);

    return isset($template) && count($template) ? $template : null;
  }

  protected function normalize_include($include) {
    if (is_string($include)) {
      $include = Strings::nullOnEmpty($include);
      if (isset($include)) {
        $include = ['file' => $include];
      }
    } else if (Arrays::is_map($include)) {
      $include = $this->normalize_css($include);
      $include = $this->normalize_js($include);
      $include = $this->normalize_string_array('required', $include);
      if (key_exists('file', $include)) {
        $include = $this->set_or_remove('file', $include, Strings::nullOnEmpty($include['file']));
      }

      $include = count($include) ? $include : null;
    }

    return $include;
  }

  protected function normalize_css($settings) {
    $css = Arrays::get('css', $settings);
    if (isset($css)) {
      if (is_string($css)) {
        $file = Strings::nullOnEmpty($css);
        if (isset($file)) {
          $css = ['files' => [$file]];
        }
      } else if (is_array($css)) {
        if (!Arrays::is_map($css)) {
          $css = ['files' => is_array($css) ? $css : [$css]];
        } else {
          // Normalize 'includes' settings (if any)            
          $css = $this->normalize_string_array('files', $css);

          // Normalize 'styles'
          $css = $this->normalize_styles($css);
        }
      } else {
        $css = null;
      }
    }
    $settings = $this->set_or_remove('css', $settings, $css);

    return $settings;
  }

  protected function normalize_js($settings) {
    $js = Arrays::get('js', $settings);
    if (isset($js)) {
      if (is_string($js)) {
        $file = Strings::nullOnEmpty($js);
        if (isset($file)) {
          $js = ['files' => [$file]];
        }
      } else if (is_array($js)) {
        if (!Arrays::is_map($js)) {
          $js = ['files' => is_array($js) ? $js : [$js]];
        } else {
          // Normalize 'includes' settings (if any)            
          $js = $this->normalize_string_array('files', $js);

          // Normalize 'styles'
          $js = $this->normalize_scripts($js);
        }
      } else {
        $js = null;
      }
    }
    $settings = $this->set_or_remove('js', $settings, $js);

    return $settings;
  }

  protected function normalize_styles($settings) {
    $styles = Arrays::get('styles', $settings);
    if (isset($styles)) {
      if (Arrays::is_map($styles)) {
        foreach ($styles as $htmltag => $map) {
          $map = $this->normalize_map_string($map);
          if (isset($map)) {
            $styles[$htmltag] = $map;
          } else {
            unset($styles[$htmltag]);
          }
        }
      }
    }
    $settings = $this->set_or_remove('styles', $settings, $styles);

    return $settings;
  }

  protected function normalize_scripts($settings) {
    $scripts = Arrays::get('scripts', $settings);
    if (isset($scripts)) {
      $scripts = $this->normalize_map_string_array($scripts);
    }
    $settings = $this->set_or_remove('scripts', $settings, $scripts);

    return $settings;
  }

  protected function flatten_requirements($settings) {
    $requirements = Arrays::get('requirements', $settings);
    if (isset($requirements)) {
      if (Arrays::is_map($requirements)) {
        $requirements = $this->flatten_array($requirements);
      } else {
        $requirements = null;
      }
    }
    $settings = $this->set_or_remove('requirements', $settings, $requirements);

    return $settings;
  }

  protected function flatten_array($array, $seperator = ':') {
    $flattened = [];
    foreach ($array as $key => $value) {
      if (Arrays::is_map($value)) {
        $new_value = $this->flatten_array($value, $seperator);
        foreach ($new_value as $child_key => $child_value) {
          $flattened[$key . $seperator . $child_key] = $child_value;
        }
      } else {
        $flattened[$key] = $value;
      }
    }
    return $flattened;
  }

  protected function normalize_string_array($key, $settings) {
    $value = Arrays::get($key, $settings);
    if (isset($value)) { // YES
      // Is it a String Value?
      if (is_string($value)) { // YES
        // Is the value valid?
        $value = Strings::nullOnEmpty($value);
        if (isset($value)) { // YES: Convert it to an Array
          $value = [$value];
        }
      } else if (!is_array($value)) {
        $value = null;
      }
    }
    $settings = $this->set_or_remove($key, $settings, $value);

    return $settings;
  }

  protected function normalize_map_string($map) {
    foreach ($map as $key => $value) {
      $value = Strings::nullOnEmpty($value);
      if (isset($value)) {
        $map[$key] = $value;
      } else {
        unset($map[$key]);
      }
    }

    return count($map) ? $map : null;
  }

  protected function normalize_map_string_array($map) {
    $keys = array_keys($map);
    for ($i = 0; $i < count($keys); $i++) {
      $map = $this->normalize_string_array($keys[$i], $map);
    }

    return count($map) ? $map : null;
  }

  protected function expand_page($page) {
    // Get Page Requirements
    $page = $this->normalize_string_array('required', $page);
    $required = key_exists('required', $page) ? $page['required'] : [];

    // Merge in Include Requirements
    $required = array_merge($this->extract_include_required($page), $required);

    // Merge in Library Requiredments
    $required = array_merge($this->extract_libraries_required($required, $page), $required);

    // Extract Unique List of Requirements - Maintaining the Order
    if (count($required)) {
      $unique = [];
      $found = [];
      $library = null;
      for ($i = 0; $i < count($required); $i++) {
        $library = $required[$i];
        if (!key_exists($library, $found)) {
          $unique[] = $library;
          $found[$library] = true;
        }
      }

      $required = $unique;
    }

    // Extract Unique Keys maintaining order
    $page = $this->merge_includes($page);
    $page = $this->merge_libaries($page, array_reverse($required));

    // Do we have Requirements for the Page?
    if (key_exists('required', $page)) { // YES: Page Fully Expanded - Remove them
      unset($page['required']);
    }

    return $page;
  }

  protected function extract_include_required($template) {
    $required = [];

    // Merge in Include Requirements
    $includes = Arrays::get('includes', $template);
    if (isset($includes)) {
      foreach ($includes as $include) {
        if (key_exists('required', $include)) {
          $required = array_merge($required, $include['required']);
        }
      }
    }

    return $required;
  }

  protected function extract_libraries_required($libraries, $template) {
    $required = [];

    for ($i = 0; $i < count($libraries); $i++) {
      $required = array_merge($this->extract_library_required($libraries[$i], $template), $required);
    }

    return $required;
  }

  protected function extract_library_required($library, $template) {
    $required = Arrays::get("libraries/{$library}/required", $template, []);
    if (count($required)) {
      $required = array_merge($this->extract_libraries_required($required, $template), $required);
    }

    return $required;
  }

  protected function merge_libaries($page, $keys) {
    $css = Arrays::get('css', $page, []);
    $js = Arrays::get('js', $page, []);
    $libraries = Arrays::get('libraries', $page);
    if (isset($libraries)) {
      for ($i = 0; $i < count($keys); $i++) {
        if (key_exists($keys[$i], $libraries)) {
          $library = $libraries[$keys[$i]];

          if (key_exists('css', $library)) {
            $css = array_merge_recursive($library['css'], $css);
          }

          if (key_exists('js', $library)) {
            $js = array_merge_recursive($library['js'], $js);
          }
        }
      }
    }

    // Set or Cleanup 'css' and 'js'
    $page = $this->set_or_remove('css', $page, $css);
    $page = $this->set_or_remove('js', $page, $js);
    return $page;
  }

  protected function merge_includes($page) {
    $css = Arrays::get('css', $page, []);
    $js = Arrays::get('js', $page, []);
    $includes = Arrays::get('includes', $page);
    if (isset($includes)) {
      foreach ($includes as $include => $settings) {
        if (key_exists('css', $settings)) {
          $css = array_merge_recursive($settings['css'], $css);
        }

        if (key_exists('js', $settings)) {
          $js = array_merge_recursive($settings['js'], $js);
        }
      }
    }

    // Set or Cleanup 'css' and 'js'
    $page = $this->set_or_remove('css', $page, $css);
    $page = $this->set_or_remove('js', $page, $js);
    return $page;
  }

  protected function set_or_remove($key, $from, $value) {
    if (isset($value) && (!is_array($value) || count($value))) {
      $from[$key] = $value;
    } else if (key_exists($key, $from)) {
      unset($from[$key]);
    }

    return $from;
  }

}
