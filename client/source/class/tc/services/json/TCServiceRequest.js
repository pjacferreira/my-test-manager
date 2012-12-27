/* ************************************************************************

 TestCenter Client - Simplified Functional/User Acceptance Testing

 Copyright:
 2012-2013 Paulo Ferreira <pf at sourcenotes.org>

 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.

 Authors:
 * Paulo Ferreira

 ************************************************************************ */

/* ************************************************************************
 #require(qx.bom.String)
 #require(qx.util.Uri)
 #require(tc.util.String)
 ************************************************************************ */
qx.Class.define("tc.services.json.TCServiceRequest", {
  extend: qx.core.Object,

  events: {
    "service-error": "tc.services.json.TCServiceEvent",
    "service-ok": "tc.services.json.TCServiceEvent"
  },

  construct: function () {
    this.__requests = {};
    this.__passed = new Array();
    this.__failed = new Array();
    this.__processSettings = {};
  },

  destruct: function () {
    this.__request = null;
  },

  members: {
    __requests: null,
    __passed: null,
    __failed: null,
    __totalRequests: 0,
    __pendingRequests: 0,
    __state: 0, // 0 - Initialize, 1 - Requests are being Processed, 2 - Processing Complete (All Requests have benn received or aborted)
    __processSettings: null,

    /**
     *
     * @return {Boolean}
     */
    hasStarted: function () {
      return this.__state == 1;
    },

    /**
     *
     * @return {Number}
     */
    hasFinished: function () {
      return this.__state = 2;
    },

    /**
     * Send a Single Request to the Server.
     *
     * @param baseUrl
     * @param route
     * @param route_parameters
     * @param named_parameters
     */
    send: function (baseUrl, route, route_parameters, named_parameters) {
      this.addRequest('__single', baseUrl, route, route_parameters, named_parameters).process({'single': true});
    },

    /**
     *
     * @param request_id
     * @param baseUrl
     * @param route
     * @param route_parameters
     * @param named_parameters
     * @return {*}
     */
    addRequest: function (request_id, baseUrl, route, route_parameters, named_parameters) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertFalse(this.hasStarted(), "Can't modify request list after processing has started!");
      }

      // Prepare Required Incoming Parameters
      request_id = tc.util.String.nullOnEmpty(request_id);
      baseUrl = tc.util.String.nullOnEmpty(baseUrl);
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertNotNull(request_id, "[request_id] can't be NULL or EMPTY!");
        qx.core.Assert.assertNotNull(baseUrl, "[baseUrl] can't be NULL or EMPTY!");
      }

      if (qx.lang.Type.isArray(route)) {
        var value;
        var newRoute = new Array();
        for (var i = 0; i < route.length; ++i) {
          value = tc.util.String.nullOnEmpty(this.__valueExpand(route[i], request_id));
          if (value != null) {
            newRoute.push(value);
          }
        }

        route = newRoute.length > 0 ? newRoute.join('/') : null;
      } else {
        route = tc.util.String.nullOnEmpty(this.__valueExpand(route, request_id));
      }

      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertNotNull(route, "[route] Has to be an Not Empty String or an Array of non empty strings!!");
      }

      if ((request_id == null) || (baseUrl == null) || (route == null)) {
        // Abort Creation of the Request (Required Parameters)
        return this;
      }

      /* Request Format
       * (Property) '{request id}' (Object: AT LEAST ONE REQUEST IS REQUIRED) {
       *   'service' : (STRING:REQUIRED) - base service url
       *   'parameters' : (OBJECT:OPTIONAL) { Parameters for service
       *     'route' : (STRING or ARRAY of STRINGs:OPTIONAL) parameters used to route the request to action
       *        (if array, the trimmed values will be imploded with a '/' seperator, before being appended to the service url)
       *     'named' : (OBJECT:OPTIONAL) { Named Parameter (i.e. to be used {name}={value} in a GET request)
       *        '{name}' :  (STRING or ARRAY of STRINGs:REQUIRED) - Value of parameter
       *           (if array, a list will be created by imploding the values with a ',' as seperator)
       *        ....
       *
       *     }
       *   }
       * }
       */
      var request = {'service': baseUrl + '/' + route};
      if ((route_parameters != null) || (named_parameters != null)) {
        request['parameters'] = {};
        if (route_parameters != null) {
          request['parameters']['route'] = route_parameters;
        }

        if (named_parameters != null) {
          request['parameters']['named'] = named_parameters;
        }
      }

      // Add to List of Requests
      if (!this.__requests.hasOwnProperty(request_id)) {
        this.__totalRequests++;
      }
      this.__requests[request_id] = request;

      return this;
    },

    removeRequest: function (request_id) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertFalse(this.hasStarted(), "Can't modify request list after processing has started!");
      }

      if (!this.hasStarted() && this.__requests.hasOwnProperty(request_id)) {
        delete this.__requests[request_id];
        this.__totalRequests--;
        return true;
      }

      return false;
    },

    requestCount: function () {
      return this.__totalRequests;
    },

    failedCount: function () {
      return this.__failed.length;
    },

    pendingCount: function () {
      return this.__pendingRequests;
    },

    abort: function () {

      switch (this.__state) {
        case 0: // Processing Has not been Started
          return false;
        case 1: // Abort Pending Requests
          this.__abortPending();
          this.__state = 2;

          if (this.__processSettings['failOnAny'] || (this.__passed.length == 0)) {
            this.fireServiceEvent("service-error", this);
          } else {
            this.fireServiceEvent("service-ok", this);
          }

          break;
        case 2: // All Requests Completed (Nothing to Abort)
          return true;
      }

    },

    process: function (settings) {

      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertFalse(this.hasStarted(), "Requests already being processed!");
        qx.core.Assert.assertTrue((settings == null) || qx.lang.Type.isObject(settings), "[settings] is required to be NULL or an Object!");
      }

      if (!this.hasStarted()) {

        this.__state = 1;

        /* Format (Object)
         * (Property) 'settings' (Object) {
         *   'failOnAny' : (BOOLEAN) (true [default] - If Any of the Requests Fail, than the result is an error
         *                            false - If at least one request passes, than result is a pass (ONLY SUCCESSFUL RESULTS ARE RETURNED))
         *   'abortOnAny' : (BOOLEAN) (true [default] - If Any of the Requests Fail, abort any pending requests
         *                             false - complete all requests before returning)
         * }
         */
        this.__processSettings = {
          'single': false,
          'failOnAny': true,
          'abortOnAny': true
        };

        // Mixin Callin Settings
        if (settings != null) {
          this.__processSettings = qx.lang.Object.mergeWith(this.__processSettings, settings);
        }

        for (var request in this.__requests) {
          if (this.__requests.hasOwnProperty(request)) {
            this._processSingleRequest(request, this.__requests[request]);
          }
        }
      }

      return this;
    },

    _processSingleRequest: function (request_id, request) {
      var url = __TC_SERVICES_ROOT;

      // Set the Service
      if (request.hasOwnProperty('service')) {
        url += '/' + request['service'];
      }

      // Handle Parameters
      if (request.hasOwnProperty('parameters')) {
        var parameters = request.parameters;

        // Handle Routing Parameters
        if (parameters.hasOwnProperty('route')) {
          var route = parameters.route;

          if (qx.lang.Type.isArray(route)) {
            var value;
            var e_route = new Array()
            for (var i = 0; i < route.length; ++i) {
              value = tc.util.String.nullOnEmpty(this.__valueExpand(route[i], request));
              if (value != null) {  // Ignore NULL or EMPTY
                e_route.push(value);
              }
            }

            route = e_route.length > 0 ? e_route.join('/') : null;
          } else {
            route = tc.util.String.nullOnEmpty(this.__valueExpand(route, request_id));
          }

          if (url != null) {
            url += '/' + route;
          }
        }

        if (parameters.hasOwnProperty('named')) {
          var named = parameters.named;
          var value;
          for (var p in named) {
            if (named.hasOwnProperty(p)) {
              value = tc.util.String.nullOnEmpty(this.__valueExpand(named[p], request));
              if (value != null) {
                named[p] = value;
              } else { // Value is NULL remove Named Parameter
                delete named[p];
              }
            }
          }

          url = qx.util.Uri.appendParamsToUrl(url, named);
        }
      }

      // Create Xhr
      request.__xhr = new qx.io.request.Xhr();
      request.__completed = false;
      request.__aborted = false;

      // Set the URL
      request.__xhr.setUrl(url);

      // Listener : HTTP Error Code is not an Error Code 4xx-5xx
      request.__xhr.addListener("success", function (e) {
        var response = request.__xhr.getResponse();

        if (response.error.code == 0) {
          this.__requestPassed(request_id, response);
        } else {
          this.__requestFailed(request_id, response);
        }
      }, this);

      // Listener : HTTP Error Code
      request.__xhr.addListener("statusError", function (e) {
        this.__requestFailed(request_id, {
          version: {
            major: 1,
            minor: 0,
            build: 0
          },
          error: {
            code: 1,
            message: 'Error'
          }
        });
      }, this);

      this.__pendingRequests++;

      // Send the Request and Wait
      request.__xhr.send();
    },

    __valueExpand: function (value, request) {
      if (value != null) {
        if (qx.lang.Type.isString(value)) {
          return value;
        } else if (qx.lang.Type.isFunction(value)) {
          return value.call(request);
        } else if (qx.lang.Type.isArray(value)) {
          return value.join(',');
        } else {
          return value.toString();
        }
      } else {
        return null;
      }
    },

    __requestPassed: function (request_id, response) {

      // Register Success and Response
      var request = this.__requests[request_id];
      request.__completed = true;
      request.__aborted = false;
      request.__response = response;
      this.__passed.push(request_id);

      // See if we have received all the requests
      this.__pendingRequests--;
      if (this.__pendingRequests <= 0) {
        this.__state = 2;
        this.fireServiceEvent("service-ok", this);
      }
    },

    __requestFailed: function (request_id, response) {
      // Register Success and Response
      var request = this.__requests[request_id];
      request.__completed = true;
      request.__response = response;
      request.__aborted = false;

      // See if we have received all the requests
      this.__pendingRequests--;
      this.__failed.push(request_id);

      if (this.__processSettings['abortOnAny']) {
        this.__abortPending();
      }

      if (this.__pendingRequests <= 0) {
        this.__state = 2;

        if (this.__processSettings['failOnAny'] || (this.__passed.length == 0)) {
          this.fireServiceEvent("service-error", this);
        } else {
          this.fireServiceEvent("service-ok", this);
        }
      }
    },

    __abortPending: function () {
      for (var p in this.__requests) {
        if (this.__requests.hasOwnProperty(p)) {
          var request = this.__requests[p];
          if (!request.__completed && !request.__aborted) {
            request.__aborted = false;
            request.__request.abort();
            this.__pendingRequests--;
          }
        }
      }
    },

    fireServiceEvent: function (type, response, cancelable) {
      /* Handle the Sepcial Case of a Single Request
       * In this scenario, we just send back the response object, rather than the 'this' Object
       */
      if (this.__processSettings['single']) {
        var request = type == "service-ok" ? this.__passed[0] : this.__failed[0];
        request = this.__requests[request];
        response = request.__response;
      }
      return this.fireNonBubblingEvent(type, tc.services.json.TCServiceEvent, [response, !!cancelable]);
    }
  }
});

