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
qx.Class.define("tc.event.type.ColumnFilter",
  {
    extend: qx.event.type.Event,


    /*
     *****************************************************************************
     MEMBERS
     *****************************************************************************
     */

    members: {
      __column: null,
      __filter: null,
      __old: null,


      /**
       * Initializes an event object.
       *
       * @param column {var} The Column ID of the Modified Filter
       * @param filter {var} The new Filter Value
       * @param old {var?null} The old Filter Value (optional)
       * @param cancelable {Boolean?false} Whether or not an event can have its default
       *     action prevented. The default action can either be the browser's
       *     default action of a native event (e.g. open the context menu on a
       *     right click) or the default action of a qooxdoo class (e.g. close
       *     the window widget). The default action can be prevented by calling
       *     {@link qx.event.type.Event#preventDefault}
       * @return {qx.event.type.ColumnFilter} the initialized instance.
       */
      init: function (column, filter, old, cancelable) {
        this.base(arguments, false, cancelable);

        this.__column = column;
        this.__filter = filter;
        this.__old = old;

        return this;
      },


      /**
       * Get a copy of this object
       *
       * @param embryo {qx.event.type.ColumnFilter?null} Optional event class, which will
       *     be configured using the data of this event instance. The event must be
       *     an instance of this event class. If the data is <code>null</code>,
       *     a new pooled instance is created.
       * @return {qx.event.type.ColumnFilter} a copy of this object
       */
      clone: function (embryo) {
        var clone = this.base(arguments, embryo);

        clone.__column = this.__column;
        clone.__filter = this.__filter;
        clone.__old = this.__old;

        return clone;
      },


      /**
       * The Column Id of the Modified Filter.
       *
       * @return {var} The Column ID
       */
      getColumn: function () {
        return this.__column;
      },

      /**
       * The new Filter Value.
       *
       * @return {var} The new Filter Value
       */
      getFilter: function () {
        return this.__filter;
      },


      /**
       * The old Filter Value.
       *
       * @return {var} The old Filter Value
       */
      getOldFilter: function () {
        return this.__old;
      }
    },


    /*
     *****************************************************************************
     DESTRUCTOR
     *****************************************************************************
     */

    destruct: function () {
      this.__filter = this.__data = this.__old = null;
    }
  });
