/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * HANDLE MAIN MENU ACTIONS
 *******************************/

/**
 * 
 * @returns {undefined}
 */
function __click_logout() {
  testcenter.services.call(['session', 'logout'], null, null, {
    call_ok: function (reply) {
      // Reset Session Information
      window.__session = $.extend({}, reply);

      // Get the next page to move to
      var path = $.objects.keyToPath('__session.next_page');
      var next_page = $.objects.get(path, window, 'landing:home');
      $.objects.remove(path, window);

      // Move to the Next Page
      window.location = testcenter.site.page(next_page);
    },
    call_nok: function (code, message) {
      alert("Failed Logout Attempt. Please try again!");
    }
  });
}
