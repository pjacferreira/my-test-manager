/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/

/**
 * 
 * @returns {undefined}
 */
function initialize() {
  // Initialize the Session
  initialize_session();

  // Initialize Forms
  initialize_forms();

  // Initialize Organizations
  initialize_dropdowns();

  // Initialize the Folders List
  initialize_folders_list();

  // Mark the Tests Columns as Being Four Wide
  $('#tests').get(0).__columns = 4;

  $('#folders').on('load-folder', function(event, id) {
    console.log('load-folder [' + id + ']');
    load_tests(id);
  });

  $('#tests').click(clicked_test);

  // Remove Loader
  $('#loader').removeClass('active');
}

function initialize_folders_list() {
  var $tree = $('#folders');

  // Initialize Tree
  $tree.fancytree({
    extensions: ['contextMenu'],
    source: [{title: 'ROOT', key: 'f:4', folder: true, lazy: true, expandend: false}],
    lazyLoad: lazy_loader,
    selectMode: 1,
    postProcess: entities_to_nodes,
    activate: select_node,
    contextMenu: {
      menu: menu_items_folder,
      actions: menu_action
    }
  });
}

function lazy_loader(event, data) {
  var key = data.node.key.split(':');
  if (key.length > 1) {
    data.result = {
      url: window.testcenter.services.url.service(['folder', 'list'], [key[1], 'F'], null),
      cache: false
    };
  }
}

function entities_to_nodes(event, data) {
  var response = data.response;
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
    var node = $.extend({}, defaults, {
      key: 'f:' + entity[key_field],
      title: entity[display_field]
    });

    nodes.push(node);
  });

  data.result = nodes;
}

function select_node(event, data) {
  console.log("Event [" + event.type + "] -  Node [" + data.node.key + ":" + data.node.title + "]");

  // If we have a Form Displayed Hide it
  if ($.isset(window.$form_displayed)) {
    form_hide(window.$form_displayed);
  }

  // Set the Current Selected Node
  window.$selected_node = data.node;

  // fire event
  var id = data.node.key.split(':');
  $('#folders').trigger('load-folder', id[1]);
}

function clicked_test(event) {
  var element = event.toElement;
  var $test = $(element).closest('div.tc_test');
  if ($test.length) {
    var id = $test.attr('id').split(':');
    id = id.length > 1 ? id[1] : 'Missing ID';
    console.log('Clicked Test #' + id);
  } else {
    console.log("Didn't Click a Test!")
  }

  // Stop Propogation
  return false;
}

function menu_items_folder(node) {
  // All The Possible Actions on a Folder
  var items = {
    'rename': {'name': 'Rename', 'icon': 'edit'},
    'create': {'name': 'New Folder..', 'icon': 'add'},
    'seperator': '---------',
    'delete': {'name': 'Delete', 'icon': 'delete'}
  };

  /* NOTE: The Node we created as 'ROOT' is not the Root Node but the
   * 1st and only child of the Fancy Tree Root Node.
   */
  var isRoot = !node.isRootNode() && node.getParent().isRootNode();

  // Are we building the Context Menu for the Root Node?
  if (isRoot) { // YES: Disable Invalid Actions
    items['rename'].disabled = true;
    items['delete'].disabled = true;
  }

  return items;
}

function menu_action(node, action, options) {
  console.log('Selected action "' + action + '" on node ' + node.key);
  var $form = $('#form_' + action + '_folder');
  if ($form.length) {
    form_show($form);
  } else {
    console.log('Missing Form for action[' + action + ']');
  }
}

function load_tests(folder_id) {
  var $list = $('#tests');

  // Flag the DIV as Loading
  $list.addClass('loading');

  // Remove Existing Tests from the List
  $list.empty();

  // Load the Tests into the Folder
  testcenter.services.call(['folder', 'list'], [folder_id, 'T'], null, {
    call_ok: function(entity_set) {

      var nodes = [];
      var entities = entity_set.entities;
      var key_field = entity_set.__key;
      var display_field = entity_set.__display;
      var $template = $.templates('<div id="t:{{:' + key_field + '}}" class="tc_test aligned column"><i class="file icon"></i>{{:' + display_field + '}}</div>');

      // Build the Nodes List
      $.each(entities, function(i, entity) {
        var node = $template.render(entity);
        nodes.push(node);
      });

      // Do we have Nodes?
      if (nodes.length) { // YES
        // Build the Rows of Tests
        var n_columns = $list.get(0).__columns;
        var n_rows = Math.floor(nodes.length / n_columns) + 1;
        var offset, rows = [], row_nodes;
        for (var i = 0; i < n_rows; i++) {
          offset = i * n_columns;
          row_nodes = nodes.slice(offset, n_columns);
          rows.push('<div class="centered row">' + row_nodes.join('') + '</div>');
        }

        // Set the Test List Content
        $list.html(rows.join(''));
      } else {
        $list.html('<div class="tc_test aligned column">Empty Folder</div>');
      }

      // Finished Loading
      $list.removeClass('loading');
    },
    call_nok: function(code, message) {
      $list.html('<div class="tc_test aligned column">Error [' + code + ':' + message + '] loading test list</div>');
    }
  });

}

/*******************************
 * CREATE FOLDER FORM
 *******************************/

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_create_folder($form) {
  // Link Buttons to Handlers
  $form.find(".button").each(function() {
    var $this = $(this);
    var name = $.strings.nullOnEmpty($this.attr('id'));
    if (name !== null) {
      var handler = '__' + name;
      if (window.hasOwnProperty(handler) && $.isFunction(window[handler])) {
        $this.click($form, window[handler]);
      }
    }
  });
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_rename_folder($form) {
  // Link Buttons to Handlers
  $form.find(".button").each(function() {
    var $this = $(this);
    var name = $.strings.nullOnEmpty($this.attr('id'));
    if (name !== null) {
      var handler = '__' + name;
      if (window.hasOwnProperty(handler) && $.isFunction(window[handler])) {
        $this.click($form, window[handler]);
      }
    }
  });
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_delete_folder($form) {
  // Link Buttons to Handlers
  $form.find(".button").each(function() {
    var $this = $(this);
    var name = $.strings.nullOnEmpty($this.attr('id'));
    if (name !== null) {
      var handler = '__' + name;
      if (window.hasOwnProperty(handler) && $.isFunction(window[handler])) {
        $this.click($form, window[handler]);
      }
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_create(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;

  /* Load the Node if Not Already Loaded.
   * Why? 
   * Beacuse if we add a child node to a node, that hasn't been loaded,
   * FancyTree will assume the node is loaded, and no the ajax request to load.
   */
  var node = window.$selected_node;
  node.load();

  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Folder Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_folder,
    onFailure: function() {
      alert("Create Folder: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_rename(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;

  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Folder Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_rename_folder,
    onFailure: function() {
      alert("Create Folder: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_delete(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;

  __do_delete_folder();
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_cancel(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;

  // Hide the Form
  form_hide($form);
}


function __do_create_folder() {
  var $form = $('#form_create_folder');

  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });

  var node = window.$selected_node;
  var key = node.key.split(':');
  console.log('Create Child Node [' + values.name + '] under Parent Node [' + key[1] + ':' + node.title + ']');
  testcenter.services.call(['folder', 'create'], [key[1], values.name], null, {
    call_ok: function(entity) {
      var defaults = {
        folder: true,
        lazy: true
      };

      var key_field = entity.__key;
      var display_field = entity.__display;
      var child = $.extend({}, defaults, {
        key: 'f:' + entity[key_field],
        title: entity[display_field]
      });

      node.addChildren(child);

      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

function __do_rename_folder() {
  var $form = $('#form_rename_folder');

  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });

  var node = window.$selected_node;
  var key = node.key.split(':');
  console.log('Rename Folder [' + key[1] + ':' + node.title + '] to [' + values.name + ']');
  testcenter.services.call(['folder', 'rename'], [key[1], values.name], null, {
    call_ok: function(entity) {
      // Change the Nodes Label      
      var display_field = entity.__display;
      node.setTitle(entity[display_field]);

      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

function __do_delete_folder() {
  var $form = $('#form_delete_folder');

  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });

  var node = window.$selected_node;
  var key = node.key.split(':');
  console.log('Delete Folder [' + key[1] + ':' + node.title + ']');
  testcenter.services.call(['folder', 'delete'], key[1], null, {
    call_ok: function(entity) {
      // Delete the Child Folder
      var parent = node.getParent();
      parent.removeChild(node);

      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

/*******************************
 * HELPER FUNCTIONS
 *******************************/

function form_show($form) {
  if (window.hasOwnProperty('__session')) {
    // Do we have a form with th given id?
    if ($form.length) { // YES
      form_center($form);
      $form.removeClass('hidden');
      window.$form_displayed = $form;
    } else {
      // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
      alert("No Form with the id [" + id + "]");
    }
  } else {
    // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
    alert("Failed Initializing Comunication with Server.");
  }
}

function form_hide($form) {
  if (window.hasOwnProperty('__session')) {
    // Do we have a form with th given id?
    if ($form.length) { // YES
      // Hide the Form
      $form.addClass('hidden');
      window.$form_displayed = null;

      // Reset the Form
      form_reset($form);
    } else {
      // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
      alert("No Form with the id [" + id + "]");
    }
  } else {
    // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
    alert("Failed Initializing Comunication with Server.");
  }
}
