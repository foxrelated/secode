<div class="generic_layout_container layout_top">
<?php echo $this->content()->renderWidget('ynaffiliate.main-menu') ?>
</div>
<div class="generic_layout_container layout_main">
	<div class="generic_layout_container layout_right">
		
	</div>
	<div class="generic_layout_container layout_middle">
		<?php if($this->success): ?>
			<?php echo $this->translate('Congratulations! You have become an affiliate.')  ?>
		<?php else: ?>
			<?php echo $this->form->render($this) ?>
		<?php endif; ?>
	</div>	
</div>

