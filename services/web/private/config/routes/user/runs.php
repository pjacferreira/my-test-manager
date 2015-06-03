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
use controllers\user\RunsController as Runs;

/*
 * Run Controller
 */
$controller = Runs::getInstance();

/*
 * Run Actions
 */
$prefix = '/run';

$app->map($prefix . '/create/{name}/{set:[0-9]+}', array($controller, 'create'));
$app->map($prefix . '/create/{name}/{set:[0-9]+}/{folder:[0-9]+}', array($controller, 'create'));

$prefix = '/run/{id:[0-9]+}';

$app->map($prefix . '/read', array($controller, 'read'));
$app->map($prefix . '/update', array($controller, 'update'));
$app->map($prefix . '/delete', array($controller, 'delete'));
$app->map($prefix . '/tests/list', array($controller, 'listTests'));
$app->map($prefix . '/tests/count', array($controller, 'countTests'));

/*
 * List All Sets on Just Sets in a Particular Folder
 */
$prefix = '/runs';

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
