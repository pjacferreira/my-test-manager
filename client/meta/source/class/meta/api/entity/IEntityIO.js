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

qx.Interface.define("meta.api.entity.IEntityIO", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Container Defines an Input?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasInput: function() {
    },
    /**
     * Container Input Definition
     *
     * @abstract
     * @return {String|String[]|null} A String or String Array containing the 
     * permitted/accepted input IDs, or NULL if no input allowed
     */
    getInput: function() {
    },
    /**
     * Container Defines an Output?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasOutput: function() {
    },
    /**
     * Container Output Definition
     *
     * @abstract
     * @return {Var[]} Array containing either, a string of the name of a field
     *   or entity that is accepted, an object containing field/entity->default
     *   value, or NULL if no input is allowed
     */
    getOutput: function() {
    }
  }
});
