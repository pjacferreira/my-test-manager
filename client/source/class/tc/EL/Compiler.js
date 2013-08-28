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

// TODO : Consider wether a Static or a Singleton Class is more appropriate
/**
 * Expression Language Compiler
 *
 */
qx.Bootstrap.define("tc.EL.Compiler", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    isAST: function(a) {
      return qx.lang.Type.isObject(a) ? a.hasOwnProperty('type') && a.hasOwnProperty('children') : false;
    },
    isAST_of_Type: function(a, type) {
      return this.isAST(a) ? a['type'] === type : false;
    },
    compile: function(AST) {
      var code = null;

      /* 2 Ways to Create Functions:
       * 1. using eval
       *    eval("var func = function(arg1) { return arg1; }")
       *    Notice that we both created the function and assigned it to a variable
       *    in the eval (this is required, because function definition is like
       *    function func_name(arg1) { ... body ... }
       *    What we are creating is an anoymous function, and that can only
       *    be used either by
       *    i) assigning the anonymous function to a variable (like above)
       *    ii) creating the function and immediately calling it (something like
       *    (function(arg1) { return arg1; })(1);
       *  2. Through the function object (i.e. without eval)
       *    var f = new Function('arg1','return arg1;')
       */

      if (this.isAST(AST)) { // If Single Line, convert to multiline aray
        AST = [AST];
      } else if (!qx.lang.Type.isArray(AST)) {
        AST = null;
      }

      if ((AST !== null) && qx.lang.Type.isArray(AST) && AST.length > 0) {
        /* function prototype would be something like:
         * function (value) {
         *   ....
         *   return result;
         * }
         * 
         * Where:
         * 1 "value" is the value to be tested against the conditions.
         * 2. "result" is a boolean return, such that "TRUE" means passes, "FALSE" otherwise
         */
        var expression = null;
        var compiled = null;
        for (var i = 0; i < AST.length; ++i) {
          expression = AST[i];
          if (!this.isAST(expression)) {
            // Invalid Value in AST, skip
            continue;
          }

          // Compile the expression
          compiled = this._compileExpression(expression, code !== null);
          if (compiled === null) {
            // Failed to compile the expression
            continue;
          }

          code = code === null ? compiled : code + '\n' + compiled;
        }
      }

      if (code !== null) {
        code = new Function("value", code + "\n  return result;");
      }

      return code;
    },
    _compileExpression: function(expression, multiline) {
      var code = null;

      var child = expression.hasOwnProperty('children') ? expression['children'] : null;
      if (this.isAST(child)) {
        switch (child.type) {
          case 'comparison':
            code = this._compileComparison(child);
            break;
          case 'list':
            code = this._compileList(child);
            break;
          case 'range':
            code = this._compileRange(child);
            break;
        }
      }

      return code;
    },
    _compileComparison: function(comparison) {
      /* for a comparison we expect
       * a) 2 children AST Objects
       * b) 1st Child Operator
       * c) 2nd Child Value
       */
      var code = null;

      if (qx.lang.Type.isArray(comparison['children']) &&
              (comparison['children'].length == 2)) {
        var operator = operator_to_JS(comparison['children'][0]);
        var value = value_to_JS(comparison['children'][1]);

        if ((operator !== null) && (value !== null)) {
          return '  var result = value' + operator + " " + value + ";\n";
        }
      }
      return code;
    },
    _compileList: function(list) {
      var code = null;
      var children = list['children'];
      if (qx.lang.Type.isArray(children)) {
        // build array
        var array = '  var __array=['
        var value = null;
        var count = 0;
        for (var i = 0; i < children.length; ++i) {
          value = this._value_to_JS(children[i]);
          if (value !== null) {
            array += count++ == 0 ? value : ', ' + value;
          }
        }
        array += '];\n';

        if (count) {
          code = array + '\n' +
                  '  result=true;\n' +
                  '  for(var __i=0; _i < __array.length; ++__i) {\n' +
                  '    if(value != __array[__i]) {\n' +
                  '      result = false;\n' +
                  '      break;\n' +
                  '    }\n' +
                  '  }';
        }
      }

      return code;
    },
    _compileRange: function(range) {
      var children = range['children'];
      if (qx.lang.Type.isArray(children) && (children.length == 2)) {
        var lower_limit = this._isAST_of_Type(children[0], 'value') ? this._value_to_JS(children[0]) : null;
        var upper_limit = this._isAST_of_Type(children[1], 'value') ? this._value_to_JS(children[1]) : null;

        if ((lower_limit !== null) && (upper_limit !== null)) {
          var lower_exclude = range.start === ']' ? '>' : '>=';
          var upper_exclude = range.end === '[' ? '<' : '<=';

          return '  var result = (value' + lower_exclude + ' ' + lower_limit + ') &&\n' +
                  '               (value' + upper_exclude + ' ' + upper_limit + ');\n';
        }
      }
      return null;
    },
    _operator_to_JS: function(operator) {
      // Optimize : assert this._isAST instead of calling so that in production mode, this call is never made
      if (this.isAST(operator) && (operator['type'] === 'OPERATOR')) {
        return operator['children'];
      }

      return null;
    },
    _value_to_JS: function(value) {
      // Optimize : assert this._isAST instead of calling so that in production mode, this call is never made
      if (this.isAST(value) && (value['type'] === 'value')) {
        var real_value = value['children'];
        if (this.isAST(real_value)) {
          return real_value['children'];
        }
      }

      return null;
    }
  }
});
  