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
 * Generic Remote Metadata Loader
 */
qx.Class.define("tc.meta.UIMetadataSource", {
  extend: tc.meta.DefaultMetadataSource,
  implement: tc.meta.IUIMetadataSource,

  construct: function (dataSource) {
    this.base(arguments, dataSource);
  },

  members: {

    // interface implementation
    getActionsMeta: function (list, callback, context) {
      return this.getMetaData('actions', list, callback, context);
    },

    // interface implementation
    getFieldsMeta: function (list, callback, context) {
      return this.getMetaData('fields', list, callback, context);
    }
  }
});
