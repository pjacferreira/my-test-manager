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

qx.Interface.define("tc.metaform.interfaces.IFormModel", {

  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a new Meta Model has been initialized (this allows model load to be
     * asynchronous)
     */
    "modelReady": "qx.event.type.Event",

    /**
     * Fired when Field Values have been loaded from the Data Store
     */
    "dataLoaded": "qx.event.type.Event",

    /**
     * Fired when Field Values have been saved back to the Data Store
     */
    "dataSaved": "qx.event.type.Event",

    /**
     * Fired on any error
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
     * Initialize the model.
     *
     * @abstract
     * @param data {Object|NULL} field name, value tuplets, used to initialize the field values, after the model has been
     *  successfully initialized.
     */
    init: function (fieldValues) {
    },

    /*
     *****************************************************************************
     METADATA RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Retrieves a Single Field's Definition.
     *
     * @abstract
     * @param name {String} Name of the required field Definition.
     * @return {Object} Field Definition Object or NULL|UNDEFINED if no field with the name exists,
     *   or the Model has not been initialized.
     */
    getFieldMeta: function (name) {
    },

    /**
     * Get the Form's Title.
     *
     * @abstract
     * @return {String|NULL} Form title or NULL|UNDEFINED if the Model has not been initialized.
     */
    getFormTitle: function() {
    },


    /**
     * Get the complete list of fields used in the form.
     *
     * @abstract
     * @return {Array|NULL} Field list array or NULL|UNDEFINED if the Model has not been initialized.
     */
    getFormFields: function () {
    },

    /**
     * Retrieves the count of the number of Groups in the form.
     *
     * @abstract
     * @return {Integer} Count of number of Groups in the Form or 0 the Model has not been initialized.
     */
    getGroupCount: function () {
    },

    /**
     * Retrieves a Single Group's Label.
     * Note: Label can be NULL (in which case, no Group Label is required)
     *
     * @abstract
     * @param index {Integer} Index of Field that we want the label for ( 0 .. getGRoupCount()-1).
     * @return {String|NULL} Group labelor NULL|UNDEFINED if invalid index or the Model has not been initialized.
     */
    getGroupLabel: function (index) {
    },

    /**
     * Retrieves a Single Group's Field List.
     *
     * @abstract
     * @param index {Integer} Index of Field that we want the list for ( 0 .. getGRoupCount()-1).
     * @return {Array|NULL} Field list array or NULL|UNDEFINED if invalid index, or the Model has not been initialized.
     */
    getGroupFields: function (index) {
    },

    /*
     *****************************************************************************
     DATASOURCE RELATED MEMBERS
     *****************************************************************************
     */

    /**
     * Perform a Load of the field values (if required), from a data source.
     *
     * @abstract
     * @return {Boolean} TRUE if the save request was registered, FALSE otherwise.
     */
    load: function () {
    },

    /**
     * Perform a SAVE of the current field values (if required), back to the data source.
     *
     * @abstract
     * @return {Boolean} TRUE if the save request was registered, FALSE otherwise.
     */
    save: function () {
    },

    /**
     * Indicates if any of the fields in the data source, have been modified.
     *
     * @return {Boolean} 'true' if any of the field values have been modified, 'false' otherwise
     */
    isModified: function() {
    },

    /**
     * Mass value retrieval for the fields existing in the model.
     *
     * @abstract
     * @return {Object} field name, value tuplets, for the fields that exist in the model. If a field has no value, than
     *  null will be returned instead.
     *  If the model has not been initialized than a NULL|UNDEFINED will be returned.
     */
    getData: function () {
    },

    /**
     * Mass value set for the fields existing in the model.
     *
     * @abstract
     * @param data {Object} field name, value tuplets, for the fields we want to modify in the current model.
     *  If the model contains a field, for which no tuplet is passed in, than the field is not modified.
     *  If a tuplet is passed, for which the value is null, than the model's field value is set to null.
     * @return {Object} field name, value tuplets, for the fields whose incoming value was modified, as part of some internal
     *   transformation or NULL|UNDEFINED if no field values we changed.
     *
     * Special Note:
     * It is possible for the model to modify field values, fro which no incoming set was requested (i.e. it is possible
     * for the returned object to contain field, value tuplets that had no corresponding tuplet in the incoming data).
     */
    setData: function (data) {
    },

    /**
     *
     * @abstract
     * @param name {String} Field Name to retrieve Value for
     * @return {?} Current Field's Value or NULL if not set
     */
    getFieldValue: function (name) {
    },

    /**
     *
     * @abstract
     * @param name {String} Field Name to set the value for
     * @param value [?} New Field Value
     * @return {?} UNDEFINED if the incoming Field Value did not suffer any tranformation, otherwise, the result of
     * the field transformation will be returned (including NULL, if the result of the transformation was null)
     */
    setFieldValue: function (name, value) {
    },

    /**
     *
     * @abstract
     * @param name {String} Field Name to test
     * @return {Boolean} TRUE if field is Required (Must have a valid value set), FALSE Otherwise
     */
    isFieldRequired: function(name) {
    },

    /**
     *
     * @abstract
     * @param name {String} Field Name to test
     * @return {Boolean} TRUE if field contains a Valid Value, FALSE Otherwise
     */
    isFieldDataValid: function(name) {
    }
  }
});
