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
qx.Mixin.define("meta.ui.mixins.MLayoutContainedGroup", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Contructor
   */
  construct: function() {

    // Attach Initialization Functions
    this._init_functions
      .add(800, this._mx_lcg_initDoLayout);
  },
  /**
   * Container Destructor
   */
  destruct: function() {
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     PROTECTED METHODS (Initialization Functions)
     ***************************************************************************
     */
    _mx_lcg_initDoLayout: function(parameters) {
      var container = parameters._widget;
      var widgets = parameters['widgets'];

      // Clear Container
      this._mx_lcg_clearContainer(parameters, container);

      // Get Container and Layout
      var group = this.getEntity();
      var layout = group.getLayout();

      if ((layout !== null) &&
        qx.lang.Type.isArray(layout) &&
        layout.length) {

        var widgets = parameters['widgets'];

        // Cycle through the widgets
        var widget, count = 0;
        for (var i = 0; i < layout.length; ++i) {
          widget = widgets.get(get(layout[i]));

          // Was the widget Created?
          if (widget !== null) { // YES
            // Add Widget Display
            this._mx_lcg_addWidget(parameters, container, widget);
            count++;
          }
        }

        if (count) {
          return parameters;
        }
      }

      throw "Group [" + group.getID() + "] has no widgets to display";
    }
    /*
     ***************************************************************************
     IMPLEMENTATION REQUIRED FUNCTIONS (to be implemented in container class)
     _mx_lcg_addWidget(parameters, container, widget);
     _mx_lcg_clearContainer(parameters, container);
     ***************************************************************************
     */
  } // SECTION: MEMBERS
});
