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

qx.Interface.define("tc.meta.packages.IServicesPackage", {
  extend: [tc.meta.packages.IMetaPackage],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Service Exist in the Package?
     *
     * @abstract
     * @param id {Object} Service ID (format 'entity id:service name')
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw If Package not Ready
     */
    hasService: function(id) {
    },
    /**
     * Get Service Container (IMetaService Instance)
     *
     * @abstract
     * @param id {String} Service ID (format 'entity id:service name')
     * @return {tc.meta.data.IMetaService} Return instance of IMetaService, NULL if service doesn't exist
     * @throw If Package not Ready or Service Doesn't Exist
     */
    getService: function(id) {
    },
    /**
     * Get a List of Services in the Container
     *
     * @abstract
     * @return {Array} Array of Service ID's or Empty Array (if no services in the package)
     * @throw If Package not Ready
     */
    getServices: function() {
    }
  }
});
