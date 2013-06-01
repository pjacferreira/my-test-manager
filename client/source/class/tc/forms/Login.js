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
 
 #use(qx.ui.form.validation.Manager)
 
 ************************************************************************ */
qx.Class.define("tc.forms.Login", {
  extend: qx.ui.form.Form,
  events: {
    "logged-in": "qx.event.type.Data"
  },
  construct: function() {
    this.base(arguments);

    // BUTTON: Confirmation
    this.__btnOk = new tc.buttons.ButtonSend();
    this.__btnOk.setEnabled(false);
    this.__btnOk.addState("default");
    this.__btnOk.addListener("execute", function(e) {
      this.doLogin();
    }, this);

    // FIELD: User Name
    this.__tfUsername = new qx.ui.form.TextField();
    this.__tfUsername.setPlaceholder("Username here...");
    this.__tfUsername.setRequired(true);
    this.__tfUsername.setMaxLength(40);
    this.__tfUsername.addListener("changeValue", this.enableOk, this);

    /* BUG:
     * The fact that we use changeValue means that, the function enableOk is
     * called onnly we the field loses focus. 
     * This creates the following problem:
     * - In the initial state (when username field and password field are empty),
     * the button is disabled.
     * - If the user fills in the username and then hits TAB, the focus will pass
     *   to the password field and then the changeValue event is called.
     * - If the user then fills in the password and then hits TAB, the focus will
     *   pass BACK TO THE ADMIN FIELD (not the Button, because it is still active)
     *   and then changeValue wvent is called (which activates the Button).
     * 
     * Therefore, on the 1st go through the form, the user will have to click the
     * button, instead of HAVING THE FOCUS MOVE TO THE SEND BUTTON as expected.
     */

    // FIELD: Password
    this.__pfPassword = new qx.ui.form.PasswordField();
    this.__pfPassword.setPlaceholder("Password here...");
    this.__pfPassword.setRequired(true);
    this.__pfPassword.addListener("changeValue", this.enableOk, this);

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
    __validator: null,
    doLogin: function() {
      tc.services.Session.login(this.__tfUsername.getValue(), this.__pfPassword.getValue(),
              function(user) {
                this.fireDataEvent("logged-in", user);
              },
              null, this);
    },
    enableOk: function(e) {
      var username = this.__tfUsername.getValue();
      var password = this.__pfPassword.getValue();
      this.__btnOk.setEnabled(
              ((username != null) && (username.length > 0)) &&
              ((password != null) && (password.length > 0))
              );
    }
  }
});


