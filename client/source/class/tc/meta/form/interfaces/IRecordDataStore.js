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

qx.Interface.define("tc.meta.form.interfaces.IRecordDataStore", {
  extend: [tc.meta.interfaces.IFieldsDataStore],
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when a change to the service's definitions occurs.
     */
    "change-services-meta": "qx.event.type.Event",
    /**
     * Fired when the execution of a service passes.
     * Service Name is returned as part of the data event
     */
    "execute-ok": "qx.event.type.Data",
    /**
     * Fired when the execution of a service fails.
     * Service Name is returned as part of the data event
     */
    "execute-nok": "qx.event.type.Data"
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Set the Records Service's Definition for the Data Store
     *  
     * @param metadata {Object} New Metadata Service's Definition 
     * i.e. expects an object of type
     * {
     *   service-name: { service properties }
     * }
     * Notes:
     * - throws an exception if the metadata provided is invalid (will not
     *   modify the current metadata, if the provided metadata is invalid)
     * - Fires change-services-meta, on successful completion of metadata change
     */
    setServicesMeta: function(metadata) {
    },
    /**
     * See if a service exists
     * 
     * @param service {name} Service name
     * @return {Boolean} 'true' if service exists, 'false' otherwise.
     * Notes:
     * - throws an exception if the service metadata is not loaded
     */
    hasService: function(name) {
    },
    /**
     * Verifies that the datastore meets  the required state, for the service call
     *  
     * @param service {String} Service name
     * @return {Boolean} 'true' if service has the necessary requirements for execution, 'false' otherwise.
     * Notes:
     * - throws an exception if the service metadata is not loaded
     */
    canExecute: function(service) {
    },
    /**
     * Execute a Service against the Current DataStore
     *  
     * @param service {String} Service name
     * Notes:
     * - throws an exception if the service metadata is not loaded
     * - Fires execute-ok, execute-nok depending on the
     *   outcome of call
     */
    execute: function(service) {
    }
  }
});
