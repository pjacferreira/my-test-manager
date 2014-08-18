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

qx.Interface.define("utility.api.di.IInjectable", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is the Dependency Injector Set?
     *
     * @abstract
     * @return {Boolean} 'true' if DI Set, 'false' otherwise
     */
    hasDI: function() {
    },
    /**
     * Get Dependency Injector
     *
     * @abstract
     * @return {utility.api.di.IInjector} Return Current Dependency Injector
     * @throw {String} Throws Exception if Dependency Injector is not set
     */
    getDI: function() {
    },
    /**
     * Set Dependency Injector
     *
     * @abstract
     * @param injector {utility.api.di.IInjector} Dependency Injector
     * @return {utility.api.di.IInjector} Return Previous Dependency Injector
     */
    setDI: function(injector) {
    }
  } // SECTION: MEMBERS
});
