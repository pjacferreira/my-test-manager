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

qx.Interface.define("tc.meta.interfaces.IFieldsMetadataModel", {
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
    }
  }
});
