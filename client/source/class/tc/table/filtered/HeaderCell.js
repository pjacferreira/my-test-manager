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
qx.Class.define("tc.table.filtered.HeaderCell", {
  extend: qx.ui.table.headerrenderer.HeaderCell,

/*
  construct: function () {
    this.base(arguments);

    // Create Child Widgets
    this._createChildControl('filter');
  },
*/

  properties: {
    appearance: {
      refine: true,
      init: "table-header-cell-filter"
    },
    /** Enable or Disable Header Filter */
    enableFilter: {
      check: "Boolean",
      init: false,
      nullable: false,
      apply: "_applyEnableFilter",
      themeable : true
    },
    /** Filter */
    filter: {
      check: "String",
      init: null,
      event: "changeFilter",
      nullable: true
    }
  },

  events: {
    "changeFilter": "qx.event.type.Data"
  },

  members: {

    // property apply
    _applyEnableFilter: function (value, old) {

      var control = this.getChildControl("filter", /* notcreate */ false);
      if (value) {
        // TODO Reset the Value to it's previous (is it required to do .setValue(this.getFilter())?)
        control.show();
      } else {
        control.hide();
      }
    },

    // overridden
    _createChildControlImpl: function (id, hash) {
      var control;

      if (id == 'filter') {
        control = new qx.ui.form.TextField();
        control.addListener('changeValue', function (e) {
          this.fireDataEvent('changeFilter', e.getData(), e.getOldData());
        }, this);

        // control.setAnonymous(true);
        this._add(control, {row: 1, column: 0, colSpan: 3});
      }

      return control || this.base(arguments, id, hash);
    }
  }
});