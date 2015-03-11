<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<div id="div_header" class="ui padded grid">
  <div class="teal row">
    <div class="two wide teal column">
      <h2 class="ui header">LOGO</h2>
    </div>
    <div class="fourteen wide right aligned teal column">
      <div id="choose" class="ui pointing right label"><?php echo _("LABEL:USER:CHOOSE") ?></div>
      <select id="orgs_list" class="ui search selection dropdown">
        <option value=""><?php echo _("PLACEHOLDER:ORGANIZATION") ?></option>
      </select>
      <select id="projects_list" class="ui search selection dropdown disabled">
        <option value=""><?php echo _("PLACEHOLDER:PROJECT") ?></option>
      </select>
    </div>
  </div>
</div>
