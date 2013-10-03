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
    // START: User Management
    TestFactory::marker('users', 1, 'User Tests : START')->
            after('session', 199),
    // CREATE USERS
    TestFactory::marker('users', 100, 'START: User Create')->
            after('users', 1),
    TestFactory::tcServiceTest('users', 110, 'manage/user/create', 'testuser2', array('password' => 'test user 2'))->
            after('users', 100),
    TestFactory::tcServiceTest('users', 111, 'manage/user/create', 'testuser3', array('password' => 'test user 3', 's_description' => 'Test User 3'))->
            after('users', 110),
    TestFactory::tcServiceTest('users', 112, 'manage/user/create', 'testuser4', array('password' => 'test user 4'))->
            after('users', 111),
    TestFactory::tcServiceTest('users', 113, 'manage/user/create', 'testuser5', array('password' => 'test user 5'))->
            after('users', 112),
    TestFactory::marker('users', 199, 'END: User Create')->
            after('users', 113)->before('session', 200),
    // USERS MODIFICATIONS
    TestFactory::marker('users', 200, 'START: User Modification')->
            after('users', 199),
    // Read/Update Users
    TestFactory::tcServiceTest('users', 210, 'manage/user/update', 2, array('first_name' => 'Test', 'last_name' => 'User', 's_description' => 'Test User 2'))->
            after('users', 200),
    TestFactory::tcServiceTest('users', 211, 'manage/user/update', 2, array('password' => 'newpassword'))->
            after('users', 210),
    TestFactory::tcServiceTest('users', 212, 'manage/user/read', 2)->
            after('users', 211),
    TestFactory::tcServiceTest('users', 215, 'manage/user/update', 'testuser5', array('first_name' => 'Test', 'last_name' => 'User', 's_description' => 'Test User 5'))->
            after('users', 212),
    TestFactory::tcServiceTest('users', 216, 'manage/user/read', 'testuser5')->
            after('users', 215),
    // List/Count Users
    TestFactory::tcServiceTest('users', 220, 'manage/users/list')->
            after('users', 216),
    TestFactory::tcServiceTest('users', 221, 'manage/users/count')->
            after('users', 220),
    TestFactory::marker('users', 299, 'END: User Modification')->
            after('users', 221)->before('session', 200),
    // USER CLEANUP
    TestFactory::marker('users', 900, 'START: USER CLEANUP')->
            after('users', 299)->after('session', 899),
    TestFactory::tcServiceTest('users', 910, 'manage/user/delete', 2)->
            after('users', 900),
    TestFactory::tcServiceTest('users', 911, 'manage/user/delete', 'testuser3')->
            after('users', 910),
    TestFactory::tcServiceTest('users', 912, 'manage/user/delete', 4)->
            after('users', 911),
    TestFactory::tcServiceTest('users', 913, 'manage/user/delete', 5)->
            after('users', 912),
    // Users List and Count
    TestFactory::tcServiceTest('users', 920, 'manage/users/list')->
            after('users', 913),
    TestFactory::tcServiceTest('users', 921, 'manage/users/count')->
            after('users', 920),
    TestFactory::marker('users', 929, 'END: USER CLEANUP')->
            after('users', 921)->before('session', 900),
    // TEST NO SESSION
    TestFactory::marker('users', 950, 'PRE NO SESSION ACCESS ATTEMPTS')->
            after('users', 929)->after('session', 920),
    TestFactory::tcServiceTest('users', 960, 'manage/user/create', 'testuser2', array('password' => 'test user 2'), false)->
            after('users', 950),
    TestFactory::tcServiceTest('users', 961, 'manage/user/read', 1, null, false)->
            after('users', 960),
    TestFactory::tcServiceTest('users', 962, 'manage/user/update', 1, array('password' => 'newpassword'), false)->
            after('users', 961),
    TestFactory::tcServiceTest('users', 963, 'manage/user/delete', 1, null, false)->
            after('users', 962),
    TestFactory::tcServiceTest('users', 964, 'manage/users/list', null, null, false)->
            after('users', 963),
    TestFactory::tcServiceTest('users', 965, 'manage/users/count', null, null, false)->
            after('users', 964),
    TestFactory::marker('users', 969, 'POST NO SESSION ACCESS ATTEMPTS')->
            after('users', 965),
    // END: User Tests
    TestFactory::marker('users', 999, 'User Tests : END')->
            after('users', 969)->before('session', 999),
);
?>
