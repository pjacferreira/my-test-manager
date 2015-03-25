<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
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
/*
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
// PHP Console Library to be used with PHP Console Chromium Extension
require_once 'phar://' . __DIR__ . '/../../shared/PhpConsole.phar';
if (PhpConsole\Connector::getInstance()->isActiveClient()) {
  // if you're calling PC::debug() or any other PC class methods in your code, so PhpConsole\Helper::register() must be called anyway
  PhpConsole\Helper::register();
}

error_reporting(E_ALL);

// Define Shared Globals (to be used in include files)
$config = $di = $app = NULL;

function include_with_dev($file) {
  global $config, $di, $app, $router;

  // Include the Normal Configuration File
  $result = include __DIR__ . "/../private/config/{$file}.php";

  // If a Development File Exists - Include it as well
  file_exists(__DIR__ . "/../private/config/{$file}_dev.php") && include __DIR__ . "/../private/config/{$file}_dev.php";

  return $result;
}

try {

  /**
   * Read the configuration
   */
  $config = include_with_dev('config');

  /**
   * Include Services
   */
  $di = include_with_dev('services');

  /**
   * Include Autoloader
   */
  include_with_dev('loader');

  /**
   * Starting the application
   */
  $app = new \Phalcon\Mvc\Micro();

  /**
   * Assign service locator to the application
   */
  $app->setDi($di);

  /**
   * Incude Application (and Default Routes)
   */
  include __DIR__ . '/../private/app.php';

  /*
   * Include Specific Application Specific Routes
   */
  include_with_dev('routes');

  /* TEST ROUTES
    $router = $app->getRouter();
    $router->add('/test/create/([^/]*)(/(\d+))?', [
    'namespace' => 'controllers\user',
    'controller' => 'TestsController',
    'action' => 'create',
    'name' => 1,
    'folder' => 3
    ]);
    $router->handle('/test/create/test-1.3/4');
    if ($router->wasMatched()) {
    $handler = $app->getHandlers();
    $route = $router->getMatchedRoute();
    echo 'Namespace: '. $router->getNamespaceName(). '<br>';
    echo 'Controller: '. $router->getControllerName(). '<br>';
    echo 'Action: '. $router->getActionName(). '<br>';
    $params = var_export($router->getParams(), true);
    echo 'Params: '. $params. '<br>';
    } else {
    echo 'The route wasn\'t matched by any route<br>';
    }
    echo '<br>';
    /* */

  /*
    $router = $app->getRouter();
    $router->add('/test/create/([^/]*)(/(\d+))?', [
    'controller' => new \controllers\user\TestsController,
    'action' => 'create',
    'name' => 1,
    'folder' => 3
    ]);
   */

  /**
   * Update Route Handlers
   */
  // $app->updateRoutes();

  /**
   * Handle the request
   */
  $app->handle();
} catch (\Exception $e) {
  // Display Exceptions in PHP Console - Instead of Web Page
  PC::debug($e, 'exception');
//  echo $e->getMessage();
}

/* TODO
 * 1. Fix Annoying Warning in Micro::handle due to the adding the ability to 
 * update the application route handlers from router (api\application\Application:updateRoutes)
 * and adding the following route:
 *   $router = $app->getRouter();
 *   $router->add('/test/create/([^/]*)(/(\d+))?', [
 *     'controller' => new \controllers\user\TestsController,
 *     'action' => 'create',
 *     'name' => 1,
 *     'folder' => 3
 *   ]);
 *
 * 
 * [Tue Mar 24 20:12:29.177005 2015] [:error] [pid 822] [client 10.193.0.1:60524] PHP Warning:  Illegal offset type in /var/www/html/services/public/dev.php on line 130
 * [Tue Mar 24 20:12:29.177088 2015] [:error] [pid 822] [client 10.193.0.1:60524] PHP Stack trace:
 * [Tue Mar 24 20:12:29.177107 2015] [:error] [pid 822] [client 10.193.0.1:60524] PHP   1. {main}() /var/www/html/services/public/dev.php:0
 * [Tue Mar 24 20:12:29.177116 2015] [:error] [pid 822] [client 10.193.0.1:60524] PHP   2. Phalcon\\Mvc\\Micro->handle() /var/www/html/services/public/dev.php:130
 * [Tue Mar 24 20:12:29.177123 2015] [:error] [pid 822] [client 10.193.0.1:60524] PHP   3. Phalcon\\Mvc\\Router->handle(*uninitialized*) /var/www/html/services/public/dev.php:130
 * 
 * PROBLEM SOLVED through the use of multiple routes
 * /create/{name}
 * /create/{name}/{folder:[0-9]+}
 * 
 * Which works within the current framework
 */