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

qx.Class.define("meta.ui.AbstractGroup", {
  extend: meta.ui.AbstractWidget,
  type: "abstract",
  implement: [
    meta.api.ui.IGroup,
    meta.api.ui.IWidgetInput,
    meta.api.ui.IWidgetOutput
  ],
  include: [
    utility.mixins.MMultipleObjectEvents,
    meta.events.mixins.MMetaEventHandler,
    meta.events.mixins.MMetaEventDispatcher,
    meta.ui.mixins.MGroupInputMap,
    meta.ui.mixins.MGroupOutputMap,
    meta.ui.mixins.MValidatorGroup
  ],
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    layout: {
      nullable: false,
      apply: "_applyLayout"
    }
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Abstract Group Class
   * 
   * @param group {meta.api.entity.IContainer} Container Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(group, parent) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(group, meta.api.entity.IContainer, "[group] Is not of the expected type!");
    }

    // Initialize Base Widget
    this.base(arguments, group, parent);
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
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IGroup)
     ***************************************************************************
     */
    /**
     * Label for Group
     *
     * @return {String} Returns Group's Label.
     */
    getLabel: function() {
      return this.getEntity().getLabel();
    },
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IWidgetInput)
     ***************************************************************************
     */
    /**
     * Does the Meta Widget accept input parameters?
     * 
     * @return {Boolean} 'true' The form accepts input parameters, 'false' otherwise
     */
    acceptsInput: function() {
      return this._mx_gim_acceptsInputs();
    },
    /**
     * List of allowed Inputs, if any
     * 
     * @return {String[]|null} List of Input IDs or 'null' if none
     */
    allowedInputs: function() {
      return this._mx_gim_inputFields();
    },
    /**
     * Input value to the Meta Widget.
     * 
     * @param map {Object|null} property->value map, or null, if no input allowed
     */
    setInput: function(map) {
      this._mx_gim_forwardInputs(map);
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.widgets.IWidgetOutput)
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
        return id !== null ? this._mx_gom_hasOutputField(id) : false;
      }

      return this._mx_gom_hasOutput();
    },
    /**
     * List of Output IDs
     * 
     * @return {String[]|null} All IDs that this Widget has an Output for, or 'null' if no Outputs
     */
    getOutputs: function() {
      return this._mx_gom_outputFields();
    },
    /**
     * Output Value of the Meta Widget
     * 
     * @param id {String|null} Specific ID or 'null' if all
     * @return {Object|null} Output field->value map, or null if no output
     */
    getOutput: function(id) {
      id = (id != null) ? utility.String.v_nullOnEmpty(id, true) : null;
      return (id !== null) ? this._mx_gom_fieldOuput(id) : this._mx_gom_outputs();
    },
    /*
     ***************************************************************************
     PROPERTY HANDLERS
     ***************************************************************************
     */
    _applyLayout: function(value, old) {
      // Has the widget already been drawn?
      if (this.isReady()) { // YES: Redo the Layout
        this.getLayout().doLayout(this.getWidget());
      }
      // ELSE: Widget is not yet ready, so we don't need to do anything
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Mixin Helper Methods)
     ***************************************************************************
     */
    /**
     * Attach to meta event handler to the "dispatcher"
     *
     * @param types {String|String[]} Meta Event Type(s)
     * @param dispatcher {qx.core.Object} Entity that fires the events
     * @return {Boolean} 'TRUE' attached all possible types, 'FALSE' did nothing
     */
    _mx_attachToMetaEvents: function(types, dispatcher) {
      return this._mx_meh_attach(types, dispatcher);
    },
    /**
     * Detach meta event handler from the "dispatcher".
     *
     * @param types {String|String[]} Meta Event Type(s)
     * @param dispatcher {qx.core.Object} Entity that fires the events
     * @return {Boolean} 'TRUE' detached all possible types, 'FALSE' did nothing
     */
    _mx_detachFromMetaEvents: function(types, dispatcher) {
      return this._mx_meh_detach(types, dispatcher);
    },
    /**
     * Fire Meta Event.
     *
     * @param type {String} Meta Event Type
     * @param failure {Boolean?false} Are we notifying of failure?
     * @param parameters {Var|Var[]?null} Any parameters to pass on
     * @param code {Integer} Error Code
     * @param message {String?null} Error Message
     * @return {Boolean} Whether the event was handled successfully
     */
    _mx_fireMetaEvent: function(type, failure, parameters, message, code) {
      return !failure ?
        this._mx_med_fireEventOK(type, parameters, message, code) :
        this._mx_med_fireEventNOK(type, parameters, message, code);
    }
  } // SECTION: MEMBERS
});
