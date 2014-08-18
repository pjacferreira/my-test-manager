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

/**
 * A Series of Functions, that go Hand-in-Hand with the Mixins that
 * Support Input/Output functions of IMetaWidgetIO
 */
qx.Mixin.define("meta.ui.mixins.MFieldTransform", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /*
     ***************************************************************************
     MIXIN FUNCTIONS (Filter and Default Map Builders)
     ***************************************************************************
     */
    _mx_ft_applyTransforms: function(value) {
      var new_value = null;
      var field = this.getEntity();
      
      // STEP 1: Apply Static Transformations
      var modified_value = this.__mx_ft_applyStaticTransforms(field, value);
      if(modified_value !== null) {
        new_value = modified_value;
      }
      
      // STEP 2: Apply Dynamic Transformations
      modified_value = this.__mx_ft_applyDynamicTransforms(field, new_value === null ? value : new_value);
      if(modified_value !== null) {
        new_value = modified_value;
      }
      
      return new_value;
    },
    /*
     ***************************************************************************
     PRIVATE FUNCTIONS
     ***************************************************************************
     */
    __mx_ft_applyStaticTransforms: function(field, value) {
      var new_value = value;
      
      // Should the Value be Trimmed?
      if(field.isTrimmed()) { // YES
        new_value = utility.String.v_nullOnEmpty(value, true);
      }
            
      return value === new_value ? null : new_value;
    },
    __mx_ft_applyDynamicTransforms: function(field, value) {    
      
      // TODO Implement
      return null;
    }
  } // SECTION: MEMBERS
});
