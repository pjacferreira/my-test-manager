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

 ************************************************************************ */

qx.Class.define("tc.session.Manager", {
  extend: qx.core.Object,

  events: {
    "session-login": "qx.event.type.Event",
    "session-logout": "qx.event.type.Event",
    "session-organization": "qx.event.type.Event",
    "session-project": "qx.event.type.Event"
  },

  properties: {
    user: {
      check: 'Object',
      init: true,
      apply: "_applyUser",
      nullable: true
    },
    organization: {
      check: 'Integer',
      init: true,
      apply: "_applyOrganization",
      nullable: true
    },
    project: {
      check: 'Integer',
      init: true,
      apply: "_applyProject",
      nullable: true
    }
  },

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   *
   */
  construct: function () {
    this.base(arguments);
  },

  destruct: function () {
  },

  members: {
    _applyUser: function (value, old) {
      if (value) { // New User
        if(old) { // 1st do a Logout so that we can reset the application state
          // TODO: Allow for Quick User Change, by not doing 2 application interface updates
          this.fireEvent("session-logout");
        }
        this.fireEvent("session-login");
      } else {  // No User
        this.fireEvent("session-logout");
      }
    },
    _applyOrganization: function (value, old) {
      if (value) { // New User
        if(old) { // 1st do a Logout so that we can reset the application state
          // TODO: Allow for Quick User Change, by not doing 2 application interface updates
          this.fireEvent("session-organization");
        }
        this.fireEvent("session-organization");
      } else {  // No User
        this.fireEvent("session-organization");
      }
    },
    _applyProject: function (value, old) {
      if (value) { // New User
        if(old) { // 1st do a Logout so that we can reset the application state
          // TODO: Allow for Quick User Change, by not doing 2 application interface updates
          this.fireEvent("session-project");
        }
        this.fireEvent("session-project");
      } else {  // No User
        this.fireEvent("session-project");
      }
    }
  }
});