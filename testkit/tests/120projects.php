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
  // START: Project Tests
  TestFactory::marker('projects', 1, 'START: Projects Tests')->
    after('user-org', 199),
  // CREATE ORGANIZATIONS
  TestFactory::marker('projects', 100, 'START: Create Projects')->
    after('projects', 1),
  TestFactory::tcServiceTest('projects', 110, 'project/create', array(1, 'project O1-1'))->
    after('projects', 100),
  TestFactory::tcServiceTest('projects', 120, 'project/create', array(2, 'project O2-1'), array('project:description' => 'Organization 2 - Project 1'))->
    after('projects', 110),
  TestFactory::tcServiceTest('projects', 130, 'project/create', array(3, 'project O3-1'))->
    after('projects', 120),
  TestFactory::tcServiceTest('projects', 140, 'project/create', array(4, 'project O4-1'), array('project:description' => 'Organization 4 - Project 1'))->
    after('projects', 130),
  TestFactory::tcServiceTest('projects', 150, 'project/create', array(5, 'project O5-1'), array('project:description' => 'Organization 5 - Project 1'))->
    after('projects', 140),
  TestFactory::marker('projects', 199, 'END: Create Projects')->
    after('projects', 150)->before('session', 200),
  // ORGANIZATION SESSION : Set Current Session Project
  TestFactory::marker('projects', 200, 'START: Session Project')->
    after('projects', 199),
  TestFactory::tcServiceTest('projects', 210, 'session/set/project', 2)->
          after('projects', 200),
  TestFactory::tcServiceTest('projects', 211, 'session/get/project')->
          after('projects', 210),
  TestFactory::marker('projects', 299, 'END: Session Project')->
    after('projects', 211),
  // PROJECT MODIFICATIONS
  TestFactory::marker('projects', 300, 'START: Project Modifications')->
    after('projects', 299),
  // Read/Update Projects
  TestFactory::tcServiceTest('projects', 310, 'project/update', 1, array('project:description' => 'Organization 1 - Project 1'))->
    after('projects', 300),
  TestFactory::tcServiceTest('projects', 311, 'project/read', 1)->
    after('projects', 310),
  TestFactory::tcServiceTest('projects', 315, 'project/update', 'project O3-1', array('project:description' => 'Organization 3 - Project 1'))->
    after('projects', 311),
  TestFactory::tcServiceTest('projects', 316, 'project/read', 'project O3-1')->
    after('projects', 315),
  // List and Count All Projects
  TestFactory::tcServiceTest('projects', 320, 'projects/list')->
    after('projects', 316),
  TestFactory::tcServiceTest('projects', 321, 'projects/count')->
    after('projects', 320),
  // List and Count Projects for a Specific Organization
  TestFactory::tcServiceTest('projects', 330, 'org/projects/list', 1)->
    after('projects', 321),
  TestFactory::tcServiceTest('projects', 331, 'org/projects/count', 1)->
    after('projects', 330),
  TestFactory::marker('projects', 399, 'END: Project Modifications')->
    after('projects', 331)->before('session', 200),
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
  TestFactory::tcServiceTest('projects', 920, 'projects/list')->
    after('projects', 914),
  TestFactory::tcServiceTest('projects', 921, 'projects/count')->
    after('projects', 920),
  TestFactory::tcServiceTest('projects', 930, 'org/projects/list', 1)->
    after('projects', 921),
  TestFactory::tcServiceTest('projects', 931, 'org/projects/count', 1)->
    after('projects', 930),
  TestFactory::marker('projects', 949, 'END: Project Cleanup')->
    after('projects', 931)->before('organizations', 900),
  // END: Project Tests
  TestFactory::marker('projects', 999, 'END: Project Tests')->
    after('projects', 949)->before('session', 999),
);
?>
