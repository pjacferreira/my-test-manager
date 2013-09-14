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

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("tc.meta.datastores.IFieldStore", {
  extend: [tc.meta.datastores.IDataStore],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a new Meta Model has been initialized.
     */
    "ok": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "nok": "qx.event.type.Data",
    /**
     * Fired when a single field's value is modified.
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
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Initialize the model.
     *
     * @abstract
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    initialize: function(iv, callback) {
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
    },
    /**
     * Test if a field can be modified in the current data store.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is modifiable, 'false' otherwise.
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    isFieldMutable: function(name) {
    },
    /**
     * Test if a field has a value Set.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' field has a value defined, 'false' otherwise.
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    isFieldSet: function(name) {
    },
    /**
     * Was the field value modified (i.e. Dirty, pending changes)?
     *
     * @abstract
     * @param name {String} Field Name
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    isFieldDirty: function(name) {
    },
    /**
     * Retrieve Field Value
     *
     * @abstract
     * @param name {String} Field Name
     * @return {var} Field Value
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    getField: function(name) {
    },
    /**
     * Return a Field Value Map, containing the current Field Values
     *
     * @abstract
     * @return {Object} Field, Value Tuplets
     * @throws if the Data Store is Not Ready
     */
    getFields: function() {
    },
    /**
     * Modify the Field's Value
     *
     * @abstract
     * @param name {String} Field Name
     * @param value {var} Field Value
     * @return {var} The Incoming Field Value or The Actual Value Set (Note: the Value may be modified if Trim and Empty-as-Null are Set)
     * @throws if the Data Store is Not Ready or Field Does not exist in the Data Store
     */
    setField: function(name, value) {
    },
    /**
     * Bulk Modifies the Data Store
     *
     * @abstract
     * @param map {Object} Field Value Tuplets
     * @return {Object} Field Value Tuplets of All Modified Fields
     * @throws if the Data Store is Not Ready
     */
    setFields: function(map) {
    },
    /**
     * Reset's All Modified Values Back to the Last Saved State
     *
     * @abstract
     * @param name {String ? null} Field Name or NULL if we would like to reset all fields rather than just a single field.
     * @return {var} if Single Field is being Reset then New Original Field Value is Returned
     *                if All or Fields are being Reset a Field, Value Tuplets of All Modified Fields (with new, original value) or 
     *                NULL if No Changes
     * @throws if the Data Store is Not Ready
     */
    reset: function(name) {
    }
  } // SECTION: MEMBERS
});
