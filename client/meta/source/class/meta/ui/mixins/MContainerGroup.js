/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/**
 * A Series of Functions, that go Hand-in-Hand with the Mixins that
 * Support Input/Output functions of IMetaWidgetIO
 */
qx.Mixin.define("meta.ui.mixins.MContainerGroup", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Contructor
   */
  construct: function() {
    // Initialize
    this.__mapWidgets = new utility.Map();

    // Attach Initialization Functions
    this._init_functions
      .add(100, this._mx_cg_loadEntities, 5000)
      .add(101, this._mx_cg_initCreateWidgets)
      .add(200, this._mx_cg_initReadyWidgets, 5000);
  },
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__mapWidgets = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __mapWidgets: null,
    /*
     ***************************************************************************
     PROTECTED METHODS (Initialization Functions)
     ***************************************************************************
     */
    _mx_cg_loadEntities: function(parameters) {
      // Variables
      var repository = this.getDI().get('metarepo');
      var entities = new utility.Map();
      var mapFields = new utility.Map(), mapForms = new utility.Map();

      // Get Group
      var group = this.getEntity();

      // Build Only Widget's Defined in the Layout
      var widgets = group.getLayout();

      // Cycle Through the Widgets
      var definition, entity, id;
      for (var i = 0; i < widgets.length; ++i) {
        id = widgets[i];
        definition = group.getWidget(id);

        // Do we have a Widget Definition?
        if (definition !== null) { // YES: Use it to prepare the widget
          switch (definition.type) {
            case 'field': // Extract Field ID
              id = definition.hasOwnProperty('field') ? utility.String.v_nullOnEmpty(definition.field, true) : null;
              if (id !== null) {
                mapFields.add(id, widgets[i]);
              }
              break;
            case 'form': // Extract Form
              id = definition.hasOwnProperty('form') ? utility.String.v_nullOnEmpty(definition.form, true) : null;
              if (id !== null) {
                mapForms.add(id, widgets[i]);
              }
              break;
            default: // All Others 
              // Assume that we are dealing with a Widget that we can Directly Create
              entity = repository.createEntity(id, 'widget', definition, group);
              // Were we able to create the widget?
              if (entity !== null) { // YES
                entities.add(id, entity);
              }
          }
        } else { // NO: Assume it's a field widget that will be created later on
          mapFields.add(id, null);
        }
      }

      // Save already created entities;
      parameters['entities'] = entities;

      // Do we have Fields or Forms to Load?
      if (mapForms.count() || mapFields.count()) { // YES
        // Create a Call Sequence to load Forms and/or Fields
        var calls = new utility.SequencedCallbacks(
          function(parameters) {
            if (parameters['entities'].count()) {
              this._init_functions.next(parameters);
            } else {
              throw "Container [" + group.getID() + "] has no Valid Entities!!";
            }
          },
          function(parameters, message) {
            this._init_functions.abort(message);
          },
          this);

        // Do we have forms to load?
        if (mapForms.count()) { // YES
          calls.add(100, function(parameters) {
            repository.getForms(mapForms.keys(),
              function(forms) {
                var entities = parameters['entities'];

                // Cycle through the forms adding them to the widget entities list
                var form, widgetID, definition;
                for (var id in forms) {
                  // Valid Form 'id'
                  if (forms.hasOwnProperty(id)) { // YES
                    // Form Entity
                    form = forms[id];
                    // Widget 'id'
                    widgetID = mapForms.get(id);
                    // Widget Definition
                    definition = group.getWidget(widgetID);
                    // Does the Widget have a Metadata Overlay
                    if (definition.hasOwnProperty('overlay')) { // YES
                      form.applyOverlay(definition.overlay);
                    }
                    entities.add(widgetID, form);
                  }
                }

                // Next Call
                calls.next(parameters);
              },
              function(message) {
                calls.abort(message);
              }, this);
          }, 5000);
        }

        // Do we have fields to load?
        if (mapFields.count()) { // YES
          calls.add(200, function(parameters) {
            repository.getFields(mapFields.keys(),
              function(fields) {
                var entities = parameters['entities'];

                // Cycle through the fields adding them to the widget entities list
                var field, widgetID, definition;
                for (var id in fields) {
                  if (fields.hasOwnProperty(id)) {
                    // Field Entity
                    field = fields[id];
                    // Widget 'id'
                    widgetID = mapFields.get(id);
                    // Widget Definition
                    definition = group.getWidget(widgetID);
                    // Does the Widget have a Metadata Overlay
                    if (definition.hasOwnProperty('overlay')) { // YES
                      field.applyOverlay(definition.overlay);
                    }
                    entities.add(widgetID, field);
                  }
                }

                // Next Call
                calls.next(parameters);
              },
              function(message) {
                calls.abort(message);
              }, this);
          }, 5000);
        }

        // Execute the Load
        calls.execute(parameters);
      } else { // NO: Nothing Else to Load

        // Were we able to create any widgets?
        if (entities.count()) { // YES
          this._init_functions.next(parameters);
        } else { // NO: Abort
          throw "Container [" + group.getID() + "] has no Valid Entities!!";
        }
      }

      /* PROBLEM:
       * (SOLVED) 1. Creating Entities from the repository might require calls to services
       * (if we are using a service repository).
       * (SOLVED) 2. In order to create widgets, we have to have the entity already created
       * (because the factory creates widgets immediately).
       * (SOLVED) 3. Different types of entities, require different calls to the repository
       * in order to be created (i.e. field, form, services, etc).
       * (SOLVED) 4. Different widgets, require different calls in order to be created.
       * (SOLVED) 5. Layout contains elements to be displayed
       * (SOLVED) 6. Widgets contains elements that might be displayed or USED for IO.
       * 7. If the layout only contains widget references, then we might
       * have a situation in which the widget ID might not be the same as ENTITY ID
       * (i.e. for example a tabs form, might contain 3 widgets, ex: widget1..3,
       *  in this case, widget1 might refer to the form user:create, or a group
       *  might contain 3 field widgets, ex: fields1..3, 
       *  in this case field1 might be a reference to field user:password, 
       *  if this group has some sort of validation or IO, then the ID we
       *  need is 'user:password' and not 'field1')
       * 8. We could implement a system in which the layout entry could, either
       *  refer to a widget (in the widgets reference) or a form / field and
       *  the system would, using some cascade system, try one then other, and 
       *  so on. PROBLEM, this might imply successive calls to services.
       * 9. The other option, is to have all elements in the layout, specified 
       *  in the widgets, with their respective types (but this goes back to 
       *  problem 7 and the ids)
       *  
       * ANOTHER PROBLEM:
       * If we consider that, there might be widgets, that are only used for
       * IO (Storage Purposes) and not for display, than we shouldn't have to
       * create Display Elements to serve as storage. 
       */
    },
    _mx_cg_initCreateWidgets: function(parameters) {
      // Get List of Entities
      var entities = parameters['entities'];
      var group = this.getEntity();
      var factory = this.getDI().get('widgetfactory');

      // Cycle Through the Widgets and Create Them
      var id, widget;
      var list = entities.keys();
      var created = new utility.Map();
      for (var i = 0; i < list.length; ++i) {
        id = list[i];
        widget = factory.create(entities.get(id), this);
        // Was the widget Created?
        if (widget !== null) { // YES
          created.add(widget.getID(), widget);
        }
      }

      // Were we able to create any widgets?
      if (created.count()) { // YES
        parameters['widgets'] = created;
        return parameters;
      }

      throw "Container [" + group.getID() + "] has no Valid Widgets!!";
    },
    _mx_cg_initReadyWidgets: function(parameters) {
      // Get Previously Created Widgets
      var widgets = parameters['widgets'];

      // Initialize Multiple Object Events Handling
      this._mx_eoReset();

      // Register all the widgets for handling
      this._mx_eoRegisterObjects(
        widgets.values(), // Attach to all created widgets
        "widget-ready", // Attach to initialization success and failure event
        function() { // OK : Function
          // Finished: Move onto the Next Initialization Function
          this._init_functions.next(parameters);
        },
        null, // NO Not OK Function
        function(event) { // Our Event Handler
          // Did we successfully initialize the widget?
          if (event.getOK()) { // YES
            this._mx_cg_addWidget(event.getTarget());
          }
          return true;
        });

      // Cycle through the widgets initializing all the widgets
      var list = widgets.values();
      for (var i = 0; i < list.length; ++i) {
        list[i].initialize();
      }
    },
    /*
     ***************************************************************************
     MIXIN FUNCTIONS (Filter and Default Map Builders)
     ***************************************************************************
     */
    _mx_cg_hasWidget: function(widget) {
      return this.__mapWidgets.has(this.__mx_cg_widgetID(widget));
    },
    _mx_cg_getWidget: function(widget) {
      return this.__mapWidgets.get(this.__mx_cg_widgetID(widget));
    },
    _mx_cg_indexOf: function(widget) {
      return this.__mapWidgets.indexOf(this.__mx_cg_widgetID(widget));
    },
    _mx_cg_count: function() {
      return this.__mapWidgets.count();
    },
    _mx_cg_listIDs: function() {
      return this.__mapWidgets.keys();
    },
    _mx_cg_listWidgets: function() {
      return this.__mapWidgets.values();
    },
    _mx_cg_addWidget: function(widget) {
      // Is the widget already part of the container?
      if (!this.__mapWidgets.has(widget.getID())) { // NO
        // (Re)Add the Widget to the Container
        this.__mapWidgets.add(widget.getID(), widget);

        // Do we have a handler postAdd?
        if (qx.lang.Type.isFunction(this._mx_cg_postAdd)) { // YES
          this._mx_cg_postAdd(widget);
        }
      }

      return true;
    },
    _mx_cg_removeWidget: function(widget) {
      // Remove the Widge from the container
      var result = this.__mapWidgets.remove(this.__mx_cg_widgetID(widget));

      // Did the widget exist in the container?
      if (result !== null) { // YES

        // Do we have a handler postRemove?
        if (qx.lang.Type.isFunction(this._mx_ccg_postRemove)) { // YES
          this._mx_ccg_postRemove(widget);
        }
      }

      return result;
    },
    /*
     ***************************************************************************
     PRIVATE FUNCTIONS
     ***************************************************************************
     */
    __mx_cg_widgetID: function(widget) {
      return qx.lang.Type.isString(widget) ? widget :
        (qx.lang.Type.isObject(widget) ? widget.getID() : null);
    }
    /*
     ***************************************************************************
     IMPLEMENTATION REQUIRED FUNCTIONS (to be implemented in container class)
     _mx_cg_postAdd(widget);
     _mx_cg_postRemove(widget);
     ***************************************************************************
     */
  } // SECTION: MEMBERS
});
