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
  // START: Organization<-->Session Tests
  TestFactory::marker('org-session', 1, 'START: Organization<-->Session Tests')->
    after('user-session', 1)->after('organizations', 299),
  // NO ORGANIZATION SET for SESSION
  TestFactory::marker('org-session', 100, 'START: No Session Organization')->
    after('org-session', 1),
  TestFactory::tcServiceTest('org-session', 110, 'session/org/current', null, null, false)->
    after('org-session', 100),
  TestFactory::marker('org-session', 199, 'END: No Session Organization')->
    after('org-session', 110),
  // SESSION ORGANIZATION 1 (User 2)
  TestFactory::marker('org-session', 200, 'START: Session User 2:Organization 1')->
    after('org-session', 199)->after('user-session', 199),
  TestFactory::tcServiceTest('org-session', 210, 'session/org/change', 1)->
    after('org-session', 200),
  TestFactory::tcServiceTest('org-session', 211, 'session/org/current')->
    after('org-session', 210),
  TestFactory::tcServiceTest('org-session', 212, 'session/org/permissions')->
    after('org-session', 211),
  TestFactory::tcServiceTest('org-session', 213, 'session/org/projects/list')->
    after('org-session', 212),
  TestFactory::tcServiceTest('org-session', 214, 'session/org/projects/count')->
    after('org-session', 213),
  TestFactory::marker('org-session', 299, 'END: Session User 2:Organization 1')->
    after('org-session', 214)->before('user-session', 200),
  // SESSION ORGANIZATION 4 (User 4)
  TestFactory::marker('org-session', 300, 'START: Session User 4:Organization 1')->
    after('user-session', 399),
  TestFactory::tcServiceTest('org-session', 310, 'session/org/change', 1)->
    after('org-session', 300),
  TestFactory::tcServiceTest('org-session', 311, 'session/org/current')->
    after('org-session', 310),
  TestFactory::tcServiceTest('org-session', 312, 'session/org/permissions')->
    after('org-session', 311),
  TestFactory::tcServiceTest('org-session', 313, 'session/org/projects/list')->
    after('org-session', 312),
  TestFactory::tcServiceTest('org-session', 314, 'session/org/projects/count')->
    after('org-session', 313),
  TestFactory::marker('org-session', 399, 'END: Session User 4:Organization 1')->
    after('org-session', 314)->before('user-session', 400),
  // SESSION ORGANIZATION 4 (User 5)
  TestFactory::marker('org-session', 400, 'START: Session User 5:Organization 1')->
    after('user-session', 499),
  TestFactory::tcServiceTest('org-session', 410, 'session/org/change', 1)->
    after('org-session', 400),
  TestFactory::tcServiceTest('org-session', 411, 'session/org/current')->
    after('org-session', 410),
  TestFactory::tcServiceTest('org-session', 412, 'session/org/permissions')->
    after('org-session', 411),
  TestFactory::tcServiceTest('org-session', 413, 'session/org/projects/list')->
    after('org-session', 412),
  TestFactory::tcServiceTest('org-session', 414, 'session/org/projects/count')->
    after('org-session', 413),
  TestFactory::marker('org-session', 499, 'END: Session User 5:Organization 1')->
    after('org-session', 414),
  // END: Organization<-->Session Tests
  TestFactory::marker('org-session', 999, 'END: Organization<-->Session Tests')->
    after('org-session', 299)->after('org-session', 399)->after('org-session', 499)->
    before('user-session', 999)
);
?>
