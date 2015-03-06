<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<div id="sb_context" class="ui right sidebar vertical pointing menu" style="overflow: hidden; background-color: bisque">
  <div class="item">
    <h3>Active Friends</h3>
    <div class="pointing menu">
      <a class="active item" <?php $this->pageLink('user:home?pferreira', 'href') ?> >
        <i class="home icon"></i>Paulo Ferreira (+5)
      </a>
      <a class="item" <?php $this->pageLink('user:home?mpereira', 'href?') ?> >
        <i class="home icon"></i>Mario Pereira (+3)
      </a>
    </div>
  </div>
  <div class="item">
    <h3>Active Groups</h3>
    <div class="pointing menu">
      <a class="active item" <?php $this->pageLink('group:home?avrunners', 'href') ?> >
        <i class="home icon"></i>Aveiro Night Runners (+10)
      </a>
      <a class="item" <?php $this->pageLink('group:home?runsport', 'href') ?> >
        <i class="home icon"></i>Run Porto (+7)
      </a>
      <a class="item" <?php $this->pageLink('group:home?fullsport', 'href') ?> >
        <i class="home justify icon"></i>Full Sport (+3)
      </a>
    </div>
  </div>
</div>
