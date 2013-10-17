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
 * Tab Manager
 */
qx.Class.define("tc.widgets.TabManager", {
  extend: qx.core.Object,
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Constructor
   * 
   * @param view {qx.ui.tabview.TabView} Tab View
   */
  construct: function(view) {
    this.base(arguments);

    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertInstance(view, qx.ui.tabview.TabView, "[view] Is not of the expected type!");
    }

    this.__tabView = view;
    this.__groups = [];
    this.__tabGroups = {};
  },
  /**
   * Destructor
   * 
   */
  destruct: function() {
    this.base(arguments);

    this.__tabView = null;
    this.__groups = null;
    this.__tabGroups = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __tabView: null,
    __groups: null,
    __tabGroups: null,
    /*
     ***************************************************************************
     PUBLIC METHODS
     ***************************************************************************
     */
    /**
     * Return the Tabe View being Managed
     *
     * @return {qx.ui.tabview.TabView} Tab View
     */
    getView: function() {
      return this.__tabView;
    },
    /*
     ***************************************************************************
     PUBLIC METHODS (GROUP POSITIONING)
     ***************************************************************************
     */
    /**
     * Return's the Last Position on the List
     *
     * @param after {Boolean ? false} TRUE After the Tail of the List, FALSE Position just before the Tail of the List
     * @return {Integer} Position
     */
    last: function(after) {
      return after ? this.__groups.length - 1 : this.__groups.length;
    },
    /**
     * Return's the 1st Position in the List
     *
     * @param before {Boolean ? false} TRUE Head of List, ALSE Next position afte the head
     * @return {Integer} Position
     */
    first: function(before) {
      return before ? -1 : 0;
    },
    /**
     * Return's the next position, BEFORE the given 'group', or the 1st
     * postion if the group doesn't exist
     *
     * @param group {String} Group ID 
     * @return {Integer} Position
     */
    before: function(group) {
      group = this.__validateGroup(group);
      return group !== null ? this.__groups.indexOf(group) : -1;
    },
    /**
     * Return's the next position, AFTER the given 'group', or the 1st
     * postion if the group doesn't exist
     *
     * @param group {String} Group ID 
     * @return {Integer} Position
     */
    after: function(group) {
      group = this.__validateGroup(group);
      return group !== null ? this.__groups.indexOf(group) : 0;
    },
    /*
     ***************************************************************************
     PUBLIC METHODS (GROUP MANAGEMENT)
     ***************************************************************************
     */
    /**
     * Return Group IDs for all the Groups being Managed
     *
     * @return {String[]} Group IDs, NULL if no groups exists
     */
    getGroups: function() {
      return this.__groups.length ? this.__groups : null;
    },
    /**
     * Adds a new Tab Group to the Manager
     *
     * @param id {String} Group ID (has to be unique)
     * @param position {Integer} New Position for the Group
     * @return {Integer} Position of Group Added
     * @throw If 'id' is invalid
     */
    addGroup: function(id, position) {
      // Validate Required Parameters
      id = tc.util.String.nullOnEmpty(id);
      if (id === null) {
        throw new "[id] contains an invalid value";
      }

      if (!this.__tabGroups.hasOwnProperty(id)) {
        // Create Group Object
        this.__tabGroups[id] = {
          // 'pages' =['page-id-1',...,'page-id-n']
          'pages': [],
          /* 'tabs' = {
           *   'page-id-1' : (object instance) qx.ui.tabview.Page,
           *   ...
           *   'page-id-n' : (object instance) qx.ui.tabview.Page,
           * }
           */
          'tabs': {}
        };

        if (position < 0) {
          this.__groups.unshift(id);
          return 0;
        } else if (position < (this.__groups.length - 1)) {
          this.__groups.splice(position + 1, 0, id);
          return this.__groups.indexOf(id);
        } else {
          this.__groups.push(id);
          return this.__groups.length - 1;
        }
      }

      return this.__groups.indexOf(id);
    },
    /**
     * Moves a Tab Group to a New Position (Including all the Associated Pages)
     *
     * @param group {String} Group ID 
     * @param position {Integer} New Position for the Group
     */
    moveGroup: function(group, position) {
      group = this.__validateGroup(group);
      if (group !== null) {
        var current = this.__groups.indexOf(group);

        // Make Sure the New Position Would not Be the Same After the Move
        if ((current !== position) || ((current + 1) !== position) || ((current - 1) !== position)) {
          // Correct Position (If Position is After Current)
          position = current < position ? position - 1 : position;

          // Re-position Pages
          var pageOffset = this.__pageOffset(current);

          // Remove the Group
          if (current === 0) {
            this.__groups.shift();
          } else {
            this.__groups.splice(current, 1);
          }

          var pageNewOffset = this.__pageOffset(position);

          // Re-add the Group
          if (position < 0) {
            this.__groups.unshift(group);
          } else {
            this.__groups.splice(position, 0, group);
          }

          // Remove the Pages From the Current Position
          var pages = this.__tabGroups[group]['pages'];
          for (var i = 0; i < pages.length; ++i) {
            this.__tabView.remove(pageOffset);
          }

          // Re-add the pages in Reverse Order so as not to modify the insert position
          for (i = pages.length; i >= 0; --i) {
            this.__tabView.add(pages[i], pageNewOffset);
          }
        }
      }
    },
    /**
     * Removes a Group (and all the associated pages) or All the Groups
     *
     * @param group {String ? null} Group ID or NULL if ALL Groups are to be removed
     */
    removeGroup: function(group) {
      group = tc.util.String.nullOnEmpty(group);
      if (group !== null) { // Remove Pages from Single Group
        group = this.__validateGroup(group);
        if (group !== null) {
          this.removePages(group);

          delete this.__tabGroups[group];
          this.__removeItemFromArray(this.__groups, group);
        }
      } else { // Remove All Pages
        var pages = this.__tabView.getChildren();
        while (pages.length > 0) {
          this.__tabView.remove(pages[0]);
        }

        // Reset Variables
        this.__groups = [];
        this.__tabGroups = {};
      }
    },
    /*
     ***************************************************************************
     PUBLIC METHODS (TAB MANAGEMENT)
     ***************************************************************************
     */
    /**
     * Return Page IDs for a Particular Group
     *
     * @param group {String} Group ID
     * @return {String[]} Page IDs, NULL if group doesn't exists
     */
    getPages: function(group) {
      group = this.__validateGroup(group);
      if (group !== null) {
        var pages = this.__tabGroups[group]['pages'];
        return pages.length > 0 ? pages : null;
      }

      return null;
    },
    /**
     * Verifies if a Page (ID) Exists in a Group (ID)
     *
     * @param group {String} Group ID
     * @param page {String} Page ID
     * @return {Boolean} TRUE Page ID exists in 'group', FALSE otherwise
     */
    hasPage: function(group, page) {
      page = tc.util.String.nullOnEmpty(page);
      if (page !== null) {
        group = this.__validateGroup(group);
        if (group !== null) {
          group = this.__tabGroups[group];
          return group['tabs'].hasOwnProperty(page);
        }
      }

      return false;
    },
    /**
     * Retrieves the Page Object for the give Page (ID) in the Group (ID)
     *
     * @param group {String} Group ID
     * @param page {String} Page ID
     * @return {qx.ui.tabview.Page} Page Object or NULL if [group,page] doesn't exist
     */
    getPage: function(group, page) {
      page = tc.util.String.nullOnEmpty(page);
      if (page !== null) {
        group = this.__validateGroup(group);
        if (group !== null) {
          group = this.__tabGroups[group];
          var tabs = group['tabs'];
          if (tabs.hasOwnProperty(page)) {
            return tabs[page];
          }
        }
      }

      return null;
    },
    /**
     * Add or Replaces a Page with in a Group
     *
     * @param group {String} Group ID
     * @param id {String} Page ID (has to be unique within the group)
     * @param page {qx.ui.tabview.Page} Page Object
     * @param start {Boolean ? false} TRUE adds as 1st page in the group, FALSE adds as last page
     */
    addPage: function(group, id, page, start) {
      id = tc.util.String.nullOnEmpty(id);
      if (id !== null) {
        if (qx.core.Environment.get("qx.debug")) {
          qx.core.Assert.assertInstance(page, qx.ui.tabview.Page, "[page] Is not of the expected type!");
        }

        group = this.__validateGroup(group);
        if (group !== null) {
          var pageOffset = this.__pageOffset(this.__groups.indexOf(group));

          // Get Group Object
          group = this.__tabGroups[group];
          if (group['tabs'].hasOwnProperty(id)) {
            this.removePage(group, id);
          }

          if (start) {
            group['pages'].unshift(id);
          } else {
            pageOffset += group['pages'].length;
            group['pages'].push(id);
          }

          group['tabs'][id] = page;
          this.__tabView.addAt(page, pageOffset);
        }
      }
    },
    /**
     * Removes all the Pages From a Group
     *
     * @param group {String} Group ID
     */
    removePages: function(group) {
      group = this.__validateGroup(group);
      if (group !== null) {
        // Remove All Pages
        var pages = this.__tabGroups[group]['pages'];
        if ((pages !== null) && (pages.length > 0)) {
          var tabs = this.__tabGroups[group]['tabs'];
          while (tabs.length > 0) {
            this.__tabView.remove(tabs[0]);
          }
        }

        this.__tabGroups[group]['pages'] = [];
        this.__tabGroups[group]['tabs'] = {};
      }
    },
    /**
     * Removes a Specific Page from the Group
     *
     * @param group {String} Group ID
     * @param page {String} Page ID
     * @return {qx.ui.tabview.Page} Page Object for the page removed, NULL if [group,page] doesn't exist
     */
    removePage: function(group, page) {
      page = tc.util.String.nullOnEmpty(page);
      if (page !== null) {
        group = this.__validateGroup(group);
        if (group !== null) {
          group = this.__tabGroups[group];
          if (group['tabs'].hasOwnProperty(page)) {
            // Get Page Index and Remove from TabView
            var idxPage = group['pages'].indexOf(page);
            var pageOffset = this.__pageOffset(this.__groups.indexOf(group)) + idxPage;
            this.__tabView.remove(pageOffset);
            // Get the Actual Page and then Delete it from the Group
            var page = group['tabs'][page];
            delete group['tabs'][page];

            this.__removeItemFromArray(group['pages'], idxPage);
            return page;
          }
        }
      }

      return null;
    },
    /*
     ***************************************************************************
     PRIVATE METHODS
     ***************************************************************************
     */
    __validateGroup: function(group) {
      group = tc.util.String.nullOnEmpty(group);
      return (group !== null) && this.__tabGroups.hasOwnProperty(group) ? group : null;
    },
    __pageOffset: function(idx) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue((idx >= 0) && (idx < this.__groups.length), "[idxGroup] is out of range!");
      }

      var offset = 0, group;
      for (var i = 0; i < idx; ++i) {
        group = this.__tabGroups[this.__groups[i]];
        offset += group['pages'].length;
      }

      return offset;
    },
    __removeItemFromArray: function(a, i) {
      if (!qx.lang.Type.isInteger(i)) {
        i = a.indexOf(i);
        if (i < 0) {
          return;
        }
      }

      if (i === 0) {
        a.shift();
      } else {
        a.splice(i, 1);
      }
    }
  } // SECTION: MEMBERS
});
