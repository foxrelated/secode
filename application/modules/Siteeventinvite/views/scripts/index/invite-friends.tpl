	<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
	
	
	
	
	
	<?php 
	$modName = 'user';
	$moduleId = 'user_id';
	$modItemType = 'user';
  if($this->siteevent->parent_type == 'user') {
    $popupHeaderTitle = $this->translate("Select friends of yours who might be interested in this event. These friends will get an invitation from you to join this event.");
    //$popupHeaderDiscription = $this->translate("Selected friends will get a invitation from you to join this event.");
  } else { 
    $shortType = Engine_Api::_()->getItem($this->siteevent->parent_type, $this->siteevent->parent_id)->getShortType();
    $popupHeaderTitle = $this->translate("Select members belonging to this %s who might be interested in this event", ucfirst($shortType));
    $popupHeaderDiscription = $this->translate("Selected members belonging to this %s will get an invitation from you to join this event.", ucfirst($shortType));
  }
  $this->headTranslate(array('Please select at-least one entry above to send invitation to.', 'Search Members', 'Selected', 'No more friends are available.', 'Sorry, no more friends.'));
// 	include_once(APPLICATION_PATH ."/application/modules/eventinvite/views/scripts/_showPopupContent.tpl");
?>

<script type="text/javascript">
	var action_module = "<?php echo $modName;  ?>";
	var action_session_id = "<?php echo $moduleId;  ?>";
	var select_text_flag = "<?php echo $this->translate("Selected"); ?>";
</script>
	
	
	
	
	<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventinvite/externals/scripts/core.js');
				$displayUserStr = '';
				$array = $this->suggest_user_id;
				if(!empty($array) && is_array($array)){ $displayUserStr = implode('::', $array); }
	?>

	<script type="text/javascript">
		var eventinvite_string = '';
    var occurrence_id = '<?php echo $this->occurrence_id;?>';
    var siteevent_id = '<?php echo $this->siteevent_id;?>';
		var show_selected = '<?php echo $this->show_selected;?>';
		var friends_count = '<?php echo $this->friends_count;?>';
		var eventinvite_string_temp = '<?php echo $this->selected_checkbox;?>';
		var tempSelectedFriend = '<?php echo $this->tempSelectedFriend; ?>';
		var memberSearch = '<?php echo $this->search ?>';
		var memberPage = <?php echo sprintf('%d', $this->members->getCurrentPageNumber()) ?>;
		var displayUserStr = '<?php echo $displayUserStr; ?>';
		var paginationArray = new Array();
		var SelectedPopupContent = new Array();
		var dontHaveResult = 1;
		var popupFlag = 0;
	</script>
<?php if ( !$this->search_true ) {?>
	<div class="seaocore_popup">
		<div class="seaocore_popup_top">
			<div class="seaocore_popup_title"><?php echo $popupHeaderTitle; ?></div>
			<div class="seaocore_popup_des"><?php echo $popupHeaderDiscription; ?></div>
		</div>
			
		<div class="seaocore_popup_options">
			<div class="seaocore_popup_options_left">
				<input class="searchbox" id="<?php echo $modName; ?>_members_search_inputd" type="text" value="<?php echo $this->translate("Search Members"); ?>"  onkeyup="en4.siteeventinvite.friends.show_searched_friends(0, event);" >
			</div>
			<div class="seaocore_popup_options_tbs">
				<?php if( empty($this->selectedFriendFlag) ): ?>
					<a href="javascript:void(0);" onclick="en4.siteeventinvite.friends.selectAllFriend('<?php echo $displayUserStr; ?>')" id="newcheckbox"><?php echo $this->translate('Select All'); ?></a>
				<?php endif; ?>
				<span class="fleft" style="margin:0 5px 0 40px;"><?php echo $this->translate('View: '); ?></span>
				<a href="javascript:void(0);" onClick='javascript:en4.siteeventinvite.friends.show_all();' class="selected" id="show_all"><?php echo $this->translate('All'); ?></a>
				<a href="javascript:void(0);" id="selected_friends" onclick="javascript:en4.siteeventinvite.friends.selected_friends();" class=""><?php echo $this->translate('Selected'); ?>(0) </a>
			</div>
		</div>
		<div id="main_box">
<?php } ?>


		<form name="eventinvite" method="POST" action="<?php echo $this->url(array('action' => 'sendinvite'));?>">
			<?php if( !empty($hiddenFieldName) ){ $modName = $hiddenFieldName; }?>
			<input type="hidden" name="occurrence_id" value="<?php echo $this->occurrence_id;  ?>" />
      <input type="hidden" name="siteevent_id" value="<?php echo $this->siteevent_id;  ?>" />
<!--			<input type="hidden" name="entity_id" value="<?php //echo $moduleId;  ?>" />
                        <input type="hidden" name="entity_title" value="<?php //echo $contentName; ?>" />
                        <input type="hidden" name="entity_link" value="<?php //echo $contentLink; ?>" />-->
			<div id="hidden_checkbox"> </div>
			<!--Member list start here-->
			<div class="seaocore_popup_content">
			<div class="seaocore_popup_content_inner">	
				<?php 
					$div_id = 1;
					$send_request_user_info_array = array();
					if ( !empty($this->mod_combind_path) ) {
						$dontHaveResult = 1;
						foreach( $this->suggest_user as $user_info ):
				      $member_info = $user_info;
              if (!isset($user_info->user_id))
                $member_info = Engine_Api::_()->getItem('user', $user_info->resource_id);
            
            ?>
				<div id="eventinvite_friend_<?php echo $div_id; ?>" class="seaocore_popup_items">
					<?php $allFriendId[] = $member_info->user_id; ?>
					<a class="eventinvite_pop_friend <?php if(!empty($this->show_selected)){ echo 'selected'; } ?>" id="check_<?php echo $member_info->user_id; ?>" href="javascript:void(0);" onclick="en4.siteeventinvite.friends.moduleSelect('<?php echo $member_info->user_id; ?>');" >
				<?php 
				$getPhotoUrl = $member_info->getPhotoUrl('thumb.icon'); 
				if( empty($getPhotoUrl) ) {
					$getPhotoUrl = $this->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
				}
				?>
						<span style="background-image: url(<?php echo $getPhotoUrl; ?>);">
							<span></span>
						</span>
						<p><?php	echo $member_info->getTitle(); ?></p>
					</a>
				</div>	
				<?php  
					$div_id++;
					endforeach;	
					} else { $dontHaveResult = 0; ?>
					<div class='tip' style="margin:10px">
						<span>
							<?php 
								if( !empty($this->mod_set_error) ){
									// This msg show when click on view page link.
									echo $this->translate('No Friends were found to whom you can make this invitation.');
								}
								else{ 
                  if($this->siteevent->parent_type == 'user') 
                    echo $this->translate('No friends were found to match your search criteria.');
                  else
                    echo $this->translate('No members belonging to this %s were found to match your search criteria.', ucfirst($shortType));
								}
							?>
						</span>
					</div>
				<?php } ?>	
			</div>
			</div>
			<!--Member list end here-->
			<div class="popup_btm">
				<div class="fleft">
					<div id="check_error"></div>
					<?php if( !empty($changeButtonText) ){ $buttonText = $changeButtonText; }else { $buttonText = $this->translate("Send Invitation"); } ?>
					<button type='button' onClick='javascript:en4.siteeventinvite.friends.doCheckAll();'><?php echo $buttonText; ?></button>
					<?php echo $this->translate("or"); ?>
					<a href="javascript:void(0);" onclick="cancelPopup();"><?php echo $this->translate("Cancel"); ?></a>
				</div>
				<?php if( $this->members->count() > 1 ): ?>
					<div class="pagination">
						<?php if( $this->members->getCurrentPageNumber() > 1 ): ?>
							<div id="user_mod_members_previous" class="paginator_previous" style="font-weight:bold;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate("&laquo; Prev"), array(
									'onclick' => 'en4.siteeventinvite.friends.paginateMembers(memberPage - 1);'
								)); ?>
							</div>
						<?php endif; ?>
						<?php if( $this->members->getCurrentPageNumber() < $this->members->count() ): ?>
							<div id="user_mod_members_next" class="paginator_next" style="font-weight:bold;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate("Next &raquo;") , array(
									'onclick' => 'en4.siteeventinvite.friends.paginateMembers(memberPage + 1);'
								)); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
			</div>	
		</form>  
	<?php if (!$this->search_true) {?>
	</div>
	</div>
	<?php } 


if( !empty($this->getArray) ) {
foreach ( $this->getArray as $key => $value ) {
?>
	<script type="text/javascript">
		paginationArray['<?php echo $key; ?>'] = '<?php echo $value; ?>';
		dontHaveResult = '<?php echo $dontHaveResult; ?>';
	</script>
<?php
} }
	?>