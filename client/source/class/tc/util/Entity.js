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

/**
 *
 */
qx.Class.define("tc.util.Entity",
{
  statics: {
    isEntity : function(value) {
      return qx.lang.Type.isObject(value) && value.hasOwnProperty('__entity');
    },
    IsEntityOfType : function(value, type) {
      return tc.util.Entity.isEntity(value) && (value.__entity.toLowerCase() === type);
    }    
  }
});  

