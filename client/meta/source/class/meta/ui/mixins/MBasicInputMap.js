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
 * A Series of Functions, that go Hand-in-Hand with the Mixins that
 * Support Input/Output functions of IMetaWidgetIO
 */
qx.Mixin.define("meta.ui.mixins.MBasicInputMap", {
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    // Notifies of Changes in the Allowed Inputs
    "change-inputs": "meta.events.MetaEvent"
  }, // SECTION: EVENTS
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Contructor
   */
  construct: function() {
    // Initialize
    this.__mapRequired = new utility.Map();
    this.__mapOptional = new utility.Map();
  },
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__mapRequired = null;
    this.__mapOptional = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __mapRequired: null,
    __mapOptional: null,
    /*
     ***************************************************************************
     MIXIN FUNCTIONS (Filter and Default Map Builders)
     ***************************************************************************
     */
    _mx_bim_acceptsInputs: function() {
      return (this.__mapRequired.count() > 0) || (this.__mapOptional.count() > 0);
    },
    _mx_bim_hasInputField: function(field) {
      return this.__mapRequired.has(field) || this.__mapOptional.has(field);
    },
    _mx_bim_inputFields: function() {
      // Get Input Fields
      var fields = this.__mapRequired.count() > 0 ? this.__mapRequired.keys() : [];
      fields = this.__mapOptional.count() > 0 ? fields.concat(this.__mapOptional.keys()) : fields;
      return fields.length > 0 ? fields : null;
    },
    _mx_bim_addInputFields: function(fields, required) {
      // Inputs Added
      var added = [];

      // Did we get an array of fields?
      if (qx.lang.Type.isArray(fields)) { // YES
        for (var i = 0; i < fields.length; ++i) {
          // Do we need to add the Field to the Inputs?
          if (this.__mx_bim_addInputField(required ? this.__mapRequired : this.__mapOptional, fields[i])) { // YES
            // Input Added
            added.push(fields[i]);
          }
        }
        return true;
      } else if (qx.lang.Type.isString(fields)) { // NO: Single Field
        // Do we need to add the Field to the Inputs?
        if (this.__mx_bim_addInputField(required ? this.__mapRequired : this.__mapOptional, fields)) { // YES
          // Input Added
          added.push(fields);
        }
      }
      // ELSE: No Valid Field(s) to Add

      // Did we make a change to Inputs?
      if (added.length) { // YES
        // Notify that we have change our Input Requirements
        this._mx_fireMetaEvent("change-inputs", true, [this, added]);
        return true;
      }

      return false;
    },
    _mx_bim_removeInputFields: function(fields) {
      // Inputs Added
      var removed = [];

      // Did we get an array of fields?
      if (qx.lang.Type.isArray(fields)) { // YES
        for (var i = 0; i < fields.length; ++i) {
          if (this.__mx_bim_removeInputField(this.__mapRequired, fields[i]) ||
            this.__mx_bim_removeInputField(this.__mapOptional, fields[i])) {
            removed.push(fields[i]);
          }
        }
      } else if (qx.lang.Type.isString(fields)) { // NO: Single Field
        if (this.__mx_bim_removeInputField(this.__mapRequired, fields) ||
          this.__mx_bim_removeInputField(this.__mapOptional, fields)) {
          removed.push(fields[i]);
        }
      }
      // ELSE: No Valid Field(s) to Remove

      // Did we make a Change to the Inputs?
      if (removed.length) { // YES
        this._mx_fireMetaEvent("change-inputs", true, [this, null, removed]);
        return true;
      }

      return false;
    },
    _mx_bim_extractInput: function(inputs) {
      // Do ew have Inputs?
      if (qx.lang.Type.isObject(inputs)) { // YES

        // Extract Required and Optional Fields
        var required = this.__mx_bim_filterInputs(inputs, this.__mapRequired);
        var optional = this.__mx_bim_filterInputs(inputs, this.__mapOptional);

        // Do we have Required Inputs?
        if (required !== null) { // YES

          // Do we have Optional Inputs?
          if (optional !== null) { // YES: Mix Required and Optional
            return qx.lang.Object.mergeWith(required, optional, false);
          } else { // NO: Only Optional Inputs
            return required;
          }
        } else if (optional !== null) { // NO: Only Optional Inputs
          return optional;
        }
        // ELSE: No Required or Optional Inputs
      }

      return null;
    },
    /*
     ***************************************************************************
     PRIVATE FUNCTIONS
     ***************************************************************************
     */
    __mx_bim_addInputField: function(map, field) {
      if (!map.has(field)) {
        this.__mapRequired.add(field, null);
        return true;
      }
      return false;
    },
    __mx_bim_removeInputField: function(map, field) {
      return this.__mapRequired.remove(field) !== null;
    },
    __mx_bim_filterInputs: function(inputs, map) {
      var count = 0;
      var extracted = {};

      // Do we have any fields in the Map?
      if (map.count()) { // YES
        // Cycle through the fields 
        for (var field in inputs) {
          // Is this field listed in the Map?
          if (inputs.hasOwnProperty(field) && map.has(field)) { // YES
            extracted[field] = inputs[field];
            count++;
          }
        }
      }

      return count > 0 ? extracted : null;
    }
  } // SECTION: MEMBERS
});
