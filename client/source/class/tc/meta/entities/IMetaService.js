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

qx.Interface.define("tc.meta.entities.IMetaService", {
  extend: [tc.meta.entities.IMetaEntity],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when the service call completes successfully. The data field contains
     * the result of the service call.
     */
    "ok": "qx.event.type.Data",
    /**
     * Fired when the service call fails. The data field contains an error message.
     */
    "nok": "qx.event.type.Data"
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Returns an Array of Strings that Identify the Exact Service Request
     *
     * @abstract
     * @return {String[]} Service Name
     */
    getServicePath: function() {
    },
    /**
     * Service Requires a KEY (One or More Field Values required to call the service)
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    hasKey: function() {
    },
    /**
     * An Array of:
     * 1. Field ID's that compose the key (If only one combination is available)
     * 2. An Array of Array (2 Dimensional Array) of Field ID's, if there are 
     *    more than one possible combinations of fields to compose the key.
     *
     * @abstract
     * @return {String[][] ? null} Array of Array of Field IDs that compose the key.
     * If more than combination of field values can be used as key, then we
     * have 2 set of arrays (i.e. an array within an array)
     */
    getKeyFields: function() {
    },
    /**
     * Does the service require parameters. If Yes, buildParameterMap, has to be used 
     * to create the set of required Parameters for the Service Call.
     * 
     * @abstract
     * @return {Boolean ? false} 'true' Parameters are required, 'false' No Parameters Required
     */
    areParamsRequired: function() {
    },
    /**
     * Execute the Service Call, based on the Given Parameters.
     *
     * @abstract
     * @param mapFieldValue {Object|NULL} Field Value Map, or NULL if no Parameters Required
     * @param callback {Object ? null} Callback Object, if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @return {Boolean} 'true' Service Call Initiated, 'false' Failed to Initiate Call
     */
    execute: function(mapFieldValue, callback) {
    }
  }
});
