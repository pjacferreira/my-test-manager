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
  // START: Organization Tests
  TestFactory::marker('organizations', 1, 'START: Organization Tests')->
    after('session', 199),
  // CREATE ORGANIZATIONS
  TestFactory::marker('organizations', 100, 'START: Create Organizations')->
    after('organizations', 1),
  TestFactory::tcServiceTest('organizations', 110, 'manage/org/create', 'organization 1')->
    after('organizations', 100),
  TestFactory::tcServiceTest('organizations', 111, 'manage/org/create', 'organization 2', array('description' => 'Test Organization 2'))->
    after('organizations', 110),
  TestFactory::tcServiceTest('organizations', 112, 'manage/org/create', 'organization 3')->
    after('organizations', 111),
  TestFactory::tcServiceTest('organizations', 113, 'manage/org/create', 'organization 4')->
    after('organizations', 112),
  TestFactory::tcServiceTest('organizations', 114, 'manage/org/create', 'organization 5')->
    after('organizations', 113),
  TestFactory::marker('organizations', 199, 'END: Create Organizations')->
    after('organizations', 114),
  // ORGANIZATION MODIFICATIONS
  TestFactory::marker('organizations', 200, 'START: Organization Modifications')->
    after('organizations', 199),
  // Read/Update Organizations
  TestFactory::tcServiceTest('organizations', 210, 'manage/org/update', 1, array('description' => 'Test Organization 1'))->
    after('organizations', 200),
  TestFactory::tcServiceTest('organizations', 211, 'manage/org/read', 1)->
    after('organizations', 210),
  TestFactory::tcServiceTest('organizations', 215, 'manage/org/update', 'organization 3', array('description' => 'Test Organization 3'))->
    after('organizations', 211),
  TestFactory::tcServiceTest('organizations', 216, 'manage/org/read', 'organization 3')->
    after('organizations', 215),
  // List and Count Organizations
  TestFactory::tcServiceTest('organizations', 220, 'manage/orgs/list')->
    after('organizations', 216),
  TestFactory::tcServiceTest('organizations', 221, 'manage/orgs/count')->
    after('organizations', 220),
  TestFactory::marker('organizations', 299, 'END: Organization Modifications')->
    after('organizations', 221)->before('session', 200),
  // ORGANIZATION CLEANUP
  TestFactory::marker('organizations', 900, 'START: Organization Cleanup')->
    after('organizations', 299)->after('session', 899),
  TestFactory::tcServiceTest('organizations', 910, 'manage/org/delete', 1)->
    after('organizations', 900),
  TestFactory::tcServiceTest('organizations', 911, 'manage/org/delete', 'organization 2')->
    after('organizations', 910),
  TestFactory::tcServiceTest('organizations', 912, 'manage/org/delete', 3)->
    after('organizations', 911),
  TestFactory::tcServiceTest('organizations', 913, 'manage/org/delete', 4)->
    after('organizations', 912),
  TestFactory::tcServiceTest('organizations', 914, 'manage/org/delete', 5)->
    after('organizations', 913),
  // Organizations List and Count
  TestFactory::tcServiceTest('organizations', 920, 'manage/orgs/list')->
    after('organizations', 914),
  TestFactory::tcServiceTest('organizations', 921, 'manage/orgs/count')->
    after('organizations', 920),
  TestFactory::marker('organizations', 929, 'END: Organization Cleanup')->
    after('organizations', 921)->before('session', 900),
  // TEST NO SESSION
  TestFactory::marker('organizations', 950, 'PRE NO SESSION ACCESS ATTEMPTS')->
    after('organizations', 929)->after('session', 920),
  TestFactory::tcServiceTest('organizations', 960, 'manage/org/create', 'organization 6', array('description' => 'Test Organization 6'), false)->
    after('organizations', 950),
  TestFactory::tcServiceTest('organizations', 961, 'manage/org/read', 6, null, false)->
    after('organizations', 960),
  TestFactory::tcServiceTest('organizations', 962, 'manage/org/update', 6, array('description' => 'Test Organization 6'), false)->
    after('organizations', 961),
  TestFactory::tcServiceTest('organizations', 963, 'manage/org/delete', 6, null, false)->
    after('organizations', 962),
  TestFactory::tcServiceTest('organizations', 964, 'manage/orgs/list', null, null, false)->
    after('organizations', 963),
  TestFactory::tcServiceTest('organizations', 965, 'manage/orgs/count', null, null, false)->
    after('organizations', 964),
  TestFactory::marker('organizations', 969, 'POST NO SESSION ACCESS ATTEMPTS')->
    after('organizations', 965),
  // END: Organization Tests
  TestFactory::marker('organizations', 999, 'END: Organization Tests')->
    after('organizations', 969)->before('session', 999),
);
?>
