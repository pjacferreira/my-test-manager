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
qx.Class.define("meta.entities.Widget", {
  extend: meta.entities.AbstractEntity,
  implement: [
    meta.api.entity.IWidget,
    meta.api.entity.IDisplay
  ],
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param id {String} Widget ID
   * @param metadata {Object} Widget Metadata
   */
  construct: function(id, metadata) {
    this.base(arguments, 'widget', id, metadata);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
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
      var definition = this.getMetadata();
      return definition.hasOwnProperty('tooltip') ? definition.tooltip : null;
    },
    /**
     * Retrieve Widget Help
     * 
     * @return {String|null} Tooltip or 'null' if none available
     */
    getHelp: function() {
      return this.getTooltip();
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
      return this.getMetadata().type;
    },
    /*
     *****************************************************************************
     PROTECTED Methods
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
      if (this._validateStringProperty(definition, 'type', false, true) &&
        this._validateStringProperty(definition, 'label', false, true) &&
        this._validateStringProperty(definition, 'icon', true, true) &&
        this._validateStringProperty(definition, 'tooltip', true, true))
      { // YES
        return definition;
      }

      // ELSE: No - Abort
      return null;
    }
  }
});
