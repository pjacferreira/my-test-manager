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

qx.Interface.define("meta.api.parser.ILexer", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /** 
     * Reset the Lexer. Repositions the cursor at the Beggining of Lines and 
     * removes and clears all the markers.
     * 
     * @abstract
     */
    reset: function() {
    },
    /** 
     * Get the Current Token
     * 
     * @abstract
     * @return {Map} Current Token (i.e. the result of the last next() call) or 
     *   'null' if next not called
     */
    current: function() {
    },
    /** 
     * Get the Next Token
     * 
     * @abstract
     * @return {Map} Next Token or 'null' if nothing left
     */
    next: function() {
    },
    /** 
     * Mark the current position in the input stream 
     * 
     * @abstract
     * @return {Boolean} 'true' mark inserted, 'false' otherwise
     */
    mark: function() {
    },
    /** 
     * Drop previously saved marks
     * 
     * @abstract
     * @param numOfMarks {Integer|null} Number of marks to drop, 'null' equals drop 1 mark
     * @return {Boolean} 'true' marks removed, 'false' otherwise
     */
    dropMarks: function(numOfMarks) {
    },
    /**
     * Rewind the processing back to the last marker
     * 
     * @abstract
     * @returns {Boolean} 'true' position was rewound, 'false' otherwise
     */
    rewind: function() {
    },
    /**
     * Are we before the Beginning of the 1st Input line?
     * 
     * @abstract
     * @returns {Boolean} 'true' YES, 'false' otherwise
     */
    isBOI: function() {
    },
    /**
     * Are we past the End of the Last Input Line?
     * 
     * @abstract
     * @returns {Boolean} 'true' YES, 'false' otherwise
     */
    isEOI: function() {
    },
    /**
     * Get the current position in the input.
     * Returns an Object 
     * {
     *   'line'     : current line
     *   'position' : current position in the current line
     * }
     * 
     * @abstract
     * @returns {Map} Current Position
     */
    getPosition: function() {
    },
    /**
     * Return's the current set of input lines.
     * 
     * @abstract
     * @returns {String[]|null} Input lines
     */
    getLines: function() {
    },
    /**
     * Return's the current set of input lines.
     * 
     * @abstract
     * @param lines {String[]|null} New Set of Lines
     * @returns {String[]|null} Old Set of Lines
     */
    setLines: function(lines) {
    }
  }
});
