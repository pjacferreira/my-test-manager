<?php

/**
 * Test Center - Compliance Testing Application (Web Services)
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
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
// Include Shared Configuration (if it exists)
$shared = include __DIR__ . '/../../../shared/config/config.php';

// Setup Standard Application Configuration
$config = array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '10.193.0.200',
        'username' => 'root',
        'password' => 'testcenter',
        'dbname' => 'testcenter'
    ),
    'application' => array(
        'modelsDir' => __DIR__ . '/../models/',
        'viewsDir' => __DIR__ . '/../views/',
        'controllersDir' => __DIR__ . '/../controllers/',
        'cacheDir' => __DIR__ . '/../cache/',
        'baseUri' => '/testcenter/services/web/'
    ),
    'namespaces' => array(
        'api' => __DIR__ . '/../api/'
    )
);

// Do we have shared configuration options?
if (isset($shared)) { // YES: Merge the the arrays, recursively
  $config = array_merge_recursive($config, $shared);
}

// Return Configuration Container
return new \Phalcon\Config($config);
