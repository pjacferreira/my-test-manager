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
  global $config, $di, $app;

  // Include the Normal Configuration File
  $result = include __DIR__ . "/../private/config/{$file}.php";

  // If a Development File Exists - Include it as well
  if(file_exists(__DIR__ . "/../private/config/{$file}_dev.php")) {
    $result_dev = include __DIR__ . "/../private/config/{$file}_dev.php";
  }

  return isset($result_dev) ? $result_dev : $result;
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

  /**
   * Handle the request
   */
  $app->handle();
} catch (Phalcon\Exception $e) {
  // Display Exceptions in PHP Console - Instead of Web Page
  PC::debug($e, 'exception');
//  echo $e->getMessage();
} catch (PDOException $e) {
  // Display Exceptions in PHP Console - Instead of Web Page
  PC::debug($e, 'exception');
//  echo $e->getMessage();
}
