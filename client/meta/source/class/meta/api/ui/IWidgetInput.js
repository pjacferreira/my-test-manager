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
 * Requirements for Communication Input/Output Communication Between Meta Widgets
 */
qx.Interface.define("meta.api.ui.IWidgetInput", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notifies of Changes in the Allowed Inputs
    "change-inputs": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Meta Widget accept input parameters?
     * 
     * @abstract
     * @return {Boolean} 'true' The form accepts input parameters, 'false' otherwise
     */
    acceptsInput: function() {
    },
    /**
     * List of allowed Inputs, if any
     * 
     * @abstract
     * @return {String[]|null} List of Input IDs or 'null' if none
     */
    allowedInputs: function() {
    },
    /**
     * Input value to the Meta Widget.
     * 
     * @abstract
     * @param map {Object|null} property->value map, or null, if no input allowed
     */
    setInput: function(map) {
    }
  } // SECTION: MEMBERS
});
