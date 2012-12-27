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

qx.Theme.define("tc.theme.Appearance",
{
  extend : qx.theme.modern.Appearance,

  appearances :
  {
    "table-header-cell-filter" :
    {
      alias : "table-header-cell",
      include : "table-header-cell",

      style : function(states)
      {
        return {
          enableFilter  : false
        };
      }
    }
  }
});