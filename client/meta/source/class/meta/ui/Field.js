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

qx.Class.define("meta.ui.Field", {
  extend: meta.ui.AbstractWidget,
  implement: [
    meta.api.ui.IWidgetInput,
    meta.api.ui.IWidgetOutput,
    meta.api.ui.IWidgetValidation,
    meta.api.ui.IWidgetTransformation
  ],
  include: [
    meta.ui.mixins.MValidatorField,
    meta.ui.mixins.MFieldTransform
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notifies of Changes in the Allowed Inputs
    "change-inputs": "meta.events.MetaEvent",
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
   * Field<-->Input Widget Adaptor Constructor
   * 
   * @param field {meta.api.entity.IField} Field Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(field, parent) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(field, meta.api.entity.IField, "[field] Is not of the expected type!");
      qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is not of the expected type!");
    }

    // Initialize Base Widget
    this.base(arguments, field, parent);

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
    __value: null,
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.ui.IWidgetInput)
     ***************************************************************************
     */
    /**
     * Does the Meta Widget accept input parameters?
     * 
     * @return {Boolean} 'true' The form accepts input parameters, 'false' otherwise
     */
    acceptsInput: function() {
      /* Note: 
       * - Display wise a field always accepts inputs
       * - But it might not allow the input values to be modified.
       */
      return true;
    },
    /**
     * List of allowed Inputs, if any
     * 
     * @return {String[]|null} List of Input IDs or 'null' if none
     */
    allowedInputs: function() {
      return this.acceptsInput() ? [this.getID()] : null;
    },
    /**
     * Input value to the Meta Widget.
     * 
     * @param map {Object|null} property->value map, or null, if no input allowed
     */
    setInput: function(map) {
      if (this.acceptsInput()) {
        var id = this.getID();
        if (map.hasOwnProperty(id)) {
          this._setValue(map[id], true, true);
        }
      }
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.ui.IWidgetOutput)
     ***************************************************************************
     */
    /**
     * Does the Meta Widget have an output (for a specific id)?
     * 
     * @param id {String|null} Specific ID or 'null' if general
     * @return {Boolean} 'true' The form has an output, 'false' otherwise
     */
    hasOutput: function(id) {
      if (id != null) {
        id = utility.String.v_nullOnEmpty(id, true);
        return id !== null ? (id == this.getID()) : false;
      }

      return true;
    },
    /**
     * List of Output IDs
     * 
     * @return {String[]|null} All IDs that this Widget has an Output for, or 'null' if no Outputs
     */
    getOutputs: function() {
      return [this.getID()];
    },
    /**
     * Output Value of the Meta Widget
     * 
     * @param id {String|null} Specific ID or 'null' if all
     * @return {Object|null} Output field->value map, or null if no output
     */
    getOutput: function(id) {
      return this.hasOutput(id) ? utility.Object.singleProperty(this.getID(), this._getValue()) : null;
    },
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IWidgetValidation)
     ***************************************************************************
     */
    /**
     * Is the Widgets Current State Valid?
     * 
     * @abstract
     * @return {Boolean} Returns <code>true</code> when the widget is in a valid state.
     */
    isValidState: function() {
      var entity = this.getEntity();
      var value = this.getWidget().getValue();

      // TODO Use Value Type (integer, decimal, string, date, etc.) as part of the validation process
      return this.__mx_vf_staticValidation(entity, value) &&
        this.__mx_vf_dynamicValidation(entity, value);
    },
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IWidgetTransformation)
     ***************************************************************************
     */
    /**
     * Apply State Transformation to the Widget
     * 
     * @return {Boolean} Returns <code>true</code> when the widget transformation applied successfully.
     */
    applyTransformation: function() {
      /* NOTE: Under normal circumstances this should not be called,
       * as any transformations will automatically be applied when the
       * widget's value is set.
       */

      // Did the transform create a New Value
      var new_value = this._mx_ft_applyTransforms(this.getWidget().getValue());
      if (new_value !== null) { // YES
        // If apply == true or the value was modified by the transformation        
        // TODO: Handle the different value changes
        this.getWidget().setValue(new_value.toString());

        // Is the Value Valid?
        this.getWidget().setValid(this._mx_vf_isValid(new_value));
      }

      return false;
    },
    /*
     ***************************************************************************
     OVERRIDES METHODS (meta.ui.AbstractWidget)
     ***************************************************************************
     */
    /**
     * Create a Place Holder Widget, to be used, until real widget is built.
     * 
     * @param options {Map} Widget Container
     * @return {qx.ui.core.Widget} Placeholder Widget
     */
    _createPlaceholder: function(options) {
      // Get the Field Entity
      var field = this.getEntity();

      // Create Widget
      var widget;
      switch (field.getValueType()) {
        case 'boolean' :
          widget = new qx.ui.form.CheckBox();
          break;
        case 'password' :
          widget = new qx.ui.form.PasswordField();
          break;
        case 'html' :
          widget = new qx.ui.form.TextArea();
          break;
        case 'reference': // References are Handled Asynchronously
          widget = this._referenceWidget(field);
          break;
        default:
          widget = new qx.ui.form.TextField();
      }

      // Widget is Initially Disabled
      widget.setEnabled(false);

      return widget;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Intialization Functions)
     ***************************************************************************
     */
    _init_readyWidget: function(parameters) {
      // Get the Display Widget
      var widget = parameters._widget;
      var field = this.getEntity();

      // Add Change Value Listener
      widget.addListener("changeValue", this._handleChangeValue, this);

      // Apply Button Settings
      this._applyWidgetSettings(widget, this.getEntity().getOptions());

      // Is Read Only Field?
      if (!field.isAutoValue()) { // NO
        widget.setEnabled(true);
      }

      return parameters;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS
     ***************************************************************************
     */
    _handleChangeValue: function(e) {
      this._setValue(e.getData(), true, false);
    },
    /**
     * Apply Specific Field Settings to the Display Widget.
     *
     * @param widget {qx.ui.core.Widget} The displayble widget
     * @param options {Map} Options to apply to te widget
     */
    _applyWidgetSettings: function(widget, options) {
      // TODO Apply Basic Field Settings
    },
    _getValue: function() {
      return this.__value;
    },
    _setValue: function(value, notify, apply) {
      /* Steps:
       * 1. Apply transformation (if any)
       * 1. a) if a change in value, modify the widgets value
       * 2. Save the Value.
       * 3. Has Validation?
       * 3. a) Is valid value?
       * 4. Notify Listeners of Change
       */
      // Apply Transformation (if any)
      var new_value = this._mx_ft_applyTransforms(value);

      // Has the widget's value changed by the transform?
      this.__value = (new_value !== null) ? new_value : value;

      // Apply the Value?
      if (apply || (new_value !== null)) { // YES        
        // If apply == true or the value was modified by the transformation        
        // TODO: Handle the different value changes
        this.getWidget().setValue(((value != null) ? value.toString() : ''));

        // Is the Value Valid?
        this.getWidget().setValid(this._mx_vf_isValid(value));
      }

      // Do we need to notify listeners?
      if (notify) { // YES
        // Fire Event
        this._mx_med_fireEventOK("change-output-values", utility.Object.singleProperty(this.getID(), value));
      }
    }
  } // SECTION: MEMBERS
});
