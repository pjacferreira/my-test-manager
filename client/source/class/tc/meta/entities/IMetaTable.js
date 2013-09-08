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

qx.Interface.define("tc.meta.entities.IMetaTable", {
  extend: [tc.meta.entities.IMetaEntity],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is this a Read Only Table?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
    },
    /**
     * Returns the Table's Title
     *
     * @abstract
     * @return {String} Table's Title
     */
    getTitle: function() {
    },
    /**
     * Does the Table Allow the Service Indicated by the Alias?
     *
     * @abstract
     * @param alias {String} Table Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
    },
    /**
     * Returns the Service ID on the Service Alias (required aliases 'list' and 'count')
     *
     * @abstract
     * @param alias {String} Table Service Alias
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
     * Returns the list of ALL fields used.
     *
     * @abstract
     * @return {String[]} Array of Field IDs in the Group
     */
    getFields: function() {
    },
    /**
     * Returns the list of ALL fields To Display.
     *
     * @abstract
     * @return {String[]} Array of Field IDs in the Group
     */
    getColumns: function() {
    },
    /**
     * Returns the list of fields that are Initially Hidden.
     *
     * @abstract
     * @return {String[]} Array of Field IDs, or NULL if No Fields are to be hidden
     */
    getHiddenFields: function() {
    },
    /**
     * Does the Table Allow Sorting?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canSort: function() {
    },
    /**
     * Returns the list of fields that can be used to Sort the Table Data Set.
     *
     * @abstract
     * @return {String[]} Array of Field IDs, or NULL if Table is not Sortable
     */
    getSortFields: function() {
    },
    /**
     * Does the Table Allow Filtering?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canFilter: function() {
    },
    /**
     * Returns the list of fields that can be used to Filter the Table Data Set.
     *
     * @abstract
     * @return {String[]} Array of Field IDs, or NULL if Table is not Filterable
     */
    getFilterFields: function() {
    }
  }
});
