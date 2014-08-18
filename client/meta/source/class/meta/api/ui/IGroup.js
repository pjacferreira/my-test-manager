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

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("meta.api.ui.IGroup", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  properties: {
    "layout": {}
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Label for Group
     *
     * @abstract
     * @return {String} Returns Group's Label.
     */
    getLabel: function() {
    },
    /**
     * Whether the widget contains children.
     *
     * @abstract
     * @return {Boolean} Returns <code>true</code> when the widget has children.
     */
    hasChildren: function() {
    },
    /**
     * Get a Specific Widget from the Group.
     * 
     * @abstract
     * @param id {String} Widget ID
     * @return { meta.api.ui.IWidget|null} Requested Widget or NULL if it doesn't exist
     */
    getChild: function(id) {
    },
    /**
     * Retrieve the List of Widget IDs in the Group.
     * 
     * @abstract
     * @return {String[]} List of Widget IDs
     */
    getChildren: function() {
    }
  }
});
