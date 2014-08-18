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

qx.Interface.define("meta.api.ui.IForm", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Title for Form.
     *
     * @abstract
     * @return {String} Returns Form's Title.
     */
    getTitle: function() {      
    },
    /**
     * Number of Groups in Form.
     * Note: A Form must contain atleast one Group
     *
     * @abstract
     * @return {Integer} Number of groups in the form.
     */
    getGroupCount: function() {
    },
    /**
     * Get a Specific Group from the Form.
     * 
     * @abstract
     * @param idx {Integer} Group Index
     * @return { meta.api.ui.IGroup|null} Requested Group or NULL if it doesn't exist
     */
    getGroup: function(idx) {
    }
  }
});
