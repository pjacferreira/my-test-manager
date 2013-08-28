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

qx.Interface.define("tc.meta.entities.IMetaField", {
  extend: [tc.meta.entities.IMetaEntity],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is this a Virtual Field (A Field that does not exist in any Physical Store)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isVirtual: function() {
    },
    /**
     * Is this a KEY Field (A Field whose value can be used to uniquely identify
     * a record)?
     * Note: Key Fields cannot be NULL
     * 
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isKey: function() {
    },
    /**
     * Return's a Field Label
     *
     * @abstract
     * @return {String} Field Label
     */
    getLabel: function() {
    },
    /**
     * Returns a description of the Field, if any is defined.
     *
     * @abstract
     * @return {String} Field Description String or NULL (if not defined)
     */
    getDescription: function() {
    },
    /**
     * Is field value auto created (Assigned by the System not the User)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isAutoValue: function() {
    },
    /**
     * Should the field be trimmed, before validation?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isTrimmed: function() {
    },
    /**
     * Should an Empty String be Treated as a NULL?
     * Note, only the value, after a Trim is Applied, or Not, is tested,
     * i.e. if ' ' (single or multiple spaces) is not trimmed, then it will
     * not be considered empty. Only '' (no spaces) is considered empty.
     *
     * @abstract
     * @return {Boolean} 'true' Yes, 'false' No - Keep Empty String
     */
    isEmptyNull: function() {
    },
    /**
     * Can field contain a NULL Value?
     * Note, this is verified after the field is trimmed (if applicable) and
     * empty string are treated as NULLs (if applicable).
     * i.e. An empty could produce a NULL Result, and therefore violate or not
     * this test
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isNullable: function() {
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
     * @return {String} Field Type
     */
    getValueType: function() {
    },
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @abstract
     * @return {Integer} 0 - If no maximum length defined, > 0 Otherwise
     */
    getLength: function() {
    },
    /**
     * Return the Precision (number of digits allowed in the decimal part) of
     * decimal type field
     *
     * @abstract
     * @return {Integer} 0 - If not a DECIMAL Type Field or No Decimal Places Allowed,
     *                    > 0 Otherwise
     */
    getPrecision: function() {
    },
    /**
     * Does the the Field Have a Default Value Defined?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasDefault: function() {
    },
    /**
     * Return default value for the field.
     *
     * @abstract
     * @return {var} Field's default value, NULL if no default defined (or default is NULL)
     */
    getDefault: function() {
    },
    /**
     * Is a Validator Defined for the Field?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasValidator: function() {
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
     * @return {Function} Validator Function or NULL if no Validator is Defined
     */
    getValidator: function() {
    }
  }
});
