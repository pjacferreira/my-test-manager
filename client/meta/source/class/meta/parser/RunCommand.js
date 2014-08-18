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
 * Base Meta Package Class
 */
qx.Class.define("meta.parser.RunCommand", {
  extend: meta.parser.AbstractRunner,
  include: [
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     INTERPRETER (OVERRIDE) METHODS
     ***************************************************************************
     */
    /** 
     * Perform the actual run of the Command 
     * 
     * @param command {meta.api.parser.IASTNode} AST Command to Run
     * @throw {String} if Failed to Start for any Reason
     */
    _run: function(command) {
      // Get the commands Name
      var values = command.getValue();
      var name = qx.lang.Type.isArray(values) ? values[0] : values;

      // Dispatch the Command      
      this.__dispatchCommand(name, command);
    },
    /** 
     * Test if the Node Provided to the Run is valid 
     * 
     * @param ast {meta.api.parser.IASTNode} AST to Test
     * @throw {String} if not a valid node
     */
    _validRootNode: function(ast) {
      // Is 'ast' a CMD Node?
      if (!this._isASTNode(ast, "CMD")) { // NO
        this._throwNodeException("CMD", ast);
      }
    },
    /*
     ***************************************************************************
     INTERPRETER METHODS
     ***************************************************************************
     */
    /** 
     * Run 'CREATE' Command
     * 
     * @param command {String} Command Name (i.e. 'create')
     * @param subtype {String} Create Subtype
     * @param parameters {Array|meta.api.parser.IASTNode} Parameters for Specific Create Type
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandCreate: function(command, subtype, parameters) {
      switch (subtype) {
        case 'service':
          return this._runCommandCreateService(parameters);
        case 'form':
          return this._runCommandCreateForm(parameters[0], parameters.length > 1 ? parameters[1] : false);
        default:
          throw "Unable to Create [" + subtype + "]";
      }
    },
    /** 
     * Run 'CREATE SERVICE' Command
     * 
     * @param service {meta.api.parser.IASTNode} Service ID
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandCreateService: function(service) {
      var id = this._extractID(service);
      if (id !== null) {
        // Create the Service
        var repository = this.getDI().get('metarepo');
        var ok = repository.getService(id,
          function(service) {
            // Return the Service (OBJECT Instance)
            this._fireResults(service);
          }, this._fireError, this);

        if (ok) {
          return;
        }
      }
      // ELSE: Error
      this._fireError("Missing Service Identifier.");
    },
    /** 
     * Run 'CREATE FORM' Command
     * 
     * @param form {meta.api.parser.IASTNode} Form ID
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandCreateForm: function(form) {
      var id = this._extractID(form);

      if (id !== null) {
        // Create the Service
        var repository = this.getDI().get('metarepo');
        var ok = repository.getForm(id,
          function(form) {
            // Return the Form (OBJECT Instance)
            this._fireResults(form);
          }, this._fireError, this);

        if (ok) {
          return;
        }
      }
      // ELSE: Error
      this._fireError("Missing Form Identifier.");
    },
    /** 
     * Run 'DISPLAY' Command
     * 
     * @param command {String} Command Name (i.e. 'display')
     * @param subtype {String} Display Subtype
     * @param parameters {Array|meta.api.parser.IASTNode} Parameters for Specific Execute Type
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandDisplay: function(command, subtype, parameters) {
      switch (subtype) {
        case 'form':
          return this._runCommandDisplayForm(parameters[0], parameters[1]);
        default:
          throw "Unable to Display [" + subtype + "]";
      }
    },
    /** 
     * Run 'DISPLAY FORM' Command
     * 
     * @param form {meta.api.parser.IASTNode} Form ID
     * @param allowed {meta.api.parser.IASTNode[]|null} Allowed Service List
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandDisplayForm: function(id, allowed) {
      var form = this._extractID(id);
      var services = allowed !== null ? this._extractASTPLS(allowed) : null;

      // Do we have service identifier?
      if (qx.lang.Type.isString(form)) { // YES
        this._createDisplayForm(form, services);
      } else if (qx.lang.Type.isObject(form) &&
        qx.Class.implementsInterface(form, meta.api.entity.IForm)) { // NO: It's a Form Object Instance
        // Display the Form
        this._displayForm(form, services);
      } else { // DEFAULT: Abort
        this._fireError("Invalid Service Identifier.");
      }
    },
    /** 
     * Run 'EXECUTE' Command
     * 
     * @param command {String} Command Name (i.e. 'execute')
     * @param subtype {String} Execute Subtype
     * @param parameters {Array|meta.api.parser.IASTNode} Parameters for Specific Execute Type
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandExecute: function(command, subtype, parameters) {
      switch (subtype) {
        case 'service':
          return this._runCommandExecuteService(parameters[0], parameters[1], parameters[2]);
        default:
          throw "Unable to Execute [" + subtype + "]";
      }
    },
    /** 
     * Run 'EXECUTE SERVICE' Command
     * 
     * @param id {meta.api.parser.IASTNode} Service ID
     * @param key {meta.api.parser.IASTNode|null} Service Key
     * @param parameters {meta.api.parser.IASTNode|null} Service Parameters
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandExecuteService: function(id, key, parameters) {
      var service = this._extractID(id);
      var key = key !== null ? this._extractASTPLS(key) : null;
      var parameters = parameters !== null ? this._extractASTPLS(parameters) : null;

      // Was a key provided for the service?
      if (key === null) { // NO: Use Previous Commands Output
        key = this._incoming;
      }

      // Were parameters provides for the service?
      if (parameters === null) { // NO: Use Previous Commands Output
        parameters = this._incoming;
      }

      // Do we have service identifier?
      if (qx.lang.Type.isString(service)) { // YES
        this._createExecuteService(service, key, parameters);
      } else if (qx.lang.Type.isObject(service) &&
        qx.Class.implementsInterface(service, meta.api.entity.IService)) { // NO: It's a Service Object Instance
        // Execute the Service
        this._executeService(service, key, parameters);
      } else { // DEFAULT: Abort
        this._fireError("Invalid Service Identifier.");
      }
    },
    /** 
     * Run 'WITH' Command
     * 
     * @param command {String} Command Name (i.e. 'with')
     * @param nodeEntity {meta.api.parser.IASTNode} Entity Node
     * @param nodeAction {meta.api.parser.IASTNode} Action Node
     * @throw {String} Throws Exception on Proccesing of Command
     */
    _runCommandWith: function(command, nodeEntity, nodeAction) {
      // Is this an Entity Node?
      if (!this._isASTNode(nodeEntity, "ENT")) { // NO?
        this._throwNodeException("ENT", nodeEntity);
      }

      // Is this an Action Node?
      if (!this._isASTNode(nodeAction, "ACT")) { // NO
        this._throwNodeException("ACT", nodeAction);
      }

      // Extract Entity Name and any Possible Key
      var entity = this._extractEntity(nodeEntity);
      var key = entity.length > 1 ? entity[1] : this._incoming;

      // Extract Action and Any Possible Parameters
      var action = this._extractAction(nodeAction, entity);
      var parameters = action.length > 1 ? action[1] : this._incoming;

      this._createExecuteService(entity[0] + ":" + action[0], key, parameters);
    },
    /*
     ***************************************************************************
     PROTECTED MEMBERS
     ***************************************************************************
     */
    /** 
     * Create an Instance of the Service with the Given ID and then Execute it.
     * 
     * @param id {string} Service ID
     * @param key {String[]|Map|null} Service Key
     * @param parameters {Map|null} Service Parameters
     * @throw {String} Throws Exception on Failure to Load/Execute the Service
     */
    _createExecuteService: function(id, key, parameters) {
      if (id !== null) {
        // Create the Service
        var repository = this.getDI().get('metarepo');
        var ok = repository.getService(id,
          function(service) {
            this._executeService(service, key, parameters);
          }, this._fireError, this);

        if (ok) {
          return;
        }
      }
      // ELSE: Error
      this._fireError("Missing Service Identifier.");
    },
    /** 
     * Execute the Provided Service.
     * 
     * @param service {meta.api.entity.IService} Service Instance
     * @param key {String[]|Map|null} Service Key
     * @param parameters {Map|null} Service Parameters
     * @throw {String} Throws Exception on Failute to Execute the Service
     */
    _executeService: function(service, key, parameters) {
      // Do we have a service to execute?
      if (service !== null) { // YES
        try {
          // Apply Service Key and Parameters
          this._applyServiceKey(service, key);
          this._applyServiceParameters(service, parameters);

          // Execute the Service
          service.
            execute(function(code, message, results) {
              this._fireResults(results, message, code);
            }, function(code, message, results) {
              this._fireError(message, code);
            }, this); // Call the Service
        } catch (e) { // Dispatch Error
          this._fireError(qx.lang.Type.isString(e) ? e : e.toString());
        }
      } else { // NO: Error
        this._fireError("Missing Service.");
      }
    },
    /** 
     * Create an Instance of the Form with the Given ID and then Display it.
     * 
     * @param id {String} Form ID
     * @param services {String[]} Allowed Services
     * @throw {String} Throws Exception on Failute to Display the Form
     */
    _createDisplayForm: function(id, services) {
      if (id !== null) {
        // Create the Service
        var repository = this.getDI().get('metarepo');
        var ok = repository.getForm(id,
          function(entity) {
            // Display the Newly Created Form Entity
            this._displayForm(entity, services);
          }, this._fireError, this);

        if (ok) {
          return;
        }
      }
      // ELSE: Error
      this._fireError("Missing Form Identifier.");
    },
    /** 
     * Display the Given Form.
     * 
     * @param entity {meta.api.entity.IForm} Form Instance
     * @param services {String[]} Allowed Services
     * @throw {String} Throws Exception on Failute to Display the Form
     */
    _displayForm: function(entity, services) {
      // Do we have a Form Entity?
      if (entity !== null) {
        // Create Meta Console
        var form = new meta.scratch.ui.FormContainer(
          new meta.entities.Widget('form-container', {
            type: 'form-container',
            label: 'Form'
          }), entity);

        // Capture Widget Ready
        form.addListenerOnce("widget-ready", function(e) {
          if (e.getOK()) {
            this.__displayForm(form, entity);
          } else {
            this._fireError(e.getMessage(), e.getCode());
          }
        }, this);

        // Initialize the Form
        form.initialize();
      } else { // NO
        this._fireError("Missing Form.");
      }
    },
    /** 
     * Extract Values from Entity Node
     * 
     * @param node {meta.api.parser.IASTNode} Entity Node
     * @throw {String} Throws Exception on Extracttion
     */
    _extractEntity: function(node) {
      var values = node.getValue();
      var entity, key = null;

      // Is the value of the Entity Node an Array?
      if (qx.lang.Type.isArray(values)) { // YES
        var entity = this._extractID(values[0]);
        var key = values.length > 1 ? values[1] : null;
      } else { // NO
        throw "Unexpected value for Entity Node [" + node.toString(false) + "]";
      }

      // Do we have a Key?
      if (key !== null) { // YES: Extract it's Values
        key = this._extractKey(key);
      }

      return key !== null ? [entity, key] : [entity];
    },
    /** 
     * Extract Values from Action Node
     * 
     * @param node {meta.api.parser.IASTNode} Entity Node
     * @param entity {String} Entity Name Associated with the Command
     * @throw {String} Throws Exception on Extracttion
     */
    _extractAction: function(node, entity) {
      var values = node.getValue();
      var action, params = null;

      // Is the value of the Action Node an Array?
      if (qx.lang.Type.isArray(values)) { // YES
        var action = this._extractID(values[0]);
        var params = values.length > 1 ? values[1] : null;
      } else { // NO
        throw "Unexpected value for Action Node [" + node.toString(false) + "]";
      }

      // Do we have Parameters?
      if (this._isASTNode(params, 'MAP')) { // YES
        params = this._extractASTMAP(params);
      }

      return params !== null ? [action, params] : [action];
    },
    _extractID: function(token) {
      // Is this an Identifier Token?
      if (this._isASTToken(token, 'IDT')) { // YES: Extract Values
        try {
          // TRY to Dereference Identifier
          return this._extractIDTValue(token.token);
        } catch (e) {
          // FAILED: Treat Identifer as a simple String
          return token.token;
        }
      } else if (this._isASTToken(token, 'STR')) { // NO: It's a String
        return token.token;
      }

      throw "Expecting [IDT | STR] for Identifier. Found [" + token.type + "]";
    },
    _extractKey: function(node) {
      // Is the AST a Parameter List?
      if (this._isASTNode(node, 'PLS')) { // YES: Extract Values
        return this._extractASTPLS(node);
      } else if (this._isASTNode(node, 'MAP')) { // NO: It's and Object Map
        return this._extractASTMAP(node);
      }
      // ELSE: Not a Valid Key Type      
      return null;
    },
    _extractASTPLS: function(node) {
      var key = [];

      // Loop through Parameter List to build the Key
      var value, values = node.getValue();
      for (var i = 0; i < values.length; ++i) {
        value = values[i];
        // Is the Values Entry and Token?
        if (this._isASTToken(value)) { // YES
          switch (value.type) {
            case 'IDT':
              try {
                // TRY to Dereference Identifier
                key.push(this._extractIDTValue(value.token));
              } catch (e) {
                // FAILED: Treat Identifer as a simple String
                key.push(value.token);
              }
              break;
            case 'STR':
            case 'NUM': // Only Valid Token Types for a Parameter List Key
              key.push(value.token);
          }
        }
      }

      return key.length > 0 ? key : null;
    },
    _extractASTMAP: function(node) {
      return node.getValue();
    },
    _expandMap: function(entity, map) {
      var newMap = {};

      // Make sure each property contains the correct entity:property syntax
      for (var property in map) {
        // Does the property belong to this map
        if (map.hasOwnProperty(property)) { // YES
          // does the property already have a leading entity identifier?
          if (property.indexOf(':') <= 0) { // NO
            newMap[entity + ':' + property] = map[property];
          } else { // YES
            newMap[property] = map[property];
          }
        }
      }

      return newMap;
    },
    /** 
     * Apply Key to Service is Any and Valid
     * 
     * @param service {meta.api.entity.IService} Service
     * @param key {String[]|Map|null} Possible Service Key
     * @throw {String} Throws Exception on Failure
     */
    _applyServiceKey: function(service, key) {
      if ((key !== null) && qx.lang.Type.isArray(key)) {
        var newKey = null;
        var keys = service.getKeyFields();

        // Build Key from Possible Key Entries
        var entry;
        for (var i = 0; i < keys.length; ++i) {
          entry = keys[i];
          // Does the Key Entry Length match the Key Array Length
          if (entry.length === key.length) { // YES: Found our match

            // Build Key Map
            newKey = {};
            for (var j = 0; j < entry.length; ++j) {
              newKey[entry[j]] = key[j];
            }
            break;
          }
        }

        key = newKey;
      }

      if (key !== null) {
        service.key(key);
      } else if (service.requireKey()) {
        throw "Service Requires a Key and none was provided";
      }
    },
    /** 
     * Apply Parameters to Service is Any and Valid
     * 
     * @param service {meta.api.entity.IService} Service
     * @param parameters {Map|null} Possible Service Key
     * @throw {String} Throws Exception on Failure
     */
    _applyServiceParameters: function(service, parameters) {
      if (parameters !== null) {
        service.parameters(parameters, true);
      } else if (service.requireParameters()) {
        throw "Service Requires Input Parameters and none was provided";
      }
    },
    /*
     ***************************************************************************
     META EVENT HANDLERS 
     ***************************************************************************
     */
    __displayForm: function(form, entity) {
      // Create a Window to Contain the Form
      var win = new qx.ui.window.Window(entity.getTitle());

      // Do we have an incoming Map?
      if (qx.lang.Type.isObject(this._incoming)) { // YES
        // Use it as input to the form
        form.getForm().setInput(this._incoming);
      }

      // Add Listeners on Window
      win.addListener("close", function(e) {
        // Was the Form Submitted?
        if (!win.hasOwnProperty('__submit')) { // NO: It was closed (by using (X) button)
          // Notifiy of Results
          this._fireResults(null);
        }
      }, this);

      // Add Listeners on the Form
      form.addListenerOnce("form-submit", function(e) {
        var output = null;
        // Are we submitting the Form?
        if (e.getOK()) { // YES
          // Does the Form have Output?
          if (form.getForm().hasOutput()) { // YES: Retrieve it
            output = form.getForm().getOutput();
          }
        }

        // Notifiy of Results
        this._fireResults(output);
        // Mark Window has Been Submitted or Canceled (by way of buttons)
        win.__submit = true;
        // Close the Window
        win.close();
      }, this);

      // Add Form to Window
      win.setLayout(new qx.ui.layout.HBox());
      win.add(form.getWidget());

      // Add Window to Display
      var root = qx.core.Init.getApplication().getRoot();
      root.add(win, {
        left: 5,
        right: 5
      });

      // Open the Form
      win.open();
    },
    /*
     ***************************************************************************
     PRIVATE MEMBERS
     ***************************************************************************
     */
    __dispatchCommand: function(command, node) {
      // Build Name of Possible Command Parser
      var parser = '_runCommand';
      if (command.length === 1) {
        parser += command.charAt(0).toUpperCase();
      } else {
        parser += command.charAt(0).toUpperCase() + command.slice(1);
      }

      if (this[parser] && qx.lang.Type.isFunction(this[parser])) { // YES
        var values = node.getValue();
        if (qx.lang.Type.isArray(values)) {
          if (values.length > 1) {
            return this[parser].apply(this, values);
          } else {
            values = values[0];
          }
        }

        // Reflect the Incoming Values to the Next Command
        this._reflectIncoming();
        return this[parser].call(this, command);
      } else { // NO
        throw "Unknown command [" + command + "]";
      }
    }
  } // SECTION: MEMBERS
});
