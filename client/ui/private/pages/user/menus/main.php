<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<div id="sb_menu" class="ui left sidebar vertical pointing menu" style="overflow: hidden; background-color: green">
  <div class="item">
    <h3><?php echo _("MENU:GROUP:YOUR") ?></h3>
    <div class="pointing menu">
      <a class="active item" <?php $this->pageLink('user:home', 'href') ?> >
        <i class="home icon"></i><?php echo _("MENU:ACTION:HOME") ?>
      </a>
      <a class="item" <?php $this->pageLink('user:tests', 'href') ?> >
        <i class="file text icon"></i><?php echo _("MENU:ACTION:TESTS") ?>
      </a>
      <a class="item" <?php $this->pageLink('user:sets', 'href') ?> >
        <i class="book ol icon"></i><?php echo _("MENU:ACTION:SETS") ?>
      </a>
      <a class="item" <?php $this->pageLink('user:runs', 'href') ?> >
        <i class="tasks icon"></i><?php echo _("MENU:ACTION:RUNS") ?>
      </a>
      <a class="item" <?php $this->pageLink('user:player', 'href') ?> >
        <i class="play icon"></i><?php echo _("MENU:ACTION:PLAYER") ?>
      </a>
      <a class="item" <?php $this->pageLink('user:profile', 'href') ?> >
        <i class="settings icon"></i><?php echo _("MENU:ACTION:PROFILE") ?>
      </a>
    </div>
  </div>
  <div class="item">
    <h3><?php echo _("MENU:GROUP:LOGOUT") ?></h3>
    <div class="pointing menu">
      <a name="logout" class="item">
        <i class="sign out icon"></i><?php echo _("MENU:ACTION:LOGOUT") ?>
      </a>
    </div>
  </div>
  <div class="item">
    <h3><?php echo _("MENU:GROUP:US") ?></h3>
    <div class="pointing menu">
      <a name="contact" class="item" <?php $this->pageLink('institutional:contact', 'href') ?> >
        <i class="mail icon"></i><?php echo _("MENU:ACTION:CONTACT") ?>
      </a>
      <a name="about" class="item" <?php $this->pageLink('institutional:about', 'href') ?> >
        <i class="info circle icon"></i><?php echo _("MENU:ACTION:ABOUT") ?>
      </a>
    </div>
  </div>
  <div class="item">
    <h3><?php echo _("MENU:GROUP:HELP") ?></h3>
    <div class="pointing menu">
      <a name="help" class="item" href=<?php echo "'" . $this->url->get('page/institutional:help') . "'" ?> >
        <i class="help icon"></i><?php echo _("MENU:ACTION:HELP") ?>
      </a>
    </div>
  </div>
</div>
