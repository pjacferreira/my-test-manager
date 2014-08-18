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

qx.Class.define("meta.ui.FormBasic", {
  extend: meta.ui.ContainerGroup,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Basic UI Form Constructor
   * 
   * @param form {meta.api.entity.IForm} Form Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(form, parent) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(form, meta.api.entity.IForm, "[form] Is not of the expected type!");
    }

    // Initialize Base Widget
    this.base(arguments, form, parent);
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
