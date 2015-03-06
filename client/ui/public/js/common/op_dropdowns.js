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

function fill_list($list, entities, key_field, display_field) {
  var $template = $.templates('<option value="{{:' + key_field + '}}">{{:' + display_field + '}}</option>');
  var html, entity;
  for (var i = 0; i < entities.length; i++) {
    entity = entities[i];
    html = $template.render(entity);
    $list.append(html);
  }
}

function select_project(element, value, selectedText, $selectedItem) {
  console.log("[SELECT ORGANIZATION [" + value + "]");

  if ($.isNumeric(element) && element >= 0) {
    // Set Session Project
    testcenter.services.call(['session', 'set', 'project'], element, null, {
      call_ok: function (reply) {
        console.log('ORGANIZATION and PROJECT SET');
      },
      call_nok: function (code, message) {
        console.log('ERROR: select_project');
      }
    });
  }
}

function initialize_projects() {
  testcenter.services.call(['org', 'projects', 'list'], null, null, {
    call_ok: function (reply) {
      // Do we have a valid reply?
      if (is_entity_set(reply)) { // YES
        // Get the Drop Down
        var $dropdown = $('#projects_list');

        // Fill in the List
        fill_list($dropdown, reply.entities, reply.__key, reply.__display);

        /* NOTE: Activating the Dropdown - Wraps the Original <SELECT> in a
         * <DIV> and all classes are moved to this PARENT <DIV>.
         */
        $dropdown.parent().removeClass('disabled loading');
      }
    },
    call_nok: function (code, message) {
      console.log('ERROR: initialize_projects');
    }
  });
}

function select_organization(element, value, selectedText, $selectedItem) {
  console.log("[SELECT ORGANIZATION [" + value + "]");

  if ($.isNumeric(element) && element >= 0) {
    // Set Session Organization
    testcenter.services.call(['session', 'set', 'org'], element, null, {
      call_ok: function (reply) {
        /* NOTE: Activating the Dropdown - Wraps the Original <SELECT> in a
         * <DIV> and all classes are moved to this PARENT <DIV>.
         */
        $('#projects_list').parent().addClass('disabled loading');
        
        initialize_projects();
      },
      call_nok: function (code, message) {
        console.log('ERROR: select_organization');
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
    call_ok: function (reply) {
      // Do we have a valid reply?
      if (is_entity_set(reply)) { // YES
        // Get the Drop Down
        var $dropdown = $('#orgs_list');

        // Fill in the List
        fill_list($dropdown, reply.entities, reply.__key, reply.__display);
      }
    },
    call_nok: function (code, message) {
      console.log('ERROR: initialize_organizations');
    }
  });
}

function initialize_dropdowns() {
  $('#orgs_list').dropdown({
    onChange: select_organization
  });

  $('#projects_list').dropdown({
    onChange: select_project
  });

  initialize_organizations();
}
