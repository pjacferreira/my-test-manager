/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/

/**
 * 
 * @returns {undefined}
 */
function initialize_forms() {
  // Initialize All Forms in the Document Body
  $(document.body).find(".form").each(function () {
    var $this = $(this);
    var name = $.strings.nullOnEmpty($this.attr('id'));
    if (name !== null) {
      var initializer = '__initialize_' + name;
      if (window.hasOwnProperty(initializer) && $.isFunction(window[initializer])) {
        window[initializer]($this);
      }
    }
  });

}

/*******************************
 * HELPER FUNCTIONS
 *******************************/

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function form_load($form) {
  var name = $form.attr('id');
  if (name !== null) {
    var initializer = '__load_' + name;
    if (window.hasOwnProperty(initializer) && $.isFunction(window[initializer])) {
      window[initializer]($this);
    }
  }
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function form_center($form) {
  var $window = $(window);

  var tx = ($window.width() - $form.outerWidth()) / 2;
  var ty = ($window.height() - $form.outerHeight()) / 2;

  $form.css({top: ty, left: tx});
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function form_display_toggle($form) {
  if ($form.hasClass('hidden')) {
    form_show($form);
  } else {
    form_hide($form);
  }
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function form_show($form) {
  if ($form.hasClass('hidden')) {
    form_center($form);
    $form.removeClass('hidden');
  }
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function form_hide($form) {
  if (!$form.hasClass('hidden')) {
    $form.addClass('hidden');
  }
}

/**
 * 
 * @param {type} $form
 * @param {type} rules
 * @param {type} settings
 * @returns {undefined}
 */
function form_submit($form, rules, settings) {
  // Remove Existing Field Errors
  $form.find('.field.error').removeClass('error');

  $form.
    // Remove Existing Error on Form
    removeClass('error').
    // Initialize Form with Rules and Settings
    form(rules, settings).
    // Submit the Form
    form('submit', $form);
}

/**
 * 
 * @param {type} $form
 * @param {type} errors
 * @returns {undefined}
 */
function form_disable($form, errors) {
  // Do we have error messages to display
  if ($.isset(errors)) {
    if (!$.isArray(errors)) {
      errors = [errors];
    }
  }

  // Disable Form Buttons
  $form.find('.button').addClass('disabled');
  // Disable Form Fields
  $form.find('.field').addClass('disabled');
  form_show_errors($form, errors);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function form_reset($form) {
  // Clear the Field Values
  $form.find('.field > :input').each(function () {
    $(this).val('');
  });

  // Clear the DIV TextAreas
  $form.find('.field > div.textarea').each(function () {
    $(this).empty();
  });

  // Remove Existing Field Errors
  $form.find('.field.error').removeClass('error');
}

/**
 * 
 * @param {type} $form
 * @param {type} errors
 * @returns {undefined}
 */
function form_show_errors($form, errors) {
  if (errors !== undefined) {
    // Add an Error Message to the Display
    $form.form('add errors', [errors]).addClass('error');
  }
}

