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

qx.Interface.define("meta.api.ui.IWidget", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Retrieve the ID of the Widget
     * 
     * @abstract
     * @return {String} Widget ID
     */
    getID: function() {
    },
    /**
     * Retrieve the Meta Entity Associated with the Widget
     * 
     * @abstract
     * @return {meta.api.entities.IEntity} Meta Entity for Widget
     */
    getEntity: function() {
    },
    /**
     * Retrieve the Meta Entity Associated with the Widget
     * 
     * @abstract
     * @return { meta.api.ui.IWidget|null} Parent Widget or null if no parent
     */
    getParent: function() {
    },
    /**
     * Retrieve the Displayable Content of the Widget
     * 
     * @abstract
     * @return {qx.ui.core.Widget} A Widget or Container
     */
    getWidget: function() {
    },
    /**
     * Is Widget Ready for Use?
     * 
     * @abstract
     * @return {Boolean} 'true' Widget is Ready, 'false' otherwise 
     */
    isReady: function() {
    },
    /**
     * Initialize (or re-initialize) the Entity.
     * Fires 'ready' event on success, 'not-ready' on failure to initialize.
     *
     * @abstract
     * @param force {Boolean?false} 'true' Force entity initialization/re-initialization,
     * 'false' if entity is initialized, don't re-initialize
     */
    initialize: function(force) {
    }
  }
});
