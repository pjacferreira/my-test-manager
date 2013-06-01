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

$SKIP=true; // Skip the File for Current Test

$TESTS = array(
  // Set the Session Project
  TestFactory::tcServiceTest('user-project', 10, 'session/project/get', null, null, false),  // FAIL EXPECTED (No Project Set)
  TestFactory::tcServiceTest('user-project', 11, 'session/project/set', array(1)),
  TestFactory::tcServiceTest('user-project', 12, 'session/project/get'),
);
?>
