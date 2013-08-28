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

qx.Interface.define("tc.meta.models.IFormModel", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a new Meta Model has been initialized.
     */
    "ok": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "nok": "qx.event.type.Data",
    /**
     * Fired when any time Field Values are been modified. 
     * The returned data is:
     * 1. A string, with the name of the field modified, if a single field is
     *    modified, or
     * 2. An array of strings, containing the list of fields modified, if more than
     *    one field is modified.
     */
    "fields-changed": "qx.event.type.Data",
    /**
     * Fired when the Form's Data has Been Loaded from Any Backend Source
     */
    "loaded": "qx.event.type.Event",
    /**
     * Fired when the Form's Data has Been Saved to Any Backend Source
     */
    "saved": "qx.event.type.Event",
    /**
     * Fired when the Form's Data has Been Erased from Any Backend Source
     */
    "erased": "qx.event.type.Event"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Initialize the model.
     *
     * @param iv {iv ? null} Model's Fields Initialization Values.
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    initialize: function(iv, callback) {
    },
    /**
     * Can we use the Data Model?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReady: function() {
    },
    /*
     *****************************************************************************
     FIELD (GENERAL PROPERTIES) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Return the type of the value for the field.
     * Note: The return value should be ONE OF the Following Values:
     * 'boolean'
     * 'date'    | 'time'    | 'datetime'
     * 'integer' | 'decimal'
     * 'text'    | 'html'
     *
     * @abstract
     * @param field {String} Field ID
     * @return {String} Field Type
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldType: function(field) {
    },
    /**
     * Is this a KEY Field (A Field whose value can be used to uniquely identify
     * a record)?
     * Note: Key Fields cannot be NULL
     * 
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isKeyField: function(field) {
    },
    /**
     * Return's a Field Label
     *
     * @abstract
     * @param field {String} Field ID
     * @return {String} Field Label
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldLabel: function(field) {
    },
    /**
     * Returns a description of the Field, if any is defined.
     *
     * @abstract
     * @param field {String} Field ID
     * @return {String} Field Description String or NULL (if not defined)
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldDescription: function(field) {
    },
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @abstract
     * @param field {String} Field ID
     * @return {Integer} 0 - If no maximum length defined, > 0 Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldLength: function(field) {
    },
    /**
     * Return the Precision (number of digits allowed in the decimal part) of
     * decimal type field
     *
     * @abstract
     * @param field {String} Field ID
     * @return {Integer} 0 - If not a DECIMAL Type Field or No Decimal Places Allowed,
     *                    > 0 Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldPrecision: function(field) {
    },
    /**
     * Does the the Field Have a Default Value Defined?
     *
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    hasFieldDefault: function(field) {
    },
    /**
     * Return default value for the field.
     *
     * @abstract
     * @param field {String} Field ID
     * @return {var} Field's default value, NULL if no default defined (or default is NULL)
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldDefault: function(field) {
    },
    /**
     * Test if a field can be modified.
     *  
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' if the field is modifiable, 'false' otherwise.
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldReadOnly: function(field) {
    },
    /**
     * Test if a field is required.
     *  
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' if the field is required, 'false' otherwise.
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldRequired: function(field) {
    },
    /*
     *****************************************************************************
     FIELD (VALIDATION/TRANSFORMATION) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Does the Form require Validation for the Field?
     *
     * @abstract
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    hasFieldValidation: function(field) {
    },
    /**
     * Verifies if the value is Valid for the Field
     * 
     * @abstract
     * @param field {String} Field ID
     * @param value {var} Value to Test
     * @return {Boolean} Returns TRUE if the Value is Valid for the Field, FALSE Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isValidFieldValue: function(field, value) {
    },
    /**
     * Does the field have a Transformation Function?
     *
     * @abstract
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    hasFieldTransform: function(field) {
    },
    /**
     * Does the field have a Transformation Function?
     *
     * @abstract
     * @param field {String} Field Name to Test
     * @param value {var} Field Value
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    applyFieldTransform: function(field, value) {
    },
    /*
     *****************************************************************************
     FIELD (VALUE) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Test if a field has a value Set.
     *  
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' field has a value defined, 'false' otherwise.
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldSet: function(field) {
    },
    /**
     * Was the field value modified (i.e. Dirty, pending changes)?
     *
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldDirty: function(field) {
    },
    /**
     * Retrieve Field Value
     *
     * @abstract
     * @param field {String} Field ID
     * @return {var} Field Value
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldValue: function(field) {
    },
    /**
     * Return a Field Value Map, containing the current Field Values
     *
     * @abstract
     * @return {Object} Field, Value Tuplets
     * @throws if the Model has not been initialized
     */
    getFieldValues: function() {
    },
    /**
     * Modify the Field's Value
     *
     * @abstract
     * @param field {String} Field ID
     * @param value {var} Field Value
     * @return {var} The Incoming Field Value or The Actual Value Set (Note: the Value may be modified if Trim and Empty-as-Null are Set)
     * @throws if the Model has not been initialized, Field Does not exist in Model or
     *   Value is invalid (after transformation and valiation applied).
     */
    setFieldValue: function(field, value) {
    },
    /**
     * Bulk Modifies the Model
     *
     * @abstract
     * @param map {Object} Field Value Tuplets
     * @return {Object} Field Value Tuplets of All Modified Fields
     * @throws if the Model has not been initialized
     */
    setFieldValues: function(map) {
    },
    /**
     * Reset's All Modified Values Back to the Last Saved State
     *
     * @abstract
     * @param field {String ? null} Field ID or NULL if we would like to reset all fields rather than just a single field.
     * @return {var} if Single Field is being Reset then New Original Field Value is Returned
     *                if All or Fields are being Reset a Field, Value Tuplets of All Modified Fields (with new, original value) or 
     *                NULL if No Changes
     * @throws if the Model has not been initialized
     */
    resetFields: function(field) {
    },
    /*
     *****************************************************************************
     FORM RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Returns the Form's Title
     *
     * @abstract
     * @return {String} Form Title
     * @throws if the Model has not been initialized
     */
    getTitle: function() {
    },
    /**
     * Returns the number of field groups in the form. The form should always
     * have at the very minimum 1 group.
     *
     * @abstract
     * @return {Integer} Number of Groups
     * @throws if the Model has not been initialized
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
     * @throws if the Model has not been initialized
     */
    getGroupLabel: function(group) {
    },
    /**
     * Returns the list of fields in the Group.
     *
     * @abstract
     * @param group {Integer} Group ID
     * @return {String[]} Array of Field IDs in the Group
     * @throws if the Model has not been initialized
     */
    getGroupFields: function(group) {
    },
    /**
     * Does the form have a Global Validation?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    hasFormValidation: function() {
    },
    /**
     * Applies the Global Form Validation to all the fields in the form.
     *
     * @abstract
     * @return {Object} Returns Hash Map of Field, Message Tuplets for all Fields 
     *   that contain invalid values as per the Form Validation.
     * @throws if the Model has not been initialized
     */
    applyFormValidation: function() {
    },
    /**
     * Does the form have a Global Transformation Function?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    hasFormTransform: function() {
    },
    /**
     * Applies a Global Form Transformation, if any exists. A "fields-changed" 
     * event will be fired with the values of all modified fields.
     *
     * @abstract
     * @throws if the Model has not been initialized
     */
    applyFormTransform: function() {
    },
    /*
     *****************************************************************************
     STORAGE (GENERAL) MEMBERS
     *****************************************************************************
     */
    /**
     * Model Data is Read Only?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    isReadOnly: function() {
    },
    /**
     * Model Data has been modified (i.e. Dirty with pending changes)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    isDirty: function() {
    },
    /**
     * Is this a New Record?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    isNew: function() {
    },
    /*
     *****************************************************************************
     STORAGE (PERSISTANCE) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Can we Load the Form's Data?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    canLoad: function() {
    },
    /**
     * Can we Save the Form's Data?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    canSave: function() {
    },
    /**
     * Can we Erase the Form's Data, from the Backend Store?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws if the Model has not been initialized
     */
    canErase: function() {
    },
    /**
     * Try to load the Form's Data
     *
     * @abstract
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @throws if the Model has not been initialized or The action is not possible on Model
     */
    load: function(callback) {
    },
    /**
     * Try to save the Model's 
     *
     * @abstract
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @throws if the Model has not been initialized or The action is not possible on Model
     */
    save: function(callback) {
    },
    /**
     * Try to erase the Form Record
     *
     * @abstract
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @throws if the Model has not been initialized or The action is not possible on Model
     */
    erase: function(callback) {
    }
  } // SECTION: MEMBERS
});
