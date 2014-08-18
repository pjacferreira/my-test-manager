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
qx.Class.define("meta.entities.GroupWidget", {
  extend: meta.entities.AbstractEntity,
  implement: [
    meta.api.entity.IEntityIO,
    meta.api.entity.IContainer,
    meta.api.entity.IWidget,
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
   * @param parent {meta.api.entity.IEntity} Parent Container
   * @param id {String} Widget ID
   * @param metadata {Object} Widget Metadata
   */
  construct: function(parent, id, metadata) {
    this.base(arguments, 'group', id, metadata);
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(parent, meta.api.entity.IContainer, "[parent] Is invalid!");
    }

    // Set Variables
    this.__oParent = parent;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__oParent = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __oParent: null,
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
      // Is Potentially Valid ID?
      id = utility.String.v_nullOnEmpty(id);
      if (id !== null) { // YES
        var layout = this.getLayout();
        return layout.indexOf('id') >= 0 ? this.__oParent.getChild(id) : null;
      }

      return null;
    },
    /**
     * Return List of Widget's IDs in the Container
     *
     * @return {String[]} List of Widget's IDs 
     */
    getWidgets: function() {
      // Group Widget's Layout define all the know widgets
      return this.getLayout();
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.entity.IWidget)
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
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IDisplay)
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
