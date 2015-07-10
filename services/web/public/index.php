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

// LOAD BASE FILE
require_once '../private/base.php';

error_reporting(E_ALL);

try {

  // Make sure that any included files know we are using PHALCON
  $FLAG_PHALCON = true;

  /**
   * Read the configuration
   */
  $config = include PATH_PRIVATE . "/config/config.php";

  /**
   * Include Autoloader
   */
  include PATH_PRIVATE . '/config/loader.php';

  // Make sure that any included files know we have the Autoloader Setup
  $FLAG_PHALCON_LOADER = true;

  /**
   * Include Services
   */
  include PATH_PRIVATE . '/config/services.php';

  /**
   * Starting the application
   */
  $app = new \Phalcon\Mvc\Micro();

  /**
   * Assign service locator to the application
   */
  $app->setDi($di);

  /**
   * Incude Application
   */
  include PATH_PRIVATE . '/app.php';

  /*
   * Include Specific Application Specific Routes
   */
  include PATH_PRIVATE . '/config/routes.php';

  /**
   * Handle the request
   */
  $app->handle();
} catch (Phalcon\Exception $e) {
  echo $e->getMessage();
} catch (PDOException $e) {
  echo $e->getMessage();
}
