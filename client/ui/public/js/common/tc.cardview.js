/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */
; // For Minimification (If somebody forgot semicolon in another script file)
(function ($, undefined) {
  /*
   * LOCAL SCOPE
   */
  var defaults = {
    detach_extras: true,
    classes: {
      container: null,
      card: null,
      content: null,
      header: null,
      description: null,
      extras: null
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
      data_to_cards: null
    }
  };

  function __randomID() {
    return (((1 + Math.random()) * 0x1000000) | 0).toString(16).substring(1).toUpperCase();
  }

  function __is_promise(value) {
    return $.isObject(value) &&
      value.hasOwnProperty('then') &&
      $.isFunction(value.then);
  }

  function __initialize_settings(settings) {
    // Make sure we have all the possible settings, with default values
    settings = $.isPlainObject(settings) ? $.extend(true, {}, defaults, settings) : defaults;
    return settings;
  }

  function __append_classes(base, extra) {
    extra = $.strings.nullOnEmpty(extra);
    return $.isset(extra) ? base + ' ' + extra : base;
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

  function fire_event($container, name, data) {
    console.log("Event [" + name + "] -  Node [" + data + "]");
    $container.trigger(name + '.cardview', data);
  }

  /*
   * Event Handlers
   */
  function __view_clicked(event) {
    var $target = $(event.target);

    // Find the Nearest Card
    var $parent = $target;
    while (($parent.get() !== this) && !$parent.hasClass('card')) {
      $parent = $parent.parent();
    }

    if ($parent.hasClass('card')) {
      fire_event($(this), 'card-clicked', $parent);
    } else {
      fire_event($(this), 'view-clicked');
    }

    return false;
  }

  /*
   * Builder for Plugin Components
   */
  function __build_container(settings) {
    var $container = $('<div>', {
      id: 'CV' + __randomID(),
      class: __append_classes('ui', settings.classes.container)
    });

    return $container;
  }

  function __build_header(card, settings) {
    var title = $.strings.nullOnEmpty(card.title);
    if ($.isset(title)) {
      return $('<div>', {
        text: title,
        class: __append_classes('header', settings.classes.header)
      });
    }

    return null;
  }

  function __build_description(card, settings) {
    var $description = null;

    // Is DETAILS in TEXT Format?
    var text = __property_to_string(card, 'text');
    if ($.isset(text)) { // YES
      $description = $('<div>', {
        name: 'title',
        class: __append_classes('description', settings.classes.description),
        text: text
      });
    } else { // NO: Try HTML
      // Is DETAILS in HTML Format?
      var html = __property_to_string(card, 'html');
      if ($.isset(html)) {
        $description = $('<div>', {
          name: 'title',
          class: __append_classes('description', settings.classes.description),
          html: $('<div/>').html(html).text()
        });
      }
    }

    return $description;
  }

  function __build_contents(card, settings) {
    var $header = __build_header(card, settings);
    var $description = __build_description(card, settings);
    if ($.isset($header) || $.isset($description)) {
      var $contents = $('<div>', {
        class: __append_classes('content', settings.classes.content)
      });

      return $contents.append($header).
        data('cv.header', $header).
        append($description).
        data('cv.description', $description);
    }

    return null;
  }

  function __build_extras(settings) {
    return $('<div>', {
      class: __append_classes('extra', settings.classes.extra)
    });
  }

  function __build_card(card, settings) {
    var $contents = __build_contents(card, settings);

    if ($.isset($contents)) {
      var $card = $('<div>', {
        id: 'C' + card.id,
        class: __append_classes('ui card cardview', settings.classes.card)
      });

      return $card.append($contents).
        data('cv.contents', $contents).
        data('cv.data', card);
    }

    return null;
  }

  function __append_cards($container, cards) {
    var settings = $container.data('cv.settings');

    var $card;
    if (cards.length) {
      // Are we Adding to an Empty View?
      var exclude;
      var BOL = $container.children().length === 0;
      var $last = BOL ? null : $container.children().last();

      // Append the Nodes to the List
      $.each(cards, function (i, card) {
        $card = __build_card(card, settings);
        $container.append($card);
      });
    }

    return true;
  }

  function __add_cards($container, nodes, $after) {
    var settings = $container.data('cv.settings');

    var $node;
    if (nodes.length) {
      // Are we Adding to EOL?
      var EOL = $after.next().length === 0;
      var $previous = $after;

      // Add the Nodes to the List after the Specified Node
      $.each(nodes, function (i, node) {
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

  function __update_card($container, $card, new_values) {
    var settings = $container.data('cv.settings');
    var current_values = $card.data('cv.data');

    // Have we changed the Cards ID?
    if (current_values.id !== new_values.id) { // YES
      $card.attr('id', 'C:' + new_values.id);
    }

    // Discover if Any Changes to Contents
    var elements = ['title', 'description'];
    var changed = false, attr;
    for (var i = 0; i < elements.length; ++i) {
      attr = elements[i];
      if (current_values[attr] !== new_values[attr]) {
        changed = true;
        break;
      }
    }

    // Did we change the Contents?
    if (changed) { // YES
      // Rebuild Contents
      var $contents = $card.data('cv.contents');
      var $new_contents = __build_contents(new_values, settings);

      // Do we have a new Contents?
      if ($.isset($new_contents)) { // YES
        if ($card.data('cv.contents')) {
          $card.data('cv.contents').replaceWith($new_contents);
        } else {
          $card.prepend($new_contents);
        }
        $card.data('cv.contents', $new_contents);
      } else { // NO
        if ($card.data('cv.contents')) {
          $card.data('cv.contents').remove();
          $card.removeData('cv.contents');
        }
      }
    }

    // Save New Card Data
    $card.data('cv.data', new_values);
  }

  function __update_data($container, $card, data) {
    var settings = $container.data('ol.settings');
    if (settings) {
      var cards = data;
      var transform = $.objects.get('callbacks.data_to_cards', settings);
      if ($.isFunction(transform)) {
        cards = $.proxy(transform, $container)(data);
      }
    }

    return $.isset(cards) && cards.length ? __update_card($container, $card, cards[0]) : false;
  }

  function __load_data($container, data, $after) {
    var settings = $container.data('cv.settings');
    if (settings) {
      var cards = data;
      var transform = $.objects.get('callbacks.data_to_cards', settings);
      if ($.isFunction(transform)) {
        cards = $.proxy(transform, $container)(data);
      }
    }

    if ($.isset($after) && $after.length) {
      __add_cards($container, cards, $after);
    } else {
      __append_cards($container, cards);
    }

    fire_event($container, 'loaded');
  }

  function __to_card($card) {
    if ($.isObject($card)) {
      if ($card instanceof HTMLElement) { // YES: Convert it jQuery
        $card = $($card);
      }

      if (($card instanceof $) && $card.hasClass('card')) {
        return $card;
      }
    }
    throw 'Not a Valid Card Object'
  }

  /*
   * CARDVIEW Container SCOPE (this === jQuery Object for the CardView Container)
   */
  function lazy_load(promise, $after) {
    var $container = this;
    $container.addClass('loading');
    promise.then(function (response) {
      __load_data($container, response, $after);
      $container.removeClass('loading');
    }, function () {
      console.log("Error Loading Data");
      $container.removeClass('loading');
    });
  }

  function lazy_update($card, promise) {
    var $container = this;
    $container.addClass('loading');
    promise.then(function (response) {
      __update_data($container, response, $card);
      this.removeClass('loading');
    }, function () {
      console.log("Error Loading Data");
      $container.removeClass('loading');
    });
  }

  /* 
   * CARD Commands
   */
  function clear_current_card() {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      // Get Current Card
      var $current = this.find('.card.current');
      // Do we have a Current Card?
      if ($current.length) { // YES
        $current.removeClass('current');
        fire_event($(this), 'clear-current', $current);
      }
      return this;
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function get_current_card() {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      var $current = this.find('.card.current');
      return $current.length ? $current.first() : $();
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function set_current_card($card) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      // Make Sure we have a Card Object
      $card = __to_card($card);
      // Get Current Card
      var $current = this.find('.card.current');
      // Are the Current and New Cards the Same?      
      if ($current.length && ($current.get(0) !== $card.get(0))) { // NO: Change Current Card
        // Clear Current Card
        $current.removeClass('current');
        fire_event($(this), 'clear-current', $current);
      }
      // Make this Card Current
      $card.addClass('current');
      fire_event($(this), 'set-current', $card);
      return this;
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function set_current_card_byid(id) {
    var $card = $.proxy(card_byid, this)(id);
    $.proxy(set_current_card, this)($card);
    return this;
  }

  function card_add(parameter, $after) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      if ($.isset(parameter)) {
        // Is the Parameter a Function?
        if ($.isFunction(parameter)) { // YES: Call Function
          parameter = $.proxy(parameter, this)();
        }
      }

      if (__is_promise(parameter)) {
        return $.proxy(lazy_load, this)(parameter, $after);
      }

      __load_data(this, parameter, $after);
      return this;
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_remove($card) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      // Make Sure we have a Card Object
      $card = __to_card($card);
      // Remove the Card
      $card.remove();
      return this;
    }// ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_update($card, data) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      // Make Sure we have a Card Object
      $card = __to_card($card);

      if ($.isset(data)) {
        // Is the Parameter a Function?
        if ($.isFunction(data)) { // YES: Call Function
          data = $.proxy(data, this)();
        }
      }

      if (__is_promise(data)) {
        return $.proxy(lazy_update, this)($card, data);
      }

      __update_data(this, $card, data);
      return this;
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_data($card) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      // Make Sure we have a Card Object
      $card = __to_card($card);

      return $card.data('cv.data');
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_first() {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      return this.find('.card').first();
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_last() {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      return this.find('.card').last();
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_next($card) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      $card = $.isset($card) ? __to_card($card) : this.find('.card.current').first();
      return $card.next();
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_previous($card) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      $card = $.isset($card) ? __to_card($card) : this.find('.card.current').first();
      return $card.prev();
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_byid(id) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      return this.find('.card').filter(function (i, el) {
        return $(this).data('cv.data').id === id;
      });
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_extras_set($card, $contents) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      $card = $.isset($card) ? __to_card($card) : this.find('.card.current').first();

      // Do we have a Card?
      if ($card.length) { // YES
        var $extras = $card.data('cv.extras');
        if ($.isset($extras)) {
          // Do we want the Extras Contents Detached (i.e. They Wont be Rebuilt)?
          if (this.data('cv.settings').detach_extras) { // YES
            $extras.data('cv.extra.contents').detach();
          } else { // NO: They will just be simply rebuilt
            $extras.empty();
          }
        } else {
          $extras = __build_extras(this.data('cv.settings'));
          $card.data('cv.extras', $extras);
          $card.append($extras);
        }

        $extras.data('cv.extra.contents', $contents);
        $extras.append($contents);
      }

      return this;
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  function card_extras_remove($card) {
    // Are we dealing with Card View?
    if (this.data('cv.settings')) { // YES
      $card = $.isset($card) ? __to_card($card) : this.find('.card.current').first();

      // Do we have a Card?
      if ($card.length) { // YES
        var $extras = $card.data('cv.extras');
        if ($.isset($extras)) {
          // Do we want the Extras Contents Detached (i.e. They Wont be Rebuilt)?
          if (this.data('cv.settings').detach_extras) { // YES
            $extras.data('cv.extra.contents').detach();
          }
          $extras.remove();
          $card.removeData('cv.extras');
        }
      }

      return this;
    } // ELSE: NO
    throw "Not a Valid Card View";
  }

  /*
   * COMMANDS (Handlers)
   */
  var commands = {
    'destroy': function () {
      // Do we already have a Card View in this Spot?
      if (this.data('cv.settings')) { // YES: Remove It
        this.empty();
        this.removeData('cv.settings');
      }

      return this;
    },
    'initialize': function (settings) {
      // Make sure we have all the possible settings, with default values
      settings = __initialize_settings(settings);

      // Has the Card View Already been Initialized?
      if (this.data('cv.settings')) { // YES
        this.empty();
      } else { // NO
        // Attach onClick Listener
        this.click(__view_clicked);
      }

      // Save Settings for Container
      return this.data('cv.settings', settings);
    },
    'clear': function (parameter) {
      // Do we have a container to initialize?
      if (this.data('cv.settings')) { // YES
        this.empty();
      }

      return this;
    },
    'load': function (parameter) {
      // Do we have a container to initialize?
      if (this.data('cv.settings')) { // YES
        if ($.isset(parameter)) {
          // Is the Parameter a Function?
          if ($.isFunction(parameter)) { // YES: Call Function
            parameter = $.proxy(parameter, this)();
          }
        }

        if (!__is_promise(parameter)) {
          var settings = this.data('cv.settings');
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

        __load_data(this, parameter, null);
      } else {
        console.error("Card View hasn't been initialized.");
      }

      return this;
    },
    'card': {
      'current': {
        'get': get_current_card,
        'set': set_current_card,
        'setByID': set_current_card_byid,
        'clear': clear_current_card
      },
      'extras': {
        'set': card_extras_set,
        'remove': card_extras_remove
      },
      'add': card_add,
      'remove': card_remove,
      'update': card_update,
      'data': card_data,
      'first': card_first,
      'last': card_last,
      'next': card_next,
      'previous': card_previous,
      'byID': card_byid
    }
  };

  /*
   * PLUGIN: (JQuery Object Method) Card View Plugin Interface
   */
  $.fn.cardview = function (command) {
    if (command === undefined) {
      return this.find('div[name="cardview"]');
    }

    var $container = this.first();
    var args = [];
    if ($container.length) {
      // Is 'command' parameter an object?
      if ($.isPlainObject(command)) { // YES: Then we just want to initialize
        args.push(command);
        command = 'initialize';
      } else { // NO: Is it a function?
        // Build Arguments Array
        $.each(arguments, function (i, v) {
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
            return command.apply($container, args);
          } else {
            return command.call($container);
          }
        }
      }

      throw 'Command Invalid';
    }

    throw 'No Container for Card View';
  };

  /*
   * PLUGIN: (STATIC FUNCTION) Build Card View(s) from JQuery Selector
   */
  $.cardview = function (selector, settings) {
    var $container = null;
    // Is the Selector a String?
    if ($.isString(selector)) { // YES: Use JQuery to Extract the Objects
      $container = $(selector);
    } else // NO: Is Selector a JQuery Object?
    if (selector instanceof $) { // YES: Just Initialize it then
      $container = selector;
    }

    if ($.isset($container) && $container.length) {
      // Do we have an extra classes to add to the container?
      var extra = $.strings.nullOnEmpty($.objects.get('classes.container', settings, defaults.classes.container));
      if ($.isset(extra)) {
        $container.addClass(extra);
      }

      // Initialize Container
      return $container.cardview('initialize', settings);
    }

    console.warn('Card View: Selector Invalid or Empty');
  };

}(jQuery));

/* Test Code
 * $('#tests_cards').on('card-clicked.cardview', function(event, $card) { $('#tests_cards').cardview('card.current.clear').cardview('card.current.set', $card); });
 */