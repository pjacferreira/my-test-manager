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

qx.Interface.define("meta.api.parser.IParser", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /** 
     * Reset the Parser so it can (re)run the existing inputs
     * 
     * @abstract
     */
    reset: function() {
    },
    /**
     * Set the current input lines.
     * 
     * @abstract
     * @param lines {String[]|null} New Set of Lines
     * @returns {String[]|null} Old Set of Lines
     */
    setLines: function(lines) {
    },
    /** 
     * Parse the input lines
     * 
     * @abstract
     * @return {Map|null} AST Root Element or 'null' on failure to parse
     */
    parse: function() {
    }
  }
});
