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

qx.Class.define("tc.data.model.ProjectEntry", {
  extend: qx.core.Object,
  properties: {
    id: {
      check: 'Integer',
      event: 'changeID',
      nullable: false
    },
    project: {
      check: 'String',
      event: 'changeProject',
      nullable: false
    },
    description: {
      check: 'String',
      nullable: true
    },
    organization: {
      check: 'Integer',
      nullable: false
    }
  },
  members: {
    toString: function() {
      return this.getId() + '-' + this.getProject();
    }
  }
});
