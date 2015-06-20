/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/
var current_entry = null;
var current_run_state = 0;

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

  // Selectors
  var $player = $('#player');
  var $tests = $('#test_cards');
  var $steps = $('#step_cards');

  // Initialize Player Views
  initialize_player_tests_view($player, $tests);
  initialize_player_steps_view($player, $steps);

  // Attach Other Listeners
  $('#folders').on('folder-view.folder-selected', onFolderSelected);
  $('#list_runs').on('gridlist.item-selected', onRunSelected);

  // Tests Listeners
  $tests.on('set-current.cardview', onSetCurrentTest);
  $tests.on('loaded.cardview', onTestsLoaded);
  $tests.on('restart.player', onTestsRestart);
  $tests.on('previous.player', onTestsPrevious);
  $tests.on('next.player', onTestsNext);

  // Steps Listeners
  $steps.on('loaded.cardview', onStepsLoaded);
  $steps.on('set-current.cardview', onSetCurrentStep);
  $steps.on('pass.player', onStepsPass);
  $steps.on('pass-comment.player', onStepsPassComment);
  $steps.on('next.player', onStepsNext);
  $steps.on('restart.player', onStepsRestart);
  $steps.on('previous.player', onStepsPrevious);
  $steps.on('fail.player', onStepsFail);

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

  var $player = $('#player');

  // Get the Current Entry for the Selected Entry
  testcenter.services.call(['play', node.id.toString(), 'current']).
    then(function (response) {
      $player.removeClass('loading');

      // TODO: Extract 1st Test/Step for Play from Response
      current_entry = response.return;
      current_run_state = node.state;

      // Add Loading Class
      $('#test_cards').addClass('loading');
      $('#step_cards').addClass('loading');

      // Save Run ID
      $player.data('run.id', node.id);

      // Reload Tests View
      var $view = $('#test_cards');
      $view.cardview('clear').
        cardview('load', node.id);
    }).
    fail(function (code, message) {
      $player.removeClass('loading');
      console.error('Failed to Open Run');
    });
}

function onTestsLoaded(event) {
  var $view = $(this);

  // Remove Loading Class
  $view.removeClass('loading');

  var $card = [];
  // Is the Current Entry Set?
  if ($.isset(current_entry)) { // YES - Use it to Select the Test
    $card = $view.cardview('card.byID', current_entry.test);
  } else { // NO - Select the 1st Test as Default
    $card = $view.cardview('card.first');
  }

  // Should we set a Current Card?
  if ($card.length) { // YES
    _setCurrentCard($view, $card);
  }
}

function onSetCurrentTest(event, card) {
  var $cv = $(this);
  var test = $cv.cardview('card.data', card);

  // Are we dealing with an Open Run?
  if ((current_run_state > 0) && (current_run_state < 9)) { // YES: Put the Button in 
    // Add Buttons to Current Card
    $cv.cardview('card.extras.set', card, $cv.data('cv.extras'));
  }

  // Reload Steps View
  var $steps = $('#step_cards');
  /* NOTE: We are removing the extras before reloading the steps card view because,
   * if we are reloading, and a card was current set, if we just clear this means 
   * that, extras will be jquery 'removed' and not 'detached' and we loose the
   * buttons events.
   */
  $steps.cardview('card.extras.remove');
  // Reload Steps View
  $steps.cardview('clear').
    cardview('load', test.id);
}

function onTestsRestart(event) {
  var $cv = $(this);
  var $first = $cv.cardview('card.first');
  if ($first.length) {
    var $current = $cv.cardview('card.current.get');
    if (($current.length === 0) || ($first.get(0) !== $current.get(0))) {
      // Get the Run for the Current Player
      var run = $cv.data('player').data('run.id');

      // Display Loading Icon
      $cv.addClass('loading');

      // Try to Restart Testing
      testcenter.services.call(['play', run.toString(), 'test', 'first']).
        then(function (response) {
          // Remove Loader
          $cv.removeClass('loading');

          // Is the Returned PLE different than the Current PLE?
          var ple = response['return'];
          if (current_entry.id !== ple.id) { // YES
            if (current_entry.test === ple.test) {
              // Save the New PLE
              current_entry = ple;
              // Selected the Step Card
              $('#step_cards').cardview('card.current.setByID', ple.step);
            } else {
              // Save the New PLE
              current_entry = ple;
              // Select the Test Card
              $cv.cardview('card.current.setByID', ple.test);
            }

            console.log('Restart Test');
          }
        }).
        fail(function (code, message) {
          // Remove Loader
          $cv.removeClass('loading');
          console.error('Failed to Restart Run');
        });
    }
  }
}

function onTestsPrevious(event) {
  var $cv = $(this);
  var $first = $cv.cardview('card.first');
  if ($first.length) {
    var $current = $cv.cardview('card.current.get');
    if (($current.length === 0) || ($first.get(0) !== $current.get(0))) {
      // Get the Run for the Current Player
      var run = $cv.data('player').data('run.id');

      // Display Loading Icon
      $cv.addClass('loading');

      testcenter.services.call(['play', run.toString(), 'test', 'previous']).
        then(function (response) {
          // Remove Loader
          $cv.removeClass('loading');

          // Is the Returned PLE different than the Current PLE?
          var ple = response['return'];
          if (current_entry.id !== ple.id) { // YES
            if (current_entry.test === ple.test) {
              // Save the New PLE
              current_entry = ple;
              // Selected the Step Card
              $('#step_cards').cardview('card.current.setByID', ple.step);
            } else {
              // Save the New PLE
              current_entry = ple;
              // Select the Test Card
              $cv.cardview('card.current.setByID', ple.test);
            }

            console.log('Previous Test');
          }
        }).
        fail(function (code, message) {
          // Remove Loader
          $cv.removeClass('loading');
          console.error('Failed to Move to Previous Test');
        });
    }
  }
}

function onTestsNext(event) {
  var $cv = $(this);
  var $current = $cv.cardview('card.current.get');
  if ($current.length) {
    var $next = $cv.cardview('card.next', $current);
    if ($next.length) {
      // Get the Run for the Current Run ID
      var run = $cv.data('player').data('run.id');

      // Display Loading Icon
      $cv.addClass('loading');
      testcenter.services.call(['play', run.toString(), 'test', 'next']).
        then(function (response) {
          // Is the Returned PLE different than the Current PLE?
          var ple = response['return'];
          if (current_entry.id !== ple.id) { // YES
            if (current_entry.test === ple.test) {
              // Save the New PLE
              current_entry = ple;
              // Selected the Step Card
              $('#step_cards').cardview('card.current.setByID', ple.step);
            } else {
              // Save the New PLE
              current_entry = ple;
              // Select the Test Card
              $cv.cardview('card.current.setByID', ple.test);
            }

            console.log('Next Test');
          }
        }).
        fail(function (code, message) {
          // Remove Loader
          $cv.removeClass('loading');
          console.error('Failed to Move to Next Step in Test');
        });
    }
  }
}

function onStepsLoaded(event) {
  var $view = $(this);

  // Remove Loading Class
  $view.removeClass('loading');

  var $card = [];

  // Is the Current Entry Set?
  if ($.isset(current_entry)) { // YES: Use it to Select the Current Step
    $card = $view.cardview('card.byID', current_entry.step);
  } else { // NO: Select the 1st Step
    $card = $view.cardview('card.first');
  }

  // Should we set a Current Card?
  if ($card.length) { // YES
    _setCurrentCard($view, $card);
  }
}

function onSetCurrentStep(event, card) {
  var $cv = $(this);

  // Are we dealing with an Open Run?
  if ((current_run_state > 0) && (current_run_state < 9)) { // YES: Put the Button in 
    // Add Buttons to Current Card
    $cv.cardview('card.extras.set', card, $cv.data('cv.extras'));
  }
}

function onStepsPass(event) {
  var $cv = $(this);

  // Get the Run for the Current Player
  var run = $cv.data('player').data('run.id');

  // Display Loading Icon
  $cv.addClass('loading');
  testcenter.services.call(['play', run.toString(), 'current', 'pass'], null, null, {
    type: 'POST'
  }).
    then(function (entity) {
      // Remove Loader
      $cv.removeClass('loading');
      console.log('Pass Step');

      // TODO: With Successfull Pass (Move onto the Next Step)
    }).
    fail(function (code, message) {
      // Remove Loader
      $cv.removeClass('loading');
      console.error('Failed to Mark Step as Passed');
    });
}

function onStepsPassComment(event) {
  var $cv = $(this);

  // Get the Run for the Current Run ID
  var run = $cv.data('player').data('run.id');

  var $form = $('#form_step_complete');
  $form.data('pass_mode',true);
  form_show($form);
  /*
   // Display Loading Icon
   $cv.addClass('loading');
   testcenter.services.call(['play', run.toString(), 'current', 'pass'], pass_code, post_values, {
   type: 'POST'
   }).
   then(function (entity) {
   // Remove Loader
   $cv.removeClass('loading');
   console.log('Pass Step with Comment');
   
   // TODO: With Successfull Pass (Move onto the Next Step)
   }).
   fail(function (code, message) {
   // Remove Loader
   $cv.removeClass('loading');
   console.error('Failed to Mark Step as Passed');
   });
   */
}

function onStepsRestart(event) {
  var $cv = $(this);

  var $first = $cv.cardview('card.first');
  if ($first.length) {
    var $current = $cv.cardview('card.current.get');
    if (($current.length === 0) || ($first.get(0) !== $current.get(0))) {
      // Get the Run for the Current Run ID
      var run = $cv.data('player').data('run.id');

      // Display Loading Icon
      $cv.addClass('loading');
      testcenter.services.call(['play', run.toString(), 'test', 'step', 'first']).
        then(function (response) {
          // Remove Loader
          $cv.removeClass('loading');

          // Is the Returned PLE different than the Current PLE?
          var ple = response['return'];
          if (current_entry.id !== ple.id) { // YES
            // Save the New PLE
            current_entry = ple;
            // Selected the Step Card
            $cv.cardview('card.current.setByID', ple.step);

            console.log('Restart Steps');
          }
        }).
        fail(function (code, message) {
          // Remove Loader
          $cv.removeClass('loading');
          console.error('Failed to Move to First Step in Test');
        });
    }
  }
}

function onStepsPrevious(event) {
  var $cv = $(this);

  var $current = $cv.cardview('card.current.get');
  if ($current.length) {
    var $previous = $cv.cardview('card.previous', $current);
    if ($previous.length) {
      // Get the Run for the Current Run ID
      var run = $cv.data('player').data('run.id');

      // Display Loading Icon
      $cv.addClass('loading');
      testcenter.services.call(['play', run.toString(), 'test', 'step', 'previous']).
        then(function (response) {
          // Remove Loader
          $cv.removeClass('loading');

          // Is the Returned PLE different than the Current PLE?
          var ple = response['return'];
          if (current_entry.id !== ple.id) { // YES
            // Save the New PLE
            current_entry = ple;
            // Selected the Step Card
            $cv.cardview('card.current.setByID', ple.step);

            console.log('Previous Step');
          }
        }).
        fail(function (code, message) {
          // Remove Loader
          $cv.removeClass('loading');
          console.error('Failed to Move to Previous Step in Test');
        });
    }
  }
}

function onStepsNext(event) {
  var $cv = $(this);

  // TODO At Last Step - Move to Next Next Step, 1st Step

  var $current = $cv.cardview('card.current.get');
  if ($current.length) {
    var $next = $cv.cardview('card.next', $current);
    if ($next.length) {
      // Get the Run for the Current Run ID
      var run = $cv.data('player').data('run.id');

      // Display Loading Icon
      $cv.addClass('loading');
      testcenter.services.call(['play', run.toString(), 'test', 'step', 'next']).
        then(function (response) {
          // Remove Loader
          $cv.removeClass('loading');

          // Is the Returned PLE different than the Current PLE?
          var ple = response['return'];
          if (current_entry.id !== ple.id) { // YES
            // Save the New PLE
            current_entry = ple;
            // Selected the Step Card
            $cv.cardview('card.current.setByID', ple.step);

            console.log('Next Step');
          }
        }).
        fail(function (code, message) {
          // Remove Loader
          $cv.removeClass('loading');
          console.error('Failed to Move to Next Step in Test');
        });
    }
  }
}

function onStepsFail(event) {
  var $cv = $(this);

  // Get the Run for the Current Run ID
  var run = $cv.data('player').data('run.id');

  var $form = $('#form_step_complete');
  $form.data('pass_mode',false);
  form_show($form);
  
/*
  // Display Loading Icon
  $cv.addClass('loading');
  testcenter.services.call(['play', run.toString(), 'step', 'current', 'fail'], fail_code, post_values, {
    type: 'POST'
  }).
    then(function (entity) {
      // Remove Loader
      $cv.removeClass('loading');
      console.log('Fail Step with Comment');

      // TODO: With Fail, Allow for Terminating Run with Same Code/Comment
    }).
    fail(function (code, message) {
      // Remove Loader
      $cv.removeClass('loading');
      console.error('Failed to Mark Step as Passed');
    });
*/  
}

function _changeCurrentCard($cardview, $current, $new) {
  if ($new.length) {
    // Remove Extras from Current Card
    $cardview.cardview('card.extras.remove');

    // Clear Current Card
    $cardview.cardview('card.current.clear');

    // Make the New Card Current
    _setCurrentCard($cardview, $new);
  }

  return $cardview;
}

function _setCurrentCard($cardview, $card) {
  if ($card.length) {
    // Make the Card Current
    $cardview.cardview('card.current.set', $card);

    // NOTE: Extras are Added on the Set-Current Event
  }

  return $cardview;
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

/*******************
 * 
 * RUNS LIST VIEW
 * 
 *******************/

function initialize_runs_list(selector) {
  var $grid = $(selector);

  $grid.gridlist({
    icon: 'tasks',
    icon_color: function () {
      if ($.isset(this.state)) {
        switch (this.state) {
          case 0:
            return 'gray';
          case 9:
            return 'red';
          default:
            return 'green';
        }
      } else {
        return 'black';
      }
    },
    callbacks: {
      loader: load_runs,
      data_to_nodes: runs_to_node,
      menu_items: menu_items_runs,
      menu_handlers: menu_handlers_runs
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

function menu_items_runs($node) {
  var items = false;

  if ($.isset($node)) {
    items = {
      'open': {'name': 'Open'},
      'seperator': '---------',
      'close': {'name': 'Close'}
    };

    var element = $node.data('node.element');
    switch (element.state) {
      case 1:
        items.open.disabled = true;
        break;
      case 0:
      case 9:
        items.close.disabled = true;
        break;
    }
  }

  return items;
}

function onRunsOpen($node) {
  var element = $node.data('node.element');

  testcenter.services.call(['play', element.id.toString(), 'open']).
    then(function (entity) {
      var $folders = $('#folders');
      var $runs = $('#list_runs');
      // Reload Runs Grid Display
      $folders.trigger('folder-view.folder-selected', $runs.data('folder.parent'));
      // Select Run for Player
      $runs.trigger('gridlist.item-selected', element);
    }).
    fail(function (code, message) {
      console.error('Failed to Open Run');
    });
}

function onRunsClose($node) {
  var element = $node.data('node.element');

  testcenter.services.call(['play', element.id.toString(), 'close']).
    then(function (entity) {
      var $folders = $('#folders');
      var $runs = $('#list_runs');
      // Reload Runs Grid Display
      $folders.trigger('folder-view.folder-selected', $runs.data('folder.parent'));
      // Select Run for Player
      $runs.trigger('gridlist.item-selected', element);
    }).
    fail(function (code, message) {
      console.error('Failed to Close Run');
    });
}

function menu_handlers_runs($node, action, options) {
  if ($node) {
    console.log('Selected action "' + action + '" on Set [' + $node.attr('id') + ']');
    window.$selected_set = $node;
  } else {
    console.log('Selected action "' + action + '"');
  }

  var handler = 'onRuns' + $.ucfirst(action);
  if ($.isFunction(window[handler])) {
    console.log('CALLED Runs' + $.ucfirst(action) + ' Handler');
    window[handler]($node);
  } else {
    var $form = $('#form_' + action + '_test');
    if ($form.length) {
      // NOTE: Handler Called in the Context of Grid List Object
      $form.data('folder.parent', this.data('folder.parent'));
      form_show($form);
    } else {
      console.log('Missing Form for action[' + action + ']');
    }
  }
}

/*******************
 * 
 * PLAYER TEST VIEW
 * 
 *******************/

function initialize_player_tests_view($player, selector) {
  var $cv = $.cardview(selector, {
    callbacks: {
      loader: loader_run_tests,
      data_to_cards: tests_to_cards
    }
  });
  $cv.data('player', $player);

  var $restart = $('<i class="angle double up large icon"></i>').click(
    function (event) {
      $cv.trigger('restart.player');
      return false;
    });
  var $previous = $('<i class="angle up large icon"></i>').click(
    function (event) {
      $cv.trigger('previous.player');
      return false;
    });
  var $next = $('<i class="angle down large icon"></i>').click(
    function (event) {
      $cv.trigger('next.player');
      return false;
    });

  // Create Button Bar
  var $button_bar = $('<div style="text-align: center"></div>');
  $button_bar.append($next).append($restart).append($previous);

  // Set the Button Bar as Extras
  $cv.data('cv.extras', $button_bar);
  return $cv;
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
      $.each(entities, function (i, entity) {
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

function initialize_player_steps_view($player, selector) {
  var $cv = $.cardview(selector, {
    callbacks: {
      loader: loader_test_steps,
      data_to_cards: steps_to_cards
    }
  });
  $cv.data('player', $player);

  // Create Buttons
  var $pass_no_comment = $('<i class="large green thumbs outline up icon"></i>').click(
    function (event) {
      $cv.trigger('pass.player');
      return false;
    });
  var $pass_comment = $('<i class="large green thumbs up icon"></i>').click(
    function (event) {
      $cv.trigger('pass-comment.player');
      return false;
    });
  var $restart = $('<i class="angle double up large icon"></i>').click(
    function (event) {
      $cv.trigger('restart.player');
      return false;
    });
  var $previous = $('<i class="angle up large icon"></i>').click(
    function (event) {
      $cv.trigger('previous.player');
      return false;
    });
  var $next = $('<i class="angle down large icon"></i>').click(
    function (event) {
      $cv.trigger('next.player');
      return false;
    });
  var $fail_comment = $('<i class="thumbs down large red icon"></i>').click(
    function (event) {
      $cv.trigger('fail.player');
      return false;
    });

  // Create Button Bar
  var $left = $('<div class="column"></div>').append($pass_no_comment).append($pass_comment);
  var $middle = $('<div class="center aligned column"></div>').append($next).append($restart).append($previous);
  var $right = $('<div class="right aligned column"></div>').append($fail_comment);

  var $button_bar = $('<div class="ui three column grid">').append($left).append($middle).append($right);

  // Set the Button Bar as Extras
  $cv.data('cv.extras', $button_bar);
  return $cv;
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
      $.each(entities, function (i, entity) {
        var node = $.extend(true, {}, defaults, {
          id: entity[response.__key],
          test: entity['test'],
          title: entity[response.__display],
          description: entity['description']
        });
        nodes.push(node);
      });
      break;
    case 'entity':
      var node = $.extend(true, {}, defaults, {
        id: response[response.__key],
        test: response['test'],
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
 * FORM STEP PASS
 *******************************/

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_step_pass(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  /* Load the Node if Not Already Loaded.
   * Why? 
   * Beacuse if we add a child node to a node, that hasn't been loaded,
   * FancyTree will assume the node is loaded, and no the ajax request to load.
   */
  var node = $form.data('fv.node.selected');
  node.load();
  form_submit($form, {
    pass_code: {
      identifier: 'pass_code',
      rules: [
        {
          type: 'empty',
          prompt: 'Please choose you gender'
        }
      ]
    },
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
    onSuccess: __do_mark_step,
    onFailure: function () {
      alert("Pass Code: Missing or Invalid Fields");
    }
  });
}

function __do_mark_step() {
  var $form = $('#form_step_complete');
  // Extract Field Values
  var values = {};
  $form.find('.field > :input').each(function () {
    values[this.name] = $(this).val();
  });
  var node = $form.data('fv.node.selected');
  var key = node.key.split(':');
  console.log('Create Child Node [' + values.name + '] under Parent Node [' + key[1] + ':' + node.title + ']');
  /*  
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
   */
}

/*******************************
 * FORM INITIALIZATION
 *******************************/

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_step_complete($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __load_form_step_complete($form) {
  console.log('Loading Form [' + $form.attr('id') + ']');
}

/*******************************
 * HELPER FORMS
 *******************************/

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
  $form.find('.field > :input').each(function () {
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

/* TODO
 * On Small Screens (width < 800 px) the middle buttons for step show up on different
 * lines (i.e. next and restart / previous) this is because class
 * i.icon has a margin added by semantic 
 * (semantic.css : 5612) margin: 0em 0.25rem 0em 0em;
 * 
 * See if there is another combination of classes that removes this padding
 * I think this padding is only added to allow for space between the icon and
 * a potential label.
 */