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

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("meta.api.ui.datasource.IRecord", {
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
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Register List of Recognized Record Fields
     *
     * @abstract
     * @param fields {String[]|String} Field or List of Fields
     */
    registerFields: function(fields) {
    },
    /**
     * Retrieve all the List of Field IDs in the Record.
     *
     * @abstract
     * @return {String[]} List of Field ID's
     */
    listFields: function() {
    },
    /**
     * Test if the Field ID exists in the Record
     *
     * @abstract
     * @param id {String} Field ID
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    hasField: function(id) {
    },
    /**
     * Set the Record's ID Field
     *
     * @abstract
     * @param field {String} Field that represents that defines the Record's Unique Identifier
     * @return {String|null} Previous ID Field
     * @throw {string} Exception if field is not part of the record.
     */
    setIDField: function(field) {
    },
    /**
     * Is this a Disconnected Record?
     * 
     * For disconnected Records, flush() does not "save" changes back to the
     * back-end Data Source...
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isDisconnected: function() {
    },
    /**
     * Get Connection Object Associated with this Record.
     *
     * @abstract
     * @return {meta.api.ui.datasource.IConnection|null} Record's Connection
     */
    getConnection: function() {
    },
    /**
     * Get Connection Object Associated with this Record.
     * 
     * For Direct Records (those retrieved directly from the connection), this
     * returns "null" as the Record Set.
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecordSet|null} Record's Record Set
     */
    getRecordSet: function() {
    },
    /**
     * Record Set/Connection Allows Record Creation?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canCreate: function() {
    },
    /**
     * Record Set/Connection Allows Load/Reload of Records?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canRead: function() {
    },
    /**
     * Record Set/Connection Allows Record Update?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canUpdate: function() {
    },
    /**
     * Record Set/Connection Allows Record Deletions?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canDelete: function() {
    },
    /**
     * Is the Record Marked for Deletion ?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isDeleted: function() {
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
    },
    /**
     * Is this a New Record?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isNew: function() {
    },
    /**
     * Test if the Field has a value defined (even if it is only null)
     *
     * @abstract
     * @param id {String} Field ID
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isFieldSet: function(id) {
    },
    /**
     * Get the Field's value
     *
     * @abstract
     * @param id {String} Field ID
     * @return {Var} Field Value
     * @throw {string} Exception if Invalid Field ID or Field has no value set
     */
    getValue: function(id) {
    },
    /**
     * Get a Field<-->Value Map for all the existing fields
     *
     * @abstract
     * @return {Map} Field<-->Value Map
     */
    getValues: function() {
    },
    /**
     * Get the value of the Record's ID Field (which should uniquely identify
     * this record)
     *
     * @abstract
     * @return {Var} Record ID
     * @throw {string} Exception if no Field defined as the Record ID or The
     *   field has no value.
     */
    getRecordID: function() {      
    },
    /**
     * Set the Field's Value
     *
     * @abstract
     * @param id {String} Field ID
     * @param value {Var} Field Value
     * @return {Var} Old Field Value
     * @throw {string} Exception if Invalid Field ID
     */
    setField: function(id, value) {
    },
    /**
     * Set the values for Multiple Fields.
     * In the case of that we are initializing the fields, all previous field
     * modifications are simply dropped.
     *
     * @abstract
     * @param map {Map} Field<-->Value Map
     * @param nofire {Boolean?false} Don't Fire Event Notifying of Changes?
     * @param initialize {Boolean?false} Are we initializing the fields?
     * @return {Map|null} Map of previous values or 'null'
     */
    setFields: function(map, nofire, initialize) {
    },
    /**
     * Reset the Field's Value to undefined state (Undefined Field's will not
     * have their value's flushed back to the back end Data Source)
     *
     * @abstract
     * @param id {String} Field ID
     * @return {Var} Old Field Value
     * @throw {string} Exception if Invalid Field ID
     */
    clearField: function(id) {
    },
    /**
     * Has the Data Source been Modified since it was loaded?
     *
     * @abstract
     * @param field {String?null} Field ID to test if dirty, or 'null' to test for all
     *   field changes
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isDirty: function(field) {
    },
    /**
     * Reset the Fields Value's Back to the Previous State.
     * 
     * @abstract
     * @return {Boolean} 'true' if any modifications performed, 'false' Otherwise
     */
    reset: function() {
    },
    /**
     * Commit Changes to the Record. If the record is part of a record set,
     * this can be done immediately (now === true) or later (now === false).
     * If not part of a record set, then commit forces changes (immediately).
     *
     * @abstract
     * @param now {boolean?false} 'TRUE' commit the record immediately, 'FALSE' commit later (if part of record set)
     * @return {Boolean} 'true' changes committed or in the process of, 'false' did nothing
     * @throw {string} Exception if Record is Disconnected or Read-Only
     */
    commitRecord: function(now) {
    },
    /**
     * Load the record, using the current field values or provided values
     * 
     * @abstract
     * @param map {Map} Field<-->Value Map
     * @throw {string} Exception if Record is Disconnected or Connection doesn't allow loading
     */
    loadRecord: function(map) {
    }
  } // SECTION: MEMBERS
});

/* Problem:
 * We can retrieve a Record Object from 2 Places:
 * 1. Directly from the Connection Object (by way of the getResultRecord), or
 * 2. From a RecordSet Object (by way of getRecord)
 * 
 * The problem is that, if we retrieve the Record directly from the connection
 * (i.e. the result of executing a service is a single record) there is no 
 * Record Set to associate with.
 * 
 * This implies that, all the the thing that we would normally do through a
 * Record Set (Set as insertion, update, deletion, etc) will also need to be
 * doable, through the Connection Object.
 * 
 * Example (how to):
 * 1. Read a record is done by:
 * a) Calling the execution() on the "read" service
 * b) using getResultRecord() to retrieve the resultant record
 * 2. Update Record, can be done in 2 possible ways:
 * i) Directly
 * a) calling the execution() on the connection with "update" service and
 * the record parameters
 * ii) In-directly
 * a) Retrieving the record as in example (1)
 * b) Modify the Record (using setFieldValue/clearFieldValue/etc.)
 * c) Flush the changes to the Record (using flush())
 * 3. Delete Record, can be done in 2 possible ways:
 * i) Directly
 * a) calling the execution() on the connection with "delete" service and
 * the record parameters
 * ii) In-directly
 * a) Retrieving the record as in example (1)
 * b) Calling deleteRecord() - to mark the record as deleted,
 * c) Flush the changes to the Record (using flush())
 * 3. Create Record, can be done in 2 possible ways:
 * i) Directly
 * a) calling the execution() onm the connection with "create" service and
 * the record parameters
 * ii) In-directly
 * a) Retrieving a Special Insert Record from the Connection
 * b) Modify the Record (using setFieldValue/clearFieldValue/etc.)
 * c) Flush the changes to the Record (using flush())
 */