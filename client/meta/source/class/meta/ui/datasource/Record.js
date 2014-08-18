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
 * Base Meta Package Class
 */
qx.Class.define("meta.ui.datasource.Record", {
  extend: qx.core.Object,
  implement: meta.api.ui.datasource.IRecord,
  include: [
    meta.events.mixins.MMetaEventDispatcher,
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notify of Changes in the Records Fields Values
    "change-output-values": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * RecordSet Constructor
   * 
   * @param record {utility.Map?null} Connection associated with the Record Set
   * @param link {meta.ui.datasource.Connection|meta.ui.datasource.RecordSet?null} Connection or Record Set for Record
   * 
   */
  construct: function(record, link) {
    this.base(arguments);

    // Initialize
    // Is a Link Provided?
    if (qx.lang.Type.isObject(link)) { // YES
      // Is the link a Connection Object?
      if (qx.Class.implementsInterface(link, meta.ui.datasource.Connection)) { // YES
        this._applyConnection(link, null);
      } else if (qx.Class.implementsInterface(link, meta.ui.datasource.RecordSet)) { // NO: It's a Record Set
        this._applyRecordSet(link, null);
      }
    }
    // ELSE: No Link Provided or it is Invalid

    // Initialize Fields List from Record
    this.__fields = new utility.Map();
    this.__record = new utility.Map();

    // Create the Modification Registry
    this.__modified = {'fields': new utility.Map(), 'values': new utility.Map()};

    // Was the Record Data of a Valid Type?
    if (qx.lang.Type.isObject(record) &&
      record.hasOwnProperty('__type') &&
      record.__type === 'record') { // YES

      this._initialize(record, false);
    } else { // NO: Just Mark it as NEW
      this.__record['$$NEW'] = true;
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup
    this.__idField = null;
    this.__fields = null;
    this.__record = null;
    this.__modified = null;
    this.__connection = null;
    this.__recordset = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __idField: null,
    __fields: null,
    __record: null,
    __modified: false,
    __connection: null,
    __recordset: null,
    /*
     ***************************************************************************
     METHODS (meta.api.ui.datasource.IRecordSet)
     ***************************************************************************
     */
    /**
     * Register List of Recognized Record Fields
     *
     * @param fields {String[]|String} Field or List of Fields
     */
    registerFields: function(fields) {
      // Is the parameter an Array?
      if (qx.lang.Type.isArray(fields)) { // YES
        var field;
        for (var i = 0; i < fields.length; ++i) {
          field = utility.String.v_nullOnEmpty(fields[i]);
          // Is the field name valid and is it not currently defined for the record?
          if ((field !== null) && !this.__fields.has(field)) { // YES
            this.__fields.set(field, true);
          }
        }
      } else if (qx.lang.Type.isString(fields)) { // NO: Is it a String?
        // YES
        var field = utility.String.nullOnEmpty(fields);
        // Is the field name valid and is it not currently defined for the record?
        if ((field !== null) && !this.__fields.has(field)) { // YES
          this.__fields.set(field, true);
        }
      }
    },
    /**
     * Retrieve all the List of Field IDs in the Record.
     *
     * @return {String[]} List of Field ID's
     */
    listFields: function() {
      return this.__fields.keys();
    },
    /**
     * Test if the Field exists in the Record
     *
     * @param id {String} Field ID
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    hasField: function(id) {
      id = utility.String.v_nullOnEmpty(id);
      return (id !== null) && this.__hasField(id);
    },
    /**
     * Test if the Record has an ID Field Defined
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    hasIDField: function() {
      return (this.__idField !== null);
    },
    /**
     * Set the Record's ID Field
     *
     * @param field {String} Field that represents that defines the Record's Unique Identifier
     * @return {String|null} Previous ID Field
     * @throw {string} Exception if field is not part of the record.
     */
    setIDField: function(field) {
      field = utility.String.v_nullOnEmpty(field);
      this.__exception(field === null, "Missing field parameter.");
      this.__exception(!this._hasField(field), "Field is not part of the record.");

      var old = this.__idField;
      this.__idField = field;

      // If The ID Field is Already Set than we assume that this is Not a NEW Record
      if (this.__record.hasOwnProperty('$$NEW') &&
        this.isFieldSet(field)) {
        delete this.__record['$$NEW'];
      }

      return old;
    },
    /**
     * Is this a Disconnected Record?
     * 
     * For disconnected Records, flush() does not "save" changes back to the
     * back-end Data Source...
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isDisconnected: function() {
      return this.getConnection() !== null;
    },
    /**
     * Get Connection Object Associated with this Record.
     *
     * @return {meta.api.ui.datasource.IConnection|null} Record's Connection
     */
    getConnection: function() {
      return this.__recordset !== null ? this.__recordset.getConnection() : this._getConnection();
    },
    /**
     * Get Connection Object Associated with this Record Set.
     * 
     * For Direct Records (those retrieved directly from the connection), this
     * returns "null" as the Record Set.
     *
     * @return {meta.api.ui.datasource.IRecordSet|null} Record's Record Set
     */
    getRecordSet: function() {
      return this.__recordset;
    },
    /**
     * Record Set/Connection Allows Record Creation?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canCreate: function() {
      // Is this record part of a Data Set?
      var recordset = this.getRecordSet();
      if (recordset !== null) { // YES
        return recordset.canCreate();
      }

      // Is this Record Connected?
      var connection = this._getConnection();
      if (connection !== null) { // YES
        return connection.isReady() && connection.canCreate();
      }

      // ELSE: NO (Disconnected Record)      
      return false;
    },
    /**
     * Record Set/Connection Allows Load/Reload of Records?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canRead: function() {
      // Is this record part of a Data Set?
      var recordset = this.getRecordSet();
      if (recordset !== null) { // YES
        return recordset.canRead();
      }

      // Is this Record Connected?
      var connection = this._getConnection();
      if (connection !== null) { // YES
        return connection.isReady() && connection.canRead();
      }

      // ELSE: NO (Disconnected Record)      
      return false;
    },
    /**
     * Record Set/Connection Allows Record Update?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canUpdate: function() {
      // Is this record part of a Data Set?
      var recordset = this.getRecordSet();
      if (recordset !== null) { // YES
        return recordset.canUpdate();
      }

      // Is this Record Connected?
      var connection = this._getConnection();
      if (recordset !== null) { // YES
        return connection.isReady() && connection.canUpdate();
      }

      // ELSE: NO (Disconnected Record)      
      return false;
    },
    /**
     * Record Set/Connection Allows Record Deletions?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canDelete: function() {
      // Is this record part of a Data Set?
      var recordset = this.getRecordSet();
      if (recordset !== null) { // YES
        return recordset.canDelete();
      }

      // Is this Record Connected?
      var connection = this._getConnection();
      if (recordset !== null) { // YES
        return connection.isReady() && connection.canDelete();
      }

      // ELSE: NO (Disconnected Record)      
      return false;
    },
    /**
     * Is the Record Marked for Deletion?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isDeleted: function() {
      // Is the Record Marked (for Deletion)?
      if (this.__record.hasOwnProperty('$$DELETED')) { // YES
        return this.__record['$$DELETED'];
      }
      // ELSE: No
      return false;
    },
    /**
     * Delete the Record. If the record is part of a record set,
     * this can be done immediately (now === true) or later (now === false).
     * If not part of a record set, then delete is done immediately.
     *
     * @param now {boolean?false} 'TRUE' delete the record immediately, 'FALSE' de.lete later (if part of record set)
     * @return {Boolean} 'true' changes committed or in the process of, 'false' did nothing
     * @throw {string} Exception if Record is Disconnected or Read-Only
     */
    deleteRecord: function(now) {
      // Throw Exception if Outside Possible Range of Indexes
      this.__exception(!this.isDisconnected(), "Record is Disconnected.");
      this.__exception(!this.canDelete(), "Record Deletion NOT ALLOWED.");

      // Is the Record Already Deleted?
      if (!this.isDeleted()) { // NO
        // Is Record Part of a Record Set?
        var recordset = this.getRecordSet();
        if (recordset !== null) { // YES: Let the Record Set Handle Deletion
          recordset.deleteRecord(this, !!now);
        } else { // NO: Delete using the Connection
          this._getConnection().deleteRecord(this);
        }
        return true;
      }
      // ELSE: YES (Do Nothing)
      return false;
    },
    /**
     * Is this a New Record?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isNew: function() {
      // Is the Record Marked (as New)?
      if (this.__record.hasOwnProperty('$$NEW')) { // YES
        return this.__record['$$NEW'];
      }
      // ELSE: No
      return false;
    },
    /**
     * Test if the Field has a value defined (even if it is only null)
     *
     * @param id {String} Field ID
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isFieldSet: function(id) {
      return this.hasField(id) && this.__record.has(id);
    },
    /**
     * Get the Field's value
     *
     * @param id {String} Field ID
     * @return {Var} Field Value
     * @throw {string} Exception if Invalid Field ID or Field has no value set
     */
    getValue: function(id) {
      id = utility.String.v_nullOnEmpty(id);
      this.__exception(!this.__hasField(id), "Field is not part of the record.");
      this.__exception(!this.isFieldSet(id), "Field is not set.");

      return this.__record.get(id);
    },
    /**
     * Get a Field<-->Value Map for all the existing fields
     *
     * @return {Map} Field<-->Value Map
     */
    getValues: function() {
      return this.__record.map();
    },
    /**
     * Get the value of the Record's ID Field (which should uniquely identify
     * this record)
     *
     * @return {Var} Record ID
     * @throw {string} Exception if no Field defined as the Record ID or The
     *   field has no value.
     */
    getRecordID: function() {
      this.__exception(this.__idField === null, "No ID Field Defined for the Record.");
      this.__exception(!this.isFieldSet(this.__idField), "Field is not set.");

      return this.__record.get(this.__idField);
    },
    /**
     * Set the Field's Value.
     *
     * @param id {String} Field ID
     * @param value {Var} Field Value
     * @return {Var} Old Field Value
     * @throw {string} Exception if Invalid Field ID
     */
    setField: function(id, value) {
      id = utility.String.v_nullOnEmpty(id);
      this.__exception(!this._hasField(id), "Field is not part of the record.");

      // Was the field modified?
      var result = this._setField(id, value);
      if (result.modified) { // YES          
        // Does it have a previous value?
        if (result.hasOwnProperty('old')) { // YES
          this.this._mx_med_fireEventOK("change-output-values", utility.Object.singleProperty(id, value));
          return result.old;
        }
        // ELSE: NO Previous Value
        this._mx_med_fireEventOK("change-output-values", utility.Object.singleProperty(id, value));
      }
    },
    /**
     * Set the values for Multiple Fields.
     * In the case of that we are initializing the fields, all previous field
     * modifications are simply dropped.
     *
     * @param map {Map} Field<-->Value Map
     * @param nofire {Boolean?false} Don't Fire Event Notifying of Changes?
     * @param initialize {Boolean?false} Are we initializing the fields?
     * @return {Map|null} Map of previous values or 'null'
     */
    setFields: function(map, nofire, initialize) {
      // Make Sure that Optional Bools have a Valid Bool Value
      nofire = !!nofire;
      initialize = !!initialize;

      if (initialize) {
        this.__fields.reset();
        this.__record.reset();
        this.__modified.fields.reset();
        this.__modified.values.reset();
      }

      /* TODO: Consider the following problem?
       * If we are using the setFields to initialize the records, which set of
       * registered fields should we use?
       * 1. Fields already registered (of which, if the record was not previously
       * initialized, or registerFields was not called, there might be NONE).
       * 2. The fields that are part of the 'map' (only)
       * 3. Both, existing registered fields, and those in the 'map'
       * 
       * Current Strategy:
       * 1. If no fields register, use the fields in the 'map'
       * 2. If fields registered, merge in, any new fields in the 'map'
       */
      var modified = this._initialize(map, !initialize);

      // Are we supposed to fire an Event?
      var map = null;
      if (!nofire) { // YES
        // Were we performing initialization?
        if (initialize) { // YES
          var fields = this.__fields.keys();
          // Do we have any fields specified in the record?
          if (fields.length) {
            var field;
            map = {};
            for (var i = 0; i < fields.length; ++i) {
              field = fields[i];
              if (this.__record.has(field)) {
                map[field] = this.__record.get(field);
              } else {
                map[field] = undefined;
              }
            }
          }
        } else if (modified.count()) { // NO: Were any fields modified?
          // YES
          map = modified.map();
        }

        // Do we have an changes to notify of?
        if (map !== null) {
          this._mx_med_fireEventOK("change-output-values", map);
        }
      }

      return map;
    },
    /**
     * Reset the Field's Value to undefined state (Undefined Field's will not
     * have their value's flushed back to the back end Data Source)
     *
     * @param id {String} Field ID
     * @return {Var} Old Field Value
     * @throw {string} Exception if Invalid Field ID
     */
    clearField: function(id) {
      return this.setField(id);
    },
    /**
     * Has the Data Source been Modified since it was loaded?
     *
     * @param field {String?null} Field ID to test if dirty, or 'null' to test for all
     *   field changes
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isDirty: function(field) {
      // Is this a Record Marked as New / Deleted?
      if (this.isNew() || this.isDeleted()) { // YES: These Records are by their nature dirty
        return true;
      }

      // Do we have any modifications
      if (this.__modified.fields.count()) { // YES
        // Are we testing a single field change?
        field = utility.String.v_nullOnEmpty(field);
        if (field !== null) { // YES
          return this.__modified.fields.has(field);
        } else { // NO: Testing All Changes
          return true;
        }
      }
      //ELSE : No Modifications
      return false;
    },
    /**
     * Reset the Fields Value's Back to the Previous State.
     * 
     * @return {Boolean} 'true' if any modifications performed, 'false' Otherwise
     */
    reset: function() {
      if (this.isDirty()) {
        var modified = new utility.Map();
        var value, field, fields = this.listFields();
        for (var i = 0; i < fields.length; ++i) {
          field = fields[i];

          // Was the Field Modified from Original Value?
          if (this.__wasFieldModified(field)) { // YES: Reset Original Value
            value = this.__getFieldModification(field);
            this.__removeFieldModification(field);
            this.__setFieldValue(field, value);
            modified.add(field, value);
          }
        }

        // Clear Modifications
        this.__modified.fields.reset();
        this.__modified.values.reset();

        // Do we have any Fields Modified?
        if (modified.count()) {
          this.this._mx_med_fireEventOK("change-output-values", modified.map());
          return true;
        }
      }
      // ELSE: Nothing to modify
      return false;
    },
    /**
     * Commit Changes to the Record. If the record is part of a record set,
     * this can be done immediately (now === true) or later (now === false).
     * If not part of a record set, then commit forces changes (immediately).
     *
     * @param now {boolean?false} 'TRUE' commit the record immediately, 'FALSE' commit later (if part of record set)
     * @return {Boolean} 'true' changes committed or in the process of, 'false' did nothing
     * @throw {string} Exception if Record is Disconnected or Read-Only
     */
    commitRecord: function(now) {
      // Throw Exception if Outside Possible Range of Indexes
      this.__exception(!this.isDisconnected(), "Record is Disconnected.");

      // Has the record been deleted?
      if (!this.isDeleted()) { // NO
        // Is this a New Record
        if (this.isNew()) { // YES
          this.__exception(!this.canCreate(), "Record Creation not Allowed.");

          // Is Record Part of a Record Set?
          var recordset = this.getRecordSet();
          if (recordset !== null) { // YES: Let the Record Set Handle Write
            recordset.commitRecord(this, !!now);
          } else { // NO: Write Using Connection
            this._getConnection().createRecord(this);
          }
        } else { // NO
          this.__exception(!this.canUpdate(), "Record Update NOT ALLOWED.");

          // Is Record Part of a Record Set?
          var recordset = this.getRecordSet();
          if (recordset !== null) { // YES: Let the Record Set Handle Write
            recordset.commitRecord(this, !!now);
          } else { // NO: Write Using Connection
            this._getConnection().updateRecord(this);
          }
        }

        return true;
      }
      // ELSE: Record hasn't been modified OR has been modified
      return false;
    },
    /**
     * Load the record, using the current field values or provided values
     * 
     * @abstract
     * @param map {Map} Field<-->Value Map
     * @throw {string} Exception if Record is Disconnected or Connection doesn't allow loading
     */
    loadRecord: function(map) {
      this.__exception(!this.isDisconnected(), "Record is Disconnected.");
      this.__exception(!this.canRead(), "Record Loading not Allowed.");

      // Is Record Part of a Record Set?
      var recordset = this.getRecordSet();
      if (recordset !== null) { // YES: Let the Record Set Handle Write
        recordset.loadRecord(this, map);
      } else { // NO: Write Using Connection
        this.getConnection().loadRecord(this, map);
      }
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    _applyConnection: function(connection, old) {
      var events = ["record-read", "record-commit", "record-delete"];
      if (old !== null) {
        this._mx_meh_detach(events, old);
      }

      this.__connection = connection;
      this._mx_meh_attach(events, this.__connection);
    },
    _applyRecordSet: function(recordset, old) {
      var events = ["record-read", "record-commit", "record-delete"];
      if (old !== null) {
        this._mx_meh_detach(events, old);
      }

      this.__recordset = recordset;
      this._mx_meh_attach(events, this.__recordset);
    },
    /**
     * Get Connection Object Associated with this Record.
     *
     * @return {meta.api.ui.datasource.IConnection|null} Record's Connection
     */
    _getConnection: function() {
      return this.__connection;
    },
    _fire_fieldChange: function(field, value, old) {
      this._mx_med_fireEvent("change-output-values", [utility.Object.singleProperty(value, old)]);
    },
    _fire_fieldsChange: function(fields) {
      this._mx_med_fireEvent("change-output-values", [fields]);
    },
    _setField: function(field, value) {
      /* A Field can have 3 possible values:
       * 1. Unset
       * - field exists in the field list
       * - but not in the record
       * 2. NULL (field exists, but has a 'null' value)
       * - field exists in the field list
       * - record[field) === null
       * 3. Not NULL (field exists, but has an non 'null' value).
       * - field exists in the field list
       * - record[field] !== null
       * 
       * Modified state can 3:
       * 1. Field has not been modified
       * - field doesn't exist in the modified field list
       * 2. Field has been modified, but previous value is 'undefined'
       * - field exists in the modified field list
       * - field does not exist in the modified value
       * 3. Field has been modified and the previous value was defined
       * - field exists in the modified field list
       * - field in the modified value map
       */
      var current = this.__getFieldValue(field);

      // Is the New Value Different from the Old?
      if (current !== value) { // NO: Field is being modified
        // Was the field already modified (changed from it's pristine state)?
        if (this.__wasFieldModified(field)) { // YES
          var previous = this.__getFieldModification(field);

          // Are we resetting the field back to it's original un-modified value
          if (previous === value) { // YES
            this.__removeFieldModification(field);
          }

          /* NOTE: 
           * The Modified Value Represents the Last "Clean/Saved" State of the Field,
           * therefore, if a field has been changed twice, this "original value"
           * is never changes (this allows us to do a reset, back to the last
           * pristine state).
           */
          this.__setFieldValue(field, value);
        } else { // NO: 1st Change
          this.__setFieldModification(field, current);
          this.__setFieldValue(field, value);
        }

        return {'modified': true, 'old': current};
      }
      // ELSE: Current Value === Existing Value (No Change)
      return {'modified': false};
    },
    _initialize: function(record, save_changes) {
      save_changes = !!save_changes;

      // List of Modified Fields
      var modified = new utility.Map();

      // Does the Record have a Key/ID Field Defined?
      var key = record.hasOwnProperty('__key') ? record.__key : null;
      if (key !== null) { // YES
        this.__idField = key;

        // Has this field already been included in the list of fields?
        if (!this.__fields.has(key)) { // NO
          this.__fields.add(key, true);
        }

        // Has the record a value set for this field?
        if (record.hasOwnProperty(key)) { // YES
          var value = record[key];
          if (save_changes) {
            if (this._setField(field, value).modified) {
              modified.add(key, value);
            }
          } else {
            this.__setFieldValue(key, value);
          }
        }
      }

      // Does the Record have a List of Possible Fields?
      var field, value, fields = record.hasOwnProperty('__fields') ? record.__fields : null;
      if (fields !== null) { // YES: Then use these as the source for possible record entries
        for (var i = 0; i < fields.length; ++i) {
          field = fields[i];
          if ((key !== null) && (field !== key)) {
            this.__fields.add(field, true);
            // Is the Field Value Set?
            value = record.hasOwnProperty(field) ? record[field] : undefined;
            if (save_changes) {
              if (this._setField(field, value).modified) {
                modified.add(field, value);
              }
            } else {
              this.__setFieldValue(field, value);
            }
          }
        }
      } else { // NO: All the fields have to come from properties set on the record
        for (field in record) {
          // Is the field valid? (Note: we ignore field names starting with '_')
          if (record.hasOwnProperty(field) &&
            (field[0] !== '_')) {
            this.__fields.add(field, true);
            if (save_changes) {
              if (this._setField(field, record[field]).modified) {
                modified.add(field, value);
              }
            } else {
              this.__setFieldValue(field, record[field]);
            }
          }
        }
      }

      return modified;
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _passMetaEvent: function(type, success, code, message, parameters) {
      switch (type) {
        case "record-read":
        case "record-commit":
        case "record-delete":
          // Is this an Event Related to 'this' Record?
          var record = qx.lang.Type.isArray(parameters) ? parameters[0] : null;
          if ((record === null) || (record !== this)) { // YES
            return false;
          }

          // Is this a failure?
          if (success === false) { // YES
            // Handle Failures Here
            var code = parameters[1];
            var message = parameters[2];
            this.error("ERROR procssing [" + type + "] with Error Code [" + code + "] and Message [" + (message !== null ? message : '') + "]");
            return false;
          }
      }

      return true;
    },
    _processMetaRecordReadOK: function(code, message, record, values) {
      // The Record was Deleted
      // TODO: In the case of a Read (values) represent all the possible set of field values
      this.setFields(values, false, true);
    },
    _processMetaRecordCommitOK: function(code, message, record, values) {
      // The Record was Saved
      // TODO: In the case of a Commit (values) represent only modifications to the existing values
      this.setFields(values, false, true);
    },
    _processMetaRecordDeleteOK: function(code, message, record, values) {
      // TODO Implement
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    __hasField: function(field) {
      return this.__fields.has(field);
    },
    __getFieldValue: function(field) {
      if (this.__record.has(field)) {
        return this.__record.get(field);
      }
    },
    __setFieldValue: function(field, value) {
      if (typeof value === 'undefined') {
        this.__record.remove(field);
      } else {
        // If we are setting the ID Field then we have to assume this is not a New Record
        if (this.__record.hasOwnProperty('$$NEW') &&
          this.hasIDField() &&
          (field === this.__idField)) {
          delete this.__record['$$NEW'];
        }

        this.__record.add(field, value);
      }
    },
    __wasFieldModified: function(field) {
      return this.__modified.fields.has(field);
    },
    __getFieldModification: function(field) {
      if (this.__modified.values.has(field)) {
        return this.__modified.values.get(field);
      }
    },
    __setFieldModification: function(field, value) {
      this.__modified.fields.set(field, true);
      if (typeof value !== 'undefined') {
        this.__modified.values.remove(field);
      } else {
        this.__modified.values.add(field, value);
      }
    },
    __removeFieldModification: function(field) {
      this.__modified.fields.remove(field);
      this.__modified.values.remove(field);
    },
    /**
     * Throw an exception, if the condition matched
     * 
     * @param condition {Boolean} Did the condition match?
     * @param message {String} Message for Exception
     */
    __exception: function(condition, message) {
      // Did the condition match?
      if (condition) { // YES: Throw Exception
        throw message;
      }
      // ELSE: NO     
    }
  } // SECTION: MEMBERS
});
