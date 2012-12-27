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
  implement: tc.metaform.interfaces.IFormMetadataModel,

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
    "modelReady": "qx.event.type.Event",

    /**
     * Fired if the model failed to initialize correctly.
     */
    "modelInvalid": "qx.event.type.Event"
  },

  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** The Form's ID. */
    source: {
      check: "Object",
      nullable: false,
      apply: "_resetSource",
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
  construct: function (formName, formType, sourceMetadata) {
    this.base(arguments);

    this.setFormName(tc.util.String.nullOnEmpty(formName, true));
    this.setFormType(tc.util.String.nullOnEmpty(formType, true));
    this.setSource(sourceMetadata);
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

    /*
     *****************************************************************************
     PROPERTY APPLY METHODS
     *****************************************************************************
     */
    // property modifier
    _resetSource: function (value, old) {
      // Re-Initialize Required
      this.__ready = false;
    },

    // property modifier
    _resetForm: function (value, old) {

      if (this.__ready &&
        qx.Class.implemen1tsInterface(this.getSource(), tc.metaform.interfaces.IFormMetadataSource)) {
        // Re-Initialize Required
        this.__ready = false;
      }

      // TODO Verify that formName is not an Empty String
      // TODO If the formType is NULL or an Empty String than use 'default' as the value
    },

    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    // see interface for documentation
    init: function () {

      // TODO Maybe add a flag to force re-initialization
      if (!this.__ready) { // Skip if already Initialized
        var sourceMetadata = this.getSource();
        if (sourceMetadata) { // Have a Metadata Source
          return this.__stage1(sourceMetadata);
        }
      } else { // Already Successfully Loaded
        this.fireEvent("modelReady");
      }

      return this.__ready;
    },

    // see interface for documentation
    getFormMeta: function () {
      if (this.__ready) {
        return this.getSource().form;
      }

      return null;
    },

    // see interface for documentation
    getFieldsMeta: function () {
      if (this.__ready) {
        return this.getSource().fields;
      }

      return null;
    },

    // see interface for documentation
    getFieldMeta: function (name) {

      name = tc.util.String.nullOnEmpty(name, true);
      if (this.__ready && (name != null)) {
        var fields = this.getSource().fields;
        return fields.hasOwnProperty(name) ? fields[name] : null;
      }

      return null;
    },

    // see interface for documentation
    getFormTitle: function () {
      return this.__ready ? tc.util.Object.valueFromPath(this.getSource(), ['form', 'title'], {'default': 'Form'}) : null;
    },

    // see interface for documentation
    getFormFields: function () {
      return this.__ready ? this.getSource().form.__allFields : null;
    },

    // see interface for documentation
    getGroupCount: function () {
      return this.__ready ? this.getSource().form.fields.length : 0;
    },

    // see interface for documentation
    getGroupLabel: function (index) {

      if (this.__ready) {
        var groups = this.getSource().form.fields;
        var group = (index >= 0) && (index < groups.length) ? groups[index] : null;
        return group !== null && qx.lang.Type.isObject(group) ? tc.util.Object.getFirstProperty(group) : null;
      }

      return null;
    },

    // see interface for documentation
    getGroupFields: function (index) {
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
     INTERNAL METHODS
     *****************************************************************************
     */

    __stage1: function (sourceMetadata) {
      // Load Form Definition if Required

      if (qx.Class.implementsInterface(sourceMetadata, tc.metaform.interfaces.IFormMetadataSource)) {

        var name = this.getFormName();
        var type = this.getFormType();
        if (name != null) {
          var entry = name + ':' + (type === null ? 'default' : type);

          // Implements MetadataSource Interface (use that to load)
          return sourceMetadata.getFormMeta(entry, function (error_code, error_message, type, data) {
            if (error_code || (data == null) || !data.hasOwnProperty(entry)) {
              this.fireEvent("modelInvalid");
              return false;
            }

            // cache the information in the sourceMetadata
            sourceMetadata.form = data[entry];

            return this.__stage2(sourceMetadata);
          }, this);
        }

        // FALSE: Missing Required Parameters formName, formType
      } else if (qx.lang.Type.isObject(sourceMetadata.form) &&
        qx.lang.Type.isObject(sourceMetadata.fields)) { // Static Form Definition. Continue to Stage 2
        // AFTER STAGE 1 : An Event is Fired to Signal an Error
        return this.__stage2(sourceMetadata);
      }

      // FALSE: Not a Valid Static Form Definition, or Missing Required Parameters for Dynamic Load
      return false;
    },

    __stage2: function (sourceMetadata) {
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

        if (form.fields != null) {
          return this.__stage3(sourceMetadata);
        }
      }

      // Missing or Invalid Fields Property
      this.fireEvent("modelInvalid");
      return true;
    },

    __stage3: function (sourceMetadata) {
      // Load Fields if Required
      if (qx.Class.implementsInterface(sourceMetadata, tc.metaform.interfaces.IFormMetadataSource)) {
        if (!sourceMetadata.getFieldsMeta(sourceMetadata.form.__allFields, function (error_code, error_message, type, data) {
          if (error_code) {
            this.fireEvent("modelInvalid");
            return false;
          }

          // cache the information in the sourceMetadata
          sourceMetadata.fields = data;

          return this.__stage4(sourceMetadata);
        }, this)) {
          this.fireEvent("modelInvalid");
          return false;
        }
      } else {
        return this.__stage4(sourceMetadata);
      }

      return true;
    },

    __stage4: function (sourceMetadata) {
      // Validate that we have the Required Field Definitions and fire events
      if (this.__haveRequiredFields(sourceMetadata.form.__allFields, sourceMetadata.fields)) {
        this.__ready = true;
        this.fireEvent("modelReady");
      } else {
        this.fireEvent("modelInvalid");
      }
      return true;
    },

    __CSVToArray: function (value) {
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
    __haveRequiredFields: function (form_fields, fields) {

      for (var i = 0; i < form_fields.length; ++i) {
        if (!fields.hasOwnProperty(form_fields[i])) {
          return false;
        }
      }

      return true;
    }
  }
});
