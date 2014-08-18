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

qx.Class.define("meta.ui.ContainerGroup", {
  extend: meta.ui.AbstractGroup,
  type: "abstract",
  implement: [
    meta.api.ui.IWidgetValidation,
    meta.api.ui.IWidgetTransformation
  ],
  include: [
    meta.ui.mixins.MContainerGroup,
    meta.ui.mixins.MValidatorGroup
//    meta.api.itw.mixins.MTransformGroup
  ],
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
    // Initialize Base Widget
    this.base(arguments, group, parent);

    // Set the Default Group Layout
    this.setLayout(new meta.ui.layouts.LayoutBasic(this, this._addWidget));

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
      return this._mx_cg_count();
    },
    /**
     * Get a Specific Widget from the Group.
     * 
     * @param id {String} Widget ID
     * @return { meta.api.ui.IWidget|null} Requested Widget or NULL if it doesn't exist
     */
    getChild: function(id) {
      return this._mx_cg_getWidget(id);
    },
    /**
     * Retrieve the List of Widget IDs in the Group.
     * 
     * @return {String[]} List of Widget IDs
     */
    getChildren: function() {
      return this._mx_cg_listIDs();
    },
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IWidgetValidation)
     ***************************************************************************
     */
    /**
     * Is the Widgets Current State Valid?
     * 
     * @return {Boolean} Returns <code>true</code> when the widget is in a valid state.
     */
    isValidState: function() {
      var entity = this.getEntity();

      // TODO Use Value Type (integer, decimal, string, date, etc.) as part of the validation process
      return this._mx_vg_staticValidation(entity) &&
        this._mx_vg_dynamicValidation(entity);
    },
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IWidgetValidation)
     ***************************************************************************
     */
    /**
     * Apply State Transformation to the Widget
     * 
     * @return {Boolean} Returns <code>true</code> when the widget transformation applied successfully.
     */
    applyTransformation: function() {
      // Cycle through the widgets applying transformation to each one...
      var widget, widgets = this.getChildren();
      for (var i = 0; i < widgets.length; ++i) {
        widget = this.getChild(widget[i]);

        // Does the widget have Transformation?
        if (qx.lang.Type.isFunction(widget.applyTransformation)) { // YES
          // Was the transformation applied successfully?
          if (widget.applyTransformation()) { // NO: Abort
            return false;
          }
        }
      }

      // All Widget have their transformation applied
      return true;
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
          return parameters;
        }
      }

      throw "Container [" + this.getID() + "] failed to complete it's widgets layout";
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Helper : Used Layout by Layout Manager)
     ***************************************************************************
     */
    /**
     * Add a Display Widget to Display Container during layout
     * 
     * @abstract
     * @param container {qx.ui.core.Widget} Widget Container
     * @param widget {qx.ui.core.Widget} Display Widget 
     * @param options {Map?null} Layout Options
     */
    _addWidget: function(container, widget, options) {
    },
    /**
     * Clear Display Container of Widgets
     * 
     * @abstract
     * @param container {qx.ui.core.Widget} Widget Container
     */
    _clearContainer: function(container) {
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (meta.api.itw.mixins.MGroupContainer)
     ***************************************************************************
     */
    _mx_cg_postAdd: function(widget) {
      // Add Widget to Input and Output Maps (if required)
      this._mx_gim_addWidget(widget);
      this._mx_gom_addWidget(widget);
    },
    _mx_cg_postRemove: function(widget) {
      // Remove Widget from Input and Output Map (if required)
      this._mx_gim_removeWidget(widget);
      this._mx_gom_removeWidget(widget);
    }
  } // SECTION: MEMBERS
});
