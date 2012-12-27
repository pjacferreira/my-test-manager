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
qx.Class.define("tc.metaform.FormLoader", {
  extend: tc.meta.UIMetadataSource,
  implement: tc.metaform.interfaces.IFormMetadataSource,

  construct: function (dataSource) {
    this.base(arguments, dataSource);
  },

  members: {
    // interface implementation
    getFormMeta: function (name, callback, context) {
      return this.getMetaData('form', name, callback, context);
    }
  }
});
