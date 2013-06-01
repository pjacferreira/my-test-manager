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
qx.Class.define("tc.meta.FieldsDataStore", {
  extend: qx.core.Object,
  implement: tc.meta.interfaces.IFieldsDataStore,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a change to the fields definitions occurs.
     * Note:
     * - Every Fields Definitions change, automatically clears and removes
     * the fields. ALL CHANGES ARE LOST, and ALL NEW FIELDS ARE RESET TO
     * NULL (so a 'change-fields-value' is implied but never sent).
     * 
     */
    "change-fields-meta": "qx.event.type.Event",
    /**
     * Fired when a single field's value is modified.
     * The data returned is an object with the following format:
     * {
     *   'field-name' : value
     *   ...                   | If more than one field is modified
     *   'field-name' : value  |
     * }
     */
    "change-fields-value": "qx.event.type.Data"
  },
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
    }
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * 
   * @param fieldsmeta {Object|NULL} Field Metadata Definition
   * @param valuemap {Object|NULL} A Hash Map of Field => value tuplets, to set
   *   the initial field values
   */
  construct: function(fieldsmeta, valuemap) {
    this.base(arguments);

    // Initialize the Objects Metadata
    if (qx.lang.Type.isObject(fieldsmeta)) {
      this.setFieldsMeta(fieldsmeta);
    }

    // Initialize Fields Data
    if (valuemap != null) {
      if (this._fields != null) {
        this._setFieldsValues(valuemap, true);
      } else {
        throw "Can't set initial values, before setting the Field's Metadata"
      }
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleare Field's Metadata
    this._fields = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Counter for the current number of fields modified relative to the original values
    _modified: 0,
    // Fields Metadata
    _fields: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Verify is the Data Store has been Initialized with Values
     *  
     * @return {Boolean} 'true' if the data store has been initialized with a set of values, 'false' otherwise.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     */
    isInitialized: function() {
      if (this._fields != null) {
        return this.getOriginal() != null ? true : false;
      }

      throw "Fields Definitions not Set";
    },
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
    setFieldsMeta: function(metadata) {
      if (qx.lang.Type.isObject(metadata)) {
        var new_metadata = {};
        var field_count = 0;
        for (var field in metadata) {
          if (metadata.hasOwnProperty(field)) {
            if (!qx.lang.Type.isObject(metadata[field])) {
              // Not a Field (Skip)
              continue;
            }

            if (metadata[field].hasOwnProperty('type')) {
              // All Field Definitions are required to have a 'type' property
              new_metadata[field] = qx.lang.Object.clone(metadata[field], true);
              ++field_count;
            }
          }
        }

        if (field_count > 0) {
          this.setOriginal({});
          this.setCurrent(null);
          this._modified = 0;
          this._fields = new_metadata;
          this.fireEvent("change-fields-meta");
          return true;
        } else {
          throw "Metadata provided, contains no valid field definitions"
        }
      }

      throw "Metadata provided is not valid";
    }, // FUNCTION: setFieldsMeta
    /**
     * Test if a field is defined in the current data store.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is managed, 'false' otherwise.
     */
    hasField: function(name) {
      if (this._fields != null) {
        return this._fields.hasOwnProperty(name);
      }

      throw "Fields Definitions not Set";
    }, // FUNCTION: hasField
    /**
     * Test if a field can be modified in the current data store.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is modifiable, 'false' otherwise.
     * - throws an exception if the field is not part of the current active field
     *   list
     */
    isFieldMutable: function(name) {
      if (this.hasField(name)) {
        var field = this._fields[name];
        return !field.hasOwnProperty('value') || (field['value'] != 'auto');
      }

      throw "Field [" + name + "] does not exist.";
    }, // FUNCTION: isFieldMutable
    /**
     * Test if a field has a value Set.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' field has a value defined, 'false' otherwise.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     *  - throws exception if the field does not exist in the metadata
     */
    isFieldSet: function(name) {
      if (this.hasField(name)) {
        // Test if the Field has a new Value Set
        var source = this.getCurrent();
        if (source.hasOwnProperty(name)) {
          return true;
        }

        // Test if Field has Value Set in the Original
        source = this.getCurrent();
        if (source.hasOwnProperty(name)) {
          return true;
        }

        return false;
      }

      throw "Field [" + name + "] does not exist.";
    }, // FUNCTION: isFieldSet
    /**
     * Clear any currently set value. This means that, if the field has been modified,
     * it will be reset to it's original value (not the same as explicitly setting a
     * field to NULL).
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is managed, 'false' otherwise.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     *  - throws exception if the field does not exist in the metadata
     */
    clearFieldValue: function(name) {
      if (this.hasField(name)) {
        var current = this.getCurrent();
        if (current.hasOwnProperty(name)) {
          delete current[name];
          this._modified--;
          this.setCurrent(this._modified <= 0 ? null : current);
        }

        return true;
      }

      throw "Field [" + name + "] does not exist.";
    }, // FUNCTION: clearFieldValue
    /**
     * Retrieves a field's value if it exists in the data store.
     *  
     * @param name {String} Field name whose value is to be retrieved.
     * @param original {Boolean} 'true' get original value loaded (or as of last save), 'false' (default) current value
     * @return {Var} Field's value.
     *  Notes:
     *  - throws exception if the field does not exist in the metadata
     */
    getFieldValue: function(name, original) {
      if (this.hasField(name)) {
        var source = original ? this.getOriginal() : this.getCurrent();
        if ((source != null) && source.hasOwnProperty(name)) {
          return source[name];
        }

        // Try to Get From Orginal
        if (!original) {
          source = this.getOriginal();
          return (source != null) && source.hasOwnProperty(name) ? source[name] : null;
        }

        return null;
      }

      throw "Field [" + name + "] does not exist.";
    }, // FUNCTION: getFieldValue
    /**
     * Retrieves a field's value if it exists in the data store.
     *  
     * @param original {Boolean} 'true' get original value loaded (or as of last save), 'false' (default) current+original value
     * @return {Object} field name, value tuplets, for the fields that exist in 
     *  the data source. If a field has no value, than null will be returned instead.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     */
    getFieldsValues: function(original) {
      if (this._fields != null) {
        var values = this.getOriginal();
        if (original) {
          return this.getOriginal();
        } else {
          return this.isModified() ? qx.lang.Object.mergeWith(this.getOriginal(), this.getCurrent(), true) : this.getOriginal();
        }
      }

      throw "Fields Definitions not Set";
    }, // FUNCTION: getFieldsValues
    /**
     * Set a Field's Value in the data store (but changes are only flushed back
     * to the data source, when a create or update is called).
     *  
     * @param name {String} Field name whose value is to be modified.
     * @param value {Var} Field's new value.
     * @return {Var} Field's old value.
     * 
     * Notes:
     * - throws an exception if the field does not exist in the data store
     * - throws an exception if the field is inmutable
     * - fires field-changed, on successful completion of value change
     */
    setFieldValue: function(name, value) {
      
      /* TODO : Have to coeerce values to the correct type
       * (i.e. Form Widgets will always provide values as string (more than likely)
       *  this has to be converted to the correct field type, for example, an 
       *  integer)
       */
      if (this.isFieldMutable(name)) {
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
              this.fireDataEvent('change-fields-value', {name: value});
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
            this.fireDataEvent('change-fields-value', {name: value});
          }
        } else {
          current[name] = value
          this._modified++;
          this.setCurrent(current);

          // Setting New Current Value
          this.fireDataEvent('change-fields-value', {name: value});
        }


        return oldValue;
      }

      throw "Field [" + name + "] can not be modified.";
    }, // FUNCTION: setFieldValue
    /**
     * Set a Field's Value in the data store (but changes are only flushed back
     * to the data source, when a create or update is called).
     *  
     * @param tuplets {Object} an object containing field : value tuplets, for
     *   the fields to be modified.
     * @return {Var} Field's old value.
     * 
     * Notes:
     * - throws an execption if no metadata is loaded
     * - will silently discard any field value tuplets, for which no
     *   corresponding field exists in the current metadata
     * - will silently discard any field value tuplets, for which the
     *   corresponding fields in the metadata, is defined as immutable
     * - fires field-changed, on successful completion for the values that
     *   have been modified
     */
    setFieldsValues: function(tuplets, original) {
      var fields_modified = {};
      var fields_old_value = {};
      var changes = 0;

      if (original) { // Setting the Original Values (i.e. Initializing the Fields)
        // Initialize Fields Modified to NULLs for all Field's that are currently modified
        var current = this.getCurrent();
        if (current != null) {
          for (var field in current) {
            if (current.hasOwnProperty(field)) {
              fields_modified[field] = null;
            }
          }
        }

        // Initialize Fields Modified to NULLs for all Field's that were originally set
        var original = this.getOriginal();
        if (original != null) {
          for (field in original) {
            if (original.hasOwnProperty(field)) {
              fields_modified[field] = null;
            }
          }
        }

        // Merge in Values in Tuplets
        var new_original = {};
        for (field in tuplets) {
          if (tuplets.hasOwnProperty(field) &&
                  this._fields.hasOwnProperty(field)) {
            new_original[field] = tuplets[field];
            fields_modified[field] = tuplets[field];
            ++changes;
          }
        }

        if (changes > 0) { // Some values were modified, so keep changes
          this.setOriginal(new_original);
          this.setCurrent(null);
          this._modified = 0;
        }
      } else { // Changing Current Values
        var value = null;
        var current = this.getCurrent();
        current = (current != null) ? current : {};


        var original = this.getOriginal();
        var oldValue = null;

        for (var field in tuplets) {
          if (tuplets.hasOwnProperty(field) &&
                  this.hasField(field) &&
                  this.isFieldMutable(field)) {

            value = tuplets[field];

            // Check if we are restoring the value back to it's original settings
            if (original.hasOwnProperty(field)) {
              if (original[field] == field) {
                if (current.hasOwnProperty(field)) {
                  fields_old_value[field] = current[field];
                  delete current[field];
                  this._modified--;
                  this.setCurrent(this._modified > 0 ? current : null);
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
                this.setCurrent(current);
                changes++;
              }
            } else {
              current[field] = value
              this._modified++;
              this.setCurrent(current);
              changes++;
            }
          }
        }
      }

      if (changes > 0) {
        // Fire the Event to Show that a Field's Value has Changed
        this.fireDataEvent('change-fields-value', fields_modified);
        return fields_old_value;
      }

      // No Changes
      return null;
    }, // FUNCTION: setFieldsValues
    /**
     * Indicates if any of the fields in the data source, have been modified.
     *
     * @return {Boolean} 'true' if any of the field values have been modified, 'false' otherwise
     */
    isModified: function() {
      return this.getCurrent() != null;
    } // FUNCTION: isModified
  }
});
