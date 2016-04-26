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
<div class="tip"><span> <?php echo $this->translate("No item found.") ?> </span></div>