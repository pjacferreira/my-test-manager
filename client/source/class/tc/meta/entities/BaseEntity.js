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
 * Generic Form Model
 */
qx.Class.define("tc.meta.entities.BaseEntity", {
  extend: qx.core.Object,
  implement: tc.meta.entities.IMetaEntity,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param name {String} Entity Type
   */
  construct: function(type, id) {
    this.base(arguments);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertString(type, "[type] should be a string!");
      qx.core.Assert.assertString(id, "[id] Should be a String!");
    }

    this.__sType = type;
    this.__sEntityID = id;
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Clear all Member Fields
    this.__sType = null;
    this.__sEntityID = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Entity Type
    __sType: null,
    __sEntityID: null,
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Return Entity Type ('field','service','form','table')
     *
     * @return {String} Entity Type
     */
    getType: function() {
      return this.__sType;
    },
    /**
     * Return Entity ID
     *
     * @return {String} Entity ID
     */
    getEntityId: function() {
      return this.__sEntityID;
    }            
  }
});
