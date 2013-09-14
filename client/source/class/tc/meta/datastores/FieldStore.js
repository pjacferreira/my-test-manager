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
 * Base Meta Package Class
 */
qx.Class.define("tc.meta.datastores.FieldStore", {
  extend: qx.core.Object,
  implement: tc.meta.datastores.IFieldStore,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /*
     ***************************************************************************
     EVENTS (IFieldsStore)
     ***************************************************************************
     */
    /**
     * Fired when a new Meta Model has been initialized.
     */
    "ok": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "nok": "qx.event.type.Data",
    /**
     * Fired when field(s) value(s) are modified.
     * The data returned is an object with the following format:
     * {
     *   'field-name' : value
     *   ...                   | If more than one field is modified
     *   'field-name' : value  |
     * }
     */
    "store-fields-changed": "qx.event.type.Data"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** Original Values, for the Data Source */
    original: {
      check: "Object",
      init: null,
      nullable: true
    },
    /** The Current Values for the Data Source */
    current: {
      check: "Object",
      init: null,
      nullable: true
    },
    /** Package Containing Metadata */
    metaPackage: {
      check: "Object",
      init: null,
      nullable: false,
      apply: "_applyPackage",
      event: "changePackage"
    }
  }, // SECTION: PROPERTIES
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor
   */
  construct: function() {
    this.base(arguments);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    this._storeIV = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _bReady: false,
    _storeIV: null,
    _modified: 0,
    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _applyPackage: function(newPackage, oldPackage) {
      this._bReady = false;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (IDataStore)
     *****************************************************************************
     */
    /**
     * Can we use the Data Store?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReady: function() {
      return this._bReady;
    },
    /**
     * Is the Data Store Read Only?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
      return false;
    },
    /**
     * Is this an an offline (in memory only) data store?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isOffline: function() {
      return true;
    },
    /**
     * Was the store modified (i.e. Dirty with pending changes)?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isDirty: function() {
      return this.getCurrent() != null;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (IFieldStorage)
     *****************************************************************************
     */
    /**
     * Initialize the model.
     *
     * @abstract
     * @param package {tc.meta.package.IFieldsMetaPackage ? null} Fields Meta Package (Required to Obtain the Field Metadata)
     * @param iv {Object ? null} Store Initialization Values.
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    initialize: function(iv, callback) {
      callback = this._prepareCallback(callback);

      if (!this._bReady) {
        if (qx.lang.Type.isObject(iv)) {
          this._storeIV = iv;
        }

        this._initializePackage(callback);
      } else {
        if (qx.lang.Type.isObject(iv)) {
          this._storeIV = iv;
          this._initializeFields();
        }

        this._callbackModelReady(callback, true);
      }
    },
    /**
     * Field Exists in Data Store?
     *
     * @abstract
     * @param name {String} Field Name
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Data Store is Not Ready
     */
    hasField: function(name) {
      this._throwIsStoreReady();

      return this._getFieldsPackage().hasField(name);
    },
    /**
     * Test if a field can be modified in the current data store.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is modifiable, 'false' otherwise.
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    isFieldMutable: function(name) {
      this._throwFieldNotExists(name, this.hasField(name));

      var field = this._getFieldsPackage().getField(name);
      return !field.isAutoValue();
    },
    /**
     * Test if a field has a value Set.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' field has a value defined, 'false' otherwise.
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    isFieldSet: function(name) {
      this._throwFieldNotExists(name, this.hasField(name));

      var source = this.getCurrent();
      if ((source !== null) && source.hasOwnProperty(name)) {
        return true;
      }

      // Try to Get From Orginal
      source = this.getOriginal();
      return source.hasOwnProperty(name) ? true : false;
    }, // FUNCTION: isFieldSet
    /**
     * Was the field value modified (i.e. Dirty, pending changes)?
     *
     * @param name {String} Field Name
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    isFieldDirty: function(name) {
      this._throwFieldNotExists(name, this.hasField(name));

      var source = this.getCurrent();
      return (source !== null) && source.hasOwnProperty(name) ? true : false;
    },
    /**
     * Retrieve Field Value
     *
     * @param name {String} Field Name
     * @return {var} Field Value
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    getField: function(name) {
      this._throwFieldNotExists(name, this.hasField(name));

      var source = this.getCurrent();
      if ((source !== null) && source.hasOwnProperty(name)) {
        return source[name];
      }

      // Try to Get From Orginal
      source = this.getOriginal();
      return source.hasOwnProperty(name) ? source[name] : null;
    },
    /**
     * Return a Field Value Map, containing the current Field Values
     *
     * @return {Object} Field, Value Tuplets
     * @throws if the Data Store is Not Ready
     */
    getFields: function() {
      this._throwIsStoreReady();

      return this.isDirty() ? qx.lang.Object.mergeWith(this.getOriginal(), this.getCurrent(), true) : this.getOriginal();
    },
    /**
     * Modify the Field's Value
     *
     * @param name {String} Field Name
     * @param value {var} Field Value
     * @return {var} The Incoming Field Value or The Actual Value Set (Note: the Value may be modified if Trim and Empty-as-Null are Set)
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store or is not Modifiable
     */
    setField: function(name, value) {
      /* TODO : Have to coeerce values to the correct type
       * (i.e. Form Widgets will always provide values as string (more than likely)
       *  this has to be converted to the correct field type, for example, an 
       *  integer)
       */
      this._throwFieldNotMutable(name, this.isFieldMutable(name));

      var current = this.getCurrent();
      current = (current != null) ? current : {};

      var original = this.getOriginal();
      var oldValue = null;

      // Check if we are restoring the value back to it's original settings
      if (original.hasOwnProperty(name)) {
        if (original[name] == value) {
          if (current.hasOwnProperty(name)) {
            oldValue = current[name];
            delete current[name];
            this._modified--;
            this.setCurrent(this._modified > 0 ? current : null);

            // Resetting Value Back to it's Original Setting
            this.fireDataEvent('store-fields-changed', {name: value});
          }

          return oldValue;
        }
      }

      // Modify Current Value or Add New Current Value
      if (current.hasOwnProperty(name)) {
        if (current[name] != value) {
          oldValue = current[name];
          current[name] = value;
          this.setCurrent(current);

          // Modified Current Value
          this.fireDataEvent('store-fields-changed', {name: value});
        }
      } else {
        current[name] = value
        this._modified++;
        this.setCurrent(current);

        // Setting New Current Value
        this.fireDataEvent('store-fields-changed', {name: value});
      }

      return oldValue;
    }, // FUNCTION: setField
    /**
     * Bulk Modifies the Data Store
     *
     * @param map {Object} Field Value Tuplets
     * @return {Object} Field Value Tuplets of All Modified Fields
     * @throws if the Data Store is Not Ready
     */
    setFields: function(map) {
      this._throwIsStoreReady();

      if (qx.lang.Type.isObject(map)) { // Valid Incoming Parameter
        var fields_modified = {};
        var fields_old_value = {};
        var changes = 0;

        // Changing Current Values
        var value = null;
        var current = this.getCurrent();
        current = (current != null) ? current : {};
        var original = this.getOriginal();

        for (var field in map) {
          if (map.hasOwnProperty(field) &&
                  this.hasField(field) &&
                  this.isFieldMutable(field)) {

            value = map[field];

            // Check if we are restoring the value back to it's original settings
            if (original.hasOwnProperty(field)) {
              if (original[field] == value) {
                if (current.hasOwnProperty(field)) {
                  fields_old_value[field] = current[field];
                  delete current[field];
                  fields_modified[field] = original[field];
                  this._modified--;
                  changes++;
                }

                continue;
              }
            }

            // Modify Current Value or Add New Current Value
            if (current.hasOwnProperty(field)) {
              if (current[field] != value) {
                fields_old_value[field] = current[field];
                current[field] = value;
                fields_modified[field] = value;
                changes++;
              }
            } else {
              current[field] = value
              fields_modified[field] = value;
              this._modified++;
              changes++;
            }
          }
        }

        if (changes > 0) {
          // Save the Changes Back          
          this.setCurrent(this._modified > 0 ? current : null);

          // Fire the Event to Show that a Field's Value has Changed
          this.fireDataEvent('store-fields-changed', fields_modified);
          return fields_old_value;
        }
      }

      // No Changes
      return null;
    }, // FUNCTION: setFields
    /**
     * Reset's All Modified Values Back to the Last Saved State
     *
     * @param name {String ? null} Field Name or NULL if we would like to reset all fields rather than just a single field.
     * @return {Object} Value Tuplets of All Modified Fields (with new, original value) or NULL if No Changes
     * @throws if the Data Store is Not Ready or Field does not exist
     */
    reset: function(name) {
      this._throwIsStoreReady();

      name = tc.util.String.nullOnEmpty(name);
      var modified = null;
      if (name !== null) {
        if (this.isFieldDirty(name)) {
          // Get New Value
          modified = {};
          modified[name] = this.getOriginal()[name];

          var current = this.getCurrent();
          delete current[name];
          this._modified--;
          this.setCurrent(this._modified <= 0 ? null : current);
        }
      } else { // Reset 
        if (this.isDirty()) {
          var original = this.getOriginal();
          modified = qx.lang.Object.clone(this.getCurrent());
          for (var field in modified) {
            if (modified.hasOwnProperty(field)) {
              // Get New Value
              modified[field] = original[field];
            }
          }

          this._modified = 0;
          this.setCurrent(null);
        }
      }

      // Setting New Current Value
      if (modified !== null) {
        this.fireDataEvent('store-fields-changed', modified);
      }

      return modified;
    }, // FUNCTION: reset
    /*
     *****************************************************************************
     PROTECTED METHODS
     *****************************************************************************
     */
    _initializePackage: function(callback) {
      var metaPackage = this.getMetaPackage();
      this._throwPackageNotSet(metaPackage !== null);

      if (!metaPackage.isReady()) { // Initialize the Package - if it hasn't already been initialized
        metaPackage.initialize({
          'ok': function(metadata) {
            // Initialize the DataStore
            this._initializeFields(callback);
          },
          'nok': function(message) {
            // Initialize the DataStore
            this._callbackModelReady(callback, false, message);
          },
          'context': this});
      } else {
        // Initialize the Fields Package (If Different)
        this._initializeFields(callback);
      }
    }, // FUNCTION: _setFieldsPackage
    _initializeFields: function(callback) {
      var fieldsPackage = this._getFieldsPackage();

      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(fieldsPackage, tc.meta.packages.IFieldsPackage, "[Meta Package] Is not of the expected type!");
      }

      if (!fieldsPackage.isReady()) { // Initialize the Package - if it hasn't already been initialized
        fieldsPackage.initialize({
          'ok': function(metadata) {

            // Initialize Fields
            var fields = fieldsPackage.getFields();
            if (fields.length > 0) {
              var original = {};
              if (qx.lang.Type.isObject(this._storeIV)) {
                for (var field in this._storeIV) {
                  if (this._storeIV.hasOwnProperty(field) && fieldsPackage.hasField(field)) {
                    original[field] = this._storeIV[field];
                  }
                }
              }

              this.setOriginal(original);
              this.setCurrent(null);
              this._modified = 0;
              this._bReady = true;
            } else {
              this._bReady = false;
            }

            this._callbackModelReady(callback, this._bReady, "Invalid Fields Package");
          },
          'nok': function(message) {
            // Initialize the DataStore
            this._callbackModelReady(callback, false, message);
          },
          'context': this});
      } else {
        // Initialize the DataStore
        this._bReady = true;
        this._callbackModelReady(callback, true);
      }
    }, // FUNCTION: _initialize
    /**
     * Return's an IFieldsMetaPackage for the Store
     */
    _getFieldsPackage: function() {
      /* NOTE: Why does this function exist?
       * Because for IFieldStorage requires an IFieldsPackage in order to Initialize the store,
       * but, IFormStorage requires an IFormPackage to initialize itself (because it
       * requires the Service Meta Data). Since the IFormPackage, also contains
       * an IFieldsPackage, we have to allow an overridable function to get the
       * we require.
       */
      return this.getMetaPackage();
    },
    _prepareCallback: function(callback) {

      // Setup Default Callback Object
      var event_this = this;
      var newCallback = {// DEFAULT: No Callbacks - Fire Events
        'ok': function(result) {
          event_this.fireEvent('ok');
        },
        'nok': function(error) {
          event_this.fireDataEvent('nok', error);
        },
        'context': event_this
      };

      // Update Callback Object with User Parameters
      if (qx.lang.Type.isObject(callback)) {
        if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
          newCallback['ok'] = callback['ok'];
        }

        if (callback.hasOwnProperty('nok') && qx.lang.Type.isFunction(callback['nok'])) {
          newCallback['nok'] = callback['nok'];
        }

        if (callback.hasOwnProperty('context') && qx.lang.Type.isObject(callback['context'])) {
          newCallback['context'] = callback['context'];
        }
      }

      return newCallback;
    }, // FUNCTION: _buildCallback
    _callbackModelReady: function(callback, ok, message) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertObject(callback, "[callback] is not of the expected type!");
      }

      if (ok) {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertFunction(callback['ok'], "[callback] is missing [ok] function!");
          qx.core.Assert.assertObject(callback['context'], "[callback] is missing call [context]!");
        }

        callback['ok'].call(callback['context']);
      } else {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertFunction(callback['nok'], "[callback] is missing [nok] function!");
          qx.core.Assert.assertObject(callback['context'], "[callback] is missing call [context]!");
        }

        callback['nok'].call(callback['context'], message);
      }
    }, // FUNCTION: _callbackModelReady
    /*
     *****************************************************************************
     EXCEPTION GENERATORS
     *****************************************************************************
     */
    _throwIsStoreReady: function() {
      if (!this.isReady()) {
        throw "The Store has not been initialized";
      }
    },
    _throwPackageNotSet: function(exists) {
      if (!exists) {
        throw "The Store is missing the Meta Package";
      }
    },
    _throwFieldNotExists: function(field, exists) {
      if (!exists) {
        throw "The Field [" + field + "] does not belong to the model";
      }
    },
    _throwFieldNotMutable: function(field, exists) {
      if (!exists) {
        throw "The Field [" + field + "] cannot be modified";
      }
    }
  } // SECTION: MEMBERS
});
