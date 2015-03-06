<?php
  /* 
   * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
   * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
   */

  // Include the Normal Configuration File
  $config = include __DIR__ . "/../../private/config/config.php";

  // Set the Content Type
  header('Content-Type: application/javascript');
?>

// Does the Attach Point Exist?
if (!window.hasOwnProperty('testcenter')) { // NO: Create it
  window.testcenter = {};
} 

testcenter.site = {
  __server: <?php echo "'{$config->application->serverUrl}'" ?>,
  __offset: <?php echo "'{$config->application->baseUri}'" ?>,
  __assets: <?php echo "'{$config->application->baseAssets}'" ?>,
  __js: <?php echo "'{$config->application->baseJS}'" ?>,
  __css: <?php echo "'{$config->application->baseCSS}'" ?>,
  /**
   * Retrieve the Site's Base Relative or Complete URL
   * 
   * @param {boolean} Do we want a relative server relative url?
   * @returns {String} Base URL
   */
  base: function (relative) {
    relative = !!relative;
    // Do we want a Relative URL?
    if (relative) { // YES
      return testcenter.site.__offset !== null ? testcenter.site.__offset : '/';
    } else { // NO: Complete
      return testcenter.site.__offset !== null ? testcenter.site.__server + testcenter.site.__offset : testcenter.site.__server;
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

