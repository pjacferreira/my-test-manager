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
 
 ************************************************************************ */

qx.Class.define("tc.services.Session", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    whoami: function(ok, nok, context) {
      var service = tc.services.json.TCServiceRequest.getInstance();
      var request = service.buildRequest(service.buildURL('session', 'whoami'), null,
              function(response) {
                this._processUserResponse(response, ok, context);
              },
              function(error) {
                this._processPassThrough(error, nok, context);
              },
              this);
      return service.queueRequests(request);
    },
    login: function(name, password, ok, nok, context) {
      if (qx.lang.Type.isString(name) &&
              qx.lang.Type.isString(password)) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', 'login', [name, password]), null,
                function(response) {
                  this._processUserResponse(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [name, password]';
    },
    logout: function(ok, nok, context) {
      var service = tc.services.json.TCServiceRequest.getInstance();
      var request = service.buildRequest(service.buildURL('session', 'logout'), null,
              function(response) {
                this._processPassThrough(response, ok, context);
              },
              function(error) {
                this._processPassThrough(error, nok, context);
              },
              this);
      return service.queueRequests(request);
    },
    sudo: function(name, password, ok, nok, context) {
      if (qx.lang.Type.isString(name) &&
              qx.lang.Type.isString(password)) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', 'sudo', [name, password]), null,
                function(response) {
                  this._processUserResponse(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [name, password]';
    },
    sudoExit: function(ok, nok, context) {
      var service = tc.services.json.TCServiceRequest.getInstance();
      var request = service.buildRequest(service.buildURL('session', ['sudo', 'exit']), null,
              function(response) {
                this._processPassThrough(response, ok, context);
              },
              function(error) {
                this._processPassThrough(error, nok, context);
              },
              this);
      return service.queueRequests(request);
    },
    getOrganization: function(ok, nok, context) {
      var service = tc.services.json.TCServiceRequest.getInstance();
      var request = service.buildRequest(service.buildURL('session', ['org', 'get']), null,
              function(response) {
                this._processPassThrough(response, ok, context);
              },
              function(error) {
                this._processPassThrough(error, nok, context);
              },
              this);
      return service.queueRequests(request);
    },
    setOrganization: function(organizationId, ok, nok, context) {
      if (organizationId != null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', ['org', 'set'], organizationId), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [organizationId]';
    },
    getProject: function(ok, nok, context) {
      var service = tc.services.json.TCServiceRequest.getInstance();
      var request = service.buildRequest(service.buildURL('session', ['project', 'get']), null,
              function(response) {
                this._processPassThrough(response, ok, context);
              },
              function(error) {
                this._processPassThrough(error, nok, context);
              },
              this);
      return service.queueRequests(request);
    },
    setProject: function(projectId, ok, nok, context) {
      if (projectId != null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', ['project', 'set'], projectId), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [projectId]';
    },
    getValue: function(name, ok, nok, context) {
      if (qx.lang.Type.isString(name)) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', 'get', name), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [name]';
    },
    setValue: function(name, value, ok, nok, context) {
      if ((qx.lang.Type.isString(name) != null) &&
              (value != null)) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', 'set', [name, value.toString()]), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [name, value]';
    },
    clearValue: function(name, ok, nok, context) {
      if (qx.lang.Type.isString(name)) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('session', 'clear', name), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [name]';
    },
    _processUserResponse: function(response, callback, context) {
      /* PRE-REQUISITES: 
       * - response is a valid response object (or else this this function 
       *   should not be called)
       */

      // TODO Add Logging
      if (qx.lang.Type.isFunction(callback)) {
        var context = qx.lang.Type.isObject(context) ? context : this;
        var user = response.hasOwnProperty('return') ? response['return'] : null;
        if (tc.util.Entity.IsEntityOfType(user, 'user')) {
          callback.call(context, user);
        } else {
          callback.call(context);
        }
      }
    },
    _processPassThrough: function(response, callback, context) {
      /* PRE-REQUISITES: 
       * - response is a valid response object (or else this this function 
       *   should not be called)
       */

      // TODO Add Logging
      if (qx.lang.Type.isFunction(callback)) {
        var context = qx.lang.Type.isObject(context) ? context : this;
        callback.call(context, response.hasOwnProperty('return') ? response['return'] : null);
      }
    }
  }
});