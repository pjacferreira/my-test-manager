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

qx.Class.define("meta.ui.FormInput", {
  extend: meta.ui.FormBasic,
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    record: {
      nullable: false,
      check: "meta.api.ui.datasource.IRecord",
      apply: "_applyRecord"
    }
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Input Form UI Constructor
   * 
   * @param form {meta.api.ui.IForm} Form Definition
   * @param parent {meta.api.ui.IGroup?null} Parent Widget
   * @param record {meta.api.ui.datasource.IRecord?null} Record to use
   */
  construct: function(form, parent, record) {
    // Initialize Base Widget
    this.base(arguments, form, parent);

    // Set the Default Group Layout
    this.setLayout(new meta.ui.layouts.LayoutComplex(this, this._addWidget));

    // Is a datastore provided?
    if (record != null) { // YES
      this.setRecord(record);
    }
    /*    
     else { // NO: Use a default Basic Store
     this.setStore(new meta.ui.stores.BasicFormStore(form));
     }
     */
  },
  /**
   *
   */
  destruct: function() {
    this.base(arguments);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     PROTECTED METHODS (Properties)
     ***************************************************************************
     */
    _applyRecord: function(record, old) {
      // Did we have a Record Previously Set
      if (old !== null) { // YES
        // Remove the Listener
        this._mx_meh_detach("change-output-values", old);
      }

      // Attach Listener to New Record
      this._mx_meh_attach("change-output-values", record);
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    _passMetaEvent: function(type, success, parameters) {
      // Is this a failure?
      if (success === false) { // YES
        // Handle Failures Here
        var code = parameters[1];
        var message = parameters[2];
        this.error("ERROR procssing [" + type + "] with Error Code [" + code + "] and Message [" + (message !== null ? message : '') + "]");
        return false;
      }

      return true;
    },
    _processMetaChangeOutputValuesOK: function(code, message, fields) {
      // Modify the Inputs for the Form
      this.setInput(fields);
    }
  } // SECTION: MEMBERS
});
