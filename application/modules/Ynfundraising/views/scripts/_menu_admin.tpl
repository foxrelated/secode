<div class="headline">
  <h2>
    <?php echo $this->translate('Fundraising');?>
  </h2>
  <?php
  print_r($this->select);
	$navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynfundraising_admin_main', array (), $this->tab_select);
  if( count($navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>