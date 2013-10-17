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
    /**
     * Generate a Request for a List's Metadata. 
     *
     * @param id {String} List ID
     * @param ok {Function ? null} Function to if successfull
     * @param nok {Function ? null} Function to Call in Case of Error
     * @param context {Object ? null} Context in which to call the callback function, if NULL, the current this will be used
     * @return {Integer} Service Request Queue ID
     */
    list: function(id, ok, nok, context) {
      id = tc.util.String.nullOnEmpty(id, true);
      if (id !== null) {
        // Cleanup 'type' parameter
        var idx = id.indexOf(':');
        if (idx < 0) {
          id += ':default';
        }
        if (idx === id.length - 1) {
          id += 'default';
        }

        // Build Request
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'list', id), null,
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
    /**
     * Generate a Request for a Table's Metadata. 
     *
     * @param id {String} Table ID
     * @param ok {Function ? null} Function to if successfull
     * @param nok {Function ? null} Function to Call in Case of Error
     * @param context {Object ? null} Context in which to call the callback function, if NULL, the current this will be used
     * @return {Integer} Service Request Queue ID
     */
    table: function(id, ok, nok, context) {
      id = tc.util.String.nullOnEmpty(id, true);
      if (id !== null) {
        // Cleanup 'type' parameter
        var idx = id.indexOf(':');
        if (idx < 0) {
          id += ':default';
        }
        if (idx === id.length - 1) {
          id += 'default';
        }

        // Build Request
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL('meta', 'table', id), null,
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
    /**
     * Generate a Request for a Form's Metadata. 
     *
     * @param id {String} Form ID
     * @param ok {Function ? null} Function to if successfull
     * @param nok {Function ? null} Function to Call in Case of Error
     * @param context {Object ? null} Context in which to call the callback function, if NULL, the current this will be used
     * @return {Integer} Service Request Queue ID
     */
    form: function(id, ok, nok, context) {
      id = tc.util.String.nullOnEmpty(id, true);
      if (id !== null) {
        // Cleanup 'type' parameter
        var idx = id.indexOf(':');
        if (idx < 0) {
          id += ':default';
        }
        if (idx === id.length - 1) {
          id += 'default';
        }

        // Build Request
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

      throw 'Missing or Invalid Required Parameters [id]';
    },
    /**
     * Generate a Request the Metadata for a Single Field or Multiple Fields. 
     *
     * @param id {Var} Field ID (String) or Field List (Array)
     * @param ok {Function ? null} Function to if successfull
     * @param nok {Function ? null} Function to Call in Case of Error
     * @param context {Object ? null} Context in which to call the callback function, if NULL, the current this will be used
     * @return {Integer} Service Request Queue ID
     */
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
    /**
     * Generate a Request the Metadata for a Single Service or Multiple Services. 
     *
     * @param id {Var} Service ID (String) or Service List (Array)
     * @param ok {Function ? null} Function to if successfull
     * @param nok {Function ? null} Function to Call in Case of Error
     * @param context {Object ? null} Context in which to call the callback function, if NULL, the current this will be used
     * @return {Integer} Service Request Queue ID
     */
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