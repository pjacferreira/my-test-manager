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

qx.Interface.define("meta.api.ui.datasource.IRecordSet", {
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
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is this a Disconnected Record Set?
     *
     * For disconnected Record Sets, flush() does not "save" changes back to the
     * back-end Data Source...
     * 
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isDisconnected: function() {
    },
    /**
     * Get Connection Object Associated with this Record Set
     *
     * @abstract
     * @return {meta.api.ui.datasource.IConnection} Record Set's Connection
     */
    getConnection: function() {
    },
    /**
     * Is this a Paged Record Set (i.e. a partial load)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isPaged: function() {
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
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isEmpty: function() {
    },
    /**
     * Current Number of Records in the Record Set
     *
     * @abstract
     * @return {Integer} Current number of records in the set
     */
    getCount: function() {
    },
    /**
     * Is the Cursor at the 1st Row in the Record Set?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isFirst: function() {
    },
    /**
     * Is the Cursor at the Last Row in the Record Set?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isLast: function() {
    },
    /**
     * Position the Cursor at the 1st Record in the Record Set
     *
     * @abstract
     */
    first: function() {
    },
    /**
     * Position the Cursor at the Last Record in the Record Set
     *
     * @abstract
     * @throw {string} Exception if at the First Row in the Record Set
     */
    previous: function() {
    },
    /**
     * Position the Cursor at the Last Record in the Record Set
     *
     * @abstract
     * @throw {string} Exception if at the Last Record in the Record Set
     */
    next: function() {
    },
    /**
     * Position the Cursor at the Last Record in the Record Set
     *
     * @abstract
     */
    last: function() {
    },
    /**
     * Get Current Cursor Position (i.e. Current Record in the Record Set as
     * pointed to by the Cursor)
     *
     * @abstract
     */
    rowIndex: function() {
    },
    /**
     * Position the Cursor at the the Index Specified. If Positive Integer,
     * the cursor position is set relative to the Start of the Record Set.
     * If Negative Intger, the cursor position is set relative to the End of the
     * Record Set.
     *
     * @abstract
     * @param index {Integer} Index Position to position cursor at
     * @throw {string} Exception if invalid index
     */
    moveTo: function(index) {
    },
    /**
     * Get the Record at the Current Cursor Position, or at the position Specified
     *
     * @abstract
     * @param index {Integer?null} Index Position (>= 0) to position cursor at
     * @throw {string} Exception if invalid index
     */
    getRecord: function(index) {
    },
    /**
     * Record Set/Connection Allows New Records to be Created/Inserted?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canCreate: function() {
    },
    /**
     * Record Set/Connection Allows Record to be Loaded/Reloaded?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canRead: function() {
    },
    /**
     * Record Set/Connection Allows Record to be Updated?
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
     * Is the Row Marked for Deletion (at the Current Cursor Position, or the
     * specific index if provided)?
     *
     * @abstract
     * @param index {Integer?null} Index Position (>= 0)
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throw {string} Exception if invalid index
     */
    isDeleted: function(index) {
    },
    /**
     * Mark the Record for Deletion at Current Cursor Position, or at the position Specified
     *
     * @abstract
     * @param record {meta.api.ui.datasource.api.IRecord|Integer?null} Index Position (>= 0) to position cursor at
     * @param immediate {boolean?false} 'TRUE' deleter record immediately, 'FALSE' schedule deletion on next flush
     * @throw {string} if Record Deletion not Allowed or Invalid Index
     */
    deleteRecord: function(record, immediate) {
    },
    /**
     * Is the Row New (at the Current Cursor Position, or the specific index 
     * if provided)?
     *
     * @abstract
     * @param index {Integer?null} Index Position (>= 0)
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throw {string} Exception if invalid index
     */
    isNew: function(index) {
    },
    /**
     * Get Special Insert Row (a special row that can be used to create Records)
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecord} Record Object
     * @throw {string} Exception if New Record's Cannot be Created
     */
    getInsertRecord: function() {
    },    
    /**
     * Has the Data Source been Modified since it was loaded?
     *
     * @abstract
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    isDirty: function() {
    },
    /**
     * Commit the Record (Create for New Records or Update Existing Records)
     *
     * @abstract
     * @param record {meta.api.ui.datasource.api.IRecord|Integer?null} Record, Integer Index, or null for current cursor position
     * @param now {boolean?false} 'TRUE' delete record immediately, 'FALSE' schedule deletion on next flush
     * @throw {string} If action not allowed
     */
    commitRecord: function(record, now) {
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
