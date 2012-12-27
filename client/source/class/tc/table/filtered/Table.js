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

 ************************************************************************ */

qx.Class.define("tc.table.filtered.Table", {
  extend: qx.ui.table.Table,

  events: {
    "changeColumnFilter": "tc.event.type.ColumnFilter"
  },

  properties: {

    /** The height of the header cells. */
    headerCellHeight: {
      init: 44, // Header Label + Filter Field + "Padding" (7)
      refine: true
    },

    /**
     * A function to instantiate a table column model.  This allows subclasses
     * of Table to subclass this internal class.  To take effect, this
     * property must be set before calling the Table constructor.
     */
    newTableColumnModel: {
      refine: true,
      init: function (table) {
        var table_instance = table;
        var model = new tc.table.filtered.ColumnModel(table);
        // Add the Listener for the Change Filter
        model.addListener('changeColumnFilter', function (e) {
          // Dispatch it to the Table Object
          table_instance._modifyColumnFilter(e.getColumn(), e.getFilter(), e.getOldFilter());
//          table_instance.dispatchEvent(e.clone());
        }, table_instance);
        return model;
      }
    }
  },

  /**
   * @param tableModel {tc.table.filtered.IFilteredTableModel ? null}
   *   The table model to read the data from.
   *
   * @param custom {Map ? null}
   *   A map provided to override the various supplemental classes allocated
   *   within this constructor.  Each property must be a function which
   *   returns an object instance, as indicated by shown the defaults listed
   *   here:
   *
   *   see qx.ui.table.Table
   */
  construct: function (tableModel, custom) {
    this.base(arguments, tableModel, custom);
  },

  members: {

    // property modifier
    _applyTableModel: function (model, old) {
      this.base(arguments, model, old);

      /* Allow Asynchronous load of Table Model by modifiying the behaviour ofthe original function

      (PROBLEM 1: Model has not yet been loaded (or atleast graunteed to have been loaded, this will probably return 0)
      this.getTableColumnModel().init(value.getColumnCount(), this);

      if (old != null)
      {
        old.removeListener(
          "metaDataChanged",
          this._onTableModelMetaDataChanged, this
        );

        old.removeListener(
          "dataChanged",
          this._onTableModelDataChanged,
          this);
      }

      value.addListener(
        "metaDataChanged",
        this._onTableModelMetaDataChanged, this
      );

      value.addListener(
        "dataChanged",
        this._onTableModelDataChanged,
        this);

      // Update the status bar
      this._updateStatusBar();

      this._updateTableData(
        0, value.getRowCount(),
        0, value.getColumnCount()
      );
      this._onTableModelMetaDataChanged();

       (PROBLEM 2: init Called at End, therefore nothing is ready)

       // If the table model has an init() method, call it. We don't, however,
      // call it if this is the initial setting of the table model, as the
      // scrollers are not yet initialized. In that case, the init method is
      // called explicitly by the Table constructor.
      if (old && value.init && typeof(value.init) == "function")
      {
        value.init(this);
      }
      */

      // Add a Listener to catch the Filter Visibility
      model.addListener("changeFilterVisibility", function (e) {
        this.__modifyFilterVisibility(e.getData());
      }, this);
    },

    _modifyColumnFilter: function (column, filter, old) {
      var model = this.getTableModel();
      if (model) {
        model.changeColumnFilter(column, filter, old);
      }
    },

    __modifyFilterVisibility: function (visible) {
      if (visible) { // Enable
        this.setHeaderCellHeight(15 + 22 + 7);
      } else {  // Disable
        this.setHeaderCellHeight(16);
      }
    }
  }

});