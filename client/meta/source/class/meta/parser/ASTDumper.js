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
 * Base Meta Package Class
 */
qx.Class.define("meta.parser.ASTDumper", {
  extend: qx.core.Object,
  implement: meta.api.parser.IRunner,
  include: [
    meta.events.mixins.MMetaEventDispatcher
  ],
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
    /*
     ***************************************************************************
     METHODS 
     ***************************************************************************
     */
    /** 
     * Dump the AST Tree
     * 
     * @abstract
     * @param ast {meta.api.parser.IASTNode} AST to execute
     * @throw {String} if Failed to Start for any Reason (After Starting only 
     *   events will be fired in case of failure)
     */
    run: function(ast) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue(this.__isASTNode(ast), "[ast] Is not of the expected type!");
      }

      // Dump the AST Node
      this._mx_med_fireEventOK("run-results", ast.toString(true, true), "OK");
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    __isASTNode: function(node, type) {
      // Are we dealing with an AST Node?
      return qx.lang.Type.isObject(node) && qx.lang.Type.isFunction(node.getType);
    }
  } // SECTION: MEMBERS
});
