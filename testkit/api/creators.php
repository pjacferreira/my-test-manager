<?php

/* Test Center - Test Kit
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
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

namespace api;

use api\renderers\RenderMessage;

// TODO Create an Executor Function or Test Class to replace the type parameter

function createMark($group, $priority, $message = null) {
  $message = string_onEmpty($message);

  return Test::getMark($sGroup, $nPriority)->setRenderer(new RenderMessage(isset($message) ? $message : 'MARKER'));
}

function createTest($group, $priority, $service, $validator_code = 200,
                    $renderer = null) {
  assert('isset($group) && is_string($group)');
  assert('isset($priority) && is_integer($priority)');
  assert('isset($service) && is_string($service)');
  assert('!isset($parameters) || is_array($parameters)');
  assert('isset($validator_code)');

  // Trim the Service Request
  $service = trim($service);
  assert('count($service)');

  // Create Service Parameters String
  $params = isset($parameters) ? implode('/',
                                         array_map(function($element) {
          return trim($element);
        }, $parameters)) : null;

  // Build Service URL
  if (isset($params)) {
    $url = ($service[count($service) - 1] !== '/') ?
      "{$service}/{$params}" :
      "{$service}{$params}";
  } else {
    if ($service[strlen($service) - 1] === '/') {
      $url = substr($service, 0, strlen($service) - 1);
    } else {
      $url = $service;
    }
  }
  assert('strlen($url) > 2');

  return array(
    'type' => 2,
    'group' => strtolower(trim($group)),
    'priority' => $priority,
    'dependencies' => isset($dependencies) ? array_map(
        function($dependency) {
          $dependency['group'] = strtolower(trim($dependency['group']));
          return $dependency;
        }, $dependencies) : null,
    'url' => str_replace('%2F', '/', urlencode($url)),
    'validator' => $validator_code,
    'renderer' => isset($renderer) ? $renderer : 'renderNULL'
  );
}

function createJSONServiceTest($group, $priority, $dependencies, $service,
                               $parameters = null, $validator = 'jsonService',
                               $renderer = 'renderJSONService') {
  return createTest($group, $priority, $dependencies, $service, $parameters,
                    $validator, $renderer);
}

?>
