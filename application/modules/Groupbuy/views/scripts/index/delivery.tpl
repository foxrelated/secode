
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
<?php
if ($this->deal->current_sold >= $this->deal->max_sold || $this->deal->end_time < date("Y-m-d H:i:s")):?>
<div class="tip" style="clear: inherit;">
      <span>
<?php echo $this->translate('This deal is off!'); ?>
 </span>
           <div style="clear: both;"></div>
    </div>
 <?php else: ?>
<div>
<?php echo $this->form->render($this);?>
</div>
<?php endif; ?>