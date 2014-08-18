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
qx.Class.define("meta.entities.FormInput", {
  extend: meta.entities.Form,
  implement: [
    meta.api.entity.IFormServices
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
    this.base(arguments, id, metadata);

    // Set Variables
    this.__oServices = this.getMetadata().hasOwnProperty('services') ? this.getMetadata().services : null;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__oServices = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __oServices: null,
    /*
     *****************************************************************************
     INTERFACE METHODS (meta.api.entity.IEntityIO)
     *****************************************************************************
     */
    /**
     * Does the Form define the Service Alias?
     * 
     * @param alias {String} Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
      // Do we have Services Defined?
      if (this.__oServices !== null) { // YES
        alias = utility.String.v_nullOnEmpty(alias);
        return this.__oServices.hasOwnProperty(alias) ? true : false;
      }
      // ELSE: NO - No Services Defined
      return false;
    },
    /**
     * Get the List of Service Aliases defined
     *
     * @return {String[]} List of Service Aliases
     */
    getServices: function() {
      var services = [];
      // Do we have Services Defined?
      if (this.__oServices !== null) { // YES
        for (var service in this.__oServices) {
          // Is this a Possible Service Alias?
          if (this.__oServices.hasOwnProperty(service) &&
            qx.lang.Type.isString(this.__oServices[service])) { // YES
            services.push(service);
          }
        }
      }

      return services;
    },
    /**
     * Get the Service ID for the Alias
     *
     * @param alias {String} Service Alias
     * @return {String|null} Service ID or 'null'
     */
    getServiceID: function(alias) {
      // Is the Alias Defined?
      alias = utility.String.v_nullOnEmpty(alias);
      if (this.hasService(alias)) { // YES
        return this.__oServices[alias];
      }
      // ELSE: NO

      return null;
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
      definition = this.base(arguments, definition);

      // Does the Definition have the Minimum Required Properties?
      if ((definition !== null) &&
        this._validateMapProperty(definition, 'services', false)) {
        return definition;
      }

      // ELSE: No - Abort
      return null;
    }
  }
});
