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
 * Generic Form Model
 */
qx.Class.define("meta.entities.Service", {
  extend: meta.entities.AbstractEntity,
  implement: meta.api.entity.IService,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param id {String} Service ID
   * @param metadata {Object} Field Metadata
   */
  construct: function(id, metadata) {
    this.base(arguments, 'service', id, metadata);

    // Get Validated Metadata
    metadata = this.getMetadata();

    // Initialize Variables
    this.__oParameters = metadata.parameters;
    this.__arKey = [];

    // Create Service Path
    this.__arPath = metadata.service.slice();
    this.__arPath.push(metadata.action);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
    // Clear all Member Fields
    this.__oParameters = null;
    this.__arKey = null;
    this.__oServiceParameters = null;
    this.__arPath = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Service Meta Data
    __oParameters: null,
    __iKeyIndex: -1,
    __arKey: null,
    __oServiceParameters: null,
    __arPath: null,
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.entity.IService)
     ***************************************************************************
     */
    /**
     * Returns an Array of Strings that Identify the Exact Service Request
     *
     * @return {String[]} Service Path
     */
    path: function() {
      return this.__arPath;
    },
    /**
     * Service Requires a KEY (One or More Field Values required to call the service)
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    requireKey: function() {
      var metadata = this.getMetadata();
      return metadata.hasOwnProperty('keys') && (metadata['keys'] !== null);
    },
    /**
     * Service Requires Parameter(s)
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    requireParameters: function() {
      return this.__oParameters['require'];
    },
    /**
     * Service Allows Parameters Lists (i.e. more than one parameter can be set)
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    allowParameterList: function() {
      return !this.__oParameters['singleentry'];
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
      return this.requireKey() ? this.getMetadata()['keys'] : null;
    },
    /**
     * Reset the Service Object, for another call (clear the key and parameter
     * entries).
     * 
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     */
    reset: function() {
      this.__arKey = [];
      this.__iKeyIndex = -1;
      this.__arParameters = [];
      return this;
    },
    /**
     * Get the current set key.
     * 
     * @return {Object} Field Value Mapping of the Current Key being used or NULL, if no key set
     */
    getKey: function() {
      if (this.requireKey() && (this.__iKeyIndex >= 0)) {
        var arKey = this.getMetadata()['keys'][this.__iKeyIndex];
        var oKey = {};
        for (var i = 0; i < arKey.length; ++i) {
          oKey[arKey[i]] = this.__arKey[i];
        }

        return oKey;
      }
      return null;
    },
    /**
     * Build the Key for Service Call, using the input map.
     * 
     * @abstract
     * @param mapFieldValue {Object} Field Value Map used to build the key
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     * @throw Exception if cannot build a key, with the provided map
     */
    key: function(mapFieldValue) {
      /* TODO : Allow for Positional Keys
       * i.e. instead of providing an object map, if we pass in an array values
       * try to match it to an available and use that.
       */
      if (!qx.lang.Type.isObject(mapFieldValue)) {
        throw "[mapFieldValue] is invalid, a field->value map is required.";
      }

      // Does the Service Require a Key?
      if (this.requireKey()) { // YES: Build Key
        var keys = this.getMetadata()['keys'];
        var key = null;
        for (var i = 0; i < keys.length; ++i) {
          key = this.__buildSingleKey(mapFieldValue, keys[i]);
          if (key !== null) { // Found our Match
            this.__arKey = key;
            this.__iKeyIndex = i;
            return this;
          }
        }
      } else { // NO: Nothing to do
        return this;
      }

      // No Key for Service or Possibly no Key Definition
      throw "Unable to build a key, using [mapFieldValue].";
    }, // FUNCTION: key
    /**
     * Get the current set of Parameters defined.
     * 
     * @return {Map} Map of Name<-->Value Tuplets of Parameters Defined or NULL if none set
     */
    getParameters: function() {
      return this.__oServiceParameters;
    },
    /**
     * Reset's the Parameter List for the Service Call (does not affect the 
     * current key).
     * 
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     */
    resetParameters: function() {
      this.__oServiceParameters = null;
      return this;
    },
    /**
     * Add (another) parameter entry to the service call
     * 
     * @param parameters {Object[]|Object} Field Value Map used to build the parameter entry
     * @param nothrow {Boolean?false} TRUE - Ignore Errors, FALSE - Throw exception on failure
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     * @throw Exception if cannot build a parameter entry from the map, or
     * if adding multiple parameter entries, to a single entry service
     */
    parameters: function(parameters, nothrow) {
      var b_throw = !nothrow;
      // Is 'parameters' and Object?
      if (qx.lang.Type.isObject(parameters)) { // YES
        var entry = this.__buildSingleParameter(parameters);
        if (entry === null) {
          if (this.requireParameters() && b_throw) {
            throw "[parameters] is invalid.";
          }
        } else {
          this.__oServiceParameters = qx.lang.Object.mergeWith(this.__oServiceParameters !== null ? this.__oServiceParameters : {}, entry);
        }
      } else if (qx.lang.Type.isArray(parameters)) { // ELSE: Is it an Array (of Objects)?
        var parameter, entry;
        for (var i = 0; i < parameters.length; ++i) {
          parameter = parameters[i];
          if (qx.lang.Type.isObject(parameter)) {
            entry = this.__buildSingleParameter(parameter);
            if (entry === null) {
              if (b_throw) {
                throw "On of the parameters[index: " + i + "] is invalid.";
              }
            } else {
              this.__oServiceParameters = qx.lang.Object.mergeWith(this.__oServiceParameters !== null ? this.__oServiceParameters : {}, entry);
            }
          }
        }
      } else if (this.requireParameters()) { // ELSE: Does the Service Require Parameters?
        // YES
        if (b_throw) {
          throw "[parameters] is invalid. It should be an Object or an Array of Objects.";
        }
      }

      return this;
    }, // FUNCTION: parameters
    /**
     * Execute the Service Call, based on the Current Service State.
     *
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throw Exception on failure to initiate the service call
     */
    execute: function(ok, nok, context) {
      // Prepare callback Object
      var callback = this.__prepareCallback(ok, nok, context);
      // Verify Key Requirements
      var key = this.__arKey.length > 0 ? this.__arKey : null;
      if (this.requireKey() && (key === null)) {
        throw "Service requires a Key and none has been provided.";
      }

      // Verify Parameter Requirements
      var parameters = this.__oServiceParameters;
      if (this.requireParameters() && (parameters === null)) {
        throw "Service requires a Input Parameters and none have been provided.";
      }

      // Create Service Request and Execute it
      var metadata = this.getMetadata();
      var dispatcher = this.getDI().get('webservices');
      var request = dispatcher.buildRequest(dispatcher.buildURL(metadata.service, metadata.action, key), parameters,
        function(response) {
          var error = response['error'];
          callback['ok'].call(callback['context'], error.code, error.message, response['return']);
        },
        function(response) {
          var error = response['error'];
          var results = response.hasOwnProperty('return') ? response['return'] : null;
          callback['nok'].call(callback['context'], error.code, error.message, results);
        },
        this);
      return dispatcher.queueRequests(request);
    }, // FUNCTION: execute             
    /*
     ***************************************************************************
     PROTECTED Methods
     ***************************************************************************
     */
    /**
     * Prepare Entity Definition, according to type requirements.
     *
     * @param definition {Map} Incoming Entity Definition
     * @return {Map|null} Outgoing Entity Definition or 'null' if not valid
     */
    _preProcessMetadata: function(definition) {
      // Is the Metadata Definition Okay?
      if (!this._validateServiceProperty(definition, 'service') &&
        !this._validateStringProperty(definition, 'action', false, true)) { // NO: Abort
        return null;
      }

      // Does the Metadata Definition specify a 'key' instead of 'keys'?      
      var key = definition.hasOwnProperty('key') ? definition['key'] : null;
      if (key !== null) { // YES
        if (qx.lang.Type.isString(key)) {
          definition['keys'] = [[key]];
        } else if (qx.lang.Type.isArray(key)) {
          definition['keys'] = [key];
        }
        delete definition['key'];
      }

      // Is the Metadata Definition Okay?
      if (this._validateKeysProperty(definition, 'keys', true) &&
        this._validateParametersProperty(definition, 'parameters')) { // YES
        return definition;
      }
      // ELSE: No -Abort
      return null;
    },
    /*
     ***************************************************************************
     PROTECTED (Metadata Property Validation) Methods
     ***************************************************************************
     */
    _validateServiceProperty: function(metadata, property) {
      // Does the service property exists?
      var service = null;
      if (metadata.hasOwnProperty(property)) { // YES
        service = this.__cleanStringOrStringArray(metadata[property]);
      }

      // Do we have a valid value?
      if (service !== null) { // YES
        metadata[property] = service;
        return true;
      } else { // NO
        delete metadata[property];
        return false;
      }
    },
    _validateKeysProperty: function(metadata, property, empty) {
      // Does the service property exists?
      var keys = metadata.hasOwnProperty(property) ? metadata[property] : null;

      // Do we have a valid value?
      if (keys !== null) { // YES
        var newkeys = this.__cleanKeys(keys);
        if (newkeys !== null) {
          metadata[property] = newkeys;
          return true;
        }
      }
      // ELSE: NO
      delete metadata[property];
      return !!empty;
    },
    _validateParametersProperty: function(metadata, property) {
      // Clean up 'parameters' property
      var parameters = {
        'require': false,
        'singleentry': true,
        'required': null,
        'allowed': null,
        'excluded': null
      };

      // Does the Metadata contain a Parameters Definition?
      if (metadata.hasOwnProperty('parameters') &&
        qx.lang.Type.isObject(metadata['parameters'])) { // YES
        // Merge in the Existing Values
        parameters = qx.lang.Object.mergeWith(parameters, metadata['parameters']);

        // Make sure new values are okay
        parameters['require'] = !!parameters['require'];
        parameters['singleentry'] = !!parameters['singleentry'];
        if (parameters['required'] !== null) {
          parameters['required'] = this.__cleanRequire(parameters['required']);
        }
        if (parameters['allowed'] !== null) {
          parameters['allowed'] = this.__cleanAllow(parameters['allowed']);
        }
        if (parameters['excluded'] !== null) {
          parameters['excluded'] = this.__cleanExclude(parameters['excluded']);
        }
      }

      metadata[property] = parameters;
      return true;
    },
    /*
     ***************************************************************************
     PRIVATE Methods
     ***************************************************************************
     */
    /**
     * Attempts to build a Key for a Service Call based on the given parameters
     *
     * @param mapFieldValue {Object} Field->Value Map
     * @param key {Array} Array containing the ordered field ids of a key
     * @return {Array ? null} Key Array for Service Call
     */
    __buildSingleKey: function(mapFieldValue, key) {
      var values = [];
      for (var i = 0; i < key.length; ++i) {
        if (!mapFieldValue.hasOwnProperty(key[i]) ||
          (mapFieldValue[key[i]] == null)) {
          /* Fail if:
           * field required for the key, does not exist in the map provided or
           * it's value is NULL
           */
          values = null;
          break;
        }

        // Add Value to the Key
        values.push(mapFieldValue[key[i]]);
      }

      return values.length > 0 ? values : null;
    }, // FUNCTION: __buildSingleKey             
    /**
     * Attempts to build a Parameter Object from the Field->Value Map, for the 
     * current service definition.
     *
     * @param mapFieldValue {Object} Field Value Map
     * @return {Object} Returns a Parameter Object or NULL if not able to build
     */
    __buildSingleParameter: function(mapFieldValue) {
      var parameters = this.__oParameters;
      var key = this.getKey();
      var rparams = {};
      var rparam_count = 0;
      var inList;
      var value;
      for (var field in mapFieldValue) {
        // Phase 1 : See if Field is Part of the Key
        if ((key !== null) && key.hasOwnProperty(field)) {
          // EXCLUDE: Field is in the Key
          continue;
        }

        // Phase 2 : See if Field is Required
        inList = parameters['required'];
        if (!this.__fieldInList(field, inList)) { // FIELD is NOT in the REQUIRED LIST
          // Phase 3 : See if Field is Excluded
          inList = parameters['excluded'];
          if ((inList !== null) && this.__fieldInList(field, inList)) {
            // EXCLUDE: Field is Excluded
            continue;
          }

          // Phase 4 : See if Field is Allowed
          inList = parameters['allowed'];
          if ((inList !== null) && this.__fieldInList(field, inList)) {
            // EXCLUDE: Field is not in the Allowed List
            continue;
          }
        }

        /* RULES:
         * 1. Key Fields cannot be Parameters Fields.
         * 2. Required Fields, have precedence over allow and exclude lists.
         * 3. Exclude list, has precedence over allowed list
         */

        /* example 1:
         *   allow: null
         *   exclude: [field 1]
         *   - implies we allow all fields except 'field 1'
         * example 2:
         *   allow: [field 1, field 2]
         * or
         *   allow: [field 1, field 2]
         *   exclude: null
         *   - implies we allow only fields 'field 1' and 'field 2'
         * example 3:
         *   allow: [field 1, field 2]
         *   exclude: [field 1]
         *   - implies we allow only allow 'field 2'
         *   ** THIS could have just as easily been written
         *   allow: [field 2]
         * or
         *   allow: [field 2]
         *   exclude: null  
         */

        // FIELD : Passed
        value = mapFieldValue[field];
        // Is the Field's Value NULL
        if(value === null) { // YES: Send it as an empty string then
          /* TODO: Find Better Way to Serialize NULL so that the controller
           * can handle it correctly
           */
          value = '';
        }
        rparams[field] = value;
        ++rparam_count;
      }

      return rparam_count > 0 ? rparams : null;
    }, // FUNCTION: __buildSingleParameter
    __fieldInList: function(field, list) {
      if (qx.lang.Type.isString(list)) {// Handle Single String as Allow
        list = [list];
      }

      if (qx.lang.Type.isArray(list)) {
        var field_parts = this.__splitField(field);
        var entity = field_parts[0];
        for (var i = 0; i < list.length; ++i) {
          var list_parts = this.__splitField(list[i]);
          if ((list_parts[0] !== '*') && (entity !== field_parts[0])) {
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
      var parts = field.split(':', 2);
      var entity = utility.String.v_nullOnEmpty(parts[0], true);
      var field = parts.length > 1 ? utility.String.v_nullOnEmpty(parts[1], true) : null;
      if ((field === null) && (entity === null)) {
        throw "Invalid Field Definition [" + field + "]."
      }

      return [
        entity === null ? '*' : entity,
        field === null ? '*' : field
      ];
    },
    __cleanKeys: function(keys) {
      if (keys != null) {
        if (qx.lang.Type.isString(keys)) { // SINGLE FIELD
          keys = utility.String.v_nullOnEmpty(keys, true);
          if (keys !== null) {
            keys = [[keys]];
          }
        } else if (qx.lang.Type.isArray(keys)) { // ARRAY OF (FIELD | ARRAY OF FIELDS)
          keys = utility.Array.map(keys, this.__cleanStringOrStringArray, this);
          keys = utility.Array.clean(keys);
        } else {
          keys = null;
        }
      }
      return keys;
    },
    __cleanRequire: function(require) {
      return this.__cleanStringOrStringArray(require);
    },
    __cleanAllow: function(allow) {
      allow = this.__cleanStringOrStringArray(allow);
      if ((allow !== null) && (allow.length === 1) && (allow[1] === '*')) {
        // NULL is Equivale to Allow All Fields '*'
        allow = null;
      }
      return allow;
    },
    __cleanExclude: function(exclude) {
      return this.__cleanStringOrStringArray(exclude);
    },
    __cleanStringOrStringArray: function(value) {
      // Is it a String Value?
      if (qx.lang.Type.isString(value)) { // YES
        value = utility.String.nullOnEmpty(value, true);
        // DO we still have a value?
        if (value !== null) { // YES: Convert to Array
          value = [value];
        }
      } else if (qx.lang.Type.isArray(value)) { // NO: Is it an array?
        // YES: Trim and Clean the Presumed String Array
        value = utility.Array.clean(utility.Array.trim(value));
      } else { // NO: None of the above
        value = null;
      }

      return value;
    },
    __prepareCallback: function(ok, nok, context) {
      // Setup Default Callback Object
      var event_this = qx.lang.Type.isObject(context) ? context : this;
      var newCallback = {// DEFAULT: No Callbacks - Fire Events
        'ok': function(result) {
          event_this.fireDataEvent('ok', result);
        },
        'nok': function(error) {
          event_this.fireDataEvent('nok', error);
        },
        'context': event_this
      };
      // Update Callback Object with User Parameters
      if (qx.lang.Type.isFunction(ok)) {
        newCallback['ok'] = ok;
      }

      if (qx.lang.Type.isFunction(nok)) {
        newCallback['nok'] = nok;
      }

      return newCallback;
    } // FUNCTION: _prepareCallback
  } // SECTION: MEMBERS
});
