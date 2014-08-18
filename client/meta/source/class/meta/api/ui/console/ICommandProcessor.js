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

qx.Interface.define("meta.api.ui.console.ICommandProcessor", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Output to Console
    "console-output": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /** 
     * Process Commands and return result
     * 
     * @abstract
     * @param commands {String|String[]} Commands to be Processed
     */
    process: function(commands) {
    }
  }
});
