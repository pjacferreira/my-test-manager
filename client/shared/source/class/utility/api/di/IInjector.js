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

qx.Interface.define("utility.api.di.IInjector", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Service Exist?
     *
     * @abstract
     * @param name {String} Service Name
     * @return {Boolean} 'true' if yes, 'false' otherwise
     */
    has: function(name) {
    },
    /**
     * Get instance of a service.
     *
     * @abstract
     * @param name {String} Service Name
     * @param parameters {array} Array of Parameters to pass to the Service on Creation 
     *   (for shared services, this is only used, the 1st time the service is created)
     * @return {Var} Value of dependency
     * @throw Exception if a Dependency with the Name Does not Exist, or cannot be
     *   created.
     */
    get: function(name, parameters) {
    },
    /**
     * Register a service definition.
     *
     * @abstract
     * @param name {String} Service Name
     * @param definition {Var} definition of service
     * @param shared {Boolean} Is this shared service (i.e. a singleton), 'true' yes, 'false' otherwise
     *   (DEFAULT = false)
     * @return {utility.api.di.IInjector} this of object (for cascading creation)
     * @throw Exception on failure to register the service
     */
    set: function(name, definition, shared) {
    }
  } // SECTION: MEMBERS
});
