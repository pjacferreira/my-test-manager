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

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("meta.api.entity.IService", {
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
    path: function() {
    },
    /**
     * Service Requires a KEY (One or More Field Values required to call the service)
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    requireKey: function() {
    },
    /**
     * Service Requires Parameter(s)
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    requireParameters: function() {
    },
    /**
     * Service Allows Parameters Lists (i.e. more than one parameter can be set)
     *
     * @abstract
     * @return {Boolean ? false} 'true' YES, 'false' Otherwise
     */
    allowParameterList: function() {
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
     * Reset the Service Object, for another call (clear the key and parameter
     * entries).
     * 
     * @abstract
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     */
    reset: function() {
    },
    /**
     * Get the current set key.
     * 
     * @abstract
     * @return {Object} Field Value Mapping of the Current Key being used or NULL, if no key set
     */
    getKey: function() {      
    },
    /**
     * Build the Key for Service Call, using the input map.
     * 
     * @abstract
     * @param mapFieldValues {Object} Field Value Map used to build the key
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     * @throw Exception if cannot build a key, with the provided map
     */
    key: function(mapFieldValues) {
    },
    /**
     * Get the current set of Parameters defined.
     * 
     * @abstract
     * @return {String[]} Array of Fields ID's that compose the parameters or 
     *   'null' if no parameters allowed.
     */
    getParameters: function() {      
    },
    /**
     * Reset's the Parameter List for the Service Call (does not affect the 
     * current key).
     * 
     * @abstract
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     */
    resetParameters: function() {
    },
    /**
     * Add (another) parameter entry to the service call
     * 
     * @abstract
     * @param mapFieldValues {Object} Field Value Map used to build the parameter entry
     * @param bThrow {Boolean?true} TRUE - Throw exception on failure, FALSE - Ignore errors
     * @return {Object} Returns 'this' for object, allowing for sequenced calls
     * @throw Exception if cannot build a parameter entry from the map, or
     * if adding multiple parameter entries, to a single entry service
     */
    parameters: function(mapFieldValues, bThrow) {
    },
    /**
     * Execute the Service Call, based on the Current Service State.
     *
     * @abstract
     * @param ok {Function?null} Function used to signal success, NULL if event to be used
     * @param nok {Function?null} Function used to signal failure, NULL if event to be used
     * @param context {Function?null} Context in which to call the functions, NULL - use service object as context
     * @throw Exception on failure to initiate the service call
     */
    execute: function(ok, nok, context) {
    }
  }
});
