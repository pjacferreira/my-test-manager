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
qx.Interface.define("meta.api.ui.IWidgetTransformation", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Apply State Transformation to the Widget
     * 
     * @abstract
     * @return {Boolean} Returns <code>true</code> when the widget transformation applied successfully.
     */
    applyTransformation: function() {
    }
  } // SECTION: MEMBERS
});
