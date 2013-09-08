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
 * Table Meta Package Class
 */
qx.Class.define("tc.meta.packages.TablePackage", {
  extend: tc.meta.packages.BasePackage,
  implement: tc.meta.packages.ITablePackage,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for an Table MetaPackage
   * 
   * @param table {String} Table ID
   */
  construct: function(table) {
    this.base(arguments);

    this.__sTable = tc.util.String.nullOnEmpty(table);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertString(table, "[table] Should be a Non Empty String!");
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear Variables
    this.__sTable = null;
    this.__oMetaData = null;
    this.__oFieldsPackage = null;
    this.__oServicesPackage = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __sTable: null,
    __oTable: null,
    __oMetaData: null,
    __oFieldsPackage: null,
    __oServicesPackage: null,
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
      // Clear Current Meta Data
      this.__oMetaData = null;

      if (this.__sTable !== null) {
        // Load Fields Definition
        tc.services.Meta.table(this.__sTable,
                function(form) {
                  this.__oMetaData = this.__postProcess(form);
                  if ((this.__oMetaData !== null) && (this.__oFieldsPackage !== null)) {
                    this._bReady = true;

                    if (callback !== null) {
                      if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
                        callback['ok'].call(callback['context'], this.__sTable);
                      }
                    } else {
                      this.fireDataEvent('ready', this.__sTable);
                    }

                    // Done
                    return true;
                  }

                  this.fireDataEvent('error', null);
                  return false;
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

      // No Table to Load
      return false;
    }, // FUNCTION: initialize
    /*
     *****************************************************************************
     INTERFACE METHODS : ITableMetaPackage
     *****************************************************************************
     */
    /**
     * Get Fields Package (IFieldsMetaPackage Instance)
     *
     * @return {tc.meta.packages.IFieldsPackage} Return Fields Package
     * @throw If Package not Ready
     */
    getFields: function() {
      this._throwIsPackageReady();

      return this.__oFieldsPackage;
    },
    /**
     * Get Services Package (IServicesMetaPackage Instance)
     *
     * @return {tc.meta.packages.IServicesPackage} Return Serivce Package or NULL on failure
     * @throw If Package not Ready
     */
    getServices: function() {
      this._throwIsPackageReady();

      return this.__oServicesPackage;
    },
    /**
     * Get Table Container (IMetaTable Instance)
     *
     * @return {tc.meta.data.IMetaTable} Return instance of IMetaForm
     * @throw If Package not Ready
     */
    getTable: function() {
      this._throwIsPackageReady();

      return this.__oTable;
    },
    /*
     *****************************************************************************
     PRIVATE METHODS
     *****************************************************************************
     */
    /**
     * 
     */
    __postProcess: function(table) {
      if (qx.lang.Type.isObject(table) &&
              table.hasOwnProperty('title') &&
              table.hasOwnProperty('fields') &&
              table.hasOwnProperty('services')) {

        // Create Table Entity        
        this.__oTable = new tc.meta.entities.TableEntity(this.__sTable, table);

        // Create Fields Package
        this.__oFieldsPackage = new tc.meta.packages.FieldsPackage(this.__oTable.getFields());

        // Create Services Package
        var services = this.__oTable.getServices();
        if (services !== null) { // Normalize Fields Property
          var arServices = [];
          for (var i = 0; i < services.length; ++i) {
            arServices.push(this.__oTable.getService(services[i]));
          }

          this.__oServicesPackage = new tc.meta.packages.ServicesPackage(arServices);
        }
      } else { // Invalid Form Definition
        table = null;
      }

      return table;
    }, // FUNCTION: __postProcess
    /*
     *****************************************************************************
     EXCEPTION GENERATORS
     *****************************************************************************
     */
    _throwIsPackageReady: function() {
      if (!this.isReady()) {
        throw "Package has not been initialized";
      }
    }
  }
});
