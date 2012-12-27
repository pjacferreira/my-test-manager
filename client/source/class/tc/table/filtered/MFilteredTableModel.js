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
 * Mixin implementation of tc.table.filtered.IFilteredTableModel
 */
qx.Mixin.define("tc.table.filtered.MFilteredTableModel", {

  properties: {
    filterVisible: {
      check: 'Boolean',
      init: true,
      event: 'changeFilterVisibility',
      apply: '_applyFilterVisibility',
      themeable: true
    }
  },


  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */

  members: {
    __filter: null,
    __filterableColArr : null,

    // overridden
    setColumnFilterable : function(columnIndex, filterable)
    {
      if (filterable != this.isColumnFilterable(columnIndex))
      {
        if (this.__filterableColArr == null) {
          this.__filterableColArr = [];
        }

        this.__filterableColArr[columnIndex] = filterable;

        this.fireEvent("metaDataChanged");
      }
    },

    // overridden
    isColumnFilterable : function(columnIndex)
    {
      return (
        this.__filterableColArr
          ? (this.__filterableColArr[columnIndex] != null) && (this.__filterableColArr[columnIndex] === true)
          : false
        );
    },

    /**
     * Mixin method. Applies Changes toFilter Visibility.
     *
     * @return {void}
     */
    _applyFilterVisibility: function (value, old) {
      if (this.__filter) { // If we have a Filter just Reload the Data
        this.reloadData();
      }
    },

    _changeColumnFilter: function (column_id, value) {
      var bReload = false;

      if (column_id != null) {
        value = tc.util.String.nullOnEmpty(value);
        if ((value != null) && value.length) {
          if (this.__filter == null) {
            this.__filter = { };
          }
          this.__filter[column_id] = value;
          bReload = true;
        }
        else if ((this.__filter != null) && this.__filter.hasOwnProperty(column_id)) {
          delete this.__filter[column_id];
          bReload = true;
        }
      }

      if (bReload) { // Reload Model Data
        this.reloadData();
      }
    },

    /**
     * Mixin method. Builds a String from the Filter Object.
     *
     * @return {String} filter string or null
     */
    _buildFilter: function () {
      var filter = null;
      var bFirst = true;
      if (this.getFilterVisible() && this.__filter) {
        for (var field in this.__filter) {
          if (this.__filter.hasOwnProperty(field)) {
            if (bFirst) {
              filter = field + ':' + this.__filter[field];
              bFirst = false;
            } else {
              filter += ';' + field + ':' + this.__filter[field];
            }
          }
        }
      }

      return filter === null ? undefined : filter;
    }
  },


  /*
   *****************************************************************************
   DESTRUCTOR
   *****************************************************************************
   */

  destruct: function () {
    this.__filter = null;
    this.__filterableColArr = null;
  }
});
