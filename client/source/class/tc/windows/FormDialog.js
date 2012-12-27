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

#require(qx.ui.form.renderer.Single)
#require(qx.ui.layout.Basic)

************************************************************************ */
qx.Class.define("tc.windows.FormDialog",
{
  extend : qx.ui.window.Window,

  construct : function(sTitle, objFormView) {
    this.base(arguments, sTitle);
    
    // Set Dialog Properties
    this.setModal(true);
    this.setAlwaysOnTop(true);
    this.setResizable(false);
    this.setShowMaximize(false);
    this.setShowMinimize(false);
        
    // Set layout
    this.setLayout(new qx.ui.layout.Basic());

    // Add the Form to the Dialog
    this.add(objFormView);
  }
});


