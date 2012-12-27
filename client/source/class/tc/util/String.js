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
 #require(qx.lang.Type)
 #require(qx.lang.String)
 ************************************************************************ */

/**
 * String helper functions
 *
 */
qx.Bootstrap.define("tc.util.String", {
  statics: {
    /**
     * Returns null if the value is not a string or is an empty string, otherwise it optionally returns the
     * trimmed string
     *
     * @param str {var} a value to test.
     * @param trim {Boolean?false} If str is a string, should it return the trimmed value?
     * @return {String} Null or Non Empty String.
     */
    nullOnEmpty: function (str, trim) {

      if (qx.lang.Type.isString(str)) {
        var value = str.trim();
        if (value.length > 0) {
          return trim ? value : str;
        }
      }

      return null;
    }
  }
});