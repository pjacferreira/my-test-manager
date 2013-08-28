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

qx.Class.define("tc.meta.forms.Form", {
  extend: qx.ui.form.Form,
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
    "ok": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "nok": "qx.event.type.Data",
    /**
     * Fired when the forms data was submitted back to the datastore
     */
    "formSubmitted": "qx.event.type.Event",
    /**
     * Fired when the form was cancelled or was submitted when no data was changed
     */
    "formCancelled": "qx.event.type.Event"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** The Form's model. */
    model: {
      check: "tc.meta.models.IFormModel",
      apply: "_applyModel",
      event: "changeModel"
    },
    widgetFactory: {
      check: "tc.meta.forms.DefaultWidgetFactory",
      nullable: false,
      apply: "_applyFactory",
      event: "reloadForm"
    }
  }, // SECTION: PROPERTIES
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * @param form {String | tc.meta.model.IFormModel} Form Name to use, or Pre-Created Form Model
   * @param store {tc.meta.datastore.IFieldStorage ? null} Storage to Use for Form
   * @param iv {Object ? null} Field Initialization Values.
   */
  construct: function(form, store, iv) {
    this.base(arguments);

    // MODEL : Create Default or Apply Given
    if (qx.lang.Type.isString(form)) { // Create Default Model
      form = new tc.meta.models.FormModel(form, store);
    } else { // Model Provided
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(form, tc.meta.models.IFormModel, "[form] Is not of the expected type!");
      }

      // Apply the Store if Any Provided
      if (qx.lang.Type.isObject(store)) {
        form.setStore(store);
      }
    }

    // Save Initialization Values
    if (qx.lang.Type.isObject(iv)) {
      this.__formIV = iv;
    }

    // Set Default Widget Factory
    this.setWidgetFactory(new tc.meta.forms.DefaultWidgetFactory());

    // Set the Form Model
    this.setModel(form);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup Variables
    this.__formIV = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _bReady: false, // Is the FORM Ready
    __formIV: null, // Form Data Initialization Vector
    __buttonSubmit: null, // Submit Button
    __fieldWidgets: null, // Array Containing the Field Widgets
    __options: null, // Incoming Form Options
    __disableDataStoreUpdate: false, // Disable Updates to DataStore (So that we don't loop)
    /*
     ***************************************************************************
     PUBLIC METHODS
     ***************************************************************************
     */
    /**
     * Initialize the model.
     *
     * @param iv {iv ? null} Form Fields Initialization Values.
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    initialize: function(iv, callback) {
      /* Can be called
       * 1. Multiple Times - On 1st Successfull Initialization of the Model, the
       * form will not be initialized again.
       * 2. If the model is initialized, and 'iv' specified, then the form will be
       * re-initialized with the values specified in 'iv'
       * 3. 'ok' and 'nok' will always signal the completion of this function, independently
       * of the wether any initialization was done or not..
       */

      // Setup Callback Correctly
      callback = this._prepareCallback(callback);

      if (!this._bReady) { // Initialize the Form
        if (qx.lang.Type.isObject(iv)) {
          this.__formIV = iv;
        }

        this._initializeModel(callback);
      } else { // 
        if (qx.lang.Type.isObject(iv)) {
          this.__formIV = iv;

          // TODO - Set Model New Values
        }

        this._callbackModelReady(callback, true);
      }
    },
    /*
     ***************************************************************************
     PROPERTY APPLY METHODS
     ***************************************************************************
     */
    // property modifier
    _applyModel: function(newModel, oldModel) {
      if (oldModel != null) {
        oldModel.removeListener('ok', this._eventOK, this);
        oldModel.removeListener('nok', this._eventNOK, this);
        oldModel.removeListener('fields-changed', this._eventFieldsChanged, this);
        oldModel.removeListener('loaded', this._eventRecordLoaded, this);
        oldModel.removeListener('saved', this._eventRecordSaved, this);
        oldModel.removeListener('erased', this._eventRecordErased, this);
      }

      newModel.addListener('ok', this._eventOK, this);
      newModel.addListener('nok', this._eventNOK, this);
      newModel.addListener('fields-changed', this._eventFieldsChanged, this);
      newModel.addListener('loaded', this._eventRecordLoaded, this);
      newModel.addListener('saved', this._eventRecordSaved, this);
      newModel.addListener('erased', this._eventRecordErased, this);

      // Re-Initialize the Form
      this._bReady = false;
//      this.initialize(null, null);
    },
    // property modifier
    _applyFactory: function(newFactory, oldFactory) {
      // TODO Clear Form and Rebuild Widgets
    },
    /*
     ***************************************************************************
     MODEL EVENT HANDLERS
     ***************************************************************************
     */
    _eventOK: function(e) {
      // TODO Implement Better Error Handling
      this.fireEvent("ok");
    },
    _eventNOK: function(e) {
      // TODO Implement Better Error Handling
      this.fireDataEvent("nok", e.getData());
    },
    _eventFieldsChanged: function(e) {
      var tuplets = e.getData();

      // TODO (Optimization) Consider a Field : Value tuplet as the data for the event
      if (qx.core.Environment.get('qx.debug')) {
        this.assertTrue(qx.lang.Type.isObject(tuplets), "Invalid Data Value from event[fields-changed].");
      }

      this.__disableDataStoreUpdate = true;
      this.__setWidgetValues(tuplets);
      this.__disableDataStoreUpdate = false;
    },
    _eventRecordLoaded: function(e) {
      /* Why Capture?
       * 1. Does the Record Loaded event Contain any data?
       * 2. When a record is loaded, do we also fire a Fields Changed Event
       * 2.1. if so, then the record loaded provides no additional information!?
       */
    },
    _eventRecordSaved: function(e) {
      // Notify the Application
      this.fireEvent("formSubmitted");
    },
    _eventRecordErased: function(e) {
      // Notify the Application
      this.fireEvent("formSubmitted");
    },
    /*
     ***************************************************************************
     INITIALIZATION FUNCTIONS
     ***************************************************************************
     */
    /**
     * Initialize the Form Model
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     */
    _initializeModel: function(callback) {
      var model = this.getModel();
      if (model.isReady()) {
        this._initializeWidgets(callback);
      } else {
        model.initialize(this.__formIV, {
          'ok': function() {
            this._initializeWidgets(callback);
          },
          'nok': function(message) {
            this._callbackModelReady(callback, false, message);
          },
          'context': this
        });
      }
    },
    /**
     * Initialize the Form Widgets
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     */
    _initializeWidgets: function(callback) {
      if (this.__callStages(this,
              [
                this.__createSubmitButton,
//                this.__createFieldWidgets,
                this.__buildFormUI,
                {"__setWidgetValues": this.__formIV},
              ],
              true)) {
        this._callbackModelReady(callback, true);
      } else {
        this._callbackModelReady(callback, false, 'Failed to initialize widgets');
      }
    },
    /*
     ***************************************************************************
     FORM CREATION FUNCTIONS
     ***************************************************************************
     */
    /**
     *
     * @return {Boolean}
     * @private
     */
    __createSubmitButton: function() {

      // BUTTON: Confirmation
      this.__buttonSubmit = this.getWidgetFactory().createSubmitButton();
      this.__buttonSubmit.addListener("execute", function(e) { // Trigger Form Validation
        if (this.getModel().isDirty()) { // For was modified : Validate and Save if Complete
          this.validate();
        } else { // No Changes
          this.fireEvent("formSubmitted");
        }
      }, this);

      return true;
    },
    /**
     *
     * @return {Boolean}
     * @private
     */
    __buildFormUI: function() {
      var model = this.getModel();

      // Standard Validate Function
      /* TODO Verify if we can create this function as Class member, rather than an anonymous function
       * (i.e. verify what is the 'this' context in which this function is called)
       */
      var functionValidate = function(value, widget) {
        if (widget.getRequired() && !model.isValidFieldValue(widget.__fieldID, value)) {
          widget.setInvalidMessage('Invalid.');
          return false;
        }

        // TODO Allow for custom field validation functions (ex: validate that the password confirmation field is actually == password field)
        // Not Required or Valid
        return true;
      };

      // Initialize Cache for Field Widgets
      if (this.__fieldWidgets === null) {
        this.__fieldWidgets = {};
      }

      var groups = model.getGroupCount();
      var label, fields, key, widget;
      for (var i = 0; i < groups; ++i) {

        // Get the Group Label
        label = model.getGroupLabel(i);
        if (label != null) { // No Label so do not add
          this.addGroupHeader(label);
        }

        // Add Field Widgets
        fields = model.getGroupFields(i);
        for (var j = 0; j < fields.length; ++j) {
          key = fields[j];
          if (!this.__fieldWidgets.hasOwnProperty(key)) {
            widget = this.__createWidget(key);
            this.__fieldWidgets[key] = widget;
            this.add(widget, model.getFieldLabel(key), functionValidate);
          } else {
            // TODO : This is actually a BUG a form can't have 2 references to the same field
          }
        }
      }

      if (groups > 0) { // Add Submit Button
        this.addButton(this.__buttonSubmit);

        // Get Validation Manager
        var manager = this.getValidationManager();

        manager.addListener("complete", function(e) {
          if (this.getValidationManager().isValid()) { // Form Passed Validation
            var model = this.getModel();
            if (model.canSave()) {
              model.save();
            }
          }
        }, this);
        return true;
      }

      return false;
    },
    /*
     ***************************************************************************
     HELPER FUNCTIONS
     ***************************************************************************
     */
    /**
     *
     * @param context
     * @param stages
     * @param abortOnFalse
     * @return {Boolean}
     * @private
     */
    __callStages: function(context, stages, abortOnFalse) {
      var passed = false, stage;

      // Make sure a context is set
      context = context == null ? this : context;

      for (var i = 0; i < stages.length; ++i) {
        stage = stages[i];
        if (qx.lang.Type.isFunction(stage)) {
          passed = stage.call(context);
        } else if (qx.lang.Type.isString(stage)) {
          if (qx.lang.Type.isFunction(context[stage])) {
            passed = context[stage].call(context);
          } else { // Invalid Entries
            continue;
          }
        } else if (qx.lang.Type.isObject(stage)) {
          var found = false, key, parameters
          for (key in stage) {
            if (stage.hasOwnProperty(key) && qx.lang.Type.isFunction(context[key])) {
              found = true;
              parameters = stage[key];
              break;
            }
          }

          if (found) {
            if (parameters != null) {
              passed = qx.lang.Type.isArray(parameters) ?
                      context[key].apply(context, parameters) : // Parameters is an Array of Parameters (so use apply)
                      context[key].call(context, parameters); // Single Parameter (so use call)
            } else {
              passed = context[key].call(context);
            }
          } else { // No Call's Found
            continue;
          }
        } else { // Invalid Entry Skip
          continue;
        }

        if (abortOnFalse && !passed) {
          break;
        }
      }

      return passed;
    },
    __createWidget: function(field) {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertNotNull(field, "Invalid argument 'field'.");
      }

      var widget = this.getWidgetFactory().createFieldWidget(this.getModel().getFieldType(field));
      if (widget) {
        widget.__fieldID = field;
        this.__applyPlaceholder(field, widget);
        this.__applyLength(field, widget);
        this.__applyIsRequired(field, widget);
        if (this.__isReadOnly(field)) {
          widget.setEnabled(false);
        } else {
          widget.addListener("changeValue", function(e) {
            var widget = e.getTarget();
            if (!widget.__flagResetValue) {
              var value = e.getData();
              var model = this.getModel();

              if (!this.__disableDataStoreUpdate) {
                // Set the Value for the Field (Capturing the return value, in the case it has been changed)
                var newValue = model.setFieldValue(widget.__fieldID, value);
                if (newValue !== value) { // Return Value is Different than Sent Value (Modify the Widget's Value)
                  widget.__flagResetValue = true; // Mark the Widget as Having it's Value Modified
                  widget.setValue(newValue);
                }
              }
            } else { // Just Resetting the Widget's Value (avoid a second call to the model's setFieldValue)
              widget.__flagResetValue = false;
            }

          }, this);
        }
      }

      return widget;
    },
    __applyPlaceholder: function(field, widget) {
      var model = this.getModel();
      var def = model.hasFieldDefault(field) ? model.getFieldDefault(field) : null;
      if (def != null) {
        widget.setPlaceholder(def);
      }
    },
    __applyLength: function(field, widget) {
      var length = this.getModel().getFieldLength(field);
      if (length > 0) {
        widget.setMaxLength(length);
      }
    },
    __applyIsRequired: function(name, widget) {
      var model = this.getModel();
      widget.setRequired(model && model.isFieldRequired(name));
    },
    __isReadOnly: function(field) {
      return this.getModel().isFieldReadOnly(field);
    },
    __setWidgetValues: function(values) {
      var key;
      if (values != null) {
        for (key in values) {
          if (values.hasOwnProperty(key) && this.__fieldWidgets.hasOwnProperty(key)) {
            this.__fieldWidgets[key].setValue(values[key].toString());
          }
        }
      } else { // Reset Form Values
        var model = this.getModel();
        var value = null;
        for (key in this.__fieldWidgets) {
          if (this.__fieldWidgets.hasOwnProperty(key)) {
            value = model.getFieldValue(key);
            // TODO Have to make this Generic, setValue only Works for Text Type Fields
            if (value == null) {
              this.__fieldWidgets[key].setValue("");
            } else {
              this.__fieldWidgets[key].setValue(value.toString());
            }
          }
        }
      }

      return true;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS
     ***************************************************************************
     */
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
    } // FUNCTION: _callbackModelReady
  } // SECTION: MEMBERS
});
