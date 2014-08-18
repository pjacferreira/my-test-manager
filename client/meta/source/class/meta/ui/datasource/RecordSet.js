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
qx.Class.define("meta.ui.datasource.RecordSet", {
  extend: qx.core.Object,
  implement: meta.api.ui.datasource.IRecordSet,
  include: [
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired on necessary meta events.
     */
    "meta": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * RecordSet Constructor
   * 
   * @param connection {Function?null} Connection associated with the Record Set
   * @param records {Function?null} Initial List of Records
   * @param pagesize {Integer?null} Page Size for Record Set
   * 
   */
  construct: function(connection, records, pagesize) {
    this.base(arguments);

    // Initialize
    // Is a Connection Provided?
    if (qx.lang.Type.isObject(connection) &&
      qx.Class.implementsInterface(connection, meta.ui.datasource.Connection)) { // YES
      this.__connection = connection;

      // Attach Meta Event Handler
      this._mx_meh_attach(this.__connection);
    }
    // ELSE: No Connection Provided or it is Invalid
    this.__records = records;
    this.__pendingRecords = new utility.Map();
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup
    this.__connection = null;
    this.__records = null;
    this.__pendingRecords = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __ready: false,
    __connection: null,
    __records: null,
    __pageSize: null,
    __pendingRecords: null,
    __cursor: 0,
    /*
     ***************************************************************************
     METHODS (meta.api.ui.datasource.IRecordSet)
     ***************************************************************************
     */
    /**
     * Is this a Disconnected Record Set?
     *
     * For disconnected Record Sets, flush() does not "save" changes back to the
     * back-end Data Source...
     * 
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isDisconnected: function() {
      return this.__connection === null;
    },
    /**
     * Get Connection Object Associated with this Record Set
     *
     * @return {meta.api.ui.datasource.IConnection|null} Record Set's Connection
     */
    getConnection: function() {
      return this.__connection;
    },
    /**
     * Is this a Paged Record Set (i.e. a partial load)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isPaged: function() {
      return this.__pageSize !== null;
    },
    /**
     * Is this the 1st Page in the Record Set?
     *
     * @abstract
     * @return {Boolean} 'true' if YES or Record Set Not Paged, 'false' Otherwise
     */
    isFirstPage: function() {
    },
    /**
     * Is this the Last Page in the Record Set?
     *
     * @abstract
     * @return {Boolean} 'true' if YES or Record Set Not Paged, 'false' Otherwise
     */
    isLastPage: function() {
    },
    /**
     * Load 1st Page
     * 
     * Note: All Unsaved Changes are Lost...
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throw {string} Exception Disconnected Record Set, or not a Paged Record Set
     */
    firstPage: function(ok, nok, context) {
    },
    /**
     * Load Previous Page
     * 
     * Note: All Unsaved Changes are Lost...
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throw {string} Exception Disconnected Record Set, or not a Paged Record Set
     */
    previousPage: function(ok, nok, context) {
    },
    /**
     * Load Next Page?
     * 
     * Note: All Unsaved Changes are Lost...
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throw {string} Exception Disconnected Record Set, or not a Paged Record Set
     */
    nextPage: function(ok, nok, context) {
    },
    /**
     * Load Last Page
     * 
     * Note: All Unsaved Changes are Lost...
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throw {string} Exception Disconnected Record Set, or not a Paged Record Set
     */
    lastPage: function(ok, nok, context) {
    },
    /**
     * Page Size for Record Set
     *
     * @abstract
     * @return {Integer} If Paged Record Set, size of page, if not, current number
     *   of records in the record set.
     */
    getPageSize: function() {
    },
    /**
     * Is the Record Set Empty?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isEmpty: function() {
      return (this.__records !== null) || (this.__records.length === 0);
    },
    /**
     * Current Number of Records in the Record Set
     *
     * @return {Integer} Current number of records in the set
     */
    getCount: function() {
      return this.__records !== null ? this.__records.length : 0;
    },
    /**
     * Is the Cursor at the 1st Row in the Record Set?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isFirst: function() {
      return this.__cursor === 0;
    },
    /**
     * Is the Cursor at the Last Row in the Record Set?
     * 
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isLast: function() {
      // Is the Record Set Empty?
      if (!this.isEmpty()) { // NOT EMPTY
        return this.__cursor === (this.__records.length - 1);
      }
      // ELSE: EMPTY
      return true;
    },
    /**
     * Position the Cursor at the 1st Record in the Record Set
     */
    first: function() {
      this.__cursor = 0;
    },
    /**
     * Position the Cursor at the Last Record in the Record Set
     *
     * @throw {string} Exception if at the First Row in the Record Set
     */
    previous: function() {
      this.__exception(this.isFirst(), "Cursor is at the Start of the Record Set.");
      this.__cursor--;
    },
    /**
     * Position the Cursor at the Last Record in the Record Set
     *
     * @throw {string} Exception if at the Last Record in the Record Set
     */
    next: function() {
      this.__exception(this.isLast(), "Cursor is at the End of the Record Set.");
      this.__cursor++;
    },
    /**
     * Position the Cursor at the Last Record in the Record Set
     */
    last: function() {
      this.__cursor = this.isEmpty() ? 0 : this.__records.length - 1;
    },
    /**
     * Get Current Cursor Position (i.e. Current Record in the Record Set as
     * pointed to by the Cursor)
     */
    rowIndex: function() {
      return this.__cursor;
    },
    /**
     * Position the Cursor at the the Index Specified. If Positive Integer,
     * the cursor position is set relative to the Start of the Record Set.
     * If Negative Intger, the cursor position is set relative to the End of the
     * Record Set.
     *
     * @param index {Integer} Index Position to position cursor at
     * @throw {string} Exception if invalid index
     */
    moveTo: function(index) {
      // Enable logging in debug variant
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInteger(index, "[index] is Invalid!");
      }

      // Move Cursor to New Position
      this.__cursor = this.__position(index);
    },
    /**
     * Get the Record at the Current Cursor Position, or at the position Specified
     *
     * @param index {Integer?null} Index Position (>= 0) to position cursor at
     * @throw {string} Exception if invalid index
     */
    getRecord: function(index) {
      // Get Record at the Index
      var position = this.__position(index);
      var record = this.__records[position];
      // Has the record already have a Record Object Associated?
      if (!record.hasOwnProperty('$$__RECORD')) { // NO: Create and Cache the Object
        record['$$__RECORD'] = new meta.ui.datasource.Record(record, this);
      }

      return record['$$__RECORD'];
    },
    /**
     * Record Set/Connection Allows New Records to be Created/Inserted?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canCreate: function() {
      // Is this Record Set Connected?
      var connection = this.getConnection();
      if (connection !== null) { // YES
        return connection.isReady() && connection.canCreate();
      }

      // ELSE: NO (Disconnected Record Set)      
      return false;
    },
    /**
     * Record Set/Connection Allows Record to be Loaded/Reloaded?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canRead: function() {
      // Is this Record Set Connected?
      var connection = this.getConnection();
      if (connection !== null) { // YES
        return connection.isReady() && connection.canRead();
      }

      // ELSE: NO (Disconnected Record Set)      
      return false;
    },
    /**
     * Record Set/Connection Allows Record to be Updated?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canUpdate: function() {
      // Is this Record Set Connected?
      var connection = this.getConnection();
      if (connection !== null) { // YES
        return connection.isReady() && connection.canUpdate();
      }

      // ELSE: NO (Disconnected Record Set)      
      return false;
    },
    /**
     * Record Set/Connection Allows Record Deletions?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canDelete: function() {
      // Is this Record Set Connected?
      var connection = this.getConnection();
      if (connection !== null) { // YES
        return connection.isReady() && connection.canDelete();
      }

      // ELSE: NO (Disconnected Record Set)      
      return false;
    },
    /**
     * Is the Row Marked for Deletion (at the Current Cursor Position, or the
     * specific index if provided)?
     *
     * @param index {Integer?null} Index Position (>= 0)
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throw {string} Exception if invalid index
     */
    isDeleted: function(index) {
      // Can we Delete Records in the Record Set?
      if (this.canDelete()) { // YES
        var record = this.getRecord(index);
        return record.isDeleted();
      }
      // ELSE: NO
      return false;
    },
    /**
     * Mark the Record for Deletion at Current Cursor Position, or at the position Specified
     *
     * @param record {meta.api.ui.datasource.IRecord|Integer?null} Record, Integer Index, or null for current cursor position
     * @param now {boolean?false} 'TRUE' delete record immediately, 'FALSE' schedule deletion on next flush
     * @throw {string} if Record Deletion not Allowed, Invalid Index or Record Doesn't Belong to this Record Set
     */
    deleteRecord: function(record, now) {
      // Validate Incoming Parameters
      var sourceIsRecord = false;
      var position = -1;

      // Is Incoming record Paramter an Object?
      if (qx.lang.Type.isObject(record)) { // YES
        // Is it a Record Object?
        if (!qx.Class.implementsInterface(record, meta.api.ui.datasource.IRecord)) { // NO
          this.__exception(true, "[record] Invalid Parameter Value.");
        }
        // ELSE: YES
        this.__exception(record.getRecordSet() !== this, "Record doesn't belong to this Record Set.");
        sourceIsRecord = true;
      } else { // NO: Assume it's an Integer
        position = this.__position(record);
        record = this.getRecord(position);
      }

      // Did the Record Initiate Deletion
      if (!(sourceIsRecord && record.isDeleted())) { // NO
        // Initiate the Process, by letting the record start
        record.deleteRecord(!!now);
        // Add Record to List of Dirty Records
        this.__pendingRecords.push(record.getRecordID(), record);
      }
    },
    /**
     * Is the Record New (at the Current Cursor Position, or the specific index 
     * if provided)?
     *
     * @param index {Integer?null} Index Position (>= 0)
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throw {string} Exception if invalid index
     */
    isNew: function(index) {
      index = this.__position(index);
      return this.getRecord(index).isNew();
    },
    /**
     * Get Special Insert Row (a special row that can be used to create Records)
     *
     * @return {meta.api.ui.datasource.IRecord} Record Object
     * @throw {string} Exception if New Record's Cannot be Created
     */
    getInsertRecord: function() {
      this.__exception(!this.canCreate(), "Record Set Cannot Create Records.");

      // Create a New Record
      var record = new meta.ui.datasource.Record(null, this);

      /* NOTE: A New Record is Only Added to the Record Set when it's changes
       * are committed, until then it's purely virtual and can be deleted
       * with no harm done.
       */

      // TODO: We need to initialize the record with a set of possible fields
      return record;
    },
    /**
     * Has the Data Source been Modified since it was loaded?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isDirty: function() {
      return this.__pendingRecords.count() > 0;
    },
    /**
     * Commit the Record (Create for New Records or Update Existing Records)
     *
     * @param record {meta.api.ui.datasource.IRecord|Integer?null} Record, Integer Index, or null for current cursor position
     * @param now {boolean?false} 'TRUE' delete record immediately, 'FALSE' schedule deletion on next flush
     * @throw {string} If action not allowed
     */
    commitRecord: function(record, now) {
      // Validate Incoming Parameters
      var sourceIsRecord = false;
      var position = -1;

      // Is Incoming record Paramter an Object?
      if (qx.lang.Type.isObject(record)) { // YES
        // Is it a Record Object?
        if (!qx.Class.implementsInterface(record, meta.api.ui.datasource.IRecord)) { // NO
          this.__exception(true, "[record] Invalid Parameter Value.");
        }
        // ELSE: YES
        this.__exception(record.getRecordSet() !== this, "Record doesn't belong to this Record Set.");
        sourceIsRecord = true;
      } else { // NO: Assume it's an Integer
        position = this.__position(record);
        record = this.getRecord(position);
      }

      // Did the Record Initiate Commit
      if (!sourceIsRecord) { // NO
        // Initiate the Process, by letting the record take-over
        record.commitRecord(!!now);
        // Add Record to List of Dirty Records
        this.__pendingRecords.push(record.getRecordID(), record);
      }
    },
    /**
     * Load the record, using the given Map as the starting point, or
     * if no Map provided, using the actual Record Values as the starting point.
     * This load will reload the record with new values.
     * 
     * @param record {meta.api.ui.datasource.IRecord|Integer?null} Index Position (>= 0) to position cursor at
     * @param map {Map?null} Field<-->Value Map 
     * @throw {string} Exception if Record is Disconnected or Connection doesn't allow loading
     */
    loadRecord: function(record, map) {
      // Validate Incoming Parameters
      var sourceIsRecord = false;
      var position = -1;

      // Is Incoming record Paramter an Object?
      if (qx.lang.Type.isObject(record)) { // YES
        // Is it a Record Object?
        if (!qx.Class.implementsInterface(record, meta.api.ui.datasource.IRecord)) { // NO
          this.__exception(true, "[record] Invalid Parameter Value.");
        }
        // ELSE: YES
        this.__exception(record.getRecordSet() !== this, "Record doesn't belong to this Record Set.");
        sourceIsRecord = true;
      } else { // NO: Assume it's an Integer
        position = this.__position(record);
        record = this.getRecord(position);
      }

      // Did the Record Initiate Load
      if (!sourceIsRecord) { // NO
        // Initiate the Process, by letting the record take-over
        record.loadRecord(map);
        // Add Record to List of Dirty Records
        this.__pendingRecords.push(record.getRecordID(), record);
      }
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    _deleteRecord: function(index, record) {
      // Is the Record Already Deleted?
      if (!(record.hasOwnProperty('$$DELETED') && record['$$DELETED'])) { // NO
        // Mark the Record as Deleted
        record['$$DELETED'] = true;
        // Register Modification
        this._registerModified(record);
      }
    },
    _undeleteRecord: function(index, record) {

    },
    _registerModified: function(index, record) {
      /* We haev to do somehting more complex like:
       * 1. Register the type of modification, so that if we decide to undo changes
       * we can figure out when the record is back to it's un-modified state.
       */
      if (!this.__pendingRecords.has(index)) {
        this.__pendingRecords.add(index, record);
      }
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaRecordCreated: function(record) {
      // Does the Record Belong to this Record Set?
      var recordset = record.getRecordSet();
      if (recordset === this) { // YES
        // Remove the Record from the Pending List
        this.__pendingRecords.remove(record.getRecordID());

        // Create an Entry to be Added to the end of the Current Record Set
        var entry = record.getValues();
        entry['$$__RECORD'] = record;
        this.__records.push(entry);
      }
    },
    _processMetaRecordRead: function(record) {
      this._processMetaRecordUpdated(record);
    },
    _processMetaRecordUpdated: function(record) {
      // Does the Record Belong to this Record Set?
      var recordset = record.getRecordSet();
      if (recordset === this) { // YES
        var entry;
        for (var i = 0; i < this.__records.length; ++i) {
          entry = this.__records[i];

          // Is this the record we are looking for?
          if (entry.hasOwnProperty('$$__RECORD') &&
            (entry['$$__RECORD'] === record)) { // YES
            // Remove the Record from the Record Set
            this.__pendingRecords.remove(record.getRecordID());
            // Cleanup Old Entry
            entry['$$__RECORD'] = null;
            // Create a Replacment Entry
            var new_entry = record.getValues();
            new_entry['$$__RECORD'] = record;
            this.__records.splice(i, 1, new_entry);
            break;
          }
        }
      }
    },
    _processMetaRecordDeleted: function(record) {
      // Does the Record Belong to this Record Set?
      var recordset = record.getRecordSet();
      if (recordset === this) { // YES
        var entry;
        for (var i = 0; i < this.__records.length; ++i) {
          entry = this.__records[i];

          // Is this the record we are looking for?
          if (entry.hasOwnProperty('$$__RECORD') &&
            (entry['$$__RECORD'] === record)) { // YES
            // Remove the Record from the Record Set
            this.__pendingRecords.remove(record.getRecordID());
            this.__records.splice(i, 1);

            // Cleanup Entry
            entry['$$__RECORD'] = null;
            break;
          }
        }
      }
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    /**
     * Calculates and Verifies Position Indices
     * 
     * @param index {Integer?null} Index of record
     * @return {Integer} Valid Index Position
     * @throw {string} Exception if invalid index
     */
    __position: function(index) {
      var position = this.__cursor;
      // Is the index a Number?
      if (qx.lang.Type.isNumber(index)) { // YES
        // Make sure we have an integer
        index = Math.floor(index);

        // Calculate the Position of the Record
        var count = this.getCount();
        position = index > 0 ? index : count - index;

        // Throw Exception if Outside Possible Range of Indexes
        this.__exception((position < 0) || (position >= count), "[index] is outside of the allowed range.");
      } else if (index != null) { // NO: Is it null or undefined?
        // NO: Unexpected Type
        this.__exception(true, "[index] Invalid Type.");
      }

      return position;
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
