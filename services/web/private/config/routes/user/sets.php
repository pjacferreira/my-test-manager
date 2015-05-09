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


/*
 * Set<-->Test Controller
 */
$controller = new controllers\user\SetTestsController();

/*
 * Set<->Test Actions
 */

$prefix = '/set/{set:[0-9]+}/test/{test:[0-9]+}';

$app->map($prefix . '/add', array($controller, 'link'));
$app->map($prefix . '/add/bol', array($controller, 'linkBOL'));
$app->map($prefix . '/add/after/{sequence:[0-9]+}', array($controller, 'linkAFTER'));
$app->map($prefix . '/delete', array($controller, 'unlink'));
$app->map($prefix . '/move/up', array($controller, 'moveTestUp'));
$app->map($prefix . '/move/up/{before:[0-9]+}', array($controller, 'moveTestUp'));
$app->map($prefix . '/move/down', array($controller, 'moveTestDown'));
$app->map($prefix . '/move/down/{after:[0-9]+}', array($controller, 'moveTestDown'));

/*
 * Set<->Test Actions by Sequence
 */

$prefix = '/set/{set:[0-9]+}';

$app->map($prefix . '/delete/{sequence:[0-9]+}', array($controller, 'delete'));
$app->map($prefix . '/move/{sequence:[0-9]+}/{position:[0-9]+}', array($controller, 'moveDown'));
$app->map($prefix . '/move/up/{sequence:[0-9]+}', array($controller, 'moveUp'));
$app->map($prefix . '/move/up/{sequence:[0-9]+}/{before:[0-9]+}', array($controller, 'moveUp'));
$app->map($prefix . '/move/down/{sequence:[0-9]+}', array($controller, 'moveDown'));
$app->map($prefix . '/move/down/{sequence:[0-9]+}/{after:[0-9]+}', array($controller, 'moveDown'));
$app->map($prefix . '/list', array($controller, 'listLinks'));
$app->map($prefix . '/count', array($controller, 'count'));
$app->map($prefix . '/renumber', array($controller, 'renumber'));

/*
 * List All Tests attached to a Set
 */
$prefix = '/set/{set:[0-9]+}/tests';

$app->map($prefix . '/list', array($controller, 'listTests'));
$app->map($prefix . '/count', array($controller, 'count'));
$app->map($prefix . '/renumber', array($controller, 'renumberTests'));
