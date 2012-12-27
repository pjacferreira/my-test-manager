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

qx.Interface.define("tc.metaform.interfaces.IFormWidgetFactory", {

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {

    /**
     * Creates the Form's Submit Button
     *
     * @param form {Object} form metadata definition.
     * @return {qx.ui.form.Button} Form button (or derived class)
     */
    createSubmitButton: function (form) {
    },

    /**
     * Creates A Fields Widget
     *
     * @param field {Object} field metadata definition.
     * @return {?} Widget for Field
     */
    createFieldWidget: function (field) {
    }
  }
});
