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

/**
 * Generic Form Model
 */
qx.Class.define("tc.meta.entities.FormEntity", {
  extend: tc.meta.entities.BaseEntity,
  implement: tc.meta.entities.IMetaForm,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for a Form Entity
   * 
   * @param id {String} Form ID
   * @param metadata {Object} Form Metadata
   */
  construct: function(id, metadata) {
    this.base(arguments, 'form', id);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('title'), "[metadata] Is Missing Required Property [title]!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('fields') &&
              qx.lang.Type.isArray(metadata['fields']), "[metadata] is Missing or has an Invalid [fields] Definition!");
    }

    this.__oMetaData = qx.lang.Object.clone(metadata, true);
    this.__oMetaDataFields = this.__oMetaData.hasOwnProperty('fields') ? this.__oMetaData['fields'] : null;
    this.__oMetaDataServices = this.__oMetaData.hasOwnProperty('services') ? this.__oMetaData['services'] : null;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__oMetaDataFields = null;
    this.__oMetaDataServices = null;
    this.__oMetaData = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Field Meta Data
    __oMetaData: null,
    __oMetaDataFields: null,
    __oMetaDataServices: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Is this a Read Only Form?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
      if (this.__oMetaDataServices !== null) {
        return !(this.__oMetaDataServices.hasOwnProperty('create') ||
                this.__oMetaDataServices.hasOwnProperty('update'));
      }

      return true;
    },
    /**
     * Returns the Form's Title
     *
     * @return {String} Form's Title
     */
    getTitle: function() {
      return this.__oMetaData['title'];
    },
    /**
     * Returns the number of field groups in the form. The form should always
     * have at the very minimum 1 group.
     *
     * @return {Integer} Number of Groups
     */
    getGroupCount: function() {
      return this.__oMetaDataFields !== null ? this.__oMetaDataFields.length : 0;
    },
    /**
     * Returns the Label for the Group. If the group does not have a label,
     * NULL will be returned.
     *
     * @param group {Integer} Group ID 0 to getGroupCount() -1 
     * @return {String ? null} Group Label
     */
    getGroupLabel: function(group) {
      if ((group >= 0) && (group < this.getGroupCount())) {
        var entry = this.__oMetaDataFields[group];
        if (qx.lang.Type.isObject(entry)) {
          return tc.util.Object.getFirstProperty(entry);
        }
      }

      return null;
    },
    /**
     * Returns the list of fields in the Group.
     *
     * @param group {Integer} Group ID
     * @return {String[]} Array of Field IDs in the Group
     */
    getGroupFields: function(group) {
      if ((group >= 0) && (group < this.getGroupCount())) {
        var entry = this.__oMetaDataFields[group];
        if (qx.lang.Type.isObject(entry)) {
          var label = tc.util.Object.getFirstProperty(entry);
          return qx.lang.Type.isArray(entry[label]) ? entry[label] : null;
        } else if (qx.lang.Type.isArray(entry)) {
          return entry;
        }
      }

      return null;
    },
    /**
     * Does the Form Allow the Service Indicated by the Alias?
     *
     * @param alias {String} Form Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
      return this.__oMetaDataServices !== null ? this.__oMetaDataServices.hasOwnProperty(alias) : false;
    },
    /**
     * Returns the Service ID on the Service Alias (alias is one of 'create', 
     * 'read', 'update', 'delete')
     *
     * @param alias {String} Form Service Alias
     * @return {String} Return Service ID or NULL on Failure
     */
    getService: function(alias) {
      return (this.__oMetaDataServices !== null) && this.__oMetaDataServices.hasOwnProperty(alias) ? this.__oMetaDataServices[alias] : null;
    },
    /**
     * Does the Form require Validation for the Field?
     *
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFieldValidation: function(field) {
      // TODO Implement
      return false;
    },
    /**
     * Does the form have global validation?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFormValidation: function() {
      // TODO Implement
      return false;
    },
    /**
     * Get Field Validation Function
     * NOTE: Field Validation Function, follows the following template
     * function (field_value) {
     *  ...
     *  return pass_of_fail; // BOOLEAN
     * }
     *
     * @param field {String} Field ID
     * @return {Function} Returns Field Validation Function, or NULL if field does not exist/has no validation
     */
    getFieldValidation: function(field) {
      // TODO Implement
      return null;
    },
    /**
     * Get Form Validation Function
     * NOTE: Form Validation Function, follows the following template
     * function (mapFieldValue) {
     *  ...
     *  return pass_of_fail; // BOOLEAN
     * }
     *
     * @return {Function} Returns Form Validation Function, or NULL if field does not exist/has no validation
     */
    getFormValidation: function() {
      // TODO Implement
      return null;
    },
    /**
     * Does the field have a Transformation Function?
     *
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFieldTransform: function(field) {
      // TODO Implement
      return false;
    },
    /**
     * Does the form have global Transformation Function?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasFormTransform: function() {
      // TODO Implement
      return false;
    },
    /**
     * Get Field Transformation Function
     * NOTE: Field Transformation Function, follows the following template
     * function (field_value) {
     *  ...
     *  return new_value; // BOOLEAN
     * }
     *
     * @param field {String} Field ID
     * @return {Function} Returns Field Validation Function, or NULL if field does not exist/has no transformation
     */
    getFieldTransform: function(field) {
      // TODO Implement
      return null;
    },
    /**
     * Get Form Transformation Function
     * NOTE: Form Transformation Function, follows the following template
     * function (mapFieldValue) {
     *  ...
     *  return modifiedFieldValueMap; // Object Containing Only Modified Field Value Tuplets
     * }
     *
     * @return {Function} Returns Form Transformation Function, or NULL if field does not exist/has no transformation
     */
    getFormTransform: function() {
      // TODO Implement
      return null;
    }
  }
});
