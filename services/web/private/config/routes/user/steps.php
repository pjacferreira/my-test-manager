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
 * USER MODE: Project Services
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
use Phalcon\Mvc\Micro\Collection as MicroCollection;

/*
 * Test Step Services
 */
$controller = new controllers\user\StepsController();

/*
 * Tests Actions
 */
$prefix = '/step';

$app->map($prefix . '/create/{test:[0-9]+}/{title}', array($controller, 'create'));
$app->map($prefix . '/create/bol/{test:[0-9]+}/{title}', array($controller, 'createBOL'));
$app->map($prefix . '/create/eol/{test:[0-9]+}/{title}', array($controller, 'createEOL'));
$app->map($prefix . '/create/after/{test:[0-9]+}/{sequence:[0-9]+}/{title}', array($controller, 'createAFTER'));
$app->map($prefix . '/read/{test:[0-9]+}/{sequence:[0-9]+}', array($controller, 'read'));
$app->map($prefix . '/update/{test:[0-9]+}/{sequence:[0-9]+}', array($controller, 'update'));
$app->map($prefix . '/delete/{test:[0-9]+}/{sequence:[0-9]+}', array($controller, 'delete'));
$app->map($prefix . '/move/{test:[0-9]+}/{sequence:[0-9]+}/{position:[0-9]+}', array($controller, 'moveDown'));
$app->map($prefix . '/move/up/{test:[0-9]+}/{sequence:[0-9]+}', array($controller, 'moveUp'));
$app->map($prefix . '/move/up/{test:[0-9]+}/{sequence:[0-9]+}/{before:[0-9]+}', array($controller, 'moveUp'));
$app->map($prefix . '/move/down/{test:[0-9]+}/{sequence:[0-9]+}', array($controller, 'moveDown'));
$app->map($prefix . '/move/down/{test:[0-9]+}/{sequence:[0-9]+}/{after:[0-9]+}', array($controller, 'moveDown'));


/*
 * List All Tests on Just Tests in a Particular Folder
 */
$prefix = '/steps';

$app->map($prefix . '/list/{test:[0-9]+}', array($controller, 'listInTest'));
$app->map($prefix . '/count/{test:[0-9]+}', array($controller, 'countInTest'));
$app->map($prefix . '/renumber/{test:[0-9]+}', array($controller, 'renumber'));
