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
qx.Interface.define("meta.api.ui.IWidgetOutput", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notifies of Changes in the Possible Outputs
    "change-outputs": "meta.events.MetaEvent",
    // Notifies of Changes in the values of the Possible Outputs
    "change-output-values": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Meta Widget have an output (for a specific id)?
     * 
     * @abstract
     * @param id {String|null} Specific ID or 'null' if general
     * @return {Boolean} 'true' The form has an output, 'false' otherwise
     */
    hasOutput: function(id) {
    },
    /**
     * List of Output IDs
     * 
     * @abstract
     * @return {String[]|null} All IDs that this Widget has an Output for, or 'null' if no Outputs
     */
    getOutputs: function() {
    },
    /**
     * Output Value of the Meta Widget
     * 
     * @abstract
     * @param id {String|null} Specific ID or 'null' if all
     * @return {Object|null} Output field->value map, or null if no output
     */
    getOutput: function(id) {
    }
  } // SECTION: MEMBERS
});
