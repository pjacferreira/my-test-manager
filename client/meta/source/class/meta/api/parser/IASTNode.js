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

qx.Interface.define("meta.api.parser.IASTNode", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /** 
     * Get the type of AST Node
     * 
     * @abstract
     * @return {String} AST Node's Type
     */
    getType: function() {
    },
    /** 
     * Get the Value for the AST Node
     * 
     * @abstract
     * @return {Var} AST Node's Value
     */
    getValue: function() {
    }
  }
});
