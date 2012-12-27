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

 #require(qx.lang.String)
 #require(qx.ui.form.TextField)
 #require(qx.ui.form.TextArea)
 #require(qx.ui.form.PasswordField)
 #require(qx.ui.form.Button)
 #require(qx.ui.form.validation.Manager)

 ************************************************************************ */
qx.Class.define("tc.forms.UserForm",
  {
    extend: qx.ui.form.Form,

    events: {
      "user-saved": "qx.event.type.Data"
    },

    construct: function (userData) {
      this.base(arguments);

      // Set Mode for Form
      this.__modeNew = !tc.util.Entity.IsEntityOfType(userData, 'user');
      this.__user = this.__modeNew ? null : userData;

      // Initialize Form
      this.__initForm();
    },

    members: {
      // Flags
      __modeNew: false, // Form Mode
      __passwordChange: false, // Password Needs Changing
      __requireUpdate: false, // Update Required

      __user: null, // User Object

      // Form Widgets
      __btnOk: null,
      __tfUserId: null,
      __tfUserName: null,
      __tfFirstName: null,
      __tfLastName: null,
      __tfShort: null,
      __taLong: null,
      __pfPassword: null,
      __pfConfirmation: null,


      __initForm: function () {
        this.__initWidgets();
        this.__initValidator();
      },

      __initWidgets: function () {

        // Capture Changes to See if we Need to Perform an Update
        var save_this = this;
        var functionUpdate = function (e) {
          save_this.__requireUpdate = true;
        };

        // FIELD: User ID
        this.__tfUserId = new qx.ui.form.TextField();
        this.__tfUserId.setPlaceholder('New User...');
        this.__tfUserId.setEnabled(false);
        // FIELD: User Name
        this.__tfUserName = new qx.ui.form.TextField();
        this.__tfUserName.setPlaceholder('Choose a User Name...');
        this.__tfUserName.setMaxLength(40);
        // FIELD: User's First Name
        this.__tfFirstName = new qx.ui.form.TextField();
        this.__tfFirstName.setPlaceholder('User\'s First Name...');
        this.__tfFirstName.setMaxLength(40);
        this.__tfFirstName.addListener("changeValue", functionUpdate);
        // FIELD: User's Last Name
        this.__tfLastName = new qx.ui.form.TextField();
        this.__tfLastName.setPlaceholder('User\'s Last Name...');
        this.__tfLastName.setMaxLength(80);
        this.__tfLastName.addListener("changeValue", functionUpdate);
        // FIELD: Short Description
        this.__tfShort = new qx.ui.form.TextField();
        this.__tfShort.setPlaceholder('Short Description...');
        this.__tfShort.setMaxLength(80);
        this.__tfShort.addListener("changeValue", functionUpdate);
        // FIELD: Long Description
        this.__taLong = new qx.ui.form.TextArea();
        this.__taLong.setPlaceholder('Long Description...');
        this.__taLong.addListener("changeValue", functionUpdate);
        // FIELD: New Password
        this.__pfPassword = new qx.ui.form.PasswordField();
        this.__pfPassword.setPlaceholder('New Password...');
        this.__pfPassword.setMaxLength(64);
        this.__pfPassword.addListener("input", function (e) {
          this.__passwordChange = e.getData().length > 0;
          this.__pfConfirmation.setRequired(this.__modeNew || this.__passwordChange);
          if (!this.__modeNew) {
            this.__requireUpdate = true;
          }
        }, this)
        // FIELD: Confirm Password
        this.__pfConfirmation = new qx.ui.form.PasswordField();
        this.__pfConfirmation.setPlaceholder('Repeat Password...');
        this.__pfConfirmation.setMaxLength(64);

        // Create Fields Based on the Form Type
        if (this.__modeNew) { // New User Form
          // FIELD: User Name
          this.__tfUserName.setRequired(true);
          // FIELD: New Password
          this.__pfPassword.setRequired(true);
          // FIELD: Confirm Password
          this.__pfConfirmation.setRequired(true);
        } else { // Edit User Form
          // FIELD: User ID
          this.__tfUserId.setValue(this.__user.id);
          // FIELD: User Name
          this.__tfUserName.setValue(this.__user.name);
          this.__tfUserName.setEnabled(false);
          // FIELD: Password Modification
          this.__pfPassword.addListener("input", function (e) {
            // IF Password Set, Confirmation is Required
            this.__pfConfirmation.setRequired(e.getData().length > 0);
          }, this)
          // FIELD: User's First Name
          if (this.__user.hasOwnProperty('first_name')) {
            this.__tfFirstName.setValue(this.__user.first_name);
          }
          // FIELD: User's Last Name
          if (this.__user.hasOwnProperty('last_name')) {
            this.__tfLastName.setValue(this.__user.last_name);
          }
          // FIELD: Short Description
          if (this.__user.hasOwnProperty('s_description')) {
            this.__tfShort.setValue(this.__user.s_description);
          }
          // FIELD: Long Description
          if (this.__user.hasOwnProperty('l_description')) {
            this.__taLong.setValue(this.__user.l_description);
          }
        }

        // BUTTON: Confirmation
        this.__btnOk = new tc.buttons.ButtonSend();
        this.__btnOk.addListener("execute", function (e) { // Trigger Form Validation
          this.validate();
        }, this);

        // Group User Information
        this.addGroupHeader("User Information");
        this.add(this.__tfUserId, 'Id');
        this.add(this.__tfUserName, 'Name');

        // Optional Name Information
        this.addGroupHeader("Name");
        this.add(this.__tfFirstName, 'First Name');
        this.add(this.__tfLastName, 'Last Name');

        // Optional Description
        this.addGroupHeader("Description");
        this.add(this.__tfShort, 'Short');
        this.add(this.__taLong, 'Long');

        // Passwords
        this.addGroupHeader("Password");
        this.add(this.__pfPassword, 'Password');
        this.add(this.__pfConfirmation, 'Confirmation');

        // Buttons
        this.addButton(this.__btnOk);
      },

      __initValidator: function () {
        // create the form manager
        var manager = this.getValidationManager();

        // VALIDATOR : User Name
        var tfUserName = this.__tfUserName;
        var userNameValidator = new qx.ui.form.validation.AsyncValidator(
          function (validator, value) {
            if (tfUserName.getRequired()) {
              value = tc.util.String.nullOnEmpty(value);
              if ((value == null) || (value.length == 0)) {
                validator.setValid(false, 'Username can not be empty.');
              } else { // Call Server to See if User Name Already Taken
                var req = new tc.services.json.TCServiceRequest();

                req.addListener("service-ok", function (e) { // User Already Exists
                  validator.setValid(false, 'Username already in use.');
                }, this);

                req.addListener("service-error", function (e) { // User does not exist
                  validator.setValid(true);
                }, this);

                // Send request
                req.send('user', 'read', value);
              }
            }
          }
        );

        // VALIDATOR : Password
        var passwordValidator = function (value, item) {
          if (item.getRequired() && ((value == null) || (value.length == 0))) {
            // TODO Verify that Password satisfies complexity requirements
            item.setInvalidMessage('Password is required and can not be empty.');
            return false;
          }

          // Not Required or Valid
          return true;
        };

        // VALIDATOR : Confirmation
        var pfPassword = this.__pfPassword;
        var confirmationValidator = function (value, item) {
          if (item.getRequired()) {
            if ((value == null) || (value.length == 0)) {
              item.setInvalidMessage('Confirmation is required and must match password');
              return false;
            } else if (value != pfPassword.getValue()) {
              item.setInvalidMessage('Confirmation does not match Password Field');
              return false;
            }
          }

          // Not Required or Valid
          return true;
        };

        // Validate the Fields
        manager.add(this.__tfUserName, userNameValidator);
        manager.add(this.__pfPassword, passwordValidator);
        manager.add(this.__pfConfirmation, confirmationValidator);

        // Listen for Validation Complete
        manager.addListener("complete", function (e) {
          if (this.getValidationManager().isValid()) { // Form Passed Validation
            if (this.__modeNew) {
              this.createUser();
            } else {
              this.updateUser();
            }
          }
        }, this);
      },

      createUser: function () {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          // Get Results
          var user = e.getResult();
          if (tc.util.Entity.IsEntityOfType(user, 'user')) {
            // Set the User ID
            this.__tfUserId.setValue(user.id.toString());
            // Update the rest of the Information (Except the Password which has already been modified)
            this.__passwordChange = false;
            this.__modeNew = false; // User Created, any further changes are modifications only
            this.__user = user;
            if (this.__requireUpdate) {
              this.updateUser();
            } else { // We don't need to perform an Update (Because Certain Fields were Not Modified)
              this.fireDataEvent("user-saved", user);
            }
          }
        }, this);

        // Send request
        req.send('user', 'create', [this.__tfUserName.getValue(), this.__pfPassword.getValue()]);
      },

      updateUser: function () {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          // Get Results
          var user = e.getResult();
          if (tc.util.Entity.IsEntityOfType(user, 'user')) {
            this.__user = user;
            this.fireDataEvent("user-saved", user);
          }
        }, this);

        // TODO Handle Password Modifications

        // User Modification
        // TODO Modiy UserControler Update so it accepts the fields as HTTP GET Parameters (see user/list)
        var parameters = {
          first_name: this.parameterValue(this.__tfFirstName.getValue()),
          last_name: this.parameterValue(this.__tfLastName.getValue()),
          s_description: this.parameterValue(this.__tfShort.getValue()),
          l_description: this.parameterValue(this.__taLong.getValue())
        };

        // Send request
        req.send('user', 'update', this.__tfUserId.getValue(), parameters);
      },

      parameterValue: function (value) {
        value = tc.util.String.nullOnEmpty(value);
        return value != null ? value : 'null';
      }
    }
  });


/* TODO Solve the following problem
 * If we create the user in a 2 step process (create and then update, to modify the non-essential properties
 * there is always the possibility that the user might be created, but the update fails, for some reason.
 * HOW TO HANDLE THIS SCENARIO?
 * Possible Options :
 * Scenario 1:
 * i) Leave the user created.
 * ii) issue an error on the update, and let the user correct the modifications, and re-submit the update
 * Scenario 2:
 * i) Delete the newly created user.
 */