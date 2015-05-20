<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:USER:RUNS:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("PAGE:USER:RUNS:FOLDERS") ?>
        </h3>
      </div>
      <div id="list_runs" class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("PAGE:USER:RUNS:RUNS") ?>
        </h3>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:USER:PLAYER") ?>
  </h3>
  <div id='player' class="ui stackable column grid player segment">
    <div class="six wide column">
      <h3 class="ui centered header">
        <?php echo _("PAGE:USER:PLAYER:TEST") ?>
      </h3>
      <div id="test_cards" class="ui">
        <div class="ui card test">
          <div class="content">
            <div class="header">Previous Test Title</div>
            <div class="description">
              <p>Previous Test Description</p>
            </div>
          </div>
        </div>
        <div class="ui card test current">
          <div class="content">
            <div class="header">Current Test Title</div>
            <div class="description">
              <p>Current Test Description</p>
            </div>
          </div>
          <div style="text-align: center">
            <span>
              <i class="angle double up large icon"></i>
              Restart
            </span>
            <span>
              <i class="angle up large icon"></i>
              Previous
            </span>
          </div>
        </div>
        <div class="ui card test">
          <div class="content">
            <div class="header">Next Test Title</div>
            <div class="description">
              <p>Next Test Description</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="ten wide column">
      <h3 class="ui centered header">
        <?php echo _("PAGE:USER:PLAYER:STEPS") ?>
      </h3>
      <div id="step_cards" class="ui">
        <div class="ui card step">
          <div class="content">
            <div class="header">Previous Step Title</div>
            <div class="description">
              <p>Previous Step Description</p>
            </div>
          </div>
        </div>
        <div class="ui card step current">
          <div class="content">
            <div class="header">Current Step Title</div>
            <div class="description">
              <p>Current Step Description</p>
              <p>Current Step Description</p>
            </div>
          </div>
          <div class="extra content">
            <div class="ui three column grid">
              <div class="column">
                <i class="large green thumbs outline up icon"></i>
                <i class="large green thumbs up icon"></i>
              </div>  
              <div class="center aligned column">
                <i class="angle up large icon"></i>
              </div>
              <div class="right aligned column ">
                <i class="thumbs down large red icon"></i>
              </div>  
            </div>              
          </div>
        </div>
        <div class="ui card step">
          <div class="content">
            <div class="header">Next Step Title</div>
            <div class="description">
              <p>Next Step Description</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
