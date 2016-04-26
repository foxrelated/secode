<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mutualfriend.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="seaocore_members_popup seaocore_members_popup_notbs">
	<div class="top">
		<div class="heading">
			<?php echo $this->translate("Mutual Friends"); ?>
		</div>
	</div>	
	<div class="seaocore_members_popup_content">
		<?php foreach ($this->friend_obj as $friend_info) { ?>
			<div class="item_member_list">
				<div class="item_member_thumb">
					<?php echo $this->htmlLink($friend_info->getHref(), $this->itemPhoto($friend_info, 'thumb.icon'), array('target' => '_parent')); ?>
				</div>
				<div class="item_member_details">
					<div class="item_member_name">
						<?php echo $this->htmlLink($friend_info->getHref(), $friend_info->getTitle(), array('target' => '_parent', 'title' => $friend_info->getTitle())); ?>
					</div>		
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<div class="seaocore_members_popup_bottom">
	<button type='button' onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close"); ?></button>
</div>