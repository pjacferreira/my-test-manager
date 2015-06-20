/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/

function is_entity_set(obj) {
  if ($.isObject(obj) && obj.hasOwnProperty('__type')) {
    return obj.__type === 'entity-set';
  }

  return false;
}

function fill_list($list, entities, key_field, display_field, select) {
  // Is the List Filled?
  if ($list[0].__filled) { // YES
    // Clear the List Before Use (Leaving Only Placeholder)
    $list.find("option[value!='']").remove();
    $list[0].__filled = false;
  }

  // Fill the List
  var $template = $.templates('<option value="{{:' + key_field + '}}">{{:' + display_field + '}}</option>');

  // Fill in Dropdown List
  $.each(entities, function(i, entity) {
    $list.append($template.render(entity));
  });

  /* SEMANTIC-UI DROPDOWN NOTES
   * Everytime the Drop Down's SELECT is rebuilt, the setup select behaviour
   * has to be called in order to rebuild the UI and Drop Down's Internal State.
   */
  $list.parent().dropdown("setup select");
  $list.parent().dropdown("restore defaults");

  // Do we have an item to select?
  if ($.isset(select)) { // YES
    $list.parent().dropdown("set selected", select.toString());
  }

  // Mark the List as Filled
  $list[0].__filled = true;
}

function select_project(element, value, selectedText, $selectedItem) {
  console.log("[SELECT ORGANIZATION [" + value + "]");

  if ($.isNumeric(element) && element >= 0) {
    // Set Session Project
    testcenter.services.call(['session', 'set', 'project'], element, null, {
      call_ok: function(reply) {
        // Update Session Project
        window.__session = $.extend({}, window.__session, {
          project: reply
        });

        // Disable Choose Label
        $('#choose').addClass('hidden');

        // Trigger Project Change Event
        $(document).trigger('testcenter.project.change');

        console.log('ORGANIZATION and PROJECT SET');
      },
      call_nok: function(code, message) {
        console.log('ERROR: select_project');
      }
    });
  }
}

function initialize_projects() {
  testcenter.services.call(['org', 'projects', 'list'], null, null, {
    call_ok: function(reply) {
      // Do we have a valid reply?
      if (is_entity_set(reply)) { // YES
        // Get the Drop Down
        var $list = $('#projects_list');

        // CLEAR and Fill in the List
        /* SEMANTIC-UI NOTE:
         * - When the dropdown is initialized (i.e. $element.dropdown()) called
         * for the 1st time), unless the HTML object is a <div> (ex: HTML <select>),
         * the HTML object user to create the dropdown will be wrapped in a <div> and,
         * - This new created object will be the HTML element that the SEMANTIC-UI
         * dropdown object refers to (NOT the <select>), hence the need to call
         * parent(), before using the behaviour (or a console warning is 
         * generated).
         */

        // Fill or Re-fill the List
        var select = $.objects.get('__session.project.id', window, null);
        fill_list($list, reply.entities, reply.__key, reply.__display, select);

        /* NOTE: Activating the Dropdown - Wraps the Original <SELECT> in a
         * <DIV> and all classes are moved to this PARENT <DIV>.
         */
        $list.parent().removeClass('disabled loading');
      }
    },
    call_nok: function(code, message) {
      console.log('ERROR: initialize_projects');
    }
  });
}

function select_organization(element, value, selectedText, $selectedItem) {
  console.log("[SELECT ORGANIZATION [" + value + "]");

  if ($.isNumeric(element) && element >= 0) {
    var $list = $('#orgs_list');

    // Did we select the current value?
    if ($.isset($list[0].__current) && ($list[0].__current === element)) { // YES
      return;
    }

    // Save Current Selection
    $list[0].__current = element;

    // Set Session Organization
    testcenter.services.call(['session', 'set', 'org'], element, null, {
      call_ok: function(reply) {
        // Update Session Organization
        window.__session = $.extend({}, window.__session, {
          organization: reply
        });

        // Trigger Organization Change Event
        $(document).trigger('testcenter.organization.change');

        // Make sure the user knows he has to select something 1st
        $('#choose').removeClass('hidden');

        /* NOTE: Activating the Dropdown - Wraps the Original <SELECT> in a
         * <DIV> and all classes are moved to this PARENT <DIV>.
         */
        $('#projects_list').parent().addClass('disabled loading');

        initialize_projects();
      },
      call_nok: function(code, message) {
        console.error('ERROR: select_organization');
      }
    });
  }
}

/**
 * 
 * @returns {undefined}
 */
function initialize_organizations() {
  testcenter.services.call(['orgs', 'list'], null, null, {
    call_ok: function(reply) {
      // Do we have a valid reply?
      if (is_entity_set(reply)) { // YES
        // Get the Drop Down
        var $list = $('#orgs_list');

        // Fill in the List
        var select = $.objects.get('__session.organization.id', window, null);
        fill_list($list, reply.entities, reply.__key, reply.__display, select);
      }
    },
    call_nok: function(code, message) {
      console.log('ERROR: initialize_organizations');
    }
  });
}

function initialize_dropdowns() {
  /* SEMANTIC-UI NOTE:
   * OnChange is called when:
   * 1. You directly click on an option.
   * 2. When the Dropdown Loses Focus 
   * 
   * Therefore, it's possible for you to receive 2 onChange calls with a
   * selection change.
   */
  $('#orgs_list').dropdown({
    onChange: select_organization
  });

  $('#projects_list').dropdown({
    onChange: select_project
  });

  initialize_organizations();
}

/* TODO
 * 1. If the Organization or Project Lists Contain only a single element,
 * automatically select it.
 * 2. (DONE) If the Session has an Organization and Project Set, automatically choose
 * it.
 * 3. On Change of Session Organization/Project update the Session Information.
 * 4. On Change of Session Organization/Project fire event so as to allow the
 * page to take into considerations those changes.
 * 5. If the Current Session Organizatio/Project has been "choosen" don't call
 * the service session/set/[org|project] since nothing has changed
 */