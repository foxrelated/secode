<h2>
  <?php echo $this->translate("Shoutbox Plugin") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='settings'>
  <?php echo $this->form->render($this); ?>
</div>