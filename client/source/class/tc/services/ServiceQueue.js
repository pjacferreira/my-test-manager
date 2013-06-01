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

qx.Class.define("tc.services.ServiceQueue", {
  extend: qx.core.Object,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  construct: function() {
  }, // END: Constructor

  destruct: function() {
    this.__service = null;
    this.__queue = null;
  }, // END: Destructor
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __service: null,
    __queue: [],
    __processing: false,
    processRequest: function(request) {
      if (qx.core.Environment.get('qx.debug')) {
        // Validate Parameters
        this.error(this.assertMap(request, '[request] should be a hash map (i.e. an Object)'));
        this.error(this.assertTrue(request.hasOwnProperty('route'), '[request.route] is required'));
      }

      // Add the Request to the Queue
      this.__queue.push(request);

      // Try to process the Request
      this._processNextRequest();
      return true;
    },
    addListeners: function(request, ok, nok, context) {
      if (!qx.lang.Type.isObject(context)) {
        context = this;
      }

      request['listeners'] = {'context': context, 'ok': ok, 'nok': nok};

      return request;
    },
    addResultsProcessor: function(request, processor, context) {
      if (!qx.lang.Type.isObject(context)) {
        context = this;
      }

      request['results'] = {'context': context, 'handler': processor};
      return request;
    },
    _processNextRequest: function() {
      if (!this.__processing && (this.__queue.length > 0)) {
        // Block Further REquest Processing
        this.__processing = true;

        // Make Sure the Handle is Initialized
        this.__initializeServiceHandler();

        // Get the Current Request to be Processed
        var request = this.__queue[0];

        // Send request
        if (request.hasOwnProperty('parameters')) {
          this.__serviceHandler.send('session', request['route'], request['parameters']);
        } else {
          this.__serviceHandler.send('session', request['route']);
        }

        return true;
      }

      return false;
    },
    __initializeServiceHandler: function() {
      if (!this.__serviceHandler) {
        var handler = this.__serviceHandler = new tc.services.json.TCServiceRequest();

        // Attach Event Handlers
        handler.addListener('service-ok', this.__serviceOk, this);
        handler.addListener('service-error', this.__serviceError, this);
      }

      return true;
    },
    __serviceOk: function(e) {
      // Get the Request at the Head of the Queue
      var request = this.__queue.shift();

      // Process Results
      var results = this.__processResults(request, e.getResult());

      // Fire Message to Listener
      this.__fireMessage(request, 'ok', results);

      // Release Quest Processing
      this.__processing = false;
      
      // Continue Processing any Outstanding Requests
      this._processNextRequest();
    },
    __serviceError: function(e) {
      // Get the Request at the Head of the Queue
      var request = this.__queue.shift();

      // Fire Message to Listener
      this.__fireMessage(request, 'nok', e.getResult());

      // Release Quest Processing
      this.__processing = false;
      
      // Process More RequestS
      this._processNextRequest();
    },
    __processResults: function(request, results) {
      if (request.hasOwnProperty('results') && qx.lang.Type.isObject(request['results'])) {
        var processor = request['results'];
        var handler = this.__extractProperty(processor, 'handler', null);

        if (handler) {
          var context = this.__extractProperty(processor, 'context', this);
          if (qx.lang.Type.isString(handler)) {
            if (context.hasOwnProperty(handler) && qx.lang.Type.isFunction(context[handler])) {
              handler = context[handler]
            } else {
              handler = null;
            }
          } else if (!qx.lang.Type.isFunction(handler)) {
            handler = null;
          }

          if (handler != null) {
            return handler.call(context, results)
          }
        }
      }

      return results;
    },
    __fireMessage: function(request, listener, parameter) {
      if (request.hasOwnProperty('listeners') && qx.lang.Type.isObject(request['listeners'])) {
        var listeners = request['listeners'];
        var handler = this.__extractProperty(listeners, listener, null);

        if (handler) {
          var context = this.__extractProperty(listeners, 'context', this);
          if (qx.lang.Type.isString(handler)) {
            if (context.hasOwnProperty(handler) && qx.lang.Type.isFunction(context[handler])) {
              handler = context[handler]
            } else {
              handler = null;
            }
          } else if (!qx.lang.Type.isFunction(handler)) {
            handler = null;
          }

          if (handler != null) {
            return handler.call(context, parameter)
          }
        }
      }

      return false;
    },
    __extractProperty: function(obj, pname, defaultValue) {
      if (obj.hasOwnProperty(pname)) {
        return obj[pname];
      }

      return defaultValue;
    }
  }
});
