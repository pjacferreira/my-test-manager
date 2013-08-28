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
qx.Class.define("tc.meta.packages.FormPackage", {
  extend: tc.meta.packages.BasePackage,
  implement: tc.meta.packages.IFormPackage,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for an Form MetaPackage
   * 
   * @param form {String} Form ID
   */
  construct: function(form) {
    this.base(arguments);

    this.__sForm = tc.util.String.nullOnEmpty(form);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertString(form, "[form] Should be a Non Empty String!");
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear Variables
    this.__sForm = null;
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
    __sForm: null,
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
      // Clear Current Meta Data
      this.__oMetaData = null;

      if (this.__sForm !== null) {
        // Load Fields Definition
        tc.services.Meta.form(this.__sForm,
                function(form) {
                  this.__oMetaData = this.__postProcess(form);
                  if ((this.__oMetaData !== null) && (this.__oFieldsPackage !== null)) {
                    this._bReady = true;

                    if (callback !== null) {
                      if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
                        callback['ok'].call(callback['context'], this.__sForm);
                      }
                    } else {
                      this.fireDataEvent('ready', this.__sForm);
                    }

                    // Done
                    return true;
                  }

                  this.fireDataEvent('error', null);
                  return false;
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

      // No Form to Load
      return false;
    }, // FUNCTION: initialize
    /*
     *****************************************************************************
     INTERFACE METHODS : IFieldsMetaPackage
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
     * Get Form Container (IMetaForm Instance)
     *
     * @return {tc.meta.entities.IMetaForm} Return Form Metadata Entity
     * @throw If Package not Ready
     */
    getForm: function() {
      this._throwIsPackageReady();

      return new tc.meta.entities.FormEntity(this.__sForm, this.__oMetaData);
    },
    /*
     *****************************************************************************
     PRIVATE METHODS
     *****************************************************************************
     */
    /**
     * 
     */
    __postProcess: function(form) {
      if (qx.lang.Type.isObject(form) &&
              form.hasOwnProperty('title') &&
              form.hasOwnProperty('fields')) {

        // Normalize Form Definition
        var arFields = null;
        if (form.hasOwnProperty('fields')) { // Normalize Fields Property
          if (qx.lang.Type.isString(form.fields)) {
            // CASE 1: fields = field_id or field_id, ..., field_id
            form.fields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(form.fields)));
            if (form.fields) {
              // Save All Fields Sorted
              arFields = form.fields.slice(0).sort();
              // Convert to GROUP with No Label
              form.fields = [form.fields];
            }
          } else if (qx.lang.Type.isArray(form.fields)) {
            // CASE 2: fields = [ ('label' -> string | array) ||  string, ... ]
            arFields = [];

            var entry, fields, label;
            var normalized = [];
            for (var i = 0; i < form.fields.length; ++i) {
              entry = form.fields[i];
              if (qx.lang.Type.isString(entry)) {
                fields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(entry)));
                if (fields !== null) {
                  // Add Un-Labeled Group
                  normalized.push(fields);
                  // Merge into All Fields
                  arFields = fields.slice(0).sort();
                }
              } else if (qx.lang.Type.isObject(entry)) {
                label = tc.util.Object.getFirstProperty(entry);
                fields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(entry[label])));
                if (fields !== null) {
                  // Create a New Entry
                  entry = {};
                  entry[label] = fields;
                  // Add Labeled Group
                  normalized.push(entry);
                  // Merge into All Fields
                  arFields = tc.util.Array.union(arFields, fields.slice(0).sort());
                }
              }
            }

            if (arFields.length === 0) { // No Fields in Form
              form.fields = null;
            } else {
              form.fields = normalized;
              this.__oFieldsPackage = new tc.meta.packages.FieldsPackage(arFields);
            }
          }
        }

        if (form.hasOwnProperty('services')) { // Normalize Fields Property
          if (qx.lang.Type.isObject(form.services)) {
            var arServices = [];
            for (var service in form.services) {
              if (form.services.hasOwnProperty(service)) {
                if (qx.lang.Type.isString(form.services[service])) {
                  arServices.push(form.services[service]);
                }
              }
            }

            if (arServices.length === 0) {
              form.services = null;
            } else {
              this.__oServicesPackage = new tc.meta.packages.ServicesPackage(arServices);
            }
          } else {
            form.services = null;
          }
        }
      } else { // Invalid Form Definition
        form = null;
      }

      return form;
    }, // FUNCTION: __postProcess
    __CSVToArray: function(value) {
      if (qx.lang.Type.isString(value)) {
        return value.split(',');
      }

      return value;
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
    }
  }
});
