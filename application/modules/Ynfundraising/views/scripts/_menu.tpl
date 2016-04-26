<div class="headline">
  <h2>
    <?php echo $this->translate('Fundraising');?>
  </h2>
  <?php 
	$navigation = Engine_Api::_()->getApi('menus', 'core')
      	->getNavigation('ynfundraising_main'); 
	
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $action = $request->getParam('action');
	if($action == 'create-step-one')
	{
		$navigation = Engine_Api::_()->getApi('menus', 'core')
      		->getNavigation('ynfundraising_main', array(), 'ynfundraising_main_create'); 
	}
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