<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<div id="form_recover" class="ui form" style="background-color: #3d3131; padding: 10px; width: 500px; margin: 10px auto; border-radius: 10px">
  <div class="required field">
    <input name="key" placeholder="<?php echo _("USER:RECOVER:FIELD:KEY") ?>" type="text" <?php $this->html_attribute('value', $this->parameter('key')) ?> >
  </div>
  <div class="ui two fields">
    <div class="required field">
      <div class="ui left icon input">
        <input name="password" placeholder="<?php echo _("FORM:FIELD:PASSWORD") ?>" type="password">
        <i class="lock icon"></i>
      </div>
    </div>
    <div class="required field">
      <div class="ui left icon input">
        <input name="confirmation" placeholder="<?php echo _("FORM:FIELD:PASSWORD-CNF") ?>" type="password">
        <i class="lock icon"></i>
      </div>
    </div>
  </div>
  <div id="btn_recover" class="ui positive button"><?php echo _("USER:RECOVER:BUTTON:CHGPWD") ?></div>
  <div class="ui error message"></div>
</div>      
