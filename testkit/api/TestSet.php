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

namespace api;

require_once 'utility.php';

/**
 * Description of TestSet
 *
 * @author pferreira
 */
class TestSet implements \Iterator {

  protected $m_arSequence;
  protected $m_arTestMap;
  protected $m_nCurrent;

  public function __construct() {
    $this->m_arTestMap = array();
  }

  /**
   *
   * @param type $tests
   */
  public function addTests($tests) {

    if (isset($tests)) {
      if (!is_array($tests)) {
        if (!is_object($tests)) {
          throw new \Exception("ERROR: Invalid Parameter.");
        }

        $tests = array($tests);
      }

      foreach ($tests as $test) {
        if (isset($test)) {
          if (is_object($test)) {
            $key = "{$test->getGroup()}:{$test->getSequence()}";
            if (array_key_exists($key, $this->m_arTestMap)) {
              throw new \Exception("ERROR: Duplicate Test [{$key}].");
            }

            $this->m_arTestMap[$key] = $test;
          } else {
            throw new \Exception("ERROR: Array contains an element that is not a Test Object.");
          }
        }
      }

      // We have to Sequence the tests before using
      $this->m_arSequence = null;
    }

    return $this;
  }

  /**
   *
   * @param type $sGroup
   * @param type $nSequence
   * @return null
   */
  public function getTest($sGroup, $nSequence) {
    $sGroup = string_onEmpty($sGroup);
    $nSequence = integer_gt($nSequence, 0);

    if (isset($sGroup) && isset($nSequence)) {
      $key = "{$sGroup}:{$nSequence}";
      if (array_key_exists($key, $this->m_arTestMap)) {
        return $this->m_arTestMap[$key];
      }
    }

    return null;
  }

  /**
   *
   * @global type $DEBUG
   * @return type
   * @throws \Exception
   */
  public function sequence() {
    if (!isset($this->m_arSequence)) {

      global $DEBUG, $USE_GRAPHVIZ;


      /* TODO Optimize
       * Certain Maps are Not Required even though they are interesting for debugging
       * Example Inverted After Map
       */
      // Create Independent Before and After Dependency Maps

      /* Note : 
       * 1. mapBefore is a hashMap of form $key => array of tests
       * 2. The key is of format "{$test->getGroup()}:{$test->getSequence()}" 
       * (i.e. the $key for a test that exists in $this->m_arTestMap).
       * 3. the array os tests, represent the list of tests that have to be
       * run BEFORE the test, represented by $key, can be run.
       */
      $mapBefore = array();
      $mapAfter = array();

      $before = array();
      $after = array();
      foreach ($this->m_arTestMap as $key => $test) {
        // Split the Dependencies into Before and After
        $before = $test->getDependencies();
        $after = $test->getDependencies(false);

        // Before Dependency Map - All tests not having an After Dependency are associated with the 'null' key
        if (isset($after)) {
          $mapBefore = $this->addAsDependant($mapBefore, $key, $test, $after);
        } else {
          if (!isset($mapAfter['null'])) {
            // Create New Map with Entry
            $mapAfter['null'] = array($test);
          } else {
            // Append to Map
            $mapAfter['null'][] = $test;
          }
        }

        // After Dependency Map
        if (isset($before)) {
          $mapAfter = $this->addAsDependant($mapAfter, $key, $test, $before);
        }
      }

      logger("Map Before\n");
      $this->debugDumpDependencyMap($mapBefore);
      logger("\nMap After\n");
      $this->debugDumpDependencyMap($mapAfter);

      // Inv Before Dependency Map into an After Dependency Map
      $mapInvertedAfter = $this->invertMap($mapAfter);

      // Invert After Map into Before Map
      $mapInvertedBefore = $this->invertMap($mapBefore);

      logger("\nInverted Map After\n");
      $this->debugDumpDependencyMap($mapInvertedAfter);
      logger("\nInverted Map Before\n");
      $this->debugDumpDependencyMap($mapInvertedBefore);

      // Merge Converted Dependency Maps
      if (isset($mapInvertedAfter)) {
        $mapBefore = array_merge_recursive($mapBefore, $mapInvertedAfter);
      }
      if (isset($mapInvertedBefore)) {
        $mapAfter = array_merge_recursive($mapAfter, $mapInvertedBefore);
      }

      logger("\nCombined Before Map\n");
      $this->debugDumpDependencyMap($mapBefore);
      logger("\nCombined After Map\n");
      $this->debugDumpDependencyMap($mapAfter);

      $mapAfter = $this->sortMap($mapAfter);

      logger("\nSorted After Map\n");
      $this->debugDumpDependencyMap($mapAfter);

      // Build the Run Order, based on the Dependencies
      $run_order = array();

      // Handle No Dependencies
      if (isset($mapAfter['null'])) {
        // We start with the null entries, as they have no dependencies (every 
        // other key has a dependency, and therefore, will never be entered
        // until the 'null' key is processed).
        $run_order = $mapAfter['null'];
      }

      /* Iterative Loop
       * Loop ends when:
       * 1. All the tests have been correctly inserted into the run_order
       * 2. During the last iterations, no changes were made to the pending tests
       *    (i.e. the loop has hit a dead end)
       */
      $placed = array();
      do {
        $added_tests = 0;
        // Loop through the dependency map
        foreach ($mapBefore as $parent => $children) {

          // Get the Test Associated with the Dependencies
          $test = array_key_exists($parent, $this->m_arTestMap) ? $this->m_arTestMap[$parent] : null;
          if (!isset($test)) {
            throw new \Exception("Dependency [$parent] has no corresponding test.");
          }

          // Check How Many Dependencies we still have left
          $diff = array_uintersect($run_order, $children, function($t1, $t2) {
                    $compare = strcmp($t1->getGroup(), $t2->getGroup());
                    return ($compare === 0) ? $t1->getSequence() - $t2->getSequence() : $compare;
                  });

          $diff = array_udiff($children, $diff, function($t1, $t2) {
                    $compare = strcmp($t1->getGroup(), $t2->getGroup());
                    return ($compare === 0) ? $t1->getSequence() - $t2->getSequence() : $compare;
                  });

          // See if we have any missing dependencies and handle accordingly
          if (count($diff) != count($children)) { // Dependecy Count has decreased
            if (count($diff) > 0) { // Still Has Dependencies
              $mapBefore[$parent] = $diff;
            } else { // No More Dependencies (Can Add)
              unset($mapBefore[$parent]); // Remove the Key
              $run_order[] = $test;
              $added_tests++;
            }
          }
        }
      } while ($added_tests && count($mapBefore));

      logger("\nRun Order\n");
      if (isset($DEBUG) && $DEBUG) {
        if (isset($USE_GRAPHVIZ) && $USE_GRAPHVIZ) {
          $this->dumpGraphVIZ($run_order);
        } else {
          $this->dumpList($run_order);
        }

        $index = 1;
        foreach ($run_order as $test) {
          echo "{$index} -> {$test->getKey()}\n";
          $index++;
        }
      }

      if (count($mapBefore)) {
        $count = count($mapBefore);
        echo "\nUnresolved Tests [$count]\n";
        if (isset($USE_GRAPHVIZ) && $USE_GRAPHVIZ) {
          $this->dumpGraphVIZ($mapBefore);
        } else {
          $this->dumpList($mapBefore);
        }
        throw new \Exception("Unresolved Dependencies.");
      }

      /* Key Concepts
       * 1. Dependencies order is not important (i.e. if test(a) has an after dependency on test(b) and test(c), we only have
       *    to see if test(b) and test(c) have been run, and not if test(b) was executed before test(c)).
       * 2. The After Map (where key represents the parent step and the values the child steps, the steps, that can only
       *    be run after the parent has been run), is just the inverse of the Before Map (where the key, represents a dependant
       *    step, that can only be run after all the steps listed in the value, have been run)
       * 3. If we convert the After Map, to a Before Map, and merge that with the previous known map, then the resulting map
       *    can be displayed in 2D as a sort of spider web, in which the
       *    a) the points on the edge of the web, represent either
       *       i) start points, steps that can be run and have no previous requirements
       *       b) end points, steps that can only be run after all the steps, between the start point and end point have been rum
       * NOTE: It's important to note that, spider webs are normally closed circles (i.e. have no hanging threads), but in our
       *    test sets, we will have hanging threads (and the ends of these hanging thread represent either start points, or
       *    end points)
       *    We need to find thee set of paths, from start point to end point, that
       *    i)  Pass through all the tests, without repeating any tests.
       *    ii)
       * With this broken spider web idea, if we pick a start point (end of a hanging thread that represents a start point)
       * and lift the spider web off the ground, then
       * 1.  all the thread that are lower (height) represent points that directly, ou indirectly depend on that test to run.
       * 2 . (if the length of thread joining all the points, is the same), then the closer the node (link between 2 or more threads)
       *     is to the ground, the further it's dependency from the start point is (higher the level in a dependency tree)
       * 3. If we pickup the web by it's start points (all the start points are at the same height), then we will see all the
       *    end points, as the end points of hanging threads)
       *
       * Basic Assumptions
       * 1. All tests have:
       *   a) No After/Before Dependency
       *   b) Have an After/Forward Dependency
       * 2. Tests with no dependencies, are either:
       *   a) Starter tests (to be run as early as possible) or,
       *   b) Termination tests (to be run as late as possible, at the end of the test run).
       * 3. A dependency can be one o 2 types:
       *   a) After dependency, is one in which the dependant tests, have to be run after, the associated parent test
       *   b) Before dependency, is one in which the dependant tests, have to be run before, the associated child test.
       *   c) This allows us to to create rules that permits the conversion of an after dependency to a before dependencies,
       *      and vice-versa
       *      i) if 'a' is a test, that has dependent child test 'b' and 'c', the if we write 'a' <= 'b', 'c'
       *         this can be converted to a before dependency, by stating that 'a' has to be run before 'b' and 'c', or
       *         'b' => 'c' and 'c' => 'a'.
       *         therefore, the after dependency 'a' <= 'b', 'c', can be written as before dependencies 'b' <= 'a' and
       *         'c' <= 'a'
       */
      $this->m_arSequence = $run_order;
    }

    return $this->m_arSequence;
  }

  protected function dumpList($map) {
    foreach ($map as $key => $dependencies) {
      echo "{$key} [";
      $bfirst = true;
      foreach ($dependencies as $test) {
        if ($bfirst) {
          echo "{$test->getKey()}";
          $bfirst = false;
        } else {
          echo ", {$test->getKey()}";
        }
      }
      echo "]\n";
    }
  }

  protected function dumpGraphVIZ($map) {
    // Start Graph
    echo "digraph G {\n";

    // Create Nodes
    foreach ($map as $key => $dependencies) {
      foreach ($dependencies as $test) {
        echo "\"{$test->getKey()}\"->\"{$key}\";\n";
      }
    }

    // End Graph
    echo "}\n";
  }

  /**
   *
   */
  public function rewind() {
    if (!isset($this->m_arSequence)) {
      $this->sequence();
      $this->m_nCurrent = 0;
    }
  }

  /**
   *
   * @return type
   */
  public function valid() {
    return isset($this->m_arSequence) && ($this->m_nCurrent < count($this->m_arSequence));
  }

  /**
   *
   * @return type
   */
  public function current() {
    return isset($this->m_arSequence) ? $this->m_arSequence[$this->m_nCurrent] : null;
  }

  /**
   *
   * @return type
   */
  public function key() {
    $test = $this->current();
    return isset($test) ? $test->getKey() : null;
  }

  /**
   *
   */
  public function next() {
    $this->m_nCurrent++;
  }

  /**
   *
   * @return type
   */
  public function count() {
    return count($this->m_arTestMap);
  }

  /**
   *
   * @param type $map
   * @param type $key
   * @param type $test
   * @param type $dependencies
   * @return type
   */
  protected function addAsDependant($map, $key, $test, $dependencies) {
    foreach ($dependencies as $dependency) {
      $dep_key = "{$dependency['group']}:{$dependency['priority']}";
      if (array_key_exists($dep_key, $this->m_arTestMap)) { // Dependency Exists
        if (!array_key_exists($key, $map)) {
          // Create New Entry in the Map
          $map[$key] = array($this->m_arTestMap[$dep_key]);
        } else {
          // Append to the Map Entry
          $map[$key][] = $this->m_arTestMap[$dep_key];
        }
      } else {
        throw new \Exception("Test [{$key}] has an unresolved dependency [{$dep_key}].");
      }
    }

    return $map;
  }

  /**
   *
   * @param type $map
   * @return type
   */
  protected function invertMap($map) {

    $invertedMap = array();

    foreach ($map as $parent => $dependencies) {
      if ($parent === 'null') {
        // No Dependency Entries (can only exist in one of the Maps)
        continue;
      }

      // Get the Test from the Key
      $parent_test = $this->m_arTestMap[$parent];

      // For each Dependency, invert the order of the dependency
      foreach ($dependencies as $test) {
        $key = $test->getKey();
        if (!array_key_exists($key, $invertedMap)) {
          // Create New Entry in the Map
          $invertedMap[$key] = array($parent_test);
        } else {
          // Append to the Map Entry
          $invertedMap[$key][] = $parent_test;
        }
      }
    }

    return count($invertedMap) ? $invertedMap : null;
  }

  /**
   *
   * @param type $map
   * @return type
   */
  protected function sortMap($map) {
    /* Sort Map Before (Rules)
     * 1. 'null' key comes 1st
     * 2. keys with lower number of dependencies
     * 3. key name (lowest to highest)
     */
    uksort($map, function($a, $b) use($map) {
              if ($a === 'null') { //
                return -1;
              } else if ($b === 'null') {
                return 1;
              } else {
                $ca = count($map[$a]);
                $cb = count($map[$b]);
                if ($ca != $cb) {
                  return $ca < $cb ? -1 : 1;
                } else {
                  return strcmp($a, $b) <= 0 ? -1 : 1;
                }
              }
            });

    return $map;
  }

  protected function debugDumpDependencyMap($map, $sort = true) {

    global $DEBUG;

    if (isset($DEBUG) && $DEBUG) {
      if ($sort) { // Sort Map by Keys
        ksort($map);
      }

      foreach ($map as $key => $dependencies) {

        if ($sort) { // Sort Map Values
          try {
            uasort($dependencies, 'dependencyCompare');
          } catch (\Exception $e) {
            echo "AFTER [$key] -> {$e->getMessage()}";
            exit;
          }
        }

        echo "$key => ";
        $bfirst = true;
        foreach ($dependencies as $dependency) {
          if (!$bfirst) {
            echo ",{$dependency['group']}:{$dependency['priority']}";
          } else {
            echo "{$dependency['group']}:{$dependency['priority']}";
            $bfirst = false;
          }
        }
        echo "\n";
      }
    }
  }

}

?>
