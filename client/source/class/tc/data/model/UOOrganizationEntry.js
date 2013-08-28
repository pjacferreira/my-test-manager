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
qx.Class.define("tc.data.model.UOOrganizationEntry", {
  extend: qx.core.Object,

  properties: {
    id: {
      check: 'Integer',
      event: 'changeID',
      nullable: false
    },
    organization: {
      check: 'String',
      event: 'changeOrganization',
      nullable: false
    },
    permissions: {
      check: 'String',
      event: 'changePermissions',
      nullable: true
    }
  },

  members: {
    toString: function () {
      return this.getId() + '-' + this.getOrganization() + '[' + this.getPermissions() + ']';
    }
  }
});
