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
  // START: 
  TestFactory::marker('test-sets', 1, 'START: Test Set Creation')->
    after('session', 399),
  // CREATE SETS for User 2:Organization 1:Project 1
  TestFactory::marker('test-sets', 100, 'START: Create Test Sets in User 2:Organization 1:Project 1')->
    after('test-sets', 1)->after('tests', 199),
  // TEST CREATION
  TestFactory::tcServiceTest('test-sets', 110, 'testing/set/create', 'Set U2:O1:P1-1')->
    after('test-sets', 100),
  TestFactory::tcServiceTest('test-sets', 111, 'testing/set/create', 'Set U2:O1:P1-2')->
    after('test-sets', 110),
  // Read/Update Projects
/* NEED TO CREATE UPDATE TEST      
  TestFactory::tcServiceTest('test-sets', 120, 'testing/test/update', 1, array('description' => 'Organization 3 - Project 1'))->
    after('test-sets', 120),
 */
  TestFactory::tcServiceTest('test-sets', 121, 'testing/test/read', 1)->
    after('test-sets', 111),
  // List and Count Tests
  TestFactory::tcServiceTest('test-sets', 130, 'testing/tests/list')->
    after('test-sets', 121),
  TestFactory::tcServiceTest('test-sets', 131, 'testing/tests/count')->
    after('test-sets', 130),
  TestFactory::marker('test-sets', 199, 'END: Create Test Sets in User 2:Organization 1:Project 1')->
    after('test-sets', 131)->before('project-session', 300),
  // CREATE SETS for User 4:Organization 1:Project 1
  TestFactory::marker('test-sets', 200, 'START: Create Test Sets in User 4:Organization 1:Project 1')->
    after('tests', 299),
  // TEST CREATION
  TestFactory::tcServiceTest('test-sets', 210, 'testing/set/create', 'Set U4:O1:P1-1')->
    after('test-sets', 200),
  // List and Count Tests
  TestFactory::tcServiceTest('test-sets', 220, 'testing/sets/list')->
    after('test-sets', 210),
  TestFactory::tcServiceTest('test-sets', 221, 'testing/sets/count')->
    after('test-sets', 220),
  TestFactory::marker('test-sets', 299, 'END: Create Test Sets in User 4:Organization 1:Project ')->
    after('test-sets', 221)->before('project-session', 400),
  // CREATE SETS for User 5:Organization 1:Project 1
  TestFactory::marker('test-sets', 300, 'START: Create Test Sets in User 5:Organization 1:Project 1')->
    after('tests', 399),
  // TEST CREATION
  TestFactory::tcServiceTest('test-sets', 310, 'testing/set/create', 'Set U5:O1:P1-1')->
    after('test-sets', 300),
  // List and Count Tests (User)
  TestFactory::tcServiceTest('test-sets', 320, 'testing/sets/user/list')->
    after('test-sets', 310),
  TestFactory::tcServiceTest('test-sets', 321, 'testing/sets/user/count')->
    after('test-sets', 320),
  // List and Count Tests (Project)
  TestFactory::tcServiceTest('test-sets', 330, 'testing/sets/list')->
    after('test-sets', 321),
  TestFactory::tcServiceTest('test-sets', 331, 'testing/sets/count')->
    after('test-sets', 330),
  TestFactory::marker('test-sets', 399, 'END: Create Test Sets in User 5:Organization 1:Project')->
    after('test-sets', 331)->before('tests', 900),
  // TEST CLEANUP
  TestFactory::marker('test-sets', 900, 'START: TEST CLEANUP')->
    after('test-sets', 399)->before('org-session', 999),
   // END: Test Tests
  TestFactory::marker('test-sets', 999, 'END: Test Creation')->
    after('test-sets', 900)->before('organizations', 900),
 );
?>
