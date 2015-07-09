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
use Phalcon\DI\FactoryDefault;

//use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

$di = new FactoryDefault();

/**
 * Create View Services
 */
$di['renderer'] = function () use ($config) {
  $renderer = new \api\templates\PageRenderer($config->application->templatesDir);
  return $renderer;
};

/**
 * Create View Services
 */
$di['view'] = function () use ($config) {
  $view = new \Phalcon\Mvc\View\Simple();
  $view->setViewsDir($config->application->viewsDir);

  return $view;
};

/**
 * Shared Flag : Are we in degug mode
 */
$di->setShared('debug', function () use ($config) {
  return key_exists('debug', $config->application) ? $config->application->debug : false;
});

/**
 * Shared Service : The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
  $url = new api\services\SiteURL(
    $config->extract_string('application.site.js'),
    $config->extract_string('application.site.css'), 
    $config->extract_string('application.site.assets')
  );

  // Try to Build a Base URI
  $base = $config->extract_string('application.site.url');
  $offset = $config->extract_string('application.site.offset');
  if (isset($base) && isset($offset)) {
    $base = ltrim($base . $offset);
  }

  // Do we have a base URI?
  if (isset($base) && count($base)) { // YES
    $url->setBaseUri($base);
  }

  return $url;
});

/**
 * PHALCON Session Helper
 */
$di->setShared('session', function() {
  $session = new \Phalcon\Session\Adapter\Files();
  return $session;
});

/**
 * Shared Service : Cache Manager
 */
$di->setShared('cacheManager', function () use ($config) {
  // Creates a Shared Instance of the Cache Manager
  return new api\cache\CacheManager($config->application->cacheDir);
});

/**
 * Shared Service : YAML Compiler
 */
$di->setShared('yamlCompiler', function () use ($config) {
  // Creates a Shared Instance of the Cache Manager
  return new api\yaml\CompilerYAML($config->application->yamlDir);
});

/**
 * Shared Service : Page Template Manager
 */
$di->setShared('pageManager', function () {
  // Creates a Shared Instance of the Page Manager
  return new api\templates\PageTemplates();
});

# Include Shared Configuration (if it exists)
include PATH_SHARED . '/config/services.php';

/*
 *  MUST BE THE LAST LINE IN THE FILE
 */
return $di;
