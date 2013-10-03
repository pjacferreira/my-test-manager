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
  // START: 
  TestFactory::marker('test-steps', 1, 'START: Test Steps Creation')->
    after('session', 399),
  // CREATE TEST STEPS FOR: Test #1 
  TestFactory::marker('test-steps', 100, 'START: Create Steps in User 2:Organization 1:Project 1:Test 1')->
    after('tests', 199),
  // TEST STEP CREATION
  TestFactory::tcServiceTest('test-steps', 110, 'testing/test/steps/create', 1, array('title' => 'Step 1'))->
    after('test-steps', 100),
  TestFactory::tcServiceTest('test-steps', 111, 'testing/test/steps/create', array(1,0), array('title' => 'Step 1'))->
    after('test-steps', 110), /* Add as 1st Step */
  TestFactory::tcServiceTest('test-steps', 112, 'testing/test/steps/create', 1, array('title' => 'Step 3'))->
    after('test-steps', 111), /* Add as Last Step */
  TestFactory::tcServiceTest('test-steps', 113, 'testing/test/steps/create', 1, array('title' => 'Last Step'))->
    after('test-steps', 112), /* Add as Last Step */
  // Modify, List and Count Tests
  TestFactory::tcServiceTest('test-steps', 120, 'testing/test/steps/update',array(1,1), array('title' => 'Step 2'))->
    after('test-steps', 113),
  TestFactory::tcServiceTest('test-steps', 121, 'testing/test/steps/list',1)->
    after('test-steps', 120),
  TestFactory::tcServiceTest('test-steps', 122, 'testing/test/steps/count',1)->
    after('test-steps', 121),
  // Renumber Steps and List
  TestFactory::tcServiceTest('test-steps', 130, 'testing/test/steps/renumber',array(1,10))->
    after('test-steps', 122),
  TestFactory::tcServiceTest('test-steps', 131, 'testing/test/steps/list',1)->
    after('test-steps', 130),
  // Move Step and List
  TestFactory::tcServiceTest('test-steps', 140, 'testing/test/steps/move',array(1,40, 25))->
    after('test-steps', 131),
  TestFactory::tcServiceTest('test-steps', 141, 'testing/test/steps/list',1)->
    after('test-steps', 140),
  // Delete Step and List
  TestFactory::tcServiceTest('test-steps', 150, 'testing/test/steps/delete',array(1,25))->
    after('test-steps', 141),
  TestFactory::tcServiceTest('test-steps', 151, 'testing/test/steps/list',1)->
    after('test-steps', 150),
  // Make Test Ready
  TestFactory::tcServiceTest('test-steps', 160, 'testing/test/state/ready',1)->
    after('test-steps', 151),
  TestFactory::marker('test-steps', 199, 'END: Create Steps in User 2:Organization 1:Project 1:Test 1')->
    after('test-steps', 160)->before('project-session', 300),
  // CREATE TEST STEPS FOR: Test #4
  TestFactory::marker('test-steps', 200, 'START: Create Steps in User 4:Organization 1:Project 1:Test 4')->
    after('tests', 299),
  TestFactory::tcServiceTest('test-steps', 210, 'testing/test/steps/create', 4, array('title' => 'Step 1'))->
    after('test-steps', 200),
  TestFactory::tcServiceTest('test-steps', 211, 'testing/test/steps/create', 4, array('title' => 'Step 2'))->
    after('test-steps', 210), 
  TestFactory::tcServiceTest('test-steps', 212, 'testing/test/steps/create', 4, array('title' => 'Step 3'))->
    after('test-steps', 211), /* Add as Last Step */
  // Renumber, List and Count Steps
  TestFactory::tcServiceTest('test-steps', 220, 'testing/test/steps/renumber',array(4,10))->
    after('test-steps', 212),
  TestFactory::tcServiceTest('test-steps', 221, 'testing/test/steps/list', 4)->
    after('test-steps', 220),
  TestFactory::tcServiceTest('test-steps', 222, 'testing/test/steps/count', 4)->
    after('test-steps', 221),
  // Make Test Ready
  TestFactory::tcServiceTest('test-steps', 230, 'testing/test/state/ready',4)->
    after('test-steps', 222),
  TestFactory::marker('test-steps', 299, 'END: Create Steps in User 4:Organization 1:Project 1:Test 4')->
    after('test-steps', 230)->before('project-session', 400),
  // CREATE TEST STEPS FOR: Test #7
  TestFactory::marker('test-steps', 300, 'START: Create Steps in User 5:Organization 1:Project 1:Test 7')->
    after('tests', 399),
  TestFactory::tcServiceTest('test-steps', 310, 'testing/test/steps/create', 7, array('title' => 'Step 1'))->
    after('test-steps', 300),
  TestFactory::tcServiceTest('test-steps', 311, 'testing/test/steps/create', 7, array('title' => 'Step 2'))->
    after('test-steps', 310), 
  TestFactory::tcServiceTest('test-steps', 312, 'testing/test/steps/create', 7, array('title' => 'Step 3'))->
    after('test-steps', 311), /* Add as Last Step */
  // Renumber, List and Count Steps
  TestFactory::tcServiceTest('test-steps', 320, 'testing/test/steps/renumber',array(7,10))->
    after('test-steps', 312),
  TestFactory::tcServiceTest('test-steps', 321, 'testing/test/steps/list', 4)->
    after('test-steps', 320),
  TestFactory::tcServiceTest('test-steps', 322, 'testing/test/steps/count', 4)->
    after('test-steps', 321),
  // Make Test Ready
  TestFactory::tcServiceTest('test-steps', 330, 'testing/test/state/ready',7)->
    after('test-steps', 322),
  TestFactory::marker('test-steps', 399, 'END: Create Steps in User 5:Organization 1:Project 1:Test 7')->
    after('test-steps', 330)->before('project-session', 999),
   // END: Test Step Creation
  TestFactory::marker('test-steps', 999, 'END: Test Steps Creation')->
    after('test-steps', 199)->after('test-steps', 299)->after('test-steps', 399)->before('tests', 900),
 );
?>
