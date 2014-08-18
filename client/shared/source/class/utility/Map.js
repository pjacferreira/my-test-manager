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
 ************************************************************************ */

/**
 * MAP Class (a mixture of Array and Object) to simplify things.
 */
qx.Class.define("utility.Map", {
  extend: qx.core.Object,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * Simple Map Implementation
   */
  construct: function() {
    this.base(arguments);

    // Initialize Variables
    this.__map = {
    };
  },
  /**
   *
   */
  destruct: function() {
    // NOTE: Do not call qx.core.Object:destruct, as THERE IS NONE, and forces an exception 
    // this.base(arguments);

    // Cleanup Variables
    this.__map = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __map: null,
    __count: 0,
    has: function(key) {
      return this.__map.hasOwnProperty(key);
    },
    get: function(key) {
      return this.__map.hasOwnProperty(key) ? this.__map[key] : null;
    },
    search: function(match, context) {
      context = qx.lang.Type.isObject(context) ? context : this;
      for (var key in this.__map) {
        if (this.__map.hasOwnProperty(key)) {
          if (match.call(context, key, this.__map[key])) {
            return this.__map[key];
          }
        }
      }
      return null;
    },
    add: function(key, value) {
      if (this.__map.hasOwnProperty(key)) {
        this.__map[key] = value;
      } else {
        this.__map[key] = value;
        this.__count++;
      }
    },
    remove: function(key) {
      var value = this.get(key);
      if (value !== null) {
        delete this.__map[key];
        this.__count--;
      }
      return value;
    },
    reset: function() {
      this.__map = {
      };
      this.__count = 0;
    },
    count: function() {
      return this.__count;
    },
    keys: function() {
      var list = [];
      for (var key in this.__map) {
        if (this.__map.hasOwnProperty(key)) {
          list.push(key);
        }
      }
      return list;
    },
    values: function() {
      var list = [];
      for (var key in this.__map) {
        if (this.__map.hasOwnProperty(key)) {
          list.push(this.__map[key]);
        }
      }
      return list;
    },
    map: function() {
      return this.__map;
    },
    clone: function() {
      // Create a New Map to Contain the Clone
      var map = new utility.Map();
      
      // Go through the Keys and Copy them
      var key, keys = this.keys();
      for (var i = 0; i < keys.length; ++i) {
        key = keys[i];
        map.set(key[i], this.get(key));
      }

      return map;
    }
  }                                              // SECTION: MEMBERS
});
