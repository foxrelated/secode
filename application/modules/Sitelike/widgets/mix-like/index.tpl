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
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; ?>
<script type="text/javascript">
  var active_tab = '<?php echo $this->active_tab;?>';
  var url_mixinfo = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
  function show_mixinfo (tab_show) {
    var active_tab_old = active_tab;
    if($('mixinfo_global_content'))
    {
      $('mixinfo_global_content').innerHTML = '';
      $('mixinfo_global_content').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/spinner.gif" /></center>';
    }
    $('mixinfo_likes_tab' + active_tab_old).erase('class');
    $('mixinfo_likes_tab' + tab_show).set('class', 'active');

   var request = new Request.HTML({
   'url' : url_mixinfo,
      'data' : {
        'format' : 'html',
        'task' : 'ajax',
        'tab_show' : tab_show,
        'isajax' : 1
      },
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       $('mixinfo_dyanamic_code').style.display = 'none';
       $('mixinfo_dyanamic_code').innerHTML = responseHTML;
       var mixinfo_content = $('mixinfo_dyanamic_code').getElement('.layout_sitelike_mix_like').innerHTML;

      $('mixinfo_dyanamic_code').getParent().innerHTML = mixinfo_content;
       $('mixinfo_dyanamic_code').style.display = 'block';
				update_tooltip ();
			}
				});

        request.send();

  }
  var mixinfo_likes = function(resource_id, resource_type) {
    var content_type ='mixinfo';

    // SENDING REQUEST TO AJAX
    var request = en4.sitelike.do_like.createLike(resource_id, resource_type,content_type);
    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
      if(responseJSON.like_id )
      {
        $(resource_type + '_mixinfolike_'+ resource_id).value = responseJSON.like_id;
        $(resource_type + '_mixinfo_most_likes_'+ resource_id).style.display = 'none';
        $(resource_type + '_mixinfo_unlikes_'+ resource_id).style.display = 'block';
        $(resource_type +'_mixinfo_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
      }
      else
      {
        $(resource_type +'_mixinfolike_'+ resource_id).value = 0;
        $(resource_type +'_mixinfo_most_likes_'+ resource_id).style.display = 'block';
        $(resource_type +'_mixinfo_unlikes_'+ resource_id).style.display = 'none';
        $(resource_type +'_mixinfo_num_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
      }
    });

}
</script>



<?php //if (empty($this->ajaxrequest)) :?>
<ul  id="mixinfo_dyanamic_code" class="layout_seaocore_sidebar_tabbed_widget layout_sitelike_list_like_widget">
  <li class="seaocore_tabs_alt">
		<ul>
			<?php if ($this->active_tab == 1) {  ?>
				<li class = 'active' id = 'mixinfo_likes_tab1' onclick="javascript:show_mixinfo(1);">
			<?php } else { ?>
				<li class = '' id = 'mixinfo_likes_tab1' onclick="javascript:show_mixinfo(1);">
			<?php }?>
			<?php
				//CONDITION IS CHECK FOR WHEN THE TWO OR MORE TAB SHOW THAN LINK ON TAB NAME
				if( $this->likesetting['tab1_show'] == 1 &&( $this->likesetting['tab2_show'] == 1 || $this->likesetting['tab3_show'] == 1)) {
					//PRINT FOR LINK
					echo "<a href='javascript:void(0);'>".$this->translate($this->likesetting['tab1_name'])."</a>";
				}
				//condition is check for when only one tab show than no link is show on tab name  but only tab name is show
				else if( $this->likesetting['tab2_show'] != 1 && $this->likesetting['tab3_show'] != 1) {
					//PRINT THE SIMPLE NAME SHOW ON THE BROWSER PAGE
					echo $this->translate($this->likesetting['tab1_name']);
				}
			?>
			</li>
			<?php if ($this->active_tab == 2) {  ?>
				<li class = 'active' id = 'mixinfo_likes_tab2' onclick="javascript:show_mixinfo(2);">
			<?php } else { ?>
				<li class = '' id = 'mixinfo_likes_tab2' onclick="javascript:show_mixinfo(2);">
			<?php }?>
			<?php
				//CONDITION IS CHECK FOR WHEN THE TWO OR MORE TAB SHOW THAN LINK ON TAB NAME
				if( $this->likesetting['tab2_show'] == 1 &&( $this->likesetting['tab3_show'] == 1 || $this->likesetting['tab1_show'] == 1)) {
					//PRINT FOR LINK
					echo "<a href='javascript:void(0);'>".$this->translate($this->likesetting['tab2_name'])."</a>";
				}
				//condition is check for when only one tab show than no link is shoe only name is show
				else if( $this->likesetting['tab3_show'] != 1 && $this->likesetting['tab1_show'] != 1) {
					//PRINT THE SIMPLE NAME SHOW ON THE BROWSER PAGE
					echo $this->translate($this->likesetting['tab2_name']);
				}
			?>
			</li>
			<?php  if ($this->active_tab == 3) { ?>
				<li class = 'active' id = 'mixinfo_likes_tab3' onclick="javascript:show_mixinfo(3);">
			<?php } else {  ?>
				<li class = '' id = 'mixinfo_likes_tab3' onclick="javascript:show_mixinfo(3);">
			<?php }?>
			<?php
				//CONDITION IS CHECK FOR WHEN THE TWO OR MORE TAB SHOW THAN LINK ON TAB NAME
				if( $this->likesetting['tab3_show'] == 1 &&( $this->likesetting['tab1_show'] == 1 || $this->likesetting['tab2_show'] == 1)) {
					//ECHO FOR LINK
					echo "<a href='javascript:void(0);'>".$this->translate($this->likesetting['tab3_name'])."</a>";
				}
				//condition is check for when only one tab show than no link is shoe only name is show
				else if( $this->likesetting['tab2_show'] != 1 && $this->likesetting['tab1_show'] != 1) {
					//PRINT THE SIMPLE NAME SHOW ON THE BROWSER PAGE
					echo $this->translate($this->likesetting['tab3_name']);
				}
			?>
			</li>
    </ul>
  </li>
  <li class="sitelike_tabs_content">
		<ul id="mixinfo_global_content">
<?php //endif; ?>
	<?php if( $this->likesetting['view_layout'] != 1) { ?>
		<div class="jq-checkpoints">
		<ul style="padding:0;">
	<?php } ?>
  <?php if( count($this->mix_object) > 0  ) { ?>
		<?php
			foreach ($this->mix_object as $row_mix_fetch) {
				$show_like = 0;
        $item = $row_mix_fetch['object'][0];
				if (!empty($item)) {

					switch ($row_mix_fetch['type'])	{
						//Display group.
						case 'group':
								$id = 'group_id';
								$moduleName = 'group';
								$titleModule = "Join Group";
								$title = "View Group";
								$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
								$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
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
 								$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $title_groupphoto, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
 								$thumb_title = $this->htmlLink($item->getHref(),$truncatetitle, array('title' => $title_groupphoto, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
 								$like_state = $this->htmlLink($item->getHref(),$this->translate("$title"), array('class' => "buttonlink $moduleName", 'title' => $title_groupphoto));
 						break;

							// Display blog.
						case 'blog':
								$id = 'blog_id' ;
								$title = "View Blog";
								$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon', $item->getOwner()->username), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
								$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
								$like_state = $this->htmlLink($item->getHref(), $this->translate("$title"), array('class' => "buttonlink icon_type_blog_likes", 'title' => $item->getTitle()));
						break;

						// Display Event.
						case 'event':
								$id = 'event_id';
								$moduleName = 'event';
								$titleModule = "Join Event";
								$title = "View Event";
								$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
								$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
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
 								$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $title_groupphoto, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
 								$thumb_title = $this->htmlLink($item->getHref(),$truncatetitle, array('title' => $title_groupphoto, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
 								$like_state = $this->htmlLink($item->getHref(),$this->translate("$title"), array('class' => "buttonlink $moduleName", 'title' => $title_groupphoto));
 						break;
						case 'user':
								$id = 'user_id' ;
								$title = "";
								$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon', $item->getOwner()->username), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
								$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
						$like_state = '';
						break;
						default:
							$getResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getResults(array('resource_type' => $row_mix_fetch['type'], 'column_name' => array('title_items')));
							$viewtitle =  "View ".$getResults[0]['title_items'];
							if ($viewtitle == 'View Member') { $title = '';  } else { $title = "View ".$getResults[0]['title_items']; }

              $icon='icon_type_'.strtolower($item->getModuleName()).'_likes'.'  item_icon_'.$row_mix_fetch['type'].' ';

							$thumb_photo = $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));

							$thumb_title = $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation( $item->getTitle()), array('title' => $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch['type'].' '.$item->getIdentity()));
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
								<?php	if(!empty($like_state))
								echo $like_state;	?>
							</div>
							<div class='sitelike_stats' style="clear:both;" id = "<?php echo $module_type;?>_mixinfo_num_of_like_<?php echo $module_type_id ;?>">
								<?php  echo $this->translate(array('%s like', '%s likes', $row_mix_fetch['limit']), $this->locale()->toNumber($row_mix_fetch['limit'])); ?>
							</div>
					<?php
					}
				}


				if(!empty($show_like) && $this->likesetting['view_layout'] == 1) {
					if(!empty($item))	{
						$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike( $module_type,$module_type_id);
						
						if (!empty($like_ids[0]['like_id']))	{
							$unlike_show = "display:block";
						} else	{
							$unlike_show = "display:none;";
						}

						if(!empty( $this->like_setting_button)) {
							$like_setting_button = $unlike_show;
						} else {
							$like_setting_button = "display:none;";
						}
					?>
					<?php if(!empty($this->viewer_id) && isset($like_ids[0])) : ?>
						<div class="sitelike_button" id= "<?php echo $module_type ?>_mixinfo_unlikes_<?php echo $module_type_id;?>" style ='<?php echo $like_setting_button;?>' >
							<a href = "javascript:void(0);" onclick = "mixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>' )">
								<i class="like_thumbdown_icon"></i>
								<span><?php echo $this->translate('Unlike') ?></span>
							</a>
						</div>
						<div class="sitelike_button" id= "<?php echo $module_type ?>_mixinfo_most_likes_<?php echo $module_type_id;?>" style ='display:<?php echo $like_ids[0]['like_id'] ?"none":"block"?>' >
							<a href = "javascript:void(0);" onclick = "mixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type; ?>' )">
								<i class="like_thumbup_icon"></i>
								<span><?php echo $this->translate('Like') ?></span>
							</a>
						</div>
						<input type ="hidden" id = "<?php echo $module_type;?>_mixinfolike_<?php echo $module_type_id;?>" value = '<?php echo $like_ids ? $like_ids[0]['like_id'] :0; ?>' />
					<?php endif;  ?>
		</div>
	</li>
	
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
							<div class='tooltip_sitelikes_stat' style="clear:both;" id = "<?php echo $module_type;?>_mixinfo_num_of_like_<?php echo $module_type_id ;?>">
								<?php  echo $this->translate(array('%s like', '%s likes', $row_mix_fetch['limit']), $this->locale()->toNumber($row_mix_fetch['limit']));  ?>
							</div>
							<?php
								if(!empty($item))	{
									$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($row_mix_fetch['type'],$module_type_id);
									
									if (!empty($like_ids[0]['like_id']))	{
										$unlike_show = "display:block";
									} else	{
										$unlike_show = "display:none;";
									}
									
									if(!empty( $this->like_setting_button)) {
										$like_setting_button = $unlike_show;
									} else {
										$like_setting_button = "display:none;";
									}
							?>
							<?php if(!empty($this->viewer_id) && isset($like_ids[0])) : ?>
								<div class="sitelike_button" id= "<?php echo $module_type ?>_mixinfo_unlikes_<?php echo $module_type_id;?>" style ='<?php echo $like_setting_button;?>' >
									<a href = "javascript:void(0);" onclick = "mixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>' )">
										<i class="like_thumbdown_icon"></i>
										<span><?php echo $this->translate('Unlike') ?></span>
									</a>
								</div>
								<div class="sitelike_button" id= "<?php echo $module_type ?>_mixinfo_most_likes_<?php echo $module_type_id;?>" style ='display:<?php echo $like_ids[0]['like_id'] ?"none":"block"?>' >
									<a href = "javascript:void(0);" onclick = "mixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type; ?>' )">
										<i class="like_thumbup_icon"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
								</div>
								<input type ="hidden" id = "<?php echo $module_type;?>_mixinfolike_<?php echo $module_type_id;?>" value = '<?php echo $like_ids ? $like_ids[0]['like_id'] :0; ?>'  />
							<?php endif; ?>
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
	</ul>
	</li>
	</ul>