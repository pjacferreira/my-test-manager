/*
 * jQuery Utility Functions
 *
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

;
(function ($) {

  // equivalen to PHP isset
  $.isset = function (value) {
    return (value !== undefined) && (value !== null);
  };

  // equivalue to PHP is_string
  $.isString = function (value) {
    return $.type(value) === 'string';
  }

  $.isObject = function (value) {
    return $.type(value) === 'object';
  }

  // String Helper Functions
  $.strings = $.extend({}, $.strings, {
    nullOnEmpty: function (value) {
      if ($.isString(value)) {
        value = $.trim(value);
        return value.length ? value : null;
      }

      return null;
    }
  });

  // Object Helper Functions
  $.objects = $.extend({}, $.objects, {
    keyToPath: function (keypath) {
      if (!$.isArray(keypath)) {
        keypath = $.strings.nullOnEmpty(keypath);
        if (keypath !== null) {
          keypath = keypath.split('.');
        }
      }

      return  keypath;
    },
    parent: function (keypath, object) {
      var path = $.objects.keyToPath(keypath);
      if ($.isObject(object) && (path !== null)) {
        var i, l, parent;
        for (parent = object, i = 0, l = path.length - 1; i < l; ++i) {
          if (parent.hasOwnProperty(path[i])) {
            parent = parent[path[i]];
          } else {
            parent = null;
            break;
          }
        }

        return parent;
      }
      return null;
    },
    key_exists: function (keypath, object) {
      var path = $.objects.keyToPath(keypath);
      if ($.isObject(object) && (path !== null)) {
        var parent = $.objects.parent(path, object);
        return (parent !== null) && parent.hasOwnProperty(path[path.length - 1]) ? true : false;
      }

      return false;
    },
    get: function (keypath, object, def) {
      def = $.isset(def) ? def : null;
      var path = $.objects.keyToPath(keypath);
      if ($.isObject(object) && (path !== null)) {
        var parent = $.objects.parent(path, object);
        var key = (parent !== null) ? path[path.length - 1] : null;
        return (key !== null) && parent.hasOwnProperty(key) ? parent[key] : def;
      }

      return def;
    },
    set: function (keypath, object, value) {
      var path = $.objects.keyToPath(keypath);
      if ($.isObject(object) && (path !== null)) {
        var i, l = path.length - 1, parent = object;

        // Find Last Existing Parent
        for (i = 0; i < l; ++i) {
          if (parent.hasOwnProperty(path[i])) {
            parent = parent[path[i]];
          } else {
            break;
          }
        }

        // Create Any Descendants
        for (; i < l; ++i) {
          parent = parent[path[i]] = {};
        }

        // Set the value
        parent[path[i]] = value;
      }
    },
    remove: function (keypath, object) {
      var path = $.objects.keyToPath(keypath);
      if ($.isObject(object) && (path !== null)) {
        var parent = $.objects.parent(path, object);
        var key = (parent !== null) ? path[path.length - 1] : null;
        if ((parent !== null) && (key !== null)) {
          delete parent[key];
          return true;
        }
      }

      return false;
    }
  });

})(jQuery);