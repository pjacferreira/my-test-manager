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
qx.Class.define("tc.meta.models.FormModel", {
  extend: qx.core.Object,
  implement: tc.meta.models.IFormModel,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a new Meta Model has been initialized.
     */
    "ok": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "nok": "qx.event.type.Data",
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
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** The Model's Data Store */
    store: {
      check: "tc.meta.datastores.IFieldStore",
      apply: "_applyStore",
      event: "changeStore"
    }
  }, // SECTION: PROPERTIES
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor
   * 
   * @param form {var} Form Name or Meta Package (if pre-initialize)
   * @param store {tc.meta.datastore.IFieldStorage ? null} A Field Storage Object
   * @param iv {Object ? null} Form Store Initialize Vector (Field -> Value Tuplets to initialize the Store With)
   */
  construct: function(form, store, iv) {
    this.base(arguments);
    // Create or Use Form Meta Package
    if (qx.lang.Type.isString(form)) {
      form = new tc.meta.packages.FormPackage(form);
    }

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(form, tc.meta.packages.IFormPackage, "[form] Is not of the expected type!");
    }
    this._formPackage = form;

    // Create or Use Field Storage
    if (store == null) {
      store = new tc.meta.datastores.FieldStore();
    }

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(store, tc.meta.datastores.IFieldStore, "[store] Is not of the expected type!");
    }
    this.setStore(store);

    // Storage Initialization Vector
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertTrue((iv == null) || qx.lang.Type.isObject(iv), "[iv] Is not of the expected type!");
    }
    this._modelIV = iv;

    // Initialize Field Entities Cache
    this.__fieldEntities = {};
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    this._formPackage = null;
    this._fieldsPackage = null;
    this._formEntity = null;
    this._formStore = null;
    this._modelIV = null;
    this.__fieldEntities = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _formPackage: null,
    _fieldsPackage: null,
    _formEntity: null,
    _modelIV: null,
    _bReady: false,
    _bPendingInitialization: false,
    __fieldEntities: null,
    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _applyStore: function(newStore, oldStore) {
      if (oldStore != null) {
        oldStore.removeListener('ok', this._eventOK, this);
        oldStore.removeListener('nok', this._eventNOK, this);
        oldStore.removeListener('fields-changed', this._eventFieldsChanged, this);
        if (qx.Class.implementsInterface(oldStore, tc.meta.datastores.IRecordStore)) {
          // Extra Form Storage Events
          oldStore.removeListener('loaded', this._eventRecordLoaded, this);
          oldStore.removeListener('saved', this._eventRecordSaved, this);
          oldStore.removeListener('erased', this._eventRecordErased, this);
        }
      }

      // Standard Field Storage Events
      newStore.addListener('ok', this._eventOK, this);
      newStore.addListener('nok', this._eventNOK, this);
      newStore.addListener('fields-changed', this._eventFieldsChanged, this);
      if (qx.Class.implementsInterface(newStore, tc.meta.datastores.IRecordStore)) {
        // Extra Form Storage Events
        newStore.addListener('loaded', this._eventRecordLoaded, this);
        newStore.addListener('saved', this._eventRecordSaved, this);
        newStore.addListener('erased', this._eventRecordErased, this);
      }

      // Re-Initialize the Form
      this._bReady = false;
//      this.initialize(null, null);
    },
    /*
     *****************************************************************************
     EVENT HANDLERS
     *****************************************************************************
     */
    _eventOK: function(e) {
    },
    _eventNOK: function(e) {
    },
    _eventFieldsChanged: function(e) {
    },
    _eventRecordLoaded: function(e) {
    },
    _eventRecordSaved: function(e) {
    },
    _eventRecordErased: function(e) {
    },
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Initialize the model.
     *
     * @param iv {iv ? null} Model's Fields Initialization Values.
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    initialize: function(iv, callback) {
      if (this._bPendingInitialization) {
        throw "Multiple Initilization Calls";
        return;
      }

      this._bPendingInitialization = true;
      callback = this._prepareCallback(callback);

      if (!this._bReady) {
        if (qx.lang.Type.isObject(iv)) {
          this._modelIV = iv;
        }

        this._initializePackage(callback);
      } else {
        if (qx.lang.Type.isObject(iv)) {
          this._modelIV = iv;
          this._initializeStore(callback);
        } else {
          this._bPendingInitialization = false;
          this._callbackModelReady(callback, true);
        }
      }
    },
    /**
     * Can we use the Data Model?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReady: function() {
      return this._bReady;
    },
    /*
     *****************************************************************************
     FIELD (GENERAL PROPERTIES) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Return the type of the value for the field.
     * Note: The return value should be ONE OF the Following Values:
     * 'boolean'
     * 'date'    | 'time'    | 'datetime'
     * 'integer' | 'decimal'
     * 'text'    | 'html'
     *
     * @param field {String} Field ID
     * @return {String} Field Type
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldType: function(field) {
      return this._getFieldEntity(field).getValueType();
    },
    /**
     * Is this a KEY Field (A Field whose value can be used to uniquely identify
     * a record)?
     * Note: Key Fields cannot be NULL
     * 
     * @param field {String} Field ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    isKeyField: function(field) {
      return this._getFieldEntity(field).isKey();
    },
    /**
     * Return's a Field Label
     *
     * @param field {String} Field ID
     * @return {String} Field Label
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldLabel: function(field) {
      return this._getFieldEntity(field).getLabel();
    },
    /**
     * Returns a description of the Field, if any is defined.
     *
     * @param field {String} Field ID
     * @return {String} Field Description String or NULL (if not defined)
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldDescription: function(field) {
      return this._getFieldEntity(field).getDescription();
    },
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @param field {String} Field ID
     * @return {Integer} 0 - If no maximum length defined, > 0 Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldLength: function(field) {
      return this._getFieldEntity(field).getLength();
    },
    /**
     * Return the Precision (number of digits allowed in the decimal part) of
     * decimal type field
     *
     * @param field {String} Field ID
     * @return {Integer} 0 - If not a DECIMAL Type Field or No Decimal Places Allowed,
     *                    > 0 Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldPrecision: function(field) {
      return this._getFieldEntity(field).getPrecision();
    },
    /**
     * Does the the Field Have a Default Value Defined?
     *
     * @param field {String} Field ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    hasFieldDefault: function(field) {
      return this._getFieldEntity(field).hasDefault();
    },
    /**
     * Return default value for the field.
     *
     * @param field {String} Field ID
     * @return {var} Field's default value, NULL if no default defined (or default is NULL)
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldDefault: function(field) {
      return this._getFieldEntity(field).getDefault();
    },
    /**
     * Test if a field can be modified.
     *  
     * @param field {String} Field ID
     * @return {Boolean} 'true' if the field is modifiable, 'false' otherwise.
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldReadOnly: function(field) {
      var f = this._getFieldEntity(field);
      return f.isAutoValue() || !this.canSave();
    },
    /**
     * Test if a field is required.
     *  
     * @abstract
     * @param field {String} Field ID
     * @return {Boolean} 'true' if the field is required, 'false' otherwise.
     * @throws if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldRequired: function(field) {
      return this.isKeyField(field);
    },
    /*
     *****************************************************************************
     FIELD (VALIDATION/TRANSFORMATION) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Does the Form require Validation for the Field?
     *
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    hasFieldValidation: function(field) {
      var f = this._getFieldEntity(field);
      return !f.isNullable() || // Field Value Can't be NULL
              this._formEntity.hasFieldValidation(field);  // Form has Specific Validation for the Field
    },
    /**
     * Verifies if the value is Valid for the Field
     * 
     * @param field {String} Field ID
     * @param value {var} Value to Test
     * @return {Boolean} Returns TRUE if the Value is Valid for the Field, FALSE Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    isValidFieldValue: function(field, value) {
      var f = this._getFieldEntity(field);
      if (!f.isNullable() && (value == null)) {
        return false;
      }

      // TODO: APPLY SPRECIFIC FORM VALIDATION      
      return true;
    },
    /**
     * Does the field have a Transformation Function?
     *
     * @param field {String} Field Name to Test
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    hasFieldTransform: function(field) {
      var f = this._getFieldEntity(field);
      return f.isTrimmed() || // Field Value Can Be Trimmed
              f.isEmptyNull() || // Field Value is Empty if an Empty String
              this._formEntity.hasFieldTransform(field);  // Form has Specific Transform for the Field
    },
    /**
     * Does the field have a Transformation Function?
     *
     * @param field {String} Field Name to Test
     * @param value {var} Field Value
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    applyFieldTransform: function(field, value) {
      var f = this._getFieldEntity(field);
      if (qx.lang.Type.isString(value)) {
        // Trim Field Value?
        if (f.isTrimmed()) { // See if we have to trim  the value
          value = value.trim();
        }

        // Convert Empty Field to NULL?
        if (f.isEmptyNull() && (value.length === 0)) {
          value = null;
        }
      }

      // TODO: APPLY SPRECIFIC FORM TRANSFORM      
      return value;
    },
    /*
     *****************************************************************************
     FIELD (VALUE) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Test if a field has a value Set.
     *  
     * @param field {String} Field ID
     * @return {Boolean} 'true' field has a value defined, 'false' otherwise.
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldSet: function(field) {
      this._throwIsModelReady();

      return this.getStore().isFieldSet(field);
    },
    /**
     * Was the field value modified (i.e. Dirty, pending changes)?
     *
     * @param field {String} Field ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    isFieldDirty: function(field) {
      this._throwIsModelReady();

      return this.getStore().isFieldDirty(field);
    },
    /**
     * Retrieve Field Value
     *
     * @param field {String} Field ID
     * @return {var} Field Value
     * @throw if the Model has not been initialized or Field Does not exist in Model
     */
    getFieldValue: function(field) {
      this._throwIsModelReady();

      return this.getStore().getField(field);
    },
    /**
     * Return a Field Value Map, containing the current Field Values
     *
     * @return {Object} Field, Value Tuplets
     * @throw if the Model has not been initialized
     */
    getFieldValues: function() {
      this._throwIsModelReady();

      return this.getStore().getFields();
    },
    /**
     * Modify the Field's Value
     *
     * @param field {String} Field ID
     * @param value {var} Field Value
     * @return {var} The Incoming Field Value or The Actual Value Set (Note: the Value may be modified if Trim and Empty-as-Null are Set)
     * @throw if the Model has not been initialized, Field Does not exist in Model or
     *   Value is invalid (after transformation and valiation applied).
     */
    setFieldValue: function(field, value) {
      if (this.hasFieldTransform(field)) {
        value = this.applyFieldTransform(field, value);
      }

      this._throwValueInvalid(field, !this.hasFieldValidation(field) ||
              this.isValidFieldValue(field, value));

      this.getStore().setField(field, value);
      return value;
    },
    /**
     * Bulk Modifies the Model
     *
     * @param map {Object} Field Value Tuplets
     * @return {Object} Field Value Tuplets of All Modified Fields
     * @throw if the Model has not been initialized
     */
    setFieldValues: function(map) {
      this._throwIsModelReady();

      // Process 
      for (var field in map) {
        if (map.hasOwnProperty(field)) {
          try {
            map[field] = this.setFieldValue(field, map[field]);
          } catch (e) {
            delete(map[field]);
          }
        }
      }

      return map;
    },
    /**
     * Reset's All Modified Values Back to the Last Saved State
     *
     * @param field {String ? null} Field ID or NULL if we would like to reset all fields rather than just a single field.
     * @return {var} if Single Field is being Reset then New Original Field Value is Returned
     *                if All or Fields are being Reset a Field, Value Tuplets of All Modified Fields (with new, original value) or 
     *                NULL if No Changes
     * @throw if the Model has not been initialized
     */
    resetFields: function(field) {
      this._throwIsModelReady();

      return this.getStore().reset();
    },
    /*
     *****************************************************************************
     FORM RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Returns the Form's Title
     *
     * @return {String} Form Title
     * @throw if the Model has not been initialized
     */
    getTitle: function() {
      this._throwIsModelReady();

      return this._formEntity.getTitle();
    },
    /**
     * Returns the number of field groups in the form. The form should always
     * have at the very minimum 1 group.
     *
     * @return {Integer} Number of Groups
     * @throw if the Model has not been initialized
     */
    getGroupCount: function() {
      this._throwIsModelReady();

      return this._formEntity.getGroupCount();
    },
    /**
     * Returns the Label for the Group. If the group does not have a label,
     * NULL will be returned.
     *
     * @param group {Integer} Group ID 0 to getGroupCount() -1 
     * @return {String ? null} Group Label
     * @throw if the Model has not been initialized
     */
    getGroupLabel: function(group) {
      this._throwIsModelReady();

      return this._formEntity.getGroupLabel(group);
    },
    /**
     * Returns the list of fields in the Group.
     *
     * @param group {Integer} Group ID
     * @return {String[]} Array of Field IDs in the Group
     * @throw if the Model has not been initialized
     */
    getGroupFields: function(group) {
      this._throwIsModelReady();

      return this._formEntity.getGroupFields(group);
    },
    /**
     * Does the form have a Global Validation?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized
     */
    hasFormValidation: function() {
      this._throwIsModelReady();

      return this._formEntity.hasFormValidation();
    },
    /**
     * Applies the Global Form Validation to all the fields in the form.
     *
     * @return {Object} Returns Hash Map of Field, Message Tuplets for all Fields 
     *   that contain invalid values as per the Form Validation.
     * @throw if the Model has not been initialized
     */
    applyFormValidation: function() {
      this._throwIsModelReady();

      // TODO : Implement
    },
    /**
     * Does the form have global Transformation Function?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized
     */
    hasFormTransform: function() {
      this._throwIsModelReady();

      return this._formEntity.hasFormTransform();
    },
    /**
     * Applies a Global Form Transformation, if any exists. A "fields-changed" 
     * event will be fired with the values of all modified fields.
     *
     * @throw if the Model has not been initialized
     */
    applyFormTransform: function() {
      this._throwIsModelReady();

      // TODO : Implement
    },
    /*
     *****************************************************************************
     STORAGE (GENERAL) MEMBERS
     *****************************************************************************
     */
    /**
     * Model Data is Read Only?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized
     */
    isReadOnly: function() {
      this._throwIsModelReady();

      throw "Deprecated: Use canSave";
      return this.getStore().isReadOnly();
    },
    /**
     * Model Data has been modified (i.e. Dirty with pending changes)?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized
     */
    isDirty: function() {
      this._throwIsModelReady();

      return this.getStore().isDirty();
    },
    /**
     * Is this a New Record?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Model has not been initialized
     */
    isNew: function() {
      this._throwIsModelReady();

      // If Record Store use Function - Otherwise it is always a new New Record
      var store = this.getStore();
      return qx.Class.implementsInterface(store, tc.meta.datastores.IRecordStore) ? store.isNew() : true;
    },
    /*
     *****************************************************************************
     STORAGE (PERSISTANCE) RELATED MEMBERS
     *****************************************************************************
     */
    /**
     * Can we Load the Form's Data?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canLoad: function() {
      this._throwIsModelReady();

      // If Form Store use Function - Otherwise always false
      var store = this.getStore();
      return qx.Class.implementsInterface(store, tc.meta.datastores.IRecordStore) ? store.canLoad() : false;
    },
    /**
     * Can we Save the Form's Data?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canSave: function() {
      this._throwIsModelReady();

      // If Form Store use Function - Otherwise always false
      var store = this.getStore();
      if (!store.isReadOnly()) {
        return qx.Class.implementsInterface(store, tc.meta.datastores.IRecordStore) ? store.canSave() : false;
      }

      return false;
    },
    /**
     * Can we Erase the Form's Data, from the Backend Store?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canErase: function() {
      this._throwIsModelReady();

      // If Form Store use Function - Otherwise always false
      var store = this.getStore();
      return qx.Class.implementsInterface(store, tc.meta.datastores.IRecordStore) ? store.canErase() : false;
    },
    /**
     * Try to load the Form's Data
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @throw if the Model has not been initialized or The action is not possible on Model
     */
    load: function(callback) {
      this._throwActionNotSupported('load', this.canLoad());

      /* TODO : When we call the Store, events are fired or callbacks are called,
       * so we either have to intercept the events and re-forward, or create 
       * callback wrappers...
       */
      return this.getStore().load(callback);
    },
    /**
     * Try to save the Model's 
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @throw if the Model has not been initialized or The action is not possible on Model
     */
    save: function(callback) {
      this._throwActionNotSupported('save', this.canSave());

      return this.getStore().save(callback);
    },
    /**
     * Try to erase the Form Record
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @throw if the Model has not been initialized or The action is not possible on Model
     */
    erase: function(callback) {
      this._throwActionNotSupported('erase', this.canErase());

      return this.getStore().erase(callback);
    },
    /*
     *****************************************************************************
     HELPER (INITIALIZATION) METHODS
     *****************************************************************************
     */
    /**
     * Try to erase the Form Record
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    _initializePackage: function(callback) {
      if (this._formPackage.isReady()) {
        this._initializeStore(callback);
      } else { // Initialize Form Package
        this._formPackage.initialize({
          'ok': function() {
            this._initializeStore(callback);
          },
          'nok': function(message) {
            this._bPendingInitialization = false;
            this._callbackModelReady(callback, false, message);
          },
          'context': this
        });
        ;
      }
    },
    /**
     * Try to erase the Form Record
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    _initializeStore: function(callback) {
      if (this.getStore().isReady()) {
        this._callbackModelReady(callback, true);
      } else {
        // Initialize the Store
        this.getStore().setMetaPackage(this._formPackage);
        this.getStore().initialize(this._modelIV, {
          'ok': function() {
            this._formEntity = this._formPackage.getForm();
            this._fieldsPackage = this._formPackage.getFields();
            this._bReady = true;
            this._bPendingInitialization = false;
            this._callbackModelReady(callback, true);
          },
          'nok': function(message) {
            this._bPendingInitialization = false;
            this._callbackModelReady(callback, false, message);
          },
          'context': this
        });
      }
    },
    _prepareCallback: function(callback) {
      // Setup Default Callback Object
      var event_this = this;
      var newCallback = {// DEFAULT: No Callbacks - Fire Events
        'ok': function(result) {
          event_this.fireEvent('ok');
        },
        'nok': function(error) {
          event_this.fireDataEvent('nok', error);
        },
        'context': event_this
      };

      // Update Callback Object with User Parameters
      if (qx.lang.Type.isObject(callback)) {
        if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
          newCallback['ok'] = callback['ok'];
        }

        if (callback.hasOwnProperty('nok') && qx.lang.Type.isFunction(callback['nok'])) {
          newCallback['nok'] = callback['nok'];
        }

        if (callback.hasOwnProperty('context') && qx.lang.Type.isObject(callback['context'])) {
          newCallback['context'] = callback['context'];
        }
      }

      return newCallback;
    }, // FUNCTION: _buildCallback
    _callbackModelReady: function(callback, ok, message) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertObject(callback, "[callback] is not of the expected type!");
      }

      if (ok) {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertFunction(callback['ok'], "[callback] is missing [ok] function!");
          qx.core.Assert.assertObject(callback['context'], "[callback] is missing call [context]!");
        }

        callback['ok'].call(callback['context']);
      } else {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertFunction(callback['nok'], "[callback] is missing [nok] function!");
          qx.core.Assert.assertObject(callback['context'], "[callback] is missing call [context]!");
        }

        callback['nok'].call(callback['context'], message);
      }
    }, // FUNCTION: _callbackModelReady
    /*
     *****************************************************************************
     HELPER (GENERAL) METHODS
     *****************************************************************************
     */
    _getFieldEntity: function(field) {
      this._throwIsModelReady();

      // Change Field ID so that it doesn't conflict with Javascript's Internal Properties
      var id = '?' + field;
      if (!this.__fieldEntities.hasOwnProperty(id)) {
        this._throwFieldNotExists(field, this._fieldsPackage.hasField(field));
        this.__fieldEntities[id] = this._fieldsPackage.getField(field);
      }

      return this.__fieldEntities[id];
    },
    /*
     *****************************************************************************
     EXCEPTION GENERATORS
     *****************************************************************************
     */
    _throwIsModelReady: function() {
      if (!this.isReady()) {
        throw "Model has not been initialized";
      }
    },
    _throwFieldNotExists: function(field, exists) {
      if (!exists) {
        throw "The Field [" + field + "] does not belong to the model";
      }
    },
    _throwValueInvalid: function(field, valid) {
      if (!valid) {
        throw "The Field's [" + field + "] is not valid";
      }
    },
    _throwActionNotSupported: function(action, supported) {
      if (!supported) {
        throw "The Action [" + action + "] is not supported on the model";
      }
    }
  } // SECTION: MEMBERS
});
