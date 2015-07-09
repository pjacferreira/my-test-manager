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

// LOAD BASE FILE and \common\config\Config Class
require_once __DIR__ . '/../base.php';
require_once PATH_SHARED . '/common/config/Config.php';

// Include Shared Configuration (if it exists)
$shared = include PATH_SHARED . '/config/config.php';

// Local Configuration 
$config = array(
  'database' => array(
    'adapter' => 'Mysql',
    'host' => '10.193.0.200',
    'username' => 'root',
    'password' => 'testcenter',
    'dbname' => 'testcenter'
  ),
  'application' => array(
    /* PHALCON SYSTEM PATHS */
    'viewsDir' => PATH_PRIVATE . '/views/',
    'cacheDir' => PATH_PRIVATE . '/cache/',
    /* APPPLICATION PATHS */
    'localesDir' => PATH_PRIVATE . '/locales/',
    /* APPPLICATION RESOURCES */
    'baseUri' => '/services/',
  ),
  'namespaces' => array(
    'controllers' => PATH_PRIVATE . '/controllers/',
    'models' => PATH_PRIVATE . '/models/',
    'api' => PATH_PRIVATE . '/api/'
  ),
  'properties' => array(
//        'allowed-origins' => '(.*\.)?test-to.com$'
  )
);

// Do we have shared configuration options?
if (isset($shared)) { // YES: Merge the the arrays, recursively (Local Config takes Precedence over Shared Config)
  $config = array_merge_recursive($shared, $config);
}

// Return Configuration Container
return new \common\config\Config($config);
