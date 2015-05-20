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
    icon: 'terminal',
    classes: {
      left: 'ui tiny image',
      contents: 'middle aligned content',
      image: 'ui tiny image',
      icon: 'inverted circular big',
      header: 'header',
      details: 'description',
      extras: 'extra',
      buttons: 'ui right floated buttons',
      button: 'ui button',
      item: 'item',
      items: 'ui divided items'
    },
    callbacks: {
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
    },
    buttons: {
      'up': {
        order: 1,
        icon: 'arrow up',
        callback: null
      },
      'edit': {
        order: 10,
        icon: 'write',
        callback: null
      },
      'add': {
        order: 20,
        icon: 'plus',
        callback: null
      },
      'delete': {
        order: 30,
        icon: 'erase',
        class: 'negative',
        callback: null
      },
      'down': {
        order: 99,
        icon: 'arrow down',
        callback: null
      }
    }
  };

  function fire_event($container, name, data) {
    console.log("Event [" + name + "] -  Node [" + data + "]");
    $container.trigger('orderedlist.' + name, data);
  }

  function __randomID() {
    return (((1 + Math.random()) * 0x1000000) | 0).toString(16).substring(1).toUpperCase();
  }

  function __append_if_not_null($to, $object) {
    if ($.isset($object)) {
      $to.append($object);
    }

    return $to;
  }

  function __is_promise(value) {
    return $.isObject(value) &&
      value.hasOwnProperty('then') &&
      $.isFunction(value.then);
  }

  function __list_click(event) {
    var $target = $(event.target);
    var $parent = $target.parent();
    if ($parent.data('node.element')) {
      fire_event($(this), 'item-selected', $parent.data('node.element'));
    }

    return false;
  }

  function __initialize_settings(settings) {
    // Make sure we have all the possible settings, with default values
    settings = $.isPlainObject(settings) ? $.extend(true, {}, defaults, settings) : defaults;

    // Prepare Button List
    var callback, order, sequence = [];
    $.each(settings.buttons, function(button, options) {
      callback = options.hasOwnProperty('callback') && $.isFunction(options.callback) ? options.callback : null;
      order = options.hasOwnProperty('order') && $.isNumeric(options.order) ? ((+(options.order)) | 0) : null;
      if (callback && (order !== null)) {
        sequence[order] = button;
      } else {
        delete settings.buttons[button];
      }
    });

    // Convert Sparse Array -> Dense Array with Holes Removed
    settings.buttons.__sequence = sequence.filter(function(value) {
      return true;
    });

    return settings;
  }

  function __build_icon(node, settings) {
    // Get Icon Value for the Node
    var icon = __property_to_string(node, 'icon', settings.icon);

    // Create ICON Element
    var $icon = null;
    if ($.isset(icon)) {
      if (icon.indexOf('/') >= 0) {
        $icon = $('<img>', {
          src: icon
        });
      } else {
        $icon = $('<i>', {
          class: settings.classes.icon + " " + icon + " icon"
        });
      }
    }

    return $icon = $('<div>', {
      name: 'icon',
      class: settings.classes.image
    }).append($icon);
  }

  function __build_header(node, settings) {
    var header = __property_to_string(node, 'title');

    // Create ICON Element
    var $header = null;
    if ($.isset(header)) {
      $header = $('<h3>', {
        name: 'header',
        class: settings.classes.header,
        text: header
      });
    }

    return $header;
  }

  function __build_details(node, settings) {
    var description = __property_to_string(node, 'description');

    // Create ICON Element
    var $details = null;
    if ($.isset(description)) {
      $details = $('<div>', {
        name: 'details',
        class: settings.classes.details,
        text: description
      });
    }

    return $details;
  }

  function __build_button(name, settings, $node) {
    var $button = null;
    var button = settings.buttons[name];
    if ($.isset(button)) {
      $button = $('<div>', {
        name: name,
        class: (settings.classes.button + ' ' + __property_to_string(button, 'class', '')).trim()
      }).append($('<i>', {
        class: (button.icon + " icon").trim()
      }));

      // Add On Click Handler
      $button.click(function(event) {
        // Make sure the On Click is Called in the Context of the Ordered List
        return $.proxy(button.callback, $node.closest('div[name="ordered-list"]'))($node, $node.data('item.node'));
      });
    }

    return $button;
  }

  function __build_buttons_bar(node, settings) {
    if (settings.buttons.__sequence.length > 0) {
      return $('<div>', {
        name: 'extras',
        class: settings.classes.extras
      }).append($('<div>', {
        name: 'buttons',
        class: settings.classes.buttons
      }));
    }

    return null;
  }

  function __build_content(node, settings) {
    var $contents = $('<div>', {
      name: 'contents',
      class: settings.classes.contents
    });

    __append_if_not_null($contents, __build_header(node, settings));
    __append_if_not_null($contents, __build_details(node, settings));
    return $contents.append(__build_buttons_bar(node, settings));
  }

  function __build_node(node, settings) {
    var $node = $('<div>', {
      id: 'N:' + node.id,
      class: settings.classes.item
    });

    __append_if_not_null($node, __build_icon(node, settings));
    var $contents = __build_content(node, settings);
    $node.data('item.buttons', $contents.find('div[name="buttons"]'));
    $node.data('item.node', node);
    return $node.append($contents);
  }

  function __build_container(settings) {
    var $container = $('<div>', {
      id: 'OL' + __randomID(),
      class: settings.classes.items
    });

    return $container;
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

  function __add_button_bar($container, $node, exclude_1st, exclude_last) {
    var settings = $container.data('ol.settings');
    var sequence = settings.buttons.__sequence;
    var $buttons = $node.data('item.buttons');
    if ($buttons.length) {
      var i = exclude_1st ? 1 : 0;
      var limit = exclude_last ? sequence.length - 1 : sequence.length;

      for (; i < limit; ++i) {
        $buttons.append(__build_button(sequence[i], settings, $node));
      }
    }
  }

  function __add_button($container, $node, name) {
    var settings = $container.data('ol.settings');
    var $buttons = $node.data('item.buttons');
    var $button = $buttons.find('div[name="' + name + '"]');
    if ($button.length === 0) {
      $button = __build_button(name, settings, $node);
      var position = $.inArray(name, settings.buttons.__sequence);
      var $before = null;
      if (position >= 0) {
        if (position === 0) {
          $buttons.prepend($button);
        } else if (position === (settings.buttons.__sequence.length - 1)) {
          $buttons.append($button);
        } else {
          var current, name, $after = null;
          $.each($buttons.children, function(i, $element) {
            current = $.inArray($element.attr('name'), settings.buttons.__sequence);
            if (current > position) {
              return false;
            } else {
              $after = $element;
            }
          });

          if ($.isset($after)) {
            $after.next($button);
          } else {
            $buttons.prepend($button);
          }
        }
      }
    }
  }

  function __remove_button($container, $node, name) {
    var $buttons = $node.data('item.buttons');
    var $button = $buttons.find('div[name="' + name + '"]');
    if ($button.length === 1) {
      $button.remove();
    }
  }

  function __append_nodes($container, nodes) {
    var settings = $container.data('ol.settings');
    var $list = $container.data('ol.list');

    var $node;
    if (nodes.length) {
      // Are we Adding to an Empty List?
      var exclude;
      var BOL = $list.children().length === 0;
      var $last = BOL ? null : $list.children().last();

      // Append the Nodes to the List
      $.each(nodes, function(i, node) {
        exclude = [];
        if (BOL && (i === 0)) {
          exclude.push('up');
        }
        if (i === (nodes.length - 1)) {
          exclude.push('down');
        }
        $node = __build_node(node, settings);
        __add_button_bar($container, $node, BOL && (i === 0), i === (nodes.length - 1));
        $list.append($node);
      });

      // Make Sure that Node has a 'down' Button
      if ($last !== null) {
        __add_button($container, $last, 'down');
      }
    }

    return true;
  }

  function __add_nodes($container, nodes, $after) {
    var settings = $container.data('ol.settings');
    var $list = $container.data('ol.list');

    var $node;
    if (nodes.length) {
      // Are we Adding to EOL?
      var EOL = $after.next().length === 0;
      var $previous = $after;

      // Add the Nodes to the List after the Specified Node
      $.each(nodes, function(i, node) {
        $node = __build_node(node, settings);
        __add_button_bar($container, $node, false, EOL && (i === (nodes.length - 1)));
        $previous.after($node);
        $previous = $node;
      });

      // Make Sure that Node has a 'down' Button
      __add_button($container, $after, 'down');
    }

    return true;
  }

  function __update_node($container, $node, new_values) {
    var settings = $container.data('ol.settings');
    var old_values = $node.data('item.node');
    var elements = ['id', 'icon', 'title', 'description'];
    var modifications = [];
    var attr;
    for (var i = 0; i < elements.length; ++i) {
      attr = elements[i];
      if (old_values[attr] !== new_values[attr]) {
        modifications.push(attr);
      }
    }

    if (modifications.length) {
      var additions = {};
      var $old_element, $new_element;
      for (i = 0; i < modifications.length; ++i) {
        attr = modifications[i];
        switch (attr) {
          case 'id':
            $new_element = null;
            $node.attr('id', 'N:' + new_values.id);
            break;
          case 'icon':
            $old_element = $node.find('[name="icon"]');
            $new_element = __build_icon(new_values, settings);
            break;
          case 'title':
            $old_element = $node.find('[name="contents"] > [name="title"]');
            $new_element = __build_header(new_values, settings);
            break;
          case 'description':
            $old_element = $node.find('[name="contents"] > [name="details"]');
            $new_element = __build_details(new_values, settings);
        }

        if ($new_element === null) {
          if ((attr !== 'id') && ($old_element.length)) {
            $old_element.remove();
          }
        } else {
          if ($old_element.length) {
            $old_element.replaceWith($new_element);
          } else {
            additions[attr] = $new_element;
          }
        }
      }

      $.each(additions, function(p, $element) {
        var $placeholder;
        if (additions.hasOwnProperty(p)) {
          switch (p) {
            case 'icon':
              $node.prepend($element);
              break;
            case 'title':
              $node.find('[name="contents"]').prepend($element);
              break;
            case 'description':
              $placeholder = $node.find('[name="contents"] > [name="header"]');
              if ($placeholder.length) {
                $placeholder.after($element);
              } else {
                $placeholder = $node.find('[name="contents"] > [name="extras"]');
                if ($placeholder.length) {
                  $placeholder.before($element);
                } else {
                  $node.find('[name="contents"]').append($element);
                }
              }
              break;
            case 'extras':
              $node.find('[name="contents"]').append($element);
              break;
          }
        }
      });

      // Set New Values
      $node.data('item.node', new_values);
    }
  }

  function __update_data($container, $node, data) {
    var $list = $container.data('ol.list');
    if ($list) {
      var settings = $container.data('ol.settings');
      if (settings) {
        var nodes = data;
        var transform = $.objects.get('callbacks.data_to_nodes', settings);
        if ($.isFunction(transform)) {
          nodes = $.proxy(transform, $container)(data);
        }
      }

      return $.isset(nodes) && nodes.length ? __update_node($container, $node, nodes[0]) : false;
    }

    console.log("Invalid System State.");
    return false;
  }

  function __load_data($container, data, $after) {
    var $list = $container.data('ol.list');
    if ($list) {
      var settings = $container.data('ol.settings');
      if (settings) {
        var nodes = data;
        var transform = $.objects.get('callbacks.data_to_nodes', settings);
        if ($.isFunction(transform)) {
          nodes = $.proxy(transform, $container)(data);
        }
      }

      return $.isset($after) && $after.length ?
        __add_nodes($container, nodes, $after) :
        __append_nodes($container, nodes);
    }

    console.log("Invalid System State.");
    return false;
  }

  function lazy_load(promise, $after) {
    var $container = this;
    $container.addClass('loading');
    promise.then(function(response) {
      __load_data($container, response, $after);
      $container.removeClass('loading');
    }, function() {
      console.log("Error Loading Data");
      $container.removeClass('loading');
    });
  }

  function lazy_update($node, promise) {
    var $container = this;
    $container.addClass('loading');
    promise.then(function(response) {
      __update_data($container, response, $node);
      this.removeClass('loading');
    }, function() {
      console.log("Error Loading Data");
      $container.removeClass('loading');
    });
  }

  function node_up($node) {
    // Reposition Node
    var $previous = $node.prev();
    $node.detach();
    $previous.before($node);

    // Make Sure that Previous has an 'up' Button
    __add_button(this, $previous, 'up');
    // Make Sure that Node has a 'down' Button
    __add_button(this, $node, 'down');

    // Do we have a previous node?
    if ($node.prev().length === 0) { // No
      __remove_button(this, $node, 'up');
    }
    // Do we have a next node?
    if ($previous.next().length === 0) { // No
      __remove_button(this, $previous, 'down');
    }
  }

  function node_down($node) {
    // Reposition Node
    var $next = $node.next();
    $node.detach();
    $next.after($node);

    // Make Sure that Next has an 'down' Button
    __add_button(this, $next, 'down');
    // Make Sure that Node has a 'up' Button
    __add_button(this, $node, 'up');

    // Do we now have a next node?
    if ($node.next().length === 0) { // No
      __remove_button(this, $node, 'down');
    }
    // Do we have a previous node?
    if ($next.prev().length === 0) { // No
      __remove_button(this, $next, 'up');
    }
  }

  function node_add(parameter, $after) {
    if (this.data('ol.list')) { // YES
      if ($.isset(parameter)) {
        // Is the Parameter a Function?
        if ($.isFunction(parameter)) { // YES: Call Function
          parameter = $.proxy(parameter, this)();
        }
      }

      if (__is_promise(parameter)) {
        return $.proxy(lazy_load, this)(parameter, $after);
      }

      return __load_data(this, parameter, $after);
    } else {
      console.log("List hasn't been initialized.");
    }

    return false;
  }

  function node_remove($node) {
    var $previous = $node.prev();
    var $next = $node.next();

    // Remove the Current Node
    $node.remove();

    // Is there a Next Node?
    if ($next.length === 0) { // NO
      // Do we have a Previous Node?
      if ($previous.length) { // YES: Remove the DOWN Button
        __remove_button(this, $previous, 'down');
      }
    }

    // Is there a Previous Node?
    if ($previous.length === 0) { // NO
      // Do we have a next Node?
      if ($next.length) { // YES: Remove the UP Button
        __remove_button(this, $next, 'up');
      }
    }
  }

  function node_update($node, data) {
    if (this.data('ol.list')) { // YES
      if ($.isset(data)) {
        // Is the Parameter a Function?
        if ($.isFunction(data)) { // YES: Call Function
          data = $.proxy(data, this)();
        }
      }

      if (__is_promise(data)) {
        return $.proxy(lazy_update, this)($node, data);
      }

      return __update_data(this, $node, data);
    } else {
      console.log("List hasn't been initialized.");
    }

    return false;
  }

  function node_data($node) {
    return $node.data('item.node');
  }

  var commands = {
    'destroy': function() {
      // Do we already have a Grid List in this Spot?
      if (this.data('ol.list')) { // YES: Remove It
        this.remove(this.data('ol.list'));
        this.removeData('ol.list');
      }
    },
    'initialize': function(settings) {
      // Make sure we have all the possible settings, with default values
      settings = __initialize_settings(settings);

      // Has the Grid Already been Initialized?
      var $list = null;
      if (this.data('ol.list')) { // YES
        $list = this.data('ol.list');
        $list.empty();
      } else { // NO
        $list = __build_container(settings);
        this.attr('name', 'ordered-list');
        $list.data('ol.container', this);
        this.data('ol.list', $list);
        this.append($list);

        // Attach onClick Listener
        this.click(__list_click);
      }

      // Build Grid List Container
      if ($list) {
        this.data('ol.settings', settings);
      }
    },
    'clear': function(parameter) {
      // Do we have a container to initialize?
      if (this.data('ol.list')) { // YES
        this.data('ol.list').empty();
      }
    },
    'load': function(parameter) {
      // Do we have a container to initialize?
      if (this.data('ol.list')) { // YES
        if ($.isset(parameter)) {
          // Is the Parameter a Function?
          if ($.isFunction(parameter)) { // YES: Call Function
            parameter = $.proxy(parameter, this)();
          }
        }

        if (!__is_promise(parameter)) {
          var settings = this.data('ol.settings');
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
          return $.proxy(lazy_load, this)(parameter, null);
        }

        return __load_data(this, parameter, null);
      } else {
        console.error("List hasn't been initialized.");
      }
    },
    'node': {
      'up': node_up,
      'down': node_down,
      'add': node_add,
      'remove': node_remove,
      'update': node_update,
      'data': node_data
    }

  };
  /*
   * PLUGIN Function (Create Initialization)
   */
  $.fn.orderedlist = function(command) {
    if (command === undefined) {
      return this.find('div[name="ordered-list"]');
    } else {
      var $container = this.first();
      var args = [];
      if ($container.length) {
        // Is 'command' parameter an object?
        if ($.isPlainObject(command)) { // YES: Then we just want to initialize
          args.push(command);
          command = 'initialize';
        } else { // NO: Is it a function?
          // Build Arguments Array
          $.each(arguments, function(i, v) {
            if (i) {
              args.push(v);
            }
          });
          if ($.isFunction(command)) { // YES: Then the result of the call will be the tree settings
            if (args.length) {
              args = command.apply($container, args);
            } else {
              args = command.call($container);
            }
            if (!$.isArray(args)) {
              args = [args];
            }
            command = 'initialize';
          } else { // NO: It must be a string
            command = $.strings.nullOnEmpty(command);
          }
        }

        // Do we have a command set?
        if ($.isset(command)) { // YES
          // Do we have a handler for the command?
          command = $.objects.get(command, commands);
          if ($.isset(command)) { // YES: Call it
            if (args.length) {
              command.apply($container, args);
            } else {
              command.call($container);
            }
          }
        }
      } else {
        console.error('No Container for Ordered List.');
      }

      return this;
    }

  };
}(jQuery));

/* TODO:
 * 1. On Small Screens the Step Header / Description Appears Below the Icon
 *    Potential Solution:
 *    1. Maintain Format (with icon position intact, just decrease size of
 *    content div).
 * 2. The Button Bar contains to appear on the right side.
 *    Potential Solution:;
 *    1. It might be better if it appeared on the right?
 *    2. Or, centered?
 * 3. When a button is missing, the corresponding buttons are not aligned between
 *    steps (example, last line is missing button down, right most button,
 *    this makes it so that the up button is below the create button). 
 *    Potential solution: 
 *    1. Maybe instead of not creating the button we simply
 *    hide it, so that it occupies the space, but is not visible.
 *    2. Create the button, but, disable it.
 */
/* IDEA: For Node Actions (up,down,delete,etc.) allow promises. How?
 * 
 * Example Code for Node Up Button - Click Handler
 function move_step_up($node, node) {
 var $list = this;
 // Load the Test Steps
 testcenter.services.call(['step', 'move', 'up'], [node.test, node.id], null, {
 call_ok: function(step) {
 $list.orderedlist('node.up', $node);
 },
 call_nok: function(code, message) {
 console.log('ERROR: Failed to Move Step [' + code + ':' + message + ']');
 }
 });
 return true;
 }
 
 It would have been nice to just simply do:
 function move_step_up($node, node) {
 return testcenter.services.call(['step', 'move', 'up'], [node.test, node.id]);
 }
 
 But this would require that, the button handler be able to accept promises and
 perform the correct action. Something like:
 
 if(is_promise(return)) {
 return.then(function(response) {
 commands.node.up($node);
 }, function(error) {
 console.error(error);
 });
 }
 
 * 
 */
/* EXAMPLE CODE:
 function load_steps(test_id) {
 return testcenter.services.call(['steps', 'list'], test_id);
 }
 
 function to_nodes(response) {
 var response = response['return'];
 var nodes = [];
 var defaults = {
 };
 
 switch (response.__type) {
 case 'entity-set':
 var entities = response.entities;
 var key_field = response.__key;
 var display_field = response.__display;
 // Build Nodes
 $.each(entities, function(i, entity) {
 var node = $.extend(true, {}, defaults, {
 id: entity.sequence,
 key: entity[key_field],
 title: entity[display_field],
 description: entity['description']
 });
 nodes.push(node);
 });
 break;
 case 'entity':
 var node = $.extend(true, {}, defaults, {
 id: response.sequence,
 key: response[key_field],
 title: response[display_field],
 description: response['description']
 });
 nodes.push(node);
 break;
 default:
 console.log('Invalid Response');
 }
 return nodes;
 }
 
 function do_nothing($node, node) {
 console.log('Clicked ['+$node.attr('id')+'] - Step ['+node.title+']');
 }
 
 s= {
 callbacks: {
 loader: load_steps, 
 data_to_nodes: to_nodes
 },
 buttons: {
 'up': {
 callback: do_nothing
 },
 'edit': {
 callback: do_nothing
 },
 'add': {
 callback: do_nothing
 },
 'delete': {
 callback: do_nothing
 },
 'down': {
 callback: do_nothing
 }
 }
 }
 
 $('#test_steps').orderedlist(s);
 $('#test_steps').orderedlist('load', 1);
 
 $('#test_steps').orderedlist('clear');
 */