<!-- render my widget -->
<div class="headline">
  <h2>
    <?php echo $this->translate('GroupBuy');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<h2><?php echo $this->translate("Helps"); ?></h2>
<br />  
<div class="global_content">
	<!--  do not remove this line  -->
	<a id="faq-0" name="faq-0"></a>
	<div class="layout_left">
		<?php echo $this->content()->renderWidget('groupbuy.help-navigator') ?>	
	</div>
	<div class="layout_middle">
		<h3><?php echo $this->item->getTitle() ?></h3>	
		<div>
			<?php echo $this->item->content ?>
		</div>
	</div>
</div>
