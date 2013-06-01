<?php

/* Test Center - Test Kit
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
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

namespace tests;

use api\TestFactory;

$SKIP = true; // Skip the File for Current Test

$TESTS = array(
  // Set the Session Project
  TestFactory::tcServiceTest('project-session', 10, 'session/project/get', null, null, false)->after('session', 20), // FAIL EXPECTED (No Project Set)
  // Set Project 1 as the Session Organization
  TestFactory::tcServiceTest('project-session', 20, 'session/project/set', 1)
    ->after('projects', 10),
  TestFactory::tcServiceTest('project-session', 21, 'session/project/get')
    ->after('project-session', 20)->before('projects', 16),
  // Set Project 2 as the Session Organization
  TestFactory::tcServiceTest('project-session', 30, 'session/project/set', 2)->after('projects', 11)->before('projects', 900),
  // Set Project 3 as the Session Organization
  TestFactory::tcServiceTest('project-session', 40, 'session/project/set', 3)->after('projects', 12)->before('projects', 900),
  // Set Project 4 as the Session Organization
  TestFactory::tcServiceTest('project-session', 50, 'session/project/set', 4)->after('projects', 20)->before('projects', 900),
  // Set Project 5 as the Session Organization
  TestFactory::tcServiceTest('project-session', 60, 'session/project/set', 5)->after('projects', 21)->before('projects', 900),
  // Set Project 6 as the Session Organization
  TestFactory::tcServiceTest('project-session', 70, 'session/project/set', 6)->after('projects', 22)->before('projects', 900),
);
?>
