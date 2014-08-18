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

qx.Class.define("meta.ui.console.Console", {
  extend: meta.ui.AbstractWidget,
  include: [
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** Console Command Processor*/
    "processor": {
      nullable: false,
      check: "meta.api.ui.console.ICommandProcessor",
      apply: "_applyProcessor"
    },
    "historySize": {
      nullable: false,
      init: 20,
      check: "Integer",
      validate: qx.util.Validate.range(0, 100),
      apply: "_applyHistorySize"
    }
  }, // SECTION: PROPERTIES
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Widget<-->Button Adaptor Constructor
   * 
   * @param widget {meta.api.entity.IWidget} Widget Definition
   */
  construct: function(widget) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(widget, meta.api.entity.IWidget, "[widget] Is not of the expected type!");
    }

    // Initialize Base Widget
    this.base(arguments, widget);

    // Initialize Variables
    this.__history = [];
    this.__currentCommand = [];

    // Setup Local Init Functions
    this._init_functions
      .add(900, this._init_readyWidget);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    this.__currentInput = null;
    this.__history = null;
    this.__currentCommand = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __currentInput: null,
    __currentCommand: null,
    __history: null,
    __historyScrolling: false,
    __historyScrollPosition: 0,
    /*
     ***************************************************************************
     IMPLEMENTATION of ABSTRACT METHODS (meta.ui.AbstractWidget)
     ***************************************************************************
     */
    /**
     * Create a Place Holder Widget, to be used, until real widget is built.
     * 
     * @param options {Map} Widget Container
     * @return {qx.ui.core.Widget} Placeholder Widget
     */
    _createPlaceholder: function(options) {
      // Create Console's Input Area
      var console = new qx.ui.container.Composite();

      // Define Layout
      console.setLayout(new qx.ui.layout.VBox());

      // Define console Properties
      console.set({
        minWidth: 200,
        minHeight: 100,
        margin: 5,
        backgroundColor: "black",
        textColor: "green"
      });

      // Add 1st Command Line
      this.__addInputArea(console, false, null);
      return console;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Intialization Functions)
     ***************************************************************************
     */
    _init_readyWidget: function(parameters) {
      return parameters;
    },
    /*
     ***************************************************************************
     EVENT Handlers
     ***************************************************************************
     */
    _handleKeyPress: function(e) {
      // NOTE: e is of type qx.event.type.KeySequence
      switch (e.getKeyIdentifier()) {
        case "Enter":
          /* Step-by-Step:
           * 1. Extract the Command From the Input Area
           * 2. Process the lines into a more usable form
           * 3. Record Command in Command History (for posterity)
           * 4. Execute the Input, and, Display the Result.
           * Wait for Output Event
           */
          this.info("Enter Pressed");

          // Clear History Scrolling Flag
          this.__historyScrolling = false;

          // Step 1 & 2
          var lines = this._inputToLines(this._removeInputArea());
          // Step 2:
          this._addStaticLines(lines);
          // Step 3:
          this._addHistoryEntry(lines);
          // Step 4
          this._execute(lines);
          break;
        case "Up": // Previous Command
          // Should we ignore the Scroll Up Key?
          if (!this._ignoreScrollUp(e) && (this.__history.length > 0)) { // NO:
            // Are we already Scrolling?
            if (this.__historyScrolling === true) { // YES
              // Are we at the beginning of the scroll history?
              if (this.__historyScrollPosition === 0) { // YES
                // Do Nothing
                break;
              }
              // ELSE: Move Backwards in History
            } else { // NO: Starting Scroll
              this.__historyScrollPosition = this.__history.length;
              this.__historyScrolling = true;
            }
            this._moveBackwardsInHistory();
          }
          break;
        case "Down": // Next Command
          // Should we ignore the Scroll Down Key?
          if (!this._ignoreScrollDown(e) && (this.__history.length > 0)) { // NO:
            // Are we scrolling through the history?
            if (this.__historyScrolling === true) { // YES
              // Have we reached the end of the Scroll History?
              if (this.__historyScrollPosition < (this.__history.length - 1)) { // NO    
                this._moveForwardInHistory();
              }
            }
          }
          // ELSE: Nothing Scrolling so ignore the key
          break;

          /* Command History Problems:
           * 1. Multiline Input :
           * - a line is defined as any text terminated in ';'
           * - Multiple lines are text inputs that are not terminated in ';'
           * Solution:
           * - Treat Multiple Lines as a Single Command in the Command History
           * (i.e. concat the lines together so that they are displayed as a
           * single line in command history)
           * - Problems: Really long lines (larger than the console width)?
           * Alternate Solution:
           * - Use A TextArea rather than TextField as the input (this would 
           * allow us to maintain the original format (i.e. line breaks)
           * This might be doable, by dynamically converting from Text Field to 
           * Area if a Return was entered by no prvious semi-colon was found).
           * 2. If a change is made to the current line it has to be added to 
           * the command history
           * 3. If a change is made to a line taken from the command history
           * then a new entry in the command history should be added
           * 2. Multiple commands on a single line have to be handled
           * (i.e. if the a semi-colon was previously entered, but a new
           * command started the line cannot be considered complete. ex:
           * with session do whoami
           * 2. Multiline Inputs Require that, if we are in the middle of the
           * input, that we allow the up and down to move through the text.
           */
        default:
          this.__historyScrolling = false;
      }
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaConsoleOutput: function(success, code, message, lines) {
      // Step 1: Add Output
      // Did Last Command Succeed?
      if (success) {// YES
        this._addStaticLines(lines);
      } else { // NO
        this._addStaticLines(["ERROR [" + code + "]:" + message]);
      }
      // Step 2: Add New Input Area
      this._addInputArea();
    },
    /*
     ***************************************************************************
     PROPERTY Methods
     ***************************************************************************
     */
    _applyProcessor: function(processor, old) {
      var events = ["console-output"];
      if (old !== null) {
        this._mx_meh_detach(events, old);
      }

      this._mx_meh_attach(events, processor);
    },
    _applyHistorySize: function(value, old) {
      // Is the new value greater than the current length?
      if (value > this.__history.length) { // YES

        // Are we removing all history?
        if (value === 0) { // YES
          this.__history = [];
        } else if (value === 1) { // No: Setting to History to just the previous command
          this.__history = [this.__history[0]];
        } else { // No: Just setting a shorter history length
          this.__history.splice(0, value);
        }
      }
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (History Management)
     ***************************************************************************
     */
    _ignoreScrollUp: function(e) {
      // TODO Implement
      return false;
    },
    _ignoreScrollDown: function(e) {
      // TODO Implement
      return false;
    },
    _moveBackwardsInHistory: function() {
      var lines = this.__history[--this.__historyScrollPosition];
      this._changeCurrentInput(lines);
    },
    _moveForwardInHistory: function() {
      var lines = this.__history[++this.__historyScrollPosition];
      this._changeCurrentInput(lines);
    },
    _addHistoryEntry: function(lines) {
      if (lines !== null) {
        var maxSize = this.getHistorySize();
        // Are we supposed to store in the histor?
        if (maxSize > 0) { // YES
          // Is the history size limited to a single entry?
          if (maxSize === 1) { // YES
            this.__history[0] = lines;
          } else { // NO
            // By adidng the new entry are we exceeding the history size?
            if (this.__history.length >= (maxSize - 1)) { // YES
              // Remove 1st Entry in the History
              this.__history.shift();
            }
            this.__history.push(lines);
          }
        }
      }
    },
    /*
     ***************************************************************************
     PROTECTED METHODS
     ***************************************************************************
     */
    _changeCurrentInput: function(commands) {
      // Is this a Multiline Command?
      var multiline = commands.length > 1;

      // Do we have to convert to Multi-Line?
      if (multiline &&
        (this.__currentInput instanceof qx.ui.form.TextField)) { // YES
        // Replace the Single Line Input Area with the New Multi Line Input Area
        var lines = this._inputToLines(this._removeInputArea());
        this._addInputArea(true, lines);
      }

      // Change the Current Input Line
      commands = commands.length > 1 ? commands.join("\n") : commands[0];
      this.__currentInput.setValue(commands);
    },
    _removeInputArea: function() {
      var input = this.__currentInput.getValue();

      // Attach Listener to Command Line
      this.__currentInput.removeListener("keypress", this._handleKeyPress, this);

      // Remove the Used Input Field
      this.getWidget().remove(this.__currentInput);

      this.__currentInput = null;

      return input;
    },
    _addInputArea: function(multiline, lines) {
      /* Note: Double Layered Function because,
       * 1. In createPlaceHolder, the widget is not yet initialized and, therefore,
       * this.getWidget() is not yet valid...
       * 2. Any alternate solution would require that we duplicate code.
       */

      return this.__addInputArea(this.getWidget(),
        !!multiline,
        lines != null ? lines.join('\n') : null);
    },
    _addStaticLines: function(lines) {
      var multiline = lines.length > 1 ? true : false;
      var output = multiline ? this.__deepJoin(lines, '\n') : lines[0];

      // Create Static Command Element
      var element = new qx.ui.basic.Label();

      // Set Element Properties
      element.set({
        value: multiline ? ("<pre>" + output + "</pre>") : output,
        rich: multiline
      });

      // Add line to the Console
      this.getWidget().add(element);
      return element;
    },
    _inputToLines: function(input) {
      var lines = input.split('\n');
      lines = utility.Array.clean(utility.Array.trim(lines));
      return lines;
    },
    _execute: function(lines) {
      // Do we have lines to execute?
      if (lines) { // NO
        var processor = this.getProcessor();
        processor.process(lines);
      } else { // NO: Just Simply add a New Input Line
        this._addInputArea();
      }
    },
    /**
     * Apply Specific Field Settings to the Display Widget.
     *
     * @param widget {qx.ui.core.Widget} The displayble widget
     */
    _applyWidgetSettings: function(widget) {
      // TODO Apply Basic Field Settings
    },
    /*
     ***************************************************************************
     PRIVATE METHODS
     ***************************************************************************
     */
    __addInputArea: function(console, multiline, value) {
      // Create Input Element
      var widget = multiline ? new qx.ui.form.TextArea() : new qx.ui.form.TextField();

      // Set Element Properties
      widget.setPlaceholder('Command?');

      // Attach Listener to Command Line
      widget.addListener("keypress", this._handleKeyPress, this);

      // Initialize with a default value
      if (value != null) {
        widget.setValue(value);
      }

      // Add line to the Console
      console.add(widget);

      // Make sure it's the Focused Element
      this.__currentInput = widget;
      this.__currentInput.focus();

      return widget;
    },
    __deepJoin: function(array, seperator) {
      // Loop through the Array collapsing any nested arrays
      for (var i = 0; i < array.length; ++i) {
        if (qx.lang.Type.isArray(array[i])) {
          array[i] = this.__deepJoin(array[i], seperator);
        }
      }

      // Collapse the remainder
      return array.join(seperator);
    }
  } // SECTION: MEMBERS
});

/* JEG.PS Language Definition
 http://peg.arcanis.fr/
 http://bottlecaps.de/convert/
 
 with
 = 'with' WS+ service WS* 'do' WS+ action WS* ';'
 
 service
 = name WS* service_key?
 
 action
 = name WS* object_map?
 
 service_key
 = parameter_list
 / object_map
 
 parameter_list
 = '(' WS* (value_list WS*)? ')'
 
 object_map 
 = '{' WS* (map WS*)? '}'
 
 value_list
 = first:value rest:( ',' value )* { return rest ? [first].concat(rest) : [first]; }
 
 value
 = parameter:[^,)]+ { return parameter.join(""); }
 
 map
 = first:tuplet rest:( ',' tuplet )* { return rest ? [first].concat(rest) : [first]; }
 
 
 tuplet
 = property WS* ':' WS* tuplet_value
 
 property
 = name
 / ['"] chars:[^'"]+ ['"] { return chars.join(""); }
 
 tuplet_value
 =  chars:[^,}]+ { return chars.join(""); }
 
 name
 = start:[a-zA-Z_] remainder:[a-zA-Z_0-9]* { return start+remainder.join(""); }
 
 WS
 = [ \t] { return null; }
 
 */