/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */
; // For Minimification (If somebody forgot semicolon in another script file)
(function($, undefined) {
  /*
   * LOCAL SCOPE
   */
  var defaults = {
    // Number of Columns in the Grid
    columns: 4,
    classes: {
      grid: "ui four column grid middle aligned internally celled"
    },
    callbacks: {
      id_encoder: null,
      id_decoder: null,
      /* Loader Function for Nodes
       * definition - function(parameter)
       * -- parameter: is single parameter of any type, can be used to pass
       * information required by the load function (can also be undefined) in
       * which case the internal function should either load with no parameters
       * or produce no results
       * -- return: 
       * (null or undefined) - no items to load
       * (array) - array of nodes to use
       * (jQuery promise) - for any sort of ajax loading
       */
      loader: null, // Loader Nodes from Data-Source,
      /* If a jQuery Promise is used as a loader, then this function can be used
       * to convert the result of the jQuery Promise to an array of nodes, in the
       * correct format.
       */
      data_to_nodes: null,
      menu_items: null,
      menu_handlers: null
    }
  };

  function fire_event($container, name, data) {
    console.log("Event [" + name + "] -  Node [" + data + "]");
    $container.trigger('gridlist.' + name, data);
  }

  function __randomID() {
    return (((1 + Math.random()) * 0x1000000) | 0).toString(16).substring(1).toUpperCase();
  }

  function __build_container(settings) {
    var $grid = $('<div>', {
      id: 'GLC' + __randomID(),
      class: $.objects.get('classes.grid', settings, 'ui four column grid middle aligned internally celled')
    });

    return $grid;
  }

  function __property_to_string(object, property, def) {
    var value = def;
    if (object.hasOwnProperty(property)) {
      value = object[property];
    }

    if ($.isset(value)) {
      if ($.isString(value)) {
        value = $.strings.nullOnEmpty(value);
      } else if ($.isFunction(value)) {
        value = $.strings.nullOnEmpty(value(object));
      } else {
        value = $.strings.nullOnEmpty(value.toString());
      }
    }

    return value;
  }

  function __create_row(settings) {
    return $('<div>', {
      class: $.objects.get('classes.row', settings, 'centered row')
    });
  }

  /* NOTES:
   * NODE Object Format {
   *   id: mixed (must be a unique and convertible to a string, so as to use as part of the DOM element ID)
   *   icon: mixed (null or undefined - no icon will be displayed
   *                string - will use semantic font awesome definition, or
   *                url - will load a specific url image)
   *   class: extra classes to use on the contaiing div
   *   (one-of: in case both defined, text will be used)
   *   text: (non-html) text to display
   *   html: (html) text to display
   * }
   */
  function __create_node(node, settings) {
    var $element = null;

    // Extract the Node ID
    var id = __property_to_string(node, 'id');
    if ($.isset(id)) {
      var id = 'GN:' + id;
      var icon = __property_to_string(node, 'icon', settings.icon);
      var text = __property_to_string(node, 'text');
      var html = $.isset(text) ? null : __property_to_string(node, 'html');

      // Create ICON Element
      var $icon = null;
      if ($.isset(icon)) {
        if (icon.indexOf('/') >= 0) {
          $icon = $('<img/>', {
            class: "ui small image",
            src: icon
          });
        } else {
          $icon = $('<i/>', {
            class: icon + " icon"
          });
        }
      }

      // Create Display Element
      var $display = null;
      if ((text !== null) || (html !== null)) {
        $display = $('<div>', {
          text: text !== null ? text : html
        });
      }

      // Create Node Element
      if (($icon !== null) || ($display !== null)) {
        $element = $('<div/>', {
          id: id,
          class: __property_to_string(node, 'class', $.objects.get('classes.node', settings, 'aligned column'))
        });

        if ($icon) {
          $element.append($icon);
        }

        if ($display) {
          $element.append($display);
        }

        $element.data('node.element', node);
      }
    }

    return $element;
  }

  function __load_nodes($grid, nodes, settings) {
    var columns = settings.columns,
      $row = $grid.find('.row').last(),
      column = $row.children().length;

    // Append the Nodes to the Grid
    $.each(nodes, function(i, node) {
      // Starting a New Row?
      var is_new_row = false;
      if (($row.length === 0) || ((column / columns) >= 1)) {
        $row = __create_row(settings);
        is_new_row = true;
        column = 0;
      }

      // Create and Append the Node
      var $node = __create_node(node, settings);
      if ($.isset($node)) {
        $row.append($node);
        if (is_new_row) {
          $grid.append($row);
        }
        column++;
      }
    });

    return true;
  }

  function __load_data($container, data) {
    var $grid = $container.data('gl.grid');
    if ($grid) {
      var settings = $container.data('gl.settings');
      if (settings) {
        var nodes = data;
        var transform = $.objects.get('callbacks.data_to_nodes', settings);
        if ($.isFunction(transform)) {
          nodes = $.proxy(transform, this)(data);
        }

        var id_encoder = $.objects.get('callbacks.id_encoder', settings);
        if ($.isFunction(id_encoder)) {
          $.each(nodes, function(i, node) {
            node.key = id_encoder(node);
          });
        }
      }

      return __load_nodes($grid, nodes, settings);
    }

    console.log("Inivalid System State.");
    return false;
  }

  function __lazy_load($container, promise) {
    $container.addClass('loading');
    promise.then(function(response) {
      __load_data($container, response);
      $container.removeClass('loading');
    }, function() {
      console.log("Error Loading Data");
      $container.removeClass('loading');
    });
  }

  function __is_promise(value) {
    return $.isObject(value) &&
      value.hasOwnProperty('then') &&
      $.isFunction(value.then);
  }

  function __grid_click(event) {
    var $target = $(event.target);
    var $parent = $target.parent();
    if ($parent.data('node.element')) {
      fire_event($(this), 'item-selected', $parent.data('node.element'));
    }

    return false;
  }

  function __context_menu(event) {
    var $grid = event.data;
    if ($grid.length === 0) {
      console.log("Grid Container Missing.");
      return false;
    }

    var settings = $grid.data('gl.container').data('gl.settings');
    var menu_items = $.objects.get('callbacks.menu_items', settings);
    if (!$.isset(menu_items)) {
      console.log("No Context Menu");
      return;
    }

    if ($.isFunction(menu_items)) {
      var $node = $(event.target).parent();
      $node = $node.length && $node.data('node.element') ? $node : null;
      menu_items = $.proxy(menu_items, this)($node);
    }

    if (!$.isPlainObject(menu_items)) {
      console.log("Invalid Context Menu.");
      return;
    }

    // Remove the Previous Context Menu
    $.contextMenu('destroy', selector);

    // Create Selector for Grid List
    var selector = "div#" + $grid.attr('id');

    // Create New Context Menu
    var menu_handlers = $.objects.get('callbacks.menu_handlers', settings);
    menu_handlers = $.isFunction(menu_handlers) ? menu_handlers : null;

    $.contextMenu({
      selector: selector,
      build: function($trigger, event) {
        return {
          reposition: false,
          items: menu_items,
          callback: function(action, options) {
            var item = options.items[action];
            var handler = item.hasOwnProperty('callback') ? item.callback : menu_handlers;
            if ($.isFunction(handler)) {
              // Menu Handler is Called in the Context of the Grid List
              $.proxy(handler, $grid.data('gl.container'))($node, action, options);
            }
          }
        };
      }
    });

    return true;
  }

  var commands = {
    'destroy': function() {
      // 1st Item in the Selector is the Container
      var $container = this.first();
      // Do we already have a Grid List in this Spot?
      if ($container.data('gl.grid')) { // YES: Remove It
        $container.remove('gl.grid');
        $container.removeData('gl.grid');
      }
    },
    'initialize': function(settings) {
      // Do we have a container to initialize?
      var $container = this.first();
      if ($container.length) { // YES
        // Make sure we have all the possible settings, with default values
        settings = $.isPlainObject(settings) ? $.extend(true, {}, defaults, settings) : defaults;

        // Has the Grid Already been Initialized?
        var $grid = null;
        if ($container.data('gl.grid')) { // YES
          $grid = $container.data('gl.grid');
          $grid.empty();
        } else { // NO
          $grid = __build_container(settings);
          $container.attr('name', 'grid-list');
          $grid.data('gl.container', $container);
          $container.data('gl.grid', $grid);
          $container.append($grid);

          // Attach onClick Listener
          $container.click(__grid_click);

          // Attach ContextMenu Listener
          $grid.on('contextmenu', null, $grid, __context_menu);
        }

        // Build Grid List Container
        if ($grid) {
          $container.data('gl.settings', settings);
        }
      }
    },
    'clear': function(parameter) {
      // Do we have a container to initialize?
      var $container = this.first();
      if ($container.length) { // YES
        if ($container.data('gl.grid')) { // YES
          $container.data('gl.grid').empty();
        }
      }
    },
    'add': function(parameter) {
      // Do we have a container to initialize?
      var $container = this.first();
      if ($container.length) { // YES
        if ($container.data('gl.grid')) { // YES
          if ($.isset(parameter)) {
            // Is the Parameter a Function?
            if ($.isFunction(parameter)) { // YES: Call Function
              parameter = $.proxy(parameter, this)();
            }
          }

          if (__is_promise(parameter)) {
            __lazy_load($container, parameter);
            return;
          }

          if ($.isArray(parameter)) {
            __load_data($container, parameter);
          }
        } else {
          console.log("Grid hasn't been initialized.");
        }
      } else {
        console.log("Nothing to load nodes into.");
      }
    },
    'load': function(parameter) {
      // Do we have a container to initialize?
      var $container = this.first();
      if ($container.length) { // YES
        if ($container.data('gl.grid')) { // YES
          if ($.isset(parameter)) {
            // Is the Parameter a Function?
            if ($.isFunction(parameter)) { // YES: Call Function
              parameter = $.proxy(parameter, this)();
            }
          }

          if (!__is_promise(parameter)) {
            var settings = $container.data('gl.settings');
            var loader = $.objects.get('callbacks.loader', settings);
            if ($.isFunction(loader)) {
              parameter = $.proxy(loader, this)(parameter);
            } else
            if ($.isArray(loader)) {
              parameter = loader;
            } else {
              parameter = null;
            }
          }

          if (__is_promise(parameter)) {
            __lazy_load($container, parameter);
            return;
          }

          if ($.isArray(parameter)) {
            __load_data($container, parameter);
          }
        } else {
          console.log("Grid hasn't been initialized.");
        }
      } else {
        console.log("Nothing to load nodes into.");
      }
    }
  };
  /*
   * PLUGIN Function (Create Initialization)
   */
  $.fn.gridlist = function(command, parameters) {
    if (command === undefined) {
      return this.find('div[name="grid-list"]');
    } else {
      // Is 'command' parameter an object?
      if ($.isPlainObject(command)) { // YES: Then we just want to initialize
        parameters = command;
        command = 'initialize';
      } else // NO: Is it a function?
      if ($.isFunction(command)) { // YES: Then the result of the call will be the tree settings
        parameters = command(parameters);
        command = 'initialize';
      } else { // NO: It must be a string
        command = $.strings.nullOnEmpty(command);
      }

      // Do we have a command set?
      if ($.isset(command)) { // YES
        // Do we have a handler for the command?
        command = $.objects.get(command, commands);
        if ($.isset(command)) { // YES: Call it
          $.proxy(command, this)(parameters);
        }
      }

      return this;
    }
  };
}(jQuery));

/* EXAMPLE USE
 $gl = $('#items_2');
 
 function load_tests(folder_id) {
 // Load the Sets into the Folder
 return testcenter.services.call(['folders', 'list'], [folder_id, 'T']);
 }
 
 function entities_to_node(response) {
 var entity_set = response['return'];
 var nodes = [];
 var defaults = {
 icon: 'file'
 };
 var entities = entity_set.entities;
 var key_field = entity_set.__key;
 var display_field = entity_set.__display;
 // Build Nodes
 $.each(entities, function(i, entity) {
 var node = $.extend(true, {}, defaults, {
 id: entity[key_field],
 text: entity[display_field]
 });
 nodes.push(node);
 });
 return nodes;
 }
 
 s = { 
 columns: 5, 
 callbacks : {
 loader: load_tests,
 data_to_nodes: entities_to_node    
 }
 };
 
 $gl.gridlist(s);
 $gl.gridlist('load',4);
 */