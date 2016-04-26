	<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Suggestion/externals/scripts/core.js');
				$displayUserStr = '';
				$array = $this->suggest_user_id;
				if(!empty($array) && is_array($array)){ $displayUserStr = implode('::', $array); }
	?>

	<script type="text/javascript">
		var suggestion_string = '';
		var show_selected = '<?php echo $this->show_selected;?>';
		var friends_count = '<?php echo $this->friends_count;?>';
		var suggestion_string_temp = '<?php echo $this->selected_checkbox;?>';
		var tempSelectedFriend = '<?php echo $this->tempSelectedFriend; ?>';
		var memberSearch = '<?php echo $this->search ?>';
		var memberPage = <?php echo sprintf('%d', $this->members->getCurrentPageNumber()) ?>;
		var notification_type = '<?php echo $this->notification_type; ?>';
		var entity = '<?php echo $this->entity; ?>';
		var item_type = '<?php echo $this->item_type; ?>';
		var findFriendFunName = '<?php echo $this->findFriendFunName; ?>';
		var notificationType = '<?php echo $this->notificationType; ?>';
		var modError = '<?php echo $this->modError; ?>';
		var modName = '<?php echo $this->modName; ?>';
		var modItemType = '<?php echo $this->modItemType; ?>';
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
				<input class="searchbox" id="<?php echo $modName; ?>_members_search_inputd" type="text" value="<?php echo $this->translate("Search Members"); ?>"  onkeyup="show_searched_friends(0, event);" >
			</div>
			<div class="seaocore_popup_options_tbs">
				<?php if( empty($this->selectedFriendFlag) ): ?>
					<a href="javascript:void(0);" onclick="selectAllFriend('<?php echo $displayUserStr; ?>')" id="newcheckbox"><?php echo $this->translate('Select All'); ?></a>
				<?php endif; ?>
				<span class="fleft" style="margin:0 5px 0 40px;"><?php echo $this->translate('View: '); ?></span>
				<a href="javascript:void(0);" onClick='javascript:show_all();' class="selected" id="show_all"><?php echo $this->translate('All'); ?></a>
				<a href="javascript:void(0);" id="selected_friends" onclick="javascript:selected_friends();" class=""><?php echo $this->translate('Selected'); ?>(0) </a>
			</div>
		</div>
		<div id="main_box">
<?php } ?>


		<form name="suggestion" method="POST">
			<?php if( !empty($hiddenFieldName) ){ $modName = $hiddenFieldName; }?>
			<input type="hidden" name="entity" value="<?php echo $modName;  ?>" />
			<input type="hidden" name="entity_id" value="<?php echo $moduleId;  ?>" />
                        <input type="hidden" name="entity_title" value="<?php echo $contentName; ?>" />
                        <input type="hidden" name="entity_link" value="<?php echo $contentLink; ?>" />
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
				?>
				<div id="suggestion_friend_<?php echo $div_id; ?>" class="seaocore_popup_items">
					<?php $allFriendId[] = $user_info->user_id; ?>
					<a class="suggestion_pop_friend <?php if(!empty($this->show_selected)){ echo 'selected'; } ?>" id="check_<?php echo $user_info->user_id; ?>" href="javascript:void(0);" onclick="moduleSelect('<?php echo $user_info->user_id; ?>');" >
				<?php 
				$getPhotoUrl = $user_info->getPhotoUrl('thumb.icon'); 
				if( empty($getPhotoUrl) ) {
					$getPhotoUrl = $this->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
				}
				?>
						<span style="background-image: url(<?php echo $getPhotoUrl; ?>);">
							<span></span>
						</span>
						<p><?php	echo $user_info->getTitle(); ?></p>
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
									echo $this->translate('No Friends were found to whom you can make this suggestion.');
								}
								else{
									echo $this->translate('No friends were found to match your search criteria.');
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
					<?php if( !empty($changeButtonText) ){ $buttonText = $changeButtonText; }else { $buttonText = $this->translate("Send Suggestions"); } ?>
					<button type='button' onClick='javascript:doCheckAll();'><?php echo $buttonText; ?></button>
					<?php echo $this->translate("or"); ?>
					<a href="javascript:void(0);" onclick="cancelPopup();"><?php echo $this->translate("Cancel"); ?></a>
				</div>
				<?php if( $this->members->count() > 1 ): ?>
					<div class="pagination">
						<?php if( $this->members->getCurrentPageNumber() > 1 ): ?>
							<div id="user_mod_members_previous" class="paginator_previous" style="font-weight:bold;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate("&laquo; Prev"), array(
									'onclick' => 'paginateMembers(memberPage - 1);'
								)); ?>
							</div>
						<?php endif; ?>
						<?php if( $this->members->getCurrentPageNumber() < $this->members->count() ): ?>
							<div id="user_mod_members_next" class="paginator_next" style="font-weight:bold;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate("Next &raquo;") , array(
									'onclick' => 'paginateMembers(memberPage + 1);'
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