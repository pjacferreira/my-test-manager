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
qx.Mixin.define("meta.ui.mixins.MGroupOutputMap", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notifies of Changes in the Possible Outputs
    "change-outputs": "meta.events.MetaEvent",
    // Notifies of Changes in the values of the Possible Outputs
    "change-output-values": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Contructor
   */
  construct: function() {
    // Initialize
    this.__mapOutputToWidget = new utility.Map();
    this.__mapWidgetToOutputs = new utility.Map();
  },
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__mapOutputToWidget = null;
    this.__mapWidgetToOutputs = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __mapOutputToWidget: null,
    __mapWidgetToOutputs: null,
    /*
     ***************************************************************************
     MIXIN FUNCTIONS (Filter and Default Map Builders)
     ***************************************************************************
     */
    _mx_gom_hasOutput: function() {
      return this.__mapOutputToWidget.count() > 0;
    },
    _mx_gom_hasOutputField: function(id) {
      return this.__mapOutputToWidget.has(id);
    },
    _mx_gom_outputFields: function() {
      return this._mx_gom_hasOutput() ? this.__mapOutputToWidget.keys() : null;
    },
    _mx_gom_fieldOuput: function(id) {
      var widget = this.__mapOutputToWidget.get(id);
      if (widget !== null) {
        widget = this.getChild(id);
        return widget !== null ? widget.getOutput(id) : null;
      }

      return null;
    },
    _mx_gom_outputs: function() {
      // Do we have output widgets?
      if (this.__mapOutputToWidget.count() > 0) { // YES
        var empty = true, outputs = {}, widget, output;

        // Cycle through the widgets retrieving their output
        var list = this.__mapOutputToWidget.keys();
        for (var i = 0; i < list.length; ++i) {
          widget = this.__mapOutputToWidget.get(list[i]);
          // Valid Widget?
          if (widget) { // YES
            output = widget.getOutput();

            // Widget has output?
            if (output !== null) { // YES
              outputs = qx.lang.Object.mergeWith(outputs, output, false);
              empty = false;
            }
          }
        }

        return empty ? null : outputs;
      }

      return null;
    },
    _mx_gom_addWidget: function(widget, nonotify) {
      var notify = !!nonotify;

      // Widgets Outputs Added
      var added = null;

      // Do we already have this widget?
      if (!this.__mapWidgetToOutputs.has(widget.getID())) { // NO        
        // Does the widget accept inputs?
        if (qx.lang.Type.isFunction(widget.hasOutput)) { // YES   
          // Add Widgets Outputs
          added = this.__mx_gom_addWidgetOutputs(widget);

          // Attach Listener for Outputs
          this._mx_attachToMetaEvents(["change-outputs", "change-output-values"], widget);
        }
      }

      // Should we Notify of these Outputs Changes?
      if (added && notify) { // YES
        this._mx_fireMetaEvent("change-outputs", true, [this, added]);
      }

      return added !== null;
    },
    _mx_gom_removeWidget: function(widget, nonotify) {
      var notify = !!nonotify;

      // Remove Listener for Output Changes
      this._mx_detachFromMetaEvents(["change-outputs", "change-output-values"], widget);

      // Remove Widgets Outputs
      var removed = this.__mx_gom_removeWidgetOutputs(widget);

      // Remove Widget from Map
      this.__mapWidgetToOutputs.remove(widget.getID());

      // Should we Notify of these Outputs Changes?
      if (removed && notify) { // YES
        this._mx_fireMetaEvent("change-outputs", true, [this, null, removed]);
      }

      return removed !== null;
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaChangeOutputsOK: function(code, message, widget, add, remove) {
      // Has the widget removed outputs?
      if (remove != null) { // YES
        this.__mx_gom_removeWidgetOutputs(widget);

        // Does the widget Still Have Outputs?
        if (widget.hasOutputs()) { // YES
          // Make sure to signal that we want to re-add them
          add = true;
        }
      }

      if (add != null) {
        this.__mx_gom_addWidgetOutputs(widget);
      }
    },
    /*
     ***************************************************************************
     HELPER FUNCTIONS
     ***************************************************************************
     */
    __mx_gom_addWidgetOutputs: function(widget) {
      // Does the widget Currently Have any Defined Outputs?
      var outputs = widget.getOutputs();
      if (outputs !== null) { // YES
        // Cycle through the Allowed Outputs and Add them to the respective maps
        var output, map = {};
        for (var i = 0; i < outputs.length; ++i) {
          output = outputs[i];
          this.__mapOutputToWidget.add(output, widget);
          map[output] = null;
        }

        this.__mapWidgetToOutputs.add(widget.getID(), map);
      }

      return outputs;
    },
    __mx_gom_removeWidgetOutputs: function(widget) {
      // Array of Outputs that were removed
      var removed = [];

      // Remove the Widget, if it exists, from the maps.
      var outputs = this.__mapWidgetToOutputs.get(widget.getID());
      if (outputs) {
        // Cycle through the Widget's Outputs and Remove Assocciated Entries
        var mapped = null;
        for (var output in outputs) {
          if (outputs.hasOwnProperty(output)) {
            mapped = this.__mapOutputToWidget.get(output);
            // Is this Output Mapped to this Widget?
            if ((mapped !== null) && (mapped === widget.getID())) { // YES
              removed.push(output);
              this.__mapOutputToWidget.remove(output);
            }
          }
        }
      }

      return removed.length ? removed : null;
    }
    /*
     ***************************************************************************
     ABSTRACT (Methods to be implemented in Container Class) 
     ***************************************************************************
     */
    // _mx_attachToMetaEvents: function(types, dispatcher);
    // _mx_detachFromMetaEvents: function(types, dispatcher);
    // _mx_fireMetaEvent: function(type, success, parameters, message, code);
  } // SECTION: MEMBERS
});
