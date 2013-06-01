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
  // Add User to Project - 1st Method of Linking Users to Project (From Users Services Group - Into a Specific Project)
  TestFactory::tcServiceTest('project-user', 10, 'user/projects/add', array(2, 3))->
    after('org-user', 10)->after('projects', 12), // Add User 2 to Project 3
  TestFactory::tcServiceTest('project-user', 11, 'user/projects/add', array(3, 2))->after('project-user', 10), // Add User 3 to Project 2
  TestFactory::tcServiceTest('project-user', 12, 'user/projects/add', array(3, 3))->after('project-user', 10), // Add User 3 to Project 3
  // Marker: Project-User Initialized
  TestFactory::marker('project-user', 20, 'Project-User Setup Complete')->after('project-user', 12),
  // Add Users to Project - 2nd Method of Linking Users to Project (From Project Services Group - Using the Current Session Project)
  TestFactory::tcServiceTest('project-user', 30, 'project/users/add', 3)->
    after('org-user', 60)->after('project-user', 20)->after('org-session', 20)->after('project-session', 20), // Add User 3 to Session Project (1)
  // List/Count Users in Project - 1st Method (From Users Services Group - Specific Project)
  TestFactory::tcServiceTest('project-user', 31, 'user/projects/list', 3)->after('project-user', 30),
  TestFactory::tcServiceTest('project-user', 32, 'user/projects/count', 3)->after('project-user', 31),
  // List/Count Users in Project - 2nd Method (From Project Services Group - Using the Current Session Project)
  TestFactory::tcServiceTest('project-user', 35, 'project/users/list')->after('project-user', 32),
  TestFactory::tcServiceTest('project-user', 36, 'project/users/count')->after('project-user', 35),
  // Get/Set Users in Project - 1st Method (From Users Services Group - Specific Project)
  TestFactory::tcServiceTest('project-user', 40, 'user/projects/get', array(3, 2))->after('project-user', 36),
  TestFactory::tcServiceTest('project-user', 41, 'user/projects/set', array(3, 2, 'project 1'))->after('project-user', 40),
  TestFactory::tcServiceTest('project-user', 42, 'user/projects/get', array(3, 2))->after('project-user', 41),
  // Remove User 3 from Current Session Project
  TestFactory::tcServiceTest('project-user', 45, 'project/users/remove', 3)->
    after('project-user', 42)->before('org-session', 30)->before('project-user', 900),
  // Get/Set Users in Project - 2nd Method (From Project Services Group - Using the Current Session Project)
  // TODO Re-Organize Tests (Dependencies are getting too hard to manage) - Have to build the correct User/Org/Project/Session Structure before further tests can be done
//  TestFactory::tcServiceTest('project-user', 50, 'project/users/get', 3)->
//    after('project-user', 20)->after('org-session', 30)->after('project-session', 40),
//  TestFactory::tcServiceTest('project-user', 51, 'project/users/set', array(3, 'org2'))->
//    after('project-user', 50),
//  TestFactory::tcServiceTest('project-user', 52, 'project/users/get', 3)->
//    after('project-user', 51)->before('org-session', 40)->before('project-session', 50)->before('project-user', 900),
  // Marker: Project-User Cleanup
  TestFactory::marker('project-user', 900, 'Project-User Cleanup')->
    after('project-user', 20)->before('projects', 900),
  // Remove User to Project - 1st Method of Unlinking Users to Project (From Users Services Group - Into a Specific Project)
  TestFactory::tcServiceTest('project-user', 910, 'user/projects/remove', array(2, 3))->after('project-user', 900),
  TestFactory::tcServiceTest('project-user', 911, 'user/projects/remove', array(3, 2))->after('project-user', 910),
// TestFactory::tcServiceTest('project-user', 912, 'user/projects/remove', array(3, 3))->after('project-user', 911),
  // TEST Expected to Fail
  TestFactory::tcServiceTest('project-user', 920, 'project/users/get', 3, null, false)->
    after('project-user', 911)->before('projects', 900), // User no longer part of organization
  TestFactory::tcServiceTest('project-user', 921, 'project/users/remove', 3, null, false)->
    after('project-user', 911)->before('projects', 900), // User no longer part of organization
);
?>
