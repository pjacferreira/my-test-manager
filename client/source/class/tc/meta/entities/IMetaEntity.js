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

qx.Interface.define("tc.meta.entities.IMetaEntity", {
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
    getEntityId: function() {
    }
  }
});
