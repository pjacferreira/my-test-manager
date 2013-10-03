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
  // START: Project Tests
  TestFactory::marker('projects', 1, 'START: Projects Tests')->
    after('organizations', 299),
  // CREATE ORGANIZATIONS
  TestFactory::marker('projects', 100, 'START: Create Projects')->
    after('projects', 1),
  TestFactory::tcServiceTest('projects', 110, 'manage/org/project/create', array(1, 'project O1-1'))->
    after('projects', 100),
  TestFactory::tcServiceTest('projects', 120, 'manage/org/project/create', array(2, 'project O2-1'), array('description' => 'Organization 2 - Project 1'))->
    after('projects', 110),
  TestFactory::tcServiceTest('projects', 130, 'manage/org/project/create', array(3, 'project O3-1'))->
    after('projects', 120),
  TestFactory::tcServiceTest('projects', 140, 'manage/org/project/create', array(4, 'project O4-1'), array('description' => 'Organization 4 - Project 1'))->
    after('projects', 130),
  TestFactory::tcServiceTest('projects', 150, 'manage/org/project/create', array(5, 'project O5-1'), array('description' => 'Organization 5 - Project 1'))->
    after('projects', 140),
  TestFactory::marker('projects', 199, 'END: Create Projects')->
    after('projects', 150),
  // PROJECT MODIFICATIONS
  TestFactory::marker('projects', 200, 'START: Project Modifications')->
    after('projects', 199),
  // Read/Update Projects
  TestFactory::tcServiceTest('projects', 210, 'manage/project/update', 1, array('description' => 'Organization 1 - Project 1'))->
    after('projects', 200),
  TestFactory::tcServiceTest('projects', 211, 'manage/project/read', 1)->
    after('projects', 210),
  TestFactory::tcServiceTest('projects', 215, 'manage/project/update', 'project O3-1', array('description' => 'Organization 3 - Project 1'))->
    after('projects', 211),
  TestFactory::tcServiceTest('projects', 216, 'manage/project/read', 'project O3-1')->
    after('projects', 215),
  // List and Count All Projects
  TestFactory::tcServiceTest('projects', 220, 'manage/projects/list')->
    after('projects', 216),
  TestFactory::tcServiceTest('projects', 221, 'manage/projects/count')->
    after('projects', 220),
  // List and Count Projects for a Specific Organization
  TestFactory::tcServiceTest('projects', 230, 'manage/org/projects/list', 1)->
    after('projects', 221),
  TestFactory::tcServiceTest('projects', 231, 'manage/org/projects/count', 1)->
    after('projects', 230),
  TestFactory::marker('projects', 299, 'END: Project Modifications')->
    after('projects', 231)->before('session', 200),
  // PROJECT CLEANUP
  TestFactory::marker('projects', 900, 'START: Project Cleanup')->
    after('projects', 299)->after('session', 899),
  // Delete Projects (Organization 1)
  TestFactory::tcServiceTest('projects', 910, 'project/delete', 1)->
    after('projects', 900),
  TestFactory::tcServiceTest('projects', 911, 'project/delete', 'project O2-1')->
    after('projects', 910),
  TestFactory::tcServiceTest('projects', 912, 'project/delete', 3)->
    after('projects', 911),
  TestFactory::tcServiceTest('projects', 913, 'project/delete', 'project O4-1')->
    after('projects', 912),
  TestFactory::tcServiceTest('projects', 914, 'project/delete', 5)->
    after('projects', 913),
  TestFactory::tcServiceTest('projects', 920, 'manage/projects/list')->
    after('projects', 914),
  TestFactory::tcServiceTest('projects', 921, 'manage/projects/count')->
    after('projects', 920),
  TestFactory::tcServiceTest('projects', 930, 'manage/org/projects/list', 1)->
    after('projects', 921),
  TestFactory::tcServiceTest('projects', 931, 'manage/org/projects/count', 1)->
    after('projects', 930),
  TestFactory::marker('projects', 949, 'END: Project Cleanup')->
    after('projects', 931)->before('organizations', 900),
  // END: Project Tests
  TestFactory::marker('projects', 999, 'END: Project Tests')->
    after('projects', 949)->before('session', 999),
);
?>
