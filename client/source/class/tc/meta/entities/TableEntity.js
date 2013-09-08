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
qx.Class.define("tc.meta.entities.TableEntity", {
  extend: tc.meta.entities.BaseEntity,
  implement: tc.meta.entities.IMetaTable,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for a Table Entity
   * 
   * @param id {String} Table ID
   * @param metadata {Object} Table Metadata
   */
  construct: function(id, metadata) {
    this.base(arguments, 'table', id);


    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('title'), "[metadata] Is Missing Required Property [title]!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('services') &&
              qx.lang.Type.isObject(metadata['services']), "[metadata] is Missing or has an Invalid [services] Property!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('fields'), "[metadata] Is Missing Required Property [fields]!");
    }

    this.__oMetaData = qx.lang.Object.clone(metadata, true);
    this.__oMetaData['fields'] = this.__fieldsList(metadata['fields']);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertTrue(this.__oMetaData['fields'] !== null, "[metadata] Is has an Invalid [fields] Property!");
    }

    // Clear up Services
    var arServices = [];
    var serviceID = null;
    for (var service in metadata.services) {
      if (metadata.services.hasOwnProperty(service)) {
        serviceID = tc.util.String.nullOnEmpty(metadata.services[service]);
        if (serviceID) {
          this.__oMetaData['services'][service] = serviceID;
          arServices.push(service);
        }
      }
    }

    if (arServices.length > 0) {
      this.__arServiceAlias = arServices;
      this.__oMetaDataServices = this.__oMetaData['services'];
    } else {
      this.__oMetaData['services'] = null;
    }

    this.__oMetaData['columns'] = this.__fieldsList(this.__oMetaData.hasOwnProperty('columns') ? this.__oMetaData['columns'] : null);
    this.__oMetaData['hidden'] = this.__fieldsList(this.__oMetaData.hasOwnProperty('hidden') ? this.__oMetaData['hidden'] : null);
    this.__oMetaData['sort-on'] = this.__fieldsList(this.__oMetaData.hasOwnProperty('sort-on') ? this.__oMetaData['sort-on'] : null);
    this.__oMetaData['filter-on'] = this.__fieldsList(this.__oMetaData.hasOwnProperty('filter-on') ? this.__oMetaData['filter-on'] : null);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__arServiceAlias = null;
    this.__oMetaDataServices = null;
    this.__oMetaData = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Field Meta Data
    __oMetaData: null,
    __arServiceAlias: null,
    __oMetaDataServices: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Is this a Read Only Table?
     *
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
      // For NOW, we only allow for Read Only Tables
      return true;
    },
    /**
     * Returns the Table's Title
     *
     * @return {String} Table's Title
     */
    getTitle: function() {
      return this.__oMetaData['title'];
    },
    /**
     * Does the Table Allow the Service Indicated by the Alias?
     *
     * @param alias {String} Table Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
      return this.__oMetaDataServices !== null ? this.__oMetaDataServices.hasOwnProperty(alias) : false;
    },
    /**
     * Returns the Service ID on the Service Alias (required aliases 'list' and 'count')
     *
     * @param alias {String} Table Service Alias
     * @return {String} Return Service ID or NULL on Failure
     */
    getService: function(alias) {
      return (this.__oMetaDataServices !== null) && this.__oMetaDataServices.hasOwnProperty(alias) ? this.__oMetaDataServices[alias] : null;
    },
    /**
     * Return Service Aliases Supported
     *
     * @return {String[]} Service Aliases Supported, or NULL if none
     */
    getServices: function() {
      return this.__arServiceAlias;
    },
    /**
     * Returns the list of ALL fields used.
     *
     * @return {String[]} Array of Field IDs in the Group
     */
    getFields: function() {
      return this.__oMetaData['fields'];
    },
    /**
     * Returns the list of ALL fields To Display.
     *
     * @return {String[]} Array of Field IDs in the Group
     */
    getColumns: function() {
      return this.__oMetaData.hasOwnProperty('columns') ? this.__oMetaData['columns'] : this.getFields();
    },
    /**
     * Returns the list of fields that are Initially Hidden.
     *
     * @abstract
     * @return {String[]} Array of Field IDs, or NULL if No Fields are to be hidden
     */
    getHiddenFields: function() {
      return this.__oMetaData['hidden'];
    },
    /**
     * Does the Table Allow Sorting?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canSort: function() {
      return this.__oMetaData['sort-on'] !== null;
    },
    /**
     * Returns the list of fields that can be used to Sort the Table Data Set.
     *
     * @abstract
     * @return {String[]} Array of Field IDs
     */
    getSortFields: function() {
      return this.__oMetaData['sort-on'];
    },
    /**
     * Does the Table Allow Filtering?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    canFilter: function() {
      return this.__oMetaData['filter-on'] !== null;
    },
    /**
     * Returns the list of fields that can be used to Filter the Table Data Set.
     *
     * @return {String[]} Array of Field IDs, or NULL if Table is not Filterable
     */
    getFilterFields: function() {
      return this.__oMetaData['filter-on'];
    },
    /*
     *****************************************************************************
     PRIVATE METHODS
     *****************************************************************************
     */
    __fieldsList: function(list) {
      if (qx.lang.Type.isString(list)) {
        list = tc.util.Array.clean(tc.util.Array.trim(list.split(',')));
      } else if (qx.lang.Type.isArray(list)) {
        list = tc.util.Array.clean(tc.util.Array.trim(list));
      } else {
        list = null;
      }

      return list;
    }
  }
});
