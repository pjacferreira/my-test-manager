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
  TestFactory::marker('org-user', 1, 'Organization<-->User Tests : START')->
    after('session', 1),
  // NO SESSION REQUIREMENTS
  TestFactory::marker('org-user', 100, 'PRE No Session Requirements')->
    after('org-user', 1)->after('organizations',199),
  TestFactory::tcServiceTest('org-user', 110, 'user/org/add', array(2, 1))
    ->after('org-user', 100), // User 2 <--> Organization 1
  TestFactory::tcServiceTest('org-user', 111, 'user/org/add', array(2, 2))
    ->after('org-user', 110), // User 2 <--> Organization 2
  TestFactory::tcServiceTest('org-user', 112, 'user/org/add', array(4, 4))
    ->after('org-user', 111), // User 4 <--> Organization 4
  TestFactory::marker('org-user', 199, 'POST No Session Requirements')->
    after('org-user', 112)->before('organizations', 900),
  // SESSION REQUIREMENT (ORGANIZATION 3)
  TestFactory::marker('org-user', 200, 'PRE Session Organization 3')->
    after('org-session', 200),
  TestFactory::tcServiceTest('org-user', 210, 'org/user/add', 3)
    ->after('org-user', 200), // User 3 <--> Organization 3
  TestFactory::tcServiceTest('org-user', 220, 'org/user/add', 4)
    ->after('org-user', 210), // User 4 <--> Organization 3
  TestFactory::tcServiceTest('org-user', 221, 'org/user/remove', 4)
    ->after('org-user', 220), // Delete User 4 <--> Organization 3
  TestFactory::tcServiceTest('org-user', 230, 'user/org/list', 4)
    ->after('org-user', 221), // User 4 (Only Organization 4)
  TestFactory::tcServiceTest('org-user', 231, 'user/org/count', 4)
    ->after('org-user', 230), // User 4 (Only Organization 4)
  TestFactory::tcServiceTest('org-user', 235, 'org/user/list')
    ->after('org-user', 231), // List Organization 3 USERS
  TestFactory::tcServiceTest('org-user', 236, 'org/user/count')
    ->after('org-user', 235), // Count Organization 3 USERS
  TestFactory::marker('org-user', 299, 'POST Session Organization 3')->
    after('org-user', 236)->before('org-session', 300),
  // SESSION REQUIREMENT (ORGANIZATION 4)
  TestFactory::marker('org-user', 300, 'PRE Session Organization 4')->
    after('org-session', 300),
  TestFactory::tcServiceTest('org-user', 310, 'org/user/add', 5)
    ->after('org-user', 300), // User 5 <--> Organization 4
  TestFactory::marker('org-user', 399, 'POST Session Organization 4')->
    after('org-user', 310)->before('org-session', 400),
  // SESSION REQUIREMENT (ORGANIZATION 5)
  TestFactory::marker('org-user', 400, 'PRE Session Organization 5')->
    after('org-session', 400),
  TestFactory::tcServiceTest('org-user', 410, 'org/user/add', 4)
    ->after('org-user', 400), // User 4 <--> Organization 5
  TestFactory::tcServiceTest('org-user', 411, 'org/user/add', 5)
    ->after('org-user', 410), // User 5 <--> Organization 5
  TestFactory::marker('org-user', 499, 'POST Session Organization 5')->
    after('org-user', 411)->before('org-session', 500),
  // END: Organization<-->User Tests
  TestFactory::marker('org-user', 999, 'Organization<-->User Tests : END')->
    after('org-session', 699)->before('organizations', 900),
);
?>
