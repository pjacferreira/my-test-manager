<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<body id="page_lorem" style="background-color: #F6E1D3">
  <!-- BACKGROUND -->
  <?php include $this->templatesPath() . $this->setting('includes/menu'); ?>
  <div class="pusher">
    <div id="btn_menu" class="ui black huge launch right attached button" style="z-index: 100">
      <i class="icon list layout"></i>
      <span class="text" style="display: none;"><?php echo _("LANDING:BODY:CLICK") ?></span>
    </div>
    <div class="bg_background">
      <img id="bg_image" class="transition" src=<?php echo '"' . $this->url->getUrlAsset('img/home.png') . '"'; ?> >
    </div>
    <div id="div_content" class="segment" style="margin: auto 100px;">
      <?php include $this->templatesPath() . $this->setting('includes/content'); ?>
    </div>
  </div>
  <div id="toaster">
  </div>
  <!-- JS BODY SCRIPTS -->
  <?php $this->includeJSScripts('body'); ?>
  <!-- JS ON-LOAD SCRIPTS -->
  <?php $this->includeJSScripts('on-load'); ?>
</body>
