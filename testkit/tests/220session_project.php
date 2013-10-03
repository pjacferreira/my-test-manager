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
  // START: Project<-->Session Tests
  TestFactory::marker('project-session', 1, 'START: Project<-->Session Tests')->
    after('org-session', 199),
  // NO PROJECT SET for SESSION
  TestFactory::marker('project-session', 100, 'START: No Session Project')->
    after('project-session', 1),
  TestFactory::tcServiceTest('project-session', 110, 'session/project/current', null, null, false)->
    after('project-session', 100),
  TestFactory::marker('project-session', 199, 'END: No Session Project')->
    after('project-session', 110),
  // SESSION PROJECT 1 (User 2) 
  TestFactory::marker('project-session', 200, 'START: Session User 2:Organization 1:Project 1')->
    after('project-session', 199)->after('org-session', 299),
  TestFactory::tcServiceTest('project-session', 210, 'session/project/change', 1)->
    after('project-session', 200),
  TestFactory::tcServiceTest('project-session', 211, 'session/project/current')->
    after('project-session', 210),
  TestFactory::tcServiceTest('project-session', 212, 'session/project/permissions')->
    after('project-session', 211),
  TestFactory::tcServiceTest('project-session', 213, 'session/project/projects/list')->
    after('project-session', 212),
  TestFactory::tcServiceTest('project-session', 214, 'session/project/projects/count')->
    after('project-session', 213),
  TestFactory::marker('project-session', 299, 'END: Session User 2:Organization 1:Project 1')->
    after('project-session', 214)->before('org-session', 300),
  // SESSION PROJECT 4 (User 4)
  TestFactory::marker('project-session', 300, 'START: Session User 4:Organization 1:Project 1')->
    after('org-session', 399),
  TestFactory::tcServiceTest('project-session', 310, 'session/project/change', 1)->
    after('project-session', 300),
  TestFactory::tcServiceTest('project-session', 311, 'session/project/current')->
    after('project-session', 310),
  TestFactory::tcServiceTest('project-session', 312, 'session/project/permissions')->
    after('project-session', 311),
  TestFactory::tcServiceTest('project-session', 313, 'session/project/projects/list')->
    after('project-session', 312),
  TestFactory::tcServiceTest('project-session', 314, 'session/project/projects/count')->
    after('project-session', 313),
  TestFactory::marker('project-session', 399, 'END: Session User 4:Organization 1:Project 1')->
    after('project-session', 314)->before('user-session', 400),
  // SESSION PROJECT 4 (User 5)
  TestFactory::marker('project-session', 400, 'START: Session User 5:Organization 1:Project 1')->
    after('org-session', 499),
  TestFactory::tcServiceTest('project-session', 410, 'session/project/change', 1)->
    after('project-session', 400),
  TestFactory::tcServiceTest('project-session', 411, 'session/project/current')->
    after('project-session', 410),
  TestFactory::tcServiceTest('project-session', 412, 'session/project/permissions')->
    after('project-session', 411),
  TestFactory::tcServiceTest('project-session', 413, 'session/project/projects/list')->
    after('project-session', 412),
  TestFactory::tcServiceTest('project-session', 414, 'session/project/projects/count')->
    after('project-session', 413),
  TestFactory::marker('project-session', 499, 'END: Session User 5:Organization 1:Project 1')->
    after('project-session', 414),
  // END: Project<-->Session Tests
  TestFactory::marker('project-session', 999, 'END: Project<-->Session Tests')->
    after('project-session', 299)->after('project-session', 399)->after('project-session', 499)->
    before('user-session', 999)
);
?>
