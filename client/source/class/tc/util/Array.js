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
 * Array helper functions
 *
 */
qx.Bootstrap.define("tc.util.Array", {
  type: "static",
  /*
   *****************************************************************************
   STATIC MEMBERS
   *****************************************************************************
   */
  statics: {
    /**
     * Returns an array, that is the intersection of 2 (previously) sorted arrays.
     *
     * @param a {Array} First Array (Sorted)
     * @param b {Array} Second Array (Sorted)
     * @return {Array} Resultant Intersection
     */
    intersection: function(a, b) {

      var ai = 0, bi = 0;
      var result = new Array();

      // TODO Handle Special Cases (arrays of length == 1)
      while (ai < a.length && bi < b.length) {
        if (a[ai] < b[bi]) {
          ai++;
        }
        else if (a[ai] > b[bi]) {
          bi++;
        }
        else { /* they're equal */
          result.push(a[ai]);
          ai++;
          bi++;
        }
      }

      return result.length ? result : null;
    },
    /**
     * Returns an array, that is the union of 2 (previously) sorted arrays.
     * If an element co-exists in both arrays, only a single element will exist in the result.
     *
     * @param a {Array} First Array (Sorted)
     * @param b {Array} Second Array (Sorted)
     * @return {Array} Resultant Union
     */
    union: function(a, b) {
      var dif = tc.util.Array.difference(a, b);
      if ((dif != null) && (dif.length > 0)) {
        return a.concat(dif);
      } else {
        return a.length ? a : null;
      }
    },
    /**
     * Set Theory Difference http://en.wikipedia.org/wiki/Complement_(set_theory)
     * Returns the elements in array B, that are not part of array A
     *
     * @param a {Array} First Array (Sorted)
     * @param b {Array} Second Array (Sorted)
     * @return {Array} Resultant Difference
     */
    difference: function(a, b) {

      var ai = 0, bi = 0;
      var result = new Array();

      // TODO Handle Special Cases (arrays of length == 1)
      while (ai < a.length) {
        if (a[ai] < b[bi]) {
          ai++;
        }
        else if (a[ai] > b[bi]) { // Element in A is greater than the element in B (therefore Element in B can not be part of A)
          result.push(b[bi]);
          bi++;
        } else { // Equal
          ai++;
          bi++;
        }
      }

      if (bi < b.length) { // Left Over Elements in B (Remaining Elements are greater than any Element in A)
        result = result.concat(b.slice(bi));
      }

      return result.length ? result : null;
    },
    /**
     * Set Theory Difference http://en.wikipedia.org/wiki/Complement_(set_theory)
     * Returns the elements in array B, that are not part of array A
     *
     * @param array {Array} The Array to map the function over.
     * @param callback {Function} The function to apply to the array elements
     * @param thisArg {Object? null} The context in which to call the function
     * @return {Array} Resultant Difference
     */
    map: function(array, callback, thisArg) {
      if (Array.prototype.map) {
        return array.map(callback, thisArg);
      } else {
        // See Source: https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/map 
        var T, A, k;

        if (this == null) {
          throw new TypeError(" this is null or not defined");
        }

        // 1. Let O be the result of calling ToObject passing the |this| value as the argument.
        var O = Object(this);

        // 2. Let lenValue be the result of calling the Get internal method of O with the argument "length".
        // 3. Let len be ToUint32(lenValue).
        var len = O.length >>> 0;

        // 4. If IsCallable(callback) is false, throw a TypeError exception.
        // See: http://es5.github.com/#x9.11
        if (typeof callback !== "function") {
          throw new TypeError(callback + " is not a function");
        }

        // 5. If thisArg was supplied, let T be thisArg; else let T be undefined.
        if (thisArg) {
          T = thisArg;
        }

        // 6. Let A be a new array created as if by the expression new Array(len) where Array is
        // the standard built-in constructor with that name and len is the value of len.
        A = new Array(len);

        // 7. Let k be 0
        k = 0;

        // 8. Repeat, while k < len
        while (k < len) {

          var kValue, mappedValue;

          // a. Let Pk be ToString(k).
          //   This is implicit for LHS operands of the in operator
          // b. Let kPresent be the result of calling the HasProperty internal method of O with argument Pk.
          //   This step can be combined with c
          // c. If kPresent is true, then
          if (k in O) {

            // i. Let kValue be the result of calling the Get internal method of O with argument Pk.
            kValue = O[ k ];

            // ii. Let mappedValue be the result of calling the Call internal method of callback
            // with T as the this value and argument list containing kValue, k, and O.
            mappedValue = callback.call(T, kValue, k, O);

            // iii. Call the DefineOwnProperty internal method of A with arguments
            // Pk, Property Descriptor {Value: mappedValue, : true, Enumerable: true, Configurable: true},
            // and false.

            // In browsers that support Object.defineProperty, use the following:
            // Object.defineProperty(A, Pk, { value: mappedValue, writable: true, enumerable: true, configurable: true });

            // For best browser support, use the following:
            A[ k ] = mappedValue;
          }
          // d. Increase k by 1.
          k++;
        }

        // 9. return A
        return A;
      }
    },
    /**
     * Apply Trim to All Elements of a String Array (Non-String Elements or Empty 
     * String Elements will be set to NULL)
     *
     * @param array {String[]} String Array
     * @return {String[]} Return Trimmed String Array
     */
    trim: function(array) {

      if (array && (array.length > 0)) {
        // Trim all the Array Elements
        return tc.util.Array.map(array, function(element, index, array) {
          return tc.util.String.nullOnEmpty(element, true);
        }, this);
      }

      return null;
    },
    /**
     * Remove all NULL elements from an Array
     *
     * @param value {Var[]} Array to Clean
     * @return {Var[]} Clean Array
     */
    clean: function(array) {

      if (array && (array.length > 0)) {
        var newArray = [];
        for (var i = 0; i < array.length; ++i) {
          if (array[i] != null) { // Save Only Non-Null Elements
            newArray.push(array[i]);
          }
        }

        return newArray.length ? newArray : null;
      }

      return null;
    },
    /**
     * Split a CSV String into an Array
     *
     * @param value {String} String Value to Test
     * @param seperator {String ? null} Seperator Value (default ',')
     * @return {String[]} Return's a String or NULL if an Invalid Value
     */
    CSVtoArray: function(value, seperator) {
      if (qx.lang.Type.isString(value)) {
        seperator = tc.util.String.nullOnEmpty(seperator, true);
        return value.split(seperator !== null ? seperator : ',');
      } else if (qx.lang.Type.isArray(value)) {
        return value;
      }

      return null;
    }
  }
});