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
 * Generic Form Model
 */
qx.Class.define("meta.entities.Field", {
  extend: meta.entities.AbstractEntity,
  implement: [
    meta.api.entity.IEntityIO,
    meta.api.entity.IEntityVT,
    meta.api.entity.IWidget,
    meta.api.entity.IDisplay,
    meta.api.entity.IField
  ],
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
    this.base(arguments, 'field', id, metadata);

    var definition = this.getMetadata();
    this._oDefinitionValue = (definition !== null) ? definition.value : null;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this._oDefinitionValue = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Field Meta Data
    _oDefinitionValue: null,
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IEntityIO)
     *****************************************************************************
     */
    /**
     * Container Defines an Input?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasInput: function() {
      return !this.isAutoValue();
    },
    /**
     * Container Input Definition
     *
     * @abstract
     * @return {String|String[]} Array containing either, a string of the name of a field
     *   or entity that is accepted, an object containing field/entity->default
     *   value, or NULL if no input is allowed
     */
    getInput: function() {
      return this.hasInput() ? this.getID() : null;
    },
    /**
     * Container Defines an Output?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasOutput: function() {
      return true;
    },
    /**
     * Container Output Definition
     *
     * @abstract
     * @return {String|String[]|null} A String or String Array containing the 
     * permitted/allowed output IDs, or NULL if no output allowed
     */
    getOutput: function() {
      return this.hasOutput() ? this.getID() : null;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IDisplay)
     *****************************************************************************
     */
    /**
     * Return's a Field Label
     *
     * @return {String} Field Label
     */
    getLabel: function() {
      return this.getMetadata().label;
    },
    /** Get URL for Icon
     * 
     * @return {String|null} Icon URL or 'null' if none available
     */
    getIcon: function() {
      var definition = this.getMetadata();
      return definition.hasOwnProperty('icon') ? definition.icon : null;
    },
    /**
     * Retrieve Tooltip Text
     * 
     * @return {String|null} Tooltip or 'null' if none available
     */
    getTooltip: function() {
      return this.getDescription();
    },
    /**
     * Retrieve Widget Help
     * 
     * @abstract
     * @return {String|null} Tooltip or 'null' if none available
     */
    getHelp: function() {
      return this.getDescription();
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.entities.IWidget)
     ***************************************************************************
     */
    /**
     * Retrieve Widget Type
     * 
     * @return {String} Widget Type
     */
    getWidgetType: function() {
      return this._oDefinitionValue.type;
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.entities.IField)
     ***************************************************************************
     */
    /**
     * Is this a Virtual Field (A Field that does not exist in any Physical Store)?
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    isVirtual: function() {
      var definition = this.getMetadata();
      return definition.hasOwnProperty('virtual') ? definition.virtual : false;
    },
    /**
     * Is this a KEY Field (A Field whose value can be used to uniquely identify
     * a record)?
     * Note: Key Fields cannot be NULL
     * 
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    isKey: function() {
      var definition = this.getMetadata();
      return definition.hasOwnProperty('key') ? definition.key : false;
    },
    /**
     * Returns a description of the Field, if any is defined.
     *
     * @return {String ? null} Field Description String or NULL (if not defined)
     */
    getDescription: function() {
      var definition = this.getMetadata();
      return definition.hasOwnProperty('description') ? definition.description : null;
    },
    /**
     * Is field value auto created (Assigned by the System not the User)?
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    isAutoValue: function() {
      return this._oDefinitionValue.hasOwnProperty('auto') ? !!this._oDefinitionValue['auto'] : false;
    },
    /**
     * Should the field be trimmed, before validation?
     *
     * @return {Boolean ? true} 'true' YES, 'false' Otherwise
     */
    isTrimmed: function() {
      return this._oDefinitionValue.hasOwnProperty('trim') ? !!this._oDefinitionValue['trim'] : true;
    },
    /**
     * Should an Empty String be Treated as a NULL?
     * Note, only the value, after a Trim is Applied, or Not, is tested,
     * i.e. if ' ' (single or multiple spaces) is not trimmed, then it will
     * not be considered empty. Only '' (no spaces) is considered empty.
     *
     * @return {Boolean ? true} 'true' Yes, 'false' No - Keep Empty String
     */
    isEmptyNull: function() {
      return this._oDefinitionValue.hasOwnProperty('empty') ? this._oDefinitionValue['empty'] != 'as-empty' : true;
    },
    /**
     * Can field contain a NULL Value?
     * Note, this is verified after the field is trimmed (if applicable) and
     * empty string are treated as NULLs (if applicable).
     * i.e. An empty could produce a NULL Result, and therefore violate or not
     * this test
     *
     * @return {Boolean ? true} 'true' YES, 'false' Otherwise
     */
    isNullable: function() {
      return this._oDefinitionValue.hasOwnProperty('nullable') ? !!this._oDefinitionValue['nullable'] : true;
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
     * @return {String ? 'text'} Field Type
     */
    getValueType: function() {
      return this._oDefinitionValue.hasOwnProperty('type') ? this._oDefinitionValue['type'] : 'text';
    },
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @return {Integer ? 0} 0 - If no maximum length defined, > 0 Otherwise
     */
    getLength: function() {
      return this._oDefinitionValue.hasOwnProperty('length') && qx.lang.Type.isNumber(this._oDefinitionValue['length']) ? this._oDefinitionValue['length'] : 0;
    },
    /**
     * Return the Precision (number of digits allowed in the decimal part) of
     * decimal type field
     *
     * @return {Integer ? 0} 0 - If not a DECIMAL Type Field or No Decimal Places Allowed,
     *                    > 0 Otherwise
     */
    getPrecision: function() {
      return this._oDefinitionValue.hasOwnProperty('precision') && qx.lang.Type.isNumber(this._oDefinitionValue['precision']) ? this._oDefinitionValue['length'] : 0;
    },
    /**
     * Does the the Field Have a Default Value Defined?
     *
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    hasDefault: function() {
      return this._oDefinitionValue.hasOwnProperty('default');
    },
    /**
     * Return default value for the field.
     *
     * @return {var ? null} Field's default value
     */
    getDefault: function() {
      return this.hasDefault() ? this._oDefinitionValue['default'] : null;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IEntityVT)
     *****************************************************************************
     */
    /**
     * Retrieves the List of Validation Rules.
     * Format:
     *  rule:= comparison_operator ['|"] value ['|"] [ '||' rule ]
     *  
     * If an array of rules is provided, than the result of each rule in the
     * array, is ANDed to obtain the result.
     *
     * @return {String|String[]|null} Validation Rules or NULL if none
     */
    getValidationRules: function() {
      return this._oDefinitionValue.hasOwnProperty('validations') ? this._oDefinitionValue['validations'] : null;
    },
    /**
     * Retrieves the List of Transformations Rules.
     * Format:
     *  rule:= comparison_operator ['|"] value ['|"] [ '||' rule ]
     *  
     * If an array of rules is provided, than the result of each rule in the
     * array, is ANDed to obtain the result.
     *
     * @return {String|String[]|null} Transformation Rules or NULL if none
     */
    getTransformationRules: function() {
      return this._oDefinitionValue.hasOwnProperty('transformations') ? this._oDefinitionValue['transformations'] : null;
    },
    /*
     *****************************************************************************
     ABSTRACT METHODS (meta.entities.AbstractEntity)
     *****************************************************************************
     */
    /**
     * Prepare Entity Definition, according to type requirements.
     *
     * @param definition {Map} Incoming Entity Definition
     * @return {Map|null} Outgoing Entity Definition or 'null' if not valid
     */
    _preProcessMetadata: function(definition) {
      // Does the Definition have the Minimum Required Properties?
      if (this._validateStringProperty(definition, 'label', false, true) &&
        this._validateStringProperty(definition, 'icon', true, true) &&
        this._validateStringProperty(definition, 'description', true, true) &&
        this._validateMapProperty(definition, 'value') &&
        this._validateStringProperty(definition.value, 'type', false, true))
      { // YES
        return definition;
      }

      // ELSE: No - Abort
      return null;
    }
  }
});
