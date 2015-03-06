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
function initialize_menu(id_menu, id_button) {
  var $sidebar = $('#' + id_menu);
  // Did we find the Menu Container?
  if ($sidebar.length) { // YES
    var $button = $('#' + id_button);

    // Did we find the Menu Show/Hide Button?
    if ($button.length) { // YES
      /* Menu Button */
      // HOVER EFFECT
      $button.hover(function () {
        $(this).find('span').show();
      }, function () {
        $(this).find('span').hide();
      });
      // MENU DISPLAY
      $button.click(function () {
        $sidebar.sidebar('toggle');
      });
    }

    // Initialize Menu
    $sidebar.find("a.item").each(function () {
      var $this = $(this);
      var name = $.strings.nullOnEmpty($this.attr('name'));
      if (name !== null) {
        var handler = '__click_' + name;
        if (window.hasOwnProperty(handler) && $.isFunction(window[handler])) {
          $this.click($sidebar, window[handler]);
        }
      }
    });
  }
}
