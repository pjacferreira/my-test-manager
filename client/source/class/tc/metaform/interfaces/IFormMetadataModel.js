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

qx.Interface.define("tc.metaform.interfaces.IFormMetadataModel", {

  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when the model is initialized and ready to be used (this allows model load to be
     * asynchronous):
     */
    "modelReady": "qx.event.type.Event",

    /**
     * Fired if the model failed to initialize correctly.
     */
    "modelInvalid": "qx.event.type.Event"
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
     * @return {Boolean} 'true' on request launched, 'false' otherwise.
     *  Note in the case the value is 'true', then "modelReady" and "modelInvalid" events will signal if the module was
     *    successfully loaded or not. If 'false' then no "modelReady" or "modelInvalid" will be sent (i.e. this is a
     *    general failure).
     *  Note the events may be received, before the init functions returns (ex: in the case that the model
     *    is static (or already loaded), then events will be launched, before the init function returns.
     */
    init: function () {
    },

    /**
     * Retrieves Metadata Definition for the Form.
     *
     * @abstract
     * @return {Object} Form Definition Object or NULL|UNDEFINED if the Model has not been initialized.
     */
    getFormMeta: function () {
    },

    /**
     * Retrieves All of the Field Definitions that may be used in the Form.
     *
     * @abstract
     * @return {Object} Field Definition Object or NULL|UNDEFINED if no field with the name exists,
     *   or the Model has not been initialized.
     */
    getFieldsMeta: function () {
    },

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
    getFormFields: function() {
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
    }
  }
});
