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
 * A Series of Functions, that go Hand-in-Hand with the Mixins that
 * Support Input/Output functions of IMetaWidgetIO
 */
qx.Mixin.define("meta.ui.mixins.MValidatorGroup", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__validator = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __validator: null,
    /*
     ***************************************************************************
     PROTECTED FUNCTIONS
     ***************************************************************************
     */
    _mx_vg_staticValidation: function(entity) {
      // Cycle through the children and verify if all are valid
      var list = this.getChildren();

      var widget;
      for (var i = 0; i < list.length; ++i) {
        widget = this.getChild(list[i]);

        // Does the Widget Support Validation?
        if (qx.lang.Type.isFunction(widget.isValidState)) { // YES

          // Is the Widget Valid?
          if (!widget.isValidState()) { // NO: Abort
            return false;
          }
        }
      }

      return true;
    },
    _mx_vg_dynamicValidation: function(entity) {
      // Do we have a validation function?
      if (this.__validator === null) { // NO: Try to build one

        // Do we have validation rules?
        var rules = entity.getValidationRules();
        if (rules !== null) { // YES

          // Is this a Single Rule?
          if (qx.lang.Type.isString(rules)) { // YES
            // Is the rule not empty?
            rules = utility.String.nullOnEmpty(rules);
            if (rules !== null) { // YES
              rules = [rules];
            }
          } else if (qx.lang.Type.isArray(rules)) { // NO: Multiple Rules
            rules = utility.Array.clean(utility.Array.trim(rules));
          }

          // Do we have rules to convert?
          if (rules !== null) { // YES
            var results = this.__mx_vg_parseRules(rules);

            // Do we have results to build a function on?
            if (results !== null) { // YES
              this.__validator = this.__mx_vg_buildValidator(results);
            } else { // NO
              this.__validator = null;
            }
          }
        }
      }

      return this.__validator === null ? true : this.__validator.call(this);
    },
    /*
     ***************************************************************************
     PRIVATE FUNCTIONS
     ***************************************************************************
     */
    __mx_vg_buildValidator: function(rules) {
      // Start Function
      var validator = "function () { return";

      // Cycle through the rules to make a single statement
      var rule, addAND = false;
      for (var i = 0; i < rules.length; ++i) {
        if (addAND) {
          validator += " && ";
        }

        // Did we correctly build the rule?
        rule = this.__mx_vg_buildRule(rules[i]);
        if (rule === null) { // NO: Abort
          return null;
        }

        validator += "(" + rule + ")";
        addAND = true;
      }

      // End Function
      validator += "; }";

      // Convert Validator String to Function
      return this.__validator = eval(validator);
    },
    __mx_vg_buildRule: function(rule) {
      // Cycle through the rule to make a single expression
      var result = '', expression, addOR = false;
      for (var i = 0; i < rule.length; ++i) {
        if (addOR) {
          result += " || ";
        }

        // Did we correctly build the javascript expression?
        expression = this.__mx_vg_buildExpression(rule[i]);
        if (expression === null) { // NO: Abort
          return null;
        }

        result += "(" + expression + ")";
        addOR = true;
      }

      return result;
    },
    __mx_vg_buildExpression: function(expression) {
      var widget_tests = null, value_tests = null;

      // Handle LHS
      switch (expression.lhs.type) {
        case 20: // Widget ID
          widget_tests = "this.this._mx_gv_hasValueWidget('" + expression.lhs.token + "')";
          value_tests = "(this.this._mx_gv_getWidgetValue('" + expression.lhs.token + "')";
          break;
        case 21: // Quoted String
          value_tests = "(" + expression.lhs.token;
          break;
        case 22: // String
          value_tests = "('" + expression.lhs.token + "'";
          break;
        default: // ??
          return null;
      }

      // Handle Operator
      switch (expression.operator.type) {
        case 10:
          value_tests += '==';
          break;
        case 11:
          value_tests += '!=';
          break;
        case 12:
          value_tests += '<=';
          break;
        case 13:
          value_tests += '<';
          break;
        case 14:
          value_tests += '>=';
          break;
        case 15:
          value_tests += '>';
          break;
        default: // ??
          return null;
      }

      // Handle RHS
      switch (expression.lhs.type) {
        case 20: // Widget ID
          // Do we already have a Widget Test?
          if (widget_tests === null) { // NO
            widget_tests = "this._mx_gv_hasValue('" + expression.lhs.token + "')";
          } else { // YES
            widget_tests = "&& this._mx_gv_hasValue('" + expression.lhs.token + "')";
          }
          value_tests = "this._mx_gv_getWidget('" + expression.lhs.token + "'))";
          break;
        case 21: // Quoted String
          value_tests = expression.lhs.token + ")";
          break;
        case 22: // String
          value_tests = "'" + expression.lhs.token + "')";
          break;
        default: // ??
          return null;
      }

      return widget_tests + " && " + value_tests;
    },
    __mx_vg_parseRules: function(rules) {
      var results = [];

      // Cycle through and parse all the rules
      var expressions, rule;
      for (var i = 0; i < rules.length; ++i) {
        rule = utility.String.v_nullOnEmpty(rules[i], true);

        // Do we have a Non-Empty String?
        if (rule === null) { // NO: Skip it
          continue;
        }

        // Did we successfully Parse the Rule?
        expressions = this.__mx_vg_parseRule(rule);
        if (expressions !== null) {  // YES        
          results.push(expressions);
        } else { // NO: Abort
          this.error("Error Parsing Validation Rule [" + rules[i] + "]");
          return null;
        }
      }

      return results.length > 0 ? results : null;
    },
    __mx_vg_parseRule: function(rule) {
      var expressions = [];

      var expression, token;
      do {
        expression = this.__mx_vg_parseExpression(rule);
        // Was the expression parsed correctly?
        if (expression.error === null) { // YES
          expression.push(expression);
          rule = expression.leftover;
        } else { // NO: Abort
          expressions = null;
          break;
        }

        token = this.__mx_vg_nextToken(rule, null);
        if (token.type == 0) { // End-of-Rule
          break;
        } else if (token.type != 1) { // ??
          expressions = null;
          break;
        }
      } while (rule.length > 0);

      return expressions;
    },
    __mx_vg_parseExpression: function(text) {
      // Do we have a Valid Lefthand Side Token?
      var lhs = this.__mx_vg_nextToken(text, null);
      if ((lhs >= 20) && (lhs <= 22)) { // YES

        // Do we have an Operator?
        var operator = this.__mx_vg_nextToken(lhs.leftover, null);
        if ((operator >= 10) && (operator < 20)) { // YES

          // Do we have a Valid Righthand Side Token
          var rhs = this.__mx_vg_nextToken(operator.leftover, null);
          if ((rhs >= 20) && (rhs <= 22)) { // YES
            // Are both sides values?
            if ((lhs >= 21) && (lhs <= 22) && (rhs >= 21) && (rhs <= 22)) { // YES : Error
              return {
                error: "Atleast on of the sides of the operator has to be a Widget ID"
              };
            }

            // OK: We have a Valid Expression
            return {
              lhs: lhs,
              operator: operator,
              rhs: rhs,
              leftover: rhs.leftover,
              error: null
            };
          }
        } else {
          return {
            error: "Expecting an Operator"
          };
        }
      } else {
        return {
          error: "Expecting a Widget ID or a Value"
        };
      }
    },
    __mx_vg_nextToken: function(text, putback) {

      // Do we have a token to putback?
      if (putback !== null) { // YES
        return putback;
      }

      // Do we have more tokens?
      if (text.length > 0) { // YES
        switch (text[0]) {
          case '|': // OR
            return {
              type: 1,
              token: '1',
              leftover: text.splice(1).trim()
            };
          case '=': // OPERATOR ==
            return {
              type: 10,
              token: '=',
              leftover: text.splice(1).trim()
            };
          case '<': // OPERATOR < | <= | <>
            // Is it possible to have 2 char combination?
            if (text.length > 1) { // YES
              switch (text[1]) {
                case '>':
                  return {
                    type: 11,
                    token: '<>',
                    leftover: text.splice(2).trim()
                  };
                case '=':
                  return {
                    type: 12,
                    token: '<=',
                    leftover: text.splice(2).trim()
                  };
              }
            }

            return {
              type: 13,
              token: '<',
              leftover: text.splice(1).trim()
            };
          case '>': // OPERATOR > | >= 
            // Is token '>='?
            if ((text.length > 1) && (text[1] === '=')) { // YES
              return {
                type: 14,
                token: '>=',
                leftover: text.splice(2).trim()
              };
            }

            return {
              type: 15,
              token: '>',
              leftover: text.splice(1).trim()
            };
          case '{': // WIDGET {id}
            return this.__mx_vg_extractWidgetID(text);
          case '"':
          case "'": // STRING VALUE
            return this.__mx_vg_extractQuotedValue(text);
            break;
          default : // VALUE          
            return this.__mx_vg_extractValue(text);
        }
      } else { // NO: End-Of-Rule
        return {
          type: 0,
          token: null,
          leftover: null
        };
      }
    },
    __mx_vg_extractWidgetID: function(text) {
      // Search for End Brace          
      var idx = text.indexOf('}', 1);

      // Do we have a VALID terminating brace?
      if (idx > 2) { // YES

        // Do we have an ID?
        var token = text.slice(1, idx - 1).trim();
        if (token.length > 0) { // YES
          return {
            type: 20,
            token: token,
            leftover: text.splice(idx + 1).trim()
          };
        } else { // NO: Error
          return {
            type: -1,
            error: "No ID found in {}"
          };
        }
      } else { // NO: Error
        return {
          type: -1,
          error: "Expecting '}'"
        };
      }
    },
    __mx_vg_extractQuotedValue: function(text) {
      // Search for End Quote
      var idx = text.indexOf(text[0], 1);

      // Do we have a terminating quote?
      if (idx > 0) { // YES
        return {
          type: 21,
          token: text.slice(0, idx),
          leftover: text.splice(idx + 1).trim()
        };
      } else { // NO: Error
        return {
          type: -1,
          error: "Missing End Quote [" + idx[0] + "]"
        };
      }
    },
    __mx_vg_extractValue: function(text) {
      // Search for End Quote
      var idx = text.indexOf('|', 1);

      // Do we have a rule continuation?
      if (idx > 0) { // YES
        return {
          type: 22,
          token: text.slice(0, idx - 1),
          leftover: text.splice(idx).trim()
        };
      } else { // NO
        return {
          type: 22,
          token: text.trim,
          leftover: ''
        };
      }
    }
    /*
     ***************************************************************************
     ABSTRACT METHODS
     ***************************************************************************
     */
    /*
     ***************************************************************************
     IMPLEMENTATION REQUIRED FUNCTIONS (to be implemented in container class)
     _mx_gv_hasValue(id);
     _mx_gv_getValue(id);
     ***************************************************************************
     */
  } // SECTION: MEMBERS
});
