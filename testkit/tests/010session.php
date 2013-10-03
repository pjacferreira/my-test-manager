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
    // START: ALL TEstintin
    TestFactory::marker('session', 1, 'START: TESTING'),
    // ADMIN LOGIN
    TestFactory::marker('session', 100, 'PRE Admin LOGIN')->
            after('session', 1),
    TestFactory::tcServiceTest('session', 110, 'session/login', 'admin/admin')->
            after('session', 100),
    TestFactory::tcServiceTest('session', 111, 'session/user/whoami')->
            after('session', 110),
    TestFactory::marker('session', 199, 'POST Admin Login')->
            after('session', 111),
    // USER TESTS
    TestFactory::marker('session', 200, 'START: USER TESTING')->
            after('session', 199),
    TestFactory::marker('session', 300, 'PRE User Test Preparation')->
            after('session', 200),
    TestFactory::tcServiceTest('session', 310, 'session/logout')->
            after('session', 300),
    TestFactory::marker('session', 399, 'POST User Test Preparation')->
            after('session', 310),
    TestFactory::marker('session', 799, 'END: USER TESTING')->
            after('session', 399),
    // CLEANUP PREPARATION
    TestFactory::marker('session', 800, 'CLEANUP: Start Preparation')->
            after('session', 799),
    TestFactory::tcServiceTest('session', 810, 'session/logout')->
            after('session', 800),
    TestFactory::tcServiceTest('session', 820, 'session/login', 'admin/admin')->
            after('session', 810),
    TestFactory::marker('session', 899, 'CLEANUP: END Preparation')->
            after('session', 820),
    // CLEAN UP
    TestFactory::marker('session', 900, 'CLEANUP: START')->
            after('session', 899),
    TestFactory::tcServiceTest('session', 910, 'session/logout')->
            after('session', 900),
    TestFactory::marker('session', 920, 'CLEANUP: END')->
            after('session', 910),
    // END: ALL TESTING
    TestFactory::marker('session', 999, 'END: TESTING')->
            after('session', 920),
);
?>
