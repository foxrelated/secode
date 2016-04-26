<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {
 display: table;
  height: 65px;
}
</style>

<h2><?php echo $this->translate("Store Plugin") ?></h2>
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<h3><?php echo $this->translate("Payment Gateways") ?></h3>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>
