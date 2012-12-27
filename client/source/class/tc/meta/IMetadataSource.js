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

qx.Interface.define("tc.meta.IMetadataSource", {
  members: {

    /**
     * Retrieves Metadata from some source (Note that, there is no garauntee that the call is asynchronous,
     * i.e. that the callback function is called, only after the current function has returned).
     *
     * @abstract
     * @param type {String} Identifies the type of meta data we are after
     * @param parameters {var} Parameters to specifically identify the elements we are after
     * @param callback {Function} Function to call on load complete or failure. Function signature is
     *    function(error_code, error_message, type, data), where
     *    error_code is 0 (Ok) otherwise error
     *    error_message is (String|Null)
     *    type is the value of type passed into to the call
     *    data is (Null on Failure or Missing or a Map (Object) of the data in which the key is the identifier and
     *      the value is the meta data requested)
     *
     *    Note:
     *    - If the identifier does not exist in the store, it is not returned (so it is possible to have less
     *      return values, than requested)
     *    - If none of the identifiers exists, than NULL is returned in the hash, but the error code is still (0)
     *      as the call completed successfully
     *
     * @param context {Object} The 'this' context for the callback
     *
     * @return {Boolean} 'true' on request launched, 'false' otherwise
     */
    getMetaData: function (type, parameters, callback, context) {
      return true;
    }

  }
});
