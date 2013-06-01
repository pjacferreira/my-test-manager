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
qx.Class.define("tc.metaform.DefaultMetadataModel", {
  extend: qx.core.Object,
  implement: [
    tc.meta.interfaces.IFieldsMetadataModel,
    tc.meta.interfaces.IServicesMetadataModel,
    tc.metaform.interfaces.IFormMetadataModel
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when the model is initialized and ready to be used (this allows model load to be
     * asynchronous):
     */
    "model-ready": "qx.event.type.Event",
    /**
     * Fired if the model failed to initialize correctly.
     */
    "error": "qx.event.type.Event"
  },
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** The Form's Definition (if not provided, the form will be loaded from the Meta Service) */
    source: {
      check: "Object",
      init: null,
      nullable: true,
      event: "reloadModel"
    },
    /** The Form's ID. */
    formName: {
      check: "String",
      init: null,
      nullable: true,
      apply: "_resetForm",
      event: "reloadModel"
    },
    /** The Form type. */
    formType: {
      check: "String",
      init: null,
      nullable: true,
      apply: "_resetForm",
      event: "reloadModel"
    }
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   * @param formName
   * @param formType
   * @param sourceMetadata
   */
  construct: function(formName, formType, sourceMetadata) {
    this.base(arguments);

    this.setFormName(tc.util.String.nullOnEmpty(formName, true));
    this.setFormType(tc.util.String.nullOnEmpty(formType, true));

    if (qx.lang.Type.isObject(sourceMetadata)) {
      this.setSource(sourceMetadata);
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
   MEMBERS
   *****************************************************************************
   */
  members: {
    __ready: false,
    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _resetForm: function(value, old) {

      if (this.__ready &&
              qx.Class.implemenetsInterface(this.getSource(), tc.metaform.interfaces.IFormMetadataSource)) {
        // Re-Initialize Required
        this.__ready = false;
      }

      // TODO Verify that formName is not an Empty String
      // TODO If the formType is NULL or an Empty String than use 'default' as the value
    },
    /**
     * 
     */
    init: function() {

      // TODO Maybe add a flag to force re-initialization
      if (!this.__ready) { // Skip if already Initialized
        return this.__stage1(this.getSource());
      } else { // Already Successfully Loaded
        this.fireEvent('model-ready');
      }

      return this.__ready;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS [tc.metaform.interfaces.IFormMetadataModel]
     *****************************************************************************
     */
    // see interface for documentation
    getFormMeta: function() {
      if (this.__ready) {
        return this.getSource().form;
      }

      return null;
    },
    // see interface for documentation
    getFormTitle: function() {
      return this.__ready ? tc.util.Object.valueFromPath(this.getSource(), ['form', 'title'], {'default': 'Form'}) : null;
    },
    // see interface for documentation
    getFormFields: function() {
      return this.__ready ? this.getSource().form.__allFields : null;
    },
    // see interface for documentation
    getGroupCount: function() {
      return this.__ready ? this.getSource().form.fields.length : 0;
    },
    // see interface for documentation
    getGroupLabel: function(index) {

      if (this.__ready) {
        var groups = this.getSource().form.fields;
        var group = (index >= 0) && (index < groups.length) ? groups[index] : null;
        return group !== null && qx.lang.Type.isObject(group) ? tc.util.Object.getFirstProperty(group) : null;
      }

      return null;
    },
    // see interface for documentation
    getGroupFields: function(index) {
      if (this.__ready) {
        var groups = this.getSource().form.fields;
        var group = (index >= 0) && (index < groups.length) ? groups[index] : null;
        if (group !== null) {
          if (qx.lang.Type.isObject(group)) {
            return group[tc.util.Object.getFirstProperty(group)];
          } else { // If Fields are Normalized (as they should be) 'group' is an Array
            return group;
          }
        }
      }

      return null;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS [tc.meta.interfaces.IFieldsMetadataModel]
     *****************************************************************************
     */
    // see interface for documentation
    getFieldsMeta: function() {
      if (this.__ready) {
        return this.getSource().fields;
      }

      return null;
    },
    // see interface for documentation
    getFieldMeta: function(name) {
      name = tc.util.String.nullOnEmpty(name, true);
      if (this.__ready && (name != null)) {
        var fields = this.getSource().fields;
        return fields.hasOwnProperty(name) ? fields[name] : null;
      }

      return null;
    },
    /*
     *****************************************************************************
     INTERFACE METHODS [tc.meta.interfaces.IServicesMetadataModel]
     *****************************************************************************
     */
    // see interface for documentation
    getServicesMeta: function() {
      if (this.__ready) {
        var source = this.getSource();
        var services = source.form.services;
        var list = {};
        var count = 0;
        for (var service in services) {
          if (services.hasOwnProperty(service) &&
                  source.services.hasOwnProperty(services[service])) {
            list[service] = source.services[services[service]]
            ++count;
          }
        }

        return count > 0 ? list : null;
      }

      return null;
    },
    // see interface for documentation
    getServiceMeta: function(name) {
      name = tc.util.String.nullOnEmpty(name, true);
      if (this.__ready && (name != null)) {
        var source = this.getSource();
        var services = source.form.services;
        if (services.hasOwnProperty(name) &&
                source.services.hasOwnProperty(services[name])) {
          return source.services[services[name]];
        }
      }

      return null;
    },
    /*
     *****************************************************************************
     INTERNAL METHODS
     *****************************************************************************
     */

    __stage1: function(sourceMetadata) {
      // Check if the Form is Already Loaded
      if ((sourceMetadata != null) &&
              qx.lang.Type.isObject(sourceMetadata.form) &&
              qx.lang.Type.isObject(sourceMetadata.fields)) { // Static Form Definition. Continue to Stage 2
        // AFTER STAGE 1 : An Event is Fired to Signal an Error
        return this.__stage2(sourceMetadata);
      }

      // No: Load the Form from the Meta Service
      var name = this.getFormName();
      var type = this.getFormType();
      tc.services.Meta.form(name, type,
              function(metadata) {
                // cache the information in the sourceMetadata
                if (qx.lang.Type.isObject(metadata)) {
                  return this.__stage2({'form': metadata});
                }
                this.fireEvent('error');
              },
              function(error) {
                this.fireEvent('error');

              }, this);
    },
    __stage2: function(sourceMetadata) {
      // Normalize Form Definition
      var form = sourceMetadata.form;

      if (form.hasOwnProperty('fields')) { // Normalize Fields Property

        form.fields = this.__CSVToArray(form.fields);

        if (qx.lang.Type.isString(form.fields)) {
          // Convert the String to an Array of Fields
          form.__allFields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(form.fields)));
          form.fields = form.__allFields === null ? null : [form.__allFields];
        } else if (qx.lang.Type.isArray(form.fields)) {
          form.__allFields = [];

          // Cycle through all the entries and normalize them
          var object = null, normalized = [];
          var entry = null, group = null, fields = null;
          for (var i = 0; i < form.fields.length; i++) {
            entry = form.fields[i];
            group = null;
            if (qx.lang.Type.isObject(entry)) {
              group = tc.util.Object.getFirstProperty(entry);
              if (group === null) { // Skip : Invalid Object
                continue;
              }

              // Extract the Entry for Further Parsing (if necessary)
              entry = entry[group];
            }

            // Handle String and Array Entries
            if (qx.lang.Type.isString(entry)) {
              fields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(entry)));
            } else if (qx.lang.Type.isArray(entry)) {
              fields = tc.util.Array.clean(tc.util.Array.trim(entry));
            } else {  // Skip : Unexpected Type
              continue;
            }

            if (fields === null) { // Skip : NULL Groups
              continue;
            }

            // Add to Normalized Entry
            if (group !== null) { // We have a Label so save an Object
              object = {};
              object[group] = fields;
              normalized.push(object);
            } else { // We have no Label, so just store the fields array
              normalized.push(fields);
            }

            // Merge into All Fields
            form.__allFields = tc.util.Array.union(form.__allFields, fields.slice(0).sort());
          }

          if (form.__allFields.length === 0) {
            form.__allFields = null;
            form.fields = null;
          } else {
            form.fields = normalized;
          }
        }
      }

      if (form.hasOwnProperty('services')) { // Normalize Fields Property

        if (qx.lang.Type.isObject(form.services)) {
          form.__allServices = [];
          for (var service in form.services) {
            if (form.services.hasOwnProperty(service)) {
              if (qx.lang.Type.isString(form.services[service])) {
                form.__allServices.push(form.services[service]);
              }
            }
          }
        } else {
          form.__allServices = null;
          form.services = null;
        }
      }

      if (form.fields != null) {
        return this.__stage3(sourceMetadata);
      }

      // Missing or Invalid Fields Property
      this.fireEvent('error');
      return true;
    },
    __stage3: function(sourceMetadata) {
      if (sourceMetadata.hasOwnProperty('fields') &&
              qx.lang.Type.isObject(sourceMetadata['fields'])) {
        // Fields Definition already set, continue
        return this.__stage4(sourceMetadata);
      }

      // Load Fields Definition
      tc.services.Meta.fields(sourceMetadata.form.__allFields,
              function(fields) {
                if (qx.lang.Type.isObject(fields)) {
                  sourceMetadata['fields'] = fields;
                  return this.__stage4(sourceMetadata);
                }
                this.fireEvent('error');
              },
              function(error) {
                this.fireEvent('error');
              }, this);

    },
    __stage4: function(sourceMetadata) {
      if (sourceMetadata.hasOwnProperty('services') &&
              qx.lang.Type.isObject(sourceMetadata['services'])) {
        // Services Definition already set, continue
        return this.__stage5(sourceMetadata);
      }

      // Load Fields Definition
      tc.services.Meta.services(sourceMetadata.form.__allServices,
              function(services) {
                if (qx.lang.Type.isObject(services)) {
                  sourceMetadata['services'] = services;
                  return this.__stage5(sourceMetadata);
                }
                this.fireEvent('error');
              },
              function(error) {
                this.fireEvent('error');
              }, this);
    },
    __stage5: function(sourceMetadata) {
      // Validate that we have the Required Field Definitions and fire events
      if (this.__haveRequiredFields(sourceMetadata.form.__allFields, sourceMetadata.fields)) {
        this.__ready = true;

        // Save the Metadat so that we don't have to reload it
        this.setSource(sourceMetadata);
        this.fireEvent('model-ready');
      } else {
        this.fireEvent('error');
      }
      return true;
    },
    __CSVToArray: function(value) {
      if (qx.lang.Type.isString(value)) {
        return value.split(',');
      }

      return value;
    },
    /**
     *
     * @param form_fields
     * @param fields
     * @return {Boolean}
     * @private
     */
    __haveRequiredFields: function(form_fields, fields) {

      for (var i = 0; i < form_fields.length; ++i) {
        if (!fields.hasOwnProperty(form_fields[i])) {
          return false;
        }
      }

      return true;
    }
  }
});
