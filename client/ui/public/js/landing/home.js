/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

function window_resize() {
  var $window = $(window);

  // Re-size Image Clipper to Garauntee No Overflow
  $(".bg_background").height($window.height());
  $(".bg_background").width($window.width());

  // Active Image - Re-center
  var $active_img = $("#bg_image");
  var tx = ($window.width() - $active_img.width()) / 2;
  var ty = ($window.height() - $active_img.height()) / 2;
  $active_img.css('transform', 'translate(' + tx + 'px, ' + ty + 'px)');

  // Active Form - Re-center
  if (window.hasOwnProperty('$form_displayed')) {
    form_center(window.$form_displayed);
  }
}

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/

/**
 * 
 * @returns {undefined}
 */
function initialize() {
  // Initialize the Session
  initialize_session();

  // Initialize Forms
  initialize_forms();

  // Force Initial Resize Calculations
  window_resize();

  // Capture Window Resize so that we can re-position the images
  $(window).resize(window_resize);
}

/*******************************
 * LOGIN FORM
 *******************************/

function __click_login() {
  if ($.isset(window.$form_displayed)) {
    window.$form_displayed.addClass('hidden');
  }
  form_show($('#form_login'));
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_login($form) {
  // Link Buttons to Handlers
  $form.find(".button").each(function () {
    var $this = $(this);
    var name = $.strings.nullOnEmpty($this.attr('id'));
    if (name !== null) {
      var handler = '__' + name;
      if (window.hasOwnProperty(handler) && $.isFunction(window[handler])) {
        $this.click($form, window[handler]);
      }
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_login(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;

  form_submit($form,
    {
      username: {
        identifier: 'username',
        rules: [
          {
            type: 'username_email',
            prompt: 'Missing/Invalid Username or Email'
          }
        ]
      },
      password: {
        identifier: 'password',
        rules: [
          {
            type: 'empty',
            prompt: 'Invalid Password'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_login,
    onFailure: function () {
      alert("Login: Missing or Invalid Fields");
    },
    rules: {
      username_email: rule_username_email
    }
  });
}

function __do_login() {
  var $form = $('#form_login');

  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function () {
    values[this.name] = $(this).val();
  });

  // Calculate the Salted Hash of the Password
  var hash = salted_hash(window.__session.salt, values.password);

  testcenter.services.call(['session', 'login'], [values.username, hash], null, {
    call_ok: function (reply) {
      // Add Response to Session 
      window.__session = $.extend({}, window.__session, reply);

      // Get the next page to move to
      var path = $.objects.keyToPath('__session.next_page');
      var next_page = $.objects.get(path, window, 'user:home');
      $.objects.remove(path, window);

      // Move to the Next Page
      window.location = testcenter.site.page(next_page);
    },
    call_nok: function (code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_recover(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;

  form_submit($form,
    {
      username: {
        identifier: 'username',
        rules: [
          {
            type: 'email',
            prompt: 'Missing/Invalid Username or Email'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_recover,
    onFailure: function () {
      alert("Recover: Missing or Invalid Fields");
    },
    rules: {
      email: rule_email
    }
  });
}

function __do_recover() {
  var $form = $('#form_login');

  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function () {
    values[this.name] = $(this).val();
  });

  // Calculate the Salted Hash of the Password
  var hash = salted_hash(window.__session.salt, values.password);

  testcenter.services.call(['user', 'recover'], values.username, null, {
    call_ok: function (reply) {
      // Add Response to Session 
      window.__session = $.extend({}, window.__session, reply);

      // Get the next page to move to
      var path = $.objects.keyToPath('__session.next_page');
      var next_page = $.objects.get(path, window, 'user:home');
      $.objects.remove(path, window);

      // Move to the Next Page
      window.location = testcenter.site.page(next_page);
    },
    call_nok: function (code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

/*******************************
 * HELPER FUNCTIONS
 *******************************/

function form_show($form) {
  if (window.hasOwnProperty('__session')) {
    // Do we have a form with th given id?
    if ($form.length) { // YES
      form_center($form);
      $form.removeClass('hidden');
      window.$form_displayed = $form;
    } else {
      // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
      alert("No Form with the id [" + id + "]");
    }
  } else {
    // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
    alert("Failed Initializing Comunication with Server.");
  }
}

function form_hide($form) {
  if (window.hasOwnProperty('__session')) {
    // Do we have a form with th given id?
    if ($form.length) { // YES
      $form.addClass('hidden');
      window.$form_displayed = null;
    } else {
      // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
      alert("No Form with the id [" + id + "]");
    }
  } else {
    // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
    alert("Failed Initializing Comunication with Server.");
  }
}

/**
 * 
 * @param {type} value
 * @returns {Boolean}
 */
function rule_email(value) {
  var emailRE = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?", "i");
  var ok = !(value === undefined || '' === value) && emailRE.test(value);
  return ok;
}

/**
 * 
 * @param {type} value
 * @returns {Boolean}
 */
function rule_username_email(value) {
  var emailRE = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?", "i");
  var usernameRE = new RegExp("[a-z][a-z0-9_]*$", "i");
  var ok = !(value === undefined || '' === value) && (emailRE.test(value) || usernameRE.test(value));
  return ok;
}

/**
 * 
 * @param {type} salt
 * @param {type} password
 * @returns {unresolved}
 */
function salted_hash(salt, password) {
  // Create MD5 HASH, HEX REPRESENTATION
  var hash_MD5 = CryptoJS.MD5(password).toString(CryptoJS.enc.Hex);
  // Create SHA256 HASH, HEX REPRESENTATION, of the SALT (as is) and MD5 HASH Lower Case
  return CryptoJS.SHA256(salt + hash_MD5).toString(CryptoJS.enc.Hex);
}

/**
 * 
 * @param {type} password
 * @returns {unresolved}
 */
function password_hash(password) {
  // Create MD5 HASH, HEX REPRESENTATION in Lower Case
  return CryptoJS.MD5(password).toString(CryptoJS.enc.Hex);
}
