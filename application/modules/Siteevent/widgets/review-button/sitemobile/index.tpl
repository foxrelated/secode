<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php if($this->createAllow == 1):?>
  <a href="<?php echo $this->url(array('action' => 'create', 'event_id' => $this->siteevent->event_id,'tab' => $this->tab), "siteevent_user_general", true);?>" data-role='button' data-icon="pencil" data-inset='false' data-corners='false' data-shadow='true'>
		<span><?php echo $this->translate('Write a Review') ?></span>
	</a>
<?php elseif($this->createAllow == 2):?>
	<a href="<?php echo $this->url(array('action' => 'update', 'event_id' => $this->siteevent->event_id, 'review_id' => $this->review_id,'tab' => $this->tab), "siteevent_user_general", true);?>" data-role='button' data-icon="pencil" data-inset='false' data-corners='false' data-shadow='true'>
		<span><?php echo $this->translate('Update your Review') ?></span>
	</a>
<?php endif;?>


