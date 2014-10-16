<?php

/* Test Center - Compliance Testing Application (Metadata Service)
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
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Create Routes for Metadata Controller
 */

// Create a Collection of Routes
$routes = new MicroCollection();

// Associate a Controller with Routes
$routes->setHandler(new MetadataController());

// Associate Routes with Controller Functions
$routes->map('/field/{id}', 'field');
$routes->map('/fields[/]?{list}', 'fields');
$routes->map('/form/{id}', 'form');
$routes->map('/forms[/]?{list}', 'forms');
$routes->map('/service/{id}', 'service');
$routes->map('/services[/]?{list}', 'services');
$routes->map('/dataset/{id}', 'dataset');
$routes->map('/datasets[/]?{list}', 'datasets');

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Route Collection to Application
$app->mount($routes);
