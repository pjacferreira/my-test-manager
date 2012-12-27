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
qx.Class.define("tc.metaform.DefaultWidgetFactory", {
  extend: qx.core.Object,
  implement: tc.metaform.interfaces.IFormWidgetFactory,

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
    // interface implementation
    createSubmitButton: function (form) {
      return new qx.ui.form.Button("Submit");
    },

    // interface implementation
    createFieldWidget: function (field) {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertNotNull(field, "Invalid argument 'field'.");
      }

      if (!field.hasOwnProperty('type')) {
        field.type = "text";
      }

      var widget = null;
      switch (field.type) {
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
