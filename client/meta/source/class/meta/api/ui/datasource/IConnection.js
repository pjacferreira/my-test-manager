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

qx.Interface.define("meta.api.ui.datasource.IConnection", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /*
     * Meta Events - for Record Actions
     */
    "record-read": "meta.events.MetaEvent",
    "record-commit": "meta.events.MetaEvent",
    "record-delete": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is Connection Ready for Use?
     * 
     * @abstract
     * @return {Boolean} 'true' Connection is Ready, 'false' otherwise 
     */
    isReady: function() {
    },
    /**
     * Does the connection support the service function?
     *
     * @abstract
     * @param alias {String} One of the possible service functions (create,read,update,delete)
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
    },
    /**
     * Register a Service Function with the Connection Object
     *
     * Note: Read - Return's either an error / a value / a single record
     *       List - Return's either an error / a record set
     *
     * ** PROBLEM ** 
     * 
     * How to distinguish / implement services that are should be able to work on 
     * single records and record sets ex: create / update / delete.
     * 
     * Scenario, if we want to implement a system that allows for us to be able
     * create / update / delete both SINGLE RECORDS and a LIST OF RECORDS (BULK
     * changes), how should we do this? 
     * 
     * Possible Solutions:
     * 1. Implement multiple services ex: create (SINGLE RECORD) list-create (RECORD SET)
     * or
     * 2. Implement a single service THAT IS ABLE TO ACCEPT both SINGLE RECORDS
     * and RECORD SETS.
     * 
     * We also have to know, if the service supports SINGLE / BULK / BOTH types of
     * changes. Why? Because the way we implement the flush function depends on
     * knowing the kind of service (SINGLE / BULK / BOTH) the service provides.
     * 
     * NOTE: BULK Services provide a serious perform advantage i.e. we could
     * perform MANY change to the back-end datastore with a single call....
     * 
     * ** END PROBLEM **
     *
     * @abstract
     * @param alias {String} One of the possible service functions (create,read,update,delete,list,count)
     * @param service {meta.api.entity.IService|String} Service Definition or Service ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} if the Connection has already been initialized (is ready)
     */
    registerService: function(alias, service) {
    },
    /**
     * Can the connection produce paged Result Sets?
     * 
     * @abstract
     * @return {Boolean} 'true' YES, 'false' otherwise 
     * @throws {String} if the Connection is Not Ready
     */
    canPageResults: function() {
    },
    /**
     * Is Connection Ready for Use?
     * 
     * @abstract
     * @return {Integer|null} Last page size set, or null, if page size not set
     */
    getPageSize: function() {
    },
    /**
     * Set new page size for Result Sets.
     * 
     * @abstract
     * @param size {Integer?null} New page size, or 'null' to clear paging
     * @return {Integer|null} Previous page size set, or null, if page size was not set
     * @throws {String} if the Connection is Not Ready, cannot be paged, or invalid
     *   page size
     */
    setPageSize: function(size) {
    },
    /**
     * Initialize Connection.
     * Fires 'ready' event on success, 'not-ready' on failure to initialize.
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     */
    initialize: function(ok, nok, context) {
    },
    /**
     * Execute service function
     *
     * @abstract
     * @param alias {String} One of the possible service functions (create,read,update,delete)
     * @param parameters {Map?null} (Optional) Parameter map to pass to the service function
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throws {String} Is connection ready
     */
    execute: function(alias, parameters, ok, nok, context) {
    },
    /**
     * Was execute() called on the connection?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    haveResult: function() {
    },
    /**
     * Was the result of the Last Execution an Error?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultError: function() {
    },
    /**
     * Was the result of the Last Execution a Simple Value (i.e. an int, boolean
     * or even an array of something, but not a record)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultValue: function() {
    },
    /**
     * Was the result of the Last Execution a Record (i.e. a Map of 
     * field<-->value tuplets)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultRecord: function() {
    },
    /**
     * Was the result of the Last Execution a Record Set (i.e. an Array of 
     * Records)?
     * 
     * Note: A Single Record is Always Considered a Record Set (i.e. can be 
     * returned as a Record Set), but a Record Set can not be returned as a
     * Record
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultRecordSet: function() {
    },
    /**
     * Retrieve the Last Execution Error Code
     *
     * @abstract
     * @return {Integer} Last Execution's Error Code
     * @throws {String} If no previous execution
     */
    getErrorCode: function() {
    },
    /**
     * Retrieve the Last Execution Error Message
     *
     * @abstract
     * @return {String|null} Last Execution's Error Message, or 'null' if no 
     *   error message
     * @throws {String} If no previous execution
     */
    getErrorMessage: function() {
    },
    /**
     * Retrieve the un-encapsulated result of the execution (or in the case of
     * single value result, just simply return the result).
     *
     * @abstract
     * @return {Var} Last Execution's Result
     * @throws {String} If no results available (i.e. no previous execution, or
     *   the last execution returned an error)
     */
    getResult: function() {
    },
    /**
     * Retrieve last execution's Record Result
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecord} Last Execution's Record
     * @throws {String} If no results available (i.e. no previous execution, or
     *   the last execution returned an error), or result is not a Record
     */
    getResultRecord: function() {
    },
    /**
     * Retrieve last execution's Record Set Result
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecordSet} Last Execution's Record Set
     * @throws {String} If no results available (i.e. no previous execution, or
     *   the last execution returned an error), or result is not a Record Set
     */
    getRecordSet: function() {
    },
    /**
     * Connection Allows New Records to be Created/Inserted?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canCreate: function() {
    },
    /**
     * Connection Allows Record Lookup's?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canRead: function() {
    },
    /**
     * Connection Allows for Record Updates?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canUpdate: function() {
    },
    /**
     * Connection Allows for Record Deletion?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canDelete: function() {
    },
    /**
     * Get a Record that is associated with this Connection. This record is 
     * initially set as a New Record, but can then be loaded using the 
     * Connection Object.
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecord} Record Object
     * @throws {String} If connection is not ready or Can't Insert
     */
    getRecord: function() {
    },
    /**
     * Write record back to the Back-end Data Source.
     *
     * @abstract
     * @param record {meta.api.ui.datasource.api.IRecord} Record to Create or Update
     * @throws {String} if the Connection is Not Ready or Connection cannot Create/Update Records
     */
    commitRecord: function(record) {
    },
    /**
     * Delete the Record
     *
     * @abstract
     * @param record {meta.api.ui.datasource.api.IRecord} Record to delete
     * @throws {String} if the Connection is Not Ready or Connection cannot Delete Records
     */
    deleteRecord: function(record) {
    },
    /**
     * Load the record, using the given Map as the starting point, or
     * if no Map provided, using the actual Record Values as the starting point.
     * This load will reload the record with new values.
     * 
     * @abstract
     * @param record {meta.api.ui.datasource.api.IRecord|Integer?null} Index Position (>= 0) to position cursor at
     * @param map {Map?null} Field<-->Value Map 
     * @throw {string} Exception if Record is Disconnected or Connection doesn't allow loading
     */
    loadRecord: function(record, map) {
    }
  } // SECTION: MEMBERS
});
