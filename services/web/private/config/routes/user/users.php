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
 * USER MODE:  User Services
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */

$controller = new controllers\user\UsersController();
$prefix = '/user';

// Associate Routes with Controller Functions
$app->map($prefix . '/profile', array($controller, 'profile'));

/*
 * User<-->Organization Relationships
 */
$controller = new controllers\user\UserOrganizationController();
$prefix = '/user/orgs';

// Associate Routes with Controller Functions
$app->map($prefix . '/list', array($controller, 'listOrganizations'));
$app->map($prefix . '/count', array($controller, 'countOrganizations'));
$app->map($prefix . '/permissions/list', array($controller, 'listOrganizationPermissions'));
$app->map($prefix . '/permissions/count', array($controller, 'countOrganizations'));

/*
 * User<-->Project Relationships
 */
$controller = new controllers\user\UserProjectController();
$prefix = '/user/projects';

// Associate Routes with Controller Functions
$app->map($prefix . '/list', array($controller, 'listProjects'));
$app->map($prefix . '/count', array($controller, 'countUserProjects'));
$app->map($prefix . '/permissions/list', array($controller, 'listProjectsPermissions'));
$app->map($prefix . '/permissions/count', array($controller, 'countProjects'));
