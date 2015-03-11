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
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Create Routes for Single User Actions
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new controllers\admin\UsersController());

// Base Route Prefix
$routes->setPrefix('/admin/user');

// Associate Routes with Controller Functions
$routes->map('/create/{name}', 'create');
$routes->map('/read/{name}', 'readByName');
$routes->map('/update/{name}', 'updateByName');
$routes->map('/delete/{name}', 'deleteByName');

// Routes WITH LIMITS (Have to be Last)
$routes->map('/read/{id:[0-9]+}', 'read');
$routes->map('/update/{id:[0-9]+}', 'update');
$routes->map('/delete/{id:[0-9]+}', 'delete');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

/*
 * User<-->Organization Management
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new controllers\admin\UserOrganizationController());

// Base Route Prefix
$routes->setPrefix('/admin/user/org/permissions');

// Associate Routes with Controller Functions
$routes->map('/set/{user_id}/{org_id}/{permissions}', 'link');
$routes->map('/get/{user_id}/{org_id}', 'get');
$routes->map('/clear/{user_id}/{org_id}', 'unlink');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new controllers\admin\UserOrganizationController());

// Base Route Prefix
$routes->setPrefix('/admin/user/orgs');

// Associate Routes with Controller Functions
$routes->map('/list[/]?{user_id}', 'listOrganizations');
$routes->map('/count[/]?{user_id}', 'countOrganizations');
$routes->map('/permissions/list[/]?{user_id}', 'listOrganizationPermissions');
$routes->map('/permissions/count[/]?{user_id}', 'countOrganizations');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

/*
 * User<-->Project Management
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new controllers\admin\UserProjectController());

// Base Route Prefix
$routes->setPrefix('/admin/user/project/permissions');

// Associate Routes with Controller Functions
$routes->map('/set/{user_id}/{project_id}/{permissions}', 'link');
$routes->map('/get/{user_id}/{project_id}', 'get');
$routes->map('/clear/{user_id}/{project_id}', 'unlink');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new controllers\admin\UserProjectController());

// Base Route Prefix
$routes->setPrefix('/admin/user/projects');

// Associate Routes with Controller Functions
$routes->map('/list[/]?{user_id}', 'listProjects');
$routes->map('/count[/]?{user_id}', 'countProjects');
$routes->map('/permissions/list[/]?{user_id}', 'listProjectsPermissions');
$routes->map('/permissions/count[/]?{user_id}', 'countProjects');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);

/*
 * Create Routes for Multi User Actions
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new controllers\admin\UsersController());

// Base Route Prefix
$routes->setPrefix('/admin/users');

// Associate Routes with Controller Functions
$routes->map('/list', 'listUsers');
$routes->map('/count', 'countUsers');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);
