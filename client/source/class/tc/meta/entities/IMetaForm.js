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

qx.Interface.define("tc.meta.entities.IMetaForm", {
  extend: [tc.meta.entities.IMetaEntity],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is this a Read Only Form?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
    },
    /**
     * Returns the Form's Title
     *
     * @abstract
     * @return {String} Form Title
     */
    getTitle: function() {
    },
    /**
     * Returns the number of field groups in the form. The form should always
     * have at the very minimum 1 group.
     *
     * @abstract
     * @return {Integer} Number of Groups
     */
    getGroupCount: function() {
    },
    /**
     * Returns the Label for the Group. If the group does not have a label,
     * NULL will be returned.
     *
     * @abstract
     * @param group {Integer} Group ID 0 to getGroupCount() -1 
     * @return {String ? null} Group Label
     */
    getGroupLabel: function(group) {
    },
    /**
     * Returns the list of fields in the Group.
     *
     * @abstract
     * @param group {Integer} Group ID
     * @return {String[]} Array of Field IDs in the Group
     */
    getGroupFields: function(group) {
    },
    /**
     * Does the Form Allow the Service Indicated by the Alias?
     *
     * @abstract
     * @param alias {String} Form Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
    },
    /**
     * Returns the Service ID on the Service Alias (alias is one of 'create', 
     * 'read', 'update', 'delete')
     *
     * @abstract
     * @param alias {String} Form Service Alias
     * @return {String ? null} Return Metadata or NULL on Failure
     */
    getService: function(alias) {
    },
    /**
     * Does the Form require Validation for the Field?
     *
     * @abstract
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFieldValidation: function(field) {
    },
    /**
     * Does the form have global validation?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFormValidation: function() {
    },
    /**
     * Get Field Validation Function
     * NOTE: Field Validation Function, follows the following template
     * function (field_value) {
     *  ...
     *  return pass_of_fail; // BOOLEAN
     * }
     *
     * @abstract
     * @param field {String} Field ID
     * @return {Function} Returns Field Validation Function, or NULL if field does not exist/has no validation
     */
    getFieldValidation: function(field) {
    },
    /**
     * Get Form Validation Function
     * NOTE: Form Validation Function, follows the following template
     * function (mapFieldValue) {
     *  ...
     *  return pass_of_fail; // BOOLEAN
     * }
     *
     * @abstract
     * @return {Function} Returns Form Validation Function, or NULL if field does not exist/has no validation
     */
    getFormValidation: function() {
    },
    /**
     * Does the field have a Transformation Function?
     *
     * @abstract
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFieldTransform: function(field) {
    },
    /**
     * Does the form have global Transformation Function?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFormTransform: function() {
    },
    /**
     * Get Field Transformation Function
     * NOTE: Field Transformation Function, follows the following template
     * function (field_value) {
     *  ...
     *  return new_value; // BOOLEAN
     * }
     *
     * @abstract
     * @param field {String} Field ID
     * @return {Function} Returns Field Validation Function, or NULL if field does not exist/has no transformation
     */
    getFieldTransform: function(field) {
    },
    /**
     * Get Form Transformation Function
     * NOTE: Form Transformation Function, follows the following template
     * function (mapFieldValue) {
     *  ...
     *  return modifiedFieldValueMap; // Object Containing Only Modified Field Value Tuplets
     * }
     *
     * @abstract
     * @return {Function} Returns Form Transformation Function, or NULL if field does not exist/has no transformation
     */
    getFormTransform: function() {
    }
  }
});
