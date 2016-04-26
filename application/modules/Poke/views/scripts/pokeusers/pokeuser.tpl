<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: pokeuser.tpl 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<?php if(empty($this->error)): ?>
	<?php echo $this->form->render($this) ?>
<?php elseif($this->error == 1): ?>
  <div class='settings'>
  	<?php echo $this->message ?>
  </div> 
<?php elseif($this->error == 2 ): ?>
  <div class='settings'>
  	<?php echo $this->message ?>
  </div>   
<?php endif; ?>

<script type="text/javascript">
function closesmoothbox() {
	parent.Smoothbox.close();
}
</script>