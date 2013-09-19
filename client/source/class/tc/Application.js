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
 #asset(tc/user_green.png)
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
    __paneHeader: null,
    __paneContent: null,
    __sessionManager: null,
    __toaster: null,
    __counter: 1,
    __menubar: null,
    __currentUser: null,
    __actionRegistry: null,
    __widgetOrganizations: null,
    __widgetProjects: null,
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

      // Enable logging in debug variant
      if (qx.core.Environment.get("qx.debug")) {
        // support native logging capabilities, e.g. Firebug for Firefox
        qx.log.appender.Native;
        // support additional cross-browser console. Press F7 to toggle visibility
        qx.log.appender.Console;
      }

      // Create the Seperate Panes
      this.__createHeader(60);
      this.__createContent();

      // Create Toaster
      this.__toaster = new tc.toaster.Manager();

      // Create the Session Manager
      this.__sessionManager = new tc.session.Manager();

      // Add the Panes to the Window
      this.getRoot().add(this.__paneHeader, {
        top: 5,
        left: 5,
        right: 5
      });
      this.getRoot().add(this.__paneContent, {
        top: 70,
        left: 5,
        right: 5,
        bottom: 5
      });

      // Initialize the Header Pane
      this.__initializeHeader();
    },
    __createHeader: function(height) {
      // Create the layout for the Header Pane
      var layout = new qx.ui.layout.HBox(5, 'right');
      layout.setReversed(true);

      // Create Page Header
      this.__paneHeader = new qx.ui.container.Composite();
      this.__paneHeader.setLayout(layout);
      this.__paneHeader.setHeight(height);
    }, // FUNCTION: __createHeader
    __createContent: function() {
      // Create Content Pane
      this.__paneContent = new qx.ui.container.Composite();
      this.__paneContent.setLayout(new qx.ui.layout.Canvas());
    }, // FUNCTION: __createContent
    __initializeHeader: function() {
      // Create the Login Widget
      var login = new tc.widgets.LoginWidget();
      login.addListener("user-change", function(e) {
        this.__doLogin(e.getData());
      }, this);
      login.addListener("no-user", function(e) {
        this.__doLogout();
      }, this);

      // Add Elements to Pane Header
      this.__paneHeader.add(login);
    }, // FUNCTION: __initializeHeader
    __doLogin: function(user) {
      if (this.__widgetOrganizations == null) {
        // Create the Organization Widget (Only After Session User Set)
        this.__widgetOrganizations = new tc.widgets.OrganizationWidget();
        this.__widgetOrganizations.addListener("org-change", function(e) {
          this.__setOrganization(e.getData());
        }, this);
        this.__widgetOrganizations.addListener("org-none", function(e) {
          this.__noOrganization();
        }, this);

        this.__paneHeader.add(this.__widgetOrganizations);
      }
      this.__addTabUser();
      this.__addTabUserManage();
      this.__toaster.add('Logged In [' + user['user:name'] + "]");
    },
    __doLogout: function() {
      // Projects
      if (this.__widgetProjects != null) {
        this.__paneHeader.remove(this.__widgetProjects);
        this.__widgetProjects = null;
      }

      // Organizations
      if (this.__widgetOrganizations != null) {
        this.__paneHeader.remove(this.__widgetOrganizations);
        this.__widgetOrganizations = null;
      }
      this.__removeAllTabs();
      this.__toaster.add('User Logged Out');
    },
    __setOrganization: function(orgId) {
      tc.services.Session.setOrganization(orgId,
              function(organization) {
                // Add Project Selection Box
                if (this.__widgetProjects == null) {
                  // Create and Add New Projects Widget
                  this.__widgetProjects = new tc.widgets.ProjectWidget();
                  this.__widgetProjects.addListener("project-change", function(e) {
                    this.__setProject(e.getData());
                  }, this);
                  this.__widgetProjects.addListener("project-none", function(e) {
                    this.__noProject();
                  }, this);

                  this.__paneHeader.add(this.__widgetProjects);
                } else {
                  // Try to Repaint the Widget
                  this.__widgetProjects.refresh();
                }
              },
              null, this);

      // Set the Session Organization
      this.__toaster.add('New Organization [' + orgId + "]");

      // Add Organizations Tab
      this.__addTabOrganizations();
    },
    __noOrganization: function() {
      // Projects
      if (this.__widgetProjects != null) {
        this.__paneHeader.remove(this.__widgetProjects);
        this.__widgetProjects = null;
      }

      this.__toaster.add('Organization Cleared');
      this.__sessionManager.setOrganization(null);
    },
    __setProject: function(projectId) {
      tc.services.Session.setProject(projectId,
              function(project) {
              },
              null, this);

      // Set the Session Project
      this.__toaster.add('Project Project [' + projectId + "]");

      // Add Project and Tests Tab
      this.__addTabProjects();
      this.__addTabTests();
      this.__addTabTesting();
    },
    __noProject: function() {
      this.__toaster.add('Project Cleared');
      this.__sessionManager.setOrganization(null);
    },
    __removeAllTabs: function() {
      if (this.__tabHolder !== null) {
        this.__tabHolder.removePages();
      }
    },
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
    __addTabUserManage: function() {
      // Create Tab Holder if it Doesn't Exist
      if (this.__tabHolder == null) {
        this.__createTabHolder();
      }

      if (!this.__tabHolder.hasPage('management', 'user')) {
        // Create Tab Page
        var tab = new qx.ui.tabview.Page('User Manager');
        tab.setLayout(new qx.ui.layout.VBox());

        // Set Load Hint
        tab.add(new qx.ui.basic.Label("Loading..."));

        // Create the Table and add it to the Tab
        this.__addTableToTab('user:manage', tab);

        // Add TAB to Tab Manager
        this.__tabHolder.addPage('management', 'user', tab, true);
      }
    }, // FUNCTION: __addTabUserManage
    __addTabOrganizations: function() {
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
      }
    }, // FUNCTION: __addTabOrganizations
    __addTabProjects: function() {
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
    __addTabTesting: function() {
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
    }, // FUNCTION: __addTabTesting
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
