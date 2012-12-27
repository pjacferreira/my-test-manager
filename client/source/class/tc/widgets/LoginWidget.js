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

 #require(qx.ui.layout.HBox)
 #require(qx.ui.basic.Atom)
 #require(qx.ui.form.Button)
 #require(qx.ui.tooltip.ToolTip)

 #asset(tc/user_gray.png)
 #asset(tc/user_green.png)
 #asset(tc/door_in.png)
 #asset(tc/door_out.png)

 ************************************************************************ */

qx.Class.define("tc.widgets.LoginWidget",
  {
    extend: qx.ui.core.Widget,

    events: {
      "no-user": "qx.event.type.Event",
      "user-change": "qx.event.type.Data"
    },

    construct: function () {
      this.base(arguments);

      // Set the Layout for the Widgets
      this._setLayout(new qx.ui.layout.HBox(5));

      // Create Child Widgets
      this._createChildControl('label-user');
      this._createChildControl('button-loginout');

      // Initialize Widget State
      this.__initializeState();
    },

    members: {
      __bUserLoggedIn: false,
      __lblUser: null,
      __btnLogInOut: null,

      isLoggedIn: function () {
        return this.__bUserLoggedIn;
      },

      _createChildControlImpl: function (id) {
        var control;

        switch (id) {
          case 'label-user':
            control = this.__lblUser = new qx.ui.basic.Atom('Initializing', 'tc/user_gray.png');
            this._add(this.__lblUser);
            break;
          case 'button-loginout':
            control = this.__btnLogInOut = new qx.ui.form.Button();
            this.__btnLogInOut.addListener('execute', function (e) {
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

      __initializeState: function () {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener('service-ok', function (e) {
          var user = e.getResult();
          if (tc.util.Entity.IsEntityOfType(user, 'user')) {
            this.__setState(true, user);
          } else {
            this.__setState(false);
          }
        }, this);

        req.addListener('service-error', function (e) {
          this.__setState(false);
        }, this);

        // Send request
        req.send('session','whoami');
      },

      doLogin: function () {
        // Create the Form
        var form = new tc.forms.Login();

        // Create Dialog
        var dialog = new tc.windows.FormDialog('Please Login', new qx.ui.form.renderer.Single(form));

        // Add Form Listener for Login Event
        form.addListener('logged-in', function (e) {
          var user = e.getData();
          if (tc.util.Entity.IsEntityOfType(user, 'user')) {
            this.__setState(true, user);
          }

          // Close the Dialog
          dialog.close();
        }, this);

        dialog.moveTo(50, 30);
        dialog.open();
      },

      doLogout: function () {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          this.__setState(false);
        }, this);

        // Send request
        req.send('session','logout');
      },

      __setState: function (loggingIn, user) {
        if (loggingIn && user) {
          // Change Login State
          this.__bUserLoggedIn = true;

          // Change Label State
          this.__lblUser.setLabel(user['user:name']);
          this.__lblUser.setIcon("tc/user_green.png");

          // Add Tooltip if Possible
          var tooltip;

          if (user.hasOwnProperty('user:description')) {
            tooltip = user['user:description'];
          } else if (user['user:first_name']) {
            if (user['user:last_name']) {
              tooltip = user['user:first_name'] + " " + user['user:last_name'];
            } else {
              tooltip = user['user:first_name'];
            }
          }

          if (tooltip) {
            this.__lblUser.setToolTip(new qx.ui.tooltip.ToolTip(tooltip));
          }

          // Change Button State
          this.__btnLogInOut.setLabel("Logout");
          this.__btnLogInOut.setIcon("tc/door_out.png");
          this.__btnLogInOut.setToolTip(new qx.ui.tooltip.ToolTip("Logout?"));

          // Warn of Change
          this.fireDataEvent("user-change", user);
        } else {
          // Change Login State
          this.__bUserLoggedIn = false;

          // Change Label State
          this.__lblUser.setLabel("Please Login!");
          this.__lblUser.setIcon("tc/user_gray.png");
          this.__lblUser.resetToolTip();

          // Change Button State
          this.__btnLogInOut.setLabel("Login");
          this.__btnLogInOut.setIcon("tc/door_in.png");
          this.__btnLogInOut.setToolTip(new qx.ui.tooltip.ToolTip("Login Required"));

          // Warn of Change
          this.fireEvent("no-user");
        }
      }
    }
  });
