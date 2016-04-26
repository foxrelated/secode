<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form'>
  <form method="post" class="global_form">
  	<div>
      <div>
      	<h3><?php echo $this->translate('Delete Group Poll ?');?></h3>
      	<p>
        	<?php echo $this->translate('Are you sure that you want to delete the group poll titled "%1$s" ? It will not be recoverable after being deleted.', $this->grouppoll->title); ?>
      	</p>
      	<br />
      	<p>
        	<input type="hidden" name="confirm" value="true"/>
        	<button type='submit' style="color:#D12F19;"><?php echo $this->translate('Delete');?></button>
        	<?php echo $this->translate('or');?> <a href='<?php echo $this->url(array('id' => $this->group_id, 'tab' => $this->TAB_SELECTED_ID), 'group_profile', true) ?>'><?php echo $this->translate('cancel');?></a>
      	</p>
    	</div>
    </div>
  </form>
</div>