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
qx.Class.define("tc.table.filtered.FactoryHeaderCell", {
  extend: qx.ui.table.headerrenderer.Default,

  events: {
    "changeColumnFilter": "tc.event.type.ColumnFilter"
  },

  members: {
    // overridden
    createHeaderCell: function (cellInfo) {
      var widget = new tc.table.filtered.HeaderCell();
      var column = cellInfo.col;
      widget.addListener('changeFilter', function (e) {
        var eFilter = new tc.event.type.ColumnFilter();
        eFilter.init(column, e.getData(), e.getOldData());
        eFilter.setType('changeColumnFilter');
        this.dispatchEvent(eFilter);
      }, this);

      // Update the Widget
      this.updateHeaderCell(cellInfo, widget);

      return widget;
    },

    // overridden
    updateHeaderCell: function (cellInfo, cellWidget) {
      this.base(arguments, cellInfo, cellWidget);

      var table = cellInfo.table;
      var filtered = false;
      if (table != null) {
        var model = table.getTableModel();
        if (qx.lang.Type.isFunction(model.isColumnFilterable)) {
          filtered = model.isColumnFilterable(cellInfo.col);
        }
      }

      // Enable Filter if  Filterable
      cellWidget.setEnableFilter(filtered);
    }

  }
});