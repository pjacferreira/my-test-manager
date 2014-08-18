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
 *
 */
qx.Bootstrap.define("utility.SequencedCallbacks", {
  extend : qx.core.Object,

  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */

  /**
   * Create an Object that processes functions in sequence.
   * ok prototype is:
   * - function(parameters) {
   *     }
   * nok prototype is:
   * - function(error_message) {
   *     }
   *
   * @param ok {Function} Function called on success
   * @param nok {Function?null} Function called in case of error, if not provided
   *   a generic function is created that just logs the error.
   * @param context {Object} Context for Function Call
   */
  construct : function(ok, nok, context) {
    if (qx.core.Environment.get("qx.debug")) {
      qx.core.Assert.assertFunction(ok, "[ok] Is not of the expected type!");
      qx.core.Assert.assertObject(context, "[context] Is not of the expected type!");
    }
    if (!qx.lang.Type.isFunction(nok)) {
      nok = function(message) {
        this.debug("No NOK FUNCTION defined");
        this.error(message);
      }
    }
    this.__ok = ok;
    this.__nok = nok;
    this.__context = context;
    this.__callbacks = new utility.Map();
    this.__active_sequences = [];
    this.__new_additions = [];
    this.__new_removals = [];
  },

  /**
   *
   */
  destruct : function() {
    // NOTE: Do not call qx.core.Object:destruct, as THERE IS NONE, and forces an exception 
    // this.base(arguments);

    // Cleanup Variables
    this.__context = null;
    this.__ok = null;
    this.__nok = null;
    this.__callbacks = null;
    this.__active_sequences = null;
    this.__new_additions = null;
    this.__new_removals = null;
  },

  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members : {
    __context : null,
    __ok : null,
    __nok : null,
    __callbacks : null,
    __executing : false,
    __aborted : false,
    __active_sequences : null,
    __current_sequence : null,
    __new_additions : null,
    __new_removals : null,
    __parameters : null,

    /**
     * Returns the number of calls to execute in the sequence.
     *
     * @return {Integer} Number of calls to execute 
     */
    count: function() {
      return this.__callbacks.count();
    },
    /**
     * Add a Callback Function at the sequence number specified.
     * Callback prototype is:
     * - function(parameters) {
     *     ...
     *     return new-parameter-values;
     *     or
     *     throw "exception-message" in case of failure.
     *     }
     *
     * Where parameters can be, and can be modified, through the sequence of the
     * calls.
     *
     * This function will replace, silently, any existing callback function that
     * exists with the same sequence number.
     * The callback sequence, can be altered, during the execution phase, but only
     * sequences higher than the current sequence will be executed. Changes with
     * sequence numbers lower than the current sequence will be ignored.
     *
     * @param sequence {Integer} Sequence for Callback Function
     * @param callback {Function} Callback function
     * @param timeout {Integer?null} Timeout for asynchronous call, this implies
     *   that, the callback should be called asynchronously (or has asynchronous
     *   components, that will stop the normal processing until complete)
     * @return {Object} 'this' to allow chaining of function calls
     */
    add : function(sequence, callback, timeout) {
      // Is 'sequence' an Number (float or integer)?
      if (qx.lang.Type.isNumber(sequence)) {     // YES

        // Make sure qe have an integer and not a float
        sequence = parseInt(sequence);

        // Is sequence and callback valid?
        if ((sequence > 0) && qx.lang.Type.isFunction(callback)) {   // YES

          // Is timeout a Number
          if (qx.lang.Type.isNumber(timeout) && (timeout > 0)) {     // YES

            // Make sure it contains a valid value
            timeout = parseInt(timeout);
            timeout = timeout > 0 ? timeout : null;
          } else {                               // NO
            timeout = null;
          }

          // Does the callback require asynchronous handling?
          if (timeout !== null) {                // YES
            this.__callbacks.add(sequence, {
              'callback' : callback,
              'timeout' : timeout
            });
          } else {                               // NO

            // Add function to the list of Callback
            this.__callbacks.add(sequence, callback);
          }

          // Are we making this change while we are executing?
          if (this.__executing) {                // YES

            // Make sure that the change is noted
            this.__new_additions.push(sequence);
          }
        }
      }
      return this;
    },

    /**
     * Remove Callback with the Sequence Number given. If the callback doesn't
     * exists, the function will, silently, do nothing.
     *
     * @param sequence {Integer} the sequence to remove
     * @return {Object} 'this' to allow chaining of function calls
     */
    remove : function(sequence) {
      // Is 'sequence' an Number (float or integer)?
      if (qx.lang.Type.isNumber(sequence)) {     // YES

        // Make sure qe have an integer and not a float
        sequence = parseInt(sequence);

        // If the sequence exists remove it
        this.__callbacks.remove(sequence);

        // Are we making this change while we are executing?
        if (this.__executing) {                  // YES

          // Make sure that the change is noted
          this.__new_removals.push(sequence);
        }
      }
      return this;
    },

    /**
     * Does the specified sequence number exist?
     *
     * @param sequence {Integer} the sequence number to test
     * @return {Boolean} 'true' if the sequence exists, 'false' otherwise.
     */
    has : function(sequence) {
      // Is 'sequence' an Number (float or integer)?
      if (qx.lang.Type.isNumber(sequence)) {     // YES

        // Make sure qe have an integer and not a float
        sequence = parseInt(sequence);

        // Does the sequence exist in the map
        return this.__callbacks.has(sequence);
      }
      return false;
    },

    /**
     * Resets the current object to a pristine state, remove any existing
     * callbacks, BUT will not touch the existing ok, nok and context of the
     * object.
     *
     * @return {Object} 'this' to allow chaining of function calls
     */
    reset : function() {
      // Reset All Internal Members to the default state
      this.__callbacks.reset();
      this.__active_sequences = [];
      this.__new_additions = [];
      this.__new_removals = [];
      this.__executing = false;
      this.__aborted = false;
      this.__current_sequence = null;
      this.__parameters = null;

      /* NOTE: Possible race condition
       * If a reset is called in THE MIDDLE OF an assynchronous call, then
       * a call to 'next' or 'abort' might occur after the object has been
       * reset
       */
      return this;
    },

    /**
     * Execute the callbacks, in order.
     *
     * @param parameters {Var} Value for the parameter, to be passed to the 1st callback
     * @param delay {Integer?null} If defined, the execute will setup a timer,
     *   with the delayed specified, so that the actual processing of the call back
     *   sequence is delayed, allowing the execute to return immediately. Otherwise
     *   the execute will only return, when all the initialize functions have
     *   executed.
     */
    execute : function(parameters, delay) {
      // Do we have functios to process?
      if (this.__callbacks.count() > 0) {        // YES

        // Is a delay specified (i.e. do we want to execute the functions asynchronously)?
        if (qx.lang.Type.isNumber(delay) && (delay > 0)) {           // YES
          var save_this = this;
          setTimeout(function() {
            // Call the execute in the context of the this object
            save_this.execute(parameters);
          }, delay);
        } else {                                 // NO
          this.__execute(parameters);
        }
      } else {                                   // NO: Nothing to do
        this.__callOK(parameters);
      }
    },
    next : function(parameters) {
      if (this.__executing && !this.__aborted) {
        // Process Any Sequence Changes
        this.processSequenceChanges();

        // Continue Processing
        this.__parameters = parameters;
        this.__next();
      }
    },
    abort : function(message) {
      if (this.__executing && !this.__aborted) {
        // Abort Processing
        this.__abort(message);
      }
    },

    /*
     ***************************************************************************
     PRIVATE METHODS
     ***************************************************************************
     */
    __execute : function(parameters) {
      this.__executing = true;

      // Create Sequence List ot Process
      this.__active_sequences = utility.Array.map(this.__callbacks.keys(), function(key) {
        return parseInt(key);
      }, this).sort();

      // Save the Current Parameters
      this.__parameters = parameters;

      // Do we have an functions to process?
      if (this.__active_sequences.length) {      // YES

        // Start the Process
        this.__next();
      } else {                                   // NO

        // All Finished successfully: Call OK
        this.__callOK(parameters);
        this.__executing = false;
      }
    },
    __next : function() {
      if (this.__active_sequences.length) {      // YES

        // Set the Current State
        this.__current_sequence = this.__active_sequences.shift();

        // Get the Next Function to Process
        var callback = this.__callbacks.get(this.__current_sequence);

        // Is it a simple function?
        if (qx.lang.Type.isFunction(callback)) { // YES
          this.__processSynchronous(callback);

          // Has an abort been signaled?
          if (!this.__aborted) {                 // NO

            // Continue Processing
            this.__next();
          }

          // ELSE: Stop Processing (Note: The Abort has already called the NOK Function)
        } else {
          this.__processaAsynchronous(callback);

          /* Asynchronous calls require that the called function use the
           * 'next' or 'abort' functions on this object to continue or
           * finish processing
           */
        }
      } else {                                   // NO: Nothing Left to do

        // All Finished successfully: Call OK
        this.__callOK(this.__parameters);
        this.__executing = false;
      }
    },
    __abort : function(message) {
      // Make sure we signal an abort
      this.__aborted = true;
      this.__executing = false;

      // LOG Message
      this.error(message);

      // Function Failed: Call NOK
      this.__callNOK(this.__parameters, message);
    },
    __processSynchronous : function(callback) {
      try {
        // Call the Function
        this.__parameters = callback.call(this.__context, this.__parameters);

        // Process Any Sequence Changes
        this.processSequenceChanges();
      }catch (message) {                         // Had a problem executing the function call
        this.__abort(message);
      }
    },
    __processaAsynchronous : function(to_call) {
      try {
        // Handle Closure
        var save_this = this;
        var context = this.__context;
        var parameters = this.__parameters;
        var callback = to_call.callback;
        var timeout = to_call.timeout;
        var test_timeout = this.__test_sequence_timeout;
        var sequence = this.__current_sequence;

        // Initiate Function (asynchronously) after Small Delay
        setTimeout(function() {
          try {
            // Create a Timeout for the Current Call
            setTimeout(function() {
              test_timeout.call(save_this, sequence);
            }, timeout);

            // Call the Function
            callback.call(context, parameters);
          }catch (message) {                     // Had a problem executing the function call
            save_this.abort(message);
          }
        }, 50);
      }catch (message) {
        this.__abort(message);
      }
    },
    processSequenceChanges : function() {
      // Did we remove sequences during the execution of the function?
      if (this.__new_removals.length) {          // YES
        this.__active_sequences = this.__remove_sequences(this.__new_removals, this.__current_sequence, this.__active_sequences);
        this.__new_removals = [];
      }

      // Did we add sequences during the execution of the function?
      if (this.__new_additions.length) {         // YES
        this.__active_sequences = this.__remove_sequences(this.__new_additions, this.__current_sequence, this.__active_sequences);
        this.__new_additions = [];
      }
    },
    __remove_sequences : function(to_remove, cutoff, from) {
      // Does 'from' have anything?
      if (from.length > 0) {                     // YES

        // Do we have more than one entry?
        if (to_remove.length > 1) {              // YES
          var sequence;
          for (var i = 0; i < to_remove.length; ++i) {
            sequence = to_remove[i];

            // Is the Sequence Greater than the Current Cutoff Value?
            if (sequence > cutoff) {             // YES:
              from = this.__remove_a_sequence(sequence, from);
            }
          }
        } else {                                 // NO: Single Entry
          var sequence = to_remove[0];

          // Is the Sequence Greater than the Current Cutoff Value?
          if (sequence > cutoff) {               // YES
            from = this.__remove_a_sequence(sequence, from);
          }

          // ELSE: No so Nothing to do
        }
      }

      // ELSE: No so Nothing to do
      return from;
    },
    __remove_a_sequence : function(sequence, from) {
      var index = from.indexOf(sequence);

      // Does the sequence exist in the array?
      if (index >= 0) {                          // YES

        // Is it the 1st Entry?
        if (index === 0) {                       // YES: Use simple shift
          from.shift();
        } else {                                 // NO: Use splice to remove
          from.splice(index, 1);
        }
      }
      return from;
    },
    __add_sequences : function(to_add, cutoff, to) {
      if (to_add.length > 1) {
        var sequence, added = false;
        for (var i = 0; i < to_add.length; ++i) {
          sequence = to_add[i];

          // Is the Sequence Greater than the Current Cutoff Value?
          if (sequence > cutoff) {               // YES

            // Does the sequence already exist in the array?
            if (to.indexOf(sequence) < 0) {      // NO
              to.add(sequence);
              added = true;
            }
          }
        }

        // Did we add anything to the array?
        if (added) {                             // YES: Sort it to make sure we have the correct sequencing
          to.sort();
        }
      } else {                                   // NO: Single Entry
        var sequence = to_add[0];

        // Is the Sequence Greater than the Current Cutoff Value?
        if (sequence > cutoff) {                 // YES: Add it then
          to.push(sequence);

          // Does the new array have more than one entry?
          if (to.length > 1) {                   // YES: Sort it to make sure we have the correct sequencing
            to.sort();
          }
        }

        // ELSE: No so Nothing to do
      }
      return to;
    },
    __test_sequence_timeout : function(sequence) {
      if (this.__executing && (this.__current_sequence === sequence)) {
        this.__abort("Function Call [" + sequence + "] Timed out.");
      }
    },
    __callOK : function(parameters) {
      this.__ok.call(this.__context, parameters);
    },
    __callNOK : function(parameters, message) {
      this.__nok.call(this.__context, parameters, message);
    }
  }                                              // SECTION: MEMBERS
});
