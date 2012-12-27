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
qx.Class.define("tc.services.json.TCServiceEvent",
{
  extend : qx.event.type.Event,
  
  members :
  {
    __response : null,

    init : function(response, cancelable)
    {
      this.base(arguments, false, cancelable);

      this.__response = response;

      return this;
    },
    
    clone : function(embryo)
    {
      var clone = this.base(arguments, embryo);

      clone.__response = this.__response;

      return clone;
    },
    
    getResponse : function() {
      return this.__response;
    },
    
    isError : function() {
      return this.__response.error.code != 0;  
    },
    
    getErrorCode : function() {
      return this.__response.error.code;
    },

    getErrorMessage : function() {
      return this.__response.error.message;
    },
    
    getResult : function() {
      return this.__response.hasOwnProperty('return') ? this.__response['return'] : null;
    },
    
    /*
     *****************************************************************************
     DESTRUCTOR
     *****************************************************************************
     */

    destruct : function() {
      this.__response =  null;
    }
  }
});
    



