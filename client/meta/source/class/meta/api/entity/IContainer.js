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

qx.Interface.define("meta.api.entity.IContainer", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Container the Specified Widget's Definition
     *
     * @abstract
     * @param id {String} Widget ID
     * @return {Map|null} Widget Definition or NULL if it doesn't exist
     */
    getWidget: function(id) {
    },
    /**
     * Return List of Widget's IDs in the Container
     *
     * @abstract
     * @return {String[]} List of Widget's IDs 
     */
    getWidgets: function() {
    }
  }
});
