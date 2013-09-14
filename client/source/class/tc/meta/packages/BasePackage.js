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
 * Base Meta Package Class
 */
qx.Class.define("tc.meta.packages.BasePackage", {
  type : "abstract",
  extend: qx.core.Object,
  implement: tc.meta.packages.IMetaPackage,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /*
     ***************************************************************************
     EVENTS (IMetaPackage)
     ***************************************************************************
     */
    /**
     * Fired when Package is Initialized
     */
    "ok": "qx.event.type.Data",

    /**
     * Fired if the package failed to initialize correctly.
     */
    "nok": "qx.event.type.Data"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an MetaPackage
   * 
   */
  construct: function() {
    this.base(arguments);
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    _bReady: false,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Is Package Ready?
     *
     * @return {Boolean} 'true' if Package is Ready, 'false' Otherwise
     */
    isReady: function() {
      return this._bReady;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS
     ***************************************************************************
     */
    _prepareCallback: function(callback) {
      // Setup Default Callback Object
      var event_this = this;
      var newCallback = {// DEFAULT: No Callbacks - Fire Events
        'ok': function(result) {
          event_this.fireEvent('ok');
        },
        'nok': function(error) {
          event_this.fireDataEvent('nok', error);
        },
        'context': event_this
      };

      // Update Callback Object with User Parameters
      if (qx.lang.Type.isObject(callback)) {
        if (callback.hasOwnProperty('ok') && qx.lang.Type.isFunction(callback['ok'])) {
          newCallback['ok'] = callback['ok'];
        }

        if (callback.hasOwnProperty('nok') && qx.lang.Type.isFunction(callback['nok'])) {
          newCallback['nok'] = callback['nok'];
        }

        if (callback.hasOwnProperty('context') && qx.lang.Type.isObject(callback['context'])) {
          newCallback['context'] = callback['context'];
        }
      }

      return newCallback;
    }, // FUNCTION: _buildCallback
    _callbackPackageReady: function(callback, ok, message) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertObject(callback, "[callback] is not of the expected type!");
      }

      if (ok) {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertFunction(callback['ok'], "[callback] is missing [ok] function!");
          qx.core.Assert.assertObject(callback['context'], "[callback] is missing call [context]!");
        }

        callback['ok'].call(callback['context']);
      } else {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertFunction(callback['nok'], "[callback] is missing [nok] function!");
          qx.core.Assert.assertObject(callback['context'], "[callback] is missing call [context]!");
        }

        callback['nok'].call(callback['context'], message);
      }
    } // FUNCTION: _callbackPackageReady                        
  } // SECTION: MEMBERS
});
