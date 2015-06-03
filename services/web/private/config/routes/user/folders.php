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
 * USER MODE: Folder Services
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
use controllers\user\ContainersController as Folders;
use controllers\user\TestsController as Tests;
use controllers\user\SetsController as Sets;
use controllers\user\RunsController as Runs;

// Controller Instances
$folders = Folders::getInstance();

/*
 * Basic Folder Management
 */
$prefix = '/folder/{folder:[0-9]+}';

$app->map($prefix . '/root', array($folders, 'rootFolder'));
$app->map($prefix . '/parent', array($folders, 'parentFolder'));
$app->map($prefix . '/exists/{name}', array($folders, 'existsFolder'));
$app->map($prefix . '/rename/{name}', array($folders, 'renameFolder'));
$app->map($prefix . '/move/{to}', array($folders, 'moveFolder'));
$app->map($prefix . '/delete', array($folders, 'deleteFolder'));
$app->map($prefix . '/entry/{folder:[0-9]+}/move/{to}', array($folders, 'moveEntry'));


/*
 * Folder Entry Listing by Type
 */
$app->map($prefix . '/folders/list[/]?{filter}[/]?{sort}', array($folders, 'listFolders'));
$app->map($prefix . '/folders/count[/]?{filter}', array($folders, 'countFolders'));
$app->map($prefix . '/tests/list[/]?{filter}[/]?{sort}', array($folders, 'listTests'));
$app->map($prefix . '/tests/count[/]?{filter}', array($folders, 'countTests'));
$app->map($prefix . '/sets/list[/]?{filter}}[/]?{sort}', array($folders, 'listSets'));
$app->map($prefix . '/sets/count[/]?{filter}', array($folders, 'countSets'));
$app->map($prefix . '/runs/list[/]?{filter}[/]?{sort}', array($folders, 'listRuns'));
$app->map($prefix . '/runs/count[/]?{filter}', array($folders, 'countRuns'));

/*
 * Generic Folder Entry Listing
 */
$app->map($prefix . '/list[/]?{type}[/]?{filter}[/]?{sort}', array($folders, 'listEntries'));
$app->map($prefix . '/count[/]?{type}[/]?{filter}', array($folders, 'countEntries'));

/*
 * Folder Entry Creation
 */
$prefix = '/folder/{folder:[0-9]+}/create';

$app->map($prefix . '/folder/{name}', array($folders, 'createFolder'));
$app->map($prefix . '/test/{name}', array(Tests::getInstance(), 'create'));
$app->map($prefix . '/set/{name}', array(Sets::getInstance(), 'create'));
$app->map($prefix . '/run/{name}/{set:[0-9]+}', array(Runs::getInstance(), 'create'));
