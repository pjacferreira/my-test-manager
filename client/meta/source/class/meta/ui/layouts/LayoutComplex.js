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
qx.Class.define("meta.ui.layouts.LayoutComplex", {
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
    __columns: null,
    __rows: null,
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

      // Clear List of Widgets in the Layout
      this.__widgets = [];
      this.__columns = 0;
      this.__rows = [];

      // Get Entity and Layout
      var layout = entity.getLayout();

      // Does the entity have a valid layout definition
      if ((layout !== null) &&
        qx.lang.Type.isArray(layout) &&
        layout.length) { // YES

        // Extract and Expand All Widgets to be Displayed
        var widget;
        for (var i = 0; i < layout.length; ++i) {
          // Were we able to retrieve a widget?
          widget = this.__group.getChild(layout[i]);
          if (widget !== null) { // YES
            if (this._explodeWidget(widget)) {
              this.__widgets.push(layout[i]);
            }
          }
        }

        // Do we have anything to display?
        if (this.__rows.length) { // YES
          // Is it a single column display?
          if (this.__columns === 1) { // YES
            this._doSingleColumnLayout(container, this.__rows);
          } else { // NO: Multi Column Display
            this._doGridLayout(container, this.__rows, this.__columns);
          }
        }

        // Do we have widgets to display?
        if (this.__widgets.length) { // YES
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
    _doSingleColumnLayout: function(container, rows) {
      // Set Base Layout
      var vertical = true;

      // Set Layout Manager for container;
      if (qx.lang.Type.isFunction(container.setLayout)) {
        var options = this.__group.getEntity().getOptions();
        if ((options !== null) &&
          options.hasOwnProperty('orientation')) { // YES
          vertical = options.vertical === 'vertical';
        }
        container.setLayout(vertical ? new qx.ui.layout.VBox(5) : new qx.ui.layout.HBox(5));
      }

      // Cycle through the widgets and add them to the container
      for (var i = 0; i < rows.length; ++i) {
        this.__addWidget.call(this.__group, container, rows[i][0], null);
      }
    },
    _doGridLayout: function(container, rows, columns) {
      // Set Grid Layout
      container.setLayout(new qx.ui.layout.Grid(5, 5));

      // Cycle through the rows of widgets and add them to the container
      var row, span;
      for (var i = 0; i < rows.length; ++i) {
        row = rows[i];
        // Is the row composed of only a single widget?
        if (row.length === 1) { // YES
          this.__addWidget.call(this.__group, container, rows[i][0],
            {row: i, column: 0, rowSpan: columns});
        } else { // NO          
          // Cycle the widgets in the row
          var span;
          for (var j = 0; j < row.length; ++j) {
            span = (j === 0) ? columns - row.length : 1;
            this.__addWidget.call(this.__group, container, rows[i][j],
              {row: i, column: j, rowSpan: span});
          }
        }
      }
    },
    _explodeWidget: function(widget) {
      var entity = widget.getEntity();
      var type = entity.getType();

      // Does this object have a Handler for the Entity Type?
      var handler = "_explode" + (type.charAt(0).toUpperCase() + type.slice(1)) + "Display";
      if (this[handler] && qx.lang.Type.isFunction(this[handler])) { // YES
        handler = this[handler];
        return handler.call(this, widget, entity);
      }
      // ELSE: NO: Specific Handler for the Widget 

      // Do we have a Display for the Widget?
      var display = widget.getWidget();
      if (display !== null) { // YES
        this.__rows.push([widget.getWidget()]);

        // Is the current maximum number of columns less than 1?
        if (this.__columns < 1) { // YES
          this.__columns = 1;
        }

        return true;
      }

      return false;
    },
    _explodeGroupDisplay: function(widget, group) {
      // Do we have an input widget for the field?
      var container = widget.getWidget();
      if (container !== null) { // YES
        // Get Field Components
        var label = new qx.ui.basic.Label(group.getLabel() + ":");

        this.__rows.push([label]);
        this.__rows.push([container]);
        // Is the current maximum number of columns less than 2?
        if (this.__columns < 1) { // YES
          this.__columns = 1;
        }

        return true;
      }

      return false;
    },
    _explodeFieldDisplay: function(widget, field) {
      // Do we have an input widget for the field?
      var input = widget.getWidget();
      if (input !== null) { // YES
        // Get Field Components
        var label = new qx.ui.basic.Label(field.getLabel() + ":");
        // Set Label's Buddy so that it accompanies Enable/Disable State of Input
        label.setBuddy(input);

        this.__rows.push([label, input]);
        // Is the current maximum number of columns less than 2?
        if (this.__columns < 2) { // YES
          this.__columns = 2;
        }

        return true;
      }

      return false;
    }
  } // SECTION: MEMBERS
});
