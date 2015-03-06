<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<!-- LOGIN FORM -->
<div id="form_login" class="ui form hidden">
  <div class="required field">
    <input name="username" placeholder="<?php echo _("FORM:LOGIN:FIELD:USER") ?>" type="text">
  </div>
  <div class="required field">
    <div class="ui icon input">
      <input name="password" placeholder="<?php echo _("FIELD:PLACEHOLDER:PASSWORD") ?>" type="password">
      <i class="lock icon"></i>
    </div>
  </div>
  <div class="ui buttons">
    <div id="button_login" class="ui positive button"><?php echo _("FORM:LOGIN:BUTTON:LOGIN") ?></div>
    <div class="or"></div>
    <div id="button_recover" class="ui button"><?php echo _("FORM:LOGIN:BUTTON:RCVPWD") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
