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

qx.Class.define("meta.ui.AbstractWidget", {
  extend: qx.core.Object,
  implement: [
    meta.api.ui.IWidget,
    utility.api.di.IInjectable
  ],
  include: [
    meta.events.mixins.MMetaEventDispatcher,
    utility.mixins.di.MInjectable
  ],
  type: "abstract",
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Event Fired on Success or Failure of Widget Initialization
     */
    "widget-ready": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Class for Meta Widgets
   * 
   * @param entity {meta.api.entity.IEntity} Meta Entity Associated with Display Widget
   * @param parent {meta.api.ui.IGroup?null} Parent Widget
   */
  construct: function(entity, parent) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(entity, meta.api.entity.IEntity, "[entity] Is not of the expected type!");
      if (parent != null) {
        qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is not of the expected type!");
      }
    }

    // Setup Initialization Sequence
    /* IMPLEMENTATION NOTES on SEQUENCE NUMBERS:
     * 1-99 : Reserved for 
     * <500 : Widget Display Not Created,
     * >599 : Widget Display Created
     */

    this._init_functions = new utility.SequencedCallbacks(this._init_ready, this._init_not_ready, this);
    this.__entity = entity;
    this.__parent = parent;

    // Does the Entity have the DI Set?
    if (this.hasDI()) { // YES: Copy It.
      this.setDI(this.__entity.getDI());
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup Variables
    this.__entity = null;
    this.__parent = null;
    this.__widget = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _init_functions: null,
    __parent: null,
    __entity: null,
    __widget: null,
    __ready: false,
    /*
     ***************************************************************************
     METHODS (meta.api.widgets.IWidget)
     ***************************************************************************
     */
    /**
     * Retrieve the ID of the Widget
     * 
     * @return {String} Widget ID
     */
    getID: function() {
      return this.__entity.getID();
    },
    /**
     * Retrieve the Meta Entity Associated with the Widget
     * 
     * @return {meta.api.entities.IEntity} Meta Entity for Widget
     */
    getEntity: function() {
      return this.__entity;
    },
    /**
     * Retrieve the Meta Entity Associated with the Widget
     * 
     * @return { meta.api.ui.IWidget|null} Parent Widget or null if no parent
     */
    getParent: function() {
      return this.__parent;
    },
    /**
     * Retrieve the Displayable Content of the Widget
     * 
     * @return {qx.ui.core.Widget} A Widget or Container, 'null' if widget is not ready
     */
    getWidget: function() {
      return this.__widget;
    },
    /**
     * Is Widget Ready for Use?
     * 
     * @return {Boolean} 'true' Widget is Ready, 'false' otherwise 
     */
    isReady: function() {
      return this.__ready;
    },
    /**
     * Initialize the Widget. Only after the widget has been initialized should
     * getWidget() be called.
     * 
     * @param options {Var} any initialization options to pass in
     * @return {Boolean} 'true' Widget is Ready, 'false' otherwise 
     */
    initialize: function(options) {
      // Initialize Parameters Container for Initialization Functions
      var parameters = {};
      if (options != null) {
        parameters['options'] = options;
      }

      /* Create the Place Holder, so that the a parent's widget display can be
       * created, independently of the success or failure of the 
       */
      var placeholder = this._createPlaceholder(options);

      // To start: Pass in the placeholder, and use it as the display widget
      parameters['_placeholder'] = placeholder;
      parameters['_widget'] = placeholder;

      // Set the Placaholder
      this._setWidget(placeholder);

      // Defer execution of the Initialization Functions
      parameters = this._pre_init_execute(parameters);
      this._init_functions.execute(parameters, 100);

      // Return the Placeholder
      return placeholder;
    },
    /*
     ***************************************************************************
     ABSTRACT METHODS (Helper)
     ***************************************************************************
     */
    /**
     * Create a Place Holder Widget, to be used, until real widget is built.
     * 
     * @abstract
     * @param options {Map} Widget Container
     * @return {qx.ui.core.Widget} Placeholder Widget
     */
    _createPlaceholder: function(options) {
    },
    /*
     ***************************************************************************
     OVERRIDABLE METHODS
     ***************************************************************************
     */
    _pre_init_execute: function(parameters) {
      return parameters;
    },
    _do_init_ready: function(parameters) {
      // Make sure we have the correct widget set
      this._setWidget(parameters._widget);

      // Mark Widget as Ready
      this.__ready = true;

      // Fire Event
      this._mx_med_fireEventOK("widget-ready", this);
    },
    _do_init_not_ready: function(parameters, message) {
      // Mark Widget as Not Ready
      this.__ready = false;

      // Fire Event
      this._mx_med_fireEventNOK("widget-ready", this, message);
    },
    /*
     ***************************************************************************
     HELPER METHODS
     ***************************************************************************
     */
    _init_ready: function(parameters) {
      this._do_init_ready(parameters);
    },
    _init_not_ready: function(parameters, message) {
      this._do_init_not_ready(parameters, message);
    },
    _getPlaceHolder: function() {
      return this.__ready ? this.getWidget() : this.__widget;
    },
    /**
     * Set the Display Widget for this Object.
     *
     * @param widget {qx.ui.core.Widget} The displayble widget
     * @return {qx.ui.core.Widget} Return's the Previous Display Widget or 'null' if no previous widget
     */
    _setWidget: function(widget) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInstance(widget, qx.ui.core.Widget, "[widget] Should be a descendant of [qx.ui.core.Widget]!");
      }

      // Get the Previous Display Widget
      var old_widget = this.__widget;

      // Save the Widget Display and Associate it with this Object
      this.__widget = widget;
      this.__widget['$$meta-adaptor'] = this;

      if (this.__ready) {
        // Fire Widget modification Event
        this._fire_widget_modified(old_widget);
      }

      return old_widget;
    }
  } // SECTION: MEMBERS
});
