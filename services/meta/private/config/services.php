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

use Phalcon\Mvc\Url as UrlResolver;

$di = new \Phalcon\DI\FactoryDefault();

/**
 * Use Simple View to Render
 */
$di['view'] = function () use ($config) {
  $view = new \Phalcon\Mvc\View\Simple();
  $view->setViewsDir($config->application->viewsDir);

  $view->registerEngines(array(
      ".phtml" => function($view, $di) use ($config) {
$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
$volt->setOptions(array(
    "compiledPath" => $config->application->cacheDir
));

return $volt;
}
  ));
  return $view;
};

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function () use ($config) {
  $url = new UrlResolver();
  $url->setBaseUri($config->application->baseUri);

  return $url;
};

/**
 * Metadata Access Service 
 */
$di['metadata'] = function () use ($config) {
  $service = new api\services\MetadataCache($config->application->metadataDir, $config->application->cacheDir);
  return $service;
};

/**
 * Included Shared Services
 */
include __DIR__ . '/../../../shared/config/services.php';
