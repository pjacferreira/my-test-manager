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
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

/* Create a Shared Events Manager Service for Debug Purposes */
$di->setShared('eventsManager', function () {
  // Create Events Manager
  return new \Phalcon\Events\Manager();  
});


$di['db'] = function () use ($di, $config) {  
  // Create Events Manager
  $eventsManager = $di->getShared('eventsManager');

/*  
  // Capture Queries Before they are Sent to the Database
  $eventsManager->attach('db', function($event, $connection) {
    if ($event->getType() == 'beforeQuery') {
      PC::debug($connection->getSQLStatement(), 'db-beforeQuery');
    }
  });

  // Capture Queries After they are Sent to the Database
  $eventsManager->attach('db', function($event, $connection) {
    if ($event->getType() == 'afterQuery') {
      PC::debug($connection->getSQLStatement(), 'db-afterQuery');
    }
  });
 */
  
  $adapter = new DbAdapter(array(
      "host" => $config->database->host,
      "username" => $config->database->username,
      "password" => $config->database->password,
      "dbname" => $config->database->dbname,
  ));

  // The Options are required or PDO Converts Fields Fetched as Strings
  $adapter->getInternalHandler()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
  $adapter->getInternalHandler()->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

/*  
  //Assign the eventsManager to the db adapter instance
  $adapter->setEventsManager($eventsManager);
*/  
  return $adapter;
};

/**
 * DEVELOPMENT: PHALCON Metadata Cache System is in MEMORY
 */
$di['metadata'] = function() {
  // Instantiate a meta-data adapter
  $metaData = new \Phalcon\Mvc\Model\Metadata\Memory();

  return $metaData;
};
