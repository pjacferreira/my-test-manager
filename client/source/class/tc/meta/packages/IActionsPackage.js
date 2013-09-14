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

qx.Interface.define("tc.meta.packages.IActionsPackage", {
  extend: [tc.meta.packages.IMetaPackage],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Action Exist in the Package?
     *
     * @abstract
     * @param id {String} Action ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw If Package not Ready
     */
    hasAction: function(id) {
    },
    /**
     * Get Action Container
     *
     * @abstract
     * @param id {String} Action ID
     * @return {tc.meta.data.IMetaAction} Return Metadata for field
     * @throw If Package not Ready or Action Doesn't Exist
     */
    getAction: function(id) {
    },
    /**
     * Get a List of Action IDs in the Container
     *
     * @abstract
     * @return {String[]} Array of Action IDs or Empty Array (if no actions in the package)
     * @throw If Package not Ready
     */
    getActions: function() {
    }
  } // SECTION: MEMBERS
});
