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

qx.Interface.define("tc.metaform.interfaces.IFormDataStore", {

  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when data correctly loaded.
     */
    "dataLoaded": "qx.event.type.Event",

    /**
     * Fired when data correctly saved.
     */
    "dataSaved": "qx.event.type.Event",

    /**
     * Fired on a Load/Save Error
     */
    "error": "qx.event.type.Event"
  },

  /*
  *****************************************************************************
  MEMBERS
  *****************************************************************************
  */
  members: {

    /**
     * Load the state, from the data source
     *
     * @param keyValues {Object} field name, value tuplets, to initialize key fields, before a load is initiated.
     *   If no value set, either the data source does not have key fields, or, the current value of the key fields, will be used.
     * @return {Boolean} 'true' if save initiated correctly (transaction), 'false' if unable to initiate save.
     *   Notes:
     *   - events will be sent to notify of success/error, only if the 'true' is returned, otherwise no events will
     *   be fired.
     *   - the user should not expect the events to be fired, only after the functions has returned,
     */
    load: function (keyValues) {
    },

    /**
     * Set the data sources state, to the current values
     *
     * @return {Boolean} 'true' if save initiated correctly (transaction), 'false' if unable to initiate save.
     *   Notes:
     *   - events will be sent to notify of success/error, only if the 'true' is returned, otherwise no events will
     *   be fired.
     *   - the user should not expect the events to be fired, only after the functions has returned,
     */
    save: function() {
    },

    /**
     * Indicates if any of the fields in the data source, have been modified.
     *
     * @return {Boolean} 'true' if any of the field values have been modified, 'false' otherwise
     */
    isModified: function() {
    },

    /**
     * Mass data Retrieval
     *
     * @param last {Boolean} 'true' get original value loaded (or as of last save), 'false' (default) current value
     * @return {Object} field name, value tuplets, for the fields that exist in the data source. If a field has no value, than
     *  null will be returned instead.
     */
    getValues: function(last) {
    },

    /**
     * Mass data Save
     *
     * @param values {Object} field name, value tuplets, for the fields.
     */
    setValues: function(values) {
    },

    /**
     *
     * @param name {String} Field name for which we want to retrieve the value.
     * @param last {Boolean} 'true' get original value loaded (or as of last save), 'false' (default) current value
     * @return {?} field's current or last value. If a field does not exist or has not value, than
     *  null will be returned instead.
     */
    getValue: function(name, last) {
    },

    /**
     *
     * @param name {String} Field name for which we want to set the value. If the field does not exist in the data set,
     *   than a new field will be created
     * @param value {?} new value for the field.
     * @return {?} previous value for the field. If a field did not exist or had no value, than
     *  null will be returned instead.
     */
    setValue: function(name, value) {
    }
  }
});
