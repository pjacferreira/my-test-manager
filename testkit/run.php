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


require_once 'guzzle.phar';
require_once 'config.php';
require_once 'api/creators.php';
require_once 'api/utility.php';

use api\Test;

function logger($message) {
  global $DEBUG;

  if (isset($DEBUG) && $DEBUG) {
    echo $message;
  }
}

function testFiles($dir) {
  // Files Array
  $files = array();

  // Extract All PHP Files from Directory
  if ($handle = opendir($dir)) {
    while (false !== ($entry = readdir($handle))) {
      if (
              ($entry != ".") &&
              ($entry != "..") &&
              is_readable("{$dir}/{$entry}") &&
              (strlen($entry) > 4) &&
              (strtoupper(substr($entry, strlen($entry) - 4)) == '.PHP')
      ) {
        $files[] = $entry;
      }
    }

    closedir($handle);
  }

  // SORT the PHP (if any) in Ascending Order
  if (count($files)) {
    sort($files);
    return $files;
  }

  return null;
}

function loadFile($testSet, $filepath) {
  assert('isset($testSet) && is_object($testSet)');
  assert('isset($filepath) && is_string($filepath)');

  global $TESTS, $SKIP;

  // Clear Previous Tests if they Exist
  $TESTS = array();
  $SKIP = false;

  // Include Test File
  include_once $filepath;

  if (!$SKIP) {
    $testSet->addTests($TESTS);
  }

  return $testSet;
}

function runTests($testSet, $client, &$cookiejar) {
  assert('isset($testSet) && is_object($testSet)');

  global $BREAK_ON_TEST, $XDEBUG, $TESTS_RUN, $TESTS_FAILED, $STOP_ON_FAIL, $SIMULATE;
  global $DOCROOT;

  // Total Time Executing Tests
  $ttotal = 0;
  if (isset($BREAK_ON_TEST)) {
    list($break_group, $break_priority) = explode(':', $BREAK_ON_TEST);
    $BREAK_ON_TEST = trim($break_group) . ":" . trim($break_priority);
  }

  foreach ($testSet as $key => $test) {
    $TESTS_RUN += 1;

    echo "TEST [{$key}]=";


    if (isset($BREAK_ON_TEST) && (strcmp($BREAK_ON_TEST, $key) === 0)) {
      break;
    }

    $passed = true;
    $message = null;
    switch ($test->getType()) {
      case Test::MARKER: // Marker
        $renderer = $test->getRenderer();
        $message = isset($renderer) ? $renderer->render(null) : "** MARKER **";
        echo "{$message}\n";
        break;
      case Test::STANDARD: // Service Test
        if (isset($SIMULATE) && $SIMULATE) {
          echo "SIMULATE [{$test->getKey()}]";
        } else {
          // Get Renderer and Validator for the Test
          $validator = $test->getValidator();
          if (!isset($validator)) {
            $validator = \api\validators\ValidateNULL::getInstance();
          }
          $renderer = $test->getRenderer();
          if (!isset($renderer)) {
            $renderer = \api\renderers\RenderNULL::getInstance();
          }

          try {
            // Create Request
            $tstart = microtime(true);
            $url = $test->toUrl();
            if (($TESTS_RUN == 1) && isset($XDEBUG)) {
              // Activate XDEBUG for the Tests (XDEBUG Will be run against the request, and not locally)
              if (stripos($url, '?') === FALSE) {
                $url .= "?XDEBUG_SESSION_START=$XDEBUG";
              } else {
                $url -= "&XDEBUG_SESSION_START=$XDEBUG";
              }
            }
            $request = $client->get("$DOCROOT/$url");
            $tdelta = microtime(true) - $tstart;
            $ttotal += $tdelta;

            // Add Cookies
            $cookiejar->removeExpired();
            foreach ($cookiejar as $cookie) {
              $request->addCookie($cookie->getName(), $cookie->getValue());
            }

            // Execute Request
            $response = $request->send();

            // Save the Response Cookies
            $cookiejar->addCookiesFromResponse($response);

            // Did the Test Pass ?
            $passed = $validator->verify($response);
            if (!$passed) {
              $TESTS_FAILED += 1;
              echo "FAILED";
            } else {
              echo "PASSED";
            }

            // Test Results
            echo '-> REQUEST [' . $request->getMethod() . ' - ' . $request->getUrl() . ']';
            echo "<- RESPONSE [{$response->getStatusCode()}:{$response->getReasonPhrase()}:$tdelta-" . $renderer->render($response) . "]\n";
          } catch (Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $request = $e->getRequest();
            $response = $request->getResponse();

            // Did the Test Pass ?
            $passed = $validator->verify($response);
            if (!$passed) {
              $TESTS_FAILED += 1;
              echo "FAILED";
            } else {
              echo "PASSED";
            }

            // Test Results
            echo '-> REQUEST [' . $request->getMethod() . ' - ' . $request->getUrl() . ']';
            echo '<- RESPONSE [' . $response->getStatusCode() . ':' . $response->getReasonPhrase() . "]\n";
//        echo $request . "\n\n" . $response;
          }
        }
    }

    if ($STOP_ON_FAIL && $TESTS_FAILED) {
      // Stop on 1st FAILED Test
      break;
    }
  }

  return $ttotal;
}

// Get the List of Test Files
$testfiles = testFiles($TESTSDIR);

if (isset($testfiles)) {

  // Load Tests
  $set = new api\TestSet();
  foreach ($testfiles as $file) {
    try {
      loadFile($set, "{$TESTSDIR}/{$file}");
    } catch (\Exception $e) {
      echo "** ERROR: Failed Processing File[$file].\n";
      echo $e->getMessage() . "\n";
      echo $e->getTraceAsString();
      exit;
    }
  }

  // Create a Place to Store Cookies
  $cookiejar = new Guzzle\Http\CookieJar\ArrayCookieJar;

  // Create Guzzle HTTP Client
  $client = new Guzzle\Service\Client($DOCROOT);

  // Initialize for Test Run
  $TESTS_RUN = 0;
  $TESTS_FAILED = 0;

  // Run Tests
  runTests($set, $client, $cookiejar);

  // Display Results
  $total_tests = $set->count();
  $TESTS_PASSED = $TESTS_RUN - $TESTS_FAILED;
  echo "RESULTS [$total_tests/$TESTS_RUN/$TESTS_PASSED/$TESTS_FAILED]\n";
} else {
  echo "** ERROR: No Test Files Found in [{$TESTSDIR}].";
}

/* TODO Add Database Validation Checks (i.e. verify that tests that modify the database, actually leave the database
 * in the expected state.
 */

/* TODO Allow extraction of variables (from the JSON Response) so that can be used in tests further on
 * Possible Solution, add a fake test, that extracts values from the previous result, so that it can be inserted
 * into the test list.
 */

// TODO Allow the possibility to create dot language files (graphviz, so as to allow us to see loops
// TODO Add Tests to get/set/clear/session variables
// TODO Add Tests to manage Test Sets
?>
