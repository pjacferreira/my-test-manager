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
 * Builder for Table Toolbars
 */
qx.Bootstrap.define("tc.table.widget.TableToolbarBuilder", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    /**e
     * Returns an array, that is the intersection of 2 (previously) sorted arrays.
     *
     * @param metaPackage {tc.meta.packages.ITablePackage} Table Metadata Package
     * @param table Second {qx.ui.table.Table} Table
     * @return {Array} Resultant Intersection
     */
    build: function(metaPackage, table) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(metaPackage, tc.meta.packages.ITablePackage, "[metaPackage] Is not of the expected type!");
        qx.core.Assert.assertInstance(table, qx.ui.table.Table, "[table] Is not of the expected type!");
        qx.core.Assert.assertTrue(metaPackage.isReady(), "[metaPackage] Has not been initialized!");
      }

      var actions = metaPackage.getActions();
      if (actions !== null) {
        if (!actions.isReady()) {
          // TODO Really Implement Initialization for Actions 
          actions.initialize();
        }
        return this.__buildToolbar(actions, table);
      }
      return null;
    },
    __buildToolbar: function(metaPackage, table) {
      // Get List of Action for the Table
      var actionIDs = metaPackage.getActions();
      if (actionIDs.length) { // Have Actions?

        // Create Toolbar
        var toolbar = new qx.ui.toolbar.ToolBar();
        var action, command, button;
        for (var i = 0; i < actionIDs.length; ++i) {
          action = metaPackage.getAction(actionIDs[i]);
          if (action.isFormAction()) {
            command = this.__newFormCommand(action, table);
          } else if (action.isServiceAction()) {
            command = this.__newServiceCommand(action, table);
          } else {
            command = null;
          }

          if (command !== null) {
            button = this.__newButton(command);
            if (button !== null) {
              toolbar.add(button);
            }
          }
        }

        return toolbar;
      }

      return null;
    },
    __newFormCommand: function(action, table) {
      var command = this.__createCommand(action);
      if (command !== null) {
        command.addListener("execute", function() {

          // Selected Record (To Initialize Form From)
          var record = null;

          if (action.hasParameters()) {
            var records = this.__buildRecords(action.getParameters(), table);
            record = records !== null ? records[0] : null;
          }

          // Create and Build Form
          var form = new tc.meta.forms.Form(action.getActionEntity(), new tc.meta.datastores.RecordStore(), record);
          // Initialize Form
          form.initialize(record, {
            'ok': function(e) {
              // Load the Record (if Possible)
              var model = form.getModel();
              if ((record !== null) && model.canLoad()) {
                model.load();
              }

              // Create Form Dialog Box
              var dialog = new tc.windows.FormDialog(model.getTitle(), new qx.ui.form.renderer.Single(form));

              // Event : Data Loaded from Backend
              form.addListener("formSubmitted", function(e) {
//                this.info("Data Saved");
                dialog.close();
              }, this);
              // Event : Data Synchronized to Backend
              form.addListener("formCancelled", function(e) {
//                this.info("Form Cancelled");
                dialog.close();
              }, this);
              // Event : Error Loading Form or in Data Synchronization
              form.addListener("nok", function(e) {
//                this.error("Error");
                dialog.close();
              }, this);

              // Add it to the Application Root
              var root = qx.core.Init.getApplication().getRoot();
              var rootSize = root.getSizeHint();
              root.add(dialog, {
                left: 50,
                top: 50
              });

              // Display the Dialog
              dialog.show();
            },
            'nok': function(e) {
              this.error("Form Initialization Error");
            },
            'context': this
          });
        }, this);
      }

      return command;
    },
    __newServiceCommand: function(action, table) {

      var command = this.__createCommand(action);
      if (command !== null) {
        command.addListener("execute", function() {
          this.error("To Be Implemented");
        });
      }

      return command;
    },
    __createCommand: function(action) {
      var command = new qx.ui.core.Command(action.getShortcut());
      command.setLabel(action.getLabel());
      if (action.getIcon() !== null) {
        command.setIcon(action.getIcon());
      }
      if (action.getDescription() !== null) {
        command.setToolTipText(action.getDescription());
      }

      return command;
    },
    __newButton: function(command, show) {
      if (command !== null) {
        var button = new qx.ui.toolbar.Button();
        button.setCommand(command);
        if (show) {
          button.setShow(show);
        }

        return button;
      }
      return null;
    },
    __buildRecords: function(parameters, table) {

      // Get Table Model
      var model = table.getTableModel();

      // Convert Parameters to Column Indexes
      var ids = [];
      var columns = [];
      var index = null;
      for (var i = 0; i < parameters.length; ++i) {
        index = model.getColumnIndexById(parameters[i]);
        if (index != null) {
          ids.push(parameters[i]);
          columns.push(index);
        }
      }

      var records = [];

      if (columns.length) {
        // Build Records
        var selection = table.getSelectionModel();
        var ranges = selection.getSelectedRanges();

        if (ranges.length) {
          var record;
          for (var r = 0; r < ranges.length; ++r) {
            for (var row = ranges[r].minIndex; row <= ranges[r].maxIndex; ++row) {
              record = {};
              for (var c = 0; c < columns.length; ++c) {
                record[ids[c]] = model.getValue(columns[c], row);
              }
            }
          }

          records.push(record);
        }
      }

      return records.length ? records : null;
    }

  } // SECTION: STATICS
});
