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

 #require(tc.util.Array)
 #require(tc.table.meta.TableSource)

 ************************************************************************ */
qx.Class.define("tc.table.model.MetaTableModel", {
  extend: qx.ui.table.model.Remote,
  implement: [tc.table.filtered.IFilteredTableModel],
  include: [tc.table.filtered.MFilteredTableModel],

  properties: {
    tableID: {
      check: "String",
      init: null,
      apply: "_applyTableID"
    },
    metaLoader: {
      check: "Object",
      init: null,
      apply: "_applyMetaLoader"
    }
  },

  events: {
    /**
     * Fired when the meta Data is Completely Loaded into the Table Model.
     */
    "metadataLoaded": "qx.event.type.Event",

    /**
     * Fired on failure to load the Meta Data
     */
    "metadataInvalid": "qx.event.type.Data"
  },

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   * @param tableID {String} The Table ID to be used with the Loader
   * @param metaDataLoader {tc.meta.ITableMetadataLoader ? null}
   *   The Meta Data Loader.
   */
  construct: function (tableID, metaDataLoader) {
    this.base(arguments);

    this.setTableID(tableID);
    this.setMetaLoader(metaDataLoader || this.getNullMetadataLoader());
  },

  destruct: function () {
    this.__tableMetaData = this.__mapColumnIndex = null;
  },

  members: {
    __tableMetaData: null,
    __fieldsMetaData: null,
    __loaded: false,
    __mapColumnIndex: null,

    _applyTableID: function (value, old) {
      var wasLoaded = this.__loaded;
      this.__loaded = false;

      if (wasLoaded) { // Reload the Table Metadata
        this.init(null);
      }
    },

    _applyMetaLoader: function (value, old) {
      var wasLoaded = this.__loaded;
      this.__loaded = false;

      if (wasLoaded) { // Reload the Table Metadata
        this.init(null);
      }
    },


    // Override
    load: function (table) {
      if (!this.__loaded) {
        var tableID = this.getTableID();
        var loader = this.getMetaLoader();

        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertNotNull(tableID, "Loader has to be initialized before the class can be used!");
          qx.core.Assert.assertNotNull(loader, "Loader has to be initialized before the class can be used!");
        }

        if (loader && (tableID != null)) {

          if (!this.__loaded) { // Need to Load Metadata
            // Initiate Load Process
            loader.getTableMeta(tableID, function (error_code, error_message, type, data) {
              if (error_code) {
                this.fireDataEvent("metadataInvalid", {
                  error: error_code,
                  message: error_message
                }, null);
              } else {
                // Save the Tables Meta Data
                var key=tc.util.Object.getFirstProperty(data);
                this.__tableMetaData = data[key];

                // Load Fields in order to Build the Table Model
                this.__loadTableFields(this.__tableMetaData);
              }
            }, this);
          } else { // Metadata already loaded
            this.__initializeModel(this.__tableMetaData, this.__fieldsMetaData);
          }
        }
      }
    },

    __loadTableFields: function (table_def) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue(table_def.hasOwnProperty('datastore'), "INVALID TABLE METADATA FORMAT: Table Metadata Requires a 'datastore' definiton!");
        qx.core.Assert.assertTrue(table_def.datastore.hasOwnProperty('fields'), "INVALID TABLE METADATA FORMAT: Table Metadata Requires a 'fields' list!");
        qx.core.Assert.assertObject(table_def.datastore.fields, "INVALID TABLE METADATA FORMAT: 'fields' should be an array of Strings!");
      }

      var fields = tc.util.Object.valueFromPath(table_def,['datastore', 'fields']);
      if (fields !== null) {
        this.getMetaLoader().getFieldsMeta(fields, function (error_code, error_message, type, data) {
          if (error_code) {
            this.fireDataEvent("metadataInvalid", {
              error: error_code,
              message: error_message
            }, null);
          } else {

            // Save the Fields Meta Data forLater User
            this.__fieldsMetaData = data;

            // Initialize the Model
            this.__initializeModel(table_def, this.__fieldsMetaData);
          }
        }, this);
      }
    },

    __initializeModel: function (table_def, fields_def) {
      // Columns Listed for Display
      var columns = table_def.hasOwnProperty('columns') ? table_def.columns : table_def.fields;
      // Known Fields Definitions
      var field_names = Object.keys(fields_def).sort();

      // Create a Map between Column Names and Column Index
      this.__mapColumnIndex = {};
      for (i = 0; i < columns.length; ++i) {
        this.__mapColumnIndex[columns[i]] = i;
      }

      // Only Known fields can be displayed (All others have to be removed)
      var remove = tc.util.Array.difference(field_names, columns.slice(0).sort());
      if (remove) {

        // Remove all of the missing columns from the object
        var removed = false;
        for (i = 0; i < remove.length; ++i) {
          if (this.__mapColumnIndex.hasOwnProperty(remove[i])) { // Remove Property
            delete this.__mapColumnIndex[remove[i]];
            removed = true;
          }
        }

        if (removed) {
          // Rebuild a Sort of Sparse Array
          var sparse = new Array(columns.length);
          for (var key in this.__mapColumnIndex) {
            if (this.__mapColumnIndex.hasOwnProperty(key)) {
              sparse[this.__mapColumnIndex[key]] = key;
            }
          }

          // Condense the Sparse removing NULL entries
          columns = new Array();
          var j = 0;
          for (i = 0; i < sparse.length; ++i) {
            if (sparse[i] != null) {
              columns.push(sparse[i]);

              // Maintain the Map Between Columns and Indexes (used for Sort Columns)
              this.__mapColumnIndex[sparse[i]] = j++;
            }
          }
        }
      }

      // 'columns' now contains only the valid set of columns in the table
      var column, titles = new Array();
      for (var i = 0; i < columns.length; ++i) {
        column = fields_def[columns[i]];
        if (column.hasOwnProperty('label')) {
          titles.push(column.label);
        } else {
          titles.push(columns[i]);
        }
      }

      // Set the Columns to Display
      this.setColumns(titles, columns);

      /*
       * Limit Sortable Columns
       */
      var sort_columns = table_def.hasOwnProperty('sort-on') ? table_def['sort-on'] : null;
      if (sort_columns != null) {
        // Set All Columns to Not Sortable
        for (i = 0; i < columns.length; ++i) {
          this.setColumnSortable(i, false);
        }

        // Set Only Columns Listed to Sortable
        for (i = 0; i < sort_columns.length; ++i) {
          if (this.__mapColumnIndex.hasOwnProperty(sort_columns[i])) {
            this.setColumnSortable(this.__mapColumnIndex[sort_columns[i]], true);
          }
        }
      }

      /*
       * Limit Filterable Columns
       */
      var filter_columns = table_def.hasOwnProperty('filter-on') ? table_def['filter-on'] : null;
      if (filter_columns != null) {
        // Default: No Columns are Sortable
        // Set Only Columns Listed to Sortable
        for (i = 0; i < filter_columns.length; ++i) {
          if (this.__mapColumnIndex.hasOwnProperty(filter_columns[i])) {
            this.setColumnFilterable(this.__mapColumnIndex[filter_columns[i]], true);
          }
        }
      }

      this.__loaded = true;
      this.fireEvent("metadataLoaded");
    },

    getNullMetadataLoader: function () {
      return new tc.table.meta.TableSource();
    },

    // overloaded - called whenever the table requests the row count
    _loadRowCount: function () {

      var url = this.__url();
      if (url != null) {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          this._onRowCountLoaded(e.getResult());
        }, this);

        // Send request
        var filter = this._buildFilter();

        req.send(url, 'count', null, filter == null ? null : { filter: filter });
      } else {
        // Table Metadata is Invalid or not has not yet been loaded
        this._onRowCountLoaded(0);
      }
    },

    // overloaded - called whenever the table requests new data
    _loadRowData: function (firstRow, lastRow) {
      var url = this.__url();
      if (url != null) {
        var req = new tc.services.json.TCServiceRequest();

        req.addListener("service-ok", function (e) {
          var users = e.getResult();
          this._onRowDataLoaded(users);
        }, this);

        // Send request
        var filter = this._buildFilter();
        var sort = this.__buildSort();
        req.send('user', 'list', null, this.__mixinNotNull({}, { filter: filter, sort: sort, limit: lastRow - firstRow + 1}));
      }
    },

    __buildSort: function () {
      // get the column index to sort and the order
      var sortColumn = this.getSortColumnIndex();
      if (sortColumn >= 0) {
        var field = this.getColumnId(sortColumn);
        return this.isSortAscending() ? field : '!' + field;
      }

      return undefined;
    },

    changeColumnFilter: function (column, value, old) {
      if (column != null) {
        this._changeColumnFilter(this.getColumnId(column), value);
      }
    },

    __url: function () {

      var url = null;

      if (this.__loaded) {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertTrue(this.__tableMetaData.hasOwnProperty('datastore'), "INVALID TABLE METADATA FORMAT: Table Metadata Requires a 'url' list!");
          qx.core.Assert.assertTrue(this.__tableMetaData.datastore.hasOwnProperty('source'), "INVALID TABLE METADATA FORMAT: Table Metadata Requires a 'url' list!");
          qx.core.Assert.assertTrue(this.__tableMetaData.datastore.source.hasOwnProperty('url'), "INVALID TABLE METADATA FORMAT: Table Metadata Requires a 'url' list!");
        }

        // Get the URL
        url = tc.util.String.nullOnEmpty(tc.util.Object.valueFromPath(this.__tableMetaData,['datastore', 'source','url']));
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertNotNull(url, "INVALID TABLE METADATA FORMAT: 'url'  Has to be a NOT EMPTY String");
        }

        // Save the Value Back, just in case it was trimmed
        tc.util.Object.setFromPath(this.__tableMetaData,['datastore', 'source','url'], url);
      }

      return url;
    },

    __mixinNotNull: function (into, mixin) {

      for (var key in mixin) {
        if (mixin.hasOwnProperty(key)) {
          if (mixin[key] == null) {
            continue;
          } else {
            into[key] = mixin[key];
          }
        }
      }

      return into;
    }
  }
});
