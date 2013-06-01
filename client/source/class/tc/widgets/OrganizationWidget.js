/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2013 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/* ************************************************************************
 ************************************************************************ */

qx.Class.define("tc.widgets.OrganizationWidget", {
  extend: qx.ui.form.SelectBox,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    "org-none": "qx.event.type.Event",
    "org-change": "qx.event.type.Data"
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  construct: function() {
    this.base(arguments);

    // Initialize the List
    this.refresh();
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __dataStore: null,
    __initialized: false,
    // overridden
    _onListChangeSelection: function(e) {
      // Call the Base Function 
      this.base(arguments, e);

      if (this.__initialized) { // Only Fire Events if the List is Initialized
        var selection = e.getData();
        if (selection.length > 0) { // Have a Selection
          var id = selection[0].getModel().getId();
          this.fireDataEvent("org-change", id);
        } else { // No Selection
          this.fireEvent("org-none");
        }
      }
    }, // FUNCTION: _onListChangeSelection
    refresh: function() {
      // Mark the List as Not Ready
      this.__initialized = false;

      // Get the List Widget
      var list = this.getChildrenContainer();

      // Clear the List Before Setting the New Model
      list.removeAll();

      // Add a Place Holder
      list.add(new qx.ui.form.ListItem('Loading...'));

      // Create the JSON Data Store
      var save_this = this;
      this.__dataStore = new qx.data.store.Json(__TC_SERVICES_ROOT + '/user/org/list', {
        manipulateData: function(response) {
          var error = response.error;
          var data = [];
          if (error.code) {
            save_this.error('TestCenter Request Returned an Error [' + error.code + ':' + error.message + ']');
            var entry = new tc.data.model.UOOrganizationEntry();
            entry.setId(-1);
            entry.setOrganization('Error');
            data.push(entry);
          } else {
            var entries = response['return'];
            for (var i = 0; i < entries.length; i++) {
              var entry = new tc.data.model.UOOrganizationEntry();
              entry.setId(entries[i].organization.id);
              entry.setOrganization(entries[i].organization.name);
              entry.setPermissions(entries[i].permissions);
              data.push(entry);
            }

            save_this.info('Processed [' + data.length + '] entries');
          }
          return data;
        }
      });
      this.__dataStore.addListener('error', this._loadError, this);
      this.__dataStore.addListener('loaded', this._loadOk, this);
    }, // FUNCTION: refresh
    _loadError: function(e) {
      // Log Failure
      this.error('Request for User-Organization List Failed.');
    }, // FUNCTION: _loadError
    _loadOk: function(e) {
      /* Note that:
       * 1. the List Can Only be Created AFTER the Data Store Has Loaded, or
       * 2. We have to change the model, associated with the controller, after the JSON store has loaded.
       */
      // Create a New Controller (Able to MAP the JSON Data Store to the List)
      var list = this.getChildrenContainer();

      // Create Controller for the List Widget
      var controller = new qx.data.controller.List(null, list);

      // create the delegate to change the bindings
      var delegate = {
        configureItem: function(item) {
          item.setPadding(3);
        },
        bindItem: function(controller, item, id) {
          controller.bindProperty("organization", "label", null, item, id);
          controller.bindProperty("", "model", null, item, id);
        }
      };
      controller.setDelegate(delegate);

      // Get a Model for the JSON Data Store
      var model = this.__dataStore.getModel();

      // Clear Placeholders Before Adding New Things
      list.removeAll();

      // Change the Model (Data) for the List
      this.__initialized = true;
      controller.setModel(model);

      // Log Success
      this.info('Request for User-Organization List Succeeded.');
    } // FUNCTION: _loadOk
  }
});

