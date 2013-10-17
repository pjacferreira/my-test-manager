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
 * This is the main application class of your custom application "testcenter_web"
 */
qx.Class.define("tc.Application", {
  extend: qx.application.Standalone,
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */

  members: {
    __sessionState: null,
    __paneHeader: null,
    __paneContent: null,
    __toaster: null,
    __tabHolder: null,
    /**
     * This method contains the initial application code and gets called
     * during startup of the application
     *
     * @lint ignoreDeprecated(alert)
     */
    main: function() {
      // Call super class
      this.base(arguments);

      // Initialize Memebers
      this.__sessionState = {};

      // Enable logging in debug variant
      if (qx.core.Environment.get("qx.debug")) {
        // support native logging capabilities, e.g. Firebug for Firefox
        qx.log.appender.Native;
        // support additional cross-browser console. Press F7 to toggle visibility
        qx.log.appender.Console;
      }

      // Create Header Pane
      this.__initializeHeader();

      // Create the Seperate Panes
      this.__initializeContent();

      // Create Toaster
      this.__toaster = new tc.toaster.Manager();
    },
    __initializeHeader: function() {
      // Create the layout for the Header Pane
      var layout = new qx.ui.layout.HBox(5, 'right');
      layout.setReversed(true);

      // Create Page Header
      this.__paneHeader = new qx.ui.container.Composite();
      this.__paneHeader.setLayout(layout);
      this.__paneHeader.setHeight(30);

      // Add the Pane to the Window
      this.getRoot().add(this.__paneHeader, {
        top: 5,
        left: 5,
        right: 5
      });

      // Create Login Widget
      var widget = this.__createLoginWidget();

      // Add Handlers for Session Login/Logout      
      var ec = tc.event.EventCentral.getInstance();
      ec.addListener(ec.basicFilter("session", ["login", "logout"]), function(e) {
        var login = e.getEventSubType() === "login";
        login ? this.__doLogin(e.getData()) : this.__doLogout();
      }, this);

      // Create Container for Widgets in the Header Pane
      this.__paneHeader.$$widget = {};

      // Add Login Widget to the Header Pane
      this.__addHeaderWidget('login', widget);
    }, // FUNCTION: __initializeHeader
    __initializeContent: function() {
      // Create Content Pane
      this.__paneContent = new qx.ui.container.Composite();
      this.__paneContent.setLayout(new qx.ui.layout.Canvas());

      // Add the Pane to the Window
      this.getRoot().add(this.__paneContent, {
        top: 50,
        left: 5,
        right: 5,
        bottom: 5
      });
    }, // FUNCTION: __initializeContent
    __doLogin: function(user) {
      if (!this.__sessionState['user']) {
        // Save Current Session User
        this.__sessionState['user'] = user;

        // Create the Organization Widget (Only After Session User Set)
        var widget = this.__createOrganizationWidget();

        // Add Handler for Session Organization Change
        var ec = tc.event.EventCentral.getInstance();
        ec.addListener(ec.basicFilter("session", "organization"), function(e) {
          var organization = e.getData();
          (organization !== "null") ? this.__setOrganization(organization) : this.__clearOrganization();
        }, this);

        // Add Widget to the Header Pane
        this.__addHeaderWidget('organizations', widget);

        // Add Tabs (User Profile, Users Manager, Organizations Manager)
        this.__addTabUser();
        this.__addTabUserManage();
        this.__addTabOrganizations();

        // NOTIFICATION
        this.__toaster.add('Logged In [' + user['user:name'] + ']');
      }
    },
    __doLogout: function() {
      if (this.__sessionState['user']) {
        // Remove Current Session User
        delete this.__sessionState['user'];

        // Remove All Un-necessary Widgets
        var widgets = ['projects', 'organizations'];
        for (var i = 0; i < widgets.length; ++i) {
          this.__removeHeaderWidget(widgets[i]);
        }

        // Remove All Tabs - Clear Content Pane
        this.__tabHolder.removeGroup();

        // NOTIFICATION
        this.__toaster.add('User Logged Out');
      }
    },
    __setOrganization: function(orgId) {
      tc.services.Session.setOrganization(orgId,
              function(organization) {
                this.__sessionState['organization'] = organization;

                var widget = this.__getHeaderWidget('projects');
                if (widget === null) {
                  // Create and Add New Projects Widget
                  widget = this.__createProjectWidget();

                  //Add Handler for Session Project Change
                  var ec = tc.event.EventCentral.getInstance();
                  ec.addListener(ec.basicFilter("session", "project"), function(e) {
                    var project = e.getData();
                    (project !== "null") ? this.__setProject(project) : this.__clearProject();
                  }, this);

                  // Add Widget to the Header Pane
                  this.__addHeaderWidget('projects', widget);
                } else { // Force Widget Refresh
                  widget.refresh();
                }

                // Add Projects Tab
                this.__addTabProjects(true);

                // NOTIFICATION
                this.__toaster.add('New Organization [' + orgId + ']');
              },
              function(error) {
                this.__toaster.add('Error Setting Organization [' + orgId + ']');
              }, this);
    },
    __clearOrganization: function() {
      /* TODO: If We hane No Session Organization, the Organization Widget has
       * to reflect that, or we should not be allowed to clear the Session Organization.
       */
      if (this.__sessionState['organization']) {
        tc.services.Session.clearOrganization(
                function(result) {
                  // Remove Current Session Organization
                  delete this.__sessionState['organization'];

                  // Remove Un-necessary Elements
                  this.__removeHeaderWidget('projects');
                  this.__tabHolder.removePage('management', 'projects');
                  this.__tabHolder.removeGroup('tests');
                  this.__tabHolder.removeGroup('runs');

                  // Notification
                  this.__toaster.add('Organization Cleared');
                },
                function(error) {
                  this.__toaster.add('Error Clearing Session Organization');
                }, this);
      }
    },
    __setProject: function(projectId) {
      tc.services.Session.setProject(projectId,
              function(project) {
                // Save Current Session Project
                this.__sessionState['project'] = project;

                // Add Tab Tests and Runs
                this.__addTabTests(true);
                this.__addTabRuns(true);

                // Set the Session Project
                this.__toaster.add('Project Project [' + projectId + ']');
              },
              function(error) {
                this.__toaster.add('Error Setting Project [' + projectId + ']');
              }, this);
    },
    __clearProject: function() {
      /* TODO: If We hane No Session Project, the Project Widget has
       * to reflect that, or we should not be allowed to clear the Session Project.
       */

      if (this.__sessionState['project']) {
        tc.services.Session.clearProject(
                function(result) {
                  // Remove Current Session Project
                  delete this.__sessionState['project'];

                  // Remove Un-necessary Elements
                  this.__tabHolder.removeGroup('tests');
                  this.__tabHolder.removeGroup('runs');

                  // Notification
                  this.__toaster.add('Project Cleared');
                },
                function(error) {
                  this.__toaster.add('Error Clearing Session Project');
                }, this);
      }
    },
    __getHeaderWidget: function(id) {
      return this.__paneHeader.$$widget.hasOwnProperty(id) ? this.__paneHeader.$$widget[id] : null;
    },
    __addHeaderWidget: function(id, widget) {
      if (!this.__paneHeader.$$widget[id]) {
        this.__paneHeader.$$widget[id] = widget;
        this.__paneHeader.add(widget);

        return true;
      }

      // TODO Log Duplicate Add
      return false;
    },
    __removeHeaderWidget: function(id) {
      if (this.__paneHeader.$$widget[id]) {
        this.__paneHeader.remove(this.__paneHeader.$$widget[id]);
        delete this.__paneHeader.$$widget[id];

        return true;
      }

      return false;
    },
    __createLoginWidget: function() {
      // Create the Login Widget
      var widget = new tc.widgets.LoginWidget();

      // Hook Event Central to Login/Logout Process
      var ec = tc.event.EventCentral.getInstance();
      ec.bindDataEvent(widget, "user-change", "session", "login");
      ec.bindEvent(widget, "no-user", "session", "logout");

      return widget;
    }, // FUNCTION: __createLoginWidget
    __createOrganizationWidget: function() {
      // Create the Organization Widget (Only After Session User Set)
      var widget = qx.ui.form.SelectBox();
      var model = new qx.data.Array('Loading');
      var controller = new qx.data.controller.List(model, widget);

      // Start Loading the List into the Widget
      this.__startListLoad(controller, 'organization:user',
              function() {
                this.__toaster.add('Organization Widgets Loaded');
              },
              function(message) {
                this.__toaster.add('Failed to Load Organization Wdigets [' + message + ']');
              }, this);

      // Capture Change Selection
      widget.addListener("changeSelection", function(e) {
        if (widget.isReady() && !widget.isSelectionEmpty()) {
          // Get Selection
          var selection = e.getData();

          // Get the Organization ID Associated with the Selection
          var id = selection[0].getModel().getId();

          // Fire Event Central Session Organization Change Event
          var ec = tc.event.EventCentral.getInstance();
          ec.fireDataEvent("session", "organization", id);
        }
      }, this);

      return widget;
    }, // FUNCTION: __createOrganizationWidget
    __createProjectWidget: function() {
      // Create and Add New Projects Widget
      var widget = qx.ui.form.SelectBox();
      var model = new qx.data.Array('Loading');
      var controller = new qx.data.controller.List(model, widget);

      // Start Loading the List into the Widget
      this.__startListLoad(controller, 'project:user_org',
              function() {
                this.__toaster.add('Project Widgets Loaded');
              },
              function(message) {
                this.__toaster.add('Failed to Load Project Wdigets [' + message + ']');
              }, this);

      // Capture Change Selection
      widget.addListener("changeSelection", function(e) {
        if (widget.isReady() && !widget.isSelectionEmpty()) {
          // Get Selection
          var selection = e.getData();

          // Get the Organization ID Associated with the Selection
          var id = selection[0].getModel().getId();

          // Fire Event Central Session Organization Change Event
          var ec = tc.event.EventCentral.getInstance();
          ec.fireDataEvent("session", "project", id);
        }
      }, this);

      return widget;
    }, // FUNCTION: __createProjectWidget       
    /**
     * Builds a Widget for Reference Fields. 
     * NOTE: see notes {@link qx.data.marshal.IMarshalerDelegate.}
     *
     * @param controller {qx.data.controller.List} the Controller for the List Widget
     * @param listId {String} Meta List ID to use for Loading the Widget
     */
    __startListLoad: function(controller, listId, ok, nok, context) {
      if (!qx.lang.Type.isObject(context)) {
        context = this;
      }

      // Create List Package and Initialize it
      var package = new tc.meta.packages.ListPackage(listId);

      // Initialize the List Package
      package.initialize({
        'ok': function() {
          // Now We Ready the List
          var list = package.getList();

          // Build a Fields Package, with all the fields required to build the Adaptor Class
          var fields = list.getColumns();
          fields.push(list.getKeyField());
          fields.push(list.getDisplayField());
          fields = new tc.meta.packages.FieldsPackage(fields);

          // Initialize the Fields Package
          fields.initialize({
            'ok': function() {

              // Initialize the Services Packages
              var services = package.getServices();
              services.initialize({
                'ok': function() {
                  var listService = services.getService('list');

                  // Note: The List is Loaded Asynchronously (The Widget is Built, before we have the items to load into it)
                  listService.execute(null, {
                    'ok': function(records) {
                      var clazz = tc.meta.widgets.DataAdaptor.buildClazz(fields);

                      var items = [];
                      for (var i = 0; i < records.length; ++i) {
                        items.push(new clazz(records[i]));
                      }

                      var model = new qx.data.Array(items);
                      if (items.length) {
                        controller.setLabelPath(clazz.fieldToProperty(list.getDisplayField()));
                      }
                      controller.setModel(model);
                      if (qx.lang.Type.isFunction(ok)) {
                        ok.call(context);
                      }
                    },
                    'nok': function(message) {
                      if (qx.lang.Type.isFunction(nok)) {
                        nok.call(context, message);
                      }
                    },
                    context: this
                  });
                },
                'nok': function(message) {
                  if (qx.lang.Type.isFunction(nok)) {
                    nok.call(context, message);
                  }
                },
                context: this
              });
            },
            'nok': function(message) {
              if (qx.lang.Type.isFunction(nok)) {
                nok.call(context, message);
              }
            },
            context: this
          });
        },
        'nok': function(message) {
          if (qx.lang.Type.isFunction(nok)) {
            nok.call(context, message);
          }
        },
        'context': this
      });

      return true;
    }, // FUNCTION: __startListLoad    
    __createTabHolder: function() {
      // Create Tab Group Manager
      this.__tabHolder = new tc.widgets.TabManager(new qx.ui.tabview.TabView());

      // Create Groups
      this.__tabHolder.addGroup('management', this.__tabHolder.first());
      this.__tabHolder.addGroup('tests', this.__tabHolder.last());
      this.__tabHolder.addGroup('runs', this.__tabHolder.last());
      this.__tabHolder.addGroup('profile', this.__tabHolder.last());

      this.__paneContent.add(this.__tabHolder.getView(), {
        top: 70,
        left: 5,
        right: 5,
        bottom: 5
      });
    },
    __addTabUser: function() {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder === null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('profile', 'user')) {
        // Create the Model
        tc.services.Session.whoami(function(user) {
          // Create Tab Page
          var tab = new qx.ui.tabview.Page('Profile', 'resource/tc/user_gray.png');
          tab.setLayout(new qx.ui.layout.VBox());

          // Set Load Hint
          tab.add(new qx.ui.basic.Label("Loading User Profile..."));

          // Create and Build Form
          var form = new tc.meta.forms.Form('user:update', new tc.meta.datastores.RecordStore(), user);
          // Event : Data Loaded from Backend
          form.addListener("formSubmitted", function(e) {
            this.info("Data Saved");
          }, this);
          // Event : Data Synchronized to Backend
          form.addListener("formCancelled", function(e) {
            this.info("Form Cancelled");
          }, this);
          // Event : Error Loading Form or in Data Synchronization
          form.addListener("nok", function(e) {
            tab.add(new qx.ui.basic.Label("Error Loading User Profile"));
            this.error("Storage Access Error");
          }, this);
          // Initialize Form
          form.initialize(user, {
            'ok': function(e) {
              // Load the User Record (if Possible)
              var model = form.getModel();
              if (model.canLoad()) {
                model.load();
              }
              // Remove Existing Elements
              tab.removeAll();
              // Set New layout
              tab.setLayout(new qx.ui.layout.Basic());

              /*          
               var scrollContainer = new qx.ui.container.Scroll();
               scrollContainer.add(new qx.ui.form.renderer.Single(form));
               tabUser.add(scrollContainer);
               */
              tab.add(new qx.ui.form.renderer.Single(form));
            },
            'nok': function(e) {
              tab.add(new qx.ui.basic.Label("Error Loading User Profile"));
              this.error("Form Error");
            },
            'context': this
          });

          // Add Tab and Save Reference
          this.__tabHolder.addPage('profile', 'user', tab);
        }, null, this);
      }
    },
    __addTabUserManage: function(refresh) {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder == null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('management', 'users')) {
        // Create Tab Page
        var tab = new qx.ui.tabview.Page('User Manager');
        tab.setLayout(new qx.ui.layout.VBox());

        // Set Load Hint
        tab.add(new qx.ui.basic.Label("Loading..."));

        // Create the Table and add it to the Tab
        this.__addTableToTab('user:manage', tab);

        // Add TAB to Tab Manager
        this.__tabHolder.addPage('management', 'users', tab, true);
      } else if (refresh) {
        // Get the TAB
        var tab = this.__tabHolder.getPage('management', 'users');

        // Extract the Hidden Table Object and Force a Reload of the Datas
        tab.$$table.getTableModel().reloadData();
      }
    }, // FUNCTION: __addTabUserManage
    __addTabOrganizations: function(refresh) {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder === null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('management', 'organizations')) {
        // Create Tab Page
        var tab = new qx.ui.tabview.Page('Organizations');
        tab.setLayout(new qx.ui.layout.VBox());

        // Set Load Hint
        tab.add(new qx.ui.basic.Label("Loading..."));

        // Create the Table and add it to the Tab
        this.__addTableToTab('organization:manage', tab);

        // Add TAB to Tab Manager
        this.__tabHolder.addPage('management', 'organizations', tab);
      } else if (refresh) {
        // Get the TAB
        var tab = this.__tabHolder.getPage('management', 'organizations');

        // Extract the Hidden Table Object and Force a Reload of the Datas
        tab.$$table.getTableModel().reloadData();
      }
    }, // FUNCTION: __addTabOrganizations
    __addTabProjects: function(refresh) {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder === null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('management', 'projects')) {
        // Create Tab Page
        var tab = new qx.ui.tabview.Page('Projects');
        tab.setLayout(new qx.ui.layout.VBox());

        // Set Load Hint
        tab.add(new qx.ui.basic.Label("Loading..."));

        // Create the Table and add it to the Tab
        this.__addTableToTab('project:manage', tab);

        // Add TAB to Tab Manager
        this.__tabHolder.addPage('management', 'projects', tab);
      } else if (refresh) {
        // Get the TAB
        var tab = this.__tabHolder.getPage('management', 'projects');

        // Extract the Hidden Table Object and Force a Reload of the Datas
        tab.$$table.getTableModel().reloadData();
      }
    }, // FUNCTION: __addTabProjects
    __addTabTests: function() {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder === null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('tests', 'page')) {
        // Create Tab Page
        var tab = new qx.ui.tabview.Page('Tests');
        tab.setLayout(new qx.ui.layout.VBox());

        // Set Load Hint
        tab.add(new qx.ui.basic.Label("Test Management / Sets Management"));

        this.__tabHolder.addPage('tests', 'page', tab);
      }
    }, // FUNCTION: __addTabTests
    __addTabRuns: function() {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder === null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('runs', 'page')) {
        // Create Tab Page
        var tab = new qx.ui.tabview.Page('Runs');
        tab.setLayout(new qx.ui.layout.VBox());

        // Set Load Hint
        tab.add(new qx.ui.basic.Label("Test Runs"));

        this.__tabHolder.addPage('runs', 'page', tab);
      }
    }, // FUNCTION: __addTabRuns
    __addTableToTab: function(table, tab) {
      // Create Meta Table
      var metaPackage = new tc.meta.packages.TablePackage(table);
      var model = new tc.table.model.MetaTableModel(metaPackage);
      model.initialize({
        'ok': function() {
          // TODO Do the same thing that was done to with sort-on, to filter-on
          var table = new tc.table.filtered.Table(model);

          // Disable Footer
          table.setStatusBarVisible(false);

          // ** Composite Toolbar + Table **
          var composite = new qx.ui.container.Composite();
          composite.setLayout(new qx.ui.layout.VBox(2));

          // Set Table Size
          var t_width = 100 * model.getColumnCount() + 20;
          table.set({
            width: t_width > 600 ? 600 : t_width,
            height: 400,
            decorator: null
          });

          // Create Toolbar for Table - If Possible
          var toolbar = tc.table.widget.TableToolbarBuilder.build(metaPackage, table);
          if (toolbar !== null) {
            toolbar.setShow("icon");
            composite.add(toolbar);
          }

          composite.add(table);

          // Clear Tab before Adding Table
          tab.removeAll();
          tab.add(composite);
          tab.$$table = table;
        },
        'nok': function(e) {
          tab.removeAll();
          tab.add(new qx.ui.basic.Label("Error Loading Table..."));
          this.__toaster.add('Failed to Load Table Model.');
        },
        'context': this
      });
    } // FUNCTION: __addTableToTab
    /*            
     __buildActionRegitry: function() {
     this.__actionRegistry = tc.actions.Registry.getInstance();
     
     // Build Registry
     this.__actionRegistry.register("login", "tc.actions.ActionLogin");
     this.__actionRegistry.register("set-organization", "tc.actions.ActionSessionOrganization");
     this.__actionRegistry.register("set-project", "tc.actions.ActionSessionProject");
     this.__actionRegistry.register("create-user", "tc.actions.ActionMetaForm", function(action) {
     
     // Set Action Properties
     action.setShortcut("CTRL+N");
     action.setLabel("Create...");
     action.setIcon("tc/user_add.png")
     action.setToolTipText("Create a New User");
     
     // Build the Data Model for the MetaForm
     
     // Initialize Meta Service
     var service = tc.services.TCMetaService.getInstance();
     service.setBaseURL('meta');
     
     var sourceMetadata = new tc.metaform.FormLoader(service);
     var metadataModel = new tc.metaform.DefaultMetadataModel('user', 'create', sourceMetadata);
     // Create the Data Model
     // TODO Howto Manage DataStore
     var sourceData = new tc.metaform.TCDataStore('user');
     
     // Create the Model
     action.setModel(new tc.metaform.DefaultModel(metadataModel, sourceData));
     }, this);
     
     // Add Listeners to Login so we can activate the Other Options
     var login = this.__actionRegistry.getAction("login");
     if (login) {
     login.addListener("logged-in", function(e) {
     this.__actionRegistry.enable("set-organization", true);
     this.__doLogin(e.getData());
     }, this);
     login.addListener("logged-out", function(e) {
     this.__actionRegistry.enable("set-organization", false);
     this.__actionRegistry.enable("set-project", false);
     this.__doLogout();
     }, this);
     }
     
     
     var organization = this.__actionRegistry.getAction("set-organization");
     if (organization) {
     organization.addListener("organization-set", function(e) {
     this.__actionRegistry.enable("set-project", true);
     }, this);
     }
     
     var organization = this.__actionRegistry.getAction("set-project");
     if (organization) {
     organization.addListener("project-set", function(e) {
     this.debug("Project Set...");
     }, this);
     }
     }, // FUNCTION: __buildActionRegitry
     __newCommand: function(entity, mode) {
     var command = null;
     switch (mode) {
     case 'create': // Create Entity
     command = new qx.ui.core.Command("CTRL+N");
     command.setLabel("Create User");
     command.setIcon("tc/user_add.png")
     command.setToolTipText("Create User.");
     command.addListener("execute", function() {
     // Setup Metadata Model
     // Initialize Meta Service
     var service = tc.services.TCMetaService.getInstance();
     service.setBaseURL('meta');
     
     var sourceMetadata = new tc.metaform.FormLoader(service);
     var metadataModel = new tc.metaform.DefaultMetadataModel(entity, 'create', sourceMetadata);
     // Setup Model Data Source
     var sourceData = new tc.metaform.TCDataStore(entity);
     // Create the Model
     var model = new tc.metaform.DefaultModel(metadataModel, sourceData);
     // Create the Form
     var form = new tc.metaform.Form();
     
     // Event : Form Ready (Initialized)
     var dialog = null;
     form.addListener("formReady", function(e) {
     // Create Dialog
     dialog = new tc.windows.FormDialog(model.getFormTitle(), new qx.ui.form.renderer.Single(form));
     
     // Add it to the Application Root
     this.getRoot().add(dialog, {
     left: 50,
     top: 50
     });
     
     // Display the Dialog
     dialog.show();
     }, this);
     // Event : Data Loaded from Backend
     form.addListener("formSubmitted", function(e) {
     alert("Data Saved");
     if (dialog != null) {
     dialog.close();
     }
     }, this);
     // Event : Data Synchronized to Backend
     form.addListener("formCancelled", function(e) {
     alert("Form Cancelled");
     if (dialog != null) {
     dialog.close();
     }
     }, this);
     // Event : Error Loading Form or in Data Synchronization
     form.addListener("error", function(e) {
     alert("Form Error");
     if (dialog != null) {
     dialog.close();
     }
     }, this);
     
     // Set the Form and Initialize
     form.setFormModel(model);
     }, this);
     break;
     case 'read': // Read Entity Information
     command = new qx.ui.core.Command("CTRL+R");
     command.setLabel("Read");
     command.setIcon("tc/vcard.png")
     command.setToolTipText("Detailed User Information");
     command.addListener("execute", function() {
     alert("Detailed User Information");
     }, this);
     break;
     case 'update': // Update Entity
     command = new qx.ui.core.Command("CTRL+E");
     command.setLabel("Edit");
     command.setIcon("tc/user_edit.png")
     command.setToolTipText("Edit User Data");
     command.addListener("execute", function() {
     alert("Edit User Information");
     }, this);
     break;
     case 'delete': // Delete Entity
     command = new qx.ui.core.Command("CTRL+D");
     command.setLabel("Delete");
     command.setIcon("tc/user_delete.png")
     command.setToolTipText("Delete User");
     command.addListener("execute", function() {
     alert("Delete User");
     }, this);
     }
     
     return command;
     },
     __newButton: function(command, show) {
     
     if (command != null) {
     var button = new qx.ui.toolbar.Button();
     button.setCommand(command);
     if (show) {
     button.setShow(show);
     }
     
     return button;
     }
     return null;
     },
     __loadTable: function(entity) {
     // Initialize Meta Service
     var service = tc.services.TCMetaService.getInstance();
     service.setBaseURL('meta');
     
     // Create Model from Meta Data
     var model = new tc.table.model.MetaTableModel(entity + ':',
     new tc.table.meta.TableSource(service));
     
     // Add Event Listeners
     model.addListener("metadataLoaded", function() {
     // TODO Do the same thing that was done to with sort-on, to filter-on
     var table = new tc.table.filtered.Table(model);
     
     // Disable Footer
     table.setStatusBarVisible(false);
     
     // ** Create Toolbar **
     var toolbar = new qx.ui.toolbar.ToolBar();
     var buttons = ['create', 'read', 'update', 'delete'];
     var button = null;
     for (var i = 0; i < buttons.length; ++i) {
     button = this.__newButton(this.__newCommand(entity, buttons[i]));
     if (button !== null) {
     toolbar.add(button);
     }
     }
     toolbar.setShow("icon");
     
     // ** Composite Toolbar + Table **
     var composite = new qx.ui.container.Composite();
     composite.setLayout(new qx.ui.layout.VBox(2));
     
     // Set Table Size
     var t_width = 100 * model.getColumnCount() + 20;
     table.set({
     width: t_width > 600 ? 600 : t_width,
     height: 400,
     decorator: null
     });
     
     composite.add(toolbar);
     composite.add(table);
     
     this.getRoot().add(composite, {
     left: 0,
     top: 200
     });
     
     // ** Button to Toggle Table Filter **
     // Create a Button to Open User Form Window
     var btnFilter = new qx.ui.form.Button("Toggle Filter");
     btnFilter.addListener("execute", function() {
     this.toggleFilterVisible();
     }, model);
     
     this.getRoot().add(btnFilter, {
     left: 0,
     top: 140
     });
     }, this);
     
     model.addListener("metadataInvalid", function(e) {
     alert("Failed to Load Table Model.");
     });
     
     // Initialize the Model
     model.load();
     }, // FUNCTION: __loadTable
     
     __applicationMenu: function() {
     // Create Menu Bar (Placeholder for Menu Entries)
     var bar = new qx.ui.menubar.MenuBar();
     
     // Create Top Level Menus
     var menuSession = new qx.ui.menubar.Button("Session");
     var menuAdministration = new qx.ui.menubar.Button("Administration");
     var menuTesting = new qx.ui.menubar.Button("Testing");
     var menuProfile = new qx.ui.menubar.Button("Profile");
     bar.add(menuSession);
     bar.add(menuAdministration);
     bar.add(menuTesting);
     bar.add(menuProfile);
     
     // Create Individual Menus
     menuSession.setMenu(this.__sessionMenu());
     menuAdministration.setMenu(this.__administrationMenu());
     menuTesting.setMenu(this.__testingMenu());
     menuProfile.setMenu(this.__profilesMenu());
     
     return bar;
     }, // FUNCTION: __applicationMenu
     
     __getCommand: function(name) {
     
     var command = null;
     
     switch (name) {
     case 'sudo':
     command = new qx.ui.core.Command();
     command.setLabel("Run As");
     command.addListener("execute", function(e) {
     this.__debugCommand(e);
     }, this);
     break;
     default:
     command = this.__actionRegistry.getCommand(name);
     break;
     }
     
     return command;
     
     }, // FUNCTION: __sessionMenu
     
     __sessionMenu: function() {
     var menu = new qx.ui.menu.Menu();
     
     menu.add(new qx.ui.menu.Button(null, null, this.__getCommand('login')));
     menu.add(new qx.ui.menu.Button(null, null, this.__getCommand('sudo')));
     menu.add(new qx.ui.menu.Separator());
     menu.add(new qx.ui.menu.Button(null, null, this.__getCommand('set-organization')));
     menu.add(new qx.ui.menu.Button(null, null, this.__getCommand('set-project')));
     
     return menu;
     }, // FUNCTION: __sessionMenu
     
     __administrationMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("User", null, null, this.__adminUsersMenu()));
     menu.add(new qx.ui.menu.Button("Organization", null, null, this.__adminOrganizationsMenu()));
     menu.add(new qx.ui.menu.Button("Project", null, null, this.__adminProjectsMenu()));
     return menu;
     }, // FUNCTION: __administrationMenu
     
     __adminUsersMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button(null, null, this.__getCommand('create-user')));
     menu.add(new qx.ui.menu.Button("Modify..."));
     menu.add(new qx.ui.menu.Button("Assign", null, null, this.__adminUsersAssignMenu()));
     menu.add(new qx.ui.menu.Button("Delete..."));
     return menu;
     }, // FUNCTION: __adminUsersMenu
     
     __adminUsersAssignMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("Organizations..."));
     menu.add(new qx.ui.menu.Button("Projects..."));
     menu.add(new qx.ui.menu.Button("Permissions..."));
     return menu;
     }, // FUNCTION: __adminUsersAssignMenu
     
     __adminOrganizationsMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("New..."));
     menu.add(new qx.ui.menu.Button("Modify..."));
     menu.add(new qx.ui.menu.Button("Assign..."));
     menu.add(new qx.ui.menu.Button("Delete..."));
     return menu;
     }, // FUNCTION: __adminOrganizationsMenu
     
     __adminProjectsMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("New..."));
     menu.add(new qx.ui.menu.Button("Modify..."));
     menu.add(new qx.ui.menu.Button("Delete..."));
     return menu;
     }, // FUNCTION: __adminProjectsMenu
     
     __testingMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("Tests", null, null, this.__testingTestsMenu()));
     menu.add(new qx.ui.menu.Button("Test Sets", null, null, this.__testingSetsMenu()));
     menu.add(new qx.ui.menu.Button("Runs", null, null, this.__testingRunsMenu()));
     return menu;
     }, // FUNCTION: __testingMenu
     
     __testingTestsMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("New..."));
     menu.add(new qx.ui.menu.Button("Modify..."));
     menu.add(new qx.ui.menu.Button("Delete..."));
     return menu;
     }, // FUNCTION: __testingTestsMenu
     
     __testingSetsMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("New..."));
     menu.add(new qx.ui.menu.Button("Modify..."));
     menu.add(new qx.ui.menu.Button("Delete..."));
     return menu;
     }, // FUNCTION: __testingSetsMenu
     
     __testingRunsMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("New..."));
     menu.add(new qx.ui.menu.Button("Continue..."));
     menu.add(new qx.ui.menu.Button("Close..."));
     return menu;
     }, // FUNCTION: __testingRunsMenu
     
     __profilesMenu: function() {
     var menu = new qx.ui.menu.Menu();
     menu.add(new qx.ui.menu.Button("Place Holder..."));
     return menu;
     }, // FUNCTION: __profilesMenu
     
     __debugCommand: function(e) {
     this.debug("Execute command: " + e.getTarget().getLabel());
     }, // FUNCTION: __debugCommand
     
     __doLogInOut: function(command, login) {
     
     if (login) {
     // Create the Form
     var form = new tc.forms.Login();
     
     // Create Dialog
     var dialog = new tc.windows.FormDialog('Please Login', new qx.ui.form.renderer.Single(form));
     
     // Add Form Listener for Login Event
     form.addListener('logged-in', function(e) {
     var user = e.getData();
     if (tc.util.Entity.IsEntityOfType(user, 'user')) {
     command.setLabel("Logout...");
     this.__currentUser = user;
     this.debug("Logged In...");
     }
     
     // Close the Dialog
     dialog.close();
     }, this);
     
     dialog.moveTo(50, 30);
     dialog.open();
     } else {
     var req = new tc.services.json.TCServiceRequest();
     
     req.addListener("service-ok", function(e) {
     command.setLabel("Login...");
     this.__currentUser = null;
     this.debug("Logged Out...");
     }, this);
     
     // Send request
     req.send('session', 'logout');
     }
     }
     }  // FUNCTION: __doLogInOut
     */
  } // MEMBERS
});
