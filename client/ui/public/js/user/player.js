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

  // Initialize Player Views
  initialize_player_tests_view('#test_cards');
  initialize_player_steps_view('#step_cards');

  // Attach Other Listeners
  $('#folders').on('folder-view.folder-selected', onFolderSelected);
  $('#list_runs').on('gridlist.item-selected', onRunSelected);
  $('#test_cards').on('card-clicked.cardview', onTestClicked);
  $('#test_cards').on('set-current.cardview', onSetCurrentTest);
  $('#test_cards').on('loaded.cardview', onTestsLoaded);

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
  initialize_runs_list('#list_runs');
}

function onFolderSelected(event, id) {
  console.log('load-folder [' + id + ']');

  // Reload Sets View
  var $view = $('#list_runs');
  $view.gridlist('clear').
    data('folder.parent', id).
    gridlist('load', id);
}

function onRunSelected(event, node) {
  console.log('gridlist.item-selected [' + node.id + ':' + node.text + ']');

  // Reload Tests View
  var $view = $('#test_cards');
  $view.cardview('clear').
    data('run.id', node.id).
    cardview('load', node.id);
}

function onTestsLoaded(event) {
  var $view = $(this);
  // Make the 1st Test the Current test
  $view.cardview('card.current.set', $view.cardview('card.first'));
}

function onTestClicked(event, card) {
  $(this).cardview('card.current.clear').cardview('card.current.set', card);
}

function onSetCurrentTest(event, card) {
  var test = $(this).cardview('card.data', card);

  // Reload Steps View
  var $view = $('#step_cards');
  $view.cardview('clear').
    data('test.id', test.id).
    cardview('load', test.id);
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
      data_to_nodes: folders_to_nodes
    }
  });
}

function folder_loader(id) {
  // SEE NT-001
  return testcenter.services.call(['folders', 'list'], [id, 'F']);
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

/*******************
 * 
 * RUNS LIST VIEW
 * 
 *******************/

function initialize_runs_list(selector) {
  var $grid = $(selector);

  $grid.gridlist({
    icon: 'tasks',
    callbacks: {
      loader: load_runs,
      data_to_nodes: runs_to_node,
      menu_items: menu_items_runs,
      menu_handlers: menu_handlers_runs
    }
  });
}

function load_runs(folder_id) {
  return testcenter.services.call(['folders', 'list'], [folder_id, 'R']);
}

function runs_to_node(response) {
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

function menu_items_runs($node) {
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

function menu_handlers_runs($node, action, options) {
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
 * PLAYER TEST VIEW
 * 
 *******************/

function initialize_player_tests_view(selector) {
  return $.cardview(selector, {
    callbacks: {
      loader: loader_run_tests,
      data_to_cards: tests_to_cards
    }
  });
}

function loader_run_tests(run) {
  return testcenter.services.call(['run', run.toString(), 'tests', 'list']);
}

function tests_to_cards(response) {
  var response = response['return'];
  var nodes = [];
  var defaults = {
  };

  switch (response.__type) {
    case 'entity-set':
      var entities = response.entities;
      // Build Nodes
      $.each(entities, function(i, entity) {
        var node = $.extend(true, {}, defaults, {
          id: entity[response.__key],
          title: entity[response.__display],
          description: entity['description']
        });
        nodes.push(node);
      });
      break;
    case 'entity':
      var node = $.extend(true, {}, defaults, {
        id: response[response.__key],
        title: response[response.__display],
        description: response['description']
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
 * PLAYER STEPS VIEW
 * 
 *******************/

function initialize_player_steps_view(selector) {
  return $.cardview(selector, {
    callbacks: {
      loader: loader_test_steps,
      data_to_cards: steps_to_cards
    }
  });
}

function loader_test_steps(test) {
  return testcenter.services.call(['steps', 'list'], test);
}

function steps_to_cards(response) {
  var response = response['return'];
  var nodes = [];
  var defaults = {
  };

  switch (response.__type) {
    case 'entity-set':
      var entities = response.entities;
      // Build Nodes
      $.each(entities, function(i, entity) {
        var node = $.extend(true, {}, defaults, {
          id: entity.sequence,
          test: entity['test'],
          key: entity[response.__key],
          title: entity[response.__display],
          description: entity['description']
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
        description: response['description']
      });
      nodes.push(node);
      break;
    default:
      console.log('Invalid Response');
  }
  return nodes;
}

/*******************************
 * CREATE FOLDER FORM
 *******************************/

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
function __button_cancel(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  // Hide the Form
  form_hide($form);
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
