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

/**
 * Generic Form Model
 */
qx.Class.define("tc.meta.forms.DefaultWidgetFactory", {
  extend: qx.core.Object,
  implement: tc.meta.forms.IFormWidgetFactory,

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Creates the Form's Submit Button
     *
     * @return {qx.ui.form.Button} Form button (or derived class)
     */
    createSubmitButton: function (form) {
      return new qx.ui.form.Button("Submit");
    },
    /**
     * Creates A Fields Widget
     *
     * @param type {String} field metadata definition.
     * @return {var} Widget for Field
     */
    createFieldWidget: function (type) {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertString(type, "Invalid argument [type].");
      }

      var widget = null;
      switch (type) {
        case 'password' :
          widget = new qx.ui.form.PasswordField();
          break;
        case 'html' :
          widget = new qx.ui.form.TextArea();
          break;
        default :
          widget = new qx.ui.form.TextField();
      }

      return widget;
    }
  }
});
