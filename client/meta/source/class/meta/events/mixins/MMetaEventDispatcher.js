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
qx.Mixin.define("meta.events.mixins.MMetaEventDispatcher", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     MIXIN FUNCTIONS
     ***************************************************************************
     */
    /**
     * Fire Meta Failed Meta Event.
     *
     * @param type {String} Meta Event Type
     * @param parameters {Var|Var[]?null} Any Extra Parameters
     * @param message {String?null} Error Message
     * @param code {Integer?0} Error Code
     * @return {Boolean} Whether the event was handled successfully
     */
    _mx_med_fireEventOK: function(type, parameters, message, code) {
      return this.__mx_med_fireEvent(type,
        false,
        qx.lang.Type.isNumber(code) ? code : 0,
        utility.String.v_nullOnEmpty(message, true),
        parameters === undefined ? null : parameters);
    },
    /**
     * Fire Meta Failed Meta Event.
     *
     * @param type {String} Meta Event Type
     * @param parameters {Var|Var[]?null} Any Extra Parameters
     * @param message {String?null} Error Message
     * @param code {Integer?0} Error Code
     * @return {Boolean} Whether the event was handled successfully
     */
    _mx_med_fireEventNOK: function(type, parameters, message, code) {
      return this.__mx_med_fireEvent(type,
        true,
        qx.lang.Type.isNumber(code) ? code : 0,
        utility.String.v_nullOnEmpty(message, true),
        parameters === undefined ? null : parameters);
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    /**
     * Fire Meta Event.
     *
     * @param type {String} Meta Event Type
     * @param failure {Boolean?false} Are we notifying of failure?
     * @param code {Integer} Error Code
     * @param message {String?null} Error Message
     * @return {Boolean} Whether the event was handled successfully
     * @param parameters {var?null} Any parameters to pass on
     */
    __mx_med_fireEvent: function(type, failure, code, message, parameters) {
      if (!this.$$disposed)
      {
        return this.fireNonBubblingEvent(type, meta.events.MetaEvent, [!!failure, code, message, parameters, true]);
      }

      return true;
    }
  } // SECTION: MEMBERS
});
