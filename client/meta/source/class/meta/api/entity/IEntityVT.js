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

qx.Interface.define("meta.api.entity.IEntityVT", {
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    /**
     * Retrieves the List of Validation Rules.
     * Format:
     *  rule:= comparison_operator ['|"] value ['|"] [ '||' rule ]
     *  
     * If an array of rules is provided, than the result of each rule in the
     * array, is ANDed to obtain the result.
     *
     * @abstract
     * @return {String|String[]|null} Validation Rules or NULL if none
     */
    getValidationRules: function() {
    },
    /**
     * Retrieves the List of Transformation Rules.
     * Format:
     *  rule:= comparison_operator ['|"] value ['|"] [ '||' rule ]
     *  
     * If an array of rules is provided, than the result of each rule in the
     * array, is ANDed to obtain the result.
     *
     * @abstract
     * @return {String|String[]|null} Transformation Rules or NULL if none
     */
    getTransformationRules: function() {
    }
  }
});
