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

/**
 * String helper functions
 *
 */
qx.Bootstrap.define("meta.Meta", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    __repository: null,
    __factory: null,
    /**
     * Get Current Metadata Repository in use.
     *
     * @return {meta.api.repository.IMetaRepository} Current Repository or NULL if none set.
     */
    getRepository: function() {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue(this.__repository !== null, "Metadata Repository has not been set.");
      }

      return this.__repository;
    },
    /**
     * Set Repository for Use by Meta
     *
     * @param repository {meta.api.repository.IMetaRepository} New Repository
     * @return {meta.api.repository.IMetaRepository} If set Previous Repository, null otherwise
     */
    setRepository: function(repository) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertInterface(repository, meta.api.repository.IMetaRepository, "[repository] Is not of the expected type!");
      }

      var old = this.__repository;
      this.__repository = repository;
      return old;
    },
    /**
     * Get Current Widget Factory in use.
     *
     * @return {meta.api.factory.IWidgetFactory} Current Widget Factory or NULL if none set.
     */
    getWidgetFactory: function() {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertTrue(this.__factory !== null, "Widget Factory has not been set.");
      }

      return this.__factory;
    },
    /**
     * Set Wdiget Factory to be used
     *
     * @param factory {meta.api.factory.IWidgetFactory} New Widget Factory
     * @return {meta.api.factory.IWidgetFactory} If set Previous Widget Factory, null otherwise
     */
    setWidgetFactory: function(factory) {
      if (qx.core.Environment.get("qx.debug")) {
//        qx.core.Assert.assertInterface(factory, meta.api.factory.IWidgetFactory, "[factory] Is not of the expected type!");
      }

      var old = this.__factory;
      this.__factory = factory;
      return old;
    }
  } // SECTION: STATICS
});