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
  TestFactory::marker('set-steps', 1, 'START: Test<-->Set Association')->
    after('session', 399),
  // DEFINE STEPS for User 5:Organization 1:Project 1:Set #4
  TestFactory::marker('set-steps', 100, 'START: Create Test Sets in User 5:Organization 1:Project 1:Set 4')->
    after('set-steps', 1)->after('test-sets', 399),
  // TEST CREATION
  TestFactory::tcServiceTest('set-steps', 110, 'testing/set/tests', array(4,4))->
    after('set-steps', 100), /* START OF LIST */
  TestFactory::tcServiceTest('set-steps', 111, 'testing/set/tests', array(4,1,0))->
    after('set-steps', 110), /* BEGINNING OF LIST */
  TestFactory::tcServiceTest('set-steps', 112, 'testing/set/tests', array(4,7,2))->
    after('set-steps', 111), /* BEFORE TEST 2 */
  // List and Count Tests
  TestFactory::tcServiceTest('set-steps', 120, 'testing/set/tests/list',4)->
    after('set-steps', 112),
  TestFactory::tcServiceTest('set-steps', 121, 'testing/set/tests/count',4)->
    after('set-steps', 120),
  // Renumber Steps and List
  TestFactory::tcServiceTest('set-steps', 130, 'testing/set/tests/renumber',array(4,10))->
    after('set-steps', 121),
  TestFactory::tcServiceTest('set-steps', 131, 'testing/set/tests/list',4)->
    after('set-steps', 130),
  // Move Step and List
  TestFactory::tcServiceTest('set-steps', 140, 'testing/set/tests/move',array(4,30, 15))->
    after('set-steps', 131),
  TestFactory::tcServiceTest('set-steps', 141, 'testing/set/tests/list',4)->
    after('set-steps', 140),
  // Make Test Ready
  TestFactory::tcServiceTest('set-steps', 150, 'testing/set/state/ready',4)->
    after('set-steps', 141),
  TestFactory::marker('set-steps', 199, 'END: Create Test Sets in User 5:Organization 1:Project 1:Set 4')->
    after('set-steps', 150)->before('test-sets', 900),
   // END: Test Tests
  TestFactory::marker('set-steps', 999, 'END: Test<-->Set Association')->
    after('set-steps', 199)->before('test-sets', 900)->before('tests', 900),
 );
?>
