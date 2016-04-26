<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: connectionsetting.tpl 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="headline">
	<h2><?php echo $this->translate('Connection Settings');?></h2>
	<div class='tabs'>
	  <?php
	    // Render the menu
	    echo $this->navigation()
	      ->menu()
	      ->setContainer($this->navigation)
	      ->render();
	  ?>
	</div>  
</div>
<div class='layout_right'></div>
<div class='layout_middle'>
	<div class="usersetting">
		<?php  echo $this->form->render($this) ?>
	</div>
</div>