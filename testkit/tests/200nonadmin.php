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

//$SKIP = true; // Skip the File for Current Test

$TESTS = array(
    // START: Normal User Session Tests 
    TestFactory::marker('user-session', 1, 'START: Normal User Session Tests')->
        after('session', 399)->after('users', 299),
    // USER SESSION : Test User 2
    TestFactory::marker('user-session', 100, 'START: Test User 2 TESTS')->
            after('user-session', 1),
    TestFactory::marker('user-session', 110, 'START: Test User 2 LOGIN')->
            after('user-session', 100),
    TestFactory::tcServiceTest('user-session', 120, 'session/logout')->
            after('user-session', 110),
    TestFactory::tcServiceTest('user-session', 130, 'session/login', array('testuser2', 'newpassword'))->
            after('user-session', 120),
    TestFactory::tcServiceTest('user-session', 131, 'session/user/whoami')->
            after('user-session', 130),
    TestFactory::marker('user-session', 199, 'END: Test User 2 LOGIN')->
            after('user-session', 131),
    // USER SESSION : Test User 3
    TestFactory::marker('user-session', 200, 'START: Test User 3 TESTS')->
            after('user-session', 199),
    TestFactory::marker('user-session', 210, 'START: Test User 3 LOGIN')->
            after('user-session', 200),
    TestFactory::tcServiceTest('user-session', 220, 'session/logout')->
            after('user-session', 210),
    TestFactory::tcServiceTest('user-session', 230, 'session/login', array('testuser3', 'test user 3'))->
            after('user-session', 220),
    TestFactory::tcServiceTest('user-session', 231, 'session/user/whoami')->
            after('user-session', 230),
    TestFactory::marker('user-session', 299, 'END: Test User 3 LOGIN')->
            after('user-session', 231),
    // USER SESSION : Test User 4
    TestFactory::marker('user-session', 300, 'PRE Test User 4 TESTS')->
            after('user-session', 299),
    TestFactory::marker('user-session', 310, 'PRE Test User 4 LOGIN')->
            after('user-session', 300),
    TestFactory::tcServiceTest('user-session', 320, 'session/logout')->
            after('user-session', 310),
    TestFactory::tcServiceTest('user-session', 330, 'session/login', array('testuser4', 'test user 4'))->
            after('user-session', 320),
    TestFactory::tcServiceTest('user-session', 331, 'session/user/whoami')->
            after('user-session', 330),
    TestFactory::marker('user-session', 399, 'POST Test User 4 LOGIN')->
            after('user-session', 331),
    // USER SESSION : Test User 5
    TestFactory::marker('user-session', 400, 'PRE Test User 5 TESTS')->
            after('user-session', 399),
    TestFactory::marker('user-session', 410, 'PRE Test User 5 LOGIN')->
            after('user-session', 400),
    TestFactory::tcServiceTest('user-session', 420, 'session/logout')->
            after('user-session', 410),
    TestFactory::tcServiceTest('user-session', 430, 'session/login', array('testuser5', 'test user 5'))->
            after('user-session', 420),
    TestFactory::tcServiceTest('user-session', 431, 'session/user/whoami')->
            after('user-session', 430),
    TestFactory::marker('user-session', 499, 'POST Test User 5 LOGIN')->
            after('user-session', 431),
    // END: Normal User Session Tests 
    TestFactory::marker('user-session', 999, 'END: Normal User Session Tests')->
            after('user-session', 499)->before('session', 799),
);
?>
