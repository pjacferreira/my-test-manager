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
 * This implements handling of Meta Event at a Propagation Point of a Tree 
 * (i.e. the event will be propagated down, and then up).
 */
qx.Mixin.define("meta.ui.mixins.MWidgetActions", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Contructor
   */
  construct: function() {
    // Initialize
    this.__actionsGroups = new utility.Map();

    // Setup Local Init Functions
    this._init_functions
      .add(100, this._mx_wa_initActions)
      .add(110, this._mx_wa_initServiceActions, 5000);
  },
  /**
   * Destructor
   */
  destruct: function() {
    // Cleanup
    this.__actionsGroups = null;
    this.__actionServices = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __actionsGroups: null,
    __actionServices: null,
    /*
     ***************************************************************************
     INTERFACE METHODS  ( meta.api.ui.IWidgetActions)
     ***************************************************************************
     */
    /**
     * Does the Widget have any action Groups?
     * 
     * @return {Boolean} Returns <code>true</code> when the widget implements state validation.
     */
    hasActions: function() {
      return this.__actionsGroups.count() !== 0;
    },
    /**
     * Get list of Action Groups for the Widget
     * 
     * @abstract
     * @return {String[]} Returns List of Action Groups or 'null' if there are none
     */
    getActions: function() {
      return this.__actionsGroups.keys();
    },
    /**
     * Get the Definition for a Particular Action Group
     * 
     * @abstract
     * @param group {String} Name of Action Group
     * @return {String[]} Returns list of action entries for the group or 'null' 
     *   if group doesn't exist
     */
    getAction: function(group) {
      return this.__actionsGroups.get(group);
    },
    /**
     * Execute the Action Group
     * 
     * @abstract
     * @param group {String} Name of Action Group
     * @return {Boolean} 'true' if execution started (potential asynchronous execution),
     *   'false' onn any failure
     */
    executeAction: function(group) {
      // Is the Group Name Valid
      group = utility.String.v_nullOnEmpty(group, true);
      if (group !== null) { // YES

        // Does the Group Exist in the List?
        group = this.__actionsGroups.get(group);
        if (group !== null) { // YES

          // STEP 1: Prepare all Actions
          var action, sequence = [];
          for (var i = 0; i < group.actions.length; ++i) {
            action = this._mx_wa_prepareActionSequence(group.actiongs[i]);

            // Did we fail to prepare the action?
            if (action === null) { // YES: Abort
              return false;
            }
          }

          // STEP 2: Call Actions
          if (group['synchronized']) { // Synchronized Call
            this._mw_wa_callActionSequence(sequence);
          } else { // Asynchronous Call
            for (var i = 0; i < sequence.length; ++i) {
              if (!this._mx_wa_callAction(sequence[i])) {
                break;
              }
            }
          }

          return true;
        }
      }

      return false;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Initialization Functions)
     ***************************************************************************
     */
    _mx_wa_initActions: function(parameters) {
      var entity = this.getEntity();

      // Does the Widget have Actions?
      var actions = entity.getActions();
      if (actions !== null) { // YES

        // Cycle through the action Group
        var group, entry, set;
        for (var i = 0; i < actions.length; ++i) {
          group = actions[i];

          // Is the Group Valid for this Widget?
          if (this._mx_wa_isValidAction(group)) { // YES    
            entry = entity.getAction(group);
            set = this._mx_wa_parseActionSet(entry.actions);

            // If the Group Definition Valid?
            if (set !== null) { // YES
              this.__actionsGroups.add(group, {
                'synchronized': entry.hasOwnProperty('synchonized') ? !!entry.synchonized : true,
                'actions': set
              });
            }
          }
        }

        // Does the Widget Have Valid Action Groups?
        if (this.__actionsGroups.count()) { // YES
          return parameters;
        }
      }

      throw "Widget [" + entity.getID() + "] has no valid action definitions";
    },
    _mx_wa_initServiceActions: function(parameters) {
      var services = [];

      // Cycle through the Actions Groups and Extract Required Service ID's
      var groups = this.__actionsGroups.keys();
      var group;
      for (var i = 0; i < groups.length; ++i) {
        group = this.__actionsGroups.get(groups[i]);

        // Cycle through the actions in the group, extract required service id's
        var entry;
        for (var j = 0; j < group.actions.length; ++j) {
          entry = group.actions[i];
          if (entry.type === 'service') {
            services.push(entry.action);
          }
        }
      }

      // Do we have services to Initialize?
      if (services.length > 0) { // YES: Create Service Entities
        var repository = meta.Meta.getRepository();
        repository.getServices(services,
          function(entities) {
            // Assume we have all the required services
            var ready = true;

            // Cycle through the returned entities, looking for missing services
            for (var k = 0; k < services.length; ++k) {
              // Is the service missing?
              if (!entities.hasOwnProperty(services[i])) { // YES: Abort
                ready = false;
                break;
              }

              // Register Service Inputs
              this.__mx_wa_extractServiceInputs(entities[services[i]]);
            }

            if (ready) {
              this.__actionServices = entities;
              this._init_functions.next(parameters);
            } else {
              this._init_functions.abort("Widget [" + this.getEntity().getID() + "] references missing or invalid services");
            }
          },
          function(message) {
            this._init_functions.abort(message);
          },
          this);
      } else { // NO: No Service Initialization Required
        this._init_functions.next(parameters);
      }
    },
    /*
     ***************************************************************************
     HELPER FUNCTIONS
     ***************************************************************************
     */
    _mx_wa_parseActionSet: function(set) {
      var actions = [];

      // Cycle througn the possible action entries
      var entry;
      for (var i = 0; i < set.length; ++i) {
        entry = utility.String.v_nullOnEmpty(set[i], true);

        // Is this Possible Action Entry?
        if (entry !== null) { // YES
          entry = this._parseActionEntry(entry);

          // Did the entry Parse Correctly?
          if (entry !== null) { // YES
            actions.push(entry);
          }
        }
      }

      return actions.length > 0 ? actions : null;
    },
    _mx_wa_parseActionEntry: function(entry) {
      var definition = null;

      // action-entry = type \ action [ ( [parameters] ) ];
      var slash = entry.indexOf('\\');

      // Do we have a possible type \ action split?
      if ((slash > 0) && (slash < (entry.length - 1))) { // YES
        // Split the Action Type from the Action
        var working = {
          'type': utility.String.nullOnEmpty(entry.slice(0, slash - 1), true),
          'action': utility.String.nullOnEmpty(entry.slice(slash + 1), true)
        };

        // Do we have a valid definition so far?
        if ((working.type !== null) &&
          (working.action !== null)) { // YES
          definition = working;
        }
      }

      return definition;
    },
    _mx_wa_prepareActionSequence: function(action) {
      return action.hasOwnProperty('service') ?
        this.__mx_wa_prepareServiceAction(action) :
        this.__mx_wa_prepareEventAction();
    },
    _mw_wa_callActionSequence: function(sequence, i) {
      // Are qe starting the action sequence?
      if (i == null) { // YES:
        i = 0;
      }

      // Call Element in Sequence
      this._mx_wa_callAction(sequence[i], function() {
        // Have anymore Element's in the Sequence?
        if (i < sequence.length) { // YES: Call Next Element
          this._mw_wa_callActionSequence(sequence, i++);
        } else { // NO: Re-enable the button, to show that we are finished
          this.__button.setEnabled(true);
        }
      }, function(e) {
        // TODO: LOG Error
        this.__button.setEnabled(true);
      });
    },
    _mx_wa_callAction: function(action, ok, nok) {
      return action.hasOwnProperty('service') ?
        this.__mx_wa_callServiceAction(action, ok, nok) :
        this.__mx_wa_callEventAction(action, ok, nok);
    },
    /*
     ***************************************************************************
     PRIVATE FUNCTIONS
     ***************************************************************************
     */
    __mx_wa_extractServiceInputs: function(service) {
      // Does the service have any key fields?
      var require = service.requireKey();
      var fields = service.getKeyFields();
      if (fields !== null) { // YES
        for (var i = 0; i < fields.length; ++i) {
          this._mx_wa_registerInputFields(fields[i], require);
        }
      }


      // Does the service have any Parameter fields?
      require = service.requireParameters();
      fields = service.getParameters();
      if (fields !== null) { // YES
        this._mx_wa_registerInputFields(fields, require);
      }
    },
    __mx_wa_prepareServiceAction: function(action) {
      // Reset the Service Handler
      action.service.reset();

      // Does the Service Require a KEY?
      if (action.service.requireKey()) { // YES: Try to Initialize it
        try { // Initialize Key
          var inputs = this._mx_wa_getCurrentInputs();
          action.service.key(inputs);
        } catch (e) {
          // TODO LOG: Can't Build Key
          return false;
        }
      } else { // NO: Okay to Continue
        return true;
      }
    },
    __mx_wa_prepareEventAction: function(action) {
      // TODO: Implement

      // Filter out an input parameters used by the event

    },
    __mx_wa_callServiceAction: function(action, ok, nok) {
      var service = action.service;

      // Execute the Service Call (Service has been previously initialized)
      service.execute(ok, nok, this);

      return true;
    },
    __mx_wa_callEventAction: function(action, ok, nok) {
      // Fire Event
      this._fireMetaEvent(action.event,
        action.hasOwnProperty('event-data') ? action['event-data'] : null);

      // Call OK Function if Provided
      if (ok != null) {
        ok.call(this);
      }

      return true;
    }
    /*
     ***************************************************************************
     ABSTRACT METHODS
     ***************************************************************************
     */
    /*
     ***************************************************************************
     IMPLEMENTATION REQUIRED FUNCTIONS (to be implemented in container class)
     _mx_wa_isValidAction(group);
     _mx_wa_getCurrentInputs();
     _mx_wa_registerInputField(field, required);
     ***************************************************************************
     */
  } // SECTION: MEMBERS
});
