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

qx.Class.define("meta.ui.ContainedGroup", {
  extend: meta.ui.AbstractGroup,
  include: [
    meta.ui.mixins.MContainerContainedGroup
  ],
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Field<-->Input Widget Adaptor Constructor
   * 
   * @param group {meta.api.entity.IContainer} Group Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(group, parent) {
    // Initialize Base Widget
    this.base(arguments, group, parent);
    
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is not of the expected type!");
    }


    // Set the Default Group Layout
    switch (group.getWidgetType()) {
      case 'toolbar' :
        this.setLayout(new meta.ui.layouts.LayoutBasic(this, this._addWidget));
        break;
      default:
        this.setLayout(new meta.ui.layouts.LayoutComplex(this, this._addWidget));
    }

    // Setup Local Init Functions
    this._init_functions
      .add(900, this._init_performLayout);
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
     * Whether the widget contains children.
     *
     * @return {Boolean} Returns <code>true</code> when the widget has children.
     */
    hasChildren: function() {
      return this._mx_ccg_count();
    },
    /**
     * Get a Specific Widget from the Group.
     * 
     * @param id {String} Widget ID
     * @return { meta.api.ui.IWidget|null} Requested Widget or NULL if it doesn't exist
     */
    getChild: function(id) {
      return this._mx_ccg_getWidget(id);
    },
    /**
     * Retrieve the List of Widget IDs in the Group.
     * 
     * @return {String[]} List of Widget IDs
     */
    getChildren: function() {
      return this._mx_ccg_listIDs();
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Intialization Functions)
     ***************************************************************************
     */
    _init_performLayout: function(parameters) {
      var container = parameters._widget;

      // Clear Container
      this._clearContainer(container);

      // Has the Layout Algorithm been defined?
      var layout = this.getLayout();
      if (layout !== null) { // YES
        // Did the Container Layout Succeed?
        if (layout.doLayout(container)) { // YES
          // Apply New Widget
          this._setWidget(container);

          return parameters;
        }
      }

      throw "Container [" + this.getID() + "] failed to complete it's widgets layout";
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
      // Get the Field Entity
      var entity = this.getEntity();

      // Create Widget
      var widget;
      switch (entity.getWidgetType()) {
        case 'toolbar' :
          widget = new qx.ui.toolbar.ToolBar();
          break;
        default:
          widget = new qx.ui.container.Composite();
      }

      // Widget is Initially Disabled
      widget.setEnabled(false);

      return widget;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Helper : Layout)
     ***************************************************************************
     */
    /**
     * Add a Display Widget to Display Container during layout
     * 
     * @param container {qx.ui.core.Widget} Widget Container
     * @param widget {qx.ui.core.Widget} Display Widget 
     * @param options {Map?null} Layout Options
     */
    _addWidget: function(container, widget, options) {
      container.add(widget, options);
    },
    /**
     * Clear Display Container of Widgets
     * 
     * @param container {qx.ui.core.Widget} Widget Container
     */
    _clearContainer: function(container) {
      container.removeAll();
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (meta.api.itw.mixins.MGroupContainer)
     ***************************************************************************
     */
    _mx_ccg_postAdd: function(widget) {
      // Add Widget to Input and Output Maps (if required)
      this._mx_gim_addWidget(widget);
      this._mx_gom_addWidget(widget);
    },
    _mx_ccg_postRemove: function(widget) {
      // Remove Widget from Input and Output Map (if required)
      this._mx_gim_removeWidget(widget);
      this._mx_gom_removeWidget(widget);
    }
  } // SECTION: MEMBERS
});
