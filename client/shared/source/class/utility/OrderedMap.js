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
 * Ordered MAP Class (a mixture of Array and Object), in which the order of
 * insertion is maintained, in the key list
 */
qx.Class.define("utility.OrderedMap", {
  extend : qx.core.Object,

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * Simple Map Implementation
   */
  construct : function() {
    this.base(arguments);

    // Initialize Variables
    this.__map = {

    };
    this.__order = [];
  },

  /**
   *
   */
  destruct : function() {
    // NOTE: Do not call qx.core.Object:destruct, as THERE IS NONE, and forces an exception 
    // this.base(arguments);

    // Cleanup Variables
    this.__map = null;
    this.__order = null;
  },

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members : {
    __map : null,
    __order : null,
    has : function(key) {
      return this.__map.hasOwnProperty(key);
    },
    get : function(key) {
      return this.__map.hasOwnProperty(key) ? this.__map[key] : null;
    },
    search : function(match, context) {
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
    add : function(key, value) {
      if (this.__map.hasOwnProperty(key)) {
        this.__map[key] = value;
      } else {
        this.__map[key] = value;
        this.__order.push(key);
      }
    },
    addAt : function(index, key, value) {
      if (qx.lang.Type.isNumber(index)) {
        // If a Widget with the ID already exists - remove it
        if (this.__map.hasOwnProperty(key)) {
          this.remove(key);
        }
        this.__map[key] = value;
        if (index < 0) {                         // At the Start
          this.__order.unshift(key);
        } else if (index >= (this.__order.length - 1)) {             // At the End
          this.__order.push(key);
        } else {                                 // Some where in the middle
          this.__order.splice(index, 0, key);
        }
      }
    },
    remove : function(key) {
      var value = this.get(key);
      if (value !== null) {
        if (this.__order.length > 1) {
          var idx = this.__order.indexOf(key);
          this.__order.splice(idx, 1);
        } else {
          this.__order = [];
        }
        delete this.__map[key];
      }
      return value;
    },
    removeAt : function(index) {
      var key = this.keyOf(index);
      if (key !== null) {
        return this.remove(key);
      }

      // TODO Optimize
      return null;
    },
    reset : function() {
      this.__map = {

      };
      this.__order = [];
    },
    count : function() {
      return this.__order.length;
    },
    keys : function() {
      return this.__order;
    },
    keyOf : function(index) {
      if (qx.lang.Type.isNumber(index)) {
        if ((index >= 0) && (index <= this.__order.length)) {
          return this.__order[index];
        }
      }
      return null;
    },
    valueOf : function(index) {
      var key = this.keyOf(index);
      if (key !== null) {
        return this.__map[key];
      }
      return null;
    },
    indexOf : function(key) {
      if (this.__map.hasOwnProperty(key)) {
        this.__order.indexOf(key);
      }
      return -1;
    },
    values : function() {
      var list = [];
      for (var i = 0; i < this.__order.length; ++i) {
        list.push(this.__map[this.__order[i]]);
      }
      return list;
    },
    map : function() {
      return this.__map;
    }
  }                                              // SECTION: MEMBERS
});
