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

qx.Interface.define("tc.meta.entities.IMetaAction", {
  extend: [tc.meta.entities.IMetaEntity],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Return's a Action's Label
     *
     * @abstract
     * @return {String} Action Label
     */
    getLabel: function() {
    },
    /**
     * Returns a Description for the Action, if any is defined.
     *
     * @abstract
     * @return {String} Action Description String or NULL (if not defined)
     */
    getDescription: function() {
    },
    /**
     * Returns a Shortcut Key Combination for the Action, if any is defined.
     *
     * @abstract
     * @return {String} Action Shortcut or NULL (if not defined)
     */
    getShortcut: function() {
    },
    /**
     * Returns an URL to an Icon Image for the Action, if any is defined.
     *
     * @abstract
     * @return {String} Icon URL or NULL (if not defined)
     */
    getIcon: function() {
    },
    /**
     * Does the Action Specify a Value for the Option?
     *
     * @abstract
     * @param name {String} Option name
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasOption: function(name) {
    },
    /**
     * Get the Value for the Specified Option
     *
     * @abstract
     * @param name {String} Option Name
     * @return {var} Option's value, NULL if no Option Value Defined
     */
    getOptionValue: function(name) {
    },
    /**
     * Does the Action Specify a Set of Parameters?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    hasParameters: function() {
    },
    /**
     * Get the List of Parameters Allowed or Required by the Action.
     * 
     * @abstract
     * @return {String[]} List of Parameters Required/Allowed by the Action
     */
    getParameters: function() {
    },
    /**
     * Does the Action Execute a Service?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isServiceAction: function() {
    },
    /**
     * Does the Action Execute a Form?
     *
     * @abstract
     * @return {Boolean} 'true' YES, 'false' Otherwise
     */
    isFormAction: function() {
    },
    /**
     * Returns the Entity ID Associated with the Action
     *
     * @abstract
     * @return {String} Entity ID
     */
    getActionEntity: function() {
    }
  }
});
