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
function initialize() {
  // Initialize the Session
  initialize_session();

  // Initialize Organizations
  initialize_dropdowns();
  
  // Initialize Conext Menu
  initialize_menu('sb_context', 'btn_context');

  // Remove Loader
  $('#loader').removeClass('active');
}
