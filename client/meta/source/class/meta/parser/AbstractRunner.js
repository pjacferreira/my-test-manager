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
qx.Class.define("meta.parser.AbstractRunner", {
  extend: qx.core.Object,
  type: "abstract",
  implement: [
    meta.api.parser.IRunner,
    utility.api.di.IInjectable
  ],
  include: [
    meta.events.mixins.MMetaEventDispatcher,
    utility.mixins.di.MInjectable
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
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Destructor to Cleanup
   */
  destruct: function() {
    // Cleanup
    this._env = null;
    this._incoming = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _env: null,
    _incoming: null,
    /*
     ***************************************************************************
     METHODS 
     ***************************************************************************
     */
    /** 
     * Run AST 
     * 
     * @param ast {meta.api.parser.IASTNode} AST to Run
     * @param env {Map|null} Enviroment Properties
     * @throw {String} if Failed to Start for any Reason (After Starting only 
     *   events will be fired in case of failure)
     */
    run: function(ast, env) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue(this._isASTNode(ast), "[ast] Is not of the expected type!");
      }

      try {
        // Test if the node is valid to start with it 
        this._validRootNode(ast);

        // Save Enviroment
        this._env = qx.lang.Type.isObject(env) ? env : {};

        // Do we have an Piped Results from Previous Command?
        if (this._env.hasOwnProperty('__pipe')) { // YES
          // Save the Results Internally
          this._incoming = this._env.__pipe;
          // Remove the Piped Results
          delete this._env.__pipe;
        } else { // NO
          this._incoming = null;
        }

        // Run the AST
        this._run(ast);
      } catch (e) { // Dispatch Error
        this._mx_med_fireEventNOK("run-results", null, qx.lang.Type.isString(e) ? e : e.toString());
      }
    },
    /*
     ***************************************************************************
     INTERPRETER (OVERRIDE) METHODS
     ***************************************************************************
     */
    /** 
     * Perform the actual run of the AST 
     * 
     * @abstract
     * @param ast {meta.api.parser.IASTNode} AST to Run
     * @throw {String} if Failed to Start for any Reason
     */
    _run: function(ast) {
    },
    /** 
     * Test if the Node Provided to the Run is valid 
     * 
     * @abstract
     * @param ast {meta.api.parser.IASTNode} AST to Test
     * @throw {String} if not a valid node
     */
    _validRootNode: function(ast) {
    },
    /*
     ***************************************************************************
     PROTECTED HELPER FUNCTIONS
     ***************************************************************************
     */
    _dereferenceIDT: function(node) {
      var token = node.getValue();
      // Is this an Identifier Token?
      if (this._isASTToken(token, 'IDT')) { // YES
        return this._extractIDTValue(token.token);
      } else {
        this._throwNodeException('IDT', token);
      }
    },
    _extractIDTValue: function(identifier) {
      // Is the IDT set in the enviroment?
      if (this._env.hasOwnProperty(identifier)) { // YES
        return this._env[identifier];
      }

      throw "[" + identifier + "] has not been set.";
    },
    _extractConstant: function(node) {
      var token = node.getValue();

      // Is the Value a Token?
      if (this._isASTToken(token)) { // YES
        return token.token;
      }

      throw "Expecting Constant Value. Found [" + token + "]";
    },
    _isASTNode: function(node, type) {
      // Are we dealing with an AST Node?
      if (qx.lang.Type.isObject(node) &&
        qx.lang.Type.isFunction(node.getType)) { // YES

        // IF we a type was provided, make sure it matches as well
        return type != null ? node.getType() === type : true;
      }

      return false;
    },
    _isASTToken: function(token, type) {
      if (qx.lang.Type.isObject(token) &&
        token.hasOwnProperty('type') &&
        token.hasOwnProperty('token')) {
        return type != null ? token.type === type : true;
      }
      return false;
    },
    _throwNodeException: function(expecting, found) {
      if (qx.lang.Type.isArray(expecting)) {
        expecting = expecting.join('|');
      }
      throw "Expecting one of [" + expecting + "] found [" + found.toString() + "]";
    },
    _fireResults: function(results, message, code) {
      if (results !== null) {
        // Set the Input for the Next Command in the Chain
        this._env['__pipe'] = results;
        // Fire the Notification (With Results)
        /* NOTE: We encapsulate the "output" in an array so that it's treated as
         * single parameter by the meta-event dispatcher
         */
        this._mx_med_fireEventOK("run-results", [results], message, code);
      } else {
        // Fire the Notification (No Results)
        this._mx_med_fireEventOK("run-results", null, message, code);
      }
    },
    _fireError: function(message, code) {
      this._mx_med_fireEventNOK("run-results", null, message, code);
    },
    _reflectIncoming: function() {
      if (this._incoming !== null) {
        // Set the Current Incoming Pipe Values for the Next Command in the Chain
        this._env['__pipe'] = this._incoming;
      }
    }
  } // SECTION: MEMBERS
});
