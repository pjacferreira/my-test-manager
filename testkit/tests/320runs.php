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
  TestFactory::marker('runs', 1, 'START: Run Creation')->
    after('session', 399),
  // CREATE RUNS for User 5:Organization 1:Project 1
  TestFactory::marker('runs', 100, 'START: Create Runs based on User 5:Organization 1:Project 1:Set 4')->
    after('set-steps', 199),
  // RUNS CREATION
  TestFactory::tcServiceTest('runs', 110, 'testing/run/create', array(4, 'Run U2:O1:P1:S4-1'))->
    after('runs', 100),
  TestFactory::tcServiceTest('runs', 111, 'testing/run/create', array(4, 'Run U2:O1:P1:S4-2'))->
    after('runs', 110),
  // Read/Update Projects
  TestFactory::tcServiceTest('runs', 120, 'testing/run/update', 2, array('name' => 'Run U2:O1:P1:S4-2 UPDATED'))->
    after('runs', 111),
  TestFactory::tcServiceTest('runs', 121, 'testing/run/read', 2)->
    after('runs', 120),
  // List and Count Tests (ALL)
  TestFactory::tcServiceTest('runs', 130, 'testing/runs/list')->
    after('runs', 121),
  TestFactory::tcServiceTest('runs', 131, 'testing/runs/count')->
    after('runs', 130),
  // List and Count Tests (CURRENT SESSION USER)
  TestFactory::tcServiceTest('runs', 140, 'testing/runs/user/list')->
    after('runs', 131),
  TestFactory::tcServiceTest('runs', 141, 'testing/runs/user/count')->
    after('runs', 140),
  TestFactory::marker('runs', 199, 'END: Create Runs based on User 5:Organization 4:Project 1:Set 4')->
    after('runs', 141)->before('project-session', 999),
  // RUNS CLEANUP
  TestFactory::marker('runs', 900, 'START: Runs CLEANUP')->
    after('runs', 199)->before('org-session', 999),
  TestFactory::tcServiceTest('runs', 910, 'testing/run/delete', 1)->
    after('runs', 900),
  TestFactory::tcServiceTest('runs', 911, 'testing/run/delete', 2)->
    after('runs', 910),
   // END: Run Creation
  TestFactory::marker('runs', 999, 'END: Run Creation')->
    after('runs', 911)->before('organizations', 900),
 );
?>
