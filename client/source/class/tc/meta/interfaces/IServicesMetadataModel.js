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

qx.Interface.define("tc.meta.interfaces.IServicesMetadataModel", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Retrieves All of the Services Definitions that may be used in the Form.
     *
     * @abstract
     * @return {Object} Services' Definition Object or NULL|UNDEFINED if the Model has not been initialized.
     */
    getServicesMeta: function () {
    },

    /**
     * Retrieves a Single Service's Definition.
     *
     * @abstract
     * @param name {String} Name of the required Service Definition.
     * @return {Object} Service Definition Object or NULL|UNDEFINED if no Service with the name exists,
     *   or the Model has not been initialized.
     */
    getServiceMeta: function (name) {
    }
  }
});
