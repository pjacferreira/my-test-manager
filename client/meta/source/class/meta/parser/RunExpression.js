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
qx.Class.define("meta.parser.RunExpression", {
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
    if (this.__command !== null) {
      this._mx_meh_detach("run-results", this.__command);
      this.__command = null;
    }
    if (this.__assignment !== null) {
      this._mx_meh_detach("run-results", this.__assignment);
      this.__assignment = null;
    }
    if (this.__io !== null) {
      this._mx_meh_detach("run-results", this.__io);
      this.__io = null;
    }
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __command: null,
    __assignment: null,
    __io: null,
    /*
     ***************************************************************************
     INTERPRETER (OVERRIDE) METHODS
     ***************************************************************************
     */
    /** 
     * Process the expression
     * 
     * @param expression {meta.api.parser.IASTNode} AST Node to process
     * @throw {String} if Failed to Start for any Reason
     */
    _run: function(expression) {
      switch (expression.getType()) {
        case 'CMD':
          this._reflectIncoming();
          this._runCommand(expression);
          break;
        case 'ASS':
          this._reflectIncoming();
          this._processAssignment(expression);
          break;
        case 'AIO':
          this._reflectIncoming();
          this._processIO(expression);
          break;
        case 'IDT':
          this._fireResults(this._dereferenceIDT(expression));
          break;
        case 'STR':
        case 'NUM':
        case 'BOL':
        case 'MAP':
          this._fireResults(this._extractConstant(expression));
          break;
        default:
          throw "SYSTEM ERROR: Unexpected AST Node [" + expression.getType() + "]";
      }
    },
    /** 
     * Test if the Node Provided to the Run is valid 
     * 
     * @param ast {meta.api.parser.IASTNode} AST to Test
     * @throw {String} if not a valid node
     */
    _validRootNode: function(ast) {
      var permitted = ['CMD', 'ASS', 'AIO', 'IDT', 'STR', 'NUM', 'BOL', 'MAP'];

      // Is the AST Node in the set of Permitted AST Node Types?
      if (permitted.indexOf(ast.getType()) < 0) { // NO
        this._throwNodeException(['CMD', 'ASS', 'AIO', 'IDT', 'STR', 'NUM', 'BOL', 'MAP'], ast);
      }
    },
    /*
     ***************************************************************************
     INTERPRETER METHODS
     ***************************************************************************
     */
    /** 
     * Run a Single Command
     * 
     * @param command {meta.api.parser.IASTNode} AST Command Node
     */
    _runCommand: function(command) {
      // Do we have an existing command?
      if (this.__command === null) {
        this.__command = new meta.parser.RunCommand();

        // Pass on the Dependency Injector
        this.__command.setDI(this.getDI());
        this._mx_meh_attach("run-results", this.__command);
      }

      // Dispatch the Runner Asynchronously
      var runner = this.__command;
      var env = this._env;
      setTimeout(function() {
        runner.run(command, env);
      }, 100);
    },
    /** 
     * Process a Single Assignment
     * 
     * @param assignment {meta.api.parser.IASTNode} AST Assignment Node
     */
    _processAssignment: function(assignment) {
      // Do we have an existing command?
      if (this.__assignment === null) {
        this.__assignment = new meta.parser.RunAssignment();

        // Pass on the Dependency Injector
        this.__assignment.setDI(this.getDI());
        this._mx_meh_attach("run-results", this.__assignment);
      }

      // Dispatch the Runner Asynchronously
      var runner = this.__assignment;
      var env = this._env;
      setTimeout(function() {
        runner.run(assignment, env);
      }, 100);
    },
    /** 
     * Process an IO Command
     * 
     * @param io {meta.api.parser.IASTNode} AST Assignment Node
     */
    _processIO: function(io) {
      if (this.__io === null) {
        this.__io = new meta.parser.RunIO();

        // Pass on the Dependency Injector
        this.__io.setDI(this.getDI());
        this._mx_meh_attach("run-results", this.__io);
      }

      // Dispatch the Runner Asynchronously
      var env = this._env;
      var runner = this.__io;
      setTimeout(function() {
        runner.run(io, env);
      }, 100);
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaRunResultsOK: function(code, message, results) {
      // Fire Notification
      this._fireResults(results, message, code);
    },
    _processMetaRunResultsNOK: function(code, message) {
      this._fireError(message,code);
    }
  } // SECTION: MEMBERS
});
