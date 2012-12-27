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

qx.Interface.define("tc.metaform.interfaces.IFormMetadataSource", {

  members: {

    /**
     * Retrieves Metadata Definition for a Table from some source.
     *
     * @abstract
     * @param name {String} Form Name (identifier)
     * @param callback {Function} Function to call on load complete or failure.
     *    See {@link tc.meta.IMetadataLoader#getMetaData}.
     * @param context {Object} The 'this' context for the callback
     *
     * @return {Boolean} 'true' on request launched, 'false' otherwise
     */
    getFormMeta: function (name, callback, context) {
      return true;
    }
  }
});
