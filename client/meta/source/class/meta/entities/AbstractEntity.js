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

/**
 * Class from which All Meta Entities are Derived
 */
qx.Class.define("meta.entities.AbstractEntity", {
  type: "abstract",
  extend: qx.core.Object,
  implement: [
    meta.api.entity.IEntity,
    utility.api.di.IInjectable
  ],
  include: [
    utility.mixins.di.MInjectable
  ],
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Base Constructor for an Entity
   * 
   * @param type {String} Entity Type
   * @param id {String} Entity ID
   * @param metadata {Map} Incoming Entity Definition
   */
  construct: function(type, id, metadata) {
    this.base(arguments);

    // Initialize Reserved Parameters
    this.__sType = utility.String.v_nullOnEmpty(type, true);
    this.__sEntityID = utility.String.v_nullOnEmpty(id, true);

    /* NOTE:
     * Why not use QOOXDOO Properties for type and ID?
     * Because QOOXDOO Properties have both a get and SET methods (which
     * would allow you to change the initial values).
     */
    // Are Entity Type and ID Valid?
    if (qx.core.Environment.get("qx.debug")) { // YES
      qx.core.Assert.assertString(this.__sType, "[type] should be a string!");
      qx.core.Assert.assertString(this.__sEntityID, "[id] Should be a String!");
    }

    // Are we given Metadata?
    if (metadata != null) { // YES
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertObject(metadata, "[metadata] Should be an Object!");
      }

      this.setMetadata(metadata);
    }
  },
  /**
   *
   */
  destruct: function() {
    // NOTE: Do not call qx.core.Object:destruct, as THERE IS NONE, and forces an exception 
    // this.base(arguments);

    // Clear all Member Fields
    this.__sType = null;
    this.__sEntityID = null;
  },
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    metadata: {
      nullable: false,
      check: "Object",
      transform: "_transformMetadata"
    }
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __sType: null,
    __sEntityID: null,
    /*
     ***************************************************************************
     Javascript Object Methods
     ***************************************************************************
     */
    /**
     * Display Entity Type.
     * 
     * @returns {String} Simple Display of Element
     */
    toString: function() {
      return "[" + this.__sType + "'" + __sEntityID + "']";
    },
    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    /**
     * Return Entity Type ('field','service','form','table', etc.)
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
    getID: function() {
      return this.__sEntityID;
    },
    /**
     * Retrieve Entity Options
     *
     * @return {Map} Widget Options
     */
    getOptions: function() {
      var definition = this.getMetadata();
      return (definition !== null) && (definition.hasOwnProperty('options')) ?
        definition.options : null;
    },
    /**
     * Apply a Metadata Overlay over the Entity
     *
     * @param overlay {Map} Metadata Overlay
     * @return {Boolean} 'true' overlay applied, 'false' otherwuse
     */
    applyOverlay: function(overlay) {
      if (qx.lang.Type.isObject(overlay)) {
        try {
          var metadata = qx.lang.Object.clone(this.getMetadata(), true);
          this.setMetadata(qx.lang.Object.mergeWith(metadata, overlay, true));
          return true;
        } catch (e) {
          this.warn("Metadata Overlay is invalid.");
        }
      }

      return false;
    },
    /*
     ***************************************************************************
     PROPERTY HANDLERS
     ***************************************************************************
     */
    _transformMetadata: function(value) {
      return this._preProcessMetadata(qx.lang.Object.clone(value, true));
    },
    /*
     *****************************************************************************
     PROTECTED (HELPER) Methods
     *****************************************************************************
     */
    _validateMapProperty: function(metadata, property, empty) {
      // Is the Property a MAP?
      if (metadata.hasOwnProperty(property) &&
        qx.lang.Type.isObject(metadata[property])) { // YES
        return true;
      }


      delete metadata[property];
      return !!empty ? true : false;
    },
    _validateStringProperty: function(metadata, property, empty, trim) {
      // Is the property a Valid String?
      if (metadata.hasOwnProperty(property)) { // YES
        metadata[property] = utility.String.v_nullOnEmpty(metadata[property], !!trim);
        // Is the String Not Empty?
        if (metadata[property] !== null) { // YES
          return true;
        }
      }

      delete metadata[property];
      return !!empty ? true : false;
    },
    _validateArrayProperty: function(metadata, property, empty) {
      // Is the property a Valid Array?
      if (metadata.hasOwnProperty(property) &&
        qx.lang.Type.isArray(metadata[property]) &&
        metadata[property].length) {
        return true;
      }

      delete metadata[property];
      return !!empty ? true : false;
    },
    /*
     *****************************************************************************
     ABSTRACT METHODS
     *****************************************************************************
     */
    /**
     * Prepare Entity Definition, according to type requirements.
     *
     * @abstract
     * @param definition {Map} Incoming Entity Definition
     * @return {Map|null} Outgoing Entity Definition or 'null' if not valid
     */
    _preProcessMetadata: function(definition) {
    }
  }
});
