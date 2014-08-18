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

/**
 * Requirements for Communication Input/Output Communication Between Meta Widgets
 */
qx.Interface.define("meta.api.ui.IWidgetActions", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Widget have any action Groups?
     * 
     * @abstract
     * @return {Boolean} Returns <code>true</code> if Widget has Actions Groups Defined.
     */
    hasActions: function() {
    },
    /**
     * Get list of Action Groups for the Widget
     * 
     * @abstract
     * @return {String[]} Returns List of Action Groups or 'null' if there are none
     */
    getActions: function() {
    },
    /**
     * Get the Definition for a Particular Action Group
     * 
     * @abstract
     * @param group {String} Name of Action Group
     * @return {String[]} Returns list of action entries for the group or 'null' 
     *   if group doesn't exist
     */
    getAction: function(group) {
    },
    /**
     * Execute the Action Group
     * 
     * @abstract
     * @param group {String} Name of Action Group
     * @return {Boolean} 'true' if execution started (potential asynchronous execution),
     *   'false' onn any failure
     */
    executeAction: function(group) {
    }
  } // SECTION: MEMBERS
});
