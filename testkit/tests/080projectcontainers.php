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
  // List/Count Current Containers for Project
  TestFactory::tcServiceTest('project-containers', 10, 'project/container/cwd')->after('project-session', 20), // Project Current Working Directory/Container
  TestFactory::tcServiceTest('project-containers', 11, 'project/container/count')->after('project-containers', 10), // Project CWD Entry Count
  TestFactory::tcServiceTest('project-containers', 12, 'project/container/ls')->after('project-containers', 11), // Project CWD Listing
  // Create Child Containers
  TestFactory::tcServiceTest('project-containers', 20, 'project/container/mkdir', 'folder 1')->after('project-containers', 12), // Create Child Container
  TestFactory::tcServiceTest('project-containers', 21, 'project/container/mkdir', 'folder 2')->after('project-containers', 20), // Create Child Container
  TestFactory::tcServiceTest('project-containers', 25, 'project/container/count')->after('project-containers', 21), // Project CWD Entry Count
  TestFactory::tcServiceTest('project-containers', 26, 'project/container/ls')->after('project-containers', 25), // Project CWD Listing
  // Work with Folder 1
  TestFactory::tcServiceTest('project-containers', 30, 'project/container/cd', 'folder 1')->after('project-containers', 26), // Change to Child Container
  TestFactory::tcServiceTest('project-containers', 31, 'project/container/mkdir', 'folder 1-1')->after('project-containers', 30), // Create Child Container
  TestFactory::tcServiceTest('project-containers', 35, 'project/container/cwd')->after('project-containers', 31), // Project Current Working Directory/Container
  TestFactory::tcServiceTest('project-containers', 36, 'project/container/count')->after('project-containers', 35), // Project CWD Entry Count
  TestFactory::tcServiceTest('project-containers', 37, 'project/container/ls')->after('project-containers', 36)->before('project-session', 30), // Project CWD Listing
  // Return to Root Director
  TestFactory::tcServiceTest('project-containers', 40, 'project/container/root')->after('project-containers', 37), // Go Back to Root Directory
  TestFactory::tcServiceTest('project-containers', 41, 'project/container/cwd')->after('project-containers', 40), // Project Current Working Directory/Container
  // Marker Organizations Created
  TestFactory::marker('project-containers', 900, "Container Cleanup")->after('project-containers', 41),
);
?>