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
  TestFactory::marker('tests', 1, 'START: Test Creation')->
    after('session', 399),
  // CREATE TESTS User 2:Organization 1:Project 1
  TestFactory::marker('tests', 100, 'START: Create Tests in User 2:Organization 1:Project 1')->
    after('tests', 1)->after('project-session', 299),
  // TEST CREATION
  TestFactory::tcServiceTest('tests', 110, 'testing/test/create', 'Test U2:O1:P1-1')->
    after('tests', 100),
  TestFactory::tcServiceTest('tests', 111, 'testing/test/create', 'Test U2:O1:P1-2')->
    after('tests', 110),
  TestFactory::tcServiceTest('tests', 112, 'testing/test/create', 'Test U2:O1:P1-3')->
    after('tests', 111),
  // Read/Update Tests
/* NEED TO CREATE UPDATE TEST      
  TestFactory::tcServiceTest('tests', 120, 'testing/test/update', 1, array('description' => 'Organization 3 - Project 1'))->
    after('tests', 120),
 */
  TestFactory::tcServiceTest('tests', 121, 'testing/test/read', 1)->
    after('tests', 112),
  // List and Count Tests
  TestFactory::tcServiceTest('tests', 130, 'testing/tests/list')->
    after('tests', 121),
  TestFactory::tcServiceTest('tests', 131, 'testing/tests/count')->
    after('tests', 130),
  TestFactory::marker('tests', 199, 'END: Create Tests in User 2:Organization 1:Project 1')->
    after('tests', 131)->before('project-session', 300),
  // CREATE TESTS User 4:Organization 1:Project 1
  TestFactory::marker('tests', 200, 'START: Create Tests in User 4:Organization 1:Project 1')->
    after('project-session', 399),
  // TEST CREATION
  TestFactory::tcServiceTest('tests', 210, 'testing/test/create', 'Test U4:O1:P1-1')->
    after('tests', 200),
  TestFactory::tcServiceTest('tests', 211, 'testing/test/create', 'Test U4:O1:P1-2')->
    after('tests', 210),
  TestFactory::tcServiceTest('tests', 212, 'testing/test/create', 'Test U4:O1:P1-3')->
    after('tests', 211),
  // List and Count Tests
  TestFactory::tcServiceTest('tests', 220, 'testing/tests/list')->
    after('tests', 212),
  TestFactory::tcServiceTest('tests', 221, 'testing/tests/count')->
    after('tests', 220),
  TestFactory::marker('tests', 299, 'END: Create Tests in User 4:Organization 1:Project ')->
    after('tests', 221)->before('project-session', 400),
  // CREATE TESTS User 5:Organization 1:Project 1
  TestFactory::marker('tests', 300, 'START: Create Tests in User 5:Organization 1:Project 1')->
    after('project-session', 499),
  // TEST CREATION
  TestFactory::tcServiceTest('tests', 310, 'testing/test/create', 'Test U5:O1:P1-1')->
    after('tests', 300),
  TestFactory::tcServiceTest('tests', 311, 'testing/test/create', 'Test U5:O1:P1-2')->
    after('tests', 310),
  TestFactory::tcServiceTest('tests', 312, 'testing/test/create', 'Test U5:O1:P1-3')->
    after('tests', 311),
  // List and Count Tests (User)
  TestFactory::tcServiceTest('tests', 320, 'testing/tests/user/list')->
    after('tests', 312),
  TestFactory::tcServiceTest('tests', 321, 'testing/tests/user/count')->
    after('tests', 320),
  // List and Count Tests (Project)
  TestFactory::tcServiceTest('tests', 330, 'testing/tests/list')->
    after('tests', 321),
  TestFactory::tcServiceTest('tests', 331, 'testing/tests/count')->
    after('tests', 330),
  TestFactory::marker('tests', 399, 'END: Create Tests in User 5:Organization 1:Project')->
    after('tests', 331)->before('project-session', 999),
  // TEST CLEANUP
  TestFactory::marker('tests', 900, 'START: Test CLEANUP')->
    after('tests', 399)->before('org-session', 999),
   // END: Test Creation
  TestFactory::marker('tests', 999, 'END: Test Creation')->
    after('tests', 900)->before('organizations', 900),
 );
?>
