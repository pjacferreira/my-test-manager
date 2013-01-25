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

$TESTS = array(
  // START: Session Tests
  TestFactory::marker('session', 1, 'Session Tests : START'),
  // LOGIN ADMIN
  TestFactory::marker('session', 100, 'PRE Admin Login')->
    after('session', 1),
  TestFactory::tcServiceTest('session', 110, 'session/login', 'admin/admin')->
    after('session', 100),
  TestFactory::tcServiceTest('session', 111, 'session/whoami')->
    after('session', 110),
  TestFactory::marker('session', 199, 'POST Admin Login')->
    after('session', 110),
  // LOGOUT ADMIN
  TestFactory::marker('session', 900, 'PRE Admin Logout')->
    after('session', 199),
  TestFactory::tcServiceTest('session', 910, 'session/logout')->
    after('session', 900),
  TestFactory::marker('session', 920, 'POST Admin Logout')->
    after('session', 910),
  // END: Session Tests
  TestFactory::marker('session', 999, 'Session Tests : END')->
    after('session', 920),
);
?>
