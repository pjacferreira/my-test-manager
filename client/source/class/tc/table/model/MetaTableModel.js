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

qx.Class.define("tc.table.model.MetaTableModel", {
  extend: qx.ui.table.model.Remote,
  implement: [tc.table.filtered.IFilteredTableModel],
  include: [tc.table.filtered.MFilteredTableModel],
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
    "nok": "qx.event.type.Data"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor
   * 
   * @param form {var} Table Name or Meta Package (if pre-initialize)
   */
  construct: function(table) {
    this.base(arguments);

    this.base(arguments);
    // Create or Use Form Meta Package
    if (qx.lang.Type.isString(table)) {
      table = new tc.meta.packages.TablePackage(table);
    }

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInterface(table, tc.meta.packages.ITablePackage, "[table] Is not of the expected type!");
    }
    this._tablePackage = table;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    this.__tableMetaData = this.__mapColumnIndex = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _tablePackage: null,
    _tableEntity: null,
    _fieldsPackage: null,
    _ServicesPackage: null,
    _bReady: null,
    _bPendingInitialization: false,
    __mapColumnIndex: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Initialize the model.
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    initialize: function(callback) {
      if (this._bPendingInitialization) {
        throw "Multiple Initilization Calls";
        return;
      }

      this._bPendingInitialization = true;
      callback = this._prepareCallback(callback);

      if (!this._bReady) {
        this._initializePackage(callback);
      } else {
        this._bPendingInitialization = false;
        this._callbackModelReady(callback, true);
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
    // overloaded - called whenever the table requests the row count
    _loadRowCount: function() {
      if (this.isReady()) {

        var service = this._servicesPackage.getService(this._tableEntity.getService('count'));

        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertInterface(service, tc.meta.entities.IMetaService, "[service] Is not of the expected type!");
        }

        // Send request
        var filter = this._buildFilter();
        service.execute(filter !== null ? {
          'virtual:filter': filter
        } : null, {
          'ok': function(count) {
            this._onRowCountLoaded(count);
          },
          'nok': function(message) {
            // TODO: Log Error
            this._onRowCountLoaded(0);
          },
          'context': this});
      } else {
        // Model hasn't been initialized
        this._onRowCountLoaded(0);
      }
    },
    // overloaded - called whenever the table requests new data
    _loadRowData: function(firstRow, lastRow) {
      if (this.isReady()) {

        var service = this._servicesPackage.getService(this._tableEntity.getService('list'));

        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertInterface(service, tc.meta.entities.IMetaService, "[service] Is not of the expected type!");
        }

        // Send request
        var filter = this._buildFilter();
        var sort = this._buildSort();
        service.execute({
          'virtual:filter': filter,
          'virtual:sort': sort,
          'virtual:limit': lastRow - firstRow + 1
        }, {
          'ok': function(rows) {
            this._onRowDataLoaded(rows);
          },
          'nok': function(message) {
            // TODO: Log Error
          },
          'context': this});
      }
    },
    _buildSort: function() {
      // get the column index to sort and the order
      var sortColumn = this.getSortColumnIndex();
      if (sortColumn >= 0) {
        var field = this.getColumnId(sortColumn);
        return this.isSortAscending() ? field : '!' + field;
      }

      return undefined;
    },
    changeColumnFilter: function(column, value, old) {
      if (column != null) {
        this._changeColumnFilter(this.getColumnId(column), value);
      }
    },
    /*
     *****************************************************************************
     HELPER (INITIALIZATION) METHODS
     *****************************************************************************
     */
    /**
     * Initialize the Table Package
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    _initializePackage: function(callback) {
      if (this._tablePackage.isReady()) {
        this._callbackModelReady(callback, true);
      } else { // Initialize Table Package
        this._tablePackage.initialize({
          'ok': function() {
            this._tableEntity = this._tablePackage.getTable();
            this._initializeFields(callback);
          },
          'nok': function(message) {
            this._bPendingInitialization = false;
            this._callbackModelReady(callback, false, message);
          },
          'context': this
        });
      }
    },
    /**
     * Initialize the Table Fields Package
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    _initializeFields: function(callback) {
      if (this._tablePackage.getFields().isReady()) {
        this._fieldsPackage = this._tablePackage.getFields();
        this._initializeServices(callback);
      } else {
        // Initialize the Fields Package
        this._tablePackage.getFields().initialize({
          'ok': function() {
            this._fieldsPackage = this._tablePackage.getFields();
            this._initializeServices(callback);
          },
          'nok': function(message) {
            this._bPendingInitialization = false;
            this._callbackModelReady(callback, false, message);
          },
          'context': this
        });
      }
    },
    /**
     * Initialize the Table Fields Package
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    _initializeServices: function(callback) {
      if (this._tablePackage.getServices().isReady()) {
        this._servicesPackage = this._tablePackage.getServices();
        // Initialize the Model
        this._initializeModel(callback);
      } else {
        // Initialize the Services Package
        this._tablePackage.getServices().initialize({
          'ok': function() {
            this._servicesPackage = this._tablePackage.getServices();

            // Initialize the Model
            this._initializeModel(callback);
          },
          'nok': function(message) {
            this._bPendingInitialization = false;
            this._callbackModelReady(callback, false, message);
          },
          'context': this
        });
      }
    },
    /**
     * Initialize the Table Model
     *
     * @param callback {Object ? null} Callback Object, NULL if we would rather use events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     */
    _initializeModel: function(callback) {
      // Known Fields Definitions
      var columns = this._tableEntity.getColumns();

      // Create a Map between Column Names and Column Index
      this.__mapColumnIndex = {};
      for (var i = 0; i < columns.length; ++i) {
        this.__mapColumnIndex[columns[i]] = i;
      }

      // 'columns' now contains only the valid set of columns in the table
      var field, titles = [];
      for (var i = 0; i < columns.length; ++i) {
        field = this._fieldsPackage.getField(columns[i]);
        titles.push(field.getLabel());
      }

      // Set the Columns to Display
      this.setColumns(titles, columns);

      /*
       * Limit Sortable Columns
       */
      if (this._tableEntity.canSort()) {
        var sort_columns = this._tableEntity.getSortFields();

        // Set All Columns to Not Sortable
        for (i = 0; i < columns.length; ++i) {
          this.setColumnSortable(i, false);
        }

        // Set Only Columns Listed as Being Sortable
        for (i = 0; i < sort_columns.length; ++i) {
          if (this.__mapColumnIndex.hasOwnProperty(sort_columns[i])) {
            this.setColumnSortable(this.__mapColumnIndex[sort_columns[i]], true);
          }
        }
      }

      /*
       * Limit Filterable Columns
       */
      if (this._tableEntity.canFilter()) {
        var filter_columns = this._tableEntity.getFilterFields();

        // Set Only Columns Listed as Usable Filters
        for (i = 0; i < filter_columns.length; ++i) {
          if (this.__mapColumnIndex.hasOwnProperty(filter_columns[i])) {
            this.setColumnFilterable(this.__mapColumnIndex[filter_columns[i]], true);
          }
        }
      }

      this._bReady = true;
      this._bPendingInitialization = false;
      this._callbackModelReady(callback, true);
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
    } // FUNCTION: _callbackModelReady    
  } // SECTION: MEMBERS
});
