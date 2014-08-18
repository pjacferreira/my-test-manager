/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright: 2014 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/* 
 *
 */
qx.Class.define("utility.di.DependencyManager", {
  extend: qx.core.Object,
  implement: utility.api.di.IInjector,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Create instance of Service Requests Handler
   * 
   * @param base_url {String} Base URL for Service Requests
   */
  construct: function() {
    // Initialize Members
    this.__definitions = new utility.Map();
    this.__services = new utility.Map();
  },
  /**
   *
   */
  destruct: function() {
    // Clear all Member Fields
    this.__definitions = null;
    this.__services = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __definitions: null,
    __services: null,
    __defaults: {
      fixed: null, // Fixed Value (for service). NOTE: Fixed Services are Shared by Default
      classname: null, // Create an Instance of the Service by Class
      closure: null, // Create through a closure function that returns an instance of the service
      parameters: null, // Fixed Parameters to be passed to service creation
      shared: 'false' // Is this a shared service?
    },
    /*
     *****************************************************************************
     INTERFACE METHODS (utility.api.di.IInjector)
     *****************************************************************************
     */
    /**
     * Does the Service Exist?
     *
     * @param name {String} Service Name
     * @return {Boolean} 'true' if yes, 'false' otherwise
     */
    has: function(name) {
      // Do we have a name provided?
      name = utility.String.v_nullOnEmpty(name);
      if (name === null) { // NO
        return false;
      }

      return this.__definitions.has(name);
    },
    /**
     * Get instance of a service.
     *
     * @param name {String} Service Name
     * @param parameters {Var} An array of parameters, or a single parameter, to
     *   be passed to the service, on creation. 
     *   (for shared services, this is only used, the 1st time the service is created)
     * @return {Var} Value of dependency
     * @throw Exception if a Dependency with the Name Does not Exist, or cannot be
     *   created.
     */
    get: function(name, parameters) {
      // Do we have a name provided?
      name = utility.String.v_nullOnEmpty(name);
      if (name === null) { // NO
        throw "[name] Parameter is invalid or missing.";
      }

      // Does the Service Definition Exist?
      var definition = this.__definitions.get(name);
      if (definition === null) { // NO
        throw "Service [" + name + "] does not exist.";
      }

      // Is it a shared service, and if so, do we already have an instance?
      if (definition.shared && this.__services.has(name)) { // YES
        return this.__services.get(name);
      }

      // Are parameters defined?
      if (typeof parameters !== 'undefined') { // YES
        // Do we have a NON array parameter?
        if (!qx.lang.Type.isArray(parameters) && (parameters !== null)) { // NO: Convert to Array
          parameters = [parameters];
        }
      } else { // NO
        parameters = null;
      }

      // Merge Incoming Parameters with any existing Definition Parameters
      parameters = this.__mergeParameters(definition.parameters, parameters);

      // Get or Create an Instance of the Service
      var service = null;

      // Is the Service Defined by Class?
      if (definition.classname !== null) { // YES
        service = this.__createClassInstance(definition.classname, parameters);
      } else if (definition.closure !== null) { // NO: By Creation Function
        service = this.__createClosureInstance(definition.closure, parameters);
      } else if (definition.shared) { // NO: It's a Shared Service - So it must have a fixed value
        service = definition.fixed;
      } else { // NO: Invalid Service Definition!??
        throw "Service [" + name + "] has an invalid definition.";
      }

      // Do we have a value for a shared service?
      if (definition.shared && (service !== null)) { // YES: Cache it
        this.__services.add(name, service);
      }

      return service;
    },
    /**
     * Register a service definition.
     *
     * @param name {String} Service Name
     * @param definition {Var} definition of service
     * @param shared {Boolean} Is this shared service (i.e. a singleton), 'true' yes, 'false' otherwise
     *   (DEFAULT = false)
     * @return {utility.api.di.IInjector} this of object (for cascading creation)
     * @throw Exception on failure to register the service
     */
    set: function(name, definition, shared) {
      shared = !!shared;

      // Do we have a name provided?
      name = utility.String.v_nullOnEmpty(name);
      if (name === null) { // NO
        throw "[name] Parameter is invalid or missing.";
      }

      // Create a Definition for the Service
      var merge = null;
      // Do we want to create an object instance for the service?
      if (qx.lang.Type.isString(definition)) { // YES
        var classname = utility.String.v_nullOnEmpty(definition);
        if (classname !== null) {
          merge = {'classname': classname};
        }
      } else if (qx.lang.Type.isFunction(definition)) { // NO: We want to use a function to create an instance
        merge = {'closure': definition};
      } else if (qx.lang.Type.isObject(definition)) { // NO: We provided a specific definition
        merge = definition;
        shared = definition.hasOwnProperty('fixed');
      } else { // NO: Just a fixed Value
        merge = {'fixed': definition};
        shared = true;
      }

      // Invalid Definition?
      if (merge === null) { // YES
        throw "[definition] Parameter is invalid or missing.";
      }

      // Merge Definitions and Set the Shared Flag Accordingly:
      definition = qx.lang.Object.mergeWith(merge, this.__defaults, false);
      definition['shared'] = shared;

      // Save Definition
      var old_definition = this.__definitions.get(name);
      this.__definitions.add(name, definition);
      return old_definition;
    },
    /*
     *****************************************************************************
     PRIVATE METHODS
     *****************************************************************************
     */
    __mergeParameters: function(defaults, incoming) {
      // Do we have any Incoming Parameters?
      if (incoming === null) { // NO: Use Defaults
        return defaults;
      }

      /* Shoule we use Incoming Parameters?
       * Conditions:
       * 1. We have Incoming Parameters, but
       * 2. We have no Default Parameters, or
       * 3. Defaults contain a single parameters
       */
      if ((defaults === null) || (defaults.length === 1)) { // YES
        return incoming;
      }

      /* Current Scenario: 
       * 1. DEFAULTS !== NULL
       * 2. DEFAULTS.LENGTH > 1
       * 3. INCOMING !== NULL
       * 4. INCOMING.LENGTH >= 1
       */

      // Do we have more incoming parameters, than defaults?
      var parameters = incoming;
      if (incoming.length < defaults.length) { // YES
        // Is there only a single entry in the incoming array?
        if (incoming.length === 1) { // YES
          parameters = defaults.slice();
          parameters[0] = incoming[0];
        } else { // NO
          parameters = incoming.concat(defaults.slice(incoming.length));
        }
      }

      return parameters;
    },
    __constructor: function(clazz, args) {
      function F() {
        return clazz.apply(this, args);
      }
      F.prototype = clazz.prototype;
      return new F();
    },
    /**
     * Create and Instance of a Service Based on a Class
     *
     * @param classname {String} Class Name
     * @param parameters {Array} Parameters to Pass to Constructor
     * @return {Var} Service Instance
     * @throw Exception On any failure to create a service instance
     */
    __createClassInstance: function(classname, parameters) {
      var parts = classname.split(".");
      var parent = (window || this);
      if (parts.length > 1) {
        for (var i = 0; i < parts.length - 1; ++i) {
          if (typeof parent[parts[i]] === 'object') {
            parent = parent[parts[i]];
          } else {
            parent = null;
            break;
          }
        }
      }

      if ((parent !== null) && (typeof parent[parts[parts.length - 1]] === 'function')) {
        var instance = this.__constructor(parent[parts[parts.length - 1]], parameters);
        // Is the Object DI Injectable?
        if (qx.Class.implementsInterface(instance, utility.api.di.IInjectable)) { // YES
          // Inject Current DI
          instance.setDI(this);
        }
        return instance;
      } else {
        throw "Class [" + classname + "] doesn't exist.";
      }
    },
    /**
     * Create and Instance of a Service Based on a Closure Function
     *
     * @param closure {Function} Closure Function to Call
     * @param parameters {Array} Parameters to Pass to Constructor
     * @return {Var} Service Instance
     * @throw Exception On any failure to create a service instance
     */
    __createClosureInstance: function(closure, parameters) {
      return parameters !== null ? closure.apply(this, parameters) : closure.call(this);
    }
  } // SECTION: MEMBERS
});
