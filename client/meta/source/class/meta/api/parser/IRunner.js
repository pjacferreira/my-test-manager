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

qx.Interface.define("meta.api.parser.IRunner", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notify of Results fo Run
    "run-results": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /** 
     * Execute the AST Tree
     * 
     * @abstract
     * @param ast {meta.api.parser.IASTNode} AST to execute
     * @param env {Map|null} Enviroment Properties
     * @throw {String} if Failed to Start for any Reason (After Starting only 
     *   events will be fired in case of failure)
     */
    run: function(ast, env) {
    }
  }
});
