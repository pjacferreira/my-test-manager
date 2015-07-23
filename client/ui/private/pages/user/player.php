<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- FORM STEP COMPLETE -->
<div id="form_step_comment" class="tc ui form hidden">
  <h3 class="ui dividing inverted header"><?php echo _("FORM:TITLE:PLAYENTRY:COMMENT") ?></h3>
  <div class="field">
    <textarea name="comment" placeholder="<?php echo _("FIELD:PLACEHOLDER:PLAYENTRY:COMMENT") ?>" type="text"></textarea>
  </div>
  <div class="ui buttons">
    <div id="button_submit_comment" class="ui positive button"><?php echo _("BUTTON:SUBMIT") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>    
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:SECTION:RUNS:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("NAVIGATOR:PANE:FOLDERS") ?>
        </h3>
      </div>
      <div id="list_runs" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("NAVIGATOR:PANE:RUNS") ?>
        </h3>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:SECTION:PLAYER:DETAILS") ?>
  </h3>
  <div id='player' class="ui stackable column grid player segment">
    <div class="six wide column">
      <h3 class="ui centered header">
        <?php echo _("PAGE:SECTION:PLAYER:TESTS") ?>
      </h3>
      <div id="test_cards" class="ui">
      </div>
    </div>
    <div class="ten wide column">
      <h3 class="ui centered header">
        <?php echo _("PAGE:SECTION:PLAYER:STEPS") ?>
      </h3>
      <div id="step_cards" class="ui">
      </div>
    </div>
  </div>
</div>
