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

/**
 * String helper functions
 *
 */
qx.Bootstrap.define("utility.String", {
  type : "static",

  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics : {
    /**
     * (VALIDATING) Returns null if the value is not a string or is an empty string, otherwise it optionally returns the
     * trimmed string
     *
     * @param string {String} a value to test.
     * @param trim {Boolean?false} If str is a string, should it return the trimmed value?
     * @return {String} NULL or Non Empty String.
     */
    v_nullOnEmpty : function(string, trim) {
      return qx.lang.Type.isString(string) ? this.nullOnEmpty(string, trim) : null;
    },

    /**
     * Returns null if the value is not a string or is an empty string, otherwise it optionally returns the
     * trimmed string
     *
     * @param string {String} a value to test.
     * @param trim {Boolean?false} If string is a string, should it return the trimmed value?
     * @return {String} NULL or Non Empty String.
     */
    nullOnEmpty : function(string, trim) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertString(string, "[string] Is not a String!");
      }
      var value = string.trim();
      if (value.length > 0) {
        return !!trim ? value : string;
      }
      return null;
    },

    /**
     * (VALIDATING) Capitalize the 1st letter of a string.
     *
     * @param string {String} string to capitalize.
     * @return {String} Capitalized string or NULL if not string
     */
    v_capitalize : function(string) {
      return qx.lang.Type.isString(string) ? this.v_capitalize(string) : null;
    },

    /**
     * Capitalized the 1st letter of a string.
     *
     * @param string {String} string to capitalize.
     * @return {String} Capitalized string.
     */
    capitalize : function(string) {
      if (qx.core.Environment.get("qx.debug")) {
        qx.core.Assert.assertString(string, "[string] Is not a String!");
      }
      return string.charAt(0).toUpperCase() + string.slice(1);
    }
  }                                              // SECTION: STATICS
});
