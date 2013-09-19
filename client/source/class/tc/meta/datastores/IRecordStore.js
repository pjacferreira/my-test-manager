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

qx.Interface.define("tc.meta.datastores.IRecordStore", {
  extend: [tc.meta.datastores.IFieldStore],
  events: {
    /**
     * Fired when the Record's Data has Been Loaded from Any Backend Source
     */
    "loaded": "qx.event.type.Event",
    /**
     * Fired when the Record's Data has Been Saved to Any Backend Source
     */
    "saved": "qx.event.type.Event",
    /**
     * Fired when the Record's Data has Been Erased from Any Backend Source
     */
    "erased": "qx.event.type.Event"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Is this a New Record?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    isNew: function() {
    },
    /**
     * Can we Load the Record's Data?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    canLoad: function() {
    },
    /**
     * Can we Save the Record's Data?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    canSave: function() {
    },
    /**
     * Can we Erase the Record's Data, from the Service Store?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw if the Data Store is Not Ready
     */
    canErase: function() {
    },
    /**
     * Try to load the Record's Data from the Data Store
     *
     * @abstract
     * @param ok {Function} Function to be called, if action succeeds
     * @param nok {Function ? null} Function to be called, if action fails
     * @param context {Object ? this} Context in which the functions are executed (if not provided, run within the context of the data store)
     * @throws {String} if the Data Store is Not Ready or The action is not possible on the data store
     */
    load: function(ok, nok, context) {
    },
    /**
     * Try to save the Record's Data to the Data Store
     *
     * @abstract
     * @param ok {Function} Function to be called, if action succeeds
     * @param nok {Function ? null} Function to be called, if action fails
     * @param context {Object ? this} Context in which the functions are executed (if not provided, run within the context of the data store)
     * @throws {String} if the Data Store is Not Ready or The action is not possible on the data store
     */
    save: function(ok, nok, context) {
    },
    /**
     * Try to erase the Record Record from the Data Store
     *
     * @abstract
     * @param ok {Function} Function to be called, if action succeeds
     * @param nok {Function ? null} Function to be called, if action fails
     * @param context {Object ? this} Context in which the functions are executed (if not provided, run within the context of the data store)
     * @throws {String} if the Data Store is Not Ready or The action is not possible on the data store
     */
    erase: function(ok, nok, context) {
    }
  } // SECTION: MEMBERS
});
