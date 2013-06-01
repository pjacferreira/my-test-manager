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

qx.Class.define("tc.services.Meta", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    actions: function(list, ok, nok, context) {
      if (qx.lang.Type.isArray(list)) {
        list = tc.util.Array.clean(
                tc.util.Array.map(list, function(value) {
          return tc.util.String.nullOnEmpty(value, true);
        }
        ));

        if (list !== null) {
          list = list.join(',');
        }
      } else if (qx.lang.Type.isString(list)) {
        list = tc.util.String.nullOnEmpty(list, true);
      } else {
        throw 'Invalid Required Parameters [list]';
      }

      if (list != null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'actions'), {'list': list},
        function(response) {
          this._processPassThrough(response, ok, context);
        },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [list]';
    },
    form: function(name, type, ok, nok, context) {
      name = tc.util.String.nullOnEmpty(name, true);
      if (name !== null) {
        // Cleanup 'type' parameter
        type = tc.util.String.nullOnEmpty(type, true);

        // Build Request
        var id = name + ':' + (type === null ? 'default' : type);
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'form', id), null,
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
    field: function(id, ok, nok, context) {
      id = tc.util.String.nullOnEmpty(name, true);
      if (id !== null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'field', id), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [id]';
    },
    fields: function(list, ok, nok, context) {
      if (qx.lang.Type.isArray(list)) {
        list = tc.util.Array.clean(
                tc.util.Array.map(list, function(value) {
          return tc.util.String.nullOnEmpty(value, true);
        }
        ));

        if (list !== null) {
          list = list.join(',');
        }
      } else if (qx.lang.Type.isString(list)) {
        list = tc.util.String.nullOnEmpty(list, true);
      } else {
        throw 'Invalid Required Parameters [list]';
      }

      if (list != null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'fields'), {'list': list},
        function(response) {
          this._processPassThrough(response, ok, context);
        },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [list]';
    },
    service: function(id, ok, nok, context) {
      id = tc.util.String.nullOnEmpty(name, true);
      if (id !== null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'service', id), null,
                function(response) {
                  this._processPassThrough(response, ok, context);
                },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [id]';
    },
    services: function(list, ok, nok, context) {
      if (qx.lang.Type.isArray(list)) {
        list = tc.util.Array.clean(
                tc.util.Array.map(list, function(value) {
          return tc.util.String.nullOnEmpty(value, true);
        }
        ));

        if (list !== null) {
          list = list.join(',');
        }
      } else if (qx.lang.Type.isString(list)) {
        list = tc.util.String.nullOnEmpty(list, true);
      } else {
        throw 'Invalid Required Parameters [list]';
      }

      if (list != null) {
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'services'), {'list': list},
        function(response) {
          this._processPassThrough(response, ok, context);
        },
                function(error) {
                  this._processPassThrough(error, nok, context);
                },
                this);
        return service.queueRequests(request);
      }

      throw 'Missing or Invalid Required Parameters [list]';
    },
    _processPassForm: function(id, response, callback, context) {
      /* PRE-REQUISITES: 
       * - response is a valid response object (or else this this function 
       *   should not be called)
       */

      // TODO Add Logging
      if (qx.lang.Type.isFunction(callback)) {
        var context = qx.lang.Type.isObject(context) ? context : this;
        var form = response.hasOwnProperty('return') ? response['return'] : null;
        callback.call(context, form.hasOwnProperty(id) ? form[id] : null);
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