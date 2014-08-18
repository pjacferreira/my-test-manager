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

qx.Class.define("meta.ui.Button", {
  extend: meta.ui.AbstractWidget,
  implement: [
    meta.api.ui.IWidgetInput,
  ],
  include: [
    meta.ui.mixins.MBasicInputMap
  ],
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

    // Setup Local Init Functions
    this._init_functions
      .add(900, this._init_readyWidget);
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
    __inputs: null,
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.widgets.IWidgetInput)
     ***************************************************************************
     */
    /**
     * Does the Meta Widget accept input parameters?
     * 
     * @return {Boolean} 'true' The form accepts input parameters, 'false' otherwise
     */
    acceptsInput: function() {
      return this._mx_bim_acceptsInputs();
    },
    /**
     * List of allowed Inputs, if any
     * 
     * @return {String[]|null} List of Input IDs or 'null' if none
     */
    allowedInputs: function() {
      return this._mx_bim_inputFields();
    },
    /**
     * Input value to the Meta Widget.
     * 
     * @param map {Object|null} property->value map, or null, if no input allowed
     */
    setInput: function(map) {
      this.__inputs = this._mx_bim_extractInput(map);
    },
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
      // Create Button
      var button = new qx.ui.form.Button();

      // Get the Field Entity
      var widget = this.getEntity();

      // Get Button Lable and/or Icon
      var label = widget.getLabel();
      var icon = widget.getIcon();

      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue((label !== null) || (icon !== null), "[definition] is Invalid!");
      }

      // Initialize: Button
      if (label !== null) {
        button.setLabel(label);
      }
      if (icon !== null) {
        button.setIcon(icon);
      }

      // Button is Initially Disabled (because we have to ready the button, before it can be used)
      button.setEnabled(false);

      return button;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Intialization Functions)
     ***************************************************************************
     */
    _init_readyWidget: function(parameters) {
      var button = parameters._widget;
      
      // Enable Button
      button.setEnabled(true);
      
      return parameters;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS
     ***************************************************************************
     */
    /**
     * Apply Specific Field Settings to the Display Widget.
     *
     * @param widget {qx.ui.core.Widget} The displayble widget
     */
    _applyWidgetSettings: function(widget) {
      // TODO Apply Basic Field Settings
    },
    _execute: function(e) {
      return this.executeAction('click');
    },
    /*
     ***************************************************************************
     MIXIN INTERFACE FUNCTIONS (meta.api.itw.mixins.MWidgetActions)
     ***************************************************************************
     */
    _mx_wa_isValidAction: function(group) {
      return (group === 'click') || (group === 'shift');
    },
    _mx_wa_getCurrentInputs: function() {
      return this.__inputs;
    },
    _mx_wa_registerInputFields: function(field, required) {
      return this._mx_bim_addInputField(field, required);
    }
  } // SECTION: MEMBERS
});
