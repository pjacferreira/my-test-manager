<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- CREATE FOLDER FORM -->
<div id="form_create_folder" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:CREATE") ?></h4>
  <div class="required field">
    <input name="container-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:FOLDER:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_folder" class="ui positive button"><?php echo _("BUTTON:CREATE") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>    
<!-- RENAME FOLDER FORM -->
<div id="form_rename_folder" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:RENAME") ?></h4>
  <div class="required field">
    <input name="container-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:FOLDER:NEW-NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_rename_folder" class="ui positive button"><?php echo _("BUTTON:RENAME") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- DELETE FOLDER FORM -->
<div id="form_delete_folder" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:DELETE") ?></h4>
  <div class="ui header">
    <?php echo _("TEXT:QUESTION:FOLDER:DELETE:CONFIRMATION") ?>
  </div>
  <div class="ui buttons">
    <div id="button_delete_folder" class="ui negative button"><?php echo _("BUTTON:DELETE") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui positive button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- CREATE SET FORM -->
<div id="form_create_set" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:SET:CREATE") ?></h4>
  <div class="required field">
    <input name="set-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:SET:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_set" class="ui positive button"><?php echo _("BUTTON:CREATE") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:SECTION:SET:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="sets_folders" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("NAVIGATOR:PANE:FOLDERS") ?>
        </h3>
      </div>
      <div id="sets_grid" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("NAVIGATOR:PANE:SETS") ?>
        </h3>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:SECTION:SET:DETAILS") ?>
  </h3>
  <div class="ui attached segment">
    <div id="form_update_set" class="ui form">
      <div class="ui error message"></div>
      <h4 class="ui dividing header"><?php echo _("FIELD:GROUP:SET:DETAILS") ?></h4>
      <div class="required field">
        <label><?php echo _("FIELD:TITLE:SET:NAME") ?></label>
        <input name="set-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:SET:NAME") ?>" type="text">
      </div>
      <div class="field">
        <label><?php echo _("FIELD:TITLE:SET:DESCRIPTION") ?></label>
        <div name="set-description" class="textarea" data-placeholder="<?php echo _("FIELD:PLACEHOLDER:SET:DESCRIPTION") ?>"></div>
      </div>
      <div id="button_update_set" class="ui positive button"><?php echo _("BUTTON:UPDATE") ?></div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:SECTION:TEST:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="tests_folders" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("NAVIGATOR:PANE:FOLDERS") ?>
        </h3>
      </div>
      <div id="tests_grid" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("NAVIGATOR:PANE:TESTS") ?>
        </h3>
      </div>
    </div>
  </div>
  <div id='list_tests' class="ui bottom attached segment">
  </div>
</div>
