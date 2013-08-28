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

qx.Interface.define("tc.meta.forms.IFormWidgetFactory", {

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {

    /**
     * Creates the Form's Submit Button
     *
     * @return {qx.ui.form.Button} Form button (or derived class)
     */
    createSubmitButton: function () {
    },

    /**
     * Creates A Fields Widget
     *
     * @param type {String} field metadata definition.
     * @return {var} Widget for Field
     */
    createFieldWidget: function (type) {
    }
  }
});
