<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: friend_mycontent_likelist.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php  if(empty($this->is_ajax)) { ?>
<a id="like_members_profile" style="posituin:absolute;"></a>
<div class="seaocore_members_popup">
	<div class="top">
		<?php
			$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
			$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $this->resource_type));
			$title = $sub_status_select->title_items;
			if($this->call_status == 'public')	{
				$title = $this->translate('People Who Like This %s', $title);
			}	else	{
				$title = $this->translate('Friends Who Like This %s', $title);
			}
		?>
		<div class="heading"><?php echo $title; ?></div>
		<div class="seaocore_members_search_box">
			<div class="link">
	    	<a href="javascript:void(0);" class="<?php if($this->call_status == 'public') { echo 'selected'; } ?>" id="show_all" onclick="likedStatus('public');"><?php echo $this->translate('All '); ?>(<?php echo number_format($this->public_count); ?>)</a>
				<a href="javascript:void(0);" class="<?php if($this->call_status == 'friend') { echo 'selected'; } ?>" onclick="likedStatus('friend');"><?php echo $this->translate('Friends '); ?>(<?php echo number_format($this->friend_count); ?>)</a>
			</div>

			<div class="seaocore_members_search fright">
				<input id="like_members_search_input" type="text" value="<?php echo $this->search; ?>" onfocus="if(this.value=='')this.value='';" onblur="if(this.value=='')this.value='';"/>
			</div>
		</div>
	</div>
	<div class="seaocore_members_popup_content" id="likes_popup_content">
		<?php } ?>
    <?php if( !empty($this->user_obj) && $this->user_obj->count() > 1 ): ?>
				<?php if( $this->user_obj->getCurrentPageNumber() > 1 ): ?>
					<div class="seaocore_members_popup_paging">
						<div id="user_like_members_previous" class="paginator_previous">
							<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
								'onclick' => 'paginateLikeMembers(likeMemberPage - 1, call_status)'
							)); ?>
						</div>
					</div>
				<?php endif; ?>
			<?php  endif; ?>
		<?php $count_user = count($this->user_obj);
				if(!empty($count_user)) {
					foreach( $this->user_obj as $user_info ) { ?>
				<div class="item_member">
					<div class="item_member_thumb">
						<?php echo $this->htmlLink($user_info->getHref(), $this->itemPhoto($user_info, 'thumb.icon', $user_info->getTitle()), array('class' => 'item_photo seao_common_add_tooltip_link', 'target' => '_parent', 'title' => $user_info->getTitle(), 'rel'=> 'user'.' '.$user_info->getIdentity()));?>
					</div>
					<div class="item_member_details">
						<div class="item_member_name">
							<?php  $title1 = $user_info->getTitle(); ?>
							<?php  $truncatetitle = Engine_String::strlen($title1) > 20 ? Engine_String::substr($title1, 0, 20) . '..' : $title1?>
							<?php echo $this->htmlLink($user_info->getHref(), $truncatetitle, array('title' => $user_info->getTitle(), 'target' => '_parent', 'class' => 'seao_common_add_tooltip_link', 'rel'=> 'user'.' '.$user_info->getIdentity())); ?>
						</div>
					</div>	
				</div>
				<?php	}
			 } else { ?>
			<div class='tip' style="margin:10px 0 0 140px;"><span>
			 		<?php
			 			echo $this->no_result_msg;
			 		?>
			 </span></div>
			<?php } ?>
			<?php 
				if(!empty($this->user_obj) && $this->user_obj->count() > 1 ): ?>
					<?php if( $this->user_obj->getCurrentPageNumber() < $this->user_obj->count() ): ?>
						<div class="seaocore_members_popup_paging">
							<div id="user_like_members_next" class="paginator_next" style="border-top-width:1px;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
									'onclick' => 'paginateLikeMembers(likeMemberPage + 1, call_status)'
								)); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
<?php if(empty($this->is_ajax)) { ?>
	</div>
</div>
<div class="seaocore_members_popup_bottom">
	<button onclick="parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
</div>
<?php } ?>
<?php	include_once APPLICATION_PATH . '/application/modules/Sitelike/Api/likesettings.php'; ?>