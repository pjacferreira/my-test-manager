<?php

// $SKIP=true; // Skip the File for Current Test

/*
$TESTS = array(
  // Create Tests
  createJSONServiceTest('project-tests', 10, after('project-containers', 20), 'project/test/create', array('test 1')), // Create Test 1
  createJSONServiceTest('project-tests', 11, after('project-tests', 10), 'project/test/create', array('test 2')), // Create Test 2
  createJSONServiceTest('project-tests', 12, after('project-tests', 11, before('project-containers', 30)), 'project/test/create', array('test 3')), // Create Test 3
  createJSONServiceTest('project-tests', 15, after('project-containers', 30), 'project/test/create', array('test 4')), // Create Test 4
  createJSONServiceTest('project-tests', 16, after('project-tests', 15), 'project/test/create', array('test 5')), // Create Test 5
  createJSONServiceTest('project-tests', 17, after('project-tests', 16, before('project-containers', 40)), 'project/test/create', array('test 6')), // Create Test 6
  // Get and Update Test Information
  createJSONServiceTest('project-tests', 20, after('project-tests', 10), 'project/test/read/1'), // Get Information for Test 1
  createJSONServiceTest('project-tests', 21, after('project-tests', 10, before('project-containers', 30)), 'project/test/read', array('test 1')), // Get Information for Test 1
  createJSONServiceTest('project-tests', 22, after('project-tests', 15, before('project-containers', 40)), 'project/test/update', array(3, 'description=description test 3')), // Update Test 3
  // Created Tests
  createMark('project-tests', 30, after('project-tests', 22), 'Tests Created'),
  // Create Test Steps
  createJSONServiceTest('project-tests', 40, after('project-tests', 30), 'project/test/step/add', array(1, 'Step 1-1')),
  createJSONServiceTest('project-tests', 41, after('project-tests', 40), 'project/test/step/add', array(1, 'Step 1-2')),
  createJSONServiceTest('project-tests', 42, after('project-tests', 41), 'project/test/step/add', array(1, 'Step 1-3', 10)),
  createJSONServiceTest('project-tests', 45, after('project-tests', 30), 'project/test/step/add', array(2, 'Step 2-1')),
  createJSONServiceTest('project-tests', 46, after('project-tests', 45), 'project/test/step/add', array(2, 'Step 2-2')),
  createJSONServiceTest('project-tests', 47, after('project-tests', 46), 'project/test/step/add', array(2, 'Step 2-3')),
  createJSONServiceTest('project-tests', 48, after('project-tests', 47), 'project/test/step/add', array(3, 'Step 3-1')),
  createJSONServiceTest('project-tests', 49, after('project-tests', 48), 'project/test/step/add', array(3, 'Step 3-2')),
  // Created Tests
  createMark('project-tests', 50, after('project-tests', 42, after('project-tests', 47)), 'Steps Created'),
  // List and Count Tests
  createJSONServiceTest('project-tests', 60, after('project-tests', 50), 'project/test/list'),
  createJSONServiceTest('project-tests', 61, after('project-tests', 60), 'project/test/count'),
  // Renumber Steps
  createJSONServiceTest('project-tests', 70, after('project-tests', 50), 'project/test/step/renumber', array(1, 10)),
  createJSONServiceTest('project-tests', 71, after('project-tests', 70), 'project/test/step/renumber', array(2, 100)),
  // Delete Steps
  createJSONServiceTest('project-tests', 80, after('project-tests', 71), 'project/test/step/remove', array(2, 300)),
  // Move Steps
  createJSONServiceTest('project-tests', 90, after('project-tests', 71), 'project/test/step/move', array(2, 100, 1)),
  // Update Steps
  createJSONServiceTest('project-tests', 100, after('project-tests', 90), 'project/test/step/update', array(2, 1, 'description=Step 2-1 Moved')),
  // Move List/Count Steps
  createJSONServiceTest('project-tests', 110, after('project-tests', 100), 'project/test/step/list', array(2)),
  createJSONServiceTest('project-tests', 111, after('project-tests', 110), 'project/test/step/count', array(2)),
  // Marker Tests Cleanup
  createMark('project-tests', 900, after('project-tests', 111, before('project-session',30)), 'Test Cleanup'),
  createJSONServiceTest('project-tests', 901, after('project-tests', 900), 'project/test/delete', array(1)),
  createJSONServiceTest('project-tests', 902, after('project-tests', 901), 'project/test/delete', array(2)),
  createJSONServiceTest('project-tests', 903, after('project-tests', 902), 'project/test/delete', array(3)),
  createJSONServiceTest('project-tests', 904, after('project-tests', 903), 'project/test/delete', array(4)),
  createJSONServiceTest('project-tests', 905, after('project-tests', 904), 'project/test/delete', array(5)),
  createJSONServiceTest('project-tests', 906, after('project-tests', 905, before('project-containers', 900)), 'project/test/delete', array(6)),
);
*/
?>
