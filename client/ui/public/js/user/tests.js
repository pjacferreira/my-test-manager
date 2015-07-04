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

  // Initialize Organizations
  initialize_dropdowns();

  // Attach Other Listeners
  $('#folders').on('folder-view.folder-selected', onFolderSelected);
  $('#tests').on('gridlist.item-selected', onTestSelected);

  // Remove Loader
  $('#loader').removeClass('active');

  // Activate HTML TextArea Editor
  var editor = new MediumEditor('div.textarea');
  /*
   , {
   placeholder: {
   // Get the PlaceHolder Text Directly from the Element
   text: $editor.attr('placeholder')
   }    
   });
   */
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
  initialize_folders_list();
  initialize_items_grid();
}

function onFolderSelected(event, id) {
  console.info('load-folder [%s]', id);
  // Clear the Form
  form_reset($('#form_update_test'));
  // Update Test List
  var $list = $('#tests');
  $list.gridlist('clear').
    data('folder.parent', id).
    gridlist('load', id);
}

function onTestSelected(event, node) {
  console.info('selected-node [%d:$s]', node.id, node.text);
  load_test(node.id);

  var $list = $('#list_steps');
  if ($list.length) {
    initialize_steps_list($list, node.id);
    $list.orderedlist('load', node.id);
  }
}

/*******************
 * 
 * TESTS NAVIGATOR
 * 
 *******************/

/* FOLDER VIEW */

function initialize_folders_list() {
  var $view = $('#folders');

  $view.folderview({
    root: {
      id: window.__session.project.container,
      title: 'ROOT'
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
  $.each(entities, function (i, entity) {
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
    'create-test': {'name': 'Create Test...', 'icon': 'add'}
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
  var $form = [];
  switch (action) {
    case 'create-test':
      $form = $('#form_create_test');
      $form.data('folder.parent', node.data.id);
      break;
    default:
      $form = $('#form_' + action + '_folder');
      $form.data('fv.container', $('#folders'));
      $form.data('fv.node.selected', node);
  }

  if ($form.length) {
    form_show($form);
  } else {
    console.log('Missing Form for action[' + action + ']');
  }
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

/* TESTS VIEW */

function initialize_items_grid() {
  var $grid = $('#tests');

  $grid.gridlist({
    icon: 'file',
    icon_color: function () {
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
      loader: load_tests,
      data_to_nodes: tests_to_node,
      menu_items: menu_items_tests,
      menu_handlers: menu_handlers_tests
    }
  });
}

function load_tests(folder_id) {
  return testcenter.services.call(['folder', folder_id.toString(), 'tests', 'list']);
}

function tests_to_node(response) {
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
      $.each(entities, function (i, entity) {
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

function menu_items_tests($node) {
  var items = {
    'create': {'name': 'New Test..', 'icon': 'add'},
    'seperator': '---------',
    'delete': {'name': 'Delete', 'icon': 'delete'}
  };

  if (!$.isset($node)) {
    items.delete.disabled = true;
  }

  return items;
}

function menu_handlers_tests($node, action, options) {
  if ($node) {
    console.log('Selected action "' + action + '" on Set [' + $node.attr('id') + ']');
    window.$selected_set = $node;
  } else {
    console.log('Selected action "' + action + '"');
  }
  var $form = $('#form_' + action + '_test');
  if ($form.length) {
    // NOTE: Handler Called in the Context of Grid List Object
    $form.data('folder.parent', this.data('folder.parent'));
    form_show($form);
  } else {
    console.log('Missing Form for action[' + action + ']');
  }
}

/*******************
 * 
 * TEST STEPS VIEW
 * 
 *******************/

function initialize_steps_list($list, test_id) {
  var $list = $('#list_steps');
  $list.data('test', test_id);

  var settings = {
    callbacks: {
      loader: load_steps,
      data_to_nodes: steps_to_nodes
    },
    buttons: {
      'up': {
        callback: step_move_up
      },
      'edit': {
        callback: step_edit
      },
      'add': {
        callback: step_create
      },
      'delete': {
        callback: step_delete
      },
      'down': {
        callback: step_move_down
      }
    }
  };

  $list.orderedlist(settings);
//  $('#list_steps'.orderedlist('load', test_id);
}

function load_steps(test_id) {
  return testcenter.services.call(['steps', 'list'], test_id);
}

function steps_to_nodes(response) {
  var response = response['return'];
  var nodes = [];
  var defaults = {
  };

  switch (response.__type) {
    case 'entity-set':
      var entities = response.entities;
      // Build Nodes
      $.each(entities, function (i, entity) {
        var node = $.extend(true, {}, defaults, {
          id: entity.sequence,
          test: entity['test'],
          key: entity[response.__key],
          title: entity[response.__display],
          html: entity['description']
        });
        nodes.push(node);
      });
      break;
    case 'entity':
      var node = $.extend(true, {}, defaults, {
        id: response.sequence,
        test: response['test'],
        key: response[response.__key],
        title: response[response.__display],
        html: response['description']
      });
      nodes.push(node);
      break;
    default:
      console.log('Invalid Response');
  }
  return nodes;
}

/* ORDERED LIST ACTION HANDLER */

function do_nothing($node, node) {
  console.log('Clicked [' + $node.attr('id') + '] - Step [' + node.title + ']');
}

function step_move_up($node, node) {
  var $list = this;
  // Load the Test Steps
  testcenter.services.call(['step', 'move', 'up'], [node.test, node.id]).
    then(function (response) {
      $list.orderedlist('node.update', $node, response);
      $list.orderedlist('node.up', $node);
    }, function (code, message) {
      console.log('ERROR: Failed to Move Step [' + code + ':' + message + ']');
    });
  return true;
}

function step_move_down($node, node) {
  var $list = this;
  // Load the Test Steps
  testcenter.services.call(['step', 'move', 'down'], [node.test, node.id]).
    then(function (response) {
      $list.orderedlist('node.update', $node, response);
      $list.orderedlist('node.down', $node);
    }, function (code, message) {
      console.log('ERROR: Failed to Move Step [' + code + ':' + message + ']');
    });
  return true;
}

function step_create($node, node) {
  var $form = $('#form_create_step');
  $form.data('list', this);
  $form.data('after.node', $node);
  $form.data('after.item', node);
  form_show($form);
  return true;
}

function step_edit($node, node) {
  // Display Create Step Form
  var $form = $('#form_edit_step');
  $form.data('list', this);
  $form.data('node', $node);
  $form.data('item', node);
  $form.addClass('loading');
  form_show($form);

  // Load the Test Steps
  testcenter.services.call(['step', 'read'], [node.test, node.id], null, {
    call_ok: function (step) {
      form_load($form, step, 'step');
    },
    call_nok: function (code, message) {
      console.log('ERROR: Failed to Delete Step [' + code + ':' + message + ']');
    }
  });
}

function step_delete($node, node) {
  var $list = this;
  // Load the Test Steps
  testcenter.services.call(['step', 'delete'], [node.test, node.id], null, {
    call_ok: function (result) {
      if (result) {
        $list.orderedlist('node.remove', $node);
      }
    },
    call_nok: function (code, message) {
      console.log('ERROR: Failed to Delete Step [' + code + ':' + message + ']');
    }
  });
  return true;
}

/*******************
 * 
 * TEST UPDATE FORM
 * 
 *******************/

function load_test(id) {
  var $form = $('#form_update_test');
  // Clear the Form
  form_reset($form);
  // Flag the DIV as Loading
  $form.addClass('loading');
  // Load the Tests into the Folder
  testcenter.services.call(['test', 'read'], id, null, {
    call_ok: function (test) {
      // Save the Test Data Information with the Form
      form_load($form, test, 'test');
      $form.data('test', test);
    },
    call_nok: function (code, message) {
      form_disable($form, message);
      // Finished Loading
      $form.removeClass('loading');
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_update_test(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Test Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_update_test,
    onFailure: function () {
      alert("Update Test: Missing or Invalid Fields");
    }
  });
}

function __do_update_test() {
  var $form = $('#form_update_test');

  // Get the Test Associated with the Form
  var test = $form.data('test');

  // Extract Field Values
  var values = get_entity_values($form);

  // Call Service to Update the Step
  testcenter.services.call(['test', 'update'], test[test.__key], values, {
    type: 'POST',
    call_ok: function (entity) {
      console.info('Updated Test [%d]', test[test.__key]);
    },
    call_nok: function (code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

/*******************************
 * INITIALIZE FORMS
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
function __initialize_form_create_test($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_update_test($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_create_step($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_edit_step($form) {
  __initialize_form($form);
}

function __initialize_form($form) {
  // Link Buttons to Handlers
  $form.find(".button").each(function () {
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
    onFailure: function () {
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
    onFailure: function () {
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
function __button_create_test(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Test Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_test,
    onFailure: function () {
      alert("Create Test: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_create_step(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      test: {
        identifier: 'test',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Test ID'
          }
        ]
      },
      title: {
        identifier: 'title',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Step Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_step,
    onFailure: function () {
      alert("Create Test: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_update_step(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Step Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_update_step,
    onFailure: function () {
      alert("Update Test: Missing or Invalid Fields");
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
  var values = get_entity_values($form);

  // Get the Currently Selected Folder
  var node = $form.data('fv.node.selected');
  var key = node.key.split(':');

  // Call Service to Create the Folder
  testcenter.services.call(['folder', 'create'], [key[1], values.name], null,
    {
      call_ok: function (entity) {
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

        // Log Folder Create
        console.info('Create Child Node [%s] under Parent Node [%d,%s]', values['container:name'], key[1], node.title);
      },
      call_nok: function (code, message) {
        form_show_errors($form, message);
      }
    });

  return values;
}

function __do_rename_folder() {
  var $form = $('#form_rename_folder');

  // Extract Field Values
  var values = get_entity_values($form);

  // Get the Currently Selected Folder
  var node = $form.data('fv.node.selected');
  var key = node.key.split(':');

  // Call Service to Rename the Folder
  testcenter.services.call(['folder', 'rename'], [key[1], values['container:name']], null,
    {
      call_ok: function (entity) {
        // Change the Nodes Label      
        var display_field = entity.__display;
        node.setTitle(entity[display_field]);
        // Hide the Form 
        form_hide($form);

        // Log Folder Rename
        console.info('Rename Folder [%d:%s] to [%s]', key[1], node.title, values['container:name']);
      },
      call_nok: function (code, message) {
        form_show_errors($form, message);
      }
    });

  return values;
}

function __do_delete_folder() {
  var $form = $('#form_delete_folder');

  // Get the Currently Selected Folder
  var node = $form.data('fv.node.selected');
  var key = node.key.split(':');

  // Call Service to Delete the Folder
  testcenter.services.call(['folder', 'delete'], key[1], null,
    {
      call_ok: function (entity) {
        // Delete the Folder from the Tree View
        var parent = node.getParent();
        parent.removeChild(node);

        // Hide the Form 
        form_hide($form);

        // Log Folder Deleted
        console.info('Delete Folder [%d:%s]', key[1], node.title);
      },
      call_nok: function (code, message) {
        form_show_errors($form, message);
      }
    });

  return null;
}

function __do_create_test() {
  // Extract Field Values
  var $form = $('#form_create_test');

  // Extract Field Values
  var values = get_entity_values($form);

  // Build Route Parameters
  var params = [values['test:name']];
  var folder = $form.data('folder.parent');
  if ($.isset(folder)) {
    params.push(folder);
  }

  // Call Service to Create the Test and Add it to the Grid
  var $grid = $('#tests');
  $grid.gridlist('add',
    testcenter.services.call(['test', 'create'], params, null,
      {
        call_ok: function (entity) {
          form_hide($form);
        },
        call_nok: function (code, message) {
          form_show_errors($form, message);
        }
      })
    );

  return values;
}

function __do_create_step() {
  var $form = $('#form_create_step');

  // Get Parameters Associated with the Form
  var $list = $form.data('list');
  var $after = $form.data('after.node');
  var after = $form.data('after.item');

  // Extract Field Values
  var values = get_entity_values($form);

  // Create Service to Call and Parameters
  var service, params;
  if ($.isset(after)) {
    service = ['step', 'create', 'after'];
    params = [after.test, after.id, values['step:title']];
  } else {
    service = ['step', 'create'];
    params = [after.test, values['step:title']];
  }

  // Call Service to Create the Step
  testcenter.services.call(service, params).
    then(function (response) {
      // Add the New Entry
      $list.orderedlist('node.add', response, $after);
      // Hide the Form 
      form_hide($form);
    }, function (code, message) {
      form_show_errors($form, message);
    });

  return values;
}

function __do_update_step() {
  var $form = $('#form_edit_step');

  // Get Parameters Associated with the Form
  var $list = $form.data('list');
  var $node = $form.data('node');
  var node = $form.data('item');

  // Extract Field Values
  var values = get_entity_values($form);

  // Call Service to Update the Step
  testcenter.services.call(['step', 'update'], [node.test, node.id], values, {type: 'POST'}).
    then(function (response) {
      // Update Entry
      $list.orderedlist('node.update', $node, response);
      // Hide the Form 
      form_hide($form);
    }, function (code, message) {
      form_show_errors($form, message);
    });

  return values;
}

/*******************************
 * HELPER FUNCTIONS
 *******************************/

function get_entity_values($form) {
  var values = {};

  // Extract Values from Standard Input Fields
  $form.find('.field > :input').each(function () {
    var $this = $(this);
    var name = $this.attr('name');
    if ($.isset(name)) {
      // Explode Name based on concept (entity_name':'property_names)
      var property = name.split('-');

      // Do we have a valid entity property?
      if (property.length > 1) { // YES: Add it to list
        values[property.join(':')] = $this.val();
      }
    }
  });

  // Extract Values from HTML Editor Fields
  $form.find('.field > div.textarea').each(function () {
    var $this = $(this);
    var name = $this.attr('name');
    if ($.isset(name)) {
      // Explode Name based on concept (entity_name':'property_names)
      var property = name.split('-');

      // Do we have a valid entity property?
      if (property.length > 1) { // YES: Add it to list (NOTE: Escape HTML Tags)
        values[property.join(':')] = $this.html();
      }
    }
  });

  return values;
}

function set_entity_values($form, values, entity) {

  // Extract Values from Standard Input Fields
  $form.find('.field > :input').each(function () {
    var $this = $(this);

    // Does the Field Input have Name Property?
    var name = $this.attr('name');
    if ($.isset(name)) { // YES      
      // Do we have a Value for the Field?
      var field = field_key(name, entity);
      var value = $.objects.get(field, values);
      if (value !== null) { // YES
        if (this.type === 'checkbox') {
          value = (value === 'true') ? true : false;
          $this.prop('checked', value);
        } else if ((this.type === 'hidden') && $this.parent().hasClass('dropdown')) {
          $this.parent().dropdown('set selected', value).dropdown('set value', value);
        } else {
          $this.val(value);
        }
      }
    }
  });

  // Extract Values from HTML Editor Fields
  $form.find('.field > div.textarea').each(function () {
    var $this = $(this);

    // Does the Field Input have Name Property?
    var name = $this.attr('name');
    if ($.isset(name)) { // YES      
      // Do we have a Value for the Field?
      var field = field_key(name, entity);
      var value = $.objects.get(field, values);
      if (value !== null) { // YES : Encode HTML Tags
        $this.click();
        $this.html($('<div/>').html(value).text());
      }
    }
  });
}

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
  // Load the Form with the Incoming Value
  set_entity_values($form, values, prefix);

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

function create_navigator(selector) {
  var $container = $(selector);

  if ($container.length) {
    $container.navigator({
      title: 'PAGE:USER:TESTS:TEST_MANAGER',
      panes: {
        folders: {
          order: 1,
          size: 4,
          creator: function ($pane, settings) {
            $pane.foldertree(settings);
          },
          settings: {
            title: 'PAGE:USER:TESTS:FOLDERS',
            root: {
              id: 4,
              title: 'ROOT'
            },
            callbacks: {
              data_url: function (id) {
                return testcenter.services.call(['folder', id.toString(), 'folders', 'list']);
              },
              data_to_nodes: null,
              'context-menu': contextmenu_folder
            }
          }
        },
        items: {
          order: 2,
          size: 12,
          creator: function ($pane, settings) {
            $pane.gridlist(settings);
          },
          settings: {
            title: 'PAGE:USER:TESTS:TESTS',
            columns: 4,
            callback: {
              loader: null,
              post_process: null,
              'context-menu': contextmenu_tests
            }
          }
        }
      }
    });
  } else {
    console.log('Container for Navigator not Found.');
  }
}

/* TODO:
 * 1. We need to be able to handle organization change event (i.e. the event is
 * fired when the organization is changed in the dropdown).
 * 
 * POSSIBLE SOLUTION:
 * 1. On Capturing the event we could do:
 * a) Destroy/Clear the Tests Navigation Page.
 * b) Place Dimmer over the Navigation Pane.
 */
/* TODO: 
 * 2. We need to be able to handle changge of project (i.e. the event is
 * fired when the project is changed in the dropdown).
 * 
 * POSSIBLE SOLUTION:
 * 1. On Capturing the event we could do:
 * a) Clear the Test Pane Window (Replace it with the filler it had at the
 * beginning).
 * b) Create/Reset the fancytree.
 */
/* TODO: BUG
 * 3. Delete Test Menu Action doesn't Work.
 */
/* TODO: BUG
 * 4. When a Folder is Empty (i.e. Contains No Tests) there is no way
 * to display the Test Menu (to Create  a Test). This occurs because the
 * <div> that contains the ordered list has no heigth (since it has no items)
 * so there is nothing to click on!?
 * 
 * POSSIBLE SOLUTION:
 * 1. Force the height (minimum) to take up the avaliable space in the cell.
 */
/* TODO: 
 * 5. Currently, when the Test Navigator is created, no folder is selected
 * (i.e. nothing is loaded in the tests part of the window), therefore it
 * does not make sense to display the test context-menu since we can't really
 * do anything.
 * 
 * POSSIBLE SOLUTIONS:
 * 1. When creating the test navigator, automatically select the ROOT FOLDER
 * for the project.
 * 2. Don't display / create the Context Menun until the root folder has been
 * selected.
 */
/* TODO: BUG
 * 5. When a we create a Test in a Sub-Folder, it is being created in the
 * ROOT Folder Instead?
 */
