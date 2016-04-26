<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js'); ?>
<?php include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; ?>
<script type="text/javascript">

  var welcomemixinfo_likes = function(resource_id, resource_type) {
		var content_type ='welcomemixinfo';
		// SENDING REQUEST TO AJAX
		var request = en4.sitelike.do_like.createLike(resource_id, resource_type,content_type);
		// RESPONCE FROM AJAX
		request.addEvent('complete', function(responseJSON) {
			if(responseJSON.like_id ) {
				$(resource_type + '_welcomemixinfolike_'+ resource_id).value = responseJSON.like_id;
				$(resource_type + '_welcomemixinfo_most_likes_'+ resource_id).style.display = 'none';
				$(resource_type + '_welcomemixinfo_unlikes_'+ resource_id).style.display = 'block';
				$(resource_type +'_welcomemixinfo_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
			} else {
					$(resource_type + '_welcomemixinfolike_'+ resource_id).value = 0;
					$(resource_type + '_welcomemixinfo_most_likes_'+ resource_id).style.display = 'block';
					$(resource_type + '_welcomemixinfo_unlikes_'+ resource_id).style.display = 'none';
					$(resource_type + '_welcomemixinfo_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
			}
		});
  }
</script>

<div class="adv_activity_welcome">
	<div class="adv_activity_welcome_num">
	</div>
	<div class="adv_activity_welcome_cont">
		<div class="adv_activity_welcome_cont_title">
		<?php echo $this->translate('Most Liked Items'); ?>
	</div>
	<div class="adv_activity_welcome_cont_des">
	<?php //if (empty($this->ajaxrequest)) :?>
	<ul  id="welcomemixinfo_dyanamic_code" class="layout_seaocore_sidebar_tabbed_widget layout_sitelike_list_like_widget">
		<li class="sitelike_tabs_content">
	  <ul id="welcomemixinfo_global_content">
<?php //endif; ?>
	<?php if( $this->likesetting['view_layout'] != 1) { ?>
		<div class="jq-checkpoints">
		<ul style="padding:0;">
	<?php } ?>
  <?php if( count($this->mix_object) > 0  ) { ?>
		<?php
			foreach ($this->mix_object as $row_mix_fetch) {
				$show_like = 0;
        $item=$row_mix_fetch['object'][0];
				if (!empty($item)) {
					switch ($row_mix_fetch['type'])	{
						//Display group.
						case 'group':
							$id = 'group_id';
							$moduleName = 'group';
							$titleModule = "Join Group";
							$title = "View Group";
							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->getTitle()));
							$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle()));
							if( $this->viewer()->getIdentity() ):
								if(!$item->membership()->isMember($this->viewer(), null) ):
									$like_state = $this->htmlLink(array('route' => $moduleName."_extended", 'controller' => 'member', 'action' => 'join', "$id" => $item->getIdentity()), $this->translate("$titleModule"), array('class' => 'buttonlink smoothbox icon_'.$moduleName.'_join', 'style' => 'clear:both;', 'title' => $item->getTitle() )) ;
								else :
									$like_state =  $this->htmlLink($item->getHref(),$this->translate("$title"), array('title' => $item->getTitle(), 'class' => 'buttonlink icon_type_group_likes', 'title' => $item->getTitle()));
								endif;
							endif;
					  break;

 						//Display group.
 						case 'group_photo':
							$id = 'photo_id' ;
							$module_id = 'group_id';
							$title = "View Group Photo";
							$moduleName = 'group';
							$title_groupphoto = $item->getTitle();
							if( empty($title_groupphoto) ) {
								$title_groupphoto = Engine_Api::_()->getItem("$moduleName", $item->$module_id)->getTitle() . '\'s photo';
							}
							$truncatetitle = Engine_String::strlen($title_groupphoto) > 15 ? Engine_String::substr($title_groupphoto, 0, 15) . '..' : $title_groupphoto;
							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $title_groupphoto));
							$thumb_title = $this->htmlLink($item->getHref(),$truncatetitle, array('title' => $title_groupphoto));
							$like_state = $this->htmlLink($item->getHref(),$this->translate("$title"), array('class' => "buttonlink $moduleName", 'title' => $title_groupphoto));
 						break;

							// Display blog.
						case 'blog':
							$id = 'blog_id' ;
							$title = "View Blog";
							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon', $item->getOwner()->username), array('title' => $item->getTitle()));
							$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle()));
							$like_state = $this->htmlLink($item->getHref(), $this->translate("$title"), array('class' => "buttonlink icon_type_blog_likes", 'title' => $item->getTitle()));
						break;

						// Display Event.
						case 'event':
							$id = 'event_id';
							$moduleName = 'event';
							$titleModule = "Join Event";
							$title = "View Event";
							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->getTitle()));
							$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle()));
							if( $this->viewer()->getIdentity() ):
								if(!$item->membership()->isMember($this->viewer(), null) ):
									$like_state = $this->htmlLink(array('route' => $moduleName."_extended", 'controller' => 'member', 'action' => 'join', "$id" => $item->getIdentity()), $this->translate("$titleModule"), array('class' => 'buttonlink smoothbox icon_'.$moduleName.'_join', 'style' => 'clear:both;', 'title' => $item->getTitle() )) ;
								else :
									$like_state =  $this->htmlLink($item->getHref(),$this->translate("$title"), array('title' => $item->getTitle(), 'class' => 'buttonlink icon_type_album_likes', 'title' => $item->getTitle()));
								endif;
							endif;
						break;

 						//Display group.
 						case 'event_photo':
							$id = 'photo_id' ;
							$module_id = 'event_id';
							$title = "View Event Photo";
							$moduleName = 'event';
							$title_groupphoto = $item->getTitle();
							if( empty($title_groupphoto) ) {
								$title_groupphoto = Engine_Api::_()->getItem("$moduleName", $item->$module_id)->getTitle() . '\'s photo';
							}
							$truncatetitle = Engine_String::strlen($title_groupphoto) > 15 ? Engine_String::substr($title_groupphoto, 0, 15) . '..' : $title_groupphoto;
							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $title_groupphoto));
							$thumb_title = $this->htmlLink($item->getHref(),$truncatetitle, array('title' => $title_groupphoto));
							$like_state = $this->htmlLink($item->getHref(),$this->translate("$title"), array('class' => "buttonlink $moduleName", 'title' => $title_groupphoto));
 						break;

						default:
							$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
							$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $row_mix_fetch['type']));
              $viewtitle =  "View ".$sub_status_select->title_items;
							if ($viewtitle == 'View Member') {
								$title = '';  
							} else {
							  $title = "View ".$sub_status_select->title_items; 
							}
              $icon='icon_type_'.strtolower($item->getModuleName()).'_likes'.'  item_icon_'.$row_mix_fetch['type'].' ';
							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $item->getTitle()));
							$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle()));
							$like_state = $this->htmlLink($item->getHref(), $this->translate("$title"), array('class' => "buttonlink $icon", 'title' => $item->getTitle()));
						break;
					}
				}
				$module_type = $row_mix_fetch['type'];
				$module_type_id = $item->getIdentity();
				if ($this->likesetting['view_layout'] == 1) {
					if(!empty($item))	{
						$show_like = 1;
						?>
						<li>
							<div class='sitelike_thumb'>
								<?php echo $thumb_photo; ?>
							</div>
							<div class="sitelike_info">
								<div class="sitelike_title">
									<?php	echo $thumb_title; ?>
								</div>
								<div class="sitelike_stats">
									<?php	echo $like_state;	?>
								</div>
								<div class='sitelike_stats' style="clear:both;" id = "<?php echo 	$module_type;?>_welcomemixinfo_num_of_like_<?php echo $module_type_id ;?>">
									<?php  echo $this->translate(array('%s like', '%s likes', $row_mix_fetch['limit']), $this->locale()->toNumber($row_mix_fetch['limit'])); ?>
								</div>
						<?php
					}
				}
				if(!empty($show_like) && $this->likesetting['view_layout'] == 1) {
					if(!empty($item))	{
						$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($module_type,$module_type_id);
						
						if (!empty($like_ids[0]['like_id']))	{
							$unlike_show = "display:block";
							$like_show = "display:none";
							$like_id = $like_ids[0]['like_id'];
						} else	{
							$unlike_show = "display:none;";
							$like_show = "display:block;";
							$like_id = 0;
						}
					?>
					<?php
						if(!empty( $this->like_setting_button)) {
							$like_setting_button = $unlike_show;
						} else {
								$like_setting_button = "display:none;";
						}
					?>
					<?php if(!empty($this->viewer_id)) {  ?>
						<div class="sitelike_button" id= "<?php echo $module_type ?>_welcomemixinfo_unlikes_<?php echo $module_type_id;?>" style ='<?php echo $like_setting_button;?>' >
							<a href = "javascript:void(0);" onclick = "welcomemixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>' )">
								<i class="like_thumbdown_icon"></i>
								<span><?php echo $this->translate('Unlike') ?></span>
							</a>
						</div>
						<div class="sitelike_button" id= "<?php echo $module_type ?>_welcomemixinfo_most_likes_<?php echo $module_type_id;?>" style ='<?php echo $like_show;?>'>
							<a href = "javascript:void(0);" onclick = "welcomemixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type; ?>' )">
								<i class="like_thumbup_icon"></i>
								<span><?php echo $this->translate('Like') ?></span>
							</a>
						</div>
					<?php } ?>
		</div>
	</li>
	<input type ="hidden" id = "<?php echo $module_type;?>_welcomemixinfolike_<?php echo $module_type_id;?>" value = "<?php echo $like_id; ?>"  />
  <?php } ?>
	<?php }
		else	{
			echo '<li class="likes_view_thumb">';
				echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $item->getTitle()));  ?>
				<!--tooltip start here-->
				<div class="jq-checkpointSubhead">
					<div class="sitelikes_tooltip_content_outer">
						<div class="sitelikes_tooltip_content_inner">
							<div class="tooltip_arrow">
								<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/tooltip_arrow_top.png' alt="" />
							</div>
							<div class="tooltip_sitelike_title">
								<?php	echo $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle()));  ?>
							</div>
							<div class='tooltip_sitelikes_stat' style="clear:both;" id = "<?php echo $module_type;?>_welcomemixinfo_num_of_like_<?php echo $module_type_id ;?>">
								<?php  echo $this->translate(array('%s like', '%s likes', $row_mix_fetch['limit']), $this->locale()->toNumber($row_mix_fetch['limit']));  ?>
							</div>
							<?php
								if(!empty($item))	{
									$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($row_mix_fetch['type'],$item->$id);
									
									if (!empty($like_ids[0]['like_id']))	{
										$unlike_show = "display:block";
										$like_show = "display:none";
										$like_id = $like_ids[0]['like_id'];
									} else	{
										$unlike_show = "display:none;";
										$like_show = "display:block;";
										$like_id = 0;
									}
							?>
							<?php
								if(!empty( $this->like_setting_button)) {
									$like_setting_button = $unlike_show;
								} else {
									$like_setting_button = "display:none;";
								}
							?>
							<?php if(!empty($this->viewer_id)) {  ?>
								<div class="sitelike_button" id= "<?php echo $module_type ?>_welcomemixinfo_unlikes_<?php echo $module_type_id;?>" style ='<?php echo $like_setting_button;?>' >
									<a href = "javascript:void(0);" onclick = "welcomemixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>' )">
										<i class="like_thumbdown_icon"></i>
										<span><?php echo $this->translate('Unlike') ?></span>
									</a>
								</div>
								<div class="sitelike_button" id= "<?php echo $module_type ?>_welcomemixinfo_most_likes_<?php echo $module_type_id;?>" style ='<?php echo $like_show;?>'>
									<a href = "javascript:void(0);" onclick = "welcomemixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type; ?>' )">
										<i class="like_thumbup_icon"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
								</div>
							<?php } ?>
							<input type ="hidden" id = "<?php echo $module_type;?>_welcomemixinfolike_<?php echo $module_type_id;?>" value = "<?php echo $like_id;  ?>"  />
							<?php } ?>
						</div>
					</div>
				</div>
				<!--tooltip end here-->
			<?php echo '</li>';
		} ?>
<?php } ?>
<?php } else { ?>
		<div class="tip" style="margin:2px;"><span style="margin:0px;"><?php echo $this->translate('No entry could be found.') ?></span></div>
  <?php } ?>
  <?php if( $this->likesetting['view_layout'] != 1) { ?>
		</ul></div>
  <?php } ?>
  <div style="clear:both;height:0;font-size:0;"></div>
	</li>
	</ul>
		</div>
	</div>
</div>

