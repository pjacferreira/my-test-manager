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
qx.Class.define("tc.table.filtered.ColumnModel", {
  extend: qx.ui.table.columnmodel.Basic,

  events: {
    "changeColumnFilter": "tc.event.type.ColumnFilter"
  },

  members: {

    init: function (colCount, table) {

      // Call Bass Class
      this.base(arguments, colCount, table);

      // Create a new Header Cell Renderer
      var renderer = new tc.table.filtered.FactoryHeaderCell();
      // Add the Listener for the Change Filter
      renderer.addListener('changeColumnFilter', function (e) {
        this.dispatchEvent(e.clone());
      }, this);

      // Set the Header Cell Renderer to the New Filtered Cell Renderer
      for (var i = 0; i < colCount; ++i) {
        this.setHeaderCellRenderer(i, renderer);
      }
    }
  }
});