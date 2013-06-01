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
qx.Class.define("tc.metaform.DefaultModel", {
  extend: qx.core.Object,
  implement: tc.metaform.interfaces.IFormModel,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a new Meta Model has been initialized (this allows model load to be
     * asynchronous)
     */
    "model-ready": "qx.event.type.Event",
    /**
     * Fired when any time Field Values are been modified. 
     * The returned data is:
     * 1. A string, with the name of the field modified, if a single field is
     *    modified, or
     * 2. An array of strings, containing the list of fields modified, if more than
     *    one field is modified.
     */
    "fields-changed": "qx.event.type.Data",
    /**
     * Fired when Field Values have been loaded from the Data Store
     */
    "record-loaded": "qx.event.type.Event",
    /**
     * Fired when Field Values have been saved back to the Data Store
     */
    "record-saved": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "error": "qx.event.type.Event"
  },
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** The Form's Metadata Model. */
    metaModel: {
      check: "tc.metaform.interfaces.IFormMetadataModel",
      nullable: false,
      apply: "_applyModel",
      event: "reload"
    },
    /** The Form's Data Source. */
    dataStore: {
      check: "tc.meta.interfaces.IFieldsDataStore",
      nullable: false,
      apply: "_applyStore",
      event: "reloadForm"
    }
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   * @param metadataModel
   * @param dataStore
   * @param keyValues
   */
  construct: function(metadataModel, dataStore, keyValues) {
    this.base(arguments);

    // Save the Key Values (if Any)
    this.__initialValues = keyValues;

    // Set and Initialize the Model and Data Source
    this.setMetaModel(metadataModel);
    this.setDataStore(dataStore);
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
    __ready: false,
    __dsSkipEvents: false,
    __initialValues: null,
    __restoreCurrent: null,
    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _applyModel: function(newModel, oldModel) {

      if (oldModel != null) {
        // Remove Listeners from the Old Model
        oldModel.removeListener('model-ready', this.__modelLoaded, this);
        oldModel.removeListener('error', this.__error, this);
      }

      // Add Listeners to the New Model
      newModel.addListener('model-ready', this.__modelLoaded, this);
      newModel.addListener('error', this.__error, this);

      // Re-Initialize Required
      this.__ready = false;

      // Initialize the Model
      newModel.init();
    },
    // property modifier
    _applyStore: function(newStore, oldStore) {

      if (oldStore != null) {
        // Remove Listeners from the Old Data Store
        oldStore.removeListener("change-fields-meta", this._dsChangeMetaFields, this);
        oldStore.removeListener("change-fields-value", this._dsChangeFieldValues, this);
        if (qx.Class.implemenetsInterface(oldStore, tc.meta.form.interfaces.IRecordDataStore)) {
          oldStore.removeListener("change-services-meta", this._dsChangeMetaServices, this);
          oldStore.removeListener("execute-ok", this._dsServiceOK, this);
          oldStore.removeListener("execute-nok", this._dsServiceNOK, this);
        }
      }

      // Add Listeners to the New Data Store
      newStore.addListener("change-fields-meta", this._dsChangeMetaFields, this);
      newStore.addListener("change-fields-value", this._dsChangeFieldValues, this);
      if (qx.Class.implementsInterface(newStore, tc.meta.form.interfaces.IRecordDataStore)) {
        newStore.addListener("change-services-meta", this._dsChangeMetaServices, this);
        newStore.addListener("execute-ok", this._dsServiceOK, this);
        newStore.addListener("execute-nok", this._dsServiceNOK, this);
      }
    },
    /*
     *****************************************************************************
     Event HANDLERs
     *****************************************************************************
     */
    __modelLoaded: function(e) {
      // Continue On to Make Sure the DataStore is Synchronized
      // Get Meta Model
      var metamodel = this.getMetaModel();

      // Get Metadata
      var metadata = metamodel.getFormMeta();

      // Make Sure the Data Store is In-Sync with the MetaModel
      var datastore = this.getDataStore();
      if (datastore != null) {
        var restoreOriginal = null;
        try {
          if (datastore.isInitialized()) {
            // Save the State of the Current Data Store
            restoreOriginal = datastore.getFieldsValues(true);
            this.__restoreCurrent = datastore.getFieldsValues();
          }
        } catch (e) {
          // Take into Account that the Datastore Might not have the Metadata Set
          this.info("Datastore Not Initialized.");
        }

        // Reset the Metdata for the Datastore (and Per Consequence all the Field Data)
        this.__dsSkipEvents = true;
        datastore.setFieldsMeta(metamodel.getFieldsMeta());
        if (qx.Class.implementsInterface(datastore, tc.meta.form.interfaces.IRecordDataStore)) {
          datastore.setServicesMeta(metamodel.getServicesMeta());
        }
        this.__dsSkipEvents = false;

        /* NOTES:
         * 1. If we move the firing of the event, to before we set the Metadata
         * Information, for the datastore, then
         * a) When the Form tries to build the Widgets
         * b) The widget will try to retrieve the current values from the
         * datastore
         * c) Which will throw an exception, since the datastore has no
         * field definitions set (no metadata set)
         * 2. If we mode the firing of the event, to after we initialize the
         * datastore, with values, then:
         * a) When the values are changed in the datastore,
         * b) the datastore will fire events, to notify of those changes,
         * c) But since no Form Widgets have been built (only after the model-ready
         * is captured by the form, will the widgets be built)
         * d) There will be no widget to update, and therefore, the form widgets
         * will contain invalid values.
         * 
         * Question to study:
         * 1. When the widget are built, they will try to retrieve the current
         * value for the field, so why didn't they displa the correct value,
         * when we fired the event, only at the end of this function?
         * i.e. Why is (2) Correct, when it shouldn't really matter.
         */
        // Form Model Ready - DataStore Metadata Synchronized
        this.__ready = true;
        this.fireEvent('model-ready');

        // Restore Datastore - Saved or Initial Values
        var bReload = false;
        if (restoreOriginal != null) {
          // Restore Original State
          datastore.setFieldsValues(restoreOriginal, true);
          bReload = true;
        } else if (this.__initialValues != null) {
          // Set Initial Values
          datastore.setFieldsValues(this.__initialValues, true);
          bReload = true;
        }

        // Reload the Record from the Store, if Possible
        if (bReload && qx.Class.implementsInterface(datastore, tc.meta.form.interfaces.IRecordDataStore)) {
          if (datastore.hasService('read') && datastore.canExecute('read')) {
            bReload = false;
            datastore.execute('read');
          }
        }

        // Restore Current State if Necessary
        if (bReload && (this.__restoreCurrent != null)) {
          datastore.setFieldsValues(this.__restoreCurrent);
          this.__restoreCurrent = null;
        }
      }
    },
    __error: function(e) {
      this.fireEvent('error');
    },
    _dsChangeMetaFields: function(e) {
      if (!this.__dsSkipEvents) {
        /* Metadata for Fields Store Changed
         * 1. If we have Initial Values for the Data Store, reload them into the Store
         * 2. If it's a Record Data Store, try to complete the values, with those returned by the Services.
         */
        var datastore = this.getDataStore();
        if (this.__ready) { // Metadata Loaded?
          // Set Initial Values
          if (this.__initialValues != null) {
            // Set Initial Values
            datastore.setFieldsValues(this.__initialValues, true);
          }

          // Reload the Record from the Store, if Possible
          if (qx.Class.implementsInterface(datastore, tc.meta.form.interfaces.IRecordDataStore)) {
            if (datastore.hasService('read') && datastore.canExecute('read')) {
              datastore.execute('read');
            }
          }
        }
      }
    },
    _dsChangeFieldValues: function(e) {
      if (!this.__dsSkipEvents) {
        this.fireDataEvent('fields-changed', e.getData());
      }
    },
    _dsChangeMetaServices: function(e) {
    },
    _dsServiceOK: function(e) {
      var service = e.getData();

      switch (service) {
        case 'create':
          this.fireEvent('record-saved');
          break;
        case 'read':
          if (this.__restoreCurrent != null) { // If we have a restore Point - Load it TOO
            this.getDataStore().setFieldsValues(this.__restoreCurrent);
            this.__restoreCurrent = null;
          }
          this.fireEvent('record-loaded');
          break;
        case 'update':
          this.fireEvent('record-saved');
          break;
        default:
          this.warn('Unsupported service[' + service + '] was called.');
      }
    },
    _dsServiceNOK: function(e) {
      var service = e.getData();
      this.error('Service[' + service + '] called end in error.');
      this.fireEvent("error");
    },
    /*
     *****************************************************************************
     METADATA RELATED METHODS
     *****************************************************************************
     */
    // interface implementation
    init: function() {
      return true;
    },
    // interface implementation
    getFieldMeta: function(name) {
      // If Ready, then return the Meta Data for a Field if it exists
      return this.__ready ? this.getMetaModel().getFieldMeta(name) : null;
    },
    // interface implementation
    getFormTitle: function() {
      // If Ready, then return the Meta Data for a Field if it exists
      return this.__ready ? this.getMetaModel().getFormTitle() : null;
    },
    // interface implementation
    getFormFields: function() {
      // If Ready, then return the list of unique Fields in the Form
      return this.__ready ? this.getMetaModel().getFormFields() : null;
    },
    // interface implementation
    getGroupCount: function() {
      // If Ready, then return the Number of Groups in the Meta Model
      return this.__ready && (this.getMetaModel() != null) ? this.getMetaModel().getGroupCount() : 0;
    },
    // interface implementation
    getGroupLabel: function(index) {
      // If Ready, then return the Label for the Group from the Meta Model
      return this.__ready && (this.getMetaModel() != null) ? this.getMetaModel().getGroupLabel(index) : null;
    },
    // interface implementation
    getGroupFields: function(index) {
      // If Ready, then return the Fields for the Group from the Meta Model
      return this.__ready && (this.getMetaModel() != null) ? this.getMetaModel().getGroupFields(index) : null;
    },
    // interface implementation
    isFieldRequired: function(name) {
      return this.__ready && !this.__fieldProperty(name, 'nullable', true);
    },
    /*
     *****************************************************************************
     FIELD DATA RELATED METHODS
     *****************************************************************************
     */
    // interface implementation
    load: function() {
      // If ready Load the Data from the Source
      return this.__ready && (this.getDataStore() !== null) ? this.getDataStore().load(this.__initialValues) : false;
    },
    // interface implementation
    save: function() {
      // If ready Save the Current State of the Data to the Source
      return this.__ready && (this.getDataStore() !== null) ? this.getDataStore().save() : false;
    },
    // interface implementation
    isModified: function() {
      // If ready Save the Current State of the Data to the Source
      return this.__ready && (this.getDataStore() !== null) ? this.getDataStore().isModified() : false;
    },
    // interface implementation
    getData: function() {
      return this.getDataStore().getFieldsValues();
    },
    // interface implementation
    setData: function(data) {
      return this.__ready && (this.getDataStore() !== null) && qx.lang.Type.isObject(data) ? this.getDataStore().setFieldsValues(data) : false;
    },
    // interface implementation
    getFieldValue: function(name) {
      name = tc.util.String.nullOnEmpty(name);
      return this.__ready && (this.getDataStore() !== null) && (name != null) ? this.getDataStore().getFieldValue(name) : null;
    },
    // interface implementation
    setFieldValue: function(name, value) {
      if (this.__ready) {
        var field = this.getFieldMeta(name);

        if (field) {
          // Trim the Fields Value (if set)
          if (this.__fieldProperty(field, 'trim', true)) {
            value = tc.util.String.nullOnEmpty(value, true);
          }

          /* TODO Add Transformation Capabilities
           * Widget displays one value, but the Data Store uses another value (example mapping booleans true,false to 0,1)
           * Different Type of Transormations (From simple One to One Mappings, to complex functions)
           */
          // How to handle NULL values
          if ((value === null) && (this.__fieldProperty(field, 'empty', 'as-null') !== 'as-null')) {
            value = ""; //Treat as Empty String
          }

          /* returns previous value */
          this.getDataStore().setFieldValue(name, value);
          return value;
        }
      }

      return null;
    },
    // interface implementation
    isFieldDataValid: function(name) {
      if (this.__ready) {
        var field = this.getFieldMeta(name);

        if (field) {
          var value = this.getFieldValue(name);
          value = tc.util.String.nullOnEmpty(value, this.__fieldProperty(field, 'trim', true));
          if (value === null) {
            return this.__fieldProperty(field, 'nullable', true);
          } else {
            return true;
          }
        }
      }

      return false;
    },
    /*
     *****************************************************************************
     HELPER FUNCTIONS
     *****************************************************************************
     */

    /**
     *
     * @param field
     * @param property
     * @param defaultValue
     * @return {String}
     * @private
     */
    __fieldProperty: function(field, property, defaultValue) {
      if (qx.lang.Type.isString(field)) {
        field = this.getFieldMeta(field);
      }

      return field ? tc.util.Object.valueFromPath(field, property, {'default': defaultValue}) : defaultValue;
    },
    /**
     *
     * @param property
     * @param defaultValue
     * @return {String}
     * @private
     */
    __formProperty: function(property, defaultValue) {
      var form = this.__ready ? this.getMetaModel().getFormMeta() : null;

      return form ? tc.util.Object.valueFromPath(form, property, {'default': defaultValue}) : defaultValue;
    }
  }
});
