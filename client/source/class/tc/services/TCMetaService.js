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
 #require(qx.core.Environment)
 ************************************************************************ */
qx.Class.define("tc.services.TCMetaService", {
  type: "singleton",
  extend: qx.core.Object,

  properties: {
    baseURL: {
      check: 'String',
      init: null,
      nullable: true
    }
  },

  construct: function () {
    this.__cache = { 'tables': {}, 'forms': {} };
  },

  destruct: function () {
    this.__cache = null;
  },

  members: {
    __cache: null,

    getMetaData: function (type, keys, callback, context) {
      callback = qx.lang.Type.isFunction(callback) ? callback : null;
      context = qx.lang.Type.isObject(context) ? context : this;

      if (qx.lang.Type.isString(keys)) {
        if(type[type.length-1] == 's') { // For Single Items, the meta service request is always singular
          type=type.substring(0,type.length-1);
        }
        keys = tc.util.String.nullOnEmpty(keys, true);
      } else if (qx.lang.Type.isArray(keys)) {
        if(type[type.length-1] != 's') { // For Lists, the meta service request is always plural
          type+='s';
        }
      } else {
        return false;
      }

      return this.__retrieveData(type, keys, callback, context);
    },

    __retrieveData: function (type, keys, callback, context) {
      var haveCachedData = false;
      var cachedData = {};
      var pendingKeys = new Array();

      // Convert String to an Array to Simplify Code
      if (qx.lang.Type.isString(keys)) {
        keys = new Array(keys);
      }

      var data;
      var key;
      for (var i = 0; i < keys.length; ++i) {
        key = tc.util.String.nullOnEmpty(keys[i]);
        if (key != null) {
          var data = this.__retrieveDefinition(type, key);
          if (data != null) {
            haveCachedData = true;
            cachedData[key] = data;
          } else {
            pendingKeys.push(key);
          }
        }
      }

      if (pendingKeys.length) { // Some Items pending
        return this.__request(type, pendingKeys, callback, context, haveCachedData ? cachedData : null);
      } else { // Nothing Pending (Nothing to Resolve or Completely Resolved from Cache)
        callback.call(context, 0, 'Ok', type, haveCachedData ? cachedData : null);
        return true;
      }
    },

    __request: function (type, key, callback, context, cachedData) {

      if (this.getBaseURL() == null) {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertNotNull(this.baseURL, "Need to initialize the Meta Service Base URL, before using this class!");
          qx.core.Assert.assertString(type);
        }
        return false;
      }

      // Create the Request
      var request = new tc.services.json.TCServiceRequest();

      // Add Listeners (Ok, Error) to handle results
      request.addListener("service-ok", function (e) {
        var result = e.getResult();
        // Cache New Items
        this.__cacheDefinition(type, result);

        // Merge in Previously Cached Items
        if (cachedData != null) {
          result = qx.lang.Object.mergeWith(result, cachedData, false);
        }
        callback.call(context, 0, 'Ok', type, result);
      }, this);

      request.addListener("service-error", function (e) {
        callback.call(context, e.getErrorCode(), e.getErrorMessage(), type, null);
      }, this);

      // key is an array, convert to a list
      if (qx.lang.Type.isArray(key)) {
        key = this.__toList(key);
      } else {
        // Remove Trailing 's' in type (i.e. fields becomes field, if we only want to retrieve a single field's data)
        if(type[type.length-1] == 's') {
          type = type.substr(0,type.length-1)
        }
      }

      request.send(this.getBaseURL(), [type, key]);
      return true;
    },

    __toList: function (array) {

      var list, value;
      for (var i = 0; i < array.length; ++i) {
        value = tc.util.String.nullOnEmpty(array[i], true);
        if (value != null) {
          if (list != null) {
            list += ',' + value;
          } else {
            list = value;
          }
        }
      }

      return list;
    },

    __retrieveDefinition: function (type, key) {
      var cacheRoot = this.__typeToCacheEntry(type);
      if ((cacheRoot != null) && cacheRoot.hasOwnProperty(key)) {
        /* TODO Implement a Limited Cache, so as to conserve memory (i.e. touch most recently accessed items and
         * periodically remove least used items)
         */
        return cacheRoot[key];
      }
    },

    __cacheDefinition: function (type, value) {

      var mergeInto = this.__typeToCacheEntry(type);
      if (mergeInto != null) { // Save into Cache
        qx.lang.Object.mergeWith(mergeInto, value, true);
      }

      return mergeInto;
    },

    __typeToCacheEntry: function (type) {
      switch (type) {
        case 'table':
        case 'tables':
          return this.__cache.tables;
        case 'form':
        case 'forms':
          return this.__cache.forms;
        case 'field':
        case 'fields':
          return this.__cache.fields;
        case 'action':
        case 'actions':
          return this.__cache.actions;
        default:
          if (qx.core.Environment.get("qx.debug")) {
            qx.core.Assert.fail("Meta Data Type [" + type + "] unknown!");
          }
      }

      return null;
    }
  }
});
