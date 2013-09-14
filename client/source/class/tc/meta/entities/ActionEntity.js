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
qx.Class.define("tc.meta.entities.ActionEntity", {
  extend: tc.meta.entities.BaseEntity,
  implement: tc.meta.entities.IMetaAction,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param id {String} Action Name
   * @param metadata {Object} Action Metadata
   */
  construct: function(id, metadata) {
    this.base(arguments, 'action', id);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('label'), "[metadata] Is not a valid Metadata Definition for an Action!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('form') ||
              metadata.hasOwnProperty('service'), "[metadata] Action missing a Action Entity ID!");
    }

    this.__oMetaData = qx.lang.Object.clone(metadata, true);
    this.__oMetaDataOptions = this.__oMetaData.hasOwnProperty('options') ? this.__oMetaData['options'] : null;
    if (this.__oMetaData.hasOwnProperty('parameters')) {
      var value = tc.util.Array.clean(tc.util.Array.trim(tc.util.Array.CSVtoArray(this.__oMetaData['parameters'])));
      ;
      if (value !== null) {
        this.__oMetaData['parameters'] = value;
      } else {
        delete this.__oMetaData['parameters'];
      }
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Members
    this.__oMetaDataOptions = null;
    this.__oMetaData = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Meta Data Members
    __oMetaData: null,
    __oMetaDataOptions: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Return's a Action's Label
     *
     * @return {String} Action Label
     */
    getLabel: function() {
      return this.__oMetaData.hasOwnProperty('label') ? this.__oMetaData['label'] : this.getEntityId();
    },
    /**
     * Returns a Description for the Action, if any is defined.
     *
     * @return {String} Action Description String or NULL (if not defined)
     */
    getDescription: function() {
      return this.__oMetaData.hasOwnProperty('description') ? this.__oMetaData['description'] : null;
    },
    /**
     * Returns a Shortcut Key Combination for the Action, if any is defined.
     *
     * @return {String} Action Shortcut or NULL (if not defined)
     */
    getShortcut: function() {
      return this.__oMetaData.hasOwnProperty('shortcut') ? this.__oMetaData['shortcut'] : null;
    },
    /**
     * Returns an URL to an Icon Image for the Action, if any is defined.
     *
     * @return {String} Icon URL or NULL (if not defined)
     */
    getIcon: function() {
      return this.__oMetaData.hasOwnProperty('icon') ? this.__oMetaData['icon'] : null;
    },
    /**
     * Does the Action Specify a Value for the Option?
     *
     * @param name {String} Option name
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasOption: function(name) {
      name = tc.util.String.nullOnEmpty(name);
      if ((name !== null) && (this.__oMetaDataOptions !== null)) {
        return this.__oMetaDataOptions.hasOwnProperty(name.toLowerCase());
      }

      return false;
    },
    /**
     * Get the Value for the Specified Option
     *
     * @param name {String} Option Name
     * @return {var} Option's value, NULL if no Option Value Defined
     */
    getOptionValue: function(name) {
      name = tc.util.String.nullOnEmpty(name);
      if ((name !== null) && (this.__oMetaDataOptions !== null)) {
        name = name.toLowerCase();
        return this.__oMetaDataOptions.hasOwnProperty(name) ? this.__oMetaDataOptions[name] : null;
      }

      return null;
    },
    /**
     * Does the Action Specify a Set of Parameters?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasParameters: function() {
      return this.__oMetaData.hasOwnProperty('parameters');
    },
    /**
     /**
     * Get the List of Parameters Allowed or Required by the Action.
     * 
     * @return {String[]} List of Parameters Required/Allowed by the Action
     */
    getParameters: function() {
      return this.__oMetaData.hasOwnProperty('parameters') ? this.__oMetaData['parameters'] : null;
    },
    /**
     * Does the Action Execute a Service?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isServiceAction: function() {
      return this.__oMetaData.hasOwnProperty('service');
    },
    /**
     * Does the Action Execute a Form?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isFormAction: function() {
      return this.__oMetaData.hasOwnProperty('form');
    },
    /**
     * Returns the Entity ID Associated with the Action
     *
     * @return {String} Entity ID
     */
    getActionEntity: function() {
      if (this.__oMetaData.hasOwnProperty('form')) {
        return this.__oMetaData['form'];

      } else if (this.__oMetaData.hasOwnProperty('service')) {
        return this.__oMetaData['service'];
      }

      this._throwInvalidActionDefinition();
    },
    /*
     *****************************************************************************
     EXCEPTION GENERATORS
     *****************************************************************************
     */
    _throwInvalidActionDefinition: function() {
      if (!this.isReady()) {
        throw "Invalid Action Definition";
      }
    }
  } // SECTION: MEMBERS
});
