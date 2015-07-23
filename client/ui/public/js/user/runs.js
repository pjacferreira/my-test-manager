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

  // Attach Listeners to Organization and Project Change Events
  $(document).on('testcenter.organization.change', onChangeOrganization);
  $(document).on('testcenter.project.change', onChangeProject);

  // Initialize Organization/Project Dropdowns
  initialize_dropdowns();

  // Attach Other Listeners
  $('#folders').on('folder-view.folder-selected', onFolderSelected);
  $('#folders').on('set-runs-folder', onSetRunsFolder);
  $('#folders').on('reload-runs-folder', onReloadRunsFolder);

  $('#list_sets').on('gridlist.item-selected', function(event, node) {
    console.log('selected-node [' + node.id + ':' + node.text + ']');
  });
  $('#list_runs').on('gridlist.item-selected', function(event, node) {
    console.log('selected-node [' + node.id + ':' + node.text + ']');
    load_run(node.id);
  });

  // Remove Loader
  $('#loader').removeClass('active');
}

/*******************
 * 
 * EVENT HANDLERS
 * 
 *******************/

function onChangeOrganization(event) {
  console.log('Captured Organization Change');
}

function onChangeProject(event) {
  console.log('Captured Project Change');
  initialize_folders_view('#folders');
  initialize_sets_list('#list_sets');
  initialize_runs_list('#list_runs');
}

function onFolderSelected(event, id) {
  console.log('load-folder [' + id + ']');

  // Clear any Information in Run Update Form
  form_reset($('#form_update_run'));

  // Reload Sets View
  var $view = $('#list_sets');
  $view.gridlist('clear').
    data('folder.parent', id).
    gridlist('load', id);

  // Have we set a Specific Runs Creation Folder?
  var runs = $('#folders').data('runs-folder');
  if (!$.isset(runs)) { // NO: Then use Runs View === Sets View
    var $view = $('#list_runs');
    $view.gridlist('clear').
      data('folder.parent', id).
      gridlist('load', id);
  }
}

function onSetRunsFolder(event, id) {
  console.log('set-runs-folder [' + id + ']');

  // Set the Runs Create Folder
  $('#folders').data('runs-folder', id);

  // Reload Runs View
  var $view = $('#list_runs');
  $view.gridlist('clear').
    data('folder.parent', id).
    gridlist('load', id);
}

function onReloadRunsFolder(event, id) {
  console.log('reload-runs-folder [' + id + ']');

  // Do we want to reload the runs view?
  var $view = $('#list_runs');
  if ($view.data('folder.parent') === id) { // YES
    $view.gridlist('clear').
      gridlist('load', id);
  }
}

/*******************
 * 
 * FOLDER VIEW
 * 
 *******************/

function initialize_folders_view(selector) {
  var $view = $(selector);

  $view.folderview({
    root: {
      id: __session.project.container,
      title: __session.project.name
    },
    callbacks: {
      data_promise: folder_loader,
      data_to_nodes: folders_to_nodes,
      menu: contextmenu_folder,
      menu_handlers: menu_folder_action
    }
  });
}

function folder_loader(id) {
  // SEE NT-001
  return testcenter.services.call(['folder', id.toString(), 'folders', 'list']);
}

function folders_to_nodes(response) {
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
      id: entity[key_field],
      title: entity[display_field]
    });
    nodes.push(node);
  });
  return nodes;
}

function contextmenu_folder(node) {
  // All The Possible Actions on a Folder
  var items = {
    'rename': {'name': 'Rename', 'icon': 'edit'},
    'create': {'name': 'New Folder..', 'icon': 'add'},
    'seperator1': '---------',
    'delete': {'name': 'Delete', 'icon': 'delete'},
    'seperator2': '---------',
    'runs_folder': {'name': 'Set Runs Folder...', 'icon': 'at'}
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

function menu_folder_action(node, action, options) {
  console.log('Selected action "' + action + '" on node ' + node.key);
  if (action === 'runs_folder') {
    $('#folders').trigger('set-runs-folder', node.data.id);
  } else {
    var $form = $('#form_' + action + '_folder');

    if ($form.length) {
      // Set Data for Form
      $form.data('fv.container', $('#folders'));
      $form.data('fv.node.selected', node);

      form_show($form);
    } else {
      console.log('Missing Form for action[' + action + ']');
    }
  }
}

/*******************
 * 
 * SETS LIST VIEW
 * 
 *******************/

function initialize_sets_list(selector) {
  var $grid = $(selector);

  $grid.gridlist({
    icon: 'tasks',
    icon_color: function() {
      if ($.isset(this.state)) {
        switch (this.state) {
          case 0:
            return 'red';
          case 9:
            return 'green';
          default:
            return 'orange';
        }
      } else {
        return 'black';
      }
    },
    callbacks: {
      loader: load_sets,
      data_to_nodes: sets_to_node,
      menu_items: menu_items_sets,
      menu_handlers: menu_handlers_sets
    }
  });
}

function load_sets(folder_id) {
  return testcenter.services.call(['folder', folder_id.toString(), 'list'], 'S');
}

function sets_to_node(response) {
  var response = response['return'];
  var nodes = [];
  var defaults = {
  };

  switch (response.__type) {
    case 'entity-set':
      var entities = response.entities;
      var display_field = response.__display;
      // Build Nodes
      $.each(entities, function(i, entity) {
        var node = $.extend(true, {}, defaults, {
          id: entity.link,
          text: entity[display_field]
        });
        nodes.push(node);
      });
      break;
    case 'entity':
      var node = $.extend(true, {}, defaults, {
        id: response[response.__key],
        text: response[response.__display]
      });
      nodes.push(node);
      break;
    default:
      console.log('Invalid Response');
  }
  return nodes;
}

function menu_items_sets($node) {
  var items = {
    'create': {'name': 'New Run..', 'icon': 'add'}
  };

  return items;
}

function menu_handlers_sets($node, action, options) {
  if ($node) {
    console.log('Selected action "' + action + '" on Set [' + $node.attr('id') + ']');
    window.$selected_set = $node;
  } else {
    console.log('Selected action "' + action + '"');
  }
  var $form = $('#form_' + action + '_run');
  if ($form.length) {
    // NOTE: Handler Called in the Context of Grid List Object
    var set = $node.data('node.element');
    var $runs = $('#list_runs');
    $form.data('set.id', set.id);
    $form.data('folder.parent', $runs.data('folder.parent'));
    form_show($form);
  } else {
    console.log('Missing Form for action[' + action + ']');
  }
}

/*******************
 * 
 * RUNS LIST VIEW
 * 
 *******************/

function initialize_runs_list(selector) {
  var $grid = $(selector);

  $grid.gridlist({
    icon: 'tasks',
    icon_color: function() {
      if ($.isset(this.state)) {
        switch (this.state) {
          case 0:
            return 'red';
          case 9:
            return 'green';
          default:
            return 'blue';
        }
      } else {
        return 'black';
      }
    },
    callbacks: {
      loader: load_runs,
      data_to_nodes: runs_to_node
    }
  });
}

function load_runs(folder_id) {
  return testcenter.services.call(['folder', folder_id.toString(), 'runs', 'list']);
}

function runs_to_node(response) {
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
          id: entity[key_field],
          text: entity[display_field],
          state: entity.state
        });
        nodes.push(node);
      });
      break;
    case 'entity':
      var node = $.extend(true, {}, defaults, {
        id: response[response.__key],
        text: response[response.__display],
        state: response.state
      });
      nodes.push(node);
      break;
    default:
      console.log('Invalid Response');
  }
  return nodes;
}

/*******************
 * 
 * RUN UPDATE FORM
 * 
 *******************/

function load_run(id) {
  var $form = $('#form_update_run');
  // Clear the Form
  form_reset($form);
  // Flag the DIV as Loading
  $form.addClass('loading');
  // Load the Tests into the Folder
  testcenter.services.call(['run', id.toString(), 'read'], null, null, {
    call_ok: function(run) {
      // Save the Test Data Information with the Form
      form_load($form, run, 'run');
      $form.data('run', run);
    },
    call_nok: function(code, message) {
      form_disable($form, message);
      // Finished Loading
      $form.removeClass('loading');
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
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_rename_folder($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_delete_folder($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_create_run($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_update_run($form) {
  __initialize_form($form);
}

function __initialize_form($form) {
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
function __button_create_folder(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  /* Load the Node if Not Already Loaded.
   * Why? 
   * Beacuse if we add a child node to a node, that hasn't been loaded,
   * FancyTree will assume the node is loaded, and no the ajax request to load.
   */
  var node = $form.data('fv.node.selected');
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
function __button_rename_folder(event) {
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
function __button_delete_folder(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  __do_delete_folder();
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_create_run(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'run-name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Run Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_run,
    onFailure: function() {
      alert("Create Run: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_update_run(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'run-name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Run Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_update_run,
    onFailure: function() {
      alert("Update Run: Missing or Invalid Fields");
    }
  });
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
  var node = $form.data('fv.node.selected');
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
  var node = $form.data('fv.node.selected');
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
  var node = $form.data('fv.node.selected');
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

function __do_create_run() {
  // Extract Field Values
  var values = {};
  var $form = $('#form_create_run');
  $form.find('.field > :input').each(function() {
    var name = this.name;
    if ($.isset(name)) {
      // Remove Leading 'test'
      var property = name.split('-');
      property = (property.length > 1) ? property[1] : property[0];
      values['run:' + property] = $(this).val();
    }
  });

  // Build Route Parameters ({name}/{set_id}[/{folder_id}]
  var params = [values['run:name']];
  params.push($form.data('set.id'));
  var folder = $form.data('folder.parent');
  if ($.isset(folder)) {
    params.push(folder);
  }

  // Add the Node to the Grid
  var $grid = $('#items_1');
  $grid.gridlist('add',
    testcenter.services.call(['run', 'create'], params, null, {
      call_ok: function(entity) {
        form_hide($form);

        // Force Reload of Folder Run was Pasted To
        $('#folders').trigger('reload-runs-folder', folder);
      },
      call_nok: function(code, message) {
        form_show_errors($form, message);
      }
    })
    );

  return values;
}

function __do_update_run() {
  var $form = $('#form_update_run');

  // Get the Test Associated with the Form
  var run = $form.data('run');

  // Extract Field Values
  var values = {};
  $form.find('.field > :input').each(function() {
    var name = this.name;
    if ($.isset(name)) {
      // Remove Leading 'test'
      var property = name.split('-');
      property = (property.length > 1) ? property[1] : property[0];
      if ($.inArray(property, run.__fields) >= 0) {
        if (property !== run.__key) {
          values['run:' + property] = $(this).val();
        }
      }
    }
  });

  testcenter.services.call(['run', run[run.__key].toString(), 'update'], null, values, {
    type: 'POST',
    call_ok: function(entity) {
      console.log("Updated Run [" + run[run.__key] + "]");
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

function form_load($form, values, prefix) {
  $form.find('.field > :input').each(function() {
    var field = field_key(this.name, prefix);

    var value = $.objects.get(field, values);
    if (value !== null) {
      $this = $(this);
      if (this.type === 'checkbox') {
        value = (value === 'true') ? true : false;
        $this.prop('checked', value);
      } else if ((this.type === 'hidden') && $this.parent().hasClass('dropdown')) {
        $this.parent().dropdown('set selected', value).dropdown('set value', value);
      } else {
        $this.val(value);
      }
    }
  });

  // Remove the Loading Symbol
  $form.removeClass('loading');
}

function field_key(name, prefix) {
  // Cleanup Variables
  name = $.strings.nullOnEmpty(name);
  if ($.isset(name)) {
    prefix = $.strings.nullOnEmpty(prefix);
    if ($.isset(prefix)) {
      prefix += '-';
      if (name.slice(0, prefix.length) === prefix) {
        name = name.slice(prefix.length);
      }
    }

    name = name.replace(/-/g, '.');
  }

  return name;
}
