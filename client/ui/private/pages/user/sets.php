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
<!-- CREATE SET FORM -->
<div id="form_create_set" class="testcenter ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:SET:CREATE") ?></h4>
  <div class="required field">
    <input name="set-name" placeholder="<?php echo _("FIELD:SET:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_set" class="ui positive button"><?php echo _("BUTTON:SET:CREATE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:USER:SETS:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders_1" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("PAGE:USER:SETS:FOLDERS") ?>
        </h3>
      </div>
      <div id="items_1" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("PAGE:USER:SETS:SETS") ?>
        </h3>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:USER:SETS:SET:DETAILS") ?>
  </h3>
  <div class="ui attached segment">
    <div id="form_update_set" class="ui form">
      <div class="ui error message"></div>
      <h4 class="ui dividing header"><?php echo _("PAGE:USER:SETS:SET:DETAILS") ?></h4>
      <div class="required field">
        <label><?php echo _("FIELD:TITLE:SET:NAME") ?></label>
        <input name="set-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:SET:NAME") ?>" type="text">
      </div>
      <div class="field">
        <label><?php echo _("FIELD:TITLE:SET:DESCRIPTION") ?></label>
        <textarea name="set-description" placeholder="<?php echo _("FIELD:PLACEHOLDER:SET:DESCRIPTION") ?>" type="text"></textarea>
      </div>
      <div id="button_update_set" class="ui positive button"><?php echo _("BUTTON:SET:UPDATE") ?></div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:USER:SETS:SET:TESTS") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders_2" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("PAGE:USER:SETS:FOLDERS") ?>
        </h3>
      </div>
      <div id="items_2" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("PAGE:USER:SETS:TESTS") ?>
        </h3>
      </div>
    </div>
  </div>
  <div id='list_tests' class="ui bottom attached segment">
  </div>
</div>
