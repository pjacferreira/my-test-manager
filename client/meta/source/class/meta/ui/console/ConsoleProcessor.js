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
qx.Class.define("meta.ui.console.ConsoleProcessor", {
  extend: qx.core.Object,
  implement: meta.api.ui.console.ICommandProcessor,
  include: [
    meta.events.mixins.MMetaEventHandler,
    meta.events.mixins.MMetaEventDispatcher
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Output to Console
    "console-output": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Create a command processor for a console
   * 
   * @param console {meta.scratch.console.Console} Console associated with command processor
   * @param parser {meta.api.parser.IParser} Parser for the console commands
   * @param runner {meta.api.parser.IRunner} Executor for the parsed results
   */
  construct: function(console, parser, runner) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(console, "[console] Is not of the expected type!");
      qx.core.Assert.assertInterface(parser, meta.api.parser.IParser, "[parser] Is not of the expected type!");
      qx.core.Assert.assertInterface(runner, meta.api.parser.IRunner, "[runner] Is not of the expected type!");
    }

    // Initialize
    this.__console = console;
    this.__parser = parser;
    this.__enviroment = {};
    this._applyRunner(runner, null);
  },
  /**
   *
   */
  destruct: function() {
    // Cleanup
    this.__console = null;
    this.__parser = null;
    this.__enviroment = null;
    this._applyRunner(null, this.__runner);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __console: null,
    __parser: null,
    __runner: null,
    __enviroment: null,
    /*
     ***************************************************************************
     METHODS (meta.api.parser.ICommandProcessor)
     ***************************************************************************
     */
    /** 
     * Process Commands and return result
     * 
     * @param commands {String|String[]} Commands to be Processed
     * @return {String} Processor Output
     */
    process: function(commands) {
      // Is commands just a single line?
      if (qx.lang.Type.isString(commands)) { // YES
        commands = utility.String.v_nullOnEmpty(commands, true);

        // Is the command terminated by ';'?
        if (commands && (commands[commands.length - 1] !== ';')) { // NO
          commands += ';';
        }
      } else if (qx.lang.Type.isArray(commands)) { // NO: Commands is an Array
        commands = utility.Array.clean(utility.Array.trim(commands));
      } else { // NO: Commands is invalid
        commands = null;
      }

      // Do we have Commands to Execute?
      if (commands !== null) { // YES
        try {
          this.__parser.setLines(commands);
          this.__runner.run(this.__parser.parse(), this.__enviroment);
        } catch (e) {
          this._processMetaRunResultsNOK(1, e.toString());
        }
      } else { // NO
        this._processMetaRunResultsNOK(2, "Nothing to Execute");
      }
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    _applyRunner: function(runner, old) {
      var events = ["run-results"];
      if (old !== null) {
        this._mx_meh_detach(events, old);
      }

      this.__runner = runner;
      if (runner !== null) {
        this._mx_meh_attach(events, this.__runner);
      }
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaRunResultsOK: function(code, message, results) {
      // Notify Console of New Output
      var lines = this.__dumpValue(results);
      lines = qx.lang.Type.isArray(lines) ? this.__indent(lines, 0) : [lines];
      this._mx_med_fireEventOK("console-output", [lines], message, code);
    },
    _processMetaRunResultsNOK: function(code, message) {
      // Notify Console of Error
      this._mx_med_fireEventNOK("console-output", null, message, code);
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    __dumpValue: function(value) {
      var lines = '';
      if (typeof value !== 'undefined') {
        if (value !== null) {
          if (qx.lang.Type.isObject(value)) {
            lines = this.__dumpObject(value);
            lines.unshift("{");
            lines.push("}");
          } else if (qx.lang.Type.isArray(value)) {
            lines = this.__dumpArray(value);
            lines.unshift("[");
            lines.push("]");
          } else if (qx.lang.Type.isFunction(value)) {
            lines = value.call(this);
          } else {
            lines = value.toString();
          }
        } else if (value === null) {
          lines = 'null';
        }
      }
      return lines;
    },
    __dumpObject: function(map) {
      var output, lines = [], first = true;
      for (var key in map) {
        if (map.hasOwnProperty(key)) {
          if (this.__isPrivateProperty(key)) {
            continue;
          }

          if (!first) {
            lines.push(',');
          }

          output = this.__dumpValue(map[key]);
          if (qx.lang.Type.isArray(output)) {
            output[0] = "'" + key + "' : " + output[0];
          } else {
            output = "'" + key + "' : " + output;
          }
          lines.push(output);
        }
      }

      return lines;
    },
    __dumpArray: function(entries) {
      var lines = [];
      for (var i = 0; i < entries.length; ++i) {
        if (i) {
          lines.push(',');
        }
        lines.push(this.__dumpValue(entries[i]));
      }
      return lines;
    },
    __isPrivateProperty: function(name) {
      return (name.length >= 2) && (name[0] === '_') && (name[1] === '_');
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
      if (lines.length > 1) {
        // Pre-calculate padding levels
        var paddingN0 = this.__repeat('  ', level);
        var paddingN1 = paddingN0 + '  ';

        // Remove head and tail
        var head = paddingN0 + lines.shift();
        var tail = paddingN0 + lines.pop();

        // Loop through the remaining lines and indent them
        var line;
        for (var i = 0; i < lines.length; ++i) {
          line = lines[i];
          if (qx.lang.Type.isArray(line)) {
            lines[i] = this.__indent(line, level + 1);
          } else {
            lines[i] = paddingN1 + line;
          }
        }

        // Restore head and tail
        lines.unshift(head);
        lines.push(tail);
      }

      return lines;
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
