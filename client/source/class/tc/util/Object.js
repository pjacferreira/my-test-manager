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
 * Object helper functions
 *
 */
qx.Bootstrap.define("tc.util.Object", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    /**
     * Returns the 1st Property Name or the Object 1st Own Property Name
     *
     * @param object {Object} Object to get the 1st property from
     * @param own {Boolean?true} If we should only search for own properties
     * @return {*}
     */
    getFirstProperty: function (object, own) {
      var key, found = false;

      // Make sure own has a valid value
      own = qx.lang.Type.isBoolean(own) ? own : true;

      // Enumerate Properties looking for the one we want
      for (key in object) {
        if (!own || object.hasOwnProperty(key)) {
          found = true;
          break;
        }
      }

      return found ? key : null;
    },

    /**
     * Sets the value for a Property's that is found by descending from the root object, using the key defined in the path.
     *
     * @param object {Object} Root object to start with
     * @param path {String|Array} Property Name or a Sequence of Property Names that set the path to the value to be set
     * @param value {var} Value to set for the property.
     * @return {Boolean} TRUE if value set, FALSE otherwise.
     */
    setFromPath: function (object, path, value) {

      if (qx.lang.Type.isObject(object)) {
        var parent = object;
        var key = path;

        if (qx.lang.Type.isArray(path) && (path.length > 0)) {

          // Descend the Tree
          for (var i = 0; parent && (i < (path.length - 1)); i++) {
            key = tc.util.String.nullOnEmpty(path[i]);
            if (key !== null) {
              if (!(key in parent)) {
                parent[key] = {};
              }
              parent = qx.lang.Type.isObject(parent[key]) ? parent[key] : null;
            }
            else {
              parent = null;
            }
          }

          // Key is the Last Entry
          key = path[i];
        }

        // Set the value
        key = tc.util.String.nullOnEmpty(key);
        if ((parent !== null) && (key !== null)) {
          parent[key] = value;
          return true;
        }
      }

      return false;

    },

    /**
     *
     *
     * @param object {Object} Root object to start with
     * @param path {String|Array} Property Name or a Sequence of Property Names that set the path to the value to be extracted
     * @param options {Object} Options for the call.
     * @return {var!null} Null or Value of the Property at the end of the chain.
     */
    valueFromPath: function (object, path, options) {

      // Initialize All Options to Default Values IF not set
      options = qx.lang.Object.mergeWith({
        onlyOwn: true,
        'default': null
      }, (options == null) ? {} : options, true);

      if (qx.lang.Type.isObject(object)) {
        var parent = object;
        var key = path;
        var has = false;

        if (qx.lang.Type.isArray(path) && (path.length > 0)) {
          // Descend the Tree
          for (var i = 0; parent && (i < (path.length - 1)); i++) {
            key = tc.util.String.nullOnEmpty(path[i]);
            if (key !== null) {
              has = options.onlyOwn ? parent.hasOwnProperty(key) : key in parent;
              parent = has ? parent[key] : null;
            }
            else {
              parent = null;
            }
          }

          // Key is the Last Entry
          key = path[i];
        }

        // Get the value
        key = tc.util.String.nullOnEmpty(key);
        if ((parent !== null) && (key !== null)) {
          has = options.onlyOwn ? parent.hasOwnProperty(key) : key in parent;
          return has ? parent[key] : options['default'];
        }
      }

      return options['default'];
    }
  }
});