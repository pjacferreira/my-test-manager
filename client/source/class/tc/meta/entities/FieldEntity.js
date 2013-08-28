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
qx.Class.define("tc.meta.entities.FieldEntity", {
  extend: tc.meta.entities.BaseEntity,
  implement: tc.meta.entities.IMetaField,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param id {String} Field Name
   * @param metadata {Object} Field Metadata
   */
  construct: function(id, metadata) {
    this.base(arguments, 'field', id);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('label') &&
              metadata.hasOwnProperty('value') &&
              qx.lang.Type.isObject(metadata['value']) &&
              metadata['value'].hasOwnProperty('type'), "[metadata] Is does not contain Metadata Definition for a Field!");
    }

    this.__oMetaData = qx.lang.Object.clone(metadata, true);
    this.__oMetaDataValue = this.__oMetaData['value'];
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__oMetaDataValue = null;
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
    __oMetaDataValue: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Is this a Virtual Field (A Field that does not exist in any Physical Store)?
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    isVirtual: function() {
      return this.__oMetaData.hasOwnProperty('virtual') ? this.__oMetaData['virtual'] == true : false;
    },
    /**
     * Is this a KEY Field (A Field whose value can be used to uniquely identify
     * a record)?
     * Note: Key Fields cannot be NULL
     * 
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    isKey: function() {
      return this.__oMetaData.hasOwnProperty('key') ? this.__oMetaData['key'] == true : false;
    },
    /**
     * Return's a Field Label
     *
     * @abstract
     * @return {String} Field Label
     */
    getLabel: function() {
      return this.__oMetaData.hasOwnProperty('label') ? this.__oMetaData['label'] : this.getEntityId();
    },
    /**
     * Returns a description of the Field, if any is defined.
     *
     * @abstract
     * @return {String ? null} Field Description String or NULL (if not defined)
     */
    getDescription: function() {
      return this.__oMetaData.hasOwnProperty('description') ? this.__oMetaData['description'] : null;
    },
    /**
     * Is field value auto created (Assigned by the System not the User)?
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    isAutoValue: function() {
      return this.__oMetaDataValue.hasOwnProperty('auto') ? this.__oMetaDataValue['auto'] == true : false;
    },
    /**
     * Should the field be trimmed, before validation?
     *
     * @abstract
     * @return {Boolean ? true} 'true' YES, 'false' Otherwise
     */
    isTrimmed: function() {
      return this.__oMetaDataValue.hasOwnProperty('trim') ? this.__oMetaDataValue['trim'] == true : true;
    },
    /**
     * Should an Empty String be Treated as a NULL?
     * Note, only the value, after a Trim is Applied, or Not, is tested,
     * i.e. if ' ' (single or multiple spaces) is not trimmed, then it will
     * not be considered empty. Only '' (no spaces) is considered empty.
     *
     * @abstract
     * @return {Boolean ? true} 'true' Yes, 'false' No - Keep Empty String
     */
    isEmptyNull: function() {
      return this.__oMetaDataValue.hasOwnProperty('empty') ? this.__oMetaDataValue['empty'] != 'as-empty' : true;
    },
    /**
     * Can field contain a NULL Value?
     * Note, this is verified after the field is trimmed (if applicable) and
     * empty string are treated as NULLs (if applicable).
     * i.e. An empty could produce a NULL Result, and therefore violate or not
     * this test
     *
     * @abstract
     * @return {Boolean ? true} 'true' YES, 'false' Otherwise
     */
    isNullable: function() {
      return this.__oMetaDataValue.hasOwnProperty('nullable') ? this.__oMetaDataValue['nullable'] == true : true;
    },
    /**
     * Return the type of the value for the field.
     * Note: The return value should be ONE OF the Following Values:
     * 'boolean'
     * 'date'    | 'time'    | 'datetime'
     * 'integer' | 'decimal'
     * 'text'    | 'html'
     * 
     *
     * @abstract
     * @return {String ? 'text'} Field Type
     */
    getValueType: function() {
      return this.__oMetaDataValue.hasOwnProperty('type') ? this.__oMetaDataValue['type'] : 'text';
    },
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @abstract
     * @return {Integer ? 0} 0 - If no maximum length defined, > 0 Otherwise
     */
    getLength: function() {
      return this.__oMetaDataValue.hasOwnProperty('length') && qx.lang.Type.isNumber(this.__oMetaDataValue['length']) ? this.__oMetaDataValue['length'] : 0;
    },
    /**
     * Return the Precision (number of digits allowed in the decimal part) of
     * decimal type field
     *
     * @abstract
     * @return {Integer ? 0} 0 - If not a DECIMAL Type Field or No Decimal Places Allowed,
     *                    > 0 Otherwise
     */
    getPrecision: function() {
      return this.__oMetaDataValue.hasOwnProperty('precision') && qx.lang.Type.isNumber(this.__oMetaDataValue['precision']) ? this.__oMetaDataValue['length'] : 0;
    },
    /**
     * Does the the Field Have a Default Value Defined?
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    hasDefault: function() {
      return this.__oMetaDataValue.hasOwnProperty('default') ? this.__oMetaDataValue['default'] == true : false;
    },
    /**
     * Return default value for the field.
     *
     * @abstract
     * @return {var ? null} Field's default value
     */
    getDefault: function() {
      return this.hasDefault() ? this.__oMetaDataValue['default'] : null;
    },
    /**
     * Is a Validator Defined for the Field?
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    hasValidator: function() {
      return this.__oMetaDataValue.hasOwnProperty('validation') ? this.__oMetaDataValue['validation'] == true : false;
    },
    /**
     * Retrieves a function, if one is defined, that can be used to validate the
     * field values. Function has the following prototype / template:
     * function (field_value) {
     *   ....
     *   return {boolean} : 'true' - valid, 'false' - invalid
     * }
     *
     * @abstract
     * @return {Function ? null} Validator Function
     */
    getValidator: function() {
      if (this.hasValidator()) {
        // TODO Implement
        return new Function('value', 'return false;');

      }

      return null;
    }
  }
});
