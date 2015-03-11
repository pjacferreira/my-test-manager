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
 * USER MODE: Organization Services
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */

/*
 * Organization Services
 */
$controller = new controllers\user\OrganizationsController();

$prefix = '/org';
/*
 * RETRIEVE the PROFILE for any ORGANIZATION the SESSION USER has Access To
 */
$app->map($prefix . '/profile/{name}', array($controller, 'readByName'));
$app->map($prefix . '/profile[/]{id:[0-9]+}', array($controller, 'read'));

$prefix = '/orgs';
/*
 * LIST and COUNT all the Organizations the SESSION USER has Access To
 */
$controller = new controllers\user\UserOrganizationController();

$app->map($prefix . '/list', array($controller, 'listOrganizations'));
$app->map($prefix . '/count', array($controller, 'countOrganizations'));

/*
 * Organization<-->Project Services
 */
$controller = new controllers\user\UserProjectController();

$prefix = '/org/projects';
/*
 * LIST and COUNT all the Projects the SESSION USER has Access To:
 * 1. In the Current SESSION ORGANIZATION or,
 * 2. In another Organization the SESSION USER has Access to
 */
$app->map($prefix . '/list[/]?{org_id}', array($controller, 'listProjects'));
$app->map($prefix . '/count[/]?{org_id}', array($controller, 'countProjects'));
