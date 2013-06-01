<?php

// $SKIP=true; // Skip the File for Current Test

/*
$TESTS = array(
  // Create Test Set
  createJSONServiceTest('project-run', 10, after('project-sets', 50), 'project/runs/clone', array(1, 'Run Test Set 1')),
  createJSONServiceTest('project-run', 11, after('project-run', 10), 'project/runs/clone', array(2, 'Run Test Set 2')),
  // Marker Sets Created
  createMark('project-run', 20, after('project-run', 11), "Runs Created"),
  // Generic Run Tests
  createJSONServiceTest('project-run', 30, after('project-run', 20), 'project/runs/read', array(1)), // Read Run 1
  createJSONServiceTest('project-run', 31, after('project-run', 30), 'project/runs/read', array('Run Test Set 2')), // Read Run 2
  createJSONServiceTest('project-run', 32, after('project-run', 31), 'project/runs/update', array(2, 'description=Run Test Set 2')), // Update Run 2
  createJSONServiceTest('project-run', 33, after('project-run', 32), 'project/runs/list'), // List Runs in Container
  createJSONServiceTest('project-run', 34, after('project-run', 33), 'project/runs/count'), // Count Runs in Container
  createJSONServiceTest('project-run', 35, after('project-run', 34), 'project/runs/delete', array(2)), // Delete Run
  // Execute Run 1 (Start)
  createJSONServiceTest('project-run', 40, after('project-run', 35), 'project/runs/run/start', array(1)), // Start Run 1
  createJSONServiceTest('project-run', 41, after('project-run', 40), 'project/runs/read', array(1)), // Read Run 1
  // Execute Run 1 (Execute Test 1)
  createJSONServiceTest('project-run', 50, after('project-run', 41), 'project/runs/run/next', array(1)),
  createJSONServiceTest('project-run', 51, after('project-run', 50), 'project/runs/run/current'),
  createJSONServiceTest('project-run', 52, after('project-run', 51), 'project/runs/run/next', array(1)),
  createJSONServiceTest('project-run', 53, after('project-run', 52), 'project/runs/run/next', array(1, 1, 'Finished Test 1')), // Current Sequence Should have Advanced
  createJSONServiceTest('project-run', 54, after('project-run', 53), 'project/runs/read', array(1)),
  // Execute Run 1 (Execute Test 2)
  createJSONServiceTest('project-run', 60, after('project-run', 54), 'project/runs/run/next', array(1)),
  createJSONServiceTest('project-run', 61, after('project-run', 60), 'project/runs/run/current', array(1)),
  createJSONServiceTest('project-run', 62, after('project-run', 61), 'project/runs/run/next', array(1, 2, 'Finished Test 2')),
  createJSONServiceTest('project-run', 63, after('project-run', 62), 'project/runs/read', array(1)),
  // Execute Run 1 (Execute Test 3)
  createJSONServiceTest('project-run', 70, after('project-run', 63), 'project/runs/run/next', array(1)),
  createJSONServiceTest('project-run', 71, after('project-run', 70), 'project/runs/run/next', array(1, 3, 'Finished Test 3')), // Run should be Closed
  createJSONServiceTest('project-run', 72, after('project-run', 71), 'project/runs/read', array(1)),
  // Execute Run 1 (Execute Test 3)
  createJSONServiceTest('project-run', 80, after('project-run', 72), 'project/runs/run/start', array(1)),
  createJSONServiceTest('project-run', 81, after('project-run', 80), 'project/runs/run/pos', array(10)),
  createJSONServiceTest('project-run', 82, after('project-run', 81), 'project/runs/read', array(1)),
  createJSONServiceTest('project-run', 83, after('project-run', 82), 'project/runs/run/close'),
  createJSONServiceTest('project-run', 84, after('project-run', 83), 'project/runs/read', array(1)),
  // Marker Cleanup
  createMark('project-run', 900, after('project-run', 84, before('project-sets', 900)), "Test Sets Cleanup"),
  // Delete Test Ses
  createJSONServiceTest('project-run', 901, after('project-run', 900), 'project/runs/delete', array(1)), // Delete Run
);
*/
?>