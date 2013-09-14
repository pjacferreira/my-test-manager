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

qx.Interface.define("tc.meta.packages.ITablePackage", {
  extend: [tc.meta.packages.IMetaPackage],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Get Fields Package (IFieldsMetaPackage Instance)
     *
     * @abstract
     * @return {tc.meta.packages.IFieldsMetaPackage} Return instance of IFieldsMetaPackage
     * @throw If Package not Ready
     */
    getFields: function() {
    },
    /**
     * Get Services Package (IServicesMetaPackage Instance)
     *
     * @abstract
     * @return {tc.meta.packages.IServicesMetaPackage} Return instance of IServicesMetaPackage, NULL on failure
     * @throw If Package not Ready
     */
    getServices: function() {
    },
    /**
     * Get Actions Package (IActionsMetaPackage Instance)
     *
     * @abstract
     * @return {tc.meta.packages.IActionsMetaPackage} Return instance of IActionsMetaPackage, NULL on failure
     * @throw If Package not Ready
     */
    getActions: function() {
    },
    /**
     * Get Table Container (IMetaTable Instance)
     *
     * @abstract
     * @return {tc.meta.data.IMetaTable} Return instance of IMetaForm
     * @throw If Package not Ready
     */
    getTable: function() {
    }
  }
});
