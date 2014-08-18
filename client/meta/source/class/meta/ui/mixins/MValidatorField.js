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
qx.Mixin.define("meta.ui.mixins.MValidatorField", {
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  /**
   * Container Destructor
   */
  destruct: function() {
    // Cleanup
    this.__validator = null;
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __validator: null,
    /*
     ***************************************************************************
     MIXIN FUNCTIONS (Filter and Default Map Builders)
     ***************************************************************************
     */
    _mx_vf_isValid: function(value) {
      var entity = this.getEntity();

      // TODO Use Value Type (integer, decimal, string, date, etc.) as part of the validation process
      return this.__mx_vf_staticValidation(entity, value) &&
        this.__mx_vf_dynamicValidation(entity, value);
    },
    /*
     ***************************************************************************
     PRIVATE FUNCTIONS
     ***************************************************************************
     */
    __mx_vf_staticValidation: function(field, value) {
      // Is the value NULL?
      if (value === null) { // YES: Is it allowed
        return field.isNullable();
      }

      return true;
    },
    __mx_vf_dynamicValidation: function(field, value) {
      // Do we have a validation function?
      if (this.__validator === null) { // NO: Try to build one

        // Do we have validation rules?
        var rules = field.getValidationRules();
        if (rules !== null) { // YES

          // Is this a Single Rule?
          if (qx.lang.Type.isString(rules)) { // YES
            // Is the rule not empty?
            rules = utility.String.nullOnEmpty(rules);
            if (rules !== null) { // YES
              rules = [rules];
            }
          } else if (qx.lang.Type.isArray(rules)) { // NO: Multiple Rules
            rules = utility.Array.clean(utility.Array.trim(rules));
          }

          // Convert Rules to Javascript Expressions
          var expressions = [];

          /* NOTES:
           * 1. Each Rule should be valid javascript code, except the potential
           *    use of {value} to refer to the fields value.
           * 2. Multiple rules are automatically ANDed together, therefore
           *    if OR is required, it has to be in a single rule.
           */
          
          /* TODO: Evaluate Security Concerns of directly importing javascript,
           * withou evaluation or testing.
           */
          
          // Do we have rules to convert?
          if (rules !== null) { // YES
            var expression;

            // Cycle through the rules and convert them to JavaScript Expressions
            for (var i = 0; i < rules.length; ++i) {
              expression = rules[i];
              // Replace All References to {value}, with the functions parameters
              expression = expression.replace(/{\s*value\s*}/i, " value ");
            }
          }

          // Do we have expressions to convert to function?
          if (expressions !== null) { // YES

            // Start Function
            var validator = "function (value) { return";

            // Cycle through the expressions to make a single statement
            var addAND = false;
            for (i = 0; i < expressions.length; ++i) {
              if (addAND) {
                validator += " && ";
              }

              validator += "(" + expressions[i] + ")";
              addAND = true;
            }

            // End Function
            validator += "; }";

            // Convert Validator String to Function
            this.__validator = eval(validator);
          }
        }
      }

      return this.__validator === null ? true : this.__validator.call(this, value);
    }
  } // SECTION: MEMBERS
});
