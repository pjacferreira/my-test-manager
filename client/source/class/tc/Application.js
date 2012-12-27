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
 #asset(tc/*)
 #require(tc.table.filtered)
 #require(tc.table.model.MetaTableModel)
 #require(tc.table.meta.TableSource)
 #require(tc.services.TCMetaService)
 #require(tc.metaform.FormLoader)
 #require(tc.metaform.DefaultMetadataModel)
 #require(tc.metaform.TCDataStore)
 #require(tc.metaform.DefaultModel)
 #require(tc.metaform.Form)
 ************************************************************************ */

/**
 * This is the main application class of your custom application "testcenter_web"
 */
qx.Class.define("tc.Application",
  {
    extend: qx.application.Standalone,


    /*
     *****************************************************************************
     MEMBERS
     *****************************************************************************
     */

    members: {
      /**
       * This method contains the initial application code and gets called
       * during startup of the application
       *
       * @lint ignoreDeprecated(alert)
       */
      main: function () {
        // Call super class
        this.base(arguments);

        // Enable logging in debug variant
        if (qx.core.Environment.get("qx.debug")) {
          // support native logging capabilities, e.g. Firebug for Firefox
          qx.log.appender.Native;
          // support additional cross-browser console. Press F7 to toggle visibility
          qx.log.appender.Console;
        }

        // create the composite
        var login = new tc.widgets.LoginWidget()

        /*
         * Commands
         */
        // Create User (Old Method)
        var cmCreate = new qx.ui.core.Command("CTRL+A");
        cmCreate.setLabel("Create User");
        cmCreate.setIcon("tc/user_add.png")
        cmCreate.setToolTipText("Create User.");
        cmCreate.addListener("execute", function () {
          // Create the Form
          var form = new tc.forms.UserForm();

          // Create Dialog
          var dialog = new tc.windows.FormDialog("New User", new qx.ui.form.renderer.Single(form));

          // Add Form Listener for User Creation/Modification
          form.addListener("user-saved", function (e) {
            dialog.close();
          });

          // Add it to the Application Root
          this.getRoot().add(dialog, {
            left: 50,
            top: 50
          });

          // Display the Dialog
          dialog.show();
        }, this);

        // Create User (Old Method)
        var cmCreate1 = new qx.ui.core.Command("CTRL+N");
        cmCreate1.setLabel("Create User NF");
        cmCreate1.setIcon("tc/user_add.png")
        cmCreate1.setToolTipText("Create User.");
        cmCreate1.addListener("execute", function () {
          // Setup Metadata Model
          // Initialize Meta Service
          var service = tc.services.TCMetaService.getInstance();
          service.setBaseURL('meta');

          var sourceMetadata = new tc.metaform.FormLoader(service);
          var metadataModel = new tc.metaform.DefaultMetadataModel('user', 'create', sourceMetadata);
          // Setup Model Data Source
          var sourceData = new tc.metaform.TCDataStore('user');
          // Create the Model
          var model = new tc.metaform.DefaultModel(metadataModel, sourceData);
          // Create the Form
          var form = new tc.metaform.Form();

          // Event : Form Ready (Initialized)
          var dialog = null;
          form.addListener("formReady", function (e) {
            // Create Dialog
            dialog = new tc.windows.FormDialog(model.getFormTitle(), new qx.ui.form.renderer.Single(form));

            // Add it to the Application Root
            this.getRoot().add(dialog, {
              left: 50,
              top: 50
            });

            // Display the Dialog
            dialog.show();
          }, this);
          // Event : Data Loaded from Backend
          form.addListener("formSubmitted", function (e) {
            alert("Data Saved");
            if (dialog != null) {
              dialog.close();
            }
          }, this);
          // Event : Data Synchronized to Backend
          form.addListener("formCancelled", function (e) {
            alert("Form Cancelled");
            if (dialog != null) {
              dialog.close();
            }
          }, this);
          // Event : Error Loading Form or in Data Synchronization
          form.addListener("error", function (e) {
            alert("Form Error");
            if (dialog != null) {
              dialog.close();
            }
          }, this);

          // Set the Form and Initialize
          form.setFormModel(model);
        }, this);

        // Read User
        var cmRead = new qx.ui.core.Command("CTRL+R");
        cmRead.setLabel("Read");
        cmRead.setIcon("tc/vcard.png")
        cmRead.setToolTipText("Detailed User Information");
        cmRead.addListener("execute", function () {
          alert("Detailed User Information");
        }, this);

        // Update User
        var cmUpdate = new qx.ui.core.Command("CTRL+E");
        cmUpdate.setLabel("Edit");
        cmUpdate.setIcon("tc/user_edit.png")
        cmUpdate.setToolTipText("Edit User Data");
        cmUpdate.addListener("execute", function () {
          alert("Edit User Information");
        }, this);

        // Delete User
        var cmDelete = new qx.ui.core.Command("CTRL+D");
        cmDelete.setLabel("Delete");
        cmDelete.setIcon("tc/user_delete.png")
        cmDelete.setToolTipText("Delete User");
        cmDelete.addListener("execute", function () {
          alert("Delete User");
        }, this);

        // Elements of the Window
        this.getRoot().add(login);
        // Create a Button to Open User Form Window
        this.getRoot().add(this.__newButton(cmCreate), {
          left: 0,
          top: 100
        });
        // Create a Button to Open User Form Window (Based on New Meta Form)
        this.getRoot().add(this.__newButton(cmCreate1), {
          left: 90,
          top: 100
        });

        /*
         * Composite Toolbar + Filter
         */
        var composite = new qx.ui.container.Composite();
        composite.setLayout(new qx.ui.layout.VBox(2));

        var toolbar = new qx.ui.toolbar.ToolBar();
        toolbar.add(this.__newButton(cmCreate));
        toolbar.add(this.__newButton(cmRead));
        toolbar.add(this.__newButton(cmUpdate));
        toolbar.add(this.__newButton(cmDelete));
        toolbar.setShow("icon");

        // Create the Table Model
        var model2 = new tc.table.RemoteUserModel();
        model2.setColumns(
          [ 'ID', 'User', 'First Name', 'Last Name', 'Description' ],
          [ 'user:id', 'user:name', 'user:first_name', 'user:last_name', 'user:s_description']
        );

        // Limit Sortable (No Sort on Description)
        model2.setColumnSortable(4, false);
        model2.setColumnFilterable(0, true);

        var filterTable2 = new tc.table.filtered.Table(model2);

        // Disable Footer
        filterTable2.setStatusBarVisible(false);

        // Set Table Size
        filterTable2.set({
          width: 600,
          height: 200,
          decorator: null
        });

        composite.add(toolbar);
        composite.add(filterTable2);

        this.getRoot().add(composite, {
          left: 700,
          top: 200
        });

        this.__loadTable('user');
      },


      __newButton: function (command, show) {

        var button = new qx.ui.toolbar.Button();
        button.setCommand(command);
        if (show) {
          button.setShow(show);
        }

        return button;
      },

      __loadTable: function (table_id) {
        // Initialize Meta Service
        var service = tc.services.TCMetaService.getInstance();
        service.setBaseURL('meta');

        // Create Model from Meta Data
        var model = new tc.table.model.MetaTableModel('user:',
          new tc.table.meta.TableSource(service));

        // Initialize the Model
        model.load();

        model.addListener("metadataLoaded", function () {
          // TODO Do the same thing that was done to with sort-on, to filter-on
          var filterTable = new tc.table.filtered.Table(model);

          // Disable Footer
          filterTable.setStatusBarVisible(false);

          // Set Table Size
          filterTable.set({
            width: 100 * model.getColumnCount() + 20,
            height: 400,
            decorator: null
          });

          this.getRoot().add(filterTable, {
            left: 0,
            top: 200
          });

          /*
           * Button to Toggle Table Filter
           */
          // Create a Button to Open User Form Window
          var btnFilter = new qx.ui.form.Button("Toggle Filter");
          btnFilter.addListener("execute", function () {
            this.toggleFilterVisible();
          }, model);

          this.getRoot().add(btnFilter, {
            left: 0,
            top: 140
          });
        }, this);

        model.addListener("metadataInvalid", function (e) {
          alert("Failed to Load Table Model.");
        });
      }
    }
  });
