<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>
<!-- assoc tips -->
<div class="layout_middle">
<div class="tip">
  <span>
    <?php echo $this->translate('You have not set up your PayPal account yet. Please provide your PayPal account email %1$shere%2$s and try again!', '<a href="'.$this->url(array('module' => 'socialstore','controller' => 'my-account','action' => 'configure'),'default', true).'">', '</a>');?>
        </span>
</div>
</div>