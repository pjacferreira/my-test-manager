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

/**
 * Supplemental Model for Filtered Tables
 */
qx.Interface.define("tc.table.filtered.IFilteredTableModel", {
  extend: [qx.ui.table.ITableModel],

  properties: {
    filterVisible: {
      type: 'Boolean',
      init: true,
      event: 'changeFilterVisibility',
      themeable: true
    }
  },

  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */

  events: {
    /**
     * Fired to Hide the Column Filter Visbility before any currently applied
     * column filters are removed, and the table data Reloaded.
     */
    "changeFilterVisibility": "qx.event.type.Data"
  },


  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */

  members: {
    /**
     * Change the Filter Value for a Column
     *
     * @abstract
     * @param old {Integer} id of column to modify
     * @param value {String} new filter value for the column
     * @return {void}
     */
    changeColumnFilter: function (old, value) {
      return true;
    },

    /**
     * Returns whether a column is filterable.
     *
     * @param columnIndex {Integer} the column to check.
     * @return {Boolean} whether the column is filterable.
     */
    isColumnSortable: function (columnIndex) {
      return true;
    },

    /**
     * Sets whether a column is filterable.
     *
     * @param columnIndex {Integer} the column of which to set the filterable state.
     * @param filterable {Boolean} whether the column should be filterable.
     * @return {void}
     */
    setColumnFilterable: function (columnIndex, filterable) {
      return true;
    }
  }
});