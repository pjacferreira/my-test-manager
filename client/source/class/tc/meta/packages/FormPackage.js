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
 * Form Meta Package Class
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
      // Prepare CallBack
      callback = this._prepareCallback(callback);

      if (!this.isReady()) {
        if (this.__sForm !== null) {
          // Load Form Definition
          tc.services.Meta.form(this.__sForm,
                  function(form) {
                    this.__oMetaData = this.__postProcess(form);
                    if ((this.__oMetaData !== null) && (this.__oFieldsPackage !== null)) {
                      this._bReady = true;
                    }

                    this._callbackPackageReady(callback, this._bReady, "Invalid Form Definition");
                  },
                  function(error) {
                    this._callbackPackageReady(callback, false, error);
                  }, this);

        } else {
          this._callbackPackageReady(callback, false, "Missing Form ID to build Package.");
        }
      } else {
        this._callbackPackageReady(callback, true);
      }

      return this.isReady();
    }, // FUNCTION: initialize
    /*
     *****************************************************************************
     INTERFACE METHODS : IFormMetaPackage
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
            // CASE 1: fields = field_id or {CSV STRING} field_id, ..., field_id
            form.fields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(form.fields)));
          }

          if (qx.lang.Type.isArray(form.fields)) { // Ungrouped Fields
            // CASE 2: fields = [field_id, ..., field_id]
            // Save All Fields Sorted
            arFields = form.fields.slice(0).sort();
            // Convert to GROUP with No Label
            form.fields = [form.fields];
          } else if (qx.lang.Type.isObject(form.fields)) { // Grouped Fields
            // CASE 3: fields = [ ('label' -> string | array) ||  string, ... ]
            arFields = [];

            var entry, fields;
            var normalized = [];
            for (var group in form.fields) {
              if (form.fields.hasOwnProperty(group)) {
                entry = form.fields[group];
                if (qx.lang.Type.isString(entry)) {
                  fields = tc.util.Array.clean(tc.util.Array.trim(this.__CSVToArray(entry)));
                } else if(qx.lang.Type.isArray(entry)) {
                  fields = tc.util.Array.clean(tc.util.Array.trim(entry));
                } else { // Skip Invalid Types
                  continue;
                }


                if (/^\d+$/.test(group)) { // If Group Name is an Integer, then we have, an Un-named group
                  normalized.push(fields);
                } else { // Named Group
                  entry = {};
                  entry[group] = fields;
                  normalized.push(entry);
                }
                
                // Merge into All Fields
                arFields = tc.util.Array.union(arFields, fields.slice(0).sort());
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
