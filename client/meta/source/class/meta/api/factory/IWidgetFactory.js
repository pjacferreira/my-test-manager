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

/* ************************************************************************
 
 ************************************************************************ */

qx.Interface.define("meta.api.factory.IWidgetFactory", {
  extend: utility.api.di.IInjectable,
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Create a Widget from an Entity
     *
     * @abstract
     * @param entity {meta.api.itw.meta.api.IEntity|meta.api.itw.meta.api.IEntity[]} Entity(ies) to base Widget(s) On
     * @param parent {meta.api.ui.IGroup?null} Parent Widget
     * @return {meta.api.widgets.IWidget|Map} New unintialized Widget on success, 'null' otherwise
     */
    create: function(entity, parent) {
    }
  }
});
