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
qx.Class.define("meta.entities.Form", {
  extend: meta.entities.AbstractEntity,
  implement: [
    meta.api.entity.IEntityIO,
    meta.api.entity.IContainer,
    meta.api.entity.IForm,
    meta.api.entity.IDisplay,
    meta.api.entity.ILayout
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
    this.base(arguments, 'form', id, metadata);

    // Set Variables
    this.__oDefinitionWidgets = this.getMetadata().widgets;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__oDefinitionWidgets = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __oDefinitionWidgets: null,
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IEntityIO)
     *****************************************************************************
     */
    /**
     * Container Defines an Input?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasInput: function() {
      return !this.isAutoValue();
    },
    /**
     * Container Input Definition
     *
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
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasOutput: function() {
      return true;
    },
    /**
     * Container Output Definition
     *
     * @return {String|String[]|null} A String or String Array containing the 
     * permitted/allowed output IDs, or NULL if no output allowed
     */
    getOutput: function() {
      return this.hasOutput() ? this.getID() : null;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IContainer)
     *****************************************************************************
     */
    /**
     * Container the Specified Widget's Definition
     *
     * @param id {String} Widget ID
     * @return {Map|null} Widget Definition or NULL if it doesn't exist
     */
    getWidget: function(id) {
      return this.__isValidWidget(id) ? this.__oDefinitionWidgets[id] : null;
    },
    /**
     * Return List of Widget's IDs in the Container
     *
     * @return {String[]} List of Widget's IDs 
     */
    getWidgets: function() {
      var widgets = [];

      // Cycle through the Widgets
      for (var id in this.__oDefinitionWidgets) {
        if (this.__isValidWidget(id)) {
          widgets.push(id);
        }
      }

      return widgets;
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
      return this.getTitle();
    },
    /** Get URL for Icon
     * 
     * @return {String|null} Icon URL or 'null' if none available
     */
    getIcon: function() {
      var definition = this.getMetadata();
      return definition.hasOwnProperty('icon') ? definition['icon'] : null;
    },
    /**
     * Retrieve Tooltip Text
     * 
     * @return {String|null} Tooltip or 'null' if none available
     */
    getTooltip: function() {
      var definition = this.getMetadata();
      return definition.hasOwnProperty('tooltip') ? definition['tooltip'] : null;
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
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.ILayout)
     *****************************************************************************
     */
    /**
     * Return Widget's Layout Definition
     *
     * @return {String[]} Layout Order
     */
    getLayout: function() {
      return this.getMetadata().layout;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IForm)
     *****************************************************************************
     */
    /**
     * Retrieve Widget Type
     * 
     * @return {String} Widget Type
     */
    getFormType: function() {
      return this.getMetadata().type;
    },
    /**
     * Is this a Read Only Form?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
      return false;
    },
    /**
     * Returns the Form's Title
     *
     * @return {String} Form Title
     */
    getTitle: function() {
      return this.getMetadata().title;
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
        this._validateArrayProperty(definition, 'layout', false) &&
        this._validateMapProperty(definition, 'widgets', false) &&
        this._validateStringProperty(definition, 'title', false, true) &&
        this._validateStringProperty(definition, 'icon', true, true) &&
        this._validateStringProperty(definition, 'tooltip', true, true))
      { // YES
        return definition;
      }

      // ELSE: No - Abort
      return null;
    },
    /*
     *****************************************************************************
     PRIVATE Methods
     *****************************************************************************
     */
    __isValidWidget: function(id) {
      return this.__oDefinitionWidgets.hasOwnProperty(id) &&
        qx.lang.Type.isObject(this.__oDefinitionWidgets[id]) &&
        this.__oDefinitionWidgets[id].hasOwnProperty('type');
    }
  }
});
