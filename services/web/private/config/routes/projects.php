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
 * Create Routes for Project Controller
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new ProjectsController());

// Base Route Prefix
$routes->setPrefix('/project');

// Associate Routes with Controller Functions
$routes->map('/create/{name}', 'create');
$routes->map('/read/{name}', 'readByName');
$routes->map('/update/{name}', 'updateByName');
$routes->map('/delete/{name}', 'deleteByName');

// Routes WITH LIMITS (Have to be Last)
$routes->map('/create/{id:[0-9]+}/{name}', 'createInOrg');
$routes->map('/read/{id:[0-9]+}', 'read');
$routes->map('/update/{id:[0-9]+}', 'update');
$routes->map('/delete/{id:[0-9]+}', 'delete');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

/*
 * Project<-->User Management
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new UserProjectController());

// Base Route Prefix
$routes->setPrefix('/project/users');

// Associate Routes with Controller Functions
$routes->map('/list[/]?{org_id}', 'listUsers');
$routes->map('/count[/]?{org_id}', 'countUsers');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

/*
 * Create Routes for Multi Project Actions
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new ProjectsController());

// Base Route Prefix
$routes->setPrefix('/projects');

// Associate Routes with Controller Functions
$routes->map('/list', 'listProjects');
$routes->map('/count', 'countProjects');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);
