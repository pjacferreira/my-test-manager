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
 * Generic List Entity (Meta Model)
 */
qx.Class.define("tc.meta.entities.ListEntity", {
  extend: tc.meta.entities.BaseEntity,
  implement: tc.meta.entities.IMetaList,
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
    this.base(arguments, 'list', id);


    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('key'), "[metadata] Is Missing Required Property [key]!");
      qx.core.Assert.assertTrue(metadata.hasOwnProperty('display'), "[metadata] Is Missing Required Property [display]!");
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
     * Returns the List's Description
     *
     * @return {String} List's Description, or NULL if none
     */
    getDescription: function() {
      return this.__oMetaData['description'];
    },
    /**
     * Does the List Allow the Service Indicated by the Alias?
     *
     * @param alias {String} List Service Alias
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasService: function(alias) {
      return this.__oMetaDataServices !== null ? this.__oMetaDataServices.hasOwnProperty(alias) : false;
    },
    /**
     * Returns the Service ID on the Service Alias (required aliases 'list', optional 'count')
     *
     * @param alias {String} List Service Alias
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
     * Returns the field, that is used as the key, for every record.
     *
     * @return {String} Key Field ID
     */
    getKeyField: function() {
      return this.__oMetaData['key'];
    },
    /**
     * Returns the field to display, in the case of ComboBox (when closed) or
     * SelectBox.
     *
     * @return {String[]} Display Field ID
     */
    getDisplayField: function() {
      return this.__oMetaData['display'];
    },
    /**
     * Returns the list of ALL fields To Display (in List or Table Format)
     *
     * @return {String[]} Array of Field IDs
     */
    getColumns: function() {
      return this.__oMetaData['fields'];
    },
    /*
     *****************************************************************************
     PRIVATE METHODS
     *****************************************************************************
     */
    __fieldsList: function(list) {
      if (qx.lang.Type.isString(list)) {
        list = tc.util.Array.CSVtoCSVtoArray(list);
      }

      if (qx.lang.Type.isArray(list)) {
        list = tc.util.Array.clean(tc.util.Array.trim(list));
      } else {
        list = null;
      }

      return list;
    }
  }
});
