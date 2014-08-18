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

/* Features :
 *
 * - Sinlge Point of Access to All Services
 * - Can Register Multiple Independent Requests
 * - Can Register Multiple Dependent Requests (will be served synchronously)
 * - Results are handled through callback
 * - (FUTURE) Handle Independent Requests in Parallel
 * - (FUTURE) Cancel Request (DEPENDANT Requests are treated as a Single Request
 *   therefore, cancelling on, cancel all remaing requests).
 * - (FUTURE) Add a default timeout period, with the possibility of per request
 *   timeouts
 */
qx.Class.define("utility.service.json.ServiceRequest", {
  extend: qx.core.Object,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * Create instance of Service Requests Handler
   * 
   * @param base_url {String} Base URL for Service Requests
   */
  construct: function(base_url) {
    // Set Base URL
    this.setBaseURL(base_url);

    // Initialize Members
    this.__requests = [];
  },
  /**
   *
   */
  destruct: function() {
    // NOTE: Do not call qx.core.Object:destruct, as THERE IS NONE, and forces an exception 
    // this.base(arguments);

    // Clear all Member Fields
    this.__requests = null;
    this.__processing = null;
    this.__currentRequest = null;
    this.__xhr = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __baseURL: null,
    __counter: 0,
    __requests: null,
    __processing: null,
    __currentRequest: null,
    __currentID: null,
    __xhr: null,
    /* PUBLIC METHODS */
    setBaseURL: function(url) {
      this.__baseURL = utility.String.v_nullOnEmpty(url, true);
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertString(this.__baseURL, "[baseurl] is Invalid!");
      }
    },
    queueRequests: function(requests) {
      if (qx.lang.Type.isObject(requests) || qx.lang.Type.isArray(requests)) {
        if (qx.lang.Type.isArray(requests)) {
          // Remove any Non-Request Objects from the Array
          utility.Array.map(requests, function(request) {
            return this._isValidRequest(request) ? request : null;
          }, this);
          requests = utility.Array.clean(requests);
        } else {
          if (!this._isValidRequest(requests)) {
            requests = null;
          }
        }
        if (requests != null) {
          // Queue Request
          this.__requests.push({
            'id': this.__counter++,
            'requests': requests
          });

          // Start Request Processig if Possible
          this._processRequests();
          return this.__counter - 1;
        }
      }
      throw "Missing or Invalid required parameter [requests]";
    },
    buildRequest: function(url, parameters, ok, nok, context) {
      // Validate Parameters
      url = utility.String.nullOnEmpty(url);
      if (url == null) {
        throw "Missing or Invalid required parameter [url]";
      }
      if (!qx.lang.Type.isFunction(ok)) {
        ok = null;
      }
      if (!qx.lang.Type.isFunction(nok)) {
        nok = null;
      }
      if (!qx.lang.Type.isObject(context)) {
        context = this;
      }
      return {
        'url': url,
        'parameters': parameters,
        'ok': ok,
        'nok': nok,
        'context': context
      };
    },
    /**
     * Build the Relative URL for the given Service Properties
     *
     * @param service {String|String[]?null} Service Name (If not providedm then
     *   we assume that the controller only provides a single service, which is
     *   already set as part fo ther URL)
     * @param action {String|String[]?null} Action for Service
     * @param key {String|String[]?null} Key for Action (If no Action is provided,
     *   then any key provided will be ignored)
     * @return {string} Relative Service URL
     */
    buildURL: function(service, action, key) {
      // Start with Service Parameter
      service = service != null ? this.__requestParameter(service, '/') : null;

      // Process action, key and options parameters
      action = action != null ? this.__requestParameter(action, '/') : null;
      key = key != null ? this.__requestParameter(key, '/') : null;

      // Join Everything Together
      var url = service;
      if (action != null) {
        url = (service !== null) ? service + '/' + action : action;
        if (key != null) {
          url += '/' + key;
        }
      }
      return url !== null ? url : '';
    },
    /* PROTECTED METHODS */
    _isValidRequest: function(request) {
      return qx.lang.Type.isObject(request) && request.hasOwnProperty('url') && qx.lang.Type.isString(request['url']);
    },
    _processRequests: function() {
      // Are we handling a Request?
      if (this.__currentRequest != null) {
        // Last Request hasn't Terminated
        return false;
      }
      if (this.__requests.length > 0) {          //Get the Next Request
        if (this.__processing == null) {         // Finished Last (Possibly) Multiple Request Queue
          this.__processing = this.__requests.shift();
        }
        var request = null;
        if (qx.lang.Type.isArray(this.__processing['requests'])) {
          // For Multiple Synchronous Requests
          this.__currentID = this.__processing['id'];
          request = this.__processing['requests'].shift();
          if (this.__processing['requests'].length <= 0) {           // Nothing left to do in the Queue
            this.__processing = null;
          }
        } else {
          this.__currentID = this.__processing['id'];
          request = this.__processing['requests'];
          this.__processing = null;
        }
        return request != null ? this.__processSingleRequest(request) : false;
      }

      // No More Requests
      return false;
    },
    _debug: function(level, message) {
      if (qx.core.Environment.get("qx.debug")) {
        switch (level) {
          case 'info':
            this.info(message);
            break;
          case 'warn':
            this.warn(message);
            break;
          default:
            this.error(message);
        }
      }
    },
    _onError: function(e) {
      this.__requestFailed(this.__processing['id'], {
        version: {
          major: 1,
          minor: 0,
          build: 0
        },
        error: {
          code: 1,
          message: 'General Network Error'
        }
      });
    },
    _onAbort: function(e) {
      this.__requestFailed(this.__processing['id'], {
        version: {
          major: 1,
          minor: 0,
          build: 0
        },
        error: {
          code: 2,
          message: 'Request Aborted'
        }
      });
    },
    _onTimeout: function(e) {
      this.__requestFailed(this.__processing['id'], {
        version: {
          major: 1,
          minor: 0,
          build: 0
        },
        error: {
          code: 3,
          message: 'Request Timed Out'
        }
      });
    },
    _onHTTPError: function(status, message) {
      this.__requestFailed(this.__currentID, {
        version: {
          major: 1,
          minor: 0,
          build: 0
        },
        error: {
          code: status,
          message: message
        }
      });
    },
    _onRequestError: function(response) {
      this.__requestFailed(this.__currentID, response);
    },
    _onRequestSuccess: function(response) {
      this.__requestPassed(this.__currentID, response);
    },
    /* PRIVATE METHODS */
    __isValidResponse: function(response) {
      return qx.lang.Type.isObject(response) &&
        response.hasOwnProperty('error') &&
        response.error.hasOwnProperty('code');
    },
    __processSingleRequest: function(request) {
      // Block Any Further Requests
      this.__currentRequest = request;
      var url = this.__baseURL;

      // Set the Service
      if (request.hasOwnProperty('url')) {
        url += '/' + request['url'];
      }

      // Do we need to create a XHR Request Class
      if (this.__xhr == null) {
        this.__xhr = new qx.io.request.Xhr();
        this.__xhr.setTimeout(300000);           // 5 Minutes

        // Listener : Handle all Fail Conditions
        this.__xhr.addListener("fail", function(e) {
          switch (this.__xhr.getPhase()) {
            case 'abort':                        // Request was Aborted
              this._onAbort(e);
              break;
            case 'timeout':                      // Request Timed out
              this._onTimeout(e);
              break;
            case 'statusError':                  // HTP Status Error
              this._onHTTPError(this.__xhr.getStatus(), this.__xhr.getStatusText());
              break;
            default:
              this._onError(e);
          }
        }, this);

        // Listener : Handle HTTP Request Success (HTTP Status Code : 2xx)
        this.__xhr.addListener("success", function(e) {
          var response = this.__xhr.getResponse();
          if (this.__isValidResponse(response)) {
            if (response.error.code == 0) {
              this._onRequestSuccess(response);
            } else {
              this._onRequestError(response);
            }
            return;
          }

          this._onRequestError({
            version: {
              major: 1,
              minor: 0,
              build: 0
            },
            'error': {
              'code': 0,
              'message': 'Invalid Service Response'
            },
            'return': response
          });
        }, this);
      }

      // Set the URL
      this.__xhr.setUrl(url);

      // Handle Parameters
      if (request.hasOwnProperty('parameters') && (request['parameters'] != null)) {
        // If we have Parameters, use POST so as to bypass GET request limits
        this.__xhr.setMethod('POST');
        this.__xhr.setRequestData(request['parameters']);
      } else {
        this.__xhr.setRequestData(null);
      }

      // Send the Request and Wait
      this.__xhr.send();
      return true;
    },
    __requestParameter: function(parameter, joinstr) {
      var value = parameter;
      if (qx.lang.Type.isFunction(parameter)) {
        value = parameter.call();
      } else if (qx.lang.Type.isArray(parameter)) {
        value = parameter.join(joinstr);
      } else if (!qx.lang.Type.isString(parameter)) {
        value = parameter.toString();
      }


      value = utility.String.nullOnEmpty(value);
      if ((parameter != null) && (value == null)) {
        this._debug('warn', 'Parameter possbile contained empty string');
      }
      return value;
    },
    __requestPassed: function(request_id, response) {
      var callback = null;
      var context = this;
      if (this.__currentRequest.hasOwnProperty('ok')) {
        if (qx.lang.Type.isFunction(this.__currentRequest['ok'])) {
          callback = this.__currentRequest['ok'];
          context = this.__currentRequest.hasOwnProperty('context') && qx.lang.Type.isObject(this.__currentRequest['context']) ? this.__currentRequest['context'] : this;
        } else {
          this._debug('warn', 'Request ID[' + request_id + '] contains an invalid value for [ok] function.');
        }
      }

      // Clear the Current Request
      this.__currentRequest = null;
      if (callback != null) {
        // Call the handler function
        callback.call(context, response);
      }

      // Continue Processing
      return this._processRequests();
    },
    __requestFailed: function(request_id, response) {
      var callback = null;
      var context = this;
      if (this.__currentRequest.hasOwnProperty('nok')) {
        if (qx.lang.Type.isFunction(this.__currentRequest['nok'])) {
          callback = this.__currentRequest['nok'];
          context = this.__currentRequest.hasOwnProperty('context') && qx.lang.Type.isObject(this.__currentRequest['context']) ? this.__currentRequest['context'] : this;
        } else {
          this._debug('warn', 'Request ID[' + request_id + '] contains an invalid value for nokok] function.');
        }
      }

      // Clear the Current Request
      this.__currentRequest = null;
      if (callback != null) {
        // Call the handler function
        callback.call(context, response);
      }

      // Continue Processing
      return this._processRequests();
    }
  }
});
