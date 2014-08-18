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

qx.Class.define("meta.repositories.ServiceRepository", {
  extend: meta.repositories.AbstractRepository,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Service Based Metadata Repository
   * 
   * @param service {Object} Specific Service Dispatcher to Handle Calls to Meta Services   
   */
  construct: function(service) {
  },
  /**
   *
   */
  destruct: function() {
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
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
      var message;
      id = utility.String.v_nullOnEmpty(id, true);
      if (id !== null) {
        try {
          var dispatcher = this.getDI().get('metaservices');
          var request = dispatcher.buildRequest(
            dispatcher.buildURL(null, 'field', id), null,
            function(response) {
              if (response.hasOwnProperty('return') && response['return'].hasOwnProperty(id)) {
                return this._callOK(ok, this._createField(id, response['return'][id]), context);
              }

              this._callNOK(nok, "No field with id[" + id + "] exists.", context);
            },
            function(error) {
              this._callRequestNOK(nok, error.error, context);
            },
            this);

          dispatcher.queueRequests(request);
          return true;
        } catch (e) { // Exception Occured Build Request
          message = e.toString();
        }
      } else {
        message = "No valid field id provided.";
      }
      // ELSE: Abort
      this._callNOK(nok, message, context);
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
      var message;
      // Is 'ids' an Array?
      if (qx.lang.Type.isArray(ids)) { // YES: Cleanup it's elements
        ids = utility.Array.clean(
          utility.Array.map(ids, function(value) {
            return utility.String.v_nullOnEmpty(value, true);
          }
          ));
      } else { // NO: Abort
        ids = null;
      }

      // Do we have 'ids'?
      if (ids !== null) { // YES: Create a List of Fields
        var list = ids.join(',');

        try {
          var dispatcher = this.getDI().get('metaservices');
          var request = dispatcher.buildRequest(
            dispatcher.buildURL(null, 'fields'),
            {'list': list},
          function(response) {
            if (response.hasOwnProperty('return')) {
              var fields = response['return'];
              var entities = new utility.Map();
              var id = null;
              for (var i = 0; i < ids.length; ++i) {
                id = ids[i];
                if (fields.hasOwnProperty(id)) {
                  entities.add(id, this._createField(id, fields[id]));
                } else {
                  this.warn("No field with id[" + id + "] exists.");
                }
              }

              if (entities.count()) {
                this._callOK(ok, entities.map(), context);
                return;
              }
            }

            this._callNOK(nok, "No valid fields found.", context);
          },
            function(error) {
              this._callRequestNOK(nok, error.error, context);
            },
            this);

          dispatcher.queueRequests(request);
          return true;
        } catch (e) { // Exception Occured Build Request
          message = e.toString();
        }
      } else {
        message = "No valid field ids provided.";
      }
      // ELSE: Abort
      this._callNOK(nok, message, context);
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
      var message;
      id = utility.String.v_nullOnEmpty(id, true);
      if (id !== null) {
        try {
          var dispatcher = this.getDI().get('metaservices');
          var request = dispatcher.buildRequest(
            dispatcher.buildURL(null, 'service', id), null,
            function(response) {
              if (response.hasOwnProperty('return') && response['return'].hasOwnProperty(id)) {
                return this._callOK(ok, this._createService(id, response['return'][id]), context);
              }

              this._callNOK(nok, "No service with id[" + id + "] exists.", context);
            },
            function(error) {
              this._callRequestNOK(nok, error.error, context);
            },
            this);

          dispatcher.queueRequests(request);
          return true;
        } catch (e) { // Exception Occured Build Request
          message = e.toString();
        }
      } else {
        message = "No valid service id provided.";
      }
      // ELSE: Abort
      this._callNOK(nok, message, context);
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
      var message;
      // Is 'ids' an Array?
      if (qx.lang.Type.isArray(ids)) { // YES: Cleanup it's elements
        ids = utility.Array.clean(
          utility.Array.map(ids, function(value) {
            return utility.String.v_nullOnEmpty(value, true);
          }
          ));
      } else { // NO: Abort
        ids = null;
      }

      // Do we have 'ids'?
      if (ids !== null) { // YES: Create a List of Fields
        try {
          var list = ids.join(',');

          var dispatcher = this.getDI().get('metaservices');
          var request = dispatcher.buildRequest(
            dispatcher.buildURL(null, 'services'),
            {'list': list},
          function(response) {
            if (response.hasOwnProperty('return')) {
              var services = response['return'];
              var entities = new utility.Map();
              var id;
              for (var i = 0; i < ids.length; ++i) {
                id = ids[i];
                if (services.hasOwnProperty(id)) {
                  entities.add(id, this._createService(id, services[id]));
                } else {
                  this.warn("No service with id[" + id + "] exists.");
                }
              }

              if (entities.count()) {
                this._callOK(ok, entities.map(), context);
                return;
              }
            }
            this._callNOK(nok, "No valid services found.", context);
          },
            function(error) {
              this._callRequestNOK(nok, error.error, context);
            },
            this);

          dispatcher.queueRequests(request);
          return true;
        } catch (e) { // Exception Occured Build Request
          message = e.toString();
        }
      } else {
        message = "No valid service ids provided.";
      }
      // ELSE: Abort
      this._callNOK(nok, message, context);
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
      var message;
      id = utility.String.v_nullOnEmpty(id, true);
      if (id !== null) {
        try {
          var dispatcher = this.getDI().get('metaservices');
          var request = dispatcher.buildRequest(
            dispatcher.buildURL(null, 'form', id), null,
            function(response) {
              if (response.hasOwnProperty('return') && response['return'].hasOwnProperty(id)) {
                var form = this._createForm(id, response['return'][id]);
                if (form !== null) {
                  return this._callOK(ok, form, context);
                }
              }

              this._callNOK(nok, "No form with id[" + id + "] exists.", context);
            },
            function(error) {
              this._callRequestNOK(nok, error.error, context);
            },
            this);

          dispatcher.queueRequests(request);
          return true;
        } catch (e) { // Exception Occured Build Request
          message = e.toString();
        }
      } else {
        message = "No valid form id provided.";
      }
      // ELSE: Abort
      this._callNOK(nok, message, context);
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
      var message;
      // Is 'ids' an Array?
      if (qx.lang.Type.isArray(ids)) { // YES: Cleanup it's elements
        ids = utility.Array.clean(
          utility.Array.map(ids, function(value) {
            return utility.String.v_nullOnEmpty(value, true);
          }
          ));
      } else { // NO: Abort
        ids = null;
      }

      // Do we have 'ids'?
      if (ids !== null) { // YES: Create a List of Fields
        try {
          var list = ids.join(',');
          var dispatcher = this.getDI().get('metaservices');
          var request = dispatcher.buildRequest(
            dispatcher.buildURL(null, 'forms'),
            {'list': list},
          function(response) {
            if (response.hasOwnProperty('return')) {
              var id, forms = response['return'], entities = new utility.Map();
              for (var i = 0; i < ids.length; ++i) {
                id = ids[i];
                if (forms.hasOwnProperty(id)) {
                  entities.add(id, this._createForm(id, forms[id]));
                } else {
                  entities.add(id, "No form with id[" + id + "] exists.");
                }
              }

              if (entities.count()) {
                return this._callOK(ok, entities.map(), context);
              }
              this._callNOK(nok, "No valid forms found.", context);
            }
          },
            function(error) {
              this._callRequestNOK(nok, error.error, context);
            },
            this);

          dispatcher.queueRequests(request);
          return true;
        } catch (e) { // Exception Occured Build Request
          message = e.toString();
        }
      } else {
        message = "No valid form ids provided.";
      }
      // ELSE: Abort
      this._callNOK(nok, message, context);
      return false;
    },
    /*
     *****************************************************************************
     PRIVATE METHODS: Manage Processing State for Tab Forms Creation
     *****************************************************************************
     */
    _callRequestNOK: function(nok, error, context) {
      if (nok != null) {
        nok.call(context == null ? this : context, error.message, error.code);
      }
    }
  } // SECTION: MEMBERS
});
