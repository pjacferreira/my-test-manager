/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */
; // For Minimification (If somebody forgot semicolon in another script file)
(function($) {
  /*
   * LOCAL SCOPE
   */
  // Defaults for Folder Tree
  var defaults = {
    classes: {
      header: "ui centered header",
      tree: null
    },
    root: {
      id: null,
      title: null
    },
    callbacks: {
      id_encoder: function(id) {
        return 'F:' + id;
      },
      id_decoder: function(encoded_id) {
        var elements = $.isString(encoded_id) ? encoded_id.split(':') : null;
        return (elements !== null) && (elements.length > 1) ? elements[1] : null;
      },
      data_url: null,
      data_to_nodes: null,
      menu: null,
      menu_handlers: null
    }
  };

  function getContainer(DOMElement) {
    var $target = $(DOMElement);
    var $container = $target.closest('div[name="folder-tree"]');
    return $container;
  }

  function data_postprocess(event, data) {
    // Get Navigator Parent from the Element
    var $container = getContainer(event.target);
    var settings = $container.data('fv.settings');
    var id_encoder = $.objects.get('callbacks.id_encoder', settings);
    var transform = $.objects.get('callbacks.data_to_nodes', settings);
    var nodes = null;
    if (transform && id_encoder) {
      nodes = transform(data.response);
      $.each(nodes, function(i, node) {
        node.folder = true;
        node.lazy = true;
        node.key = id_encoder(node.id);
      });
    }
    data.result = nodes;
  }

  function lazy_loader(event, data) {
    data.result = null;

    // Get Navigator Parent from the Element
    var $container = getContainer(event.target);
    if ($container) {
      var settings = $container.data('fv.settings');
      var decode_id = $.objects.get('callbacks.id_decoder', settings);
      var build_url = $.objects.get('callbacks.data_url', settings);
      if (decode_id && build_url) {
        var id = decode_id(data.node.key);
        if (id) {
          var url = build_url(id);
          data.result = {
            url: build_url(id),
            cache: false
          };
        }
      }
    }
  }

  function fire_event($navigator, name, data) {
    console.log("Event [" + name + "] -  Node [" + data + "]");
    $navigator.trigger('folder-view.' + name, data);
  }

  function folder_id($container, node_key) {
    var settings = $container.data('fv.settings');
    var decode_id = $.objects.get('callbacks.id_decoder', settings);
    return decode_id !== null ? decode_id(node_key) : null;
  }

  function handler_node_selected(event, data) {
    console.log("Event [" + event.type + "] -  Node [" + data.node.key + ":" + data.node.title + "]");
    var $container = getContainer(event.target);
    var id = folder_id($container, data.node.key);
    if (id !== null) {
      $container.data('fv.node.selected', data.node);
      fire_event($container, 'folder-selected', id);
    }
  }

  var commands = {
    'destroy': function() {
      // 1st Item in the Selector is the Container
      var $container = this.first();
      // Do we already have a Fancy Tree in this Spot?
      if ($container.data('fv.tree')) { // YES
        $container.data('fv.tree').fancytree('destroy');
        $container.removeData('fv.tree');
      }
    },
    'initialize': function(settings) {
      // Do we have a container to initialize?
      var $container = this.first();
      if ($container.length) { // YES
        // Remove Any Existing FancyTree 1st
        $.proxy(commands.destroy, this)();

        // Make sure we have all the possible settings, with default values
        settings = $.isPlainObject(settings) ? $.extend(true, {}, defaults, settings) : defaults;

        // Clear and Initialize the Container
        $container.attr('name', 'folder-tree');
        $container.data('fv.settings', settings);

        // Create Fancy Tree
        var id = $.objects.get('root.id', settings);
        var title = $.objects.get('root.title', settings, 'ROOT');
        var id_encoder = $.objects.get('callbacks.id_encoder', settings);
        var menu_builder = $.objects.get('callbacks.menu', settings);
        var menu_handler = $.objects.get('callbacks.menu_handlers', settings);

        id = id_encoder !== null ? id_encoder(id) : null;
        if (id !== null) {
          var tree_settings = {
            source: [{title: title, key: id, folder: true, lazy: true, expandend: false}],
            lazyLoad: lazy_loader,
            selectMode: 1,
            postProcess: data_postprocess,
            activate: handler_node_selected,
          }

          if (menu_builder !== null) {
            tree_settings.extensions = ['contextMenu'];
            tree_settings.contextMenu = {
              menu: menu_builder,
              actions: menu_handler
            };
          }

          // Create Fanncy Tree
          var $tree = $('<div>', {
            name: 'tree-items',
            class: $.objects.get('classes.tree', settings, '')
          });
          $container.data('fv.tree', $tree);
          $tree.fancytree(tree_settings);

          $container.append($tree);
        }
      }
    }

  };

  /*
   * PLUGIN Function (Create Initialization)
   */
  $.fn.folderview = function(command, parameters) {
    if (command === undefined) {
      return this.find('div[name="folder-tree"]');
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
    }
  };
}(jQuery));

/* IDEA
 * Encompass the Complete Folder Tree System (including Context Menu Here).
 * 
 * How?
 * 
 * By Defining the set of actions that can be performed, like:
 * - Create
 * - Delete
 * - Rename
 * - Move (use-able for drag and drop)
 * - Properties
 * - Load
 * 
 * If an action is not defined, then it is not available.
 * Maybe even go to the point of having dynamic actions (i.e. actions that
 * were not originally planned for)
 * 
 * Dynamic action would require that we supply such things as
 * Title (for Context Menu)
 * Keyboard Shortcut (Optional)
 * 
 * We could define actions as things that return jQuery promises, so that we
 * could defer any actual processing to later.
 * Example:
 * Create Action might work as following:
 * 1. Display a Form (with a Save / Cancel Buttons), to capture the 
 * requirements needed for creating a folder.
 * 2. The use could then
 * i) Cancel the form - folder creation is aborted
 * ii) User Accepts the Form - step 3
 * 3. Some Ajax function is used to create the form in the back-end.
 * 4. Action returns the node required to create a new node.
 * 5. Folder Tree creates a new node in the Tree View for the New Folder.
 * 
 * Implementation:
 * - The actual create function, could be passed a node (i.e. the parent under
 * which the folder would be created)
 * - The function would create a jQuery promise and return the promise back to the
 * Folder Tree View
 * - Folder Tree View would await the resolution of the promise.
 * - When the promise is resolved the Folder Tree could create or abort the
 * Folder Creation.
 * 
 * Actions could be defined simple as property map.
 * For built in actions (like create/delete/rename) we could use the value to just
 * simply be a callback function. Example:
 * 'create' : function(parent) {
 * ...
 * return promise;
 * }
 * 
 * Or if we wanted to just the Menu Display use a more complete object definition,
 * like:
 * 'create' : {
 *   'title': 'Create'
 *   'shortcut': 'Ctrl+c'
 *   'callback': function(parent) {
 *     ...
 *     return promise;
 *   }
 * }
 * 
 * There should be 2 types of actions:
 * 1. Menu Actions - Actions that are activated through menu entries
 * ex: create, rename, delete
 * 2. Internal Actions - That are called by internal processes
 * ex: move (folder using drag and drop) and load (used to load the 
 * nodes of a parent folder).
 */

/* EXAMPLE USE
 $fv = $('#folderview');
 s = { title: 'Project Folders', root: { id: window.__session.project.container, title: 'ROOT' }};
 s.callbacks = {};
 s.callbacks.data_url = function(id) { return window.testcenter.services.url.service(['folders', 'list'], [id, 'F'], null); }
 s.callbacks.data_to_nodes = function(response) {
 var entity_set = response['return'];
 var nodes = [];
 var entities = entity_set.entities;
 var key_field = entity_set.__key;
 var display_field = entity_set.__display;
 var defaults = {
 folder: true,
 lazy: true
 };
 // Build Initial Folder List
 $.each(entities, function(i, entity) {
 var node = $.extend(true, {}, defaults, {
 id: entity[key_field],
 title: entity[display_field]
 });
 nodes.push(node);
 });
 return nodes;
 };
 s.callbacks.menu = function(node) {
 // All The Possible Actions on a Folder
 var items = {
 'rename': {'name': 'Rename', 'icon': 'edit'},
 'create': {'name': 'New Folder..', 'icon': 'add'},
 'seperator': '---------',
 'delete': {'name': 'Delete', 'icon': 'delete'}
 };
 // NOTE: The Node we created as 'ROOT' is not the Root Node but the
 // 1st and only child of the Fancy Tree Root Node.
 var isRoot = !node.isRootNode() && node.getParent().isRootNode();
 // Are we building the Context Menu for the Root Node?
 if (isRoot) { // YES: Disable Invalid Actions
 items['rename'].disabled = true;
 items['delete'].disabled = true;
 }
 
 return items;
 };
 s.callbacks.menu_handlers = function(node, action, options) {
 console.log('Selected action "' + action + '" on node ' + node.key);
 var $form = $('#form_' + action + '_folder');
 if ($form.length) {
 form_show($form);
 } else {
 console.log('Missing Form for action[' + action + ']');
 }
 };
 $fv.folderview(s);
 */