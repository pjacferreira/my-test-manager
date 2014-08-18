/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("meta.api.entity.IFormServices", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Form define the Service Alias?
     * 
     * @abstract
     * @param alias {String} Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
    },
    /**
     * Get the List of Service Aliases defined
     *
     * @abstract
     * @return {String[]} List of Service Aliases
     */
    getServices: function() {
    },
    /**
     * Get the Service ID for the Alias
     *
     * @abstract
     * @param alias {String} Service Alias
     * @return {String|null} Service ID or 'null'
     */
    getServiceID: function(alias) {
    }
  }
});
