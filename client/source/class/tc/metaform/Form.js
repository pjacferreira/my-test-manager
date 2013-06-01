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
qx.Class.define("tc.metaform.Form", {
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
    "formReady": "qx.event.type.Event",
    /**
     * Fired when the forms data was submitted back to the datastore
     */
    "formSubmitted": "qx.event.type.Event",
    /**
     * Fired when the form was cancelled or was submitted when no data was changed
     */
    "formCancelled": "qx.event.type.Event",
    /**
     * Fired on any error
     */
    "error": "qx.event.type.Event"
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   */
  construct: function(model, fieldValues, options) {
    this.base(arguments);

    this.__fieldValues = fieldValues;
    this.__options = options;
    this.__fieldWidgets = {};

    // Set Default Widget Factory
    this.setWidgetFactory(new tc.metaform.DefaultWidgetFactory());
    // Set the Forms Model
    if (model) {
      this.setFormModel(model);
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
  },
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** The Form's model. */
    formModel: {
      check: "tc.metaform.interfaces.IFormModel",
      apply: "_applyModel",
      event: "changeFormModel"
    },
    widgetFactory: {
      check: "tc.metaform.interfaces.IFormWidgetFactory",
      nullable: false,
      apply: "_applyFactory",
      event: "reloadForm"
    }
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __buttonSubmit: null, // Submit Button
    __fieldWidgets: null, // Array Containing the Field Widgets
    __fieldValues: null, // Field Values to use in the Form Initialization
    __options: null, // Incoming Form Options
    __disableDataStoreUpdate: false, // Disable Updates to DataStore (So that we don't loop)

    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _applyModel: function(newModel, oldModel) {
      if (oldModel != null) {
        oldModel.removeListener('model-ready', this._modelReady, this);
        oldModel.removeListener('fields-changed', this._modelFieldsChanged, this);
        oldModel.removeListener('record-loaded', this._modelRecordLoaded, this);
        oldModel.removeListener('record-saved', this._modelRecordSaved, this);
        oldModel.removeListener('error', this._error, this);
      }

      newModel.addListener('model-ready', this._modelReady, this);
      newModel.addListener('fields-changed', this._modelFieldsChanged, this);
      newModel.addListener('record-loaded', this._modelRecordLoaded, this);
      newModel.addListener('record-saved', this._modelRecordSaved, this);
      newModel.addListener('error', this._error, this);

      // Initialize the Model to Start the Form Build Process
      newModel.init();
    },
    // property modifier
    _applyFactory: function(newFactory, oldFactory) {

      // TODO Clear Form and Rebuild Widgets
    },
    /*
     *****************************************************************************
     EVENT HANDLERS
     *****************************************************************************
     */
    _error: function(e) {
      // TODO Implement Better Error Handling
      this.fireEvent("error");
    },
    _modelFieldsChanged: function(e) {
      var tuplets = e.getData();

      // TODO (Optimization) Consider a Field : Value tuplet as the data for the event
      if (qx.core.Environment.get('qx.debug')) {
        this.assertTrue(qx.lang.Type.isObject(tuplets), "Invalid Data Value from event[fields-changed].");
      }

      this.__disableDataStoreUpdate = true;
      this.__setWidgetValues(tuplets);
      this.__disableDataStoreUpdate = false;
    },
    _modelRecordLoaded: function(e) {
    },
    _modelRecordSaved: function(e) {
      // Notify the Application
      this.fireEvent("formSubmitted");
    },
    _modelReady: function(e) {
      if (this.__callStages(this, [
        this.__createSubmitButton,
        this.__createFieldWidgets,
        {"__setWidgetValues": this.__fieldValues},
        this.__buildFormUI
      ], true)) {
        this.fireEvent("formReady");
      } else {
        this.fireEvent("error");
      }
      // TODO Load the Form (if form-direction != out)
    },
    /*
     *****************************************************************************
     FORM BUILD STAGES
     *****************************************************************************
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
        if (this.getFormModel().isModified()) { // For was modified : Validate and Save if Complete
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
    __createFieldWidgets: function() {

      var model = this.getFormModel();
      if (qx.core.Environment.get("qx.debug")) {
        this.assertNotNull(model, "Invalid argument 'model'.");
      }

      var widgetFields = model.getFormFields();
      if (qx.core.Environment.get("qx.debug")) {
        this.assertNotNull(widgetFields, "Invalid argument 'widgetFields'.");
      }

      var key, fieldMeta, widget;
      for (var i = 0; i < widgetFields.length; ++i) {
        // Get the Field Name
        key = widgetFields[i];
        fieldMeta = model.getFieldMeta(key);
        if (fieldMeta) {
          // Create the Widget
          widget = this.__createWidget(key, fieldMeta);
          if (widget == null) { // Failed to Create Widget
            return false;
          }

          //Save the Widget Associated with the Field
          this.__fieldWidgets[key] = widget;

        } else { // No definition for the Field
          return false;
        }
      }

      return true;
    },
    /**
     *
     * @return {Boolean}
     * @private
     */
    __buildFormUI: function() {
      var model = this.getFormModel();
      var groups = model.getGroupCount();

      // Standard Validate Function
      /* TODO Verify if we can create this function as Class member, rather than an anonymous function
       * (i.e. verify what is the 'this' context in which this function is called)
       */
      var functionValidate = function(value, widget) {
        if (widget.getRequired() && !model.isFieldDataValid(widget.__metaField)) {
          widget.setInvalidMessage('Invalid.');
          return false;
        }

        // TODO Allow for custom field validation functions (ex: validate that the password confirmation field is actually == password field)
        // Not Required or Valid
        return true;
      };

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
          if (this.__fieldWidgets.hasOwnProperty(key)) {
            widget = this.__fieldWidgets[key];
            this.add(widget, widget.__metadata.hasOwnProperty('label') ? widget.__metadata.label : key, functionValidate);
          }
        }
      }

      if (groups > 0) { // Add Submit Button
        this.addButton(this.__buttonSubmit);

        // Get Validation Manager
        var manager = this.getValidationManager();

        manager.addListener("complete", function(e) {
          if (this.getValidationManager().isValid()) { // Form Passed Validation
            this.getFormModel().save();
          }
        }, this);
        return true;
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
    __createWidget: function(field, definition, direction) {
      if (qx.core.Environment.get("qx.debug")) {
        this.assertNotNull(field, "Invalid argument 'field'.");
      }

      var widget = this.getWidgetFactory().createFieldWidget(definition);
      if (widget) {
        widget.__metaField = field;
        widget.__metadata = definition;
        this.__applyPlaceholder(definition, widget);
        this.__applyLength(definition, widget);
        this.__applyIsRequired(field, widget);
        if (this.__isReadOnly(definition, direction)) {
          widget.setEnabled(false);
        } else {
          widget.addListener("changeValue", function(e) {
            var widget = e.getTarget();
            if (!widget.__flagResetValue) {
              var value = e.getData();
              var model = this.getFormModel();

              if (!this.__disableDataStoreUpdate) {
                // Set the Value for the Field (Capturing the return value, in the case it has been changed)
                var newValue = model.setFieldValue(widget.__metaField, value);
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
      if (field.hasOwnProperty('default') && (field['default'] != null)) {
        widget.setPlaceholder(field['default']);
      }
    },
    __applyLength: function(field, widget) {
      if (field.hasOwnProperty('max-length') && (field['max-length'] > 0)) {
        widget.setMaxLength(field['max-length']);
      }
    },
    __applyIsRequired: function(name, widget) {
      var model = this.getFormModel();
      widget.setRequired(model && model.isFieldRequired(name));
    },
    __isReadOnly: function(field, form_direction) {
      var readOnly = (form_direction != null) && (form_direction === 'in');
      if (!readOnly && field.hasOwnProperty('data-direction')) {
        readOnly = field['data-direction'] === 'in';
      }

      return readOnly;
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
        var model = this.getFormModel();
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
    }
  }
});


/* TODO Solve the following problem
 * If we create the user in a 2 step process (create and then update, to modify the non-essential properties
 * there is always the possibility that the user might be created, but the update fails, for some reason.
 * HOW TO HANDLE THIS SCENARIO?
 * Possible Options :
 * Scenario 1:
 * i) Leave the user created.
 * ii) issue an error on the update, and let the user correct the modifications, and re-submit the update
 * Scenario 2:
 * i) Delete the newly created user.
 */