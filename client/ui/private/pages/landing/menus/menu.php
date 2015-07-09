<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<div id="sb_menu" class="ui left sidebar vertical pointing menu" style="overflow: hidden; background-color: green">
  <div class="item">
    <h3><?php echo _("MENU:GROUP:ACTIONS") ?></h3>
    <div class="pointing menu">
      <a name="home" class="item" href=<?php echo "'" . $this->url->get('page/landing:home') . "'" ?> >
        <i class="home icon"></i><?php echo _("MENU:ACTION:HOME") ?>
      </a>
    </div>
  </div>
  <!--
    <div class="item">
      <h3><?php echo _("MENU:GROUP:CONDITIONS") ?></h3>
      <div class="pointing menu">
        <a name="use" class="item" href=<?php echo "'" . $this->url->get('page/landing:use') . "'" ?> >
          <i class="content icon"></i><?php echo _("MENU:CONDITIONS:USE") ?>
        </a>
        <a name="privacy" class="item" href=<?php echo "'" . $this->url->get('page/landing:privacy') . "'" ?> >
          <i class="privacy icon"></i><?php echo _("MENU:CONDITIONS:PRIVACY") ?>
        </a>
      </div>
    </div>
    <div class="item">
      <h3><?php echo _("MENU:GROUP:US") ?></h3>
      <div class="pointing menu">
        <a name="contact" class="item" href=<?php echo "'" . $this->url->get('page/landing:contact') . "'" ?> >
          <i class="mail icon"></i><?php echo _("MENU:US:CONTACT") ?>
        </a>
        <a name="about" class="item" href=<?php echo "'" . $this->url->get('page/landing:about') . "'" ?> >
          <i class="info circle icon"></i><?php echo _("MENU:US:ABOUT") ?>
        </a>
      </div>
    </div>
  -->
  <div class="item">
    <h3><?php echo _("MENU:GROUP:HELP") ?></h3>
    <div class="pointing menu">
      <a name="help" class="item" href=<?php echo "'" . $this->url->get('page/landing:introduction') . "'" ?> >
        <i class="help icon"></i><?php echo _("MENU:ACTION:INTRODUCTION") ?>
      </a>
    </div>
    <div class="pointing menu">
      <a name="help" class="item" href=<?php echo "'" . $this->url->get('page/landing:help') . "'" ?> >
        <i class="help icon"></i><?php echo _("MENU:ACTION:HELP") ?>
      </a>
    </div>
  </div>
</div>
