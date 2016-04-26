<h3><?php echo $this->translate("Help") ?></h3>
<?php if($this -> item):?>
<div class="layout_left">
	<?php echo $this->content()->renderWidget('ynaffiliate.help-navigator') ?>	
</div>
<div class="layout_middle">
	<h3><?php echo $this->item->getTitle() ?></h3>	
	<div>
		<?php echo $this->item->content ?>
	</div>		
</div>
<?php else:?>
	<div class="tip"><span> <?php echo $this->translate("No item found.") ?> </span></div>
<?php endif;?>