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
 * Fields Meta Package Class
 */
qx.Class.define("tc.meta.packages.FieldsPackage", {
  extend: tc.meta.packages.BasePackage,
  implement: tc.meta.packages.IFieldsPackage,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for an Fields MetaPackage
   * 
   * @param fields {Array} List of Fields to Load
   */
  construct: function(fields) {
    this.base(arguments);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertArray(fields, "[fields] should be an Array!");
      qx.core.Assert.assertTrue(fields.length > 0, "[fields] Should be non Empty Array!");
    }

    this.__arFields =
            tc.util.Array.clean(
            tc.util.Array.map(fields, function(entry) {
      return tc.util.String.nullOnEmpty(entry);
    }, this));

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertTrue((this.__arFields !== null) && (this.__arFields.length > 0), "[fields] Should be non Empty Array!");
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup
    this.__arFields = null;
    this.__arFieldsInPackage = null;
    this.__oMetaData = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __arFields: null,
    __arFieldsInPackage: null,
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
      if (this.__arFields !== null) {
        // Load Fields Definition
        tc.services.Meta.fields(this.__arFields,
                function(fields) {
                  if (qx.lang.Type.isObject(fields)) {
                    this.__oMetaData = fields;
                    // TODO : How to handle a situation in which the number of returned services is 0
                    this._bReady = true;

                    // Create a List of Returned Fields
                    this.__arFieldsInPackage = [];
                    for (var i in fields) {
                      if (fields.hasOwnProperty(i)) {
                        this.__arFieldsInPackage.push(i);
                      }
                    }

                    if (this.__arFieldsInPackage.length > 0) {
                      this.__arFieldsInPackage.sort();
                    }

                    if (callback !== null) {
                      if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
                        callback['ok'].call(callback['context'], this.__arFieldsInPackage);
                      }
                    } else {
                      this.fireDataEvent('ready', this.__arFieldsInPackage);
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
     INTERFACE METHODS : IFieldsPackage
     *****************************************************************************
     */
    /**
     * Does the Field Exist in the Package?
     *
     * @param id {Object} Field ID (format 'entity id:field name')
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw If Package not Ready
     */
    hasField: function(id) {
      this._throwIsPackageReady();
      
      return (this.__oMetaData !== null) && this.__oMetaData.hasOwnProperty(id);
    },
    /**
     * Get Field Container (IMetaField Instance)
     *
     * @param id {String} Field ID (format 'entity id:field name')
     * @return {tc.meta.data.IMetaEntity} Return Metadata for field
     * @throw If Package not Ready or Field Doesn't Exist
     */
    getField: function(id) {
      this._throwFieldNotExists(id, this.hasField(id));

      return new tc.meta.entities.FieldEntity(id, this.__oMetaData[id]);
    },
    /**
     * Get a List of Fields in the Container
     *
     * @return {Array} Array of Field ID's or Empty Array (if no fields in the package)
     * @throw If Package not Ready
     */
    getFields: function() {
      this._throwIsPackageReady();
      
      return this.__arFieldsInPackage;
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
    _throwFieldNotExists: function(field, exists) {
      if (!exists) {
        throw "The Field [" + field + "] does not belong to the package";
      }
    }
  } // SECTION: MEMBERS
});
