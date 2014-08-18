/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

qx.Class.define("meta.repositories.StaticRepository", {
  extend: meta.repositories.AbstractRepository,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Test Metadata Repository
   * 
   * @param metadata {Object} Meta Data for Repository
   */
  construct: function(metadata) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] is Invalid!");
    }

    // Initialize Variables
    this.__metadata = metadata;
  },
  /**
   *
   */
  destruct: function() {
    // Cleanup Variables
    this.__metadata = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __metadata: null,
    /*
     ***************************************************************************
     METHODS (meta.api.repository.IMetaRepository)
     ***************************************************************************
     */
    /**
     * Get Meta Field Entity
     *
     * @param id {String} ID of Field Required 
     * @param ok {Function} Function called on success (function(meta.api.entities.IField) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getField: function(id, ok, nok, context) {
      id = utility.String.v_nullOnEmpty(id, true);
      if ((id !== null) && this.__metadata.hasOwnProperty('fields')) {
        if (this.__metadata.fields.hasOwnProperty(id)) {
          this._callOK(ok, this._createField(id, this.__metadata.fields[id]), context);
        } else {
          this._callNOK(nok, "No field with id[" + id + "] exists.", context);
        }
        return true;
      }

      return false;
    },
    /**
     * Return Requested Field Entities
     *
     * @param ids {String[]} IDs of Fields Required
     * @param ok {Function} Function called on success (function(Map) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getFields: function(ids, ok, nok, context) {
      if (qx.lang.Type.isArray(ids) && this.__metadata.hasOwnProperty('fields')) {
        var entities = new utility.Map();
        var id = null;
        for (var i = 0; i < ids.length; ++i) {
          id = utility.String.v_nullOnEmpty(ids[i]);
          if (id !== null) {
            if (this.__metadata.fields.hasOwnProperty(id)) {
              entities.add(id, this._createField(id, this.__metadata.fields[id]));
            } else {
              entities.add(id, "No field with id[" + id + "] exists.");
            }
          }
        }

        if (entities.count()) {
          this._callOK(ok, entities.map(), context);
          return true;
        } else {
          this._callNOK(nok, "No valid fields found.", context);
        }
      }


      return false;
    },
    /**
     * Get Meta Service Entity
     *
     * @param id {String} ID of Service Required 
     * @param ok {Function} Function called on success (function(meta.api.entities.IService) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getService: function(id, ok, nok, context) {
      id = utility.String.v_nullOnEmpty(id, true);
      if ((id !== null) && this.__metadata.hasOwnProperty('services')) {
        if (this.__metadata.services.hasOwnProperty(id)) {
          this._callOK(ok, this._createService(id, this.__metadata.services[id]), context);
        } else {
          this._callNOK(nok, "No service with id[" + id + "] exists.", context);
        }
        return true;
      }

      return false;
    },
    /**
     * Return Requested Services Entities
     *
     * @param ids {String[]} IDs of Services Required
     * @param ok {Function} Function called on success (function(utility.Map) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getServices: function(ids, ok, nok, context) {
      if (qx.lang.Type.isArray(ids) && this.__metadata.hasOwnProperty('services')) {
        var entities = new utility.Map();
        var id = null;
        for (var i = 0; i < ids.length; ++i) {
          id = utility.String.v_nullOnEmpty(ids[i]);
          if (id !== null) {
            if (this.__metadata.services.hasOwnProperty(id)) {
              entities.add(id, this._createService(id, this.__metadata.services[id]));
            } else {
              entities.add(id, "No service with id[" + id + "] exists.");
            }
          }
        }

        if (entities.count()) {
          this._callOK(ok, entities.map(), context);
          return true;
        } else {
          this._callNOK(nok, "No valid services found.", context);
        }
      }

      return false;
    },
    /**
     * Get Meta Form Entity
     *
     * @param id {String} ID of Form Required 
     * @param ok {Function} Function called on success (function(meta.api.entities.IForm) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getForm: function(id, ok, nok, context) {
      id = utility.String.v_nullOnEmpty(id, true);
      if (this.__metadata.hasOwnProperty('forms')) {
        if (this.__metadata.forms.hasOwnProperty(id)) {
          var form = this._createForm(id, this.__metadata.forms[id]);
          if (form !== null) {
            this._callOK(ok, form, context);
            return true;
          }
        }

        this._callNOK(nok, "No form with id[" + id + "] exists.", context);
        return true;
      }

      return false;
    },
    /**
     * Return Requested Form Entities
     *
     * @param ids {String[]} IDs of Services Required
     * @param ok {Function} Function called on success (function(utility.Map) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getForms: function(ids, ok, nok, context) {
      if (qx.lang.Type.isArray(ids) && this.__metadata.hasOwnProperty('forms')) {
        var entities = new utility.Map();
        var id = null;
        for (var i = 0; i < ids.length; ++i) {
          id = utility.String.v_nullOnEmpty(ids[i]);
          if (id !== null) {
            if (this.__metadata.forms.hasOwnProperty(id)) {
              entities.add(id, this._createForm(id, this.__metadata.forms[id]));
            } else {
              entities.add(id, "No form with id[" + id + "] exists.");
            }
          }
        }

        if (entities.count()) {
          this._callOK(ok, entities.map(), context);
          return true;
        } else {
          this._callNOK(nok, "No valid forms found.", context);
        }
      }

      return false;
    }
  } // SECTION: MEMBERS
});
