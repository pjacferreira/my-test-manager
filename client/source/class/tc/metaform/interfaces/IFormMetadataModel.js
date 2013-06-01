/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2013 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("tc.metaform.interfaces.IFormMetadataModel", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Retrieves Metadata Definition for the Form.
     *
     * @abstract
     * @return {Object} Form Definition Object or NULL|UNDEFINED if the Model has not been initialized.
     */
    getFormMeta: function() {
    },
    /**
     * Get the Form's Title.
     *
     * @abstract
     * @return {String|NULL} Form title or NULL|UNDEFINED if the Model has not been initialized.
     */
    getFormTitle: function() {
    },
    /**
     * Get the complete list of fields used in the form.
     *
     * @abstract
     * @return {Array|NULL} Field list array or NULL|UNDEFINED if the Model has not been initialized.
     */
    getFormFields: function() {
    },
    /**
     * Retrieves the count of the number of Groups in the form.
     *
     * @abstract
     * @return {Integer} Count of number of Groups in the Form or 0 the Model has not been initialized.
     */
    getGroupCount: function() {
    },
    /**
     * Retrieves a Single Group's Label.
     * Note: Label can be NULL (in which case, no Group Label is required)
     *
     * @abstract
     * @param index {Integer} Index of Field that we want the label for ( 0 .. getGRoupCount()-1).
     * @return {String|NULL} Group labelor NULL|UNDEFINED if invalid index or the Model has not been initialized.
     */
    getGroupLabel: function(index) {
    },
    /**
     * Retrieves a Single Group's Field List.
     *
     * @abstract
     * @param index {Integer} Index of Field that we want the list for ( 0 .. getGRoupCount()-1).
     * @return {Array|NULL} Field list array or NULL|UNDEFINED if invalid index, or the Model has not been initialized.
     */
    getGroupFields: function(index) {
    }
  }
});
