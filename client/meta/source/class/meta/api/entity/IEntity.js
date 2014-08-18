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

qx.Interface.define("meta.api.entity.IEntity", {
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    metadata: {}
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @abstract
     * @return {String} Entity Type
     */
    getType: function() {
    },
    /**
     * Return Entity ID
     *
     * @abstract
     * @return {String} Entity ID
     */
    getID: function() {
    },
    /**
     * Retrieve Widget Options
     *
     * @abstract
     * @return {Map} Widget Options
     */
    getOptions: function() {
    },
    /**
     * Apply a Metadata Overlay over the Entity
     *
     * @abstract
     * @param overlay {Map} Metadata Overlay
     * @return {Boolean} 'true' overlay applied, 'false' otherwuse
     */
    applyOverlay: function(overlay) {
    }
  }
});
