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
 * Service Routes Loader
 * 
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */

/* TODO: 
 * 1. Limit the routes that are loaded based on the Session State
 * i.e.
 * - If we don't have a login yet, then no use allowing for USER
 * or ADMIN Routes (should be limited to Session Routes)
 * - If nthe logged in user doesn't have administrative rights, no use loading
 * ADMIN routes.
 * 
 * On top of everything, it makes things more secure.
 */

// NOTE: Routes are matched in reverse order LIFO (so routes added later are processed 1st)
// Add Addmin Routes?
$ADMIN = true;

// Common routes
include __DIR__ . '/routes/common/session.php';

// User Mode Routes
include __DIR__ . '/routes/user/projects.php';
include __DIR__ . '/routes/user/organizations.php';
include __DIR__ . '/routes/user/users.php';
include __DIR__ . '/routes/user/containers.php';
include __DIR__ . '/routes/user/tests.php';
include __DIR__ . '/routes/user/steps.php';

// Should we add Admin Mode routes?
if ($ADMIN) { // YES
  include __DIR__ . '/routes/admin/projects.php';
  include __DIR__ . '/routes/admin/organizations.php';
  include __DIR__ . '/routes/admin/users.php';
}