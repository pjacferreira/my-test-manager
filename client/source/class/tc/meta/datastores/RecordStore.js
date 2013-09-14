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
 * Base Meta Package Class
 */
qx.Class.define("tc.meta.datastores.RecordStore", {
  extend: tc.meta.datastores.FieldStore,
  implement: tc.meta.datastores.IRecordStore,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /*
     ***************************************************************************
     EVENTS (IFormStore)
     ***************************************************************************
     */
    /**
     * Fired when the Form's Data has Been Loaded from Any Backend Source
     */
    "loaded": "qx.event.type.Event",
    /**
     * Fired when the Form's Data has Been Saved to Any Backend Source
     */
    "saved": "qx.event.type.Event",
    /**
     * Fired when the Form's Data has Been Erased from Any Backend Source
     */
    "erased": "qx.event.type.Event"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor
   */
  construct: function() {
    this.base(arguments);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear Members
    this.__oFormEntity = null;
    this.__oFieldsPackage = null;
    this.__oServicesPackage = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __oFormEntity: null,
    __oFieldsPackage: null,
    __oServicesPackage: null,
    _bNewRecord: true,
    /*
     ***************************************************************************
     PROPERTY APPLY METHODS
     ***************************************************************************
     */
    // property modifier
    _applyPackage: function(newPackage, oldPackage) {
      // Call Base Function
      this.base(arguments, newPackage, oldPackage);

      // Clear Cache Setttings
      this.__oFormEntity = null;
      this.__oFieldsPackage = null;
      this.__oServicesPackage = null;
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (IDataStore)
     ***************************************************************************
     */
    /**
     * Is the Data Store Read Only?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
      return this.isReady() && this._getForm().isReadOnly();
    },
    /**
     * Is this an an offline (in memory only) data store?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isOffline: function() {
      return false;
    },
    /*
     ***************************************************************************
     INTERFACE METHODS (IFormStore)
     ***************************************************************************
     */
    /**
     * Is this a New Record?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    isNew: function() {
      this._throwIsStoreReady();

      return this._bNewRecord;
    },
    /**
     * Can we Load the Record's Data?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    canLoad: function() {
      this._throwIsStoreReady();

      // TODO Verify that the actual service exists in the Service Package
      return this._getForm().hasService('read');
    },
    /**
     * Can we Save the Record's Data?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    canSave: function() {
      this._throwIsStoreReady();

      // TODO Verify that the actual service exists in the Service Package
      return this._getForm().hasService('update') || this._getForm().hasService('create');
    },
    /**
     * Can we Erase the Record's Data, from the Service Store?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    canErase: function() {
      this._throwIsStoreReady();

      // TODO Verify that the actual service exists in the Service Package
      return this._getForm().hasService('delete');
    },
    /**
     * Try to load the Record's Data from the Data Store
     *
     * @param ok {Function} Function to be called, if action succeeds
     * @param nok {Function ? null} Function to be called, if action fails
     * @param context {Object ? this} Context in which the functions are executed (if not provided, run within the context of the data store)
     * @throw if the Data Store is Not Ready or The action is not possible on the data store
     */
    load: function(ok, nok, context) {
      this._throwActionNotSupported('load', this.canLoad());

      return this._execute('read',
              this._prepareServiceCallback('loaded', ok, nok, context));
    },
    /**
     * Try to save the Record's Data to the Data Store
     *
     * @param ok {Function} Function to be called, if action succeeds
     * @param nok {Function ? null} Function to be called, if action fails
     * @param context {Object ? this} Context in which the functions are executed (if not provided, run within the context of the data store)
     * @throw if the Data Store is Not Ready or The action is not possible on the data store
     */
    save: function(ok, nok, context) {
      this._throwActionNotSupported('save', this.canSave());

      return this._execute(this.isNew() ? 'create' : 'update',
              this._prepareServiceCallback('saved', ok, nok, context));
    },
    /**
     * Try to erase the Record Record from the Data Store
     *
     * @param ok {Function} Function to be called, if action succeeds
     * @param nok {Function ? null} Function to be called, if action fails
     * @param context {Object ? this} Context in which the functions are executed (if not provided, run within the context of the data store)
     * @throw if the Data Store is Not Ready or The action is not possible on the data store
     */
    erase: function(ok, nok, context) {
      this._throwActionNotSupported('erase', this.canErase());

      return this._execute('delete',
              this._prepareServiceCallback('erased', ok, nok, context));
    },
    /*
     ***************************************************************************
     PROTECTED METHODS
     ***************************************************************************
     */
    /**
     * Execute a Service against the Current DataStore
     *  
     * @param alias {String} Service Alias
     * @param callback {Object} Callback Functions
     * @throw if Service Definition does not exist
     */
    _execute: function(alias, callback) {
      // Get Services Package
      var services = this._getServicesPackage();
      if (services.isReady()) {
        this._executeService(alias, callback);
      } else {
        var save_this = this;
        services.initialize({
          'ok': function() {
            this._executeService(alias, callback);
          },
          'nok': function(e) {
            this._callbackModelReady(callback, false, 'Service [' + alias + '] Execution Failed.');
          },
          'context': save_this
        });
      }
    }, // FUNCTION: _execute      
    _executeService: function(alias, callback) {
      // Get Services Package
      var service = this.__getService(alias);

      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(service, tc.meta.entities.IMetaService, "[service] Is not of the expected type!");
      }

      switch (alias) {
        case 'create':
        case 'update':
          return this._executeCU(alias, service, callback);
        case 'read':
          return this._executeR(service, callback);
        case 'delete':
          return this._executeD(service, callback);
      }
    }, // FUNCTION: _execute  
    _executeCU: function(alias, service, callback) {
      return service.execute(this.getFields(), {
          'ok': function(fields) {
            this.setFields(fields);
            this._callbackModelReady(callback, true);
          },
          'nok': function(e) {
            this._callbackModelReady(callback, false, 'Service [' + alias + '] Execution Failed.');
          },
          'context': this
        });
    }, // FUNCTION: _executeCU  
    _executeR: function(service, callback) {
      return service.execute(this.getFields(), {
          'ok': function(fields) {
            this.setFields(fields);
            this._callbackModelReady(callback, true);
          },
          'nok': function(e) {
            this._callbackModelReady(callback, false, 'Service [read] Execution Failed.');
          },
          'context': this
        });
    }, // FUNCTION: _executeR  
    _executeD: function(service, callback) {
      return service.execute(this.getFields(), {
          'ok': function() {
            this._callbackModelReady(callback, true);
          },
          'nok': function(e) {
            this._callbackModelReady(callback, false, 'Service [delete] Execution Failed.');
          },
          'context': this
        });
    }, // FUNCTION: _executeD  
    /**
     * Return's an IFieldsMetaPackage for the Store
     * 
     * See NOTE: FieldStorage._getPackage();
     */
    _getFieldsPackage: function() {
      if (this.__oFieldsPackage === null) {
        var metaPackage = this.getMetaPackage();
        if (metaPackage !== null) {
          if (qx.core.Environment.get("qx.debug")) {
            qx.core.Assert.assertInterface(metaPackage, tc.meta.packages.IFormPackage, "[Meta Package] Is not of the expected type!");
          }

          this.__oFieldsPackage = metaPackage.getFields();
        }
      }

      return this.__oFieldsPackage;
    },
    /**
     * Return's an IServicesMetaPackage for the Store
     * 
     * See NOTE: FieldStorage._getPackage();
     */
    _getServicesPackage: function() {
      if (this.__oServicesPackage === null) {
        var metaPackage = this.getMetaPackage();
        if (metaPackage !== null) {
          if (qx.core.Environment.get("qx.debug")) {
            qx.core.Assert.assertInterface(metaPackage, tc.meta.packages.IFormPackage, "[Meta Package] Is not of the expected type!");
          }

          this.__oServicesPackage = metaPackage.getServices();
        }
      }

      return this.__oServicesPackage;
    },
    /**
     * Return's an IFieldsMetaPackage for the Store
     * 
     * See NOTE: FieldStorage._getPackage();
     */
    _getForm: function() {
      if (this.__oFormEntity === null) {
        var metaPackage = this.getMetaPackage();
        if (metaPackage !== null) {
          if (qx.core.Environment.get("qx.debug")) {
            qx.core.Assert.assertInterface(metaPackage, tc.meta.packages.IFormPackage, "[Meta Package] Is not of the expected type!");
          }

          this.__oFormEntity = metaPackage.getForm();
        }
      }

      return this.__oFormEntity;
    },
    /*
     ***************************************************************************
     EXCEPTION GENERATORS
     ***************************************************************************
     */
    _throwActionNotSupported: function(action, supported) {
      if (!supported) {
        throw "The Action [" + action + "] is not supported on the storage system";
      }
    },
    /*
     ***************************************************************************
     PRIVATE METHODS
     ***************************************************************************
     */
    __getService: function(name) {
      if (this._getForm().hasService(name)) {
        return this._getServicesPackage().getService(this._getForm().getService(name));
      }

      throw 'Service [' + name + '] does not exist.';
    }, // FUNCTION: __getService
    _prepareServiceCallback: function(okevent, ok, nok, context) {
      // Setup Default Callback Object
      var event_this = this;
      var callback = {// DEFAULT: No Callbacks - Fire Events
        'ok': function(result) {
          if (okevent === 'loaded') {
            // Loading the Record so it can't be New!!!
            event_this._bNewRecord = false;
          }
          event_this.fireEvent(okevent);
        },
        'nok': function(error) {
          event_this.fireDataEvent('nok', error);
        },
        'context': event_this
      };

      // Update Callback Object with User Parameters
      if (qx.lang.Type.isFunction(ok)) {
        callback['ok'] = ok;
      }

      if (qx.lang.Type.isFunction(nok)) {
        callback['nok'] = nok;
      }

      if (qx.lang.Type.isObject(context)) {
        callback['context'] = context;
      }

      return callback;
    } // FUNCTION: _prepareCallback
  } // SECTION: MEMBERS
});
