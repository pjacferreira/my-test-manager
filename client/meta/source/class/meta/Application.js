/* ************************************************************************
 
 Copyright:
 
 License:
 
 Authors:
 
 ************************************************************************ */

/**
 * This is the main application class of your custom application "meta"
 *
 * @asset(meta/*)
 */
qx.Class.define("meta.Application", {
  extend: qx.application.Standalone,
  include: [
    meta.events.mixins.MMetaEventHandler
  ],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */

  members: {
    /**
     * This method contains the initial application code and gets called 
     * during startup of the application
     * 
     * @lint ignoreDeprecated(alert)
     */
    main: function()
    {
      // Call super class
      this.base(arguments);

      // Enable logging in debug variant
      if (qx.core.Environment.get("qx.debug"))
      {
        // support native logging capabilities, e.g. Firebug for Firefox
        qx.log.appender.Native;
        // support additional cross-browser console. Press F7 to toggle visibility
        qx.log.appender.Console;
      }

      // Create the Global Application DI manager
      window.$$__di = new utility.di.DependencyManager();

      // Save the Application to the Dependency Manager
      $$__di.set('application', {'fixed': this}, true);
      // Create Meta Services Dispatcher
      $$__di.set('metaservices', function() {
        return new utility.service.json.ServiceRequest($$tc.url_meta_services())
      }, true);
      // Create Web Services Dispatcher
      $$__di.set('webservices', function() {
        return new utility.service.json.ServiceRequest($$tc.url_web_services())
      }, true);
      // Create Widget Factory Service
      $$__di.set('widgetfactory', 'meta.factories.WidgetFactory', true);
      // USE A Service Repository
      $$__di.set('metarepo', 'meta.repositories.ServiceRepository', true);

      /* TEST ONLY
       // USE a Static Repository
       var save_this = this;
       $$__di.set('metarepo', function() {
       return new meta.repositories.StaticRepository(save_this.getStaticMetadata());
       }, true);
       */

      // Create Meta Console
      var console = new meta.ui.console.Console(
        new meta.entities.Widget('console', {
          type: 'console',
          label: 'Meta Console'
        }));

      // Attach Dependency Injector
      console.setDI($$__di);

      // Attach Meta Event Handlers to Widget
      this._mx_meh_attach("widget-ready", console);

      // Initialize the Console
      console.initialize();
    },
    _processMetaWidgetReady: function(success, code, message, console) {
      if (success) {
        // Create Lexor, Parser and Executor
        var lexer = new meta.parser.CommandLexer(new meta.parser.BasicLexer());
        lexer.setSkipEOL(true);
        var parser = new meta.parser.Parser(lexer);
//      var executor = new meta.parser.ASTDumper();
        var executor = new meta.parser.ASTInterpreter();
        // Set Dependency Injector
        executor.setDI($$__di);

        // Attach Command Processor to the Console
        var processor = new meta.ui.console.ConsoleProcessor(console, parser, executor);
        console.setProcessor(processor);

        var win = new qx.ui.window.Window("Console");
        win.setWidth(600);
        win.setHeight(600);

        win.setLayout(new qx.ui.layout.Grow());
        win.add(console.getWidget());

        this.getRoot().add(win, {
          left: 5,
          right: 5
        });

        win.open();
      } else {
        this.error(message);
      }
    },
    /**
     * Create a Connection Object to be used with an Input Form
     *
     * @param form {meta.api.entity.IForm} Entity to base Form Widget On
     * @return {meta.api.ui.IWidget} New unintialized Widget on success, 'null' otherwise
     */
    __createConnectionForInputForm: function(form) {
      // Does the Input Form have Services Defined?
      var services = form.getServices();
      if (services.length > 0) { // YES
        var connection = new meta.ui.datasource.Connection();
        var count = 0, alias;
        for (var i = 0; i < services.length; ++i) {
          alias = services[i];
          count = connection.registerService(alias, form.getServiceID(alias)) ? count + 1 : count;
        }

        return count > 0 ? connection : null;
      }
      // ELSE: No
      return null;
    },
    getStaticMetadata: function() {
      // Create a Static Repository
      return {
        'fields': {
          "user:id": {
            "value": {
              "type": "integer",
              "length": 0,
              "nullable": false,
              "auto": true,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "in",
            "key": true,
            "label": "User ID",
            "description": "Unique User Identifier"
          },
          "user:name": {
            "value": {
              "type": "text",
              "length": 40,
              "nullable": false,
              "auto": false,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": true,
            "label": "User Name",
            "description": "Unique User Identifier"
          },
          "user:first_name": {
            "value": {
              "type": "text",
              "length": 40,
              "nullable": true,
              "auto": false,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "First Name",
            "description": "User\\'s First Name"
          },
          "user:last_name": {
            "value": {
              "type": "text",
              "length": 80,
              "nullable": true,
              "auto": false,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Last Name",
            "description": "User\\'s Last Name"
          },
          "user:password": {
            "value": {
              "type": "password",
              "length": 64,
              "nullable": true,
              "auto": false,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Password",
            "description": "User Password"
          },
          "user:s_description": {
            "value": {
              "type": "text",
              "length": 80,
              "nullable": true,
              "auto": false,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Short Description",
            "description": "Short User Description"
          },
          "user:l_description": {
            "value": {
              "type": "html",
              "length": 0,
              "nullable": true,
              "auto": false,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Long Description",
            "description": "Long User Description"
          },
          "user:date_created": {
            "value": {
              "type": "datetime",
              "length": 0,
              "nullable": false,
              "auto": true,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Created",
            "description": "User Password"
          },
          "user:date_modified": {
            "value": {
              "type": "datetime",
              "length": 0,
              "nullable": true,
              "auto": true,
              "precision": 0,
              "trim": true,
              "empty": "as-null"
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Last Modified",
            "description": "Last Modification Date"
          },
          "user:creator": {
            "value": {
              "type": "reference",
              "link": "user:id",
              "auto": true,
              "nullable": false
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Creator",
            "description": "User that created this user",
            "reference": {
              "object": "user",
              "key": "user:id"
            },
            "display": {
              "field": "user:name"
            }
          },
          "user:last_modifier": {
            "value": {
              "type": "reference",
              "link": "user:id",
              "auto": true,
              "nullable": true
            },
            "virtual": false,
            "data-direction": "none",
            "key": false,
            "label": "Last Modifier",
            "description": "User that last modified this user",
            "reference": {
              "object": "user",
              "key": "user:id"
            },
            "display": {
              "field": "user:name"
            }
          },
          "virtual:password-confirmation": {
            "virtual": true,
            "data-direction": "none",
            "key": false,
            "label": "Confirmation",
            "description": "User Password Confirmation",
            "value": {
              "type": "password",
              "auto": false,
              "precision": 0,
              "default": null,
              "nullable": true,
              "trim": true,
              "empty": "as-null",
              "validation": null
            }
          }
        },
        'services': {
          "user:update": {
            "parameters": {
              "require": true,
              "exclude": [
                "user:id",
                "user:password"
              ]
            },
            "service": [
              "manage",
              "user"
            ],
            "action": "update",
            "keys": [
              "user:id",
              "user:name"
            ]
          },
          "user:create": {
            "parameters": {
              "require": false,
              "allow": "user:*",
              "exclude": [
                "user:id",
                "user:name"
              ]
            },
            "service": [
              "manage",
              "user"
            ],
            "action": "create",
            "key": "user:name"
          },
          "user:read": {
            "parameters": {
              "require": false,
              "exclude": null
            },
            "service": [
              "manage",
              "user"
            ],
            "action": "read",
            "keys": [
              "user:id",
              "user:name"
            ]
          }
        },
        'forms': {
          'home:tabs': {
            'type': 'tabs',
            'title': 'Tabs Form',
            'layout': ['tab1', 'tab2', 'tab3'],
            'widgets': {
              'tab1': {
                'type': 'form',
                'form': 'home:container',
                'overlay': {
                  'title': 'Tab 1'
                }
              },
              'tab2': {
                'type': 'form',
                'form': 'user:create',
                'overlay': {
                  'title': 'Tab 2'
                }
              },
              'tab3': {
                'type': 'form',
                'form': 'user:read',
                'overlay': {
                  'title': 'Tab 3'
                }
              }
            }
          },
          'home:container': {
            'type': 'container',
            'title': 'Container Form',
            'layout': ['toolbar1', 'button3'],
            'widgets': {
              'toolbar1': {
                'type': 'toolbar',
                'label': 'Toolbar 1',
                'layout': ['button1', 'button2']
              },
              'button1': {
                'type': 'button',
                'label': 'Button 1',
                'actions': {
                  'click': {
                    'actions': [
                      'event\\log'
                    ]
                  }
                }
              },
              'button2': {
                'type': 'button',
                'label': 'Button 2',
                'actions': {
                  'click': {
                    'actions': [
                      'event\\log'
                    ]
                  }
                }
              },
              'button3': {
                'type': 'button',
                'label': 'Button 3',
                'actions': {
                  'click': {
                    'actions': [
                      'event\\log'
                    ]
                  }
                }
              }
            }
          },
          'user:read': {
            "type": "input",
            "title": "User Details",
            "fields": null,
            "services": {
              "read": "user:read"
            },
            "validations": null,
            "transformations": null,
            "layout": ["user", "name", "descriptions", 'button1'],
            "in": "user:id",
            'widgets': {
              'button1': {
                'type': 'button',
                'label': 'Submit',
                'actions': {
                  'click': {
                    'actions': [
                      'event\\log'
                    ]
                  }
                }
              },
              'user': {
                'type': 'group',
                'label': 'User Information',
                'layout': ["user:id", "user:name"]
              },
              'name': {
                'type': 'group',
                'label': 'Name',
                'layout': ["user:first_name", "user:last_name"]
              },
              'descriptions': {
                'type': 'group',
                'label': 'Descriptions',
                'layout': ["user:s_description", "user:l_description"]
              }
            }
          },
          'user:create': {
            "type": "input",
            "title": "Create User",
            "fields": null,
            "services": {
              "read": "user:read",
              "create": "user:create",
              "update": "user:update"
            },
            "validations": {
              "fields": {
                "virtual:password-confirmation": "= {user:password}"
              }
            },
            "transformations": null,
            "layout": ["user", "name", "descriptions", "password"],
            'widgets': {
              'user': {
                'type': 'group',
                'label': 'User Information',
                'layout': ["user:id", "user:name"]
              },
              'name': {
                'type': 'group',
                'label': 'Name',
                'layout': ["user:first_name", "user:last_name"]
              },
              'descriptions': {
                'type': 'group',
                'label': 'Descriptions',
                'layout': ["user:s_description", "user:l_description"]
              },
              'password': {
                'type': 'group',
                'label': 'Password',
                'layout': ["user:password", "virtual:password-confirmation"]
              }
            }
          }
        }
      };
    }
  } // SECTION: MEMBERS
});
