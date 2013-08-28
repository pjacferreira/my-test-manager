/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2013 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/* ************************************************************************
 ************************************************************************ */

/**
 * Expression Language Parser
 *
 */
qx.Bootstrap.define("tc.EL.Parser", {
  type: "singleton",
  extend: qx.core.Object,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * 
   */
  construct: function() {
    this.base(arguments);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __input: null,
    __pos: null,
    /**
     * 
     */
    parse: function(input) {
      return this.__parse_expression();
    },
    __parse_expression: function(required) {
      /* expression
       *   = o:OPERATOR WS v:value  { return '** ' + o + ' ' + v + ' **'; }
       *   / ']' WS range WS ']'
       *   / '['  WS (( list WS ']' ) / ( range WS ('[' / ']')))
       */
      var result0 = null, result1;


      // Create a Marker (START)
      var pos0 = qx.lang.Object.clone(this.__pos);

      // PATH 1 : = o:OPERATOR WS v:value  { return '** ' + o + ' ' + v + ' **'; }
      result0 = this.__parse_OPERATOR();
      if (result0 !== null) {
        // Skip Whitespaces
        parse_WS();

        result1 = this.__parse_value(true);
        if (result1 !== null) {
          /* Adding this fake 'comparison' AST, makes the expression object "flat"
           * i.e. the children is never an array, but a direct reference
           */
          return {type: 'expression', children: {
              type: 'comparison', children: [result0, result1]
            }
          };
        }
      }

      // BACK TRACK to (START)
      this.__pos = pos0;
      if (this.__input.charCodeAt(this.__pos.offset) === 93) {
        // PATH 2 : / ']' range (']' | '[')
        this.__advance(this.__pos, 1);

        // Skip Whitespaces
        this.__parse_WS();

        result0 = this.__parse_range(true);

        if (result0 !== null) { // Match Range Close
          // Skip Whitespaces
          this.__parse_WS();

          // Match Range Close
          if ((this.__input.charCodeAt(this.__pos.offset) === 91) || // [
                  (this.__input.charCodeAt(this.__pos.offset) === 93)) { // ]
            result0.start = ']';
            result0.end = this.__input[this.__pos.offset];
            this.__advance(this.__pos, 1); // NOT REALLY REQUIRED as 'expression' is the top level clause and we are exiting
            return {type: 'expression', children: result0};
          }
        }
      } else if (this.__input.charCodeAt(this.__pos.offset) === 91) {
        // PATH 3 : / '['  WS (( list WS ']' ) / ( range WS ('[' / ']')))
        this.__advance(this.__pos, 1);

        // Skip Whitespaces
        this.__parse_WS();

        // Need a Second Marker to Back Track (Trying both List and Range)
        pos0 = qx.lang.Object.clone(this.__pos);

        result0 = this.__parse_list();
        if (result0 !== null) {
          // Skip Whitespaces
          this.__parse_WS();

          if (this.__input.charCodeAt(this.__pos.offset) === 93) {
            this.__advance(this.__pos, 1); // NOT REALLY REQUIRED as 'expression' is the top level clause and we are exiting
            return {type: 'expression', children: result0};
          }
        }

        // BACK TRACK
        this.__pos = pos0;

        result0 = this.__parse_range();
        if (result0 !== null) { // Match Range Close
          // Skip Whitespaces
          this.__parse_WS();

          if ((this.__input.charCodeAt(this.__pos.offset) === 91) || // [
                  (this.__input.charCodeAt(this.__pos.offset) === 93)) { // ]
            result0.start = '[';
            result0.end = this.__input[this.__pos.offset];
            this.__advance(this.__pos, 1); // NOT REALLY REQUIRED as 'expression' is the top level clause and we are exiting
            return {type: 'expression', children: result0};
          }
        }
      }

      if (required) {
        throw "EXPRESSION: <,<=,=>,>,=,! VALUE, RANGE or LIST";
      }
      return null;
    },
    __parse_list: function(required) {
      /* list
       *   = a:value b:( WS ',' WS value)* { return b != null ? a+b.join(""): a}
       */
      var result0, result1;

      result0 = [];
      result1 = this.__parse_value();
      while (result1 !== null) {
        result0.push(result1);

        // Skip Whitespaces
        this.__parse_WS();

        // Look for Next Comma
        if (this.__input.charCodeAt(this.__pos.offset) === 44) {
          this.__advance(this.__pos, 1);

          // Skip Whitespaces
          this.__parse_WS();

          // Get Next Value
          result1 = this.__parse_value();
          if (result1 == null) { // Failed to Parse Value
            result0 = null;
            break;
          }
        } else {
          break;
        }
      }

      if ((result0 === null) && required) {
        throw "LIST: value [, value]*";
      }

      return {type: 'list', children: result0};
    },
    __parse_range: function(required) {
      /* range
       *   = a:value WS .. WS b:value { return a + ' .. ' + b}
       */
      var result1, result2;

      // 1st Value
      result1 = this.__parse_value();

      if (result1 !== null) {
        // Skip Whitespaces
        this.__parse_WS();

        if ((this.__input.charCodeAt(this.__pos.offset) === 46) &&
                (this.__input.charCodeAt(this.__pos.offset + 1) === 46)) {
          this.__advance(pos, 2);

          // Skip Whitespaces
          this.__parse_WS();

          // 2nd Value
          result2 = this.__parse_value();
          if (result2 !== null) { // Failed to Parse Value
            return {type: 'range', children: [result1, result2]};
          }
        }
      }

      if (required) {
        throw "RANGE: value .. value";
      }

      return null;
    },
    __parse_value: function(required) {
      /* value 
       *   = a:SIGN? b:NUMBER { return a != null ? a+b.join("") : a }
       *   / TIME
       *   / STRING
       */
      var result0 = null;

      // MARKER : Starting Point for Match 
      var pos0 = qx.lang.Object.clone(this.__pos);

      // PATH 1 : = a:SIGN? b:NUMBER { return a != null ? a+b.join("") : a }
      result0 = this.__parse_NUMBER();
      if (result0 === null) {
        // BACK TRACK
        this.__pos = qx.lang.Object.clone(pos0);

        // PATH 2 : / TIME
        result0 = this.__parse_TIME();
        if (result0 === null) {
          // BACK TRACK
          this.__pos = pos0; // OPTIMIZATION : No Need to Clone as the Marker will not be Re-Used

          // PATH 3 : / STRING
          result0 = this.__parse_STRING();
        }
      }

      if ((result0 === null) && required) {
        throw "VALUE: NUMBER, TIME or STRING";
      }
      
      return result0 !== null ? {type: 'value', children: result0} : null;
    },
    __parse_OPERATOR: function(required) {
      /* OPERATOR
       *   = a:'<' b:'='? { return b != null ? a+b : a }
       *   / a:'>' b:'='? { return b != null ? a+b : a }
       *   / '=' { return '==' }
       *   / '!' { return '!=' }
       */
      var result0 = null;

      switch (this.__input.charCodeAt(this.__pos.offset)) {
        case 60: // PATH 1 : = a:'<' b:'='? { return b != null ? a+b : a }
          result0 = "<";
          this.__advance(this.__pos, 1);
          if (this.__input.charCodeAt(this.__pos.offset) === 61) {
            result0 = "<=";
            this.__advance(this.__pos, 1);
          }
          break;
        case 62: // PATH 2 : / a:'>' b:'='? { return b != null ? a+b : a }
          result0 = ">";
          this.__advance(this.__pos, 1);
          if (this.__input.charCodeAt(this.__pos.offset) === 61) {
            result0 = ">=";
            this.__advance(this.__pos, 1);
          }
          break;
        case 61: // PATH 3 : / '='
          result0 = "=";
          this.__advance(this.__pos, 1);
          break;
        case 33:         // PATH 4 : / '!'
          result0 = "!";
          this.__advance(this.__pos, 1);
      }

      if ((result0 === null) && required) {
        throw "OPERATOR: <, <=, >, >=, =, !";
      }

      return result0 !== null ? {type: 'OPERATOR', children: result0} : null;
    },
    __parse_STRING: function(required) {
      var result0 = null;
      var start_quote = null;
      var escape_active = false;

      // Starting Position
      var start = this.__pos.offset;
      
      // Match START-OF-STRING Character (QUOTE)
      if ((this.__input.charCodeAt(start) === 34) ||
              (this.__input.charCodeAt(start) === 39)) {
        start_quote = this.__input.charCodeAt(start);
        this.__advance(this.__pos, 1);
      } else {
        if (required) {
          throw "STRING: Missing Start Quote. Expecting \" or \'";
        }
        return null;
      }

      // Consume String Character (Anything except END-OF-STRING Character)
      var next_char = this.__input.charCodeAt(this.__pos.offset);
      while (next_char) {
        if (next_char == 92) { // Escape Character '\'
          // Toggle Escape Character
          escape_active ^= true;
          this.__advance(this.__pos, 1);
        } else if (next_char != start_quote) { // Next Character
          escape_active = false;
          this.__advance(this.__pos, 1);
        } else if (escape_active) {
          this.__advance(this.__pos, 1);
        } else { // Found End Quote
          break;
        }

        // Get the Next Character
        next_char = this.__input.charCodeAt(this.__pos.offset);
      }

      // Match END-OF-STRING Character (QUOTE)
      if (this.__input.charCodeAt(this.__pos.offset) === start_quote) {
        // Found Ending Quote
        advance(this.__pos, 1);
        result0 = this.__input.substring(start, this.__pos.offset);
        return {type: 'STRING', children: result0};
      }

      if (required) {
        throw "STRING: Missing End Quote.";
      }

      return null;
    },
    __parse_TIME: function(required) {
      /* TIME "time"
       *   = '%' [0-9] [0-9]? ':' [0-9] [0-9] (':' [0-9] [0-9])? '%'
       */
      var result0 = /^\%((2[0-3]|([0-1][0-9]))(\:[0-5][0-9]){1,2})\%/.exec(this.__input.substring(this.__pos.offset));

      if (result0 !== null) {
        this.__advance(this.__pos, result0[0].length); // Move Forward to End of Time String
        result0 = result0[1]; // Extract 1st RexExp Group which is just the Time without the '%'
        return {type: 'TIME', children: result0};
      } else if (required) {
        throw "TIME : %HH:MM[:SS]%";
      }

      return null;
    },
    __parse_NUMBER: function(required) {
      /* NUMBER "numeric"
       *   = '.' INTEGER EXPONENT?
       *   / INTEGER ('.' INTEGER?)? EXPONENT?
       */
      var result0 = /^[+-]?((\.\d([eE][+-]?\d+)?)|(\d\.?\d?([eE][+-]?\d+)?))/.exec(this.__input.substring(this.__pos.offset));

      if (result0 !== null) {
        result0 = result0[0];
        this.__advance(this.__pos, result0.length);
        return {type: 'NUMBER', children: result0};
      } else if (required) {
        throw "NUMBER: .INTEGER[EXPONENT] or INTEGER[.[INTEGER][EXPONENT]]";
      }

      return null;
    },
    __parse_WS: function(required) {
      /* WS "white spaces"
       *   = ' '* { return '' }
       */
      var result0;

      // Start of String Marker
      var start = this.__pos.offset;

      while (this.__input.charCodeAt(this.__pos.offset) === 32) {
        this.__advance(this.__pos, 1);
      }

      // Extract Whitespaces Skipped (if any)
      result0 = start < this.__pos.offset ? this.__input.substring(start, this.__pos.offset) : null;

      if ((result0 === null) && required) {
        throw  "WHITE SPACES: \b*";
      }

      return result0 !== null ? {type: 'WS', children: result0} : null;
    },
    __advance: function(pos, n) {
      var endOffset = pos.offset + n;

      for (var offset = pos.offset; offset < endOffset; offset++) {
        var ch = this.__input.charAt(offset);
        if (ch === "\n") {
          if (!pos.seenCR) {
            pos.line++;
          }
          pos.column = 1;
          pos.seenCR = false;
        } else if (ch === "\r" || ch === "\u2028" || ch === "\u2029") {
          pos.line++;
          pos.column = 1;
          pos.seenCR = true;
        } else {
          pos.column++;
          pos.seenCR = false;
        }
      }

      pos.offset += n;
    }
  }
});
  