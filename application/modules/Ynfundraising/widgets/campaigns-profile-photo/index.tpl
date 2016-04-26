<div id='ynfundraising_campaign_photo'>
  <?php echo $this->itemPhoto($this->camapign, 'thumb.profile') ?>
   <?php if(!$this->camapign->activated):?>
	<div class="wrap_link">
		<div class="link"><span class="inactivated"><?php echo $this->translate("INACTIVED")?></span></div>	
	</div>
<?php endif;?>
</div>