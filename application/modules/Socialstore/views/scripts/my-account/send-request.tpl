<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu')
?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
	<?php if($this->store->ownerCanRequest()):  ?>
		<?php echo $this->form->render($this) ?>
	<?php else: ?>
		<div class="tip">
			<span>
		<?php echo $this->translate("You need to have at least %s to make request. Your current available amount is not enough to make request.", $this->currency($this->store->getMinRequestAmount())) ?>
			</span>
		</div>
	<?php endif; ?>
</div>
