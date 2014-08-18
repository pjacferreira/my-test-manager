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

/**
 * Base Meta Package Class
 */
qx.Class.define("meta.ui.datasource.Connection", {
  extend: qx.core.Object,
  implement: meta.api.ui.datasource.IConnection,
  include: [
    meta.events.mixins.MMetaEventDispatcher
  ],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /*
     * Meta Events - Connection Specific
     */
    "connection-ready": "meta.events.MetaEvent",
    "connection-execution": "meta.events.MetaEvent",
    /*
     * Meta Events - Record Specific
     */
    "record-read": "meta.events.MetaEvent",
    "record-commit": "meta.events.MetaEvent",
    "record-delete": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Connection Constructor
   */
  construct: function() {
    this.base(arguments);

    // Initialize
    this.__mapServices = new utility.Map();
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup
    this.__mapServices = null;
    this.__resultMessage = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __ready: false,
    __pageSize: null,
    __resultError: 0,
    __resultMessage: null,
    __result: null,
    __mapServices: null,
    /*
     ***************************************************************************
     METHODS (meta.api.ui.datasource.api.IConnection)
     ***************************************************************************
     */
    /**
     * Is Connection Ready for Use?
     * 
     * @return {Boolean} 'true' Connection is Ready, 'false' otherwise 
     */
    isReady: function() {
      return this.__ready;
    },
    /**
     * Does the connection support the service function?
     *
     * @param alias {String} One of the possible service functions (create,read,update,delete)
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
      alias = utility.String.v_nullOnEmpty(alias);
      return alias !== null ? this.__mapServices.has(alias) : false;
    },
    /**
     * Register a Service Function with the Connection Object
     *
     * @abstract
     * @param alias {String} One of the possible service functions (create,read,update,delete)
     * @param service {meta.api.entity.IService} Service Definition
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} if the Connection has already been initialized (is ready)
     */
    registerService: function(alias, service) {
      this.__exception(this.isReady(), "Connection has been initialized, changes to service provided is not allowed.");

      // Is the connection in the ready state?
      if (!this.isReady()) { // NO
        this.__mapServices.add(alias, service);
        return true;
      }
      // ELSE: YES

      /* Connection has already been initialized, so, no more changes to the
       * services it provides, is allowed
       */
      return false;
    },
    /**
     * Can the connection produce paged Result Sets?
     * 
     * @abstract
     * @return {Boolean} 'true' YES, 'false' otherwise 
     * @throws {String} if the Connection is Not Ready
     */
    canPageResults: function() {
      this.__exception(!this.isReady(), "Connection is not ready.");
    },
    /**
     * Is Connection Ready for Use?
     * 
     * @return {Integer|null} Last page size set, or null, if page size not set
     */
    getPageSize: function() {
      return this.__pageSize;
    },
    /**
     * Set new page size for Result Sets.
     * 
     * @param size {Integer?null} New page size, or 'null' to clear paging
     * @return {Integer|null} Previous page size set, or null, if page size was not set
     * @throws {String} if the Connection is Not Ready, cannot be paged, or invalid
     *   page size
     */
    setPageSize: function(size) {
      this.__exception(!this.canPageResults(), "Connection cannot produce paged results.");
      this.__exception(size > 1, "Page Size has to be > 1.");

      var oldPageSize = this.__pageSize;
      this.__pageSize = size;
      return oldPageSize;
    },
    /**
     * Initialize Connection.
     * Fires 'ready' event on success, 'not-ready' on failure to initialize.
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     */
    initialize: function(ok, nok, context) {
      var save_this = this;

      // Make sure that we have Valid OK/NOK/CONTEXT          
      context = qx.lang.Type.isObject(context) ? context : this;
      // Do we have a Valid OK Function?
      if (!qx.lang.Type.isFunction(ok)) { // NO: Fire Event Instead
        ok = function(connection) {
          save_this.__ready = true;
          save_this._mx_med_fireEventOK("connection-ready", connection);
        };
      }
      // Do we have a Valid NOK Function?
      if (!qx.lang.Type.isFunction(nok)) { // NO: Fire Event Instead
        nok = function(code, message) {
          save_this.__ready = false;
          save_this._mx_med_fireEventNOK("connection-ready", save_this, message, code);
        };
      }

      // Do we have services set for the connection?
      if (this.__mapServices.count()) { // YES
        // Create a Reverse Map (Maps Service IDs to Service Aliases)
        var reverseMap = new utility.Map();
        var alias, aliases = this.__mapServices.keys();
        for (var i = 0; i < aliases.length; ++i) {
          alias = aliases[i];
          reverseMap.add(this.__mapServices.get(alias), alias);
        }

        var ids = reverseMap.keys();
        meta.Meta.getRepository().getServices(ids,
          function(services) {
            // Cycle through the requested services
            var id, alias;
            for (var i = 0; i < ids.length; ++i) {
              id = ids[i];
              alias = reverseMap.get(id);

              // Did we get an object for the requested service?
              if (services.hasOwnProperty(id)) { // YES
                // Replace the Service ID with the Service Object
                this.__mapServices.add(alias, services[id]);
              } else { // NO
                // Remove the Service Alias/ID Tuplet
                this.__mapServices.remove(alias);
              }
            }

            // Did we have any valid services?
            if (this.__mapServices.count()) { // YES
              this.__ready = true;
              ok.call(context, this);
            } else { // NO
              this.__ready = true;
              nok.call(context, null, "No VALID Services set for the Connection.");
            }
          }, function(code, message) {
          // getServices() Failed
          nok.call(context, code, message);
        }, this);
      } else { // NO: Services Alias Set
        nok.call(context, null, "No Services set for the Connection.");
      }
    },
    /**
     * Execute service function
     *
     * @abstract
     * @param alias {String} One of the possible service functions (create,read,update,delete)
     * @param parameters {Map?null} (Optional) Parameter map to pass to the service function
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     */
    execute: function(alias, parameters, ok, nok, context) {
      // Initialize for new execution
      this.__resultError = -1;
      this.__resultMessage = null;
      this.__result = undefined;

      // Was the Service Alias Given?
      alias = utility.String.v_nullOnEmpty(alias, true);
      if (alias !== null) { // YES
        // Does the Service Alias map to a known Service?
        var service = this.__mapServices.get(alias);
        if (service !== null) { // YES
          var save_this = this;

          // Make sure that we have Valid OK/NOK/CONTEXT          
          context = qx.lang.Type.isObject(context) ? context : this;
          // Do we have a Valid OK Function?
          var wrapper_ok, wrapper_nok;
          if (!qx.lang.Type.isFunction(ok)) { // NO: Fire Event Instead
            wrapper_ok = function(code, message, results) {
              save_this.__resultError = code;
              save_this.__resultMessage = message;
              save_this.__result = results;
              save_this._mx_med_fireEventOK("connection-execution", [alias, results], message, code);
            };
          } else { // YES: Use that function
            var wrapper_ok = function(code, message, results) {
              save_this.__resultError = code;
              save_this.__resultMessage = message;
              save_this.__result = results;
              ok.call(context, results);
            };
          }
          // Do we have a Valid NOK Function?
          if (!qx.lang.Type.isFunction(nok)) { // NO: Fire Event Instead
            wrapper_nok = function(code, message, results) {
              save_this.__resultError = code;
              save_this.__resultMessage = message;
              save_this.__result = results;
              save_this._mx_med_fireEventOK("connection-execution", alias, message, code);
            };
          } else { // YES: Use that function
            wrapper_nok = function(code, message, results) {
              save_this.__resultError = code;
              save_this.__resultMessage = message;
              save_this.__result = results;
              nok.call(context, code, message);
            };
          }

          // Prepare and Execute the Service
          try {
            /* TODO: If the Service is Called AGAIN, before it has returned,
             * what happens? (i.e. can the service be called multiple times?)
             */
            service.
              reset().// Reset the Service
              key(parameters).// Set up Key for Service
              parameters(parameters).// Set up any parameters
              execute(wrapper_ok, wrapper_nok, context);  // Call the Service

            return true;
          } catch (e) {
            this.error("Exception [" + e + "] executing service ca.");
          }
        }
      }

      return false;
    },
    /**
     * Was execute() called on the connection?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    haveResult: function() {
      return typeof this.__result !== "undefined";
    },
    /**
     * Was the result of the Last Execution an Error?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultError: function() {
      this.__exception(!this.haveResult(), "Connection has no results available.");

      // Was Result OK?
      return this.__resultError !== 0;
    },
    /**
     * Was the result of the Last Execution a Simple Value (i.e. an int, boolean
     * or even an array of something, but not a record)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultValue: function() {
      return !this.isResultError() ||
        !qx.lang.Type.isObject(this.__result['return']) ||
        !this.__result['return'].hasOwnProperty('__type');
    },
    /**
     * Was the result of the Last Execution a Record (i.e. a Map of 
     * field<-->value tuplets)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultRecord: function() {
      return !this.isResultError() &&
        (qx.lang.Type.isObject(this.__result['return']) &&
          this.__result['return'].hasOwnProperty('__type') &&
          (this.__result['return'].__type === 'record'));
    },
    /**
     * Was the result of the Last Execution a Record Set (i.e. an Array of 
     * Records)?
     * 
     * Note: A Single Record is Always Considered a Record Set (i.e. can be 
     * returned as a Record Set), but a Record Set can not be returned as a
     * Record
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throws {String} If no previous execution
     */
    isResultRecordSet: function() {
      return !this.isResultError() &&
        (qx.lang.Type.isObject(this.__result['return']) &&
          this.__result['return'].hasOwnProperty('__type') &&
          (this.__result['return'].__type === 'record-set'));
    },
    /**
     * Retrieve the Last Execution Error Code
     *
     * @return {Integer} Last Execution's Error Code
     * @throws {String} If no previous execution
     */
    getErrorCode: function() {
      this.__exception(!this.haveResult(), "Connection has no results available.");
      return this.__resultError;
    },
    /**
     * Retrieve the Last Execution Error Message
     *
     * @return {String|null} Last Execution's Error Message, or 'null' if no 
     *   error message
     * @throws {String} If no previous execution
     */
    getErrorMessage: function() {
      this.__exception(!this.haveResult(), "Connection has no results available");
      return this.__resultMessage;
    },
    /**
     * Retrieve the un-encapsulated result of the execution (or in the case of
     * single value result, just simply return the result).
     *
     * @return {Var} Last Execution's Result
     * @throws {String} If no results available (i.e. no previous execution, or
     *   the last execution returned an error)
     */
    getResult: function() {
      this.__exception(!this.isResultError(), "Result of last execution was an error.");
      return this.__result['return'];
    },
    /**
     * Retrieve last execution's Record Result
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecord} Last Execution's Record
     * @throws {String} If no results available (i.e. no previous execution, or
     *   the last execution returned an error), or result is not a Record
     */
    getResultRecord: function() {
      this.__exception(!this.isResultRecord(), "Result of last execution was not a Record.");
      return new meta.ui.datasource.Record(this.getResult(), this);
    },
    /**
     * Retrieve last execution's Record Set Result
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecordSet} Last Execution's Record Set
     * @throws {String} If no results available (i.e. no previous execution, or
     *   the last execution returned an error), or result is not a Record Set
     */
    getRecordSet: function() {
      this.__exception(!this.isResultError(), "Result of last execution was an error.");
      this.__exception(!this.isResultRecordSet(), "Result of last execution was not a Record Set.");

      return new meta.ui.datasource.RecordSet(this, this.getResult(), this.__pageSize);
    },
    /**
     * Connection Allows New Records to be Created/Inserted?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     */
    canCreate: function() {
//      this.__exception(!this.isReady(), "Connection is not yet ready.");

      return this.hasService("create");
    },
    /**
     * Connection Allows Record Lookup's?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canRead: function() {
//      this.__exception(!this.isReady(), "Connection is not yet ready.");

      return this.hasService("read");
    },
    /**
     * Connection Allows for Record Updates?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canUpdate: function() {
//      this.__exception(!this.isReady(), "Connection is not yet ready.");

      return this.hasService("update");
    },
    /**
     * Connection Allows for Record Deletion?
     *
     * @return {Boolean} 'true' if YES, 'false' Otherwise
     * @throws {String} If connection is not ready
     */
    canDelete: function() {
//      this.__exception(!this.isReady(), "Connection is not yet ready.");

      return this.hasService("delete");
    },
    /**
     * Get a Record that is associated with this Connection. This record is 
     * initially set as a New Record, but can then be loaded using the 
     * Connection Object.
     *
     * @abstract
     * @return {meta.api.ui.datasource.IRecord} Record Object
     */
    getRecord: function() {
      // Create a New Record
      var record = new meta.ui.datasource.Record(null, this);

      /* NOTE: A New Record is Only Added to the Record Set when it's changes
       * are committed, until then it's purely virtual and can be deleted
       * with no harm done.
       */

      // TODO: We need to initialize the record with a set of possible fields
      return record;
    },
    /**
     * Write record back to the Back-end Data Source.
     *
     * @abstract
     * @param record {meta.api.ui.datasource.IRecord} Record to Create or Update
     * @throws {String} if the Connection is Not Ready or Connection cannot Create/Update Records
     */
    commitRecord: function(record) {
      // Is it a Record Object?
      if (!qx.lang.Type.isObject(record) ||
        !qx.Class.implementsInterface(record, meta.api.ui.datasource.IRecord)) { // NO
        this.__exception(true, "[record] Invalid Parameter Value.");
      }
      // ELSE: YES
      this.__exception(record.getConnection() !== this, "Record doesn't belong to this Connection.");
      this.__exception(!this.isReady(), "Connection is not ready.");

      // Is this a New Record?
      var service = "update";
      if (record.isNew()) { // YES
        this.__exception(!this.canCreate(), "Connection doesn't allow Record Creation.");
        service = "create";
      } else { // NO
        this.__exception(!this.canUpdate(), "Connection doesn't allow Record Update.");
      }

      this.execute(service, record.getFields(),
        function(result) {
          this._mx_med_fireEventOK("record-commit", [record, result]);
        },
        function(code, message) {
          this._mx_med_fireEventNOK("record-commit", record, message, code);
        }, this);
    },
    /**
     * Delete the Record
     *
     * @abstract
     * @param record {meta.api.ui.datasource.IRecord} Record to delete
     * @throws {String} if the Connection is Not Ready or Connection cannot Delete Records
     */
    deleteRecord: function(record) {
      // Is it a Record Object?
      if (!qx.lang.Type.isObject(record) ||
        !qx.Class.implementsInterface(record, meta.api.ui.datasource.IRecord)) { // NO
        this.__exception(true, "[record] Invalid Parameter Value.");
      }
      // ELSE: YES
      this.__exception(record.getConnection() !== this, "Record doesn't belong to this Connection.");
      this.__exception(!this.isReady(), "Connection is not ready.");
      this.__exception(!this.canDelete(), "Connection doesn't allow deleting.");

      this.execute("delete", record.getFields(),
        function(result) {
          this._mx_med_fireEventOK("record-delete", record);
        },
        function(code, message) {
          this._mx_med_fireEventNOK("record-delete", record, message, code);
        }, this);
    },
    /**
     * Load the record, using the given Map as the starting point, or
     * if no Map provided, using the actual Record Values as the starting point.
     * This load will reload the record with new values.
     * 
     * @abstract
     * @param record {meta.api.ui.datasource.IRecord} Record to Load/Reload
     * @param map {Map?null} Field<-->Value Map 
     * @throw {string} Exception if Record is Disconnected or Connection doesn't allow loading
     */
    loadRecord: function(record, map) {
      // Is it a Record Object?
      if (!qx.lang.Type.isObject(record) ||
        !qx.Class.implementsInterface(record, meta.api.ui.datasource.IRecord)) { // NO
        this.__exception(true, "[record] Invalid Parameter Value.");
      }
      // ELSE: YES
      this.__exception(record.getConnection() !== this, "Record doesn't belong to this Connection.");
      this.__exception(!this.isReady(), "Connection is not ready.");
      this.__exception(!this.canRead(), "Connection doesn't allow loading.");

      // Was a Map of Fields Provides?
      if (qx.lang.Type.isObject(map)) { // YES
        // Use the Map to Initialize the Record (before loading)
        record.setFields(map, true, true);
      }

      this.execute("read", record.getValues(),
        function(result) {
          // RECORD is Loaded by Capturing this Event
          this._mx_med_fireEventOK("record-read", [record, result]);
        },
        function(code, message) {
          this._mx_med_fireEventNOK("record-read", record, message, code);
        }, this);
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    /**
     * Throw an exception, if the condition matched
     * 
     * @param condition {Boolean} Did the condition match?
     * @param message {String} Message for Exception
     */
    __exception: function(condition, message) {
      // Did the condition match?
      if (condition) { // YES: Throw Exception
        throw message;
      }
      // ELSE: NO     
    }
  } // SECTION: MEMBERS
});
