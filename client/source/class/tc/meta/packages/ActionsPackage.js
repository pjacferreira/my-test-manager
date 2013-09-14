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
 * Actions Meta Package Class
 */
qx.Class.define("tc.meta.packages.ActionsPackage", {
  extend: tc.meta.packages.BasePackage,
  implement: tc.meta.packages.IActionsPackage,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor for an Actions MetaPackage
   * 
   * @param metadataActions {Object} Actions Metadata
   */
  construct: function(metadataActions) {
    this.base(arguments);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertObject(metadataActions, "[metadataActions] should be a Metadata Object!");
    }

    // Initialize Local Properties
    this.__oMetaData = {};
    this.__mapCache = {};
    this.__arActionsInPackage = [];

    // Process Metadata
    var action = null;
    for (var id in metadataActions) {
      if (metadataActions.hasOwnProperty(id)) {
        action = metadataActions[id];
        if (qx.lang.Type.isObject(action) && action.hasOwnProperty('label')) {
          id = id.toLowerCase();
          this.__arActionsInPackage.push(id);
          this.__oMetaData[id] = qx.lang.Object.clone(action, true);
        }
      }
    }

    if (this.__arActionsInPackage.length) { // Sort the Actions by ID
      this.__arActionsInPackage.sort();
    }
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);

    // Cleanup
    this.__oMetaData = null;
    this.__mapCache = null;
    this.__arActionsInPackage = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __oMetaData: null,
    __arActionsInPackage: null,
    __mapCache: null,
    /*
     *****************************************************************************
     INTERFACE METHODS : IMetaPackage
     *****************************************************************************
     */
    /**
     * Start Package Initialization Process. Events "ready" or "error" are fired
     * to show success or failure of package initialization. 
     *
     * @param callback {Object ? null} Callback Object, if we would rather use callback then events.
     *    Note: 
     *      - Usable callback properties:
     *        - 'ok' (REQUIRED) called when call successfully completed
     *        - 'nok' (OPTIONAL) called if service execution failed for any reason
     *        - 'context' (OPTIONAL) the 'this' for the function calls  
     *      - that the callback object should specify, at the least, an 'ok' function.
     * @return {Boolean} 'true' if started initialization, 'false' if initialization failed to start
     */
    initialize: function(callback) {
      // Prepare CallBack
      callback = this._prepareCallback(callback);

      if (!this.isReady()) {
        this._bReady = this.__arActionsInPackage.length > 0;
      }

      this._callbackPackageReady(callback, this.isReady(), "No Valid Actions in the Package.");
      return this.isReady();
    },
    /*
     *****************************************************************************
     INTERFACE METHODS : IFieldsPackage
     *****************************************************************************
     */
    /**
     * Does the Action Exist in the Package?
     *
     * @param id {String} Action ID
     * @return {Boolean} 'true' YES, 'false' Otherwise
     * @throw If Package not Ready
     */
    hasAction: function(id) {
      this._throwIsPackageReady();

      return (this.__oMetaData !== null) && this.__oMetaData.hasOwnProperty(id);
    },
    /**
     * Get Action Container
     *
     * @param id {String} Action ID
     * @return {tc.meta.data.IMetaAction} Return Metadata for field
     * @throw If Package not Ready or Action Doesn't Exist
     */
    getAction: function(id) {
      this._throwActionNotExists(id, this.hasAction(id));

      // NOTE: Action Entities are Read Only (So we can Cache Them)
      if (!this.__mapCache.hasOwnProperty(id)) { // No Entry in Cache - So Create and Add it
        this.__mapCache[id] = new tc.meta.entities.ActionEntity(id, this.__oMetaData[id]);
      }

      return this.__mapCache[id]; // Use Cache Entry
    },
    /**
     * Get a List of Action IDs in the Container
     *
     * @return {String[]} Array of Action IDs or Empty Array (if no actions in the package)
     * @throw If Package not Ready
     */
    getActions: function() {
      this._throwIsPackageReady();

      return this.__arActionsInPackage;
    },
    /*
     *****************************************************************************
     EXCEPTION GENERATORS
     *****************************************************************************
     */
    _throwIsPackageReady: function() {
      if (!this.isReady()) {
        throw "Package has not been initialized";
      }
    },
    _throwActionNotExists: function(action, exists) {
      if (!exists) {
        throw "The Action [" + action + "] does not belong to the package";
      }
    }
  } // SECTION: MEMBERS
});
