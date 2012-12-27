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
 #require(qx.ui.table.model.Simple)
 #require(qx.ui.table.Table)

 ************************************************************************ */

qx.Class.define("tc.table.TCRemoteModel",
  {
    extend: qx.ui.table.model.Remote,
    implement: [tc.table.filtered.IFilteredTableModel],
    include: [tc.table.filtered.MFilteredTableModel],

    members: {
      __sort: null,

      // overloaded - called whenever the table requests the row count
      _loadRowCount: function () {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          this._onRowCountLoaded(e.getResult());
        }, this);

        // Send request
        var filter = this._buildFilter();

        req.send('user', 'count', null, filter == null ? null : { filter: filter });
      },

      // overloaded - called whenever the table requests new data
      _loadRowData: function (firstRow, lastRow) {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          var users = e.getResult();
          this._onRowDataLoaded(users);
        }, this);

        // Send request
        var filter = this._buildFilter();
        var sort = this.__buildSort();
        req.send('user', 'list', null, this.__mixinNotNull({}, { filter: filter, sort: sort, limit: lastRow - firstRow + 1}));
      },

      __buildSort: function () {
        // get the column index to sort and the order
        var sortColumn = this.getSortColumnIndex();
        if (sortColumn >= 0) {
          var field = this.getColumnId(sortColumn);
          return this.isSortAscending() ? field : '!' + field;
        }

        return undefined;
      },

      changeColumnFilter: function (column, value, old) {
        if (column != null) {
          this._changeColumnFilter(this.getColumnId(column), value);
        }
      },

      __mixinNotNull: function (into, mixin) {

        for (var key in mixin) {
          if (mixin.hasOwnProperty(key)) {
            if (mixin[key] == null) {
              continue;
            } else {
              into[key] = mixin[key];
            }
          }
        }

        return into;
      }
    }
  });