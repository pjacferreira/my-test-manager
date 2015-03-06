<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<body id="page_lorem" style="background-color: #F6E1D3">
  <?php include $this->templatesPath() . $this->setting('includes/header'); ?>
  <div id="div_content" class="segment" style="margin: auto 100px;">
    <?php include $this->templatesPath() . $this->setting('includes/content'); ?>
  </div>
  <?php include $this->templatesPath() . $this->setting('includes/footer'); ?>
  <!-- JS BODY SCRIPTS -->
  <?php $this->includeJSScripts('body'); ?>
  <!-- JS ON-LOAD SCRIPTS -->
  <?php $this->includeJSScripts('on-load'); ?>
</body>
