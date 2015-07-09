<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- CREATE FOLDER FORM -->
<div id="form_step_complete" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:STEP:COMPLETE") ?></h4>
  <div class="field">
    <select name="pass_code" class="ui search dropdown">
      <option value=""><?php echo _("PLACEHOLDER:PASS:CODE") ?></option>
    </select>
  </div>
  <div class="field">
    <textarea name="comment" placeholder="<?php echo _("FIELD:COMMENT") ?>" type="text"></textarea>
  </div>
  <div class="ui buttons">
    <div id="button_step_complete" class="ui positive button"><?php echo _("BUTTON:STEP:PASS") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>    
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:USER:RUNS:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("PAGE:USER:RUNS:FOLDERS") ?>
        </h3>
      </div>
      <div id="list_runs" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("PAGE:USER:RUNS:RUNS") ?>
        </h3>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:USER:PLAYER") ?>
  </h3>
  <div id='player' class="ui stackable column grid player segment">
    <div class="six wide column">
      <h3 class="ui centered header">
        <?php echo _("PAGE:USER:PLAYER:TEST") ?>
      </h3>
      <div id="test_cards" class="ui">
      </div>
    </div>
    <div class="ten wide column">
      <h3 class="ui centered header">
        <?php echo _("PAGE:USER:PLAYER:STEPS") ?>
      </h3>
      <div id="step_cards" class="ui">
      </div>
    </div>
  </div>
