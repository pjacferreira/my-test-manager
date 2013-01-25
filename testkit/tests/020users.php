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
  // START: Session Tests
  TestFactory::marker('users', 1, 'User Tests : START')->
    after('session', 1),
  // CREATE USERS
  TestFactory::marker('users', 100, 'PRE User Create')->
    after('session', 199)->before('session', 900),
  TestFactory::tcServiceTest('users', 110, 'user/create', 'testuser2', 'password=test user 2')->
    after('users', 100),
  TestFactory::tcServiceTest('users', 111, 'user/create', 'testuser3', array('password=test user 3','s_description=Test User 3'))->
    after('users', 110),
  TestFactory::tcServiceTest('users', 112, 'user/create', 'testuser4', 'password=test user 4')->
    after('users', 111),
  TestFactory::tcServiceTest('users', 113, 'user/create', 'testuser5', 'password=test user 5')->
    after('users', 112),
  TestFactory::marker('users', 199, 'POST User Create')->
    after('users', 113),
  // USERS MODIFICATIONS
  TestFactory::marker('users', 200, 'PRE User Modification')->
    after('users', 199)->before('session', 900),
  // Read/Update Users
  TestFactory::tcServiceTest('users', 210, 'user/update', 2, array('first_name=Test', 'last_name=User', 's_description=Test User 2'))->
    after('users', 200),
  TestFactory::tcServiceTest('users', 211, 'user/update', 2, 'password=newpassword')->
    after('users', 210),
  TestFactory::tcServiceTest('users', 212, 'user/read', 2)->
    after('users', 211),
  TestFactory::tcServiceTest('users', 215, 'user/update', 'testuser5', array('first_name=Test', 'last_name=User', 's_description=Test User 5'))->
    after('users', 211),
  TestFactory::tcServiceTest('users', 216, 'user/read', 'testuser5')->
    after('users', 215),
  // List/Count Users
  TestFactory::tcServiceTest('users', 220, 'user/list')->
    after('users', 216),
  TestFactory::tcServiceTest('users', 221, 'user/count')->
    after('users', 220),
  TestFactory::marker('users', 299, 'POST User Modification')->
    after('users', 221),
  // NON ADMIN TESTS
  TestFactory::marker('users', 300, 'PRE NON ADMIN TESTS')->
    after('users', 299)->before('session', 900),
  TestFactory::marker('users', 310, 'PRE testuser2 LOGIN')->
    after('users', 300),
  TestFactory::tcServiceTest('users', 320, 'session/logout')->
    after('users', 310),
  // NOTE: Also Tests Password Change in USERS:211
  TestFactory::tcServiceTest('users', 321, 'session/login', array('testuser2', 'newpassword'))->
    after('users', 320),
  TestFactory::tcServiceTest('users', 322, 'session/whoami')->
    after('users', 321),
  TestFactory::marker('users', 329, 'POST testuser2 LOGIN')->
    after('users', 322),
  TestFactory::marker('users', 330, 'PRE testuser3 LOGIN')->
    after('users', 329),
  // NOTE: We Don't Logout Previous User - Just Login with new
  TestFactory::tcServiceTest('users', 340, 'session/login', array('testuser3', 'test user 3'))->
    after('users', 330),
  TestFactory::tcServiceTest('users', 341, 'session/whoami')->
    after('users', 340),
  TestFactory::marker('users', 349, 'POST testuser3 LOGIN')->
    after('users', 341),
  TestFactory::marker('users', 399, 'POST ADMIN TESTS')->
    after('users', 329),
  // SESSION ADMIN RESTORE
  TestFactory::marker('users', 400, 'PRE ADMIN LOGIN')->
    after('users', 399)->before('session', 900),
  TestFactory::tcServiceTest('users', 410, 'session/login', array('admin', 'admin'))->
    after('users', 400),
  TestFactory::tcServiceTest('users', 411, 'session/whoami')->
    after('users', 410),
  TestFactory::marker('users', 499, 'POST ADMIN TESTS')->
    after('users', 411),
  // USER CLEANUP
  TestFactory::marker('users', 900, 'PRE USER CLEANUP')->
    after('users', 499)->before('session', 900),
  TestFactory::tcServiceTest('users', 910, 'user/delete', 2)->
    after('users', 900),
  TestFactory::tcServiceTest('users', 911, 'user/delete', 'testuser3')->
    after('users', 900),
  TestFactory::tcServiceTest('users', 912, 'user/delete', 4)->
    after('users', 900),
  TestFactory::tcServiceTest('users', 913, 'user/delete', 5)->
    after('users', 900),
  // Users List and Count
  TestFactory::tcServiceTest('users', 920, 'user/list')->
    after('users', 913),
  TestFactory::tcServiceTest('users', 921, 'user/count')->
    after('users', 920),
  TestFactory::marker('users', 929, 'POST USER CLEANUP')->
    after('users', 921)->before('session', 900),
  // TEST NO SESSION
  TestFactory::marker('users', 950, 'PRE NO SESSION ACCESS ATTEMPTS')->
    after('users', 929)->after('session', 920),
  TestFactory::tcServiceTest('users', 960, 'user/create', 'testuser2', 'password=test user 2', false)->
    after('users', 950),
  TestFactory::tcServiceTest('users', 961, 'user/read', 1, null, false)->
    after('users', 960),
  TestFactory::tcServiceTest('users', 962, 'user/update', 1, 'password=newpassword', false)->
    after('users', 961),
  TestFactory::tcServiceTest('users', 963, 'user/delete', 1, null, false)->
    after('users', 962),
  TestFactory::tcServiceTest('users', 964, 'user/list', null, null, false)->
    after('users', 963),
  TestFactory::tcServiceTest('users', 965, 'user/count', null, null, false)->
    after('users', 964),
  TestFactory::marker('users', 969, 'POST NO SESSION ACCESS ATTEMPTS')->
    after('users', 965),
  // END: User Tests
  TestFactory::marker('users', 999, 'User Tests : END')->
    after('users', 969),
);
?>
