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
 * Implements the Requirements for utility.api.di.IInjectable
 */
qx.Mixin.define("utility.mixins.di.MInjectable", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Destructor
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup Members
    this.__di = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __di: null,
    /*
     ***************************************************************************
     MEMBERS of utility.api.di.IInjectable
     ***************************************************************************
     */
    /**
     * Is the Dependency Injector Set?
     *
     * @return {Boolean} 'true' if DI Set, 'false' otherwise
     */
    hasDI: function() {
      return this.__di !== null;
    },
    /**
     * Get Dependency Injector
     *
     * @return {utility.api.di.IInjector} Return Current Dependency Injector
     * @throw {String} Throws Exception if Dependency Injector is not set
     */
    getDI: function() {
      if(this.__di === null) {
        throw "The Dependency Injector has not been set.";
      }
      return this.__di;
    },
    /**
     * Set Dependency Injector
     *
     * @abstract
     * @param injector {utility.api.di.IInjector} Dependency Injector
     * @return {utility.api.di.IInjector} Return Previous Dependency Injector
     */
    setDI: function(injector) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertObject(injector, "[injector] Is not of the expected type!");
        qx.core.Assert.assertInterface(injector, utility.api.di.IInjector, "[injector] Is not a Dependency Injector Object!");
      }

      var old = this.__di;
      this.__di = injector;
      return old;
    }
  } // SECTION: MEMBERS
});
