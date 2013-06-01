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

$SKIP = false; // Skip the File for Current Test

$TESTS = array(
  // START: Project Tests
  TestFactory::marker('projects', 1, 'Projects Tests : START')->
    after('session', 1),
  // CREATE Projects
  TestFactory::marker('projects', 100, 'PRE Project Create')->
    after('session', 199),
  TestFactory::tcServiceTest('projects', 110, 'session/project/get', null, null, false)->
    after('projects', 100)->before('org-session', 199),
  // ** SESSION SPECIFIC TESTS
  // Projects - Session Organization (1)
  TestFactory::marker('projects', 200, 'PRE Create Projects in Session Organization (1)')->
    after('projects', 110)->after('org-session', 299),
  TestFactory::tcServiceTest('projects', 210, 'org/projects/create', 'project O1-1')->
        after('org-session', 299), // Project 1
  TestFactory::tcServiceTest('projects', 211, 'org/projects/create', 'project O1-2',
                             'description=Test Project 2 Organization 1')->
        after('projects', 210),
  TestFactory::tcServiceTest('projects', 212, 'org/projects/create', 'project O1-3')->
        after('projects', 211),    
  TestFactory::marker('projects', 299, 'POST Create Projects in Session Organization (1)')->
    after('projects', 212)->before('org-session', 300),
  // Projects - Other Tests
  TestFactory::marker('projects', 300, 'PRE Other Project Session Tests')->
    after('projects', 299),
  TestFactory::tcServiceTest('projects', 310, 'org/projects/list')->
    after('projects', 300),
  TestFactory::tcServiceTest('projects', 311, 'org/count')->
    after('projects', 310),
  TestFactory::marker('projects', 399, 'POST Create Projects in Session Organization (1)')->
    after('projects', 311)->before('org-session', 300),    
  // ** SESSION INDEPENDENT TESTS
  // Projects - Create Other Organizations
  TestFactory::marker('projects', 400, 'PRE Create Projects in Specific Organization')->
    after('projects', 399)->after('org-session', 699),
  TestFactory::tcServiceTest('projects', 410, 'project/create', array(2, 'project O2-1'))->
    after('projects', 400),
  TestFactory::tcServiceTest('projects', 411, 'project/create', array(3, 'project O3-1'),
                             'description=Test Project 5 Organization 3')->
    after('projects', 410), 
  TestFactory::tcServiceTest('projects', 412, 'project/create', array(4, 'project O4-1'))->
    after('projects', 411), 
  TestFactory::tcServiceTest('projects', 413, 'project/create', array(5, 'project O5-1'))->
    after('projects', 412), 
  TestFactory::marker('projects', 499, 'POST Create Projects in Specific Organization')->
    after('projects', 413),
  // Projects - Other Tests
  TestFactory::marker('projects', 500, 'PRE Other Projects Tests Session Organization Independent')->
    after('projects', 499)->after('org-session', 699),
  // Read Project Data
  TestFactory::tcServiceTest('projects', 510, 'project/read', 1)->
    after('projects', 500),
  TestFactory::tcServiceTest('projects', 511, 'project/read', 'project O1-2')->
    after('projects', 510),
  TestFactory::tcServiceTest('projects', 520, 'project/update', 1, 
          'description=Test Project 1 Organization 1')->
    after('projects', 511),
  TestFactory::tcServiceTest('projects', 521, 'project/read', 1)->
    after('projects', 520),    
  TestFactory::tcServiceTest('projects', 530, 'org/projects/list', 2)->
    after('projects', 521),
  TestFactory::tcServiceTest('projects', 531, 'org/projects/count', 2)->
    after('projects', 530),    
  TestFactory::marker('projects', 599, 'POST Other Projects Tests Session Organization Independent')->
    after('projects', 531),
  // ORGANIZATION CLEANUP
  TestFactory::marker('projects', 900, 'PRE PROJECT CLEANUP')->
    after('projects', 599)->before('org-session', 999),
  // Delete Projects (Organization 1)
  TestFactory::tcServiceTest('projects', 910, 'project/delete', 1)->
    after('projects', 900),
  TestFactory::tcServiceTest('projects', 911, 'project/delete', 2)->
    after('projects', 910),
  TestFactory::tcServiceTest('projects', 912, 'project/delete', 'project O1-3')->
    after('projects', 911),
  TestFactory::tcServiceTest('projects', 913, 'project/delete', 4)->
    after('projects', 912),
  TestFactory::tcServiceTest('projects', 914, 'project/delete', 5)->
    after('projects', 913),
  TestFactory::tcServiceTest('projects', 915, 'project/delete', 6)->
    after('projects', 914),
  TestFactory::tcServiceTest('projects', 916, 'project/delete', 7)->
    after('projects', 915),
  TestFactory::tcServiceTest('projects', 920, 'org/projects/list', 1)->
    after('projects', 916),
  TestFactory::tcServiceTest('projects', 921, 'org/projects/count', 1)->
    after('projects', 920),
  // END: Organization Tests
  TestFactory::marker('projects', 999, 'Project Tests : END')->
    after('projects', 921)->before('organizations', 900),
);
?>
