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
qx.Class.define("meta.ui.layouts.LayoutTabs", {
  extend: qx.core.Object,
  implement: meta.api.ui.IWidgetLayout,
  /*
   *******************s**********************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Perform Layout
   * 
   * @param group {meta.api.ui.IGroup} Group
   */
  construct: function(group) {
    if (qx.core.Environment.get("qx.debug")) {
//      qx.core.Assert.assertInterface(group, meta.api.ui.IGroup, "[group] Is not of the expected type!");
    }

    // Save Group
    this.__group = group;
  },
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__group = null;
    this.__widgets = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __group: null,
    __widgets: null,
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
     * @param container {qx.ui.container.Composite} Composite Container for Widgets
     */
    doLayout: function(container) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInstance(container, qx.ui.tabview.TabView, "[container] Is not of the expected type!");
      }

      // Clear List of Widgets in the Layout
      this.__widgets = [];

      // Get Entity and Layout
      var group = this.__group.getEntity();
      var layout = group.getLayout();

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
            display = widget.getWidget();
            if (display !== null) { // YES
              this.__widgets.push(layout[i]);
              container.add(this._createTabPage(widget));
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
    _createTabPage: function(widget) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(widget, meta.api.ui.IWidget, "[widget] Is not of the expected type!");
      }

      // Create TAB Page
      var title = widget.getLabel();
      var page = new qx.ui.tabview.Page(title !== null ? title : widget.getID() /* TODO: ICON */);
      // NEED TO Set a Layout for the Page
      page.setLayout(new qx.ui.layout.Grow());

      // Add Contents      
      page.add(widget.getWidget());

      // Associate the Meta Form with the Page
      widget['$$meta-tab-page'] = page;

      return page;
    }
  } // SECTION: MEMBERS
});
