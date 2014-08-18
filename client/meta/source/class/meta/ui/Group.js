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

qx.Class.define("meta.ui.Group", {
  extend: meta.ui.AbstractGroup,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Field<-->Input Widget Adaptor Constructor
   * 
   * @param group {meta.api.itw.meta.api.IContainer} Field Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(group, parent) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is not of the expected type!");
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
     EVENT HANDLERS
     ***************************************************************************
     */
    _processMetaChangeInputs: function(widget) {
      // Is this Widget a Child of ours?
      if (this._mx_cg_hasWidget()) { // YES
        // Handle Changes in Inputs
        this._mx_gim_notifyInputsChanges(widget);

        // Don't Propagate the Event
        return false;
      }

      // Not one of our Children, allow the event to propagate
      return true;
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
      return new qx.ui.container.Composite();
    },
    /*
     ***************************************************************************
     IMPLEMENTATION of ABSTRACT METHODS (meta.ui.AbstractGroup)
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
    }
  } // SECTION: MEMBERS
});
