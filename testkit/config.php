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

$DOCROOT = 'http://10.193.0.201/testcenter/services/web';
$TESTSDIR = 'tests';
//$SIMULATE = true;               // SIMULATE RUN (i.e. list test execution order)
$STOP_ON_FAIL = true;
$DEBUG = false;
//$XDEBUG = 'netbeans-xdebug';
$USE_GRAPHVIZ = true;

$BREAK_ON_TEST= 'projects:900';  // Break On a Specific Test (allows us to create a specific state, from which we can, manually, continue testing)

/* TODO Consider the Following Scenarios
 * 1. Break on Exception (DONE)
 * 2. Stop/Finish Processing File in the case of some failed tests (i.e. complete processing the file, even though some
 *    tests have failed.
 * 3. (?) Break after a certain number of tests have failed.
 */

// Basic Auto Loader for Namespaces
spl_autoload_register(
        function($className) {
  $className = str_replace("_", "\\", $className);
  $className = ltrim($className, '\\');
  $fileName = '';
  $namespace = '';
  if ($lastNsPos = strripos($className, '\\')) {
    $namespace = substr($className, 0, $lastNsPos);
    $className = substr($className, $lastNsPos + 1);
    $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

  require_once $fileName;
}
);
?>
