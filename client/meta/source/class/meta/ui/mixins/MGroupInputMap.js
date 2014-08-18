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
qx.Mixin.define("meta.ui.mixins.MGroupInputMap", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notifies of Changes in the Allowed Inputs
    "change-inputs": "meta.events.MetaEvent"
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
    this.__mapInputToWidget = new utility.Map();
    this.__mapWidgetToInputs = new utility.Map();
  },
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__mapInputToWidget = null;
    this.__mapWidgetToInputs = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __mapInputToWidget: null,
    __mapWidgetToInputs: null,
    /*
     ***************************************************************************
     MIXIN FUNCTIONS (Filter and Default Map Builders)
     ***************************************************************************
     */
    _mx_gim_acceptsInputs: function() {
      return this.__mapInputToWidget.count() > 0;
    },
    _mx_gim_hasInputField: function(field) {
      return this.__mapInputToWidget.has(field);
    },
    _mx_gim_inputFields: function(field) {
      return this.__mapInputToWidget.keys();
    },
    _mx_gim_addWidget: function(widget, nonotify) {
      var notify = !!nonotify;

      // Widgets Inputs Added
      var added = null;

      // Do we already have this widget?
      if (!this.__mapWidgetToInputs.has(widget.getID())) { // NO        
        // Does the widget accept inputs?
        if (qx.lang.Type.isFunction(widget.acceptsInput)) { // YES          
          // Add Widgets Outputs
          added = this.__mx_gim_addWidgetInputs(widget);

          // Attach Listener for Outputs
          this._mx_attachToMetaEvents("change-inputs", widget);
        }
      }

      // Should we Notify of these Outputs Changes?
      if (added && notify) { // YES
        this._mx_fireMetaEvent("change-inputs", true, [this, added]);
      }

      return added !== null;
    },
    _mx_gim_removeWidget: function(widget, nonotify) {
      var notify = !!nonotify;

      // Remove Listener for Input Changes
      this._mx_detachFromMetaEvents("change-inputs", widget);

      // Remove Widgets Outputs
      var removed = this.__mx_gom_removeWidgetInputs(widget);

      // Remove Widget from Map
      this.__mapWidgetToInputs.remove(widget.getID());


      // Should we Notify of these Outputs Changes?
      if (removed && notify) { // YES
        this._mx_fireMetaEvent("change-inputs", true, [this, null, removed]);
      }

      return removed !== null;
    },
    _mx_gim_forwardInputs: function(inputs) {
      // Do we have widgets that accept these inputs?
      var filtered = this.__mx_gim_filterInputs(inputs);
      if (filtered !== null) { // YES: Forward Inputs to Widgets

        // Cycle through the Managed Widget and see which ones will accept these inputs
        var widgets = this.__mapWidgetToInputs.keys();
        var id, widget, widgetInputs;
        for (var i = 0; i < widgets.length; ++i) {
          id = widgets[i];
          widgetInputs = this.__mx_gim_filterWidgetInputs(inputs, id);

          // Does the widget accept any incoming inputs?
          if (widgetInputs !== null) { // YES
            widget = this.getChild(id);
            widget.setInput(widgetInputs);
          }
        }
      }
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _processMetaChangeInputsOK: function(code, message, widget, add, remove) {
      // Has the widget removed outputs?
      if (remove != null) { // YES
        this.__mx_gim_removeWidgetInputs(widget);

        // Does the widget Still Have Outputs?
        if (widget.hasInputs()) { // YES
          // Make sure to signal that we want to re-add them
          add = true;
        }
      }

      if (add != null) {
        this.__mx_gim_addWidgetInputs(widget);
      }
    },
    /*
     ***************************************************************************
     HELPER FUNCTIONS
     ***************************************************************************
     */
    __mx_gim_addWidgetInputs: function(widget) {
      // Does the widget Currently Have any Defined Inputs?
      var inputs = widget.allowedInputs();
      if (inputs !== null) { // YES
        // Cycle through the Allowed Inputs and Add them to the respective maps
        var input, map = {}, changedInputs = false;
        for (var i = 0; i < inputs.length; ++i) {
          input = inputs[i];
          this.__mapInputToWidget.add(input, widget);
          map[input] = null;
        }

        this.__mapWidgetToInputs.add(widget.getID(), map);
      }

      return inputs;
    },
    __mx_gim_removeWidgetInputs: function(widget) {
      // Array of Outputs that were removed
      var removed = [];

      // Remove the Widget, if it exists, from the maps.
      var inputs = this.__mapWidgetToInputs.get(widget.getID());
      if (inputs) {
        // Cycle through the Widget's Inputs and Remove Assocciated Entries
        var mapped = null;
        for (var input in inputs) {
          if (inputs.hasOwnProperty(input)) {
            mapped = this.__mapOutputToWidget.get(input);
            // Is this Input Mapped to this Widget?
            if ((mapped !== null) && (mapped === widget.getID())) { // YES
              removed.push(input);
              this.__mapInputToWidget.remove(input);
            }
          }
        }
      }

      return removed.length ? removed : null;
    },
    __mx_gim_filterWidgetInputs: function(inputs, widget) {
      var count = 0;
      var extracted = {};

      // Get list of Accepted Inputs for the Widget
      var accepted = this.__mapWidgetToInputs.get(widget);

      // Cycle through the fields 
      for (var field in inputs) {
        // Is this field listed in the Accepted Inputs?
        if (inputs.hasOwnProperty(field) && accepted.hasOwnProperty(field)) { // YES
          extracted[field] = inputs[field];
          count++;
        }
      }

      return count > 0 ? extracted : null;
    },
    __mx_gim_filterInputs: function(inputs) {
      var count = 0;
      var extracted = {};

      // Do we have any fields in the Map?
      if (this.__mapInputToWidget.count()) { // YES

        // Cycle through the fields 
        for (var field in inputs) {
          // Is this field listed in the Map?
          if (inputs.hasOwnProperty(field) && this.__mapInputToWidget.has(field)) { // YES
            extracted[field] = inputs[field];
            count++;
          }
        }
      }

      return count > 0 ? extracted : null;
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
