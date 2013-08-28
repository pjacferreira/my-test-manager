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
 * Services Meta Package Class
 */
qx.Class.define("tc.meta.packages.ServicesPackage", {
  extend: tc.meta.packages.BasePackage,
  implement: tc.meta.packages.IServicesPackage,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for an Service MetaPackage
   * 
   * @param services {Array} List of Service to Load
   */
  construct: function(services) {
    this.base(arguments);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertArray(services, "[services] should be an Array!");
      qx.core.Assert.assertTrue(services.length > 0, "[services] Should be non Empty Array!");
    }

    this.__arServices =
            tc.util.Array.clean(
            tc.util.Array.map(services, function(entry) {
      return tc.util.String.nullOnEmpty(entry);
    }, this));
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
    __arServices: null,
    __oMetaData: null,
    /*
     *****************************************************************************
     INTERFACE METHODS : IMetaPackage
     *****************************************************************************
     */
    /**
     * Start Package Initialization Process. Events "ready" or "error" are fired
     * to show success or failure of package initialization. 
     *
     * @param callback {Object ? null} Callback Object, if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @return {Boolean} 'true' if started initialization, 'false' if initialization failed to start
     */
    initialize: function(callback) {
      if (this.__arServices !== null) {
        // Load Fields Definition
        tc.services.Meta.services(this.__arServices,
                function(services) {
                  if (qx.lang.Type.isObject(services)) {
                    this.__oMetaData = services;
                    // TODO : How to handle a situation in which the number of returned services is 0
                    this._bReady = true;

                    // Create a List of Returned Services
                    var listServices = [];
                    for (var i in services) {
                      if (services.hasOwnProperty(i)) {
                        listServices.push(i);
                      }
                    }

                    if (callback !== null) {
                      if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
                        callback['ok'].call(callback['context'], listServices);
                      }
                    } else {
                      this.fireDataEvent('ready', listServices);
                    }
                  }
                  this.fireDataEvent('error', null);
                },
                function(error) {
                  if (callback !== null) {
                    if (callback.hasOwnProperty('nok') && qx.lang.Type.isFunction(callback['nok'])) {
                      callback['nok'].call(callback['context'], error);
                    }
                  } else {
                    this.fireDataEvent('error', error);
                  }
                }, this);

      }

      // No Fields to Load
      return false;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS : IServicesMetaPackage
     *****************************************************************************
     */
    /**
     * Does the Service Exist in the Package?
     *
     * @param id {Object} Service ID (format 'entity id:service name')
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw If Package not Ready
     */
    hasService: function(id) {
      this._throwIsPackageReady();

      return (this.__oMetaData !== null) && this.__oMetaData.hasOwnProperty(id);
    },
    /**
     * Get Service Container (IMetaService Instance)
     *
     * @param id {String} Service ID (format 'entity id:service name')
     * @return {Object|NULL} Return instance of IMetaService, NULL if service doesn't exist
     * @throw If Package not Ready or Service Doesn't Exist
     */
    getService: function(id) {
      this._throwServiceNotExists(id, this.hasService(id));

      return new tc.meta.entities.ServiceEntity(id, this.__oMetaData[id]);
    },
    /**
     * Get a List of Services in the Container
     *
     * @return {Array} Array of Service ID's or Empty Array (if no services in the package)
     * @throw If Package not Ready
     */
    getServices: function() {
      this._throwIsPackageReady();
      
      return this.__arServices;
    },
    /*
     *****************************************************************************
     EXCEPTION GENERATORS
     *****************************************************************************
     */
    _throwIsPackageReady: function() {
      if (!this.isReady()) {
        throw "Package has not been initialized";
      }
    },
    _throwServiceNotExists: function(service, exists) {
      if (!exists) {
        throw "The Service [" + service + "] does not belong to the package";
      }
    }
  }
});
