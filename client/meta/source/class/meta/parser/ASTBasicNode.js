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
qx.Class.define("meta.parser.ASTBasicNode", {
  extend: qx.core.Object,
  implement: meta.api.parser.IASTNode,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Create Basic AST Node
   * 
   * @param type {String} AST Node's Type
   * @param value {Var} AST Node's Value
   */
  construct: function(type, value) {
    type = utility.String.v_nullOnEmpty(type, true);
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertString(type, "[type] Is not of the expected type!");
    }

    // Initialize
    this.__type = type;
    this.__value = value;
  },
  /**
   *
   */
  destruct: function() {
    // Cleanup
    this.__type = null;
    this.__value = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __type: null,
    __value: null,
    /*
     ***************************************************************************
     METHODS (meta.api.parser.IASTNode)
     ***************************************************************************
     */
    /** 
     * Get the type of AST Node
     * 
     * @return {String} AST Node's Type
     */
    getType: function() {
      return this.__type;
    },
    /** 
     * Get the Value for the AST Node
     * 
     * @return {Var} AST Node's Value
     */
    getValue: function() {
      return this.__value;
    },
    /** 
     * AST Dump of a Node.
     * 
     * @param recurse {Boolean|true} Do a deep recursion
     * @param indent {Boolean|true} Perform an Indented Dump (implies using newlines)
     * @return {String} Dump of a node
     */
    toString: function(recurse, indent) {
      if (!!recurse) {
        var lines = this._dumpNode(this);
        return !!indent ? this.__indent(lines, 0) : this.__flatten(lines);
      } else {
        return "AST(" + this.__type + ")";
      }
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    _dumpNode: function(node) {
      // Get Output of the AST Node's Value
      var lines = this._dumpValue(node.getValue());

      // Add start of Dump for AST Nodes
      lines[0] = "AST(" + node.getType() + "|" + lines[0];

      // Add end of Dump for AST Nodes
      lines.push(lines.pop() + ")");

      return lines;
    },
    _dumpValue: function(value) {
      var lines;
      if (value == null) {
        lines = "NULL";
      } else if (this.__isASTNode(value)) {
        lines = this._dumpNode(value);
      } else if (qx.lang.Type.isArray(value)) {
        lines = this._dumpArray(value);
        lines.unshift("[");
        lines.push("]");
      } else if (qx.lang.Type.isObject(value)) {
        if (value.hasOwnProperty('token')) {
          lines = this._dumpToken(value);
        } else {
          lines = this._dumpObject(value);
          lines.unshift("{");
          lines.push("}");
        }
      } else {
        lines = value.toString();
      }

      return lines;
    },
    _dumpArray: function(entries) {
      var lines = [];
      for (var i = 0; i < entries.length; ++i) {
        if (i) {
          lines.push(',');
        }
        lines.push(this._dumpValue(entries[i]));
      }
      return lines;
    },
    _dumpObject: function(map) {
      var output, lines = [], first = true;
      for (var key in map) {
        if (map.hasOwnProperty(key)) {
          if (!first) {
            lines.push(',');
          }

          output = this._dumpValue(map[key]);
          output[0] = "'" + key + "':" + output[0];
          lines.push(output);
        }
      }

      return lines;
    },
    _dumpToken: function(token) {
      return "TOKEN(" + token.type + "|" + token.value + ")";
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    __isASTNode: function(node, type) {
      // Are we dealing with an AST Node?
      return qx.lang.Type.isObject(node) && qx.lang.Type.isFunction(node.getType);
    },
    __flatten: function(lines) {
      var output = "";
      var line;
      for (var i = 0; i < lines.length; ++i) {
        line = lines[i];
        if (qx.lang.Type.isArray(line)) {
          output += this.__flatten(line);
        } else {
          output += line;
        }
      }

      return output;
    },
    __indent: function(lines, level) {
      var paddingN0 = this.__repeat('  ', level);
      var paddingN1 = paddingN0 + '  ';

      var output = paddingN0 + lines.shift() + "\n";
      var tail = lines.pop();

      var line;
      for (var i = 0; i < lines.length; ++i) {
        if (i) {
          output += "\n";
        }

        line = lines[i];
        if (qx.lang.Type.isArray(line)) {
          output += this.__indent(line, level + 1);
        } else {
          output += paddingN1 + line;
        }
      }
      output += "\n" + paddingN0 + tail;

      return output;
    },
    __repeat: function(rep, num) {
      var s = '';
      if (num === 1) {
        return rep;
      } else if (num > 1) {
        for (; ; ) {
          if (num & 1) {
            s += rep;
          }
          num >>= 1;
          if (num) {
            rep += rep;
          } else {
            break;
          }
        }
      }

      return s;
    }
  } // SECTION: MEMBERS
});
