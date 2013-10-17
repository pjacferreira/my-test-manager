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
 * List Meta Package Class
 */
qx.Class.define("tc.meta.packages.ListPackage", {
  extend: tc.meta.packages.BasePackage,
  implement: tc.meta.packages.IListPackage,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for an List MetaPackage
   * 
   * @param list {String} List ID
   */
  construct: function(list) {
    this.base(arguments);

    this.__sList = tc.util.String.nullOnEmpty(list);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertString(list, "[list] Should be a Non Empty String!");
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear Variables
    this.__sList = null;
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
    __sList: null,
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
      // Prepare CallBack
      callback = this._prepareCallback(callback);

      if (!this.isReady()) {
        if (this.__sList !== null) {
          // Load Form Definition
          tc.services.Meta.list(this.__sList,
                  function(list) {
                    this.__oMetaData = this.__postProcess(list);
                    if ((this.__oMetaData !== null) && (this.__oFieldsPackage !== null)) {
                      this._bReady = true;
                    }

                    this._callbackPackageReady(callback, this._bReady, "Invalid List Definition");
                  },
                  function(error) {
                    this._callbackPackageReady(callback, false, error);
                  }, this);

        } else {
          this._callbackPackageReady(callback, false, "Missing List ID to build Package.");
        }
      } else {
        this._callbackPackageReady(callback, true);
      }

      return this.isReady();
    }, // FUNCTION: initialize
    /*
     *****************************************************************************
     INTERFACE METHODS : IListMetaPackage
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
     * Get Form Container (IMetaList Instance)
     *
     * @return {tc.meta.entities.IMetaList} Return List Metadata Entity
     * @throw If Package not Ready
     */
    getList: function() {
      this._throwIsPackageReady();

      return new tc.meta.entities.ListEntity(this.__sList, this.__oMetaData);
    },
    /*
     *****************************************************************************
     PRIVATE METHODS
     *****************************************************************************
     */
    /**
     * 
     */
    __postProcess: function(list) {
      if (qx.lang.Type.isObject(list) &&
              list.hasOwnProperty('key') && qx.lang.Type.isString(list.key) &&
              list.hasOwnProperty('fields') &&
              list.hasOwnProperty('services')) {
        // Normalize Fields Property
        if (list.hasOwnProperty('fields')) { // Normalize Fields Property
          if (qx.lang.Type.isString(list.fields)) {
            // CASE 1: fields = field_id or {CSV STRING} field_id, ..., field_id
            list.fields = tc.util.Array.clean(tc.util.Array.trim(tc.util.Array.CSVtoArray(list.fields)));
          }

          // Create Fields Package
          if (list.fields.length > 0) { // No Fields in Form
            this.__oFieldsPackage = new tc.meta.packages.FieldsPackage(tc.util.Array.union(list.fields.slice(0).sort(), [list.key]));
          } else {
            list.fields = null;
          }
        }

        // Normalize Services Property
        if (list.hasOwnProperty('services')) {
          if (qx.lang.Type.isObject(list.services)) {
            var arServices = [];
            for (var service in list.services) {
              if (list.services.hasOwnProperty(service)) {
                if (qx.lang.Type.isString(list.services[service])) {
                  arServices.push(list.services[service]);
                }
              }
            }

            if (arServices.length > 0) {
              this.__oServicesPackage = new tc.meta.packages.ServicesPackage(arServices);
            } else {
              list.services = null;
            }
          } else {
            list.services = null;
          }
        }

        // Normalize Display Property
        if (!list.hasOwnProperty('display') ||
                !qx.lang.Type.isString(list.display)) {
          list.display = list.key;
        }
      } else { // Invalid List Definition
        list = null;
      }

      return list;
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
