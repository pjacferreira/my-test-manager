<?php

/* Test Center - Compliance Testing Application (Services Shared Library)
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

/**
 * Shared Views Manager
 */
$di->setShared('sharedViews', function () use ($config) {
  $view = new \Phalcon\Mvc\View\Simple();
  $view->setViewsDir($config->application->sharedViewsDir);

  $view->registerEngines(array(
      ".phtml" => function($view, $di) use ($config) {
$volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
$volt->setOptions(array(
    "compiledPath" => $config->application->sharedCacheDir
));

return $volt;
}
  ));
  return $view;
});

/**
 * PHALCON Session Helper
 */
$di->setShared('session', function() {
  $session = new \Phalcon\Session\Adapter\Files();
  $session->start();
  return $session;
});

/**
 * SHARED Session Manager
 */
$di->setShared('sessionManager', array(
    'className' => 'shared\session\Manager'
));
