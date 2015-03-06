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
/* FOR DEBUG PURPOSES
 * We can listen in on the AutoLoader's Decisions
// Create an Events Manager
$eventsManager = new \Phalcon\Events\Manager();
 
//Listen all the loader events
$eventsManager->attach('loader', function($event, $loader) {
  if ($event->getType() == 'beforeCheckPath') {
    PC::debug($loader->getCheckedPath(), 'loader-beforeCheckClass');
  } else if ($event->getType() == 'pathFound') {
    PC::debug($loader->getCheckedPath(), 'loader-pathFound');
  } else if ($event->getType() == 'afterCheckClass') {
    PC::debug($loader->getCheckedPath(), 'loader-afterCheckClass');
  }
});

$loader->setEventsManager($eventsManager);
*/

// Ready Loader
$loader->register();
