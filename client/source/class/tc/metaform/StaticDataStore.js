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
qx.Class.define("tc.metaform.StaticDataStore", {
  extend: qx.core.Object,
  implement: tc.metaform.interfaces.IFormDataStore,

  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    /**
     * Fired when data correctly loaded.
     */
    "dataLoaded": "qx.event.type.Event",

    /**
     * Fired when data correctly saved.
     */
    "dataSaved": "qx.event.type.Event",

    /**
     * Fired on a Load/Save Error
     */
    "error": "qx.event.type.Event"
  },

  /*
  *****************************************************************************
  PROPERTIES
  *****************************************************************************
  */
  properties: {
    /** Original Values, for the Data Source */
    original: {
      check: "Object",
      nullable: true
    },

    /** The Current Values for the Data Source */
    current: {
      check: "Object",
      nullable: false
    }
  },

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   * @param data {Object|NULL} field name, value tuplets, for the fields that exist in the data source. If NULL, then
   *   we are working with a new record, for which we don't have any start values.
   */
  construct: function (data) {
    this.base(arguments);

    this.setOriginal(data);
    this.setCurrent({});
  },

  /**
   *
   */
  destruct: function () {
    this.base(arguments);
  },

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    // Counter for the current number of changes to the original values
    _modified: 0,

    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    // interface implementation
    load: function (keyValues) {

      // Just Say We are Loaded
      this.fireEvent("dataLoaded");
      return true;
    },

    // interface implementation
    save: function () {

      // If nothing changed then do nothing
      if (this._modified === 0) {
        this.fireEvent("dataSaved");
        return true;
      }

      // Otherwise Create the Entry or Updated it
      var original = this.getOriginal();
      if (original == null) { // New Entry
        this.setOriginal(this.getCurrent());
        this.setCurrent({});
      } else { // Update Entry
        // Merge Current Values into Original (Overwriting Existing Values)
        this.setOriginal(qx.lang.Object.mergeWith(original, this.getCurrent(), true));
        // Reset the Current Object Value
        this.setCurrent({});
      }

      // Clear the Modified Flag and Fire the Event
      this._modified = 0;
      this.fireEvent("dataSaved");
      return true;
    },

    // interface implementation
    isModified: function () {
      return this._modified !== 0;
    },

    // interface implementation
    getValues: function (last) {

      var original = this.getOriginal();
      if (original == null) {
        return this.isModified() ? this.getCurrent() : null;
      } else {
        return this.isModified() ? qx.lang.Object.mergeWith(original, this.getCurrent(), true) : original;
      }
    },

    // interface implementation
    setValues: function (values) {
      if (qx.lang.Type.isObject(values)) {
        for (var key in values) {
          if (values.hasOwnProperty(key)) {
            this.setValue(key, values[key]);
          }
        }
      }
    },

    // interface implementation
    getValue: function (name, last) {
      // Only use Current Value if last == false and there has been modifications

      var original = this.getOriginal();
      if (last || !this.isModified()) { // Want Original Value
        return (original != null) && original.hasOwnProperty(name) ? original[name] : null;
      } else { // Want Current Value
        var current = this.getCurrent();
        return current.hasOwnProperty(name) ? current[name] :
          (original != null) && original.hasOwnProperty(name) ? original[name] : null;
      }
    },

    // interface implementation
    setValue: function (name, value) {
      // Get the value from the Original Set
      var oldValue = this.getValue(name, true);
      var current = this.getCurrent();

      if (oldValue != value) { // Only Modify this if the value has changed
        if (current.hasOwnProperty(name)) { // Changing a Previously Modified Value
          oldValue = current[name];
        } else { // New Modification
          this._modified++;
        }
        current[name] = value;
        this.setCurrent(current);
      } else if (current.hasOwnProperty(name)) { // Resetting the field's value back to it's original value
        oldValue = current[name];
        delete current[name];
        this._modified--;
        this.setCurrent(current);
      }

      return oldValue;
    }
  }
});
