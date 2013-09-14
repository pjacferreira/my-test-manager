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

/**
 * Generic Form Model
 */
qx.Class.define("tc.meta.entities.ServiceEntity", {
  extend: tc.meta.entities.BaseEntity,
  implement: tc.meta.entities.IMetaService,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when the service call completes successfully. The data field contains
     * the result of the service call.
     */
    "ok": "qx.event.type.Data",
    /**
     * Fired when the service call fails. The data field contains an error message.
     */
    "nok": "qx.event.type.Data"
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param service {String} Service ID
   * @param metadata {Object} Field Metadata
   */
  construct: function(service, metadata) {
    this.base(arguments, 'service', service);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
    }

    this.__oMetaData = qx.lang.Object.clone(metadata, true);

    // Cleanup 'service' and 'action' properties
    var service = this.__oMetaData.hasOwnProperty('service') ? this.__oMetaData['service'] : null;
    var action = this.__oMetaData.hasOwnProperty('action') ? this.__oMetaData['action'] : null;

    service = tc.util.String.nullOnEmpty(service);

    if (qx.lang.Type.isString(action)) {
      action = tc.util.String.nullOnEmpty(action);
      if (action !== null) {
        action = [action];
      }
    } else if (!qx.lang.Type.isArray(action)) {
      action = null;
    } else {
      action = tc.util.Array.clean(
              tc.util.Array.map(action, function(value) {
        return tc.util.String.nullOnEmpty(value);
      }, this)
              );
    }

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertTrue(service !== null &&
              action !== null, "[metadata] Is does not contain Metadata Definition for a Service!");
    }

    this.__oMetaData['service'] = service;
    this.__oMetaData['action'] = action;
    this.__oMetaData['__path'] = [].concat(service, action);

    // Clean up 'key' property
    if (this.__oMetaData.hasOwnProperty('key')) {
      if (qx.lang.Type.isString(this.__oMetaData['key'])) {
        var key = tc.util.String.nullOnEmpty(this.__oMetaData['key']);
        if (key !== null) {
          this.__oMetaData['keys'] = [[key]];
        }
      }
      delete this.__oMetaData['key'];
    } else if (this.__oMetaData.hasOwnProperty('keys')) {
      var keys = this.__oMetaData['keys'];
      if (qx.lang.Type.isArray(keys)) {
        this.__oMetaData['keys'] = tc.util.Array.clean(
                tc.util.Array.map(keys, function(value) {
          if (qx.lang.Type.isString(value)) {
            return tc.util.String.nullOnEmpty(value);
          } else if (qx.lang.Type.isArray(value)) {
            return tc.util.Array.clean(
                    tc.util.Array.map(value, function(entry) {
              tc.util.String.nullOnEmpty(entry);
            }, this));
          }
          return null;
        }, this));
      } else {
        delete this.__oMetaData['keys'];
      }
    }

    // Clean up 'parameters' property
    if (this.__oMetaData.hasOwnProperty('parameters') &&
            qx.lang.Type.isObject(this.__oMetaData['parameters'])) {
      this.__oMetaData['parameters'] = qx.lang.Object.mergeWith({
        'require': false,
        'allow': null,
        'exclude': null
      }, this.__oMetaData['parameters']);
    } else {
      this.__oMetaData['parameters'] = {
        'require': false,
        'allow': null,
        'exclude': null
      };
    }

    this.__oMetaDataParams = this.__oMetaData['parameters'];
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__oMetaDataParams = null;
    this.__oMetaData = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Service Meta Data
    __oMetaData: null,
    __oMetaDataParams: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Returns an Array of Strings that Identify the Exact Service Request
     *
     * @return {String[]} Service Path
     */
    getServicePath: function() {
      return this.__oMetaData['__path'];
    },
    /**
     * Service Requires a KEY (One or More Field Values required to call the service)
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    hasKey: function() {
      return this.__oMetaData.hasOwnProperty('keys');
    },
    /**
     * An Array of:
     * 1. Field ID's that compose the key (If only one combination is available)
     * 2. An Array of Array (2 Dimensional Array) of Field ID's, if there are 
     *    more than one possible combinations of fields to compose the key.
     *
     * @return {String[][] ? null} Array of Array of Field IDs that compose the key.
     * If more than combination of field values can be used as key, then we
     * have 2 set of arrays (i.e. an array within an array)
     */
    getKeyFields: function() {
      return this.hasKey() ? this.__oMetaData['keys'] : null;
    },
    /**
     * Build the array containing the Key for the Service Call, in the Correct Order.
     *
     * @param mapFieldValue {Object} Field Value Map
     * @return {Array ? null} Key for Service Call
     */
    key: function(mapFieldValue) {
      var keys = this.__oMetaData.hasOwnProperty('keys') ? this.__oMetaData['keys'] : null;
      if (keys !== null) {
        var key = null;
        for (var i = 0; i < keys.length; ++i) {
          if (qx.lang.Type.isString(keys[i])) {
            key = this.__buildSingleKey(mapFieldValue, [keys[i]]);
          } else if (qx.lang.Type.isArray(keys[i])) {
            key = this.__buildSingleKey(mapFieldValue, keys[i]);
          }

          if (key !== null) { // Found our Match
            return key;
          }
        }
      }

      // No Key for Service or Possibly no Key Definition
      return null;
    }, // FUNCTION: key
    /**
     * Does the service require parameters. If Yes, buildParameterMap, has to be used 
     * to create the set of required Parameters for the Service Call.
     * 
     * @return {Boolean ? false} 'true' Parameters are required, 'false' No Parameters Required
     */
    areParamsRequired: function() {
      return this.__oMetaDataParams.hasOwnProperty('require') ? this.__oMetaDataParams['require'] == true : false;
    },
    /**
     * Build the Field,Value Tuplets  allowed for the Service Call, based on the 
     * incoming Field,Value Tuplets.
     * 
     * @param mapFieldValues {Object} Field Value Map
     * @return {Object ? null} Map Field,Value Parameters
     */
    parameters: function(mapFieldValues) {
      var parameters = this.__oMetaDataParams;
      var rparams = {};
      var rparam_count = 0;
      for (var field in mapFieldValues) {
        if (this.__allowField(parameters, field) || !this.__excludeField(parameters, field)) {
          rparams[field] = mapFieldValues[field];
          ++rparam_count;
        }
      }

      if (parameters.hasOwnProperty('require') && parameters['require']) {
        if (rparam_count === 0) {
          throw "Service [service] requires parametes, and no parameter fields available or allowed";
        }
      }

      return rparam_count > 0 ? rparams : null;
    }, // FUNCTION: parameters
    /**
     * Execute the Service Call, based on the Given Parameters.
     *
     * @param mapFieldValue {Object ? null} Field Value Map, or NULL if no Parameters Required
     * @param callback {Object ? null} Callback Object, if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @return {Boolean} 'true' Service Call Initiated, 'false' Failed to Initiate Call
     */
    execute: function(mapFieldValue, callback) {
      // Cleanup Incoming Parameters
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertObject(mapFieldValue, "[mapFieldValue] Should be an Object!");
        qx.core.Assert.assertTrue((callback == null) || qx.lang.Type.isObject(callback), "[callback] Invalid Value for Parameter!");
      }

      if (qx.lang.Type.isObject(callback)) {
        if (!callback.hasOwnProperty('context') &&
                !qx.lang.Type.isObject(callback['context'])) {
          callback['context'] = this;
        }
      } else {
        callback = null;
      }

      // Build Service Key
      var key = null;
      if (this.hasKey()) {
        var key = this.key(mapFieldValue);
        if (key == null) { // Key Required - But not able to build
          return false;
        }
      }

      // Build Service Parameters 
      var parameters = this.parameters(mapFieldValue);
      if ((parameters === null) && this.areParamsRequired()) {
        return false;
      }

      // Create Service Request and Execute it
      var service = tc.services.json.TCServiceRequest.getInstance();
      var request = service.buildRequest(service.buildURL(this.__oMetaData['service'], this.__oMetaData['action'], key), parameters,
              function(response) {
                if (callback !== null) {
                  if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
                    callback['ok'].call(callback['context'], response['return']);
                  }
                } else {
                  this.fireDataEvent('ok', response['return']);
                }
              },
              function(response) {
                if (callback !== null) {
                  if (callback.hasOwnProperty('nok') && qx.lang.Type.isFunction(callback['nok'])) {
                    callback['nok'].call(callback['context'], response['return']);
                  }
                } else {
                  this.fireDataEvent('nok', response['return']);
                }
              },
              this);
      return service.queueRequests(request);
    }, // FUNCTION: execute             
    /**
     * Attempts to build a Key for a Service Call based on the given parameters
     *
     * @param mapFieldValue {Object} Field Value Map
     * @param key {Array} Array containing field id in the key
     * @return {Array ? null} Key Array for Service Call
     */
    __buildSingleKey: function(mapFieldValues, key) {
      var values = [];
      for (var i = 0; i < key.length; ++i) {
        if (!mapFieldValues.hasOwnProperty(key[i]) ||
                (mapFieldValues[key[i]] == null)) {
          /* Fail if:
           * field required for the key, does not exist in the map provided or
           * it's value is NULL
           */
          values = null;
          break;
        }

        // Add Value to the Key
        values.push(mapFieldValues[key[i]]);
      }

      return values.length > 0 ? values : null;
    }, // FUNCTION: __buildSingleKey             
    __allowField: function(parameters, field) {
      // Test if Field is Allowed
      var allow = parameters['allow'];
      if (allow !== null) {
        return this.__fieldInList(field, allow);
      } else {
        /* Last Effort 
         * 1. if Require === true 
         * 2. exclude === null
         * then all fields are allowed
         */
        return parameters['require'] && (parameters['exclude'] === null);
      }
    }, // FUNCTION: __allowField 
    __excludeField: function(parameters, field) {
      // Test if Field is Excluded
      var exclude = parameters['exclude'];
      if (exclude !== null) {
        return this.__fieldInList(field, exclude);
      } else {
        /* Last Effort 
         * 1. if Require === true 
         * 2. allow === null
         * then all fields not explicitly excluded will be allowed
         */
        return !parameters['require'] || (parameters['allow'] !== null);
      }
    }, // FUNCTION: __excludeField     
    __fieldInList: function(field, list) {
      if (qx.lang.Type.isString(list)) {// Handle Single String as Allow
        list = [list];
      }

      if (qx.lang.Type.isArray(list)) {
        var field_parts = this.__splitField(field);
        var entity = field_parts[0];
        for (var i = 0; i < list.length; ++i) {
          var list_parts = this.__splitField(list[i]);
          if ((list_parts[0] !== '*') && (entity !== list_parts[0])) {
            // No Possible Match on Entity
            continue;
          }

          if ((list_parts[1] === '*') || (field_parts[1] === list_parts[1])) {
            // Entry Matches of Accepts All Fields
            return true;
          }
        }
      }

      return false;
    }, // FUNCTION: __fieldInList     
    __splitField: function(field) {
      var colon = field.indexOf(':');
      var entity;
      var field;
      if (colon < 0) {
        entity = field.trim();
      } else {
        var parts = field.split(':', 2);
        entity = parts[0].trim();
        field = parts[1].trim();
      }

      if (entity.length == 0) {
        entity = null;
      }

      if ((field != null) && (field.length == 0)) {
        field = null;
      }

      if ((field == null) && (entity == null)) {
        throw "Invalid Field Definition [" + field + "]."
      }

      return [
        entity == null ? '*' : entity,
        field == null ? '*' : field
      ];
    }
  }
});
