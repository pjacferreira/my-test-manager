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

qx.Class.define("meta.ui.FormTabs", {
  extend: meta.ui.FormBasic,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Tabs UI Form Constructor
   * 
   * @param form {meta.api.ui.IForm} Field Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(form, parent) {
    // Initialize Base Widget
    this.base(arguments, form, parent);

    // Set the Default Group Layout
    this.setLayout(new meta.ui.layouts.LayoutTabs(this));
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
     PROTECTED METHODS (Intialization Functions)
     ***************************************************************************
     */
    _init_performLayout: function(parameters) {
      parameters['_widget'] = new qx.ui.tabview.TabView();

      // Call the Base Method
      return this.base(arguments, parameters);
    },
    /*
     ***************************************************************************
     IMPLEMENTATION of ABSTRACT METHODS (meta.ui.AbstractGroup)
     ***************************************************************************
     */
    /**
     * Clear Display Container of Widgets
     * 
     * @param container {qx.ui.core.Widget} Widget Container
     */
    _clearContainer: function(container) {
      // Remove All Tab Pages from the Tab View
      var pages = container.getChildren();
      for (var i = 0; i < pages.length; ++i) {
        container.remove(pages[i]);
      }
    }
  } // SECTION: MEMBERS
});
