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
 * USER MODE: Set Services
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Set Controller
 */
$controller = new controllers\user\SetsController();

/*
 * Sets Actions
 */
$prefix = '/set';

$app->map($prefix . '/create/{name}', array($controller, 'create'));
$app->map($prefix . '/create/{name}/{folder:[0-9]+}', array($controller, 'create'));
$app->map($prefix . '/read/{id:[0-9]+}', array($controller, 'read'));
$app->map($prefix . '/update/{id:[0-9]+}', array($controller, 'update'));
$app->map($prefix . '/delete/{id:[0-9]+}', array($controller, 'delete'));

/*
 * List All Sets on Just Sets in a Particular Folder
 */
$prefix = '/sets';

$app->map($prefix . '/list', array($controller, 'listInProject'));
$app->map($prefix . '/list/{filter}', array($controller, 'listInProject'));
$app->map($prefix . '/list/{filter}/{sort}', array($controller, 'listInProject'));
$app->map($prefix . '/list/{folder:[0-9]+}', array($controller, 'listInFolder'));
$app->map($prefix . '/count', array($controller, 'countInProject'));
$app->map($prefix . '/count/{filter}', array($controller, 'countInProject'));
$app->map($prefix . '/list/{folder:[0-9]+}/{filter}', array($controller, 'listInFolder'));
$app->map($prefix . '/list/{folder:[0-9]+}/{filter}/{sort}', array($controller, 'listInFolder'));
$app->map($prefix . '/count/{folder:[0-9]+}', array($controller, 'countInFolder'));
$app->map($prefix . '/count/{folder:[0-9]+}/{filter}', array($controller, 'countInFolder'));
