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
 * Convert Meta Entities into Meta Widgets
 */
qx.Class.define("meta.factories.WidgetFactory", {
  extend: qx.core.Object,
  implement: meta.api.factory.IWidgetFactory,
  include: [utility.mixins.di.MInjectable],
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     INTERFACE METHODS (meta.api.IWidgetFactory) 
     ***************************************************************************
     */
    /**
     * Create a Widget from an Entity
     *
     * @param entity {meta.api.entity.IEntity|meta.api.entity.IEntity[]} Entity(ies) to base Widget(s) On
     * @param parent {meta.api.ui.IGroup?null} Parent Widget
     * @return {meta.api.ui.IWidget} New unintialized Widget on success, 'null' otherwise
     */
    create: function(entity, parent) {
      // Do we have an array of entities to build widgets for?
      if (qx.lang.Type.isArray(entity)) { // YES

        // Cycle through the list of entities trying to build the widgets
        var e, count = 0, widget, widgets = {};
        for (var i = 0; i < entity.length; ++i) {
          e = entity[i];
          widget = this.create(e, parent);
          // Did we create a Widget?
          if (widget) { // YES
            widgets[e.getEntityId()] = this.create(e, parent);
            ++count;
          }
        }

        return count ? widgets : null;
      }

      // ELSE: NO: Single Entity
      return this.__create(entity, parent);
    },
    /*
     ***************************************************************************
     PRIVATE METHODS
     ***************************************************************************
     */
    /**
     * Create a Widget from an Entity
     *
     * @param entity {meta.api.entity.IEntity} Entity to base Widget On
     * @param parent {meta.api.ui.IGroup?null} Parent Widget
     * @return {meta.api.ui.IWidget} 'null' on failurem or New unintialized Widget or Widget Map on success
     */
    __create: function(entity, parent) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(entity, meta.api.entity.IEntity, "[entity] Is invalid!");
      }

      var widget = null;
      var type = entity.getType();

      switch (type) {
        case 'field':
          widget = this.__createField(entity, parent);
          break;
        case 'form':
          widget = this.__createForm(entity);
          break;
        case 'widget':
          widget = this.__createWidget(entity, parent);
          break;
        case 'group':
          widget = this.__createGroupWidget(entity, parent);
          break;
      }

      // Does the Entity have DI Set?
      if (entity.hasDI()) { // YES: Then Maintain the Entity's DI
        widget.setDI(entity.getDI());
      } else { // NO: Use the Factory's DI
        widget.setDI(this.getDI());
      }

      return widget;
    },
    /**
     * Create a Field Widget
     *
     * @param field {meta.api.entity.IField} Field Entity to base Widget On
     * @param parent {meta.api.ui.IGroup?null} Parent Widget
     * @return {meta.api.ui.IWidget} New unintialized Widget on success, 'null' otherwise
     */
    __createField: function(field, parent) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(field, meta.api.entity.IField, "[field] Is not of the expected type!");
        qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is invalid!");
      }

      /* Creates A Widget for a Single Field Definition
       * NOTE: Widget Creation Might be a 2 Step Process
       * 1. Create the Widget,
       * 2. Load the Widget
       *
       * Even though Widgets are Created in-line, loading might be an asynchronous 
       * process. So the form is only completely ready, when the 'complete' event
       * is fired. Also note that, where as the 'complete' event is only sent once
       * per call to createWidget(s), the 'nok' can be sent more than once, depending
       * on the problems detected with the load process. 
       * 
       * NOTE: Invalid Field Definitions are Highlighted by the function returning
       * a String (Error Message) rather than an actual widget, so care has to be taken
       * in processing the results of the function.
       */
      return new meta.ui.Field(field, parent);
    },
    /**
     * Create a Basic Widget
     *
     * @param entity {meta.api.entity.IWidget} Field Entity to base Widget On
     * @param parent {meta.api.ui.IGroup?null} Parent Widget
     * @return {meta.api.ui.IWidget} New unintialized Widget on success, 'null' otherwise
     */
    __createWidget: function(entity, parent) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(entity, meta.api.entity.IWidget, "[entity] Is invalid!");
        qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is invalid!");
      }

      // CREATE: Widget
      var widget = null;

      switch (entity.getWidgetType()) {
        case 'label' :
          // TODO : implement
          break;
        case 'button' :
          widget = new meta.ui.Button(entity, parent);
          break;
        case 'list' :
          // TODO : implement
          break;
        case 'table' :
          // TODO : Implement
          break;
      }

      return widget;
    },
    /**
     * Create a Basic Widget
     *
     * @param entity {meta.api.entity.IWidget} Field Entity to base Widget On
     * @param parent {meta.api.ui.IGroup} Parent Widget
     * @return {meta.api.ui.IWidget} New unintialized Widget on success, 'null' otherwise
     */
    __createGroupWidget: function(entity, parent) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(entity, meta.api.entity.IContainer, "[entity] Is invalid!");
        qx.core.Assert.assertInterface(parent, meta.api.ui.IGroup, "[parent] Is invalid!");
      }

      // CREATE: Widget
      var widget = null;

      switch (entity.getWidgetType()) {
        case 'group' :
        case 'toolbar' :
          /* Problem:
           * Widget can build themselves, therefore, the widget definition has to 
           * contain all the information required to build itslef, or has to be
           * able to use the meta-data repository to retrieve anything that is 
           * missing.
           */
          widget = new meta.ui.ContainedGroup(entity, parent);
          break;
      }

      return widget;
    },
    /**
     * Create a Form Widget
     *
     * @param form {meta.api.entity.IForm} Entity to base Form Widget On
     * @return {meta.api.ui.IWidget} New unintialized Widget on success, 'null' otherwise
     */
    __createForm: function(form) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(form, meta.api.entity.IForm, "[form] Is invalid!");
      }

      // Create a Widget Based on it's type
      var widget = null;
      switch (form.getFormType()) {
        case 'tabs':
          widget = new meta.ui.FormTabs(form);
          break;
        case 'container':
          widget = new meta.ui.FormBasic(form);
          break;
        case 'input':
          widget = new meta.ui.FormInput(form);
          break;
      }

      return widget;
    }
  } // SECTION: MEMBERS
});
