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
  // START: Organization<-->Session Tests
  TestFactory::marker('org-session', 1, 'Organization<-->Session Tests : START')->
    after('session', 1),
  // NO ORGANIZATION SET for SESSION
  TestFactory::marker('org-session', 100, 'PRE No Session Organization')->
    after('session', 199),
  TestFactory::tcServiceTest('org-session', 110, 'session/org/get', null, null, false)->
    after('org-session', 100),
  TestFactory::marker('org-session', 199, 'POST No Session Organization')->
    after('org-session', 110)->before('organizations', 100),
  // SESSION ORGANIZATION 1
  TestFactory::marker('org-session', 200, 'PRE Session Organization 1')->
    after('organizations', 299),
  TestFactory::tcServiceTest('org-session', 210, 'session/org/set', 1)->
    after('org-session', 200),
  TestFactory::marker('org-session', 299, 'POST Session Organization 1')->
    after('org-session', 210)->before('organizations', 900),
  // SESSION ORGANIZATION 2
  TestFactory::marker('org-session', 300, 'PRE Session Organization 2')->
    after('org-session', 299),
  TestFactory::tcServiceTest('org-session', 310, 'session/org/set', 2)->
    after('org-session', 300),
  TestFactory::marker('org-session', 399, 'POST Session Organization 2')->
    after('org-session', 310)->before('organizations', 900),
  // SESSION ORGANIZATION 3
  TestFactory::marker('org-session', 400, 'PRE Session Organization 3')->
    after('org-session', 399),
  TestFactory::tcServiceTest('org-session', 410, 'session/org/set', 3)->
    after('org-session', 400),
  TestFactory::marker('org-session', 499, 'POST Session Organization 3')->
    after('org-session', 410)->before('organizations', 900),
  // SESSION ORGANIZATION 4
  TestFactory::marker('org-session', 500, 'PRE Session Organization 4')->
    after('org-session', 499),
  TestFactory::tcServiceTest('org-session', 510, 'session/org/set', 4)->
    after('org-session', 500),
  TestFactory::marker('org-session', 599, 'POST Session Organization 4')->
    after('org-session', 510)->before('organizations', 900),
  // SESSION ORGANIZATION 5
  TestFactory::marker('org-session', 600, 'PRE Session Organization 5')->
    after('org-session', 599),
  TestFactory::tcServiceTest('org-session', 610, 'session/org/set', 5)->
    after('org-session', 600),
  TestFactory::marker('org-session', 699, 'POST Session Organization 5')->
    after('org-session', 610)->before('organizations', 900),
  // END: Organization<-->Session Tests
  TestFactory::marker('org-session', 999, 'Organization<-->Session Tests : END')->
    after('org-session', 699),
);
?>
