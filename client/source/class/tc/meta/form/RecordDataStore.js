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
qx.Class.define("tc.meta.form.RecordDataStore", {
  extend: tc.meta.FieldsDataStore,
  implement: tc.meta.form.interfaces.IRecordDataStore,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a change to the service's definitions occurs.
     */
    "change-services-meta": "qx.event.type.Event",
    /**
     * Fired when the execution of a service passes.
     * Service Name is returned as part of the data event
     */
    "execute-ok": "qx.event.type.Data",
    /**
     * Fired when the execution of a service fails.
     * Service Name is returned as part of the data event
     */
    "execute-nok": "qx.event.type.Data"
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   * @param data {Object|NULL} field name, value tuplets, for the fields that exist in the data source. If NULL, then
   *   we are working with a new record, for which we don't have any start values.
   */
  construct: function(fieldsmeta, valuemap) {
    this.base(arguments, fieldsmeta, valuemap);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Services Metadata Definition
    _services: null,
    /**
     * Set the metadata definition for the Data Store
     *  
     * @param metadata {Object} New Metadata Fields Definition (Only the Fields
     * portion of a form metadata should be used), i.e. expects an object of type
     * {
     *   field-name: { field properties }
     * }
     * Notes:
     * - throws an exception if the metadata provided is invalid (will not
     *   modify the current metadata, if the provided metadata is invalid)
     * - Fire change-fields-meta, on successful completion of metadata change
     */
    setServicesMeta: function(metadata) {
      if (qx.lang.Type.isObject(metadata)) {
        var new_metadata = {};
        var count = 0;
        for (var service in metadata) {
          if (metadata.hasOwnProperty(service)) {
            if (this._isServiceDefinition(metadata[service])) {
              new_metadata[service] = qx.lang.Object.clone(metadata[service], true);
              ++count;
            }
          }
        }

        if (count > 0) {
          this._services = new_metadata;
          this.fireEvent('change-services-meta');
          return true;
        } else {
          throw "Metadata provided, contains no valid service definitions"
        }
      }

      throw "Metadata provided is not valid";
    }, // FUNCTION: setServicesMeta
    /**
     * See if a service exists
     * 
     * @param service {name} Service name
     * @return {Boolean} 'true' if service exists, 'false' otherwise.
     * Notes:
     * - throws an exception if the service metadata is not loaded
     */
    hasService: function(name) {
      if (this._services != null) {
        return this._services.hasOwnProperty(name);
      }

      throw "Services Metadata has not been set.";
    }, // FUNCTION: hasService 
    /**
     * Verifies that the datastore meets  the required state, for the service call
     *  
     * @param service {String} Service name
     * @return {Boolean} 'true' if service has the necessary requirements for execution, 'false' otherwise.
     * Notes:
     * - throws an exception if the service metadata is not loaded
     */
    canExecute: function(service) {
      if (this.hasService(service)) {
        if (this._hasKey(service)) {
          var key = this._buildServiceKey(service);
          return key != null;
        } else {
          return true;
        }
      }
      return false;
    }, // FUNCTION: canExecute 
    /**
     * Execute a Service against the Current DataStore
     *  
     * @param name {String} Service name
     * Notes:
     * - throws an exception if the service metadata is not loaded
     * - Fires execute-ok, execute-nok depending on the
     *   outcome of call
     */
    execute: function(name) {
      if (this.hasService(name)) {
        // Build Request Properties
        var definition = this._services[name];
        var key = null;
        if (this._hasKey(name)) {
          key = this._buildServiceKey(name);
          if (key == null) { // Key Required - But not able to build
            return false;
          }
        }
        var parameters = this._buildServiceParameters(name);

        // Create Service Request and Execute it
        var service = tc.services.json.TCServiceRequest.getInstance();
        var request = service.buildRequest(service.buildURL(definition['service'], definition['action'], key), parameters,
                function(response) {
                  this._processResponse(name, response);
                },
                function(error) {
                  this.fireDataEvent('execute-nok', name);
                },
                this);
        return service.queueRequests(request);
      }

      return false;
    }, // FUNCTION: execute 
    _isServiceDefinition: function(definition) {
      if (qx.lang.Type.isObject(definition)) {
        return definition.hasOwnProperty('service') && definition.hasOwnProperty('action');
      }

      return false;
    }, // FUNCTION: _isServiceDefinition 
    _hasKey: function(service) {
      if (qx.core.Environment.get('qx.debug')) {
        this.assertTrue(qx.lang.Type.isString(service), "Expecting a service name {String}");
        this.assertTrue(this._services.hasOwnProperty(service), "Service [" + service + "] does not exist in the metadata.");
      }

      var definition = this._services[service];
      return definition.hasOwnProperty('key') || definition.hasOwnProperty('keys');
    }, // FUNCTION: _hasKey 
    _buildServiceKey: function(service) {
      var definition = this._services[service];
      if (definition.hasOwnProperty('key')) { // Single Key
        return this._buildSingleKey(definition['key']);

      } else if (definition.hasOwnProperty('keys')) { // Multiple Possible Keys
        var keys = definition['keys'];
        var key;
        for (var i = 0; i < keys.length; ++i) {
          key = this._buildSingleKey(keys[i]);
          if (key != null) { // Found our Match
            return key;
          }
        }
      }

      // No Key for Service or Possibly no Key Definition
      return null;
    }, // FUNCTION: _buildServiceKey 
    _buildSingleKey: function(key) {
      if (qx.lang.Type.isString(key)) { // Single Field
        if (this.hasField(key)) {
          return this.getFieldValue(key)
        }
      } else if (qx.lang.Type.isArray(key)) {
        var values = [];
        var value;
        for (var i = 0; i < key.length; ++i) {
          if (this.hasField(key[i])) {
            value = this.getFieldValue(key[i])
            if (value != null) {
              values.push(value);
            } else {
              return null;
            }
          } else { // Unable to Build Key (i.e. a Key Requires All Fields to be Set)
            return null;
          }
        }
        return values;
      }

      return null;
    },
    _buildServiceParameters: function(service) {
      var definition = this._services[service];
      if (definition.hasOwnProperty('parameters')) { // has Parameters
        var parameters = definition['parameters'];
        var rparams = {};
        var rparam_count = 0;
        var fields = this.getFieldsValues();
        for (var field in fields) {
          if (this.__allowField(parameters, field) && !this.__excludeField(parameters, field)) {
            rparams[field] = fields[field];
            ++rparam_count;
          }
        }

        if (parameters.hasOwnProperty('require') && parameters['require']) {
          if (rparam_count == 0) {
            throw "Service [service] requires parametes, and no parameter fields available or allowd";
          }
        }

        return rparam_count > 0 ? rparams : null;
      }

      // No 'parameters' definition - assume allow: 'none', require: 'false'
      return null;
    }, // FUNCTION: _buildServiceParameters 
    _processResponse: function(service, response) {
      // In both cases, the response should contain a single record, with fields set
      var fieldValues = response.return;
      switch (service) {
        case 'create':
          var current = this.getCurrent();
          // Merge the Results, before Setting this as the New Original Values
          this.setFieldsValues(qx.lang.Object.mergeWith(current, fieldValues, true), true);
          break;
        case 'read': // These Become the New Original Values
          this.setFieldsValues(fieldValues, true);
      }

      this.fireDataEvent('execute-ok', service);
    }, // FUNCTION: _processResponse     
    __allowField: function(parameters, field) {
      if (parameters.hasOwnProperty('allow')) {
        if (qx.lang.Type.isString(parameters['allow'])) {
          switch (parameters['allow']) {
            case 'all':
              break;
            case 'none':
              return false;
            default:
              return this.__fieldInList(field, [parameters['allow']]);
          }
        } else { // Array of Fields
          return this.__fieldInList(field, parameters['allow']);
        }
      }

      // Assume allow: 'all'
      return true;
    }, // FUNCTION: __allowField 
    __excludeField: function(parameters, field) {
      if (parameters.hasOwnProperty('exclude')) {
        if (qx.lang.Type.isString(parameters['exclude'])) {
          return this.__fieldInList(field, [parameters['exclude']]);
        } else { // Array of Fields
          return this.__fieldInList(field, parameters['exclude']);
        }
      }

      // Assume exclude: null
      return false;
    }, // FUNCTION: __excludeField     
    __fieldInList: function(field, list) {
      var parts = this.__splitField(field);
      var entity = parts[0];
      var field_name = parts[1];
      for (var i = 0; i < list.length; ++i) {
        parts = this.__splitField(list[i]);
        if ((parts[0] != '*') && (entity != parts[0])) {
          continue;
        }
        if ((parts[1] == '*') || (field == parts[1])) {
          return true;
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

      if (entity.lenght == 0) {
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
