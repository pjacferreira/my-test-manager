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

qx.Interface.define("tc.meta.entities.IMetaList", {
  extend: [tc.meta.entities.IMetaEntity],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Returns the List's Description
     *
     * @abstract
     * @return {String} List's Description, or NULL if none
     */
    getDescription: function() {
    },
    /**
     * Does the List Allow the Service Indicated by the Alias?
     *
     * @abstract
     * @param alias {String} List Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
    },
    /**
     * Returns the Service ID on the Service Alias (required aliases 'list', optional 'count')
     *
     * @abstract
     * @param alias {String} List Service Alias
     * @return {String} Return Service ID or NULL on Failure
     */
    getService: function(alias) {
    },
    /**
     * Return Service Aliases Supported
     *
     * @abstract
     * @return {String[]} Service Aliases Supported, or NULL if none
     */
    getServices: function() {
    },
    /**
     * Returns the field, that is used as the key, for every record.
     *
     * @abstract
     * @return {String} Key Field ID
     */
    getKeyField: function() {
    },
    /**
     * Returns the field to display, in the case of ComboBox (when closed) or
     * SelectBox.
     *
     * @abstract
     * @return {String[]} Display Field ID
     */
    getDisplayField: function() {
    },
    /**
     * Returns the list of ALL fields To Display (in List or Table Format)
     *
     * @abstract
     * @return {String[]} Array of Field IDs
     */
    getColumns: function() {
    }
  }
});
