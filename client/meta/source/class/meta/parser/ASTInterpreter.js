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
qx.Class.define("meta.parser.ASTInterpreter", {
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
    // Detach Existing Runners
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
    __expression: null,
    /*
     ***************************************************************************
     INTERPRETER (OVERRIDE) METHODS
     ***************************************************************************
     */
    /** 
     * Perform the actual run of the AST 
     * 
     * @param ast {meta.api.parser.IASTNode} AST to Run
     * @throw {String} if Failed to Start for any Reason
     */
    _run: function(ast) {
      this._expressions(ast.getValue());
    },
    /** 
     * Test if the Node Provided to the Run is valid 
     * 
     * @param ast {meta.api.parser.IASTNode} AST to Test
     * @throw {String} if not a valid node
     */
    _validRootNode: function(ast) {
      // Is 'ast' a ROOT Node?
      if (!this._isASTNode(ast, "ROOT")) { // NO
        this._throwNodeException("ROOT", ast);
      }
    },
    /*
     ***************************************************************************
     INTERPRETER METHODS
     ***************************************************************************
     */
    /** 
     * Process all the expressions
     * 
     * @param expressions {meta.api.parser.IASTNode|meta.api.parser.IASTNode[]} AST Expressions Node
     */
    _expressions: function(expressions) {
      // Do we have a single expression?
      if (!qx.lang.Type.isArray(expressions)) { // YES: Convert it to an array for processing
        expressions = [expressions];
      }

      // Execute each expression, one by one
      var expression, type;
      for (var i = 0; i < expressions.length; ++i) {
        expression = expressions[i];
        switch (expression.getType()) {
          case 'IDT': // OPTIMIZATION: Handle Identifier HERE instead of through expression
            this._fireResults(this._dereferenceIDT(expression));
            break;
          case 'STR':
          case 'NUM':
          case 'BOL':
          case 'MAP': // OPTIMIZATION: Handle Identifier HERE instead of through expression
            this._fireResults(this._extractConstant(expression));
            break;
          default:
            this._processExpression(expression);
        }
      }
    },
    /** 
     * Process a Single Expression
     * 
     * @param expression {meta.api.parser.IASTNode} AST Expression Node
     */
    _processExpression: function(expression) {
      // Do we have an existing command?
      if (this.__expression === null) {
        this.__expression = new meta.parser.RunExpression();

        // Pass on the Dependency Injector
        this.__expression.setDI(this.getDI());
        this._mx_meh_attach("run-results", this.__expression);
      }

      // Dispatch the Runner Asynchronously
      var env = this._env;
      var runner = this.__expression;
      setTimeout(function() {
        runner.run(expression, env);
      }, 100);
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaRunResultsOK: function(code, message, results) {
      this._fireResults(results, message, code);
    },
    _processMetaRunResultsNOK: function(code, message) {
      this._fireError(message, code);
    }
  } // SECTION: MEMBERS
});
