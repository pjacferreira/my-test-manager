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
 * Session Routes
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
$controller = new controllers\common\SessionController();
$prefix = '/session';

// Session Service Routes
$app->get($prefix . '/', array($controller, 'hello'));
$app->get($prefix . '/hello', array($controller, 'hello'));
$app->get($prefix . '/whoami', array($controller, 'whoami'));
$app->map($prefix . '/logout', array($controller, 'logout'));
$app->map($prefix . '/login/{name}[/]?{password}', array($controller, 'login'));
$app->map($prefix . '/sudo/{name}[/]?{password}', array($controller, 'sudo'));
$app->map($prefix . '/sudo/exit', array($controller, 'sudoExit'));

// Get/Set/Clear/Test Variable
$app->map($prefix . '/get/{variable}', array($controller, 'getVariable'));
$app->map($prefix . '/set/{variable}/{value}', array($controller, 'setVariable'));
$app->map($prefix . '/isset/{variable}', array($controller, 'isVariableSet'));
$app->map($prefix . '/clear/{variable}', array($controller, 'clearVariable'));

// Get/Set/Clear/Test Organization
$app->map($prefix . '/get/org', array($controller, 'getOrganization'));
$app->map($prefix . '/set/org/{id}', array($controller, 'setOrganization'));
$app->map($prefix . '/isset/org', array($controller, 'isOrganizationSet'));
$app->map($prefix . '/clear/org', array($controller, 'clearOrganization'));

// Get/Set/Clear/Test Project
$app->map($prefix . '/get/project', array($controller, 'getProject'));
$app->map($prefix . '/set/project/{id}', array($controller, 'setProject'));
$app->map($prefix . '/isset/project', array($controller, 'isProjectSet'));
$app->map($prefix . '/clear/project', array($controller, 'clearProject'));

/* Routes WITH LIMITS (Have to be Last)
 * NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
 * Add Route Collection to Application
 */
