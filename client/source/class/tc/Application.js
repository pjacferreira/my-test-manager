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

        // Elements of the Window
        this.getRoot().add(login);

        this.__loadTable('project');
      },

      __newCommand: function (entity, mode) {
        var command = null;
        switch (mode) {
          case 'create': // Create Entity
            command = new qx.ui.core.Command("CTRL+N");
            command.setLabel("Create User");
            command.setIcon("tc/user_add.png")
            command.setToolTipText("Create User.");
            command.addListener("execute", function () {
              // Setup Metadata Model
              // Initialize Meta Service
              var service = tc.services.TCMetaService.getInstance();
              service.setBaseURL('meta');

              var sourceMetadata = new tc.metaform.FormLoader(service);
              var metadataModel = new tc.metaform.DefaultMetadataModel(entity, 'create', sourceMetadata);
              // Setup Model Data Source
              var sourceData = new tc.metaform.TCDataStore(entity);
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
            break;
          case 'read': // Read Entity Information
            command = new qx.ui.core.Command("CTRL+R");
            command.setLabel("Read");
            command.setIcon("tc/vcard.png")
            command.setToolTipText("Detailed User Information");
            command.addListener("execute", function () {
              alert("Detailed User Information");
            }, this);
            break;
          case 'update': // Update Entity
            command = new qx.ui.core.Command("CTRL+E");
            command.setLabel("Edit");
            command.setIcon("tc/user_edit.png")
            command.setToolTipText("Edit User Data");
            command.addListener("execute", function () {
              alert("Edit User Information");
            }, this);
            break;
          case 'delete': // Delete Entity
            command = new qx.ui.core.Command("CTRL+D");
            command.setLabel("Delete");
            command.setIcon("tc/user_delete.png")
            command.setToolTipText("Delete User");
            command.addListener("execute", function () {
              alert("Delete User");
            }, this);
        }

        return command;
      },

      __newButton: function (command, show) {

        if (command != null) {
          var button = new qx.ui.toolbar.Button();
          button.setCommand(command);
          if (show) {
            button.setShow(show);
          }

          return button;
        }
        return null;
      },

      __loadTable: function (entity) {
        // Initialize Meta Service
        var service = tc.services.TCMetaService.getInstance();
        service.setBaseURL('meta');

        // Create Model from Meta Data
        var model = new tc.table.model.MetaTableModel(entity + ':',
          new tc.table.meta.TableSource(service));

        // Add Event Listeners
        model.addListener("metadataLoaded", function () {
          // TODO Do the same thing that was done to with sort-on, to filter-on
          var table = new tc.table.filtered.Table(model);

          // Disable Footer
          table.setStatusBarVisible(false);

          /*
           * Create Toolbar
           */
          var toolbar = new qx.ui.toolbar.ToolBar();
          var buttons = ['create', 'read', 'update', 'delete'];
          var button = null;
          for (var i = 0; i < buttons.length; ++i) {
            button = this.__newButton(this.__newCommand(entity, buttons[i]));
            if (button !== null) {
              toolbar.add(button);
            }
          }
          toolbar.setShow("icon");

          /*
           * Composite Toolbar + Table
           */
          var composite = new qx.ui.container.Composite();
          composite.setLayout(new qx.ui.layout.VBox(2));

          // Set Table Size
          var t_width = 100 * model.getColumnCount() + 20;
          table.set({
            width: t_width > 600 ? 600 : t_width,
            height: 400,
            decorator: null
          });

          composite.add(toolbar);
          composite.add(table);

          this.getRoot().add(composite, {
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

        // Initialize the Model
        model.load();
      }
    }
  });
