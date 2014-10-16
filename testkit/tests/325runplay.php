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
  TestFactory::marker('play', 1, 'START: Play Run')->
    after('session', 399),
  // Initialize Run 1 for Play
  TestFactory::marker('play', 100, 'START: Initialize Run 1 on User 5:Organization 1:Project 1')->
    after('runs', 199),
  /* TEST SET 4 - Reference
   * 10 - TEST #1
   *    10 - Step 1
   *    20 - Step 2
   *    30 - Step 3
   * 15 - TEST #4
   *    10 - Step 1
   *    20 - Step 2
   *    30 - Step 3
   * 20 - TEST #7
   *    10 - Step 1
   *    20 - Step 2
   *    30 - Step 3
   */    
  // Open Run
  TestFactory::tcServiceTest('play', 110, 'testing/run/user/current')->
    after('play', 100),
  TestFactory::tcServiceTest('play', 111, 'testing/run/play/open', 1)->
    after('play', 110),
  TestFactory::tcServiceTest('play', 112, 'testing/run/user/current')->
    after('play', 111),
  // List, CountTests in Run, and Display the Current Active Test (should be 1st Test in the Run)
  TestFactory::tcServiceTest('play', 120, 'testing/run/play/tests/list')->
    after('play', 112),
  TestFactory::tcServiceTest('play', 121, 'testing/run/play/tests/count')->
    after('play', 120),
  TestFactory::tcServiceTest('play', 122, 'testing/run/play/test/current')->
    after('play', 121),
  // List, Count Steps and Display Current Active Step in the Current Active Test (should be 1st Step in the Test)
  TestFactory::tcServiceTest('play', 130, 'testing/run/play/steps/list')->
    after('play', 122),
  TestFactory::tcServiceTest('play', 131, 'testing/run/play/steps/count')->
    after('play', 130),
  TestFactory::tcServiceTest('play', 132, 'testing/run/play/step/current')->
    after('play', 131),
  TestFactory::marker('play', 199, 'END: Initialize Run 1 on User 5:Organization 1:Project 1')->
    after('play', 132)->before('project-session', 999),
  // Plays Run 1 
  TestFactory::marker('play', 200, 'START: Play Run 1')->
    after('play', 199),
  // Move Forward / Backwards
  TestFactory::tcServiceTest('play', 210, 'testing/run/play/step/next', array(1,0))->
    after('play', 200), // CURSOR POSTION TEST #1 / STEP 20
  TestFactory::tcServiceTest('play', 211, 'testing/run/play/step/current')->
    after('play', 210),
  TestFactory::tcServiceTest('play', 212, 'testing/run/play/step/previous', array(1,0))->
    after('play', 211), // CURSOR POSTION TEST #1 / STEP 10
  TestFactory::tcServiceTest('play', 213, 'testing/run/play/step/current')->
    after('play', 212),
  // Move to End of Test
  TestFactory::tcServiceTest('play', 220, 'testing/run/play/step/next', array(1,0))->
    after('play', 213), // CURSOR POSTION TEST #1 / STEP 20
  TestFactory::tcServiceTest('play', 221, 'testing/run/play/step/next', array(1,0))->
    after('play', 220), // CURSOR POSTION TEST #1 / STEP 30
  TestFactory::tcServiceTest('play', 222, 'testing/run/play/step/next', array(1,0))->
    after('play', 221), // CURSOR POSTION TEST #1 / STEP END-OF-TEST
  TestFactory::tcServiceTest('play', 223, 'testing/run/play/step/current')->
    after('play', 222),
  // Reposition Cursor
  TestFactory::tcServiceTest('play', 230, 'testing/run/play/step/position', 10)->
    after('play', 223), // CURSOR POSTION TEST #1 / STEP 10
  TestFactory::tcServiceTest('play', 231, 'testing/run/play/step/current')->
    after('play', 230),
  TestFactory::tcServiceTest('play', 232, 'testing/run/play/step/position', 30)->
    after('play', 231), // CURSOR POSTION TEST #1 / STEP 30
  TestFactory::tcServiceTest('play', 233, 'testing/run/play/step/current')->
    after('play', 232),
  TestFactory::tcServiceTest('play', 234, 'testing/run/play/step/skip', array(1,0))->
    after('play', 233), // CURSOR POSTION TEST #1 / STEP END-OF-TEST
  TestFactory::tcServiceTest('play', 235, 'testing/run/play/step/current')->
    after('play', 234),
  // Next Step / Reposition Step 
  TestFactory::tcServiceTest('play', 240, 'testing/run/play/test/next', array(1,0))->
    after('play', 235), // CURSOR POSTION TEST #2 / STEP 10
  TestFactory::tcServiceTest('play', 241, 'testing/run/play/test/current')->
    after('play', 240),
  TestFactory::tcServiceTest('play', 242, 'testing/run/play/step/current')->
    after('play', 241),
  TestFactory::tcServiceTest('play', 243, 'testing/run/play/test/position', 10)->
    after('play', 242), // CURSOR POSTION TEST #1 / STEP 10
  TestFactory::tcServiceTest('play', 244, 'testing/run/play/test/current')->
    after('play', 243),
  TestFactory::tcServiceTest('play', 245, 'testing/run/play/step/current')->
    after('play', 244),
  TestFactory::tcServiceTest('play', 246, 'testing/run/play/test/position', 15)->
    after('play', 245), // CURSOR POSTION TEST #2 / STEP 10
  // Play to End
  TestFactory::tcServiceTest('play', 250, 'testing/run/play/step/next', array(1,0))->
    after('play', 246), // CURSOR POSTION TEST #2 / STEP 20
  TestFactory::tcServiceTest('play', 251, 'testing/run/play/step/next', array(1,0))->
    after('play', 250), // CURSOR POSTION TEST #2 / STEP 30
  TestFactory::tcServiceTest('play', 252, 'testing/run/play/step/next', array(1,0))->
    after('play', 251), // CURSOR POSTION TEST #2 / STEP END-OF-TEST
  TestFactory::tcServiceTest('play', 253, 'testing/run/play/test/next', array(1,0))->
    after('play', 252), // CURSOR POSTION TEST #3 / STEP 10
  TestFactory::tcServiceTest('play', 254, 'testing/run/play/step/next', array(1,0))->
    after('play', 253), // CURSOR POSTION TEST #3 / STEP 20
  TestFactory::tcServiceTest('play', 255, 'testing/run/play/step/next', array(1,0))->
    after('play', 254), // CURSOR POSTION TEST #3 / STEP 30
  TestFactory::tcServiceTest('play', 256, 'testing/run/play/step/next', array(1,0))->
    after('play', 255), // CURSOR POSTION END-OF-RUN / END-OF-TEST
  TestFactory::tcServiceTest('play', 257, 'testing/run/play/test/current')->
    after('play', 256),
  TestFactory::tcServiceTest('play', 258, 'testing/run/play/step/current')->
    after('play', 257),
  // END: Play Run
  TestFactory::marker('play', 299, 'END: Play Run 1')->
    after('play', 258),  
  // Close Run 1 
  TestFactory::marker('play', 300, 'START: Close Run 1')->
    after('play', 299),
  TestFactory::tcServiceTest('play', 310, 'testing/run/play/close', array(1,0))->
    after('play', 300), // CURSOR POSTION TEST #2 / STEP 20
  TestFactory::marker('play', 399, 'END: Close Run 1')->
    after('play', 310),
  // END: Play Run
  TestFactory::marker('play', 999, 'END: Play a Run')->
    after('play', 399)->before('runs', 900),
 );
?>
