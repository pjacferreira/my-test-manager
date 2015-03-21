/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/

/**
 * 
 * @returns {undefined}
 */
function initialize_session() {
    testcenter.services.hello(null, {
      call_ok: $.isset(window.hello_ok) && $.isFunction(window.hello_ok) ? window.hello_ok : default_hello_ok,
      call_nok: $.isset(window.hello_nok) && $.isFunction(window.hello_nok) ? window.hello_nok : default_hello_nok
    });
}

/**
 * 
 * @param {type} response_data
 * @returns {Boolean}
 */
function default_hello_ok(response_data) {
  if ($.isObject(response_data)) {
    window.__session = $.extend({}, window.__session, response_data);

    // Do we alread have an active session?
    var path = $.objects.keyToPath('__session.next_page');
    var next_page = $.objects.get(path, window);
    if (next_page !== null) { // YES: Then skip to the correct page
      $.objects.remove(path, window);
      // Move to the Next Page
      window.location.replace(testcenter.site.page(next_page));
      return false;
    }
  }
  
  return true;
}

function default_hello_nok(code, message) {
  // TODO: Maybe if we have a Toaster Used to Display the Message?
}