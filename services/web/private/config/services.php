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
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

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
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
  $adapter = new DbAdapter(array(
      "host" => $config->database->host,
      "username" => $config->database->username,
      "password" => $config->database->password,
      "dbname" => $config->database->dbname,
  ));

  // The Options are required or PDO Converts Fields Fetched as Strings
  $adapter->getInternalHandler()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
  $adapter->getInternalHandler()->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);
  
  return $adapter;
};

/**
 * PHALCON Metadata Cache System
 */
$di['metadata'] = function() use ($config) {
  // Instantiate a meta-data adapter
  $metaData = new \Phalcon\Mvc\Model\Metadata\Files(array(
      'metaDataDir' => $config->application->cacheDir
  ));

  return $metaData;
};

/**
 * Included Shared Services
 */
include __DIR__ . '/../../../shared/config/services.php';
