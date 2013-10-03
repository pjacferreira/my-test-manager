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

// $SKIP = true; // Skip the File for Current Test

$TESTS = array(
  // START: Organization<-->Session Tests
  TestFactory::marker('user-project', 1, 'START: User<-->Project Tests')->
    after('session', 399)->after('users', 199)->after('user-org', 199)->after('projects',199),
  // CREATE ASSOCIATION BETWEEN USER AND PROJECTS
  TestFactory::marker('user-project', 100, 'START: User<-->Project Permissions')->
    after('user-project', 1),
  TestFactory::tcServiceTest('user-project', 110, 'manage/user/permissions/project/set', array(2, 1, "u2-o1-p1"))
    ->after('user-project', 100), // User 2 <--> Project 1
  TestFactory::tcServiceTest('user-project', 111, 'manage/user/permissions/project/set', array(2, 2, "u2-o2-p1"))
    ->after('user-project', 110), // User 2 <--> Project 2
  TestFactory::tcServiceTest('user-project', 120, 'manage/user/permissions/project/set', array(3, 1, "u3-o1-p1"))
    ->after('user-project', 111), // User 3 <--> Project 1
  TestFactory::tcServiceTest('user-project', 121, 'manage/user/permissions/project/set', array(3, 3, "u3-o3-p1"))
    ->after('user-project', 120), // User 3 <--> Project 3
  TestFactory::tcServiceTest('user-project', 130, 'manage/user/permissions/project/set', array(4, 1, "u4-o1-p1"))
    ->after('user-project', 121), // User 4 <--> Project 1
  TestFactory::tcServiceTest('user-project', 131, 'manage/user/permissions/project/set', array(4, 3, "u4-o3-p1"))
    ->after('user-project', 130), // User 4 <--> Project 4
  TestFactory::tcServiceTest('user-project', 132, 'manage/user/permissions/project/set', array(4, 4, "u4-o4-p1"))
    ->after('user-project', 131), // User 4 <--> Project 4
  TestFactory::tcServiceTest('user-project', 140, 'manage/user/permissions/project/set', array(5, 1, "u5-o1-p1"))
    ->after('user-project', 132), // User 5 <--> Project 1
  TestFactory::tcServiceTest('user-project', 141, 'manage/user/permissions/project/set', array(5, 4, "u5-o4-p1"))
    ->after('user-project', 140), // User 5 <--> Project 4
  TestFactory::tcServiceTest('user-project', 142, 'manage/user/permissions/project/set', array(5, 5, "u5-o5-p1"))
    ->after('user-project', 141), // User 5 <--> Project 4
  TestFactory::marker('user-project', 199, 'END: User<-->Project Permissions')->
    after('user-project', 142),
  // USER <--> PROJECT PERMISSIONS LISTINGS
  TestFactory::marker('user-project', 200, 'START: User<-->Project Permissions Lists')->
    after('user-project', 199),
  TestFactory::tcServiceTest('user-project', 210, 'manage/user/projects/list', 4)
    ->after('user-project', 200), // Projects User 4 Belongs to
  TestFactory::tcServiceTest('user-project', 211, 'manage/user/projects/count', 4)
    ->after('user-project', 210), // Projects User 4 Belongs to
  TestFactory::tcServiceTest('user-project', 220, 'manage/projects/users/list', 4)
    ->after('user-project', 211), // Users in Project 4
  TestFactory::tcServiceTest('user-project', 221, 'manage/projects/users/count', 4)
    ->after('user-project', 220), // Users in Project 4
  TestFactory::tcServiceTest('user-project', 230, 'manage/user/permissions/projects/list', 4)
    ->after('user-project', 221), // Permission, per Project, for User 4
  TestFactory::tcServiceTest('user-project', 231, 'manage/user/permissions/projects/count', 4)
    ->after('user-project', 230), // Permission, per Project, for User 4
  TestFactory::marker('user-project', 299, 'END: User<-->Project Permissions Lists')->
    after('user-project', 231),
  // USER <--> PROJECT READ/DELETE
  TestFactory::marker('user-project', 300, 'START: User<-->PROJECT Modifications')->
    after('user-project', 299),
  TestFactory::tcServiceTest('user-project', 310, 'manage/user/permissions/project/get', array(4, 3))
    ->after('user-project', 300), // User 4 <--> Project 3
  TestFactory::tcServiceTest('user-project', 311, 'manage/user/permissions/project/clear', array(4, 3))
    ->after('user-project', 310), // User 4 <--> Project 3
  TestFactory::tcServiceTest('user-project', 312, 'manage/user/projects/list', 4)
    ->after('user-project', 311), // Projects User 4 Belongs to
  TestFactory::tcServiceTest('user-project', 313, 'manage/user/projects/count', 4)
    ->after('user-project', 312), // Projects User 4 Belongs to
  TestFactory::tcServiceTest('user-project', 314, 'manage/projects/users/list', 3)
    ->after('user-project', 313), // Users in Project 3
  TestFactory::tcServiceTest('user-project', 315, 'manage/projects/users/count', 3)
    ->after('user-project', 314), // Users in Project 3
  TestFactory::marker('user-project', 399, 'END: User<-->Project Modifications')->
    after('user-project', 315),
  // END: Organization<-->User Tests
  TestFactory::marker('user-project', 999, 'END: User<-->Organization Tests')->
    after('user-project', 399)->before('projects', 900),
);
?>
