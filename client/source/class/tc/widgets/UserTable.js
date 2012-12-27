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
 #require(qx.ui.table.Table)

 ************************************************************************ */

qx.Class.define("tc.widgets.UserTable",
  {
    extend: qx.ui.container.Composite,

    properties: {
      enableFilter: {
        check: 'Boolean',
        init: true,
        apply: "_applyEnableFilter"
      }
    },

    construct: function () {
      this.base(arguments);

      // configure it with a horizontal box layout with a spacing of '5'
      this.setLayout(new qx.ui.layout.HBox(5));

      // Create the Table Model
      var model = new tc.table.RemoteUserModel();
      model.setColumns(
        [ 'ID', 'User', 'First Name', 'Last Name', 'Description' ],
        [ 'id', 'name', 'first_name', 'last_name', 's_description']
      );

      // Limit Sortable (No Sort on Description)
      model.setColumnSortable(4, false);

      var table = this.__table = new qx.ui.table.Table(model, {
          tableColumnModel: function (table) {
            return new tc.table.filtered.ColumnModel(table);
          }
        }
      );

      // Header Filter
//      table.addListener("changeFilter", function (e) {
//        alert(e.getValue());
//      });

      // Get the Preset Header Cell Height
      this.__defaultHeaderHeight = table.getHeaderCellHeight();

      // Disable Footer
      table.setStatusBarVisible(false);

      this.__defaultHeaderHeight = table.getHeaderCellHeight();

      // Set Table Size
      table.set({
        width: 600,
        height: 400,
        decorator: null,
        // Cell Height = Label Height + Text Field Widget Height + "Padding"
        headerCellHeight: this.getEnableFilter() ? 15 + 22 + 7 : this.__defaultHeaderHeight
      });

      // Change Selection Mode
      table.getSelectionModel().setSelectionMode(qx.ui.table.selection.Model.MULTIPLE_INTERVAL_SELECTION);

      this.add(table);
    },

    members: {
      __table: null,
      __defaultHeaderHeight: null,

      _applyEnableFilter: function (value, old) {
        if (value) { // Enable
          this.__table.setHeaderCellHeight(15 + 22 + 7);
        } else {  // Disable
          this.__table.resetHeaderCellHeight();
        }
      }
    }

  });