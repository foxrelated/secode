<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: friendlike.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitelike_members_popup">
	<div class="top">
		<div class="heading"><?php echo $this->translate("Group name's Like"); ?></div>
		<div class="sitelike_member_search_box">
			<div class="link">
	    	<a href="" class="selected" id="show_all"><?php echo $this->translate('Friends Like'); ?></a>
			</div>
			<div class="blog_members_search" style="float:right;">
	      <input type="text" value="" />
	    </div>
	    <div style="clear:both;height:0;"></div>
		</div>
	</div>	
	<div class="likes_popup_content">		
		<?php foreach( $this->user_obj as $user_info ) { ?>
			<div class="item_member">
				<div class="item_member_thumb">
					<?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon'), array('class' => 'item_photo'));?>
				</div>
				<div class="item_member_name">	
					<?php echo $this->htmlLink($user_info->getHref(), $user_info->getTitle(), array('title' => $user_info->getTitle())); ?>
				</div>
			</div>		
			<!--echo $this->userFriendship($user_info);-->
			<?php	} echo $this->paginationControl($this->user_obj);	?>
	</div>
</div>