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
 * Generic Remote Metadata Source
 */
qx.Class.define("tc.table.meta.TableSource", {
  extend: tc.meta.UIMetadataSource,
  implement: tc.table.meta.ITableMetadataSource,

  construct: function (dataSource) {
    this.base(arguments, dataSource);
  },

  members: {
    // interface implementation
    getTableMeta: function (name, callback, context) {
      return this.getMetaData('tables', name, callback, context);
    }
  }
});
