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
 * NULL Generic Metadata Loader
 */
qx.Class.define("tc.meta.DefaultMetadataSource", {
  extend: qx.core.Object,
  implement: tc.meta.IMetadataSource,

  construct: function (dataSource) {
    this.__dataSource = (dataSource != null) && qx.lang.Type.isObject(dataSource) ? dataSource : null;
  },

  destruct: function () {
    this.__dataSource = null;
  },

  members: {
    __dataSource: null,

    // interface implementation
    getMetaData: function (type, keys, callback, context) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertNotNull(callback, "Callback function required!");
      }

      type = tc.util.String.nullOnEmpty(type);
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertNotNull(type, "'type' has to be a Non-Empty String!");
      }

      // If Callback Provided return NULL answers
      if ((type != null) && qx.lang.Type.isFunction(callback)) {
        context = qx.lang.Type.isObject(context) ? context : this;

        // Assume No Data Source or No Results (so just return null)
        var results = null;

        if (this.__dataSource) {
          if (this.__dataSource.getMetaData &&
            qx.lang.Type.isFunction(this.__dataSource.getMetaData)) {
            return this.__dataSource.getMetaData.call(this.__dataSource, type, keys, callback, context);
          } else if (qx.lang.Type.isObject(this.__dataSource)) {
            results = this.__retrieveKeys(type, keys);
          }
        }

        callback.call(context, 0, 'Ok', type, results);
        return true;
      }

      return false;
    },

    __retrieveKeys: function (type, keys) {
      if (this.__dataSource.hasOwnProperty(type) &&
        qx.lang.Type.isObject(this.__dataSource[type])) {

        // Root Object to Search for Meta Data for the given type
        var root = this.__dataSource[type];

        // Convert String to an Array to Simplify Code
        if (qx.lang.Type.isString(keys)) {
          keys = new Array(keys);
        }

        var key;
        var results = {};
        var haveResults = false;
        for (var i = 0; i < keys.length; ++i) {
          key = tc.util.String.nullOnEmpty(keys);
          if ((key != null) && root.hasOwnProperty(key)) {
            results[key] = root[key];
            haveResults = true;
          }
        }

        return haveResults ? results : null;
      }

      return null;
    }
  }
});
