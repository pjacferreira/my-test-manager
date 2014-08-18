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
qx.Class.define("meta.parser.Parser", {
  extend: qx.core.Object,
  implement: meta.api.parser.IParser,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Build Command Parser
   * 
   * @param lexer {meta.api.parser.ILexer} Nested Lexer that does all the heavy lifting 
   * @param lines {String|String[]|null} New Set of Lines   
   */
  construct: function(lexer, lines) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(lexer, meta.api.parser.ILexer, "[lexer] Is not of the expected type!");
    }

    // Initialize Variables
    this.__lexer = lexer;
    // Dis we receive input lines?
    if (lines != null) { // YES
      this.setLines(lines);
    }
  },
  /**
   *    */
  destruct: function() {
    // Cleanup
    this.__lexer = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __lexer: null,
    /*
     ***************************************************************************
     METHODS (meta.api.parser.IParser)
     ***************************************************************************
     */
    /** 
     * Reset the Parser so it can (re)run the existing inputs
     */
    reset: function() {
      this.__lexer.reset();
    },
    /**
     * Set the current input lines.
     * 
     * @param lines {String[]|null} New Set of Lines
     * @returns {String[]|null} Old Set of Lines
     */
    setLines: function(lines) {
      // Did we receive no input?
      if ((lines === null) || (lines.length === 0)) { // YES
        // Then fake the lexer with an empty expression
        lines = [';'];
      }
      if (lines.length === 1) { // NO: We have a single line
        /* In order to allow for lazy users (i.e. users that for a single line
         * don't terminate with a ';' we add it, if it doesn't already exist)
         */
        lines = utility.String.v_nullOnEmpty(lines[0], true);
        // Do we have a non empty line?
        if (lines === null) { // NO
          lines[';'];
        } else {  // YES
          // Is it terminated by a ';'
          if (lines[lines.length - 1] !== ';') { // NO
            lines += ';';
          }

          lines = [lines];
        }
      }

      return this.__lexer.setLines(lines);
    },
    /** 
     * Parse the input lines
     * 
     * @return {meta.api.parser.IASTNode} AST Root Element
     * @throw {String} Parser Exception
     */
    parse: function() {
      // Make sure we are starting clean
      this.reset();

      // Get 1st Token
      var token = this.__lexer.next();

      // Are we at the Beginning of the Input?
      if (this._isToken(token, 'BOI')) { // YES: Just Skip it
        token = this.__lexer.next();
      }

      // Loop until we reach the End-Of-Input or Find a Parse Error
      var expressions = [];

      /* EBNF 
       * commands ::= (expression? ';')*
       */
      var expressions = [], expression = null;
      while (!this._isToken(token, 'EOI')) {

        // Is the next expression empty?
        if (this._isToken(token, 'LIT', ';')) { // YES
          // Just Skip it
          token = this.__lexer.next();
          continue;
        }

        // Try to parse an expression
        expression = this._parseExpression(token);
        expressions.push(expression);

        // Get the Current Token (All Expressions Lexer Pointing at the Last Unprocessed Token)
        token = this.__lexer.current();

        // Is the next Token a SEMI-COLON
        if (!this._isToken(token, 'LIT', ';')) { // NO
          // We have a problem, all expressions must be terminated by semi-colons...
          throw this._throwException(';', token);
        } else { // YES: Skip it
          token = this.__lexer.next();
        }
      }

      // Create Root AST Node (represent commands list)
      return this.__astNode('ROOT', expressions.length === 0 ? null : expressions);
    },
    /*
     ***************************************************************************
     PARSE METHODS
     ***************************************************************************
     */
    /** 
     * Parse a Single Expression
     * 
     * expression ::= command
     *              | id 
     *              | id '=' expression
     *              | command '<-' expression
     * 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for Expression
     * @throw {String} Parser Exception
     */
    _parseExpression: function(token) {
      var current, next;

      switch (token.type) {
        case 'CMD': // Start of a Command Expression
          var command = this._parseCommand(token);
          // Is this the start of an IO Command?
          current = this.__lexer.current();
          if (this._isToken(current, 'LIT', '<-')) { // YES
            // Skip over the token
            next = this.__lexer.next();
            return this.__astNode('AIO', [
              command,
              this._parseExpression(next)
            ]);
          } else {
            return command;
          }
        case 'IDT': // Found Identifier (2 Options Available)
          this.__lexer.mark();
          next = this.__lexer.next();
          // Is this an Assignment Expression
          if (this._isToken(next, 'LIT', '=')) { // YES
            this.__lexer.dropMarks();
            return this.__astNode('ASS', [
              this.__astNode('IDT', token),
              this._parseAssignment(token)
            ]);
          } else { // NO: We presume it's a simple Variable Output Expression
            this.__lexer.rewind();
            return this.__astNode('IDT', token);
          }
        case 'STR':
          this.__lexer.next();  // Move to the Next Token
          return this.__astNode('STR', token);
        case 'NUM':
          this.__lexer.next();  // Move to the Next Token
          return this.__astNode('NUM', token);
        case 'BOL':
          this.__lexer.next();  // Move to the Next Token
          return this.__astNode('BOL', token);
        case 'MAP':
          this.__lexer.next();  // Move to the Next Token
          return this.__astNode('MAP', token);
        default:
          throw this._throwException(['CMD', 'IDT', 'STR', 'NUM', 'BOL', 'MAP'], token);
      }
    },
    /** 
     * Parse a Single Expression
     * 
     * assignment ::= id '=' expression
     * 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for Expression
     * @throw {String} Parser Exception
     */
    _parseAssignment: function(token) {
      return this._parseExpression(this.__lexer.next());
    },
    /** 
     * Parse a Single Command
     * 
     * command  ::= 'create' create_command
     *            | 'execute' execute_command
     *            | 'with' with_command 
     *      
     * @param token {Map} Token for Command to Parse
     * @return {meta.api.parser.IASTNode} AST for Command
     * @throw {String} Parser Exception
     */
    _parseCommand: function(token) {
      return this._dispatchCommand(token.token);
    },
    /** 
     * Parse a 'create' Command
     * 
     * create_command ::= 'create' 'service' (id | string)
     *                 |  'create' 'connected'? 'form' (id | string)
     * 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for the Command
     * @throw {String} Parser Exception
     */
    _parseCommandCreate: function(token) {
      // Is the next token a reserved word?
      if (this._isToken(token, 'RSV')) { // YES
        var connected = false;
        switch (token.token) {
          case 'service':
            token = this.__lexer.next();
            return this._parseCommandCreateService(token);
            break;
          case 'form':
            token = this.__lexer.next();
            return this._parseCommandCreateForm(token, connected);
            break;
        }
      }

      throw this._throwException(['service', 'connected', 'form'], token);
    },
    /** 
     * Parse sub-command 'create service'
     * 
     * create_command ::= 'create' 'service' (id | string)
     * 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for the Command
     * @throw {String} Parser Exception
     */
    _parseCommandCreateService: function(token) {
      // Did we find a possible service id?
      if (this._isToken(token, 'IDT') || this._isToken(token, 'STR')) { // YES
        // Consume Token
        this.__lexer.next();
        return this.__astNode('CMD', ['create', 'service', token]);
      }
      // ELSE: NO
      throw this._throwException(['IDT', 'STR'], token);
    },
    /** 
     * Parse sub-command 'create form'
     * 
     * create_command ::= 'create' 'connected'? 'form' (id | string)
     * 
     * @param token {Map} Next Command's Token
     * @param connected {Boolean|false} Do we want a connected form?
     * @return {meta.api.parser.IASTNode} AST for the Command
     * @throw {String} Parser Exception
     */
    _parseCommandCreateForm: function(token, connected) {
      // Did we find a possible form id?
      if (this._isToken(token, 'IDT') || this._isToken(token, 'STR')) { // YES
        // Consume Token
        this.__lexer.next();
        return this.__astNode('CMD', ['create', 'form', [token, !!connected]]);
      }
      // ELSE: NO
      throw this._throwException(['IDT', 'STR'], token);
    },
    /** 
     * Parse a 'display' Command
     * 
     * execute_command ::= 'display' 'form' (id | string) ( service_aliases )
     * service_aliases ::= identifier ( ',' identifier )*
     *                 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for the Command
     * @throw {String} Parser Exception
     */
    _parseCommandDisplay: function(token) {
      // Is the next token a reserved word?
      if (!this._isToken(token, 'RSV', 'form')) { // NO
        throw this._throwException('form', token);
      }

      // Did we find a possible form id?
      var form = this.__internalIdentifier(this.__lexer.next());
      if (form === null) { // NO
        throw this._throwException(['IDT', 'STR'], token);
      }

      // Next Token can OPTIONAL be (Service Alias List)
      var allowed = null;

      // Move Forward
      token = this.__lexer.next();

      // Did we find a Service Key?
      if (this._isToken(token, 'LIT', '(')) { // YES
        allowed = this._parseParameterList(token);
        token = this.__lexer.current();
      }

      return this.__astNode('CMD', ['display', 'form', [form, allowed]]);
    },
    /** 
     * Parse a 'execute' Command
     * 
     * execute_command ::= 'execute' (id | string) key? map?
     *                 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for the Command
     * @throw {String} Parser Exception
     */
    _parseCommandExecute: function(token) {
      // Is the next token a reserved word?
      if (!this._isToken(token, 'RSV', 'service')) { // NO
        throw this._throwException('service', token);
      }

      // Did we find a possible service id?
      token = this.__lexer.next();
      var service = this.__internalIdentifier(token);
      if (service === null) { // NO
        throw this._throwException(['IDT', 'STR'], token);
      }
      // ELSE: YES
      var service = token;

      // Next Token can be OPTIONAL KEY (Parameter List) or PARAMETER Map
      var key = null;
      var parameters = null;

      // Move Forward
      token = this.__lexer.next();

      // Did we find a Positional Key?
      if (this._isToken(token, 'LIT', '(')) { // YES
        key = this._parseParameterList(token);
        token = this.__lexer.current();
      }

      // Did we find a Parameter Map?
      if (this._isToken(token, 'LIT', '{')) { // YES
        parameters = this._parseObject(token);
      }

      return this.__astNode('CMD', ['execute', 'service', [service, key, parameters]]);
    },
    /** 
     * Parse a 'with' Command
     * 
     * execute_command ::= 'with' (id | string) key? 'do' (id | string) map?
     * 
     * @param token {Map} Next Command's Token
     * @return {meta.api.parser.IASTNode} AST for 'with' Command
     * @throw {String} Parser Exception
     */
    _parseCommandWith: function(token) {
      // Did we find the entity name?
      var entity = this.__internalIdentifier(token);
      if (entity === null) { // NO
        throw this._throwException(['IDT', 'STR'], token);
      }

      // Next Token can be OPTIONAL KEY (Parameter List or Map)  or the REQUIRED 'do'
      var key = null;
      token = this.__lexer.next();
      // Did we find a key?
      if (this._isToken(token, 'LIT', '(')) { // YES: and it is a Positional Key
        key = this._parseParameterList(token);
        token = this.__lexer.current();
      } else if (this._isToken(token, 'LIT', '{')) { // YES: and it is an Map Key
        key = this._parseObject(token);
        token = this.__lexer.current();
      }
      // ELSE: No Key Found

      // Create AST Node (for Entity)
      var entityNode = new meta.parser.ASTBasicNode('ENT', [entity, key]);

      // Did we find the 'do'?
      if (!this._isToken(token, 'RSV', 'do')) { // NO
        // We have a problem
        throw this._throwException('do', token);
      }

      // Next Token should be the action name
      token = this.__lexer.next();

      // Did we find the action name?
      var action = this.__internalIdentifier(token);
      if (action === null) { // NO
        throw this._throwException(['IDT', 'STR'], token);
      }

      // Next Token is OPTIONAL Parameters (OBJECT)
      token = this.__lexer.next();

      // Did we find a Parameter Map?
      var parameters = null;
      if (this._isToken(token, 'LIT', '{')) { // YES
        parameters = this._parseObject(token);
      }

      // Create AST Node (for Action)
      var actionNode = this.__astNode('ACT', [action, parameters]);

      // Create the AST
      return this.__astNode('CMD', ['with', entityNode, actionNode]);
    },
    /** 
     * Parse an Expected Parameter List
     * 
     * @param token {Map} Parameter's List Start Token
     * @return {meta.api.parser.IASTNode} AST for Parameter List
     * @throw {String} Parser Exception
     */
    _parseParameterList: function(token) {
      /* TODO: Correct BNF so it's able to parse correctly this example (a,b,,)
       * should produce 4 parameters = [a,b,null,null]
       */

      /* BNF 
       * parameters = '(' parameter-list? ')'
       * parameter-list = parameter ( ',' parameter-list )*
       *                | ',' parameter-list*
       * parameter = identifier | string | number
       */
      var parameters = [];

      // Next Token should be the entity name
      var token = this.__lexer.next();
      var lastComma = false;
      while (!this._isToken(token, 'LIT', ')')) {
        // Was the last Token a Comma?
        lastComma = false;

        // Extract Next Parameter's Value
        switch (token.type) {
          case 'IDT':
          case 'STR':
          case 'NUM':
            parameters.push(token);
            // Move ON
            token = this.__lexer.next();
            break;
          case 'LIT':
            if ((token.token === ',')) {
              parameters.push(this._newToken('STR', null));
              break;
            }
          default: // Unexpected Token
            throw this._throwException(['IDT', 'STR', 'NUM', ','], token);
        }

        // Get Next Token: Expecting ','
        if (this._isToken(token, 'LIT', ',')) { // FOUND ','
          // Last Token was a Comma
          lastComma = true;
          // Move ON
          token = this.__lexer.next();
        } else if (!this._isToken(token, 'LIT', ')')) { // Unexpected Token
          throw this._throwException([',', ')'], token);
        }
      }
      // Consume Trailing ')'
      this.__lexer.next();

      // Was the Last Token Seen (Before the ')') a Comma?
      if (lastComma) { // YES: Then we have a null parameter to add
        parameters.push(this._newToken('STR', null));
      }

      // Create AST : Parameter List -> parameters
      return this.__astNode('PLS', parameters.length === 0 ? null : parameters);
    },
    /** 
     * Parse an Expected Map Object
     * 
     * @param token {Map} Map's List Start Token
     * @return {meta.api.parser.IASTNode} AST for Map Object
     * @throw {String} Parser Exception
     */
    _parseObject: function(token) {
      /* BNF 
       * map = '{' tuples? '}'
       * tuples = tuple ( ',' tuple )*
       * tuple = (identifier|string) ':' (identifier|string|number)?
       * 
       * NOTE: the '? at the end of the tuple definition (which allows for the
       *   null)
       */
      var tuples = new utility.Map();

      // Next Token should be the entity name
      var property = null;
      var token = this.__lexer.next();
      while (!this._isToken(token, 'LIT', '}')) {

        // Expecting: IDT | STR
        switch (token.type) {
          case 'IDT':
          case 'STR':
            property = token.token;
            // Move on
            token = this.__lexer.next();
            break;
          default: // Unexpected Token
            throw this._throwException(['IDT', 'STR'], token);
        }

        // Expecting: ':'
        if (this._isToken(token, 'LIT', ':')) { // Found
          // Move ON
          token = this.__lexer.next();
        } else { // NOT Found
          throw this._throwException(':', token);
        }

        // Expecting: 'IDT' | 'STR' | 'NUM' possibly ','
        switch (token.type) {
          case 'IDT':
          case 'STR':
          case 'NUM':
            tuples.add(property, token);
            // Move ON
            token = this.__lexer.next();
            break;
          case 'LIT':
            if ((token.token === ',') || (token.token === '}')) {
              tuples.add(property, this._newToken('STR', null));
              break;
            }
          default: // Unexpected Token
            throw this._throwException(['IDT', 'STR', 'NUM', ',', '}'], token);
        }

        // Get Next Token: Expecting ','
        if (this._isToken(token, 'LIT', ',')) { // FOUND ','
          // Move ON
          token = this.__lexer.next();
        } else if (!this._isToken(token, 'LIT', '}')) { // Unexpected Token
          throw this._throwException([',', '}'], token);
        }
      }
      // Consume Trailing '}'
      this.__lexer.next();

      // Create AST : MAP -> tuples
      return this.__astNode('MAP', tuples.count() === 0 ? null : tuples.map());
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    /** 
     * Dispatch the command to it's declared parser function
     * 
     * @param command {String} Command Name
     * @return {meta.api.parser.IASTNode} AST for Command
     * @throw {String} Parser Exception
     */
    _dispatchCommand: function(command) {
      // Build Name of Possible Command Parser
      var parser = '_parseCommand';
      if (command.length === 1) {
        parser += command.charAt(0).toUpperCase();
      } else {
        parser += command.charAt(0).toUpperCase() + command.slice(1);
      }

      // Do we have a parser function for this command?
      if (this[parser] && qx.lang.Type.isFunction(this[parser])) { // YES
        var token = this.__lexer.next();
        return this[parser].call(this, token);
      } else { // NO
        throw "Unknown command [" + command + "]";
      }
    },
    /**
     * Test if the token matches the given type and value.
     * 
     * @param token {Map} Token to test
     * @param type {String} Type to test for
     * @param value {String|null} Value to test for
     * @returns {Boolean} 'TRUE' type matches, 'FALSE' otherwise
     */
    _isToken: function(token, type, value) {
      // Was the token's value provided?
      if (value == null) { // NO: Use just token type for the match
        return token.hasOwnProperty('type') && (token.type === type);
      } else { // YES
        return token.hasOwnProperty('type') && (token.type === type) && (token.token === value);
      }
    },
    _newToken: function(type, value) {
      return {'type': type, 'token': (value == null ? null : value)};
    },
    _throwException: function(expecting, found) {
      if (qx.lang.Type.isArray(expecting)) {
        expecting = expecting.join('|');
      }
      throw "Expecting one of [" + expecting + "] found [" + found.type + "," + found.token + "]";
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    __internalIdentifier: function(token) {
      // Did we find a Possible Identifier?
      if (this._isToken(token, 'IDT') ||
        this._isToken(token, 'STR')) { // YES
        var action = token;
      } else if (this._isToken(token, 'CMD')) { // NO: It's a Command
        // RESERVED WORDS ARE TREATED as IDTs at this Stage of Parsing
        token.type = 'IDT';
        var action = token;
      } else { // NO
        token = null;
      }

      return token;
    },
    __astNode: function(type, values) {
      return new meta.parser.ASTBasicNode(type, values);
    }
  } // SECTION: MEMBERS
});
