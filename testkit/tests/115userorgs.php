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
  TestFactory::marker('user-org', 1, 'START: User<-->Organization Tests')->
    after('users', 299)->after('organizations',299),
  // CREATE ASSOCIATION BETWEEN USER AND ORGANIZATIONS
  TestFactory::marker('user-org', 100, 'START: User<-->Organization Permissions')->
    after('user-org', 1),
  TestFactory::tcServiceTest('user-org', 110, 'manage/user/permissions/org/set', array(2, 1, "p2-1"))
    ->after('user-org', 100), // User 2 <--> Organization 1
  TestFactory::tcServiceTest('user-org', 111, 'manage/user/permissions/org/set', array(2, 2, "p2-2"))
    ->after('user-org', 110), // User 2 <--> Organization 2
  TestFactory::tcServiceTest('user-org', 120, 'manage/user/permissions/org/set', array(3, 1, "p3-1"))
    ->after('user-org', 111), // User 3 <--> Organization 1
  TestFactory::tcServiceTest('user-org', 121, 'manage/user/permissions/org/set', array(3, 3, "p3-3"))
    ->after('user-org', 120), // User 3 <--> Organization 3
  TestFactory::tcServiceTest('user-org', 130, 'manage/user/permissions/org/set', array(4, 1, "p4-1"))
    ->after('user-org', 121), // User 4 <--> Organization 1
  TestFactory::tcServiceTest('user-org', 131, 'manage/user/permissions/org/set', array(4, 3, "p4-3"))
    ->after('user-org', 130), // User 4 <--> Organization 4
  TestFactory::tcServiceTest('user-org', 132, 'manage/user/permissions/org/set', array(4, 4, "p4-4"))
    ->after('user-org', 131), // User 4 <--> Organization 4
  TestFactory::tcServiceTest('user-org', 140, 'manage/user/permissions/org/set', array(5, 4, "p5-1"))
    ->after('user-org', 132), // User 5 <--> Organization 1
  TestFactory::tcServiceTest('user-org', 141, 'manage/user/permissions/org/set', array(5, 4, "p5-4"))
    ->after('user-org', 140), // User 5 <--> Organization 4
  TestFactory::tcServiceTest('user-org', 142, 'manage/user/permissions/org/set', array(5, 5, "p5-5"))
    ->after('user-org', 141), // User 5 <--> Organization 4
  TestFactory::marker('user-org', 199, 'END: User<-->Organization Permissions')->
    after('user-org', 142),
  // USER <--> ORGANIZATION PERMISSIONS LISTINGS
  TestFactory::marker('user-org', 200, 'START: User<-->Organization Permissions Lists')->
    after('user-org', 199),
  TestFactory::tcServiceTest('user-org', 210, 'manage/user/orgs/list', 4)
    ->after('user-org', 200), // Organizations User 4 Belongs to
  TestFactory::tcServiceTest('user-org', 211, 'manage/user/orgs/count', 4)
    ->after('user-org', 210), // Organizations User 4 Belongs to
  TestFactory::tcServiceTest('user-org', 220, 'manage/org/users/list', 4)
    ->after('user-org', 211), // Users in Organization 4
  TestFactory::tcServiceTest('user-org', 221, 'manage/org/users/count', 4)
    ->after('user-org', 220), // Users in Organization 4
  TestFactory::tcServiceTest('user-org', 230, 'manage/user/permissions/orgs/list', 4)
    ->after('user-org', 221), // Permission, per Organization, for User 4
  TestFactory::tcServiceTest('user-org', 231, 'manage/user/permissions/orgs/count', 4)
    ->after('user-org', 230), // Permission, per Organization, for User 4
  TestFactory::marker('user-org', 299, 'END: User<-->Organization Permissions Lists')->
    after('user-org', 231),
  // USER <--> ORGANIZATION READ/DELETE
  TestFactory::marker('user-org', 300, 'START: User<-->Organization Modifications')->
    after('user-org', 299),
  TestFactory::tcServiceTest('user-org', 310, 'manage/user/permissions/org/get', array(4, 3))
    ->after('user-org', 300), // User 4 <--> Organization 3
  TestFactory::tcServiceTest('user-org', 311, 'manage/user/permissions/org/clear', array(4, 3))
    ->after('user-org', 310), // User 4 <--> Organization 3
  TestFactory::tcServiceTest('user-org', 312, 'manage/user/orgs/list', 4)
    ->after('user-org', 311), // Organizations's User 4 Belongs to
  TestFactory::tcServiceTest('user-org', 313, 'manage/user/orgs/count', 4)
    ->after('user-org', 312), // Organizations User 4 Belongs to
  TestFactory::tcServiceTest('user-org', 314, 'manage/org/users/list', 3)
    ->after('user-org', 313), // Users in Organization 3
  TestFactory::tcServiceTest('user-org', 315, 'manage/org/users/count', 3)
    ->after('user-org', 314), // Users in Organization 3
  TestFactory::marker('user-org', 399, 'END: User<-->Organization Modifications')->
    after('user-org', 315)->before('session', 200),
  // END: Organization<-->User Tests
  TestFactory::marker('user-org', 999, 'END: User<-->Organization Tests')->
    after('user-org', 399)->before('users', 900)->before('organizations', 900),
);
?>
