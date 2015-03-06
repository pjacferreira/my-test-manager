<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<div id="sb_menu" class="ui left sidebar vertical pointing menu" style="overflow: hidden; background-color: green">
  <?php if($this->session->isStarted() && $this->session->has('logged-in') && $this->session->has('user')): ?>
  <div class="item">
    <h3><?php echo _("MENU:GROUP:LOGOUT") ?></h3>
    <div class="pointing menu">
      <a name="logout" class="item">
        <i class="sign out icon"></i><?php echo _("MENU:ACTION:LOGOUT") ?>
      </a>
    </div>
  </div>
  <?php endif; ?>
  <div class="item">
    <h3><?php echo _("MENU:GROUP:US") ?></h3>
    <div class="pointing menu">
      <a name="contact" class="item" <?php $this->pageLink('institutional:contact', 'href') ?> >
        <i class="mail icon"></i><?php echo _("MENU:US:CONTACT") ?>
      </a>
      <a name="about" class="item" <?php $this->pageLink('institutional:about', 'href') ?> >
        <i class="info circle icon"></i><?php echo _("MENU:US:ABOUT") ?>
      </a>
    </div>
  </div>
  <div class="item">
    <h3><?php echo _("MENU:GROUP:HELP") ?></h3>
    <div class="pointing menu">
      <a name="help" class="item" href=<?php echo "'" . $this->url->get('page/institutional:help') . "'" ?> >
        <i class="help icon"></i><?php echo _("MENU:HELP:HELP") ?>
      </a>
    </div>
  </div>
</div>
