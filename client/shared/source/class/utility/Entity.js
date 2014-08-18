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
 *
 */
qx.Class.define("utility.Entity", {
  type : "static",

  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics : {
    /**
     * Is the object and entity?
     *
     * @param entity {Object} Object representing the entity
     * @return {Boolean} 'true' YES, 'false' otherwise
     */
    isEntity : function(entity) {
      return qx.lang.Type.isObject(entity) && entity.hasOwnProperty('__entity');
    },

    /**
     * If the entity element, of the given type?
     *
     * @param entity {Object} Object representing the entity
     * @param type {String} type to test against
     * @return {Boolean} 'true' YES, 'false' otherwise
     */
    isOfType : function(entity, type) {
      return utility.Entity.isEntity(entity) && (entity.__entity === type.toLowerCase());
    },

    /**
     * Convert an Entity Path into a Path Array.
     *
     * @param field {String} Array to convert to a Path Array
     * @return {String[][]} Two Dimensional Array of String.
     * First dimension represents, each ID (entity:field) in the path.
     * Second dimension represents, the components of the ID (i.e. 0 element == entity,
     * 1 == field).
     */
    toPath : function(field) {
      var path = [];
      field = utility.String.nullOnEmpty(field, true);
      if (field !== null) {
        var path_elements = field.split('\\', field);
        var elements = null;
        for (var i = 0; i < path_elements.length; ++i) {
          elements = this.explodeID(path.elements[i]);
          if (elements === null) {               // Invalid Field Element
            path = [];
            break;
          }
          path.push(elements);
        }
      }
      return path.length > 0 ? path : null;
    },

    /**
     * Convert an Entity Path Array into an Entity Path.
     *
     * @param id {String[][]} Array to convert to a Entity Path
     * @return {String} Entity Path String or NULL if not Valid;
     */
    toField : function(id) {
      var field = null;
      if (qx.lang.Type.isArray(id)) {
        id = utility.Array.trim(id);
        var element = null;
        for (var i = 0; i < id.length; ++i) {
          element = id[i];
          if ((element === null) || (element.length !== 2)) {        // Invalid Path
            field = null;
            break;
          }

          // TODO: Verify that the entity and field names are valid
          field = field !== null ? field + '\\' + element[0] + ':' + element[1] : element[0] + ':' + element[1];
        }
      }
      return field;
    },

    /**
     * Splits an ID into entity,field components. The expected format is of the
     * form ((a-zA-Z)(a-zA-Z\-\_)*)?(':'((a-zA-Z)(a-zA-Z\-\_)*)?)?
     * Note this is not the same as toPath(), which can handle field paths
     * (i.e. string with '\' to split parent and child fields)
     *
     * @param id {String} An ID to split into it's components
     * @return {String[]|null} An array containing, the entity and field components.
     *  In the case one of the elements is missing it is replaced by '*'.
     *  If the string only contains a single ':' then both entity and field are '*'.
     *  If the string is empty, NULL is returned.
     *
     */
    explodeID : function(id) {
      id = utility.String.nullOnEmpty(id, true);
      if (id !== null) {
        var entity = '*';
        var field = '*';
        var period = id.indexOf(':');
        if (period < 0) {
          entity = field;
        } else if (period === 0) {
          field = utility.String.nullOnEmpty(entity.slice(period + 1), true);
        } else {
          entity = utility.String.nullOnEmpty(entity.slice(0, period - 1), true);
          field = utility.String.nullOnEmpty(entity.slice(period + 1), true);
        }

        return [entity, field];
      }
      return null;
    }
  }
});
