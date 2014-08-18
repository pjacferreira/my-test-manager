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

qx.Class.define("meta.repositories.AbstractRepository", {
  extend: qx.core.Object,
  type: "abstract",
  implement: meta.api.repository.IMetaRepository,
  include: [utility.mixins.di.MInjectable],
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Abstract Group Class
   * 
   * @param group {meta.api.entity.IContainer} Container Definition
   * @param parent {meta.api.ui.IGroup} Parent Widget
   */
  construct: function(group, parent) {
  },
  /**
   *
   */
  destruct: function() {
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     METHODS (meta.api.repository.IMetaRepository)
     ***************************************************************************
     */
    /**
     * Create a Meta Entity, from the metadata given, or empty if no metadata
     * set
     *
     * @param id {String} Entity ID 
     * @param type {String} Entity Type 
     * @param metadata {Map?null} Entity Metadata (or null if we are creating an Entity Shell)
     * @param parent {meta.entities.api.IContainer?null} Parent entity or null if not required
     * @return {meta.api.entity.IEntity} Entity or 'null' if not able to create
     */
    createEntity: function(id, type, metadata, parent) {
      switch (type) {
        case 'field':
          return this._createField(id, metadata);
        case 'service':
          return this._createService(id, metadata);
        case 'widget':
          return this._createWidget(id, parent, metadata);
        case 'form':
          return this._createForm(id, metadata);
      }

      // Invalid Type!?
      return null;
    },
    /*
     ***************************************************************************
     PROTECTED METHODS (Create Entities)
     ***************************************************************************
     */
    _createField: function(id, metadata) {
      var entity = new meta.entities.Field(id, metadata);

      // Set Dependency Injector for Entity
      entity.setDI(this.getDI());
      return entity;
    },
    _createService: function(id, metadata) {
      var entity = new meta.entities.Service(id, metadata);

      // Set Dependency Injector for Entity
      entity.setDI(this.getDI());
      return entity;
    },
    _createWidget: function(id, parent, metadata) {
      var widget;

      switch (metadata.type) {
        case 'group':
        case 'toolbar':
          widget = new meta.entities.GroupWidget(parent, id, metadata);
          break;
        default:
          widget = new meta.entities.Widget(id, metadata);
      }

      // Set Dependency Injector for Entity
      widget.setDI(this.getDI());
      return widget;
    },
    _createForm: function(id, metadata) {
      var form;

      switch (metadata.type) {
        case 'input':
          form = new meta.entities.FormInput(id, metadata);
          break;
        default:
          form = new meta.entities.Form(id, metadata);
      }

      // Set Dependency Injector for Entity
      form.setDI(this.getDI());
      return form;
    },
    /*
     *****************************************************************************
     PROTECTED METHODS (Callback Handlers)
     *****************************************************************************
     */
    _callOK: function(ok, results, context) {
      ok.call(context == null ? this : context, results);
    },
    _callNOK: function(nok, results, context) {
      if (nok != null) {
        nok.call(context == null ? this : context, results);
      }
    }
  } // SECTION: MEMBERS
});
