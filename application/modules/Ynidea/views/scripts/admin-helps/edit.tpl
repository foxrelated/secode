<h2><?php echo $this->translate("Ideas Plugin") ?></h2>
  <?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />

<div class="clear">
	<div class="settings">
	<?php echo $this->form->render($this )?>
		
	</div>
</div>

