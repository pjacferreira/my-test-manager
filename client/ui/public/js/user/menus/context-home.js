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
function initialize_context() {
  var $sidebar = $('#sb_context');
  
  /* Menu Button */
  // HOVER EFFECT
  $('#btn_context').hover(function () {
    $(this).find('span').show();
  }, function () {
    $(this).find('span').hide();
  });
  // MENU DISPLAY
  $('#btn_context').click(function () {
    $sidebar.sidebar('toggle');
  });
}

/*******************************
 * HANDLE CONTEXT MENU ACTIONS
 *******************************/
