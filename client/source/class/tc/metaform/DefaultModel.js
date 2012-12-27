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
    "modelReady": "qx.event.type.Event",

    /**
     * Fired when Field Values have been loaded from the Data Store
     */
    "dataLoaded": "qx.event.type.Event",

    /**
     * Fired when Field Values have been saved back to the Data Store
     */
    "dataSaved": "qx.event.type.Event",

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
      check: "tc.metaform.interfaces.IFormDataStore",
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
  construct: function (metadataModel, dataStore, keyValues) {
    this.base(arguments);

    // Save the Key Values (if Any)
    this.__keyValues = keyValues;

    // Set and Initialize the Model and Data Source
    this.setMetaModel(metadataModel);
    this.setDataStore(dataStore);
  },

  /**
   *
   */
  destruct: function () {
    this.base(arguments);
  },

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __ready: false,
    __loaded: false,
    __keyValues: null,

    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _applyModel: function (newModel, oldModel) {

      if (oldModel != null) {
        // Remove Listeners from the Old Model
        oldModel.removeListener("modelReady", this.__modelLoaded, this);
        oldModel.removeListener("modelInvalid", this.__error, this);
      }

      // Add Listeners to the New Model
      newModel.addListener("modelReady", this.__modelLoaded, this);
      newModel.addListener("modelInvalid", this.__error, this);

      // Re-Initialize Required
      this.__ready = false;

      // Initialize the Model
      if (!newModel.init()) {
        // Failed to Start Initialization Process
        this.fireEvent("error");
      }
    },

    // property modifier
    _applyStore: function (newStore, oldStore) {

      if (oldStore != null) {
        // Remove Listeners from the Old Data Store
        oldStore.removeListener("dataLoaded", this.__dataStoreLoaded, this);
        oldStore.removeListener("dataSaved", this.__dataStoreSaved, this);
        oldStore.removeListener("error", this.__error, this);
      }

      // Add Listeners to the New Data Store
      newStore.addListener("dataLoaded", this.__dataStoreLoaded, this);
      newStore.addListener("dataSaved", this.__dataStoreSaved, this);
      newStore.addListener("error", this.__error, this);
    },

    /*
     *****************************************************************************
     Event HANDLERs
     *****************************************************************************
     */
    __modelLoaded: function () {
      this.__ready = true;

      this.fireEvent("modelReady");
    },

    __dataStoreLoaded: function () {
      this.fireEvent("dataLoaded");
    },

    __dataStoreSaved: function () {
      this.fireEvent("dataSaved");
    },

    __error: function () {
      this.fireEvent("error");
    },

    /*
     *****************************************************************************
     METADATA RELATED METHODS
     *****************************************************************************
     */
    // interface implementation
    init: function () {
      // TODO Implement
      return false;
    },

    // interface implementation
    getFieldMeta: function (name) {
      // If Ready, then return the Meta Data for a Field if it exists
      return this.__ready ? this.getMetaModel().getFieldMeta(name) : null;
    },

    // interface implementation
    getFormTitle: function () {
      // If Ready, then return the Meta Data for a Field if it exists
      return this.__ready ? this.getMetaModel().getFormTitle() : null;
    },

    // interface implementation
    getFormFields: function () {
      // If Ready, then return the list of unique Fields in the Form
      return this.__ready ? this.getMetaModel().getFormFields() : null;
    },

    // interface implementation
    getGroupCount: function () {
      // If Ready, then return the Number of Groups in the Meta Model
      return this.__ready && (this.getMetaModel() != null) ? this.getMetaModel().getGroupCount() : 0;
    },

    // interface implementation
    getGroupLabel: function (index) {
      // If Ready, then return the Label for the Group from the Meta Model
      return this.__ready && (this.getMetaModel() != null) ? this.getMetaModel().getGroupLabel(index) : null;
    },

    // interface implementation
    getGroupFields: function (index) {
      // If Ready, then return the Fields for the Group from the Meta Model
      return this.__ready && (this.getMetaModel() != null) ? this.getMetaModel().getGroupFields(index) : null;
    },

    // interface implementation
    isFieldRequired: function (name) {
      return this.__ready && !this.__fieldProperty(name, 'nullable', true);
    },

    /*
     *****************************************************************************
     FIELD DATA RELATED METHODS
     *****************************************************************************
     */
    // interface implementation
    load: function () {
      // If ready Load the Data from the Source
      return this.__ready && (this.getDataStore() !== null) ? this.getDataStore().load(this.__keyValues) : false;
    },

    // interface implementation
    save: function () {
      // If ready Save the Current State of the Data to the Source
      return this.__ready && (this.getDataStore() !== null) ? this.getDataStore().save() : false;
    },

    // interface implementation
    isModified: function () {
      // If ready Save the Current State of the Data to the Source
      return this.__ready && (this.getDataStore() !== null) ? this.getDataStore().isModified() : false;
    },

    // interface implementation
    getData: function () {
      return this.getDataStore().getValues();
    },

    // interface implementation
    setData: function (data) {
      return this.__ready && (this.getDataStore() !== null) && qx.lang.Type.isObject(data) ? this.getDataStore().setValues(data) : false;
    },

    // interface implementation
    getFieldValue: function (name) {
      name = tc.util.String.nullOnEmpty(name);
      return this.__ready && (this.getDataStore() !== null) && (name != null) ? this.getDataStore().getValue(name) : null;
    },

    // interface implementation
    setFieldValue: function (name, value) {
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
          this.getDataStore().setValue(name, value);
          return value;
        }
      }

      return null;
    },

    // interface implementation
    isFieldDataValid: function (name) {
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
    __fieldProperty: function (field, property, defaultValue) {
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
    __formProperty: function (property, defaultValue) {
      var form = this.__ready ? this.getMetaModel().getFormMeta() : null;

      return form ? tc.util.Object.valueFromPath(form, property, {'default': defaultValue}) : defaultValue;
    }
  }
});
