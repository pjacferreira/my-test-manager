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
 * Implement a Group Layout Strategy
 */
qx.Class.define("meta.ui.layouts.LayoutBasic", {
  extend: qx.core.Object,
  implement: meta.api.ui.IWidgetLayout,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Layout Constructor
   * 
   * @param group {meta.api.ui.IGroup} Group
   * @param add {Function} Function [prototype: function(container, widget) {}] called,
   *   in the context of 'group', to add the Widget to Display Container
   */
  construct: function(group, add) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(group, meta.api.ui.IGroup, "[group] Is not of the expected type!");
      qx.core.Assert.assertFunction(add, "[add] Is not of the expected type!");
    }

    // Save Group
    this.__group = group;
    this.__addWidget = add;
  },
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__group = null;
    this.__widgets = null;
    this.__addWidget = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __group: null,
    __widgets: null,
    __addWidget: null,
    /*
     ***************************************************************************
     INTERFACE METHODS ( meta.api.ui.IWidgetLayout)
     ***************************************************************************
     */
    /**
     * Retrieve the Widget Associated with this Layout.
     * 
     * @return { meta.api.ui.IWidget} Associated Widget.
     */
    getWidgets: function() {
      return this.__widgets;
    },
    /**
     * Perform the Layout operation for the Group.
     * 
     * @param container {qx.ui.core.Widget} Container Widget
     */
    doLayout: function(container) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertQxWidget(container, "[container] Is not of the expected type!");
      }

      // Get Group Entity
      var entity = this.__group.getEntity();

      // Set Base Layout
      var vertical = true;

      // Does then entity specify a layout orientation?
      if (qx.lang.Type.isFunction(container.setLayout)) {
        var options = entity.getOptions();
        if ((options !== null) &&
          options.hasOwnProperty('orientation')) { // YES
          vertical = options.vertical === 'vertical';
        }
        container.setLayout(vertical ? new qx.ui.layout.VBox(5) : new qx.ui.layout.HBox(5));
      }

      // Clear List of Widgets in the Layout
      this.__widgets = [];

      // Get Entity and Layout
      var layout = entity.getLayout();

      // Does the entity have a valid layout definition
      if ((layout !== null) &&
        qx.lang.Type.isArray(layout) &&
        layout.length) { // YES

        // Cycle through the widgets
        var display, widget;
        for (var i = 0; i < layout.length; ++i) {
          // Were we able to retrieve a widget?
          widget = this.__group.getChild(layout[i]);
          if (widget !== null) { // YES
            // Get Display Widget?
            display = this._getDisplay(widget);
            if (display !== null) { // YES
              this.__widgets.push(layout[i]);
              this.__addWidget.call(this.__group, container, display);
            }
          }
        }

        if (this.__widgets.length) {
          return true;
        }
      }
      return false;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Helper)
     ***************************************************************************
     */
    _getDisplay: function(widget) {
      var entity = widget.getEntity();
      var type = entity.getType();

      // Does this object have a Handler for the Entity Type?
      var handler = "_get" + (type.charAt(0).toUpperCase() + type.slice(1)) + "Display";
      if (this[handler] && qx.lang.Type.isFunction(this[handler])) { // YES
        handler = this[handler];
        return handler.call(this, widget, entity);
      }

      return widget.getWidget();
    },
    _getFieldDisplay: function(widget, field) {
      // Create a Composite for the Field Display
      var composite = new qx.ui.container.Composite();

      // Set a Horizontal Layout (with a spacing of 5)
      composite.setLayout(new qx.ui.layout.HBox(5));

      // Get Field Components
      var label = new qx.ui.basic.Label(field.getLabel() + ":");
      var input = widget.getWidget();

      // Set Label's Buddy so that it accompanies Enable/Disable State of Input
      label.setBuddy(input);

      // Setup Field Display (Field: DISPLAY)
      composite.add(label);
      composite.add(input);
      return composite;
    }
  } // SECTION: MEMBERS
});
