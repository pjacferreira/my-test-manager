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

qx.Class.define("meta.scratch.ui.FormContainer", {
  extend: meta.ui.AbstractWidget,
  include: [
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Event Fired when to Signal Wether a Form is to be Submitted or Not
     */
    "form-submit": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Definition for Form Container Widget
   * 
   * @param widget {meta.api.entity.IWidget} Widget Definition
   * @param form {meta.api.entity.IForm} Form Entity
   */
  construct: function(widget, form) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(widget, meta.api.entity.IWidget, "[widget] Is not of the expected type!");
    }

    // Initialize Base Widget
    this.base(arguments, widget);

    // Initialize Variables
    this.__formEntity = form;

    // Setup Local Init Functions
    this._init_functions
      .add(100, this._init_createFormWidget, 5000)
      .add(600, this._init_createButtons)
      .add(900, this._init_readyWidget);

    // Copy the DI from the Form Entity
    this.setDI(form.getDI());
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup Variables
    this.__formEntity = null;
    this.__form = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __formEntity: null,
    __form: null,
    /*
     ***************************************************************************
     PUBLIC METHODS
     ***************************************************************************
     */
    /**
     * Get the Form Entity
     * 
     * @return {meta.api.entity.IForm} Form Entity
     */
    getFormEntity: function() {
      return this.__formEntity;
    },
    /**
     * Get the Form Entity
     * 
     * @return {meta.api.ui.IWidget} Form Widget
     */
    getForm: function() {
      return this.__form;
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
      // Create Console's Input Area
      var container = new qx.ui.container.Composite();

      // Define Layout
      container.setLayout(new qx.ui.layout.VBox());
      return container;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Intialization Functions)
     ***************************************************************************
     */
    _init_createFormWidget: function(parameters) {
      // Create Form Widget
      var factory = this.getDI().get('widgetfactory');
      var widget = factory.create(this.__formEntity);

      widget.addListenerOnce("widget-ready", function(e) {
        if (e.getOK()) {
          parameters['_form'] = widget;
          this._init_functions.next(parameters);
        } else {
          this._init_functions.abort(e.getMessage());
        }
      }, this);

      // Initialize the Widget
      widget.initialize();
    },
    _init_createButtons: function(parameters) {

      // Create Submit and Cancel Buttons
      var submit = new qx.ui.form.Button("Submit");
      submit.addListener("execute", function(e) {
        this._mx_med_fireEventOK("form-submit", this);
      }, this);
      parameters['_submit'] = submit;

      var cancel = new qx.ui.form.Button("Cancel");
      cancel.addListener("execute", function(e) {
        this._mx_med_fireEventNOK("form-submit", this);
      }, this);
      parameters['_cancel'] = cancel;

      return parameters;
    },
    _init_readyWidget: function(parameters) {
      // Save the Form Widget Reference
      this.__form = parameters._form;

      // Add Form to Container
      parameters._widget.add(parameters._form.getWidget());

      // Add Optional Buttons
      if (parameters.hasOwnProperty('_cancel')) {
        if (parameters.hasOwnProperty('_submit')) {
          // Create Console's Input Area
          var container = new qx.ui.container.Composite();

          // Define Layout
          container.setLayout(new qx.ui.layout.HBox());

          // Add Buttons (to Container)
          container.add(parameters._cancel);
          container.add(parameters._submit);

          // Add Button Containers to Form Container
          parameters._widget.add(container);
        } else {
          // Add Cancel Button to Form Container
          parameters._widget.add(parameters._cancel);
        }
      } else if (parameters.hasOwnProperty('_submit')) {
        // Add Submit Button to Form Container
        parameters._widget.add(parameters._submit);
      }

      return parameters;
    }
  } // SECTION: MEMBERS
});
