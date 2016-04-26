
<div class="settings">
	<?php if($this->success ): ?>
		<?php echo $this->translate("Congratulations! You are now a member of our Affiliate network!") ?>
	<?php else: ?>
  		<?php echo $this->form->render($this) ?>
  <?php endif; ?>
</div>