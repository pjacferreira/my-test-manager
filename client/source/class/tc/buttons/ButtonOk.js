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

#asset(tc/accept.png)

************************************************************************ */
qx.Class.define("tc.buttons.ButtonOk",
{
  extend : qx.ui.form.Button,
  
  construct : function() {
    this.base(arguments);
    
    this.setLabel("Ok");
    this.setIcon("tc/accept.png");
  }
  
});