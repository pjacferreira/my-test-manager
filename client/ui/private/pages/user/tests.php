<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- CREATE FOLDER FORM -->
<div id="form_create_folder" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:CREATE") ?></h4>
  <div class="required field">
    <input name="name" placeholder="<?php echo _("FIELD:FOLDER:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_folder" class="ui positive button"><?php echo _("BUTTON:FOLDER:CREATE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>    
<!-- RENAME FOLDER FORM -->
<div id="form_rename_folder" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:RENAME") ?></h4>
  <div class="required field">
    <input name="name" placeholder="<?php echo _("FIELD:FOLDER:NEW-NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_rename_folder" class="ui positive button"><?php echo _("BUTTON:FOLDER:RENAME") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- DELETE FOLDER FORM -->
<div id="form_delete_folder" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:DELETE") ?></h4>
  <div class="ui header">
    <?php echo _("TEXT:QUESTION:FOLDER:DELETE:CONFIRMATION") ?>
  </div>
  <div class="ui buttons">
    <div id="button_delete_folder" class="ui negative button"><?php echo _("BUTTON:FOLDER:DELETE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui positive button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- CREATE TEST FORM -->
<div id="form_create_test" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:TEST:CREATE") ?></h4>
  <div class="required field">
    <input name="test-name" placeholder="<?php echo _("FIELD:TEST:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_test" class="ui positive button"><?php echo _("BUTTON:TEST:CREATE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- CREATE STEP FORM -->
<div id="form_create_step" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:STEP:CREATE") ?></h4>
  <div class="required field">
    <input name="title" placeholder="<?php echo _("FIELD:STEP:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_step" class="ui positive button"><?php echo _("BUTTON:STEP:CREATE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- EDIT STEP FORM -->
<div id="form_edit_step" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:STEP:EDIT") ?></h4>
  <div class="required field">
    <input name="step-title" placeholder="<?php echo _("FIELD:STEP:NAME") ?>" type="text">
  </div>
  <div class="field">
    <label><?php echo _("FIELD:TITLE:STEP:DESCRIPTION") ?></label>
    <textarea name="step-description" placeholder="<?php echo _("FIELD:PLACEHOLDER:STEP:DESCRIPTION") ?>" type="text"></textarea>
  </div>
  <div class="ui buttons">
    <div id="button_update_step" class="ui positive button"><?php echo _("BUTTON:STEP:UPDATE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:USER:TESTS:TEST_MANAGER") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders_1" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("PAGE:USER:TESTS:FOLDERS") ?>
        </h3>
      </div>
      <div id="items_1" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("PAGE:USER:TESTS:TESTS") ?>
        </h3>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:USER:TESTS:TEST:DETAILS") ?>
  </h3>
  <div class="ui attached segment">
    <div id="form_update_test" class="ui form">
      <div class="ui error message"></div>
      <h4 class="ui dividing header"><?php echo _("PAGE:USER:TESTS:TEST:DETAILS") ?></h4>
      <div class="required field">
        <label><?php echo _("FIELD:TITLE:TEST:NAME") ?></label>
        <input name="test-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:TEST:NAME") ?>" type="text">
      </div>
      <div class="field">
        <label><?php echo _("FIELD:TITLE:TEST:DESCRIPTION") ?></label>
        <textarea name="test-description" placeholder="<?php echo _("FIELD:PLACEHOLDER:TEST:DESCRIPTION") ?>" type="text"></textarea>
      </div>
      <div id="button_update_test" class="ui positive button"><?php echo _("BUTTON:TEST:UPDATE") ?></div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:USER:TESTS:TEST:STEPS") ?>
  </h3>
  <div id='list_steps' class="ui bottom attached segment">
  </div>
</div>
