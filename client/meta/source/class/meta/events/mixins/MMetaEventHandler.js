/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/**
 * Implements Special Handling for Meta Events
 */
qx.Mixin.define("meta.events.mixins.MMetaEventHandler", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     MIXIN FUNCTIONS
     ***************************************************************************
     */
    /**
     * Attach to meta event handler to the "dispatcher"
     *
     * @param types {String|String[]} Meta Event Type(s)
     * @param dispatcher {qx.core.Object} Entity that fires the events
     * @return {Boolean} 'TRUE' attached all possible types, 'FALSE' did nothing
     */
    _mx_meh_attach: function(types, dispatcher) {
      // Clean and Polish 'types'
      if (qx.lang.Type.isString(types)) {
        types = utility.String.v_nullOnEmpty(types, true);
        if (types !== null) {
          types = [types];
        }
      } else if (qx.lang.Type.isArray(types)) {
        types = utility.Array.clean(utility.Array.trim(types));
      } else {
        types = null;
      }

      // Do we have any types to register
      if (types !== null) {
        for (var i = 0; i < types.length; ++i) {
          dispatcher.addListener(types[i], this._mx_meh_metaEventHandler, this);
        }
        return true;
      } else {
        if ((types === null) && qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertString(true, "[types] Is not of the expected type!");
        }
      }
      return false;
    },
    /**
     * Detach meta event handler from the "dispatcher".
     *
     * @param types {String|String[]} Meta Event Type(s)
     * @param dispatcher {qx.core.Object} Entity that fires the events
     * @return {Boolean} 'TRUE' detached all possible types, 'FALSE' did nothing
     */
    _mx_meh_detach: function(types, dispatcher) {
      // Clean and Polish 'types'
      if (qx.lang.Type.isString(types)) {
        types = utility.String.v_nullOnEmpty(types, true);
        if (types !== null) {
          types = [types];
        }
      } else if (qx.lang.Type.isArray(types)) {
        types = utility.Array.clean(utility.Array.trim(types));
      } else {
        types = null;
      }

      // Do we have any types to register
      if (types !== null) {
        for (var i = 0; i < types.length; ++i) {
          dispatcher.removeListener(types[i], this._mx_meh_metaEventHandler, this);
        }
        return true;
      } else {
        if ((types === null) && qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertString(true, "[types] Is not of the expected type!");
        }
      }
      return false;
    },
    /**
     * (CONSUMER) Meta Event Handler. Expands to a special processing function 
     * if available.
     *
     * @param e {meta.events.MetaEvent} Meta Event
     */
    _mx_meh_metaEventHandler: function(e) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertQxObject(e, meta.events.MetaEvent, "[e] Is not a Meta Event!");
      }

      // Build List of Possible Event Handlers in Order of Most Specific to Least Specifics
      var type = e.getType();
      var success = e.getOK() ? "OK" : "NOK";
      var params = e.getParameters();
      var catchall = "_processMeta";
      var specific = "_processMeta" + this.__mx_meh_ccEventName(type);
      var handlers = [specific + success, specific, catchall + success, catchall];

      // Make sure that params is an array (so we can add parameters for the specific handlers)
      // Are the event parameters null (i.e. the event has no parameters)?
      if (params === null) { // YES
        params = []; 
      } else if(!qx.lang.Type.isArray(params)) { // NO: We have a single non-array parameter
        params = [params];
      }
      /* NOTE: Handler Function Prototypes:
       * specific + success: function(code, message, ..extra parameters..)
       * specific : function( success, code, message, ..extra parameters..)
       * catchall + success : function( event-type, code, message, ..extra parameters..)
       * catchall : function( event-type, success, code, message, ..extra parameters..)
       */

      // Find a Possible Handler
      var handler = null, entry = null;
      for (var i = 0; i < handlers.length; ++i) {
        entry = handlers[i];
        // Did we find the handler?
        if (this[entry] && qx.lang.Type.isFunction(this[entry])) { // YES
          handler = this[entry];
          // All Handlers have a Code and Message Parameters
          params.unshift(e.getMessage());
          params.unshift(e.getCode());

          switch (i) {
            case 1: // Specific Handler with no OK/NOK Seperation
            case 2: // Catchall with OK/NOK Seperation
            case 3: // Catchall
              // Do we need to add a parameter to signal OK/NOK?
              if ((i === 1) || (i === 3)) { // YES
                params.unshift(e.getOK() ? true : false);
              }

              // Is this a Catchall Handler?
              if ((i === 2) || (i === 3)) { // YES
                params.unshift(type);
              }
          }
          break;
        }
      }

      // Do we have a Handler?
      if (handler !== null) {
        // Can we pass the Event to the Handler?
        if (!qx.lang.Type.isFunction(this._passMetaEvent) || this._passMetaEvent(type, success, e.getCode(), e.getMessage(), e.getParameters())) { // YES
          /* NOTES:
           * 1. This assumes that the order of the elements in the array 
           *    corresponds to the order of the parameters in the handler.
           * 2. If we want to pass an array (as a single parameter) we have to
           *    nest that array in another array (Ex: [[value1,value2,...]] where
           *    [value1,value2,...] is the only parameter to hanbdler
           */
          handler.apply(this, params);
        }
      }
    },
    /*
     ***************************************************************************
     HELPER FUNCTIONS
     ***************************************************************************
     */
    __mx_meh_ccEventName: function(event) {
      return utility.Array.map(event.split('-'), function(value) {
        if (value.length === 1) {
          return value.charAt(0).toUpperCase();
        } else if (value.length > 1) {
          return value.charAt(0).toUpperCase() + value.slice(1);
        } else {
          return "";
        }
      }, this).join("");
    }
  } // SECTION: MEMBERS
});
