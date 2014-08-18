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
qx.Class.define("meta.parser.BasicLexer", {
  extend: qx.core.Object,
  implement: meta.api.parser.ILexer,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Connection Constructor
   * 
   * @param lines {String[]|null} New Set of Lines   
   */
  construct: function(lines) {
    // Initialize
    if (lines !== null) {
      this.setLines(lines);
    } else {
      this.__markers = [];
    }
  },
  /**
   *    */
  destruct: function() {
    // Cleanup
    this.__lines = null;
    this.__markers = null;
    this.__current = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __lines: null,
    __currentLine: 0,
    __currentPosition: 0,
    __markers: null,
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
      this.__currentLine = 0;
      this.__currentPosition = 0;
      this.__markers = [];
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
      // Are we at the Beginning of the Input?
      if (this.isBOI()) { // YES
        // Move Cursor Forward
        this._moveNext();
        return this._setCurrentToken(this._newToken('BOI'));
      }

      // Are we at the End of the Input?
      if (this.isEOI()) { // YES
        return this._setCurrentToken(this._newToken('EOI'));
      }

      // Get the Current Char
      var ch = this._currentChar();

      // Is this the End of the Line?
      if (ch === '\n') { // YES
        // Move the Cursor Forward
        this._moveNext();
        return this._setCurrentToken(this._newToken('EOL'));
      } else if (this._isWhitespace(ch)) {
        return this._setCurrentToken(this._extractWhitespaces());
      } else if (this._isAlpha(ch)) { // ELSE: This is an Alpha Character
        return this._setCurrentToken(this._extractIdentifier());
      } else if (this._isQuote(ch)) { // ELSE: This is Quote Character
        return this._setCurrentToken(this._extractString(ch));
      } else if (this._isDigit(ch)) { // ELSE: This ia Digit
        return this._setCurrentToken(this._extractNumber());
      } else {
        var next = this._peekNextChar();
        switch (ch) {
          case '_':
            // Move the Cursor Forward
            this._moveNext();
            return this._isAlpha(next) || this._isDigit(next) ?
              this._setCurrentToken(this._extractIdentifier()) :
              this._setCurrentToken(this._newToken('LIT', '_'));
          case '.':
            // Move the Cursor Forward
            this._moveNext();
            return this._isDigit(next) ?
              this._setCurrentToken(this._extractNumber()) :
              this._setCurrentToken(this._newToken('LIT', '.'));
          case '<':
            // Move the Cursor Forward
            this._moveNext();
            if ((next !== null) && ('>=-'.indexOf(next) >= 0)) {
              // Move the Cursor Forward
              this._moveNext();
              return this._setCurrentToken(this._newToken('LIT', '<' + next));
            }
            return this._setCurrentToken(this._newToken('LIT', '<'));
          case '>':
            // Move the Cursor Forward
            this._moveNext();
            if ((next !== null) && (next === '=')) {
              // Move the Cursor Forward
              this._moveNext();
              return this._setCurrentToken(this._newToken('LIT', '>='));
            }
            return this._setCurrentToken(this._newToken('LIT', '>'));
          default:
            if (';(,){:}=!&|'.indexOf(ch) >= 0) {
              // Move the Cursor Forward
              this._moveNext();
              return this._setCurrentToken(this._newToken('LIT', ch));
            }
            return this._setCurrentToken(this._newToken('UNK', ch));
        }
      }
    },
    /** 
     * Mark the current position in the input stream 
     * 
     * @return {Boolean} 'true' mark inserted, 'false' otherwise
     */
    mark: function() {
      var marker = {
        'line': this.__currentLine,
        'position': this.__currentPosition
      };

      // Save the Marker
      this.__markers.push(marker);
      return marker;
    },
    /** 
     * Drop previously saved marks
     * 
     * @param numOfMarks {Integer|null} Number of marks to drop, 'null' equals drop 1 mark
     * @return {Boolean} 'true' marks removed, 'false' otherwise
     */
    dropMarks: function(numOfMarks) {
      if ((numOfMarks == null) || (numOfMarks === 1)) {
        if (this.__markers.length) {
          return this.__markers.pop();
        }
      } else if (numOfMarks > 0) {
        if (numOfMarks >= this.__markers.length) {
          var slice = this.__markers;
          this.__markers = [];
          return slice;
        } else {
          return this.__markers.splice(this.__markers.length - numOfMarks, numOfMarks);
        }
      }

      return null;
    }, /**
     * Rewind the processing back to the last marker
     * 
     * @returns {Boolean} 'true' position was rewound, 'false' otherwise
     */
    rewind: function() {
      var marker = this.__markers.length ? this.__markers.pop() : null;

      // Do we have a marker?
      if (marker !== null) { // YES
        // Reset Position
        this.__currentLine = marker.line;
        this.__currentPosition = marker.position;
      }

      return marker;
    },
    /**
     * Are we before the Beginning of the 1st Input line?
     * 
     * @returns {Boolean} 'true' YES, 'false' otherwise
     */
    isBOI: function() {
      return this.__currentLine === 0;
    },
    /**
     * Are we past the End of the Last Input Line?
     * 
     * @returns {Boolean} 'true' YES, 'false' otherwise
     */
    isEOI: function() {
      return this.__lines === null ? true : this.__currentLine > this.__lines.length;
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
      return {
        'line': this.__currentLine,
        'position': this.__currentPosition
      };
    },
    /**
     * Return's the current set of input lines.
     * 
     * @returns {String[]|null} Input lines
     */
    getLines: function() {
      return this.__lines;
    },
    /**
     * Return's the current set of input lines.
     * 
     * @param lines {String[]|null} New Set of Lines
     * @returns {String[]|null} Old Set of Lines
     */
    setLines: function(lines) {
      // Are we dealing with a single line?
      if (qx.lang.Type.isString(lines)) { // YES
        lines = utility.String.v_nullOnEmpty(lines, true);
        if (lines !== null) {
          lines = [lines];
        }
      } else if (qx.lang.Type.isArray(lines)) { // NO: Possible Multiple Lines
        lines = utility.Array.clean(utility.Array.trim(lines));
      } else { // NO: Invalid input
        lines = null;
      }

      // Save the Old Lines
      var oldLines = this.__lines;

      // Reset the Lexer Based on the New Input
      this.__lines = lines;
      this.reset();

      return oldLines;
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS (COMPLEX TOKEN EXTRACTORS)
     ***************************************************************************
     */
    _extractIdentifier: function(start) {
      var remainder = this._remainderOfLine();
      var match = /[a-zA-Z_][a-zA-Z0-9_]*/.exec(remainder);
      if (match !== null) {
        this._moveForward(match[0].length);
        return this._newToken('IDT', match[0]);
      }

      return this._newToken('ERR');
    },
    _extractString: function(quote) {
      var remainder = this._remainderOfLine();
      var re = (quote === "'") ? /\'[^\']*\'/ : /\"[^\"]*\"/;
      var match = re.exec(remainder);

      // Did we find a mtach for the Regular Expression?
      if (match !== null) { // YES
        var string = match[0];
        this._moveForward(string.length);
        return {
          type: 'STR',
          token: string.length === 2 ? null : string.slice(1, -1),
          quote: quote
        };
      }

      return this._newToken('ERR');
    },
    _extractNumber: function(start) {
      var remainder = this._remainderOfLine();
      var match = /(\d+(\.\d*)?(e(\+|-)?\d+)?|(\.\d+(e(\+|-)?\d+)?))/.exec(remainder);

      // Did we find a mtach for the Regular Expression?
      if (match !== null) { // YES
        this._moveForward(match[0].length);
        return this._newToken('NUM', match[0]);
      }

      return this._newToken('ERR');
    },
    _extractWhitespaces: function(start) {
      var remainder = this._remainderOfLine();
      var match = /\s+/.exec(remainder);

      // Did we find a mtach for the Regular Expression?
      if (match !== null) { // YES
        this._moveForward(match[0].length);
        return this._newToken('WSP', match[0]);
      }

      return this._newToken('ERR');
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS (CHARACTER TESTERS)
     ***************************************************************************
     */
    _isAlpha: function(ch) {
      return (ch !== null) &&
        (("abcdefghijklmnopqrstuvwxyz".indexOf(ch) >= 0) ||
          ("ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf(ch) >= 0));
    },
    _isDigit: function(ch) {
      return (ch !== null) && ("0123456789".indexOf(ch) >= 0);
    },
    _isWhitespace: function(ch) {
      return /\s/.exec(ch) !== null;
    },
    _isQuote: function(ch) {
      return ("\"'".indexOf(ch) >= 0);
    },
    _isEOL: function() {
      var line = this._currentLine();
      return (line !== null) && (this.__currentPosition > line.length);
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS (HELPERS)
     ***************************************************************************
     */
    _setCurrentToken: function(token) {
      this.__current = token;
      return this.__current;
    },
    _newToken: function(type, value) {
      return {'type': type, 'token': (value == null ? null : value)};
    },
    _currentLine: function() {
      return (this.__lines === null) || (this.__currentLine < 1) ? null : this.__lines[this.__currentLine - 1];
    },
    _currentChar: function() {
      // Are we at the Beginning or the End of the File
      var line = this._currentLine();
      if (line === null) { // YES
        return null;
      } else { // NO
        // Are we at the End of the Line?
        if (this.__currentPosition > line.length) { // YES
          return '\n';
        } else { // NO
          return line[this.__currentPosition - 1];
        }
      }
    },
    _peekNextChar: function() {
      // Mark Current Position
      this.mark();
      // Get the next character
      var hasNext = this._moveNext();
      var ch = hasNext ? this._currentChar() : null;

      // Rewind to previous position
      this.rewind();
      return ch;
    },
    _moveNext: function() {
      // Are we past the end-of-then input?
      if (this.isEOI()) { // YES
        return false;
      }
      // ELSE: NO - Move Forward One Position
      if (this.__currentLine === 0) {
        this.__currentLine = 1;
        this.__currentPosition = 1;
      } else {
        this.__currentPosition++;
      }

      // Are we past the EOL?
      var line = this._currentLine();
      if (this.__currentPosition > (line.length + 1)) { // YES
        // Move to the Start of the Next Line
        this.__currentLine++;
        this.__currentPosition = 0;
      }
      return true;
    },
    _movePrevious: function() {
      // Are we at the start of the input?
      if (this.isBOI()) { // YES
        return false;
      }
      // ELSE: NO - Backup one Position
      --this.__currentPosition;

      // Are we before the start of the line?
      if (this.__currentPosition < 1) { // YES
        // Backup one line
        --this.__currentLine;

        // Are we before the beginning of the input?
        if (this.__currentLine < 1) { // YES
          this.__currentLine = 0;
          this.__currentPosition = 0;
        } else { // NO
          // Position the Cursor 1 Character Past End of the Line (i.e. at the EOL Marker)
          this.__currentPosition = this._currentLine().length;
        }
      }
      return true;
    },
    _moveForward: function(characters) {
      // Get the Current Line
      var line = this._currentLine();

      // Loop until we reach the end of the lines or have no more characters to move forward
      while ((line !== null) && (characters > 0)) {
        // Are we moving past the End of the Line?
        // TODO: Verify the Math
        if (characters > (line.length + 1 - this.__currentPosition)) { // YES
          // Remove Remaining Characters
          characters -= (line.length + 1 - this.__currentPosition);

          // Move Forward One Line
          ++this.__currentLine;
          this.__currentPosition = 0;
          line = this._currentLine();
        } else { // NO : We are positioning somewher in the current line
          this.__currentPosition += characters;
          characters = 0;
        }
      }
    },
    _remainderOfLine: function() {
      var line = this._currentLine();
      var position = this.__currentPosition - 1;
      return (position >= line.length) ? null : line.slice(position);
    }
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
  } // SECTION: MEMBERS
});
