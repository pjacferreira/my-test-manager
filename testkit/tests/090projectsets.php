<?php

// $SKIP=true; // Skip the File for Current Test

/*
$TESTS = array(
  // Create Test Set
  createJSONServiceTest('project-sets', 10, after('project-tests', 20), 'project/set/create', array('Test Set 1')),
  createJSONServiceTest('project-sets', 11, after('project-sets', 10), 'project/set/create', array('Test Set 2')),
  // Marker Sets Created
  createMark('project-sets', 20, after('project-sets', 11), "Test Sets Created"),
  // Add Tests to Set
  createJSONServiceTest('project-sets', 30, after('project-sets', 20, after('project-tests', 50)), 'project/set/link/add', array(1, 1)), // Add Test 1 to Set 1
  createJSONServiceTest('project-sets', 31, after('project-sets', 30), 'project/set/link/add', array(1, 2)), // Add Test 2 to Set 1
  createJSONServiceTest('project-sets', 32, after('project-sets', 31), 'project/set/link/add', array(1, 3)), // Add Test 3 to Set 1
  createJSONServiceTest('project-sets', 35, after('project-sets', 20, after('project-tests', 50)), 'project/set/link/add', array(2, 1)), // Add Test 1 to Set 2
  createJSONServiceTest('project-sets', 36, after('project-sets', 35), 'project/set/link/add', array(2, 2)), // Add Test 2 to Set 2
  createJSONServiceTest('project-sets', 37, after('project-sets', 36), 'project/set/link/add', array(2, 3)), // Add Test 3 to Set 2
  // Marker Tests Added to Sets
  createMark('project-sets', 40, after('project-sets', 32, after('project-sets', 37)), "Tests add to Sets"),
  // Perform Tests on Sets
  // * RENUMBER
  createJSONServiceTest('project-sets', 50, after('project-sets', 40), 'project/set/link/renumber', array(1, 10)),
  createJSONServiceTest('project-sets', 51, after('project-sets', 40), 'project/set/link/renumber', array(2, 100)),
  // * MOVE
  createJSONServiceTest('project-sets', 60, after('project-sets', 51), 'project/set/link/move', array(2, 300, 250)),
  // * REMOVE
  createJSONServiceTest('project-sets', 70, after('project-sets', 60), 'project/set/link/remove', array(2, 3)),
  // * LIST/COUNT
  createJSONServiceTest('project-sets', 80, after('project-sets', 70), 'project/set/link/list', array(2)),
  createJSONServiceTest('project-sets', 81, after('project-sets', 80), 'project/set/link/count', array(2)),
  // Marker Cleanup
  createMark('project-sets', 900, after('project-sets', 50, after('project-sets', 81), before('project-session', 30)), "Test Sets Cleanup"),
  // Delete Test Ses
  createJSONServiceTest('project-sets', 901, after('project-sets', 900), 'project/set/delete', array(1)),
  createJSONServiceTest('project-sets', 902, after('project-sets', 901, before('project-tests', 900)), 'project/set/delete', array(2)),
);
*/
?>