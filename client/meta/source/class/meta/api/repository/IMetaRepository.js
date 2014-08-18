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

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("meta.api.repository.IMetaRepository", {
  extend: utility.api.di.IInjectable,
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Get Meta Field Entity
     *
     * @abstract
     * @param id {String} ID of Field Required 
     * @param ok {Function} Function called on success (function(meta.api.entities.IField) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getField: function(id, ok, nok, context) {
    },
    /**
     * Return Requested Field Entities
     *
     * @abstract
     * @param ids {String[]} IDs of Fields Required
     * @param ids {String[]} IDs of Services Required
     * @param ok {Function} Function called on success (function(Map) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getFields: function(ids, ok, nok, context) {
    },
    /**
     * Get Meta Service Entity
     *
     * @abstract
     * @param id {String} ID of Service Required 
     * @param ok {Function} Function called on success (function(meta.api.entities.IService) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getService: function(id, ok, nok, context) {
    },
    /**
     * Return Requested Services Entities
     *
     * @abstract
     * @param ids {String[]} IDs of Services Required
     * @param ok {Function} Function called on success (function(Map) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getServices: function(ids, ok, nok, context) {
    },
    /**
     * Get Meta Form Entity
     *
     * @abstract
     * @param id {String} ID of Form Required 
     * @param ok {Function} Function called on success (function(meta.api.entities.IForm) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getForm: function(id, ok, nok, context) {
    },
    /**
     * Get Meta Form Entity
     *
     * @abstract
     * @param ids {String[]} IDs of Forms Required
     * @param ok {Function} Function called on success (function(Map) {})
     * @param nok {Function?null} Function called in case of error (function(String|String[]) {})
     * @param context {Object?this} Context for Function Call
     * @return {Boolean} 'true' action executed, 'false' otherwise
     */
    getForms: function(ids, ok, nok, context) {
    },
    /**
     * Create a Meta Entity, from the metadata given, or empty if no metadata
     * set
     *
     * @abstract
     * @param id {String} Entity ID 
     * @param type {String} Entity Type 
     * @param metadata {Map?null} Entity Metadata (or null if we are creating an Entity Shell)
     * @param parent {meta.api.itw.meta.api.IContainer?null} Parent entity or null if not required
     * @return {meta.api.itw.meta.api.IEntity} Entity or 'null' if not able to create
     */
    createEntity: function(id, type, metadata, parent) {
    }
  }
});
