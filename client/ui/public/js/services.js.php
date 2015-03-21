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

// Create We Race Services Helpers
(function () {

  // Does the Namespace Exist?
  if (!window.hasOwnProperty('testcenter')) { // NO: Create it
    window.testcenter = {};
  }

  // Create Attach Points for Service Helpers
  var services = testcenter.services = {};

  var reply = services.reply = {
    isValidVersion: function (version) {
      if ($.isObject(version)) {
        if (version.hasOwnProperty('major') && version.hasOwnProperty('minor')) {
          return true;
        }
      }
      return false;
    },
    isValid: function (answer) {
      if ($.isObject(answer)) {
        return answer.hasOwnProperty('version') && reply.isValidVersion(answer.version);
      }

      return false;
    },
    getError: function (answer) {
      var error = {
        code: 0,
        message: 'OK'
      };

      if (answer.hasOwnProperty('error') &&
        $.isObject(answer.error)) {
        return $.extend(error, answer.error);
      }

      return error;
    },
    getResponse: function (answer) {
      return answer.hasOwnProperty('return') ? answer.return : null;
    }
  };
  // Create Service Defaults
  var defaults = services.defaults = {
    urlServer: 'http://10.193.0.201/',
    urlOffset: 'services/',
    ajax: {
      // jQuery ajax success handler
      success: function (data, textStatus, jqXHR) {
        /* NOTE: As long as 'context' IS NOT USED in the, the this is for
         * the function is equal to the settings object passed/created by 
         * the call to $.ajax(...)
         * -- PLEASE USE call_context or define call_ok as a method in 
         * 'context'
         */


        // Build a Fake Error Code
        var error = {
          code: -1,
          message: "Invalid Service Response"
        };

        // Extract a Response Value
        if (reply.isValid(data)) {
          error = reply.getError(data);
          if (error.code === 0) {
            // Do we have a callback 'ok' function?
            if ($.isFunction(this.call_ok)) { // YES
              var call = this.call_ok;
              // Should this function be called in a different context?
              if ($.isObject(this.call_context)) { // YES
                call = $.proxy(this.call_ok, this.call_context);
              }

              call(reply.getResponse(data));
            }
            return;
          }
        }

        // Do we have a callback 'nok' function?
        if ($.isFunction(this.call_nok)) { // YES
          var call = this.call_nok;
          // Should this function be called in a different context?
          if ($.isObject(this.call_context)) { // YES
            call = $.proxy(this.call_nok, this.call_context);
          }
          call(error.code, error.message);
        }
      },
      // jQuery ajax error handler
      error: function (jqXHR, textStatus, errorThrown) {
        // NOTE: see (NOTE) in success callback
        if ($.isFunction(this.call_nok)) {
          var code = -1;

          var message = (errorThrown === undefined || '' === errorThrown) ? 'Unable to connect to login server.' : errorThrown;
          switch (textStatus) {
            case 'timeout' :
              message = 'Timeout to Service Call';
              break;
            case 'error' :
              code = jqXHR.status;
              break;
            case 'abort' :
              message = 'Service Call was Aborted';
              break;
            case 'parsererror' :
              message = 'Service Call Error';
              break;
          }

          // Do we have a callback 'nok' function?
          if ($.isFunction(this.call_nok)) { // YES
            var call = this.call_nok;
            // Should this function be called in a different context?
            if ($.isObject(this.call_context)) { // YES
              call = $.proxy(this.call_nok, this.call_context);
            }
            call(code, message);
          }
        }
      },
      /** 
       * Service Call(backs) Context if any
       * 
       * @type {object}
       */
      call_context: null,
      /**
       * Service Callback OK Function
       * 
       * @type {function(object)}
       */
      call_ok: null,
      /**
       * Service Call NOK Function
       * 
       * @type {function(integer, string)}
       */
      call_nok: null
    }
  };

  var url = services.url = {
    /**
     * Retrieve the Site's Base Relative or Complete URL
     * 
     * @param {boolean} Do we want a relative url?
     * @returns {String} Base URL
     */
    base: function (relative) {
      relative = !!relative;
      // Do we want a Relative URL?
      if (relative) { // YES
        return defaults.urlOffset !== null ? defaults.urlOffset : '/';
      } else { // NO: Complete
        return defaults.urlOffset !== null ? defaults.urlServer + defaults.urlOffset : defaults.urlServer;
      }
    },
    /**
     * 
     * @param {string} action
     * @param {string|array} route_params
     * @param {object} request_params
     * @param {boolean} Do we want a relative service url?
     * @returns {undefined}
     */
    service: function (action, route_params, request_params, relative) {
      // Build Action Part of Route

      // Is the Action a String?
      if ($.isString(action)) { // YES: Convert to Array for Uniform Processing
        action = [action];
      } 

      // Is the Action an Array?
      var service_url = null
      if ($.isArray(action)) { // YES: Build URL
        $.each(action, function (index, value) {
          value = $.strings.nullOnEmpty(value);
          if (value !== null) {
            value = encodeURIComponent(value);
            service_url = service_url !== null ? service_url + '/' + value : value;
          }
        });
      }

      // Was the Action Part of the Route Created?
      if (service_url !== null) { // YES
        // Build Parameters Part of URL
        if ($.isString(route_params)) {
          route_params = [route_params];
        } else if($.isset(route_params) && !$.isArray(route_params)) {
          route_params = [route_params];
        }

        if ($.isArray(route_params)) {
          $.each(route_params, function (index, value) {
            value = $.isset(value) ? $.strings.nullOnEmpty(value.toString()) : null;
            if (value !== null) {
              value = encodeURIComponent(value);
              service_url = service_url !== null ? service_url + '/' + value : value;
            }
          });
        }

        // Build Request Parametes Part of URL
        var params = $.isObject(request_params) ? $.params(request_params) : null;

        if (params !== null) {
          service_url += '?' + params;
        }

        // Create Complete Service URL
        service_url = url.base(relative) + service_url;
      }

      return service_url;
    }
  };

  var call = services.call = function (action, route_params, request_params, settings) {
    // Is the AJAX Settings Only a function?
    if ($.isFunction(settings)) { // YES
      // Then use that as the OK Callback
      settings = {
        call_ok: settings
      }
    } else
    // Do we have a Settings Object?
    if ($.type(settings) !== "object") { // NO : Clear Settings
      settings = null;
    }

    // Do we have Settings?
    if (settings !== null) { // YES

      var service_url = null;
      if($.isset(settings.type) && (settings.type === 'POST')) {
        settings.data = request_params;
        service_url = url.service(action, route_params, null);
      } else {
        service_url = url.service(action, route_params, request_params);
      }
      
      // Can we build a service url?
      if (service_url !== null) { // YES
        // Complete the AJAX Call Context
        var ajax_context = $.extend({}, services.defaults.ajax, settings);

        // Call the Service
        return $.ajax(service_url, ajax_context);
      } else { // NO
        // Do we have a NOK Callback?
        if ($.isFunction(settings.call_nok)) { // YES: Call it
          nok(0, "Invalid Service Call");
        }
      }
    }
    
    return null;
  };

  services.hello = function (page, settings) {
    if ($.type(page) === 'string') {
      page = page.trim();
      page = page.length ? page : null;
    } else {
      page = null;
    }

    // Call the Service
    return services.call(['session', 'hello'], page, null, settings);
  };
})(); // END
