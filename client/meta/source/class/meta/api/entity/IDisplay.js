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

qx.Interface.define("meta.api.entity.IDisplay", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /** Get Label Text
     * 
     * @abstract
     * @return {String} Label Text
     */
    getLabel: function() {
    },
    /** Get URL for Icon
     * 
     * @abstract
     * @return {String|null} Icon URL or 'null' if none available
     */
    getIcon: function() {
    },
    /**
     * Retrieve Tooltip Text
     * 
     * @abstract
     * @return {String|null} Tooltip or 'null' if none available
     */
    getTooltip: function() {
    },
    /**
     * Retrieve Widget Help
     * 
     * @abstract
     * @return {String|null} Tooltip or 'null' if none available
     */
    getHelp: function() {
    }
  }
});
