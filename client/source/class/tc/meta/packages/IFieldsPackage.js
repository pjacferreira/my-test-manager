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

qx.Interface.define("tc.meta.packages.IFieldsPackage", {
  extend: [tc.meta.packages.IMetaPackage],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Does the Field Exist in the Package?
     *
     * @abstract
     * @param id {String} Field ID (format 'entity id:field name')
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw If Package not Ready
     */
    hasField: function(id) {
    },
    /**
     * Get Field Container
     *
     * @abstract
     * @param id {String} Field ID (format 'entity id:field name')
     * @return {tc.meta.data.IMetaField} Return Metadata for field
     * @throw If Package not Ready or Field Doesn't Exist
     */
    getField: function(id) {
    },
    /**
     * Get a List of Fields in the Container
     *
     * @abstract
     * @return {String[]} Array of Field ID's or Empty Array (if no fields in the package)
     * @throw If Package not Ready
     */
    getFields: function() {
    }
  } // SECTION: MEMBERS
});
