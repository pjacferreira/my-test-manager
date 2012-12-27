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

qx.Interface.define("tc.meta.IUIMetadataSource", {
  extend: tc.meta.IMetadataSource,

  members: {

    /**
     * Retrieves Metadata Definition for a List of Actions from some source.
     *
     * @abstract
     * @param keys {String|Array|null} Action Name (identifier), a Action of Field Names (Identifiers), a NULL value
     *   implies that you want the all of the actions and their metadata.
     * @param callback {Function} Function to call on load complete or failure
     *    See {@link tc.meta.IMetadataLoader#getMetaData}.
     * @param context {Object} The 'this' context for the callback
     *
     * @return {Boolean} 'true' on request launched, 'false' otherwise
     */
    getActionsMeta: function (keys, callback, context) {
      return true;
    },

    /**
     * Retrieves Metadata Definition for a Field from some source. A Field is an element
     * to be used in a Form/Table/Dialog, etc. It defines the a Field Name and Expected Value Type.
     *
     * @abstract
     * @param keys {String|Array|null} Field Name (identifier), a List of Field Names (Identifiers), a NULL value
     *   implies that you want the all of the fields and their metadata.
     * @param callback {Function} Function to call on load complete or failure
     *    See {@link tc.meta.IMetadataLoader#getMetaData}.
     * @param context {Object} The 'this' context for the callback
     *
     * @return {Boolean} 'true' on request launched, 'false' otherwise
     */
    getFieldsMeta: function (keys, callback, context) {
      return true;
    }
  }
});
