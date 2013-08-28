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
    }
  } // SECTION: MEMBERS
});
