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

qx.Interface.define("tc.meta.datastores.IDataStore", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Can we use the Data Store?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReady: function() {
    },
    /**
     * Is the Data Store Read Only?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isReadOnly: function() {
    },
    /**
     * Is this an an offline (in memory only) data store?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isOffline: function() {
    },
    /**
     * Was the store modified (i.e. Dirty with pending changes)?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isDirty: function() {
    }
  }
});
