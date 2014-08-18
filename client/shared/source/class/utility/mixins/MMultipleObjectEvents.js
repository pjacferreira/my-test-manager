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

/**
 * Creates a Structured Implementation of meta.api.widgets.IContainer
 */
qx.Mixin.define("utility.mixins.MMultipleObjectEvents", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * Container Contructor
   */
  construct : function() {
    // Initialize Members
    this.__mx_eo_mapRegistry = new utility.Map();
    this.__mx_eo_mapEventObject = new utility.Map();
  },

  /**
   * Container Destructor
   */
  destruct : function() {
    this.base(arguments);

    // Cleanup Members
    this.__mx_eo_mapRegistry = null;
    this.__mx_eo_mapEventObject = null;
    this.__mx_eo_onOK = null;
    this.__mx_eo_onNOK = null;
    this.__mx_eo_handler = null;
  },

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members : {
    __mx_eo_mapRegistry : null,
    __mx_eo_mapEventObject : null,
    __mx_eo_onOK : null,
    __mx_eo_onNOK : null,
    __mx_eo_handler : null,
    _mx_eoReset : function() {
      // Clean out any remaining objects
      this.__mx_eoUnregisterPendingObjects();
      this.__mx_eo_mapRegistry.reset();
      this.__mx_eo_mapEventObject.reset();
      this.__mx_eo_onOK = null;
      this.__mx_eo_onNOK = null;
      this.__mx_eo_handler = null;
    },
    _mx_eoRegisterObjects : function(objects, events, ok, nok, handler) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertArray(objects, "[objects] Is not of the expected type!");
        qx.core.Assert.assertTrue(qx.lang.Type.isString(events) || qx.lang.Type.isArray(events), "[events] Is not of the expected type!");
        qx.core.Assert.assertFunction(ok, "[ok] Is not of the expected type!");
      }

      /* Note: Since these re protected methods, I only do the minimum in
       * parameter validation
       */

      // Is events a String?
      if (qx.lang.Type.isString(events)) {       // YES

        // Convert to an Array
        events = [events];
      }

      // ELSE (NO): Assume that events is an array

      // Process Functions
      this.__mx_eo_onOK = ok;
      this.__mx_eo_onNOK = qx.lang.Type.isFunction(nok) ? nok : null;
      this.__mx_eo_handler = qx.lang.Type.isFunction(handler) ? handler : null;

      // Initialize the Platform by registering the events with the objects
      var object;
      for (var i = 0; i < objects.length; ++i) {
        object = objects[i];
        if (object instanceof qx.core.Object) {
          this.__mx_eoObjectRegisterEvents(object, events);
        }
      }
    },
    __mx_eoObjectRegisterEvents : function(object, events) {
      // Does the Object Already Exist in the Registry?
      if (!this.__mx_eo_mapRegistry.has(object.toHashCode())) {      // No: Create a New Entry

        // Create a Registry Entry
        var registration = {
          'object' : object,
          'events' : {

          }
        };

        // Loop through the events array registering all events
        var event;
        for (var i = 0; i < events.length; ++i) {
          event = events[i];

          // Are we duplication a listener?
          if (!registration.events.hasOwnProperty(event)) {          // NO: attach listener
            registration.events[event] = object.addListenerOnce(event, this.__mx_eoEventCapture, this);
          }
        }

        // Add the Object to the Registry
        this.__mx_eo_mapRegistry.add(object.toHashCode(), registration);
        return true;
      }
      return false;
    },
    __mx_eoObjectUnregisterEvents : function(object) {
      var registration = this.__mx_eo_mapRegistry.get(object.toHashCode());

      // Does the object exist in the registry?
      if (registration !== null) {               // YES

        // Loop through all the events registered for the object and un-register them
        for (var event in registration.events) {
          if (registration.events.hasOwnProperty(event)) {
            object.removeListenerById(registration.events[event]);
          }
        }

        // Remove the object from the registry
        this.__mx_eo_mapRegistry.remove(object.toHashCode());
        return true;
      }
      return false;
    },
    __mx_eoEventCapture : function(event) {
      var object = event.getTarget();
      var type = event.getType()

      // Assign Object to Captured Event
      var object_list = this.__mx_eo_mapEventObject.get(type);

      // Does a list object already exist for this event?
      if (object_list !== null) {                // YES: Add Object to List
        object_list.push(object);
      } else {                                   // NO: Create list
        this.__mx_eo_mapEventObject.add(type, [object]);
      }

      // Unregister anu further events for this obejct
      this.__mx_eoObjectUnregisterEvents(event.getTarget());

      // Do we have an external handler for events?
      if (this.__mx_eo_handler !== null) {       // YES

        // Did the external event handler request an Abort
        if (!this.__mx_eo_handler.call(this, event)) {               // YES
          this.__mx_eoAbort();
          return;
        }
      }

      // Continue Processing
      this.__mx_eoNext();
    },
    __mx_eoAbort : function() {
      // Clean out any remaining objects
      this.__mx_eoUnregisterPendingObjects();

      // Do we have an Not OK Function?
      if (this.__mx_eo_onNOK !== null) {         // YES
        this.__mx_eo_onNOK.call(this);
      } else {                                   // NO: Use OK Function
        this.__mx_eo_onOK.call(this);
      }
    },
    __mx_eoNext : function() {
      // Do we still have any pending objects?
      if (!this.__mx_eo_mapRegistry.count()) {   // NO: All done then

        // Call the OK Handler
        this.__mx_eo_onOK.call(this);
      }

      // ELSE: Continue Waiting
    },
    __mx_eoUnregisterPendingObjects : function() {
      // Get List of Pending Objects
      var objects = this.__mx_eo_mapRegistry.values();

      // Unregister Events for the Remaining Objects
      for (var i = 0; i < objects.length; ++i) {
        this.__mx_eoObjectUnregisterEvents(objects[i].object);
      }
    }
  }                                              // SECTION: MEMBERS
});
