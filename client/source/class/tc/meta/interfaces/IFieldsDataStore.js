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

qx.Interface.define("tc.meta.interfaces.IFieldsDataStore", {
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
  MEMBERS
  *****************************************************************************
  */
  members: {
    /**
     * Verify is the Data Store has been Initialized with Values
     *  
     * @return {Boolean} 'true' if the data store has been initialized with a set of values, 'false' otherwise.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     */
    isInitialized: function() {
    },
    /**
     * Set the Field's Definition for the Data Store
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
    },
    /**
     * Test if a field is defined in the current data store.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is managed, 'false' otherwise.
     */
    hasField: function(name) {
    },
    /**
     * Test if a field can be modified in the current data store.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' if the field is modifiable, 'false' otherwise.
     * - throws an exception if the field is not part of the current active field
     *   list
     */
    isFieldMutable: function(name) {
    },
    /**
     * Test if a field has a value Set.
     *  
     * @param name {String} Field's name to test.
     * @return {Boolean} 'true' field has a value defined, 'false' otherwise.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     *  - throws exception if the field does not exist in the metadata
     */
    isFieldSet: function(name, original) {
    },
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
    },            
    /**
     * Retrieves a field's value if it exists in the data store.
     *  
     * @param name {String} Field name whose value is to be retrieved.
     * @param original {Boolean} 'true' get original value loaded (or as of last save), 'false' (default) current value
     * @return {Var} Field's value.
     *  Notes:
     *  - throws an execption if no metadata is loaded
     *  - throws exception if the field does not exist in the metadata
     */
    getFieldValue: function(name, original) {
    },
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
    },
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
     * - throws an exception if the field is defined immutable
     * - fires field-changed, on successful completion of value change
     */
    setFieldValue: function(name, value) {
    },
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
     *   corresponding fields in the metadata, is defined immutable
     * - fires field-changed, on successful completion for the values that
     *   have been modified
     */
    setFieldsValues: function(tuplets, original) {
    },
    /**
     * Indicates if any of the fields in the data source, have been modified.
     *
     * @return {Boolean} 'true' if any of the field values have been modified, 'false' otherwise
     */
    isModified: function() {
    }
  }
});
