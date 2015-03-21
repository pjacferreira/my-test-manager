<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- CREATE FOLDER FORM -->
<div id="form_create_folder" class="ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:CREATE") ?></h4>
  <div class="required field">
    <input name="name" placeholder="<?php echo _("FIELD:FOLDER:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create" class="ui positive button"><?php echo _("BUTTON:FOLDER:CREATE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>    
<!-- RENAME FOLDER FORM -->
<div id="form_rename_folder" class="ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:RENAME") ?></h4>
  <div class="required field">
    <input name="name" placeholder="<?php echo _("FIELD:FOLDER:NEW-NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_rename" class="ui positive button"><?php echo _("BUTTON:FOLDER:RENAME") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- DELETE FOLDER FORM -->
<div id="form_delete_folder" class="ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:DELETE") ?></h4>
  <div class="ui header">
    <?php echo _("TEXT:QUESTION:FOLDER:DELETE:CONFIRMATION") ?>
  </div>
  <div class="ui buttons">
    <div id="button_delete" class="ui negative button"><?php echo _("BUTTON:FOLDER:DELETE") ?></div>
    <div class="or"></div>
    <div id="button_cancel" class="ui positive button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    Tests Manager
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          Navigation
        </h3>
        <div id="folders">
        </div>
      </div>
      <div class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          Tests
        </h3>
        <div id="tests" class="ui four column grid middle aligned internally celled">
          <div class="tc_test aligned column">Select a Folder</div>
        </div>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    Test Details
  </h3>
  <div class="ui bottom attached segment">
    Select a Test
  </div>
</div>
