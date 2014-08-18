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
qx.Class.define("meta.parser.CommandLexer", {
  extend: qx.core.Object,
  implement: meta.api.parser.ILexer,
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** Flag: Skip Whitespaces */
    "skipWS": {
      init: true,
      check: "Boolean"
    },
    /** Flag: Skip End-Of-Lines */
    "skipEOL": {
      init: false,
      check: "Boolean"
    }
  }, // SECTION: PROPERTIES
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Build Command Lexer
   * 
   * @param lexer {meta.api.parser.ILexer} Nested Lexer that does all the heavy lifting 
   * @param lines {String|String[]|null} New Set of Lines   
   */
  construct: function(lexer, lines) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(lexer, meta.api.parser.ILexer, "[lexer] Is not of the expected type!");
    }

    this.__lexer = lexer;
    this.__lexer.setLines(lines);
  },
  /**
   *
   */
  destruct: function() {
    // Cleanup
    this.__lexer = null;
    this.__current = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __lexer: null,
    __current: null,
    /*
     ***************************************************************************
     METHODS (meta.api.parser.ILexer)
     ***************************************************************************
     */
    /** 
     * Reset the Lexer. Repositions the cursor at the Beggining of Lines and 
     * removes and clears all the markers.
     */
    reset: function() {
      this.__lexer.reset();
    },
    /** 
     * Get the Current Token
     * 
     * @return {Map} Current Token (i.e. the result of the last next() call) or 
     *   'null' if next not called
     */
    current: function() {
      return this.__current;
    },
    /** 
     * Get the Next Token
     * 
     * @return {Map} Next Token or 'null' if nothing left
     */
    next: function() {
      var skipWS = this.getSkipWS();
      var skipEOL = this.getSkipEOL();

      // Get Next Token
      var next = this.__lexer.next();
      // Is this the Beginning of Input Token?
      if (this._isToken(next, 'BOI')) { // YES
        // Skip Over
        next = this.__lexer.next();
      }

      // Do we need to skip over any tokens?
      if (skipWS || skipEOL) { // YES
        while (!this._isToken(next, 'EOI')) {
          // Is the next token a White Space?
          if (this._isToken(next, 'WSP')) { // YES
            // Are we supposed to skip Whitespaces?
            if (!skipWS) { // NO
              break;
            }
          } else if (this._isToken(next, 'EOL')) { // ELSE: This is an EOL Token
            // Are we supposed to skip End-Of-Lines?
            if (!skipEOL) { // NO
              break;
            }
          } else { // ELSE: This is some other token
            break;
          }

          // Skip to the Next Token
          next = this.__lexer.next();
        }
      }

      // Did we find the End-Of-Input Token
      if (!this._isToken(next, 'EOI')) { // NO
        if (this._isToken(next, 'IDT')) {
          // Handle Special Identifiers
          switch (next.token) {
            case 'create':
            case 'display':
            case 'execute':
            case 'with': // Command Names
              next.type = 'CMD';
              break;
            case 'do':
            case 'service':
            case 'connected':
            case 'form': // Reserved Words
              next.type = 'RSV';
              break;
          }
        }
      }

      this.__current = next;
      return next;
    },
    /** 
     * Mark the current position in the input stream 
     * 
     * @return {Boolean} 'true' mark inserted, 'false' otherwise
     */
    mark: function() {
      return this.__lexer.mark();
    },
    /** 
     * Drop previously saved marks
     * 
     * @param numOfMarks {Integer|null} Number of marks to drop, 'null' equals drop 1 mark
     * @return {Boolean} 'true' marks removed, 'false' otherwise
     */
    dropMarks: function(numOfMarks) {
      return this.__lexer.dropMarks(numOfMarks);
    },
    /**
     * Rewind the processing back to the last marker
     * 
     * @returns {Boolean} 'true' position was rewound, 'false' otherwise
     */
    rewind: function() {
      return this.__lexer.rewind();
    },
    /**
     * Are we before the Beginning of the 1st Input line?
     * 
     * @returns {Boolean} 'true' YES, 'false' otherwise
     */
    isBOI: function() {
      return this.__lexer.isBOI();
    },
    /**
     * Are we past the End of the Last Input Line?
     * 
     * @returns {Boolean} 'true' YES, 'false' otherwise
     */
    isEOI: function() {
      return this.__lexer.isEOI();
    },
    /**
     * Get the current position in the input.
     * Returns an Object 
     * {
     *   'line'     : current line
     *   'position' : current position in the current line
     * }
     * 
     * @returns {Map} Current Position
     */
    getPosition: function() {
      return this.__lexer.getPosition();
    },
    /**
     * Return's the current set of input lines.
     * 
     * @returns {String[]|null} Input lines
     */
    getLines: function() {
      return this.__lexer.getLines();
    },
    /**
     * Return's the current set of input lines.
     * 
     * @param lines {String[]|null} New Set of Lines
     * @returns {String[]|null} Old Set of Lines
     */
    setLines: function(lines) {
      return this.__lexer.setLines(lines);
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    /**
     * Test if the token matches the given type.
     * 
     * @param token {Map} Token to test
     * @param type {String} Type to test for
     * @returns {Boolean} 'TRUE' type matches, 'FALSE' otherwise
     */
    _isToken: function(token, type) {
      return token.hasOwnProperty('type') && (token.type === type);
    }
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
  } // SECTION: MEMBERS
});
