<div class="headline">
  <h2>
    <?php echo $this->translate("Store");?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
      if ($this->count == 1) {
      	foreach ($this->navigation as $navi) {
      		if ($navi->controller == 'my-cart') {
      			$text = $this->translate('Item in Cart');
      			$navi->label = $this->count." ".$text;
      		}
      	}
      }
      elseif ($this->count > 1) {
      	foreach ($this->navigation as $navi) {
      		if ($navi->controller == 'my-cart') {
      			$text = $this->translate('Items in Cart');
      			$navi->label = $this->count." ".$text;
      		}
      	}		
	  }
      echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->setUlClass('navigation')
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

