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
qx.Class.define("tc.metaform.TCDataStore", {
  extend: tc.metaform.StaticDataStore,

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   *
   * @param formName
   * @param formType
   * @param sourceMetadata
   */
  construct: function (service, keyFields) {
    this.base(arguments, null);

    // Save the Service Requested
    this.__service = tc.util.String.nullOnEmpty(service);

    // Save the list of Key Fields
    if (qx.lang.Type.isArray(keyFields)) {
      this.__keyFields = tc.util.Array.trim(keyFields);
    } else if (qx.lang.Type.isString(keyFields)) {
      this.__keyFields = [tc.util.String.nullOnEmpty(keyFields, true)];
    }
    this.__keyFields = this.__keyFields == null ? null : tc.util.Array.clean(this.__keyFields);
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
    __service: null,
    __keyFields: null,

    /*
     *****************************************************************************
     INTERFACE METHODS
     *****************************************************************************
     */
    // interface implementation
    load: function (keyValues) {

      // A Service and Key Fields have to be Defined
      if ((this.__service == null) || (this.__keyFields == null)) {
        return false;
      }

      // We have to have the values for the Key Fields of the Record
      if (!qx.lang.Type.isObject(keyValues)) {
        return false;
      }

      // Extract the route parameters, from the keyValues
      var route = this.__route(keyValues);
      if (route == null) { // Need a Key to Load the Record
        return false;
      }

      // Create the Service Request
      var req = new tc.services.json.TCServiceRequest();
      req.addListener("service-ok", function (e) {
        var original = e.getResult();
        this.setOriginal(original);
        this.fireEvent("dataLoaded");
      }, this);
      req.addListener("service-error", function (e) {
        this.fireEvent("error");
      }, this);

      // Send request
      return req.send(this.__service, 'read', route, null);
    },

    // interface implementation
    save: function () {
      // A Service has to be Defined
      if ((this.__service == null)) {
        return false;
      }

      if (this.isModified()) {
        var action, route = null;
        var original = this.getOriginal();
        var current = this.getCurrent();

        if (original == null) {
          action = 'create';
        } else {
          action = 'update';
          route = this.__route(original);
        }

        // Create the Service Request
        var req = new tc.services.json.TCServiceRequest();
        req.addListener("service-ok", function (e) {
          var original = e.getResult();
          this.setOriginal(original);
          this.fireEvent("dataSaved");
        }, this);
        req.addListener("service-error", function (e) {
          this.fireEvent("error");
        }, this);

        // Send request
        return req.send(this.__service, action, route, current);
      } else { // No changes, Nothing to do
        this.fireEvent("dataSaved");
        return true;
      }
    },

    // interface implementation
    setValue: function (name, value) {
      /* For Key Fields don't allow modifications (their values will either be auto created (i.e. through integer sequences) or
       * set through the load of the record
       */
      if (this.__keyFields && qx.lang.Array.contains(this.__keyFields, name)) {
        return this.getValue(name, true);
      }

      return this.base(arguments, name, value);
    },

    /*
     *****************************************************************************
     INTERNAL METHODS
     *****************************************************************************
     */

    /**
     *
     * @param source
     * @return {*}
     * @private
     */
    __route: function (source) {
      // Extract the route parameters, from the source object
      var route = [];

      if (this.__keyFields) {
        var key;
        for (var i = 0; i < this.__keyFields; ++i) {
          key = this.__keyFields[i];
          if (!source.hasOwnProperty(key) || (source[key] == null)) { // Missing a Value for a Key Field
            return null;
          }

          route.push(source[key]);
        }
      }

      return route.length ? route : null;
    }
  }
});
