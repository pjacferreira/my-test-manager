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

 #require(qx.ui.form.TextField)
 #require(qx.ui.form.Button)

 ************************************************************************ */
qx.Class.define("tc.forms.Login",
  {
    extend: qx.ui.form.Form,

    events: {
      "logged-in": "qx.event.type.Data"
    },

    construct: function () {
      this.base(arguments);

      // BUTTON: Confirmation
      this.__btnOk = new tc.buttons.ButtonSend();
      this.__btnOk.setEnabled(false);
      this.__btnOk.addState("default");
      this.__btnOk.addListener("execute", function (e) {
        this.doLogin();
      }, this);

      // FIELD: User Name
      this.__tfUsername = new qx.ui.form.TextField();
      this.__tfUsername.setPlaceholder("Username here...");
      this.__tfUsername.setRequired(true);
      this.__tfUsername.addListener("input", function (e) {
        var value = e.getData();
        this.__btnOk.setEnabled(value.length > 0 && value.length <= 40);
      }, this)

      // FIELD: Password
      this.__pfPassword = new qx.ui.form.PasswordField();
      this.__pfPassword.setPlaceholder("Password here...");


      // Form Fields
      this.add(this.__tfUsername, 'Username');
      this.add(this.__pfPassword, 'Password');

      // Form Buttons
      this.addButton(this.__btnOk);
    },

    members: {
      __tfUsername: null,
      __pfPassword: null,
      __btnOk: null,

      doLogin: function () {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          // Get Results (Should be a User Entity)
          var results = e.getResult();
          if (tc.util.Entity.IsEntityOfType(results, 'user')) {
            this.fireDataEvent("logged-in", results);
          }
        }, this);

        // Send request
        req.send('session','login', [this.__tfUsername.getValue(), this.__pfPassword.getValue()]);
      }
    }
  });


