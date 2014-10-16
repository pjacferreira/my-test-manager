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
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Create Routes for Session Controller
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new SessionController());

// Base Route Prefix
$routes->setPrefix('/session');

// Associate Routes with Controller Functions
$routes->get('/', 'whoami');
$routes->map('/login/{name}[/]?{password}', 'login');
$routes->map('/logout', 'logout');
$routes->map('/sudo/{name}[/]?{password}', 'sudo');
$routes->map('/sudo/exit', 'sudoExit');
$routes->map('/whoami', 'whoami');

// Get and Set Organization
$routes->map('/get/org', 'getOrganization');
$routes->map('/set/org/{id}', 'setOrganization');

// Get and Set Project
$routes->map('/get/project', 'getProject');
$routes->map('/set/project/{id}', 'setProject');

// Routes WITH LIMITS (Have to be Last)
// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);
