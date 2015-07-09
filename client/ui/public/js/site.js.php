<?php
  /* 
   * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
   * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
   */

  // Include the Normal Configuration File
  $config = require_once __DIR__ . "/../../private/config/config.php";

  // Set the Content Type
  header('Content-Type: application/javascript');
?>

// Does the Attach Point Exist?
if (!window.hasOwnProperty('testcenter')) { // NO: Create it
  window.testcenter = {};
} 

testcenter.site = {
  __server: <?php echo "'".$config->extract_string('application.site.url', 'null')."'" ?>,
  __offset: <?php echo "'".$config->extract_string('application.site.offset')."'" ?>,
  __assets: <?php echo "'".$config->extract_string('application.site.assets')."'" ?>,
  __js: <?php echo "'".$config->extract_string('application.site.js')."'" ?>,
  __css: <?php echo "'".$config->extract_string('application.site.css')."'" ?>,
  /**
   * Retrieve the Site's Base Relative or Complete URL
   * 
   * @param {boolean} Do we want a relative server relative url?
   * @returns {String} Base URL
   */
  base: function (relative) {
    relative = !!relative;
    // Do we want a Relative URL (Always true if no server URL specified) ?
    if (relative || (testcenter.site.__server === null)) { // YES
      return testcenter.site.__offset;
    } else { // NO: Complete
      return testcenter.site.__server + testcenter.site.__offset;
    }
  },
  /**
   * Retrieve an Assets Site Relative Path
   * 
   * @param {string} path
   * @returns {string} Assets Site Relative Path
   */
  asset: function (path) {
    if (typeof path === 'string') {
      path = path.trim();
      if (path.length) {
        if (path.search('://') > 0) {
          return path;
        }

        if (path[0] === '/') {
          return path;
        }

        return testcenter.site.__assets !== null ? testcenter.site.base(true) + testcenter.site.__assets + path : testcenter.site.base(true) + path;
      }
    }
    
    return null;
  },
  /**
   * Build the Full URL for a Given Page
   * 
   * @param {string} ID of the Page
   * @returns {string} Full URL for Page
   */
  page: function (id) {
    if (typeof id === 'string') {
      id = id.trim();
      if (id.length) {
        return testcenter.site.base(true) + 'page/' + id;
      }
    }
    
    return null;
  }
}

