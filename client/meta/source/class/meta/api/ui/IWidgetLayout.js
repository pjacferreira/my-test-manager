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
 * Requirements for Communication Input/Output Communication Between Meta Widgets
 */
qx.Interface.define("meta.api.ui.IWidgetLayout", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Retrieve the Widget Associated with this Layout.
     * 
     * @abstract
     * @return { meta.api.ui.IWidget} Associated Widget.
     */
    getWidgets: function() {
    },
    /**
     * Perform the Layout operation for more complex forms.
     * 
     * @abstract
     */
    doLayout: function() {
    }
  } // SECTION: MEMBERS
});
