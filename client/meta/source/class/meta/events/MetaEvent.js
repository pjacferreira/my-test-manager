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
 * Event object for Meta Events.
 */
qx.Class.define("meta.events.MetaEvent", {
  extend: qx.event.type.Event,
  /*
   *****************************************************************************
   DESTRUCTOR
   *****************************************************************************
   */
  destruct: function() {
    this.__message = null;
    this.__parameters = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __ok: true,
    __code: 0,
    __message: null,
    __parameters: null,
    /**
     * Initializes the event object.
     *
     * @param type {String} The Meta Event's Type
     * @param failure {Boolean?false} Is the event signalling failure? (optional)
     * @param parameters {var?null} Possible Parameters to pass to the event handler (optional)
     * @param cancelable {Boolean?false} Whether or not an event can have its default
     * action prevented. The default action can either be the browser's
     * default action of a native event (e.g. open the context menu on a
     * right click) or the default action of a qooxdoo class (e.g. close
     * the window widget). The default action can be prevented by calling
     * {@link qx.event.type.Event#preventDefault}
     * @return {meta.api.itw.MetaEvent} the initialized instance.
     */
    init: function(failure, code, message, parameters, cancelable)
    {
      this.base(arguments, false, cancelable);

      this.__ok = !failure;
      this.__code = code;
      this.__message = message;
      this.__parameters = parameters;

      return this;
    },
    /**
     * Get a copy of this object
     *
     * @param embryo {meta.api.itw.MetaEvent?null} Optional event class, which will
     * be configured using the data of this event instance. The event must be
     * an instance of this event class. If the data is <code>null</code>,
     * a new pooled instance is created.
     * @return {meta.api.itw.MetaEvent} a copy of this object
     */
    clone: function(embryo)
    {
      var clone = this.base(arguments, embryo);

      clone.__ok = this.__ok;
      clone.__code = this.__code;
      clone.__message = this.__message;
      clone.__parameters = this.__parameters;

      return clone;
    },
    /**
     * Is the event signalling success or failure?
     *
     * @return {Boolean} 'TRUE' Signalling Success, 'FALSE' Signalling Failure
     */
    getOK: function() {
      return this.__ok;
    },
    /**
     * Is the event signalling success or failure?
     *
     * @return {Integer} Error/Success Code (DEFAULT: 0)
     */
    getCode: function() {
      return this.__code;
    },
    /**
     * Is the event signalling success or failure?
     *
     * @return {Message} Error/Success Message (DEFAULT: NULL)
     */
    getMessage: function() {
      return this.__message;
    },
    /**
     * Retrieves the parameters to be passed to the event handler, if any.
     *
     * @return {var} Parameters (DEFAULT: NULL)
     */
    getParameters: function() {
      return this.__parameters;
    }
  } // SECTION: MEMBERS
});