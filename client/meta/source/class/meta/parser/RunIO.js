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
qx.Class.define("meta.parser.RunIO", {
  extend: meta.parser.AbstractRunner,
  include: [
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Destructor to Cleanup
   */
  destruct: function() {
    // Cleanup
    this._lhs = null;

    // Detach Existing Runner
    if (this.__expression !== null) {
      this._mx_meh_detach("run-results", this.__expression);
      this.__expression = null;
    }
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _lhs: null,
    _phase: 1,
    __expression: null,
    /*
     ***************************************************************************
     INTERPRETER (OVERRIDE) METHODS
     ***************************************************************************
     */
    /** 
     * Perform the actual run of the Command 
     * 
     * @param assignment {meta.api.parser.IASTNode} AST Command to Run
     * @throw {String} if Failed to Start for any Reason
     */
    _run: function(assignment) {
      // Setup For Another Run
      this._lhs = null;
      this._phase = 1;

      // Process Expression
      var values = assignment.getValue();
      if (qx.lang.Type.isArray(values) && (values.length === 2)) {
        var lhs = values[0], rhs = values[1];

        // Are Both the LHS and RHS Nodes?
        if (this._isASTNode(lhs) && this._isASTNode(rhs)) { // YES
          // Save the LHS for Later
          this._lhs = lhs;

          switch (rhs.getType()) {
            case 'IDT': // OPTIMIZATION: Handle Identifier HERE instead of through expression
              this._processResultsRHS(this._dereferenceIDT(rhs));
              this._processExpression(lhs);
              break;
            case 'STR': // OPTIMIZATION: Handle These Types of Tokens HERE instead of through expression
            case 'NUM':
            case 'BOL':
            case 'MAP':
              this._processResultsRHS(this._extractConstant(rhs));
              this._processExpression(lhs);
              break;
            default: // Process Complex Expression Asynchronously
              this._reflectIncoming();
              this._processExpression(rhs);
          }

          // Have to Await Results
          return;
        }
      }

      // Should Never Happen
      throw "PARSER ERROR: Invalid Assignment!?? Expecting LHS NODE <- RHS NODE.";
    },
    /** 
     * Test if the Node Provided to the Run is valid 
     * 
     * @param ast {meta.api.parser.IASTNode} AST to Test
     * @throw {String} if not a valid node
     */
    _validRootNode: function(ast) {
      // Is 'ast' a CMD Node?
      if (!this._isASTNode(ast, "AIO")) { // NO
        this._throwNodeException("AIO", ast);
      }
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaRunResultsOK: function(code, message, results) {
      // Is this the Result of Processing the RHS?
      if (this._phase === 1) { // YES
        // Done Processing RHS
        this._phase++;
        this._processExpression(this._lhs);
      } else { // NO : Result of Processing LHS Expression
        this._fireResults(results, message, code);
      }
    },
    _processMetaRunResultsNOK: function(code, message) {
      // Failed to Parse the RHS!!
      this._fireError(message, code);
    },
    /*
     ***************************************************************************
     INTERPRETER METHODS
     ***************************************************************************
     */
    _processExpression: function(expression) {
      // Do we already have a runner?
      if (this.__expression === null) { // YES
        // Process the Expression Asynchronously
        this.__expression = new meta.parser.RunExpression();

        // Pass on the Dependency Injector
        this.__expression.setDI(this.getDI());
        this._mx_meh_attach("run-results", this.__expression);
      }

      var env = this._env;
      var runner = this.__expression;
      setTimeout(function() {
        runner.run(expression, env);
      }, 100);
    },
    _processResultsRHS: function(results) {
      // Set the IO Results for LHS
      this._env['__pipe'] = results;

      // Done Processing RHS
      this._phase++;
    }
  } // SECTION: MEMBERS
});
