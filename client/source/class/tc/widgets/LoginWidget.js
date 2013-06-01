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
 
 #asset(tc/door_in.png)
 #asset(tc/door_out.png)
 
 ************************************************************************ */

qx.Class.define("tc.widgets.LoginWidget", {
  extend: qx.ui.core.Widget,
  events: {
    "no-user": "qx.event.type.Event",
    "user-change": "qx.event.type.Data"
  },
  construct: function() {
    this.base(arguments);

    // Set the Layout for the Widgets
    this._setLayout(new qx.ui.layout.HBox(5));

    // Create Child Widgets
    this._createChildControl('button-loginout');

    // Initialize Widget State
    this.__initializeState();
  },
  members: {
    __bUserLoggedIn: false,
    __lblUser: null,
    __btnLogInOut: null,
    isLoggedIn: function() {
      return this.__bUserLoggedIn;
    },
    _createChildControlImpl: function(id) {
      var control;

      switch (id) {
        case 'label-user':
          control = this.__lblUser = new qx.ui.basic.Atom('Initializing', 'tc/door_in.png');
          this._add(this.__lblUser);
          break;
        case 'button-loginout':
          control = this.__btnLogInOut = new qx.ui.form.Button();
          this.__btnLogInOut.addListener('execute', function(e) {
            if (this.__bUserLoggedIn) {
              this.doLogout();
            } else {
              this.doLogin();
            }
          }, this);
          this._add(this.__btnLogInOut);
          break;
      }

      return control;
    },
    __initializeState: function() {
      tc.services.Session.whoami(
              function(user) {
                this.__setState(user != null, user);
              },
              function(error) {
                this.__setState(false);
              },
              this);
    },
    doLogin: function() {
      // Create the Form
      var form = new tc.forms.Login();

      // Create Dialog
      var dialog = new tc.windows.FormDialog('Please Login', new qx.ui.form.renderer.Single(form));

      // Add Form Listener for Login Event
      form.addListener('logged-in', function(e) {
        // Close the Dialog
        dialog.close();

        // Update User State
        var user = e.getData();
        this.__setState(true, user);
      }, this);

      dialog.moveTo(50, 30);
      dialog.open();
    },
    doLogout: function() {
      tc.services.Session.logout(
              function(result) {
                this.__setState(false);
              },
              null, this);
    },
    __setState: function(loggingIn, user) {
      if (loggingIn && user) {
        // Change Login State
        this.__bUserLoggedIn = true;

        // Change Button State
        this.__btnLogInOut.setLabel(user['user:name']);
        this.__btnLogInOut.setIcon("tc/door_out.png");
        this.__btnLogInOut.setToolTip(new qx.ui.tooltip.ToolTip("Logout?"));

        // Warn of Change
        this.fireDataEvent("user-change", user);
      } else {
        // Change Login State
        this.__bUserLoggedIn = false;

        // Change Button State
        this.__btnLogInOut.setLabel("Login!");
        this.__btnLogInOut.setIcon("tc/door_in.png");
        this.__btnLogInOut.setToolTip(new qx.ui.tooltip.ToolTip("Login Required"));

        // Warn of Change
        this.fireEvent("no-user");
      }
    }
  }
});
