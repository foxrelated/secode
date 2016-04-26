<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _my-friends-likes.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js'); ?>
<?php 
  $flag = 0;
?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; ?>
 <script type="text/javascript">
function sendMessage(thisobj) {
        Smoothbox.open(thisobj.href);
        }
</script>
<?php if( !empty($this->enablemodules) ) { ?>
<?php if (empty($this->isajaxrequest)) { ?>
<div class="seaocore_db_tabs">
	<ul>
		<?php foreach ($this->enablemodules as $module) : ?>
		<?php $moduleTitle = null;
					$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
					$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $module));
					$moduleTitle = $sub_status_select->item_title;
		?>
		<?php  if (!empty($moduleTitle)) { ?>
			<li><a href="javascript:void(0);" onclick = "show_app_likes('<?php echo $module;?>', this);" class="<?php if ($this->activetab == $module) echo 'selected'; else '' ;?>" id="<?php echo $module . '_' . $module;?>"><?php echo $this->translate("$moduleTitle") ?></a></li>
		<?php } ?>
		<?php //else { ?>
		<!--				<li><a href="javascript:void(0);" onclick = "show_app_likes('<?php //echo $module;?>', this);" class="<?php //if ($this->activetab == $module) echo 'selected'; else '' ;?>" id="<?php //echo $module . '_' . $module;?>"><?php //$display = ucfirst($moduleTitle) . 's'; echo $this->translate($display);?></a></li>-->
		<?php //} ?>
		<?php endforeach;?>
	</ul>
</div>

<div class="sitelike_likes_content" id="dynamic_app_info">
<?php } ?>

<?php
  if (!empty ($this->paginator)) {
  if ($this->paginator->count() > 0) {

		$base_url =  Zend_Controller_Front::getInstance()->getBaseUrl();
		foreach ($this->paginator as $row_mix_fetch) { ?>

			<div class="list_items">
				<div class="item_photo">
				<?php
          $href = '';
          if ($row_mix_fetch->resource_type == 'sitepage_photo' || $row_mix_fetch->resource_type == 'sitepagenote_photo' || $row_mix_fetch->resource_type == 'list_photo' || $row_mix_fetch->resource_type == 'recipe_photo' || $row_mix_fetch->resource_type == 'group_photo' || $row_mix_fetch->resource_type == 'event_photo' || $row_mix_fetch->resource_type == 'album_photo' || $row_mix_fetch->resource_type == 'sitebusiness_photo' || $row_mix_fetch->resource_type == 'sitebusinessnote_photo' )
          {
            $href = $row_mix_fetch->getHref() . '/type/' . 'like_count'.'/urlaction/'. $this->urlAction; 
            if(SEA_DISPLAY_LIGHTBOX) {
              $flag = 1;
            } 
            else {
              if(SEA_LIKE_LIGHTBOX) {
                $flag = 1;
              }
            }
          }
					if ($row_mix_fetch->resource_type == 'blog') {
						echo $this->htmlLink($row_mix_fetch->getHref(), $this->itemPhoto($row_mix_fetch->getOwner(), 'thumb.icon'), array('title' => $row_mix_fetch->getTitle(), 'target' => '_blank', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch->resource_type.' '.$row_mix_fetch->resource_id));
					} 
         
          if( $flag ) {
            echo $this->htmlLink($row_mix_fetch->getHref(), $this->itemPhoto($row_mix_fetch, 'thumb.icon'), array('title'=>$row_mix_fetch->getTitle(), 'class'=>"thumbs_photo seao_common_add_tooltip_link", 'onclick' => "openSeaocoreLightBox('$href');return false;", 'rel'=> $row_mix_fetch->resource_type.' '.$row_mix_fetch->resource_id ));
          } else {
            echo $this->htmlLink($row_mix_fetch->getHref(), $this->itemPhoto($row_mix_fetch, 'thumb.icon'), array('title'=>$row_mix_fetch->getTitle(), 'target' => '_blank', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch->resource_type.' '.$row_mix_fetch->resource_id));
          } 
				?>
				</div>
				<div class="item_list_right">

            <?php if (!empty($show_like_button)) {
             	if (!empty($like_ids[0]['like_id'])) {
								$unlike_show = "display:block";
								$like_show = "display:none";
								$like_id = $like_ids[0]['like_id'];
	             	}
	            else {
	              $unlike_show = "display:none;";
					      $like_show = "display:block;";
					      $like_id = 0;
            	}
	 	     		?>
                  <?php
          if(!empty( $this->like_setting_button)) {
            $like_setting_button = $unlike_show;
          }
          else {
            $like_setting_button = "display:none;";
            }
        ?>
						<?php if(!empty($this->viewer_id)) { ?>
							<div class = "sitelike_button" id =  "my-friend_<?php echo $row_mix_fetch->resource_type;?>_unlikes_<?php echo $row_mix_fetch->resource_id;?>" style ='<?php echo $like_setting_button;?>' >
								<a href = "javascript:void(0);" onclick = "app_likes('<?php echo $row_mix_fetch->resource_id; ?>', '<?php echo $row_mix_fetch->resource_type; ?>', 'my-friend_<?php echo $row_mix_fetch->resource_type;?>');">
									<i class="like_thumbdown_icon"></i>
									<span><?php echo $this->translate('Unlike') ?></span>
								</a>
							</div>
							<div class = "sitelike_button" id =  "my-friend_<?php echo $row_mix_fetch->resource_type;?>_most_likes_<?php echo $row_mix_fetch->resource_id;?>" style ='<?php echo $like_show;?>'>
								<a href = "javascript:void(0);" onclick = "app_likes('<?php echo $row_mix_fetch->resource_id; ?>', '<?php echo $row_mix_fetch->resource_type; ?>', 'my-friend_<?php echo $row_mix_fetch->resource_type;?>');">
									<i class="like_thumbup_icon"></i>
									<span><?php echo $this->translate('Like') ?></span>
								</a>
							</div>
						<?php } ?>
						<input type ="hidden" id = "my-friend_<?php echo $row_mix_fetch->resource_type;?>_like_<?php echo $row_mix_fetch->resource_id;?>" value = '<?php echo $like_id; ?>' /> <?php } ?>

					<span class="sugg_friend">
						<?php 
							switch ($row_mix_fetch->resource_type) {

								case 'user':
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/friend/modContentId/' . $row_mix_fetch->resource_id . '/modError/1';
									$module_sugg = $this->member_sugg;
									$resourceType  = 'user';
								break;
								case 'group':
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/group/modContentId/' . $row_mix_fetch->resource_id . '/modError/1';
									$module_sugg = $this->group_sugg;
									$resourceType = 'group';
								break;
								case 'classified':
									$module_sugg = $this->classified_sugg;
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/classified/modContentId/' . $row_mix_fetch->resource_id . '/modError/1';
									$resourceType = 'classified';
								break;
								case 'video':
									$module_sugg = $this->video_sugg;
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/video/modContentId/' . $row_mix_fetch->resource_id . '/sugg_type/video';
									$resourceType = 'video';
								break;
								case 'blog':
									$module_sugg = $this->blog_sugg;
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/blog/modContentId/' . $row_mix_fetch->resource_id;
									$resourceType = 'blog';
								break;
								case 'music_playlist':
								$module_sugg = $this->music_sugg;
								$resourceType = 'music_playlist';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/music/modContentId/' . $row_mix_fetch->resource_id . '/sugg_type/music';
								break;
								case 'album':
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/album/modContentId/' . $row_mix_fetch->resource_id . '/sugg_type/album';
									$module_sugg = $this->album_sugg;
									$resourceType = 'album';
								break;
								case 'poll':
									$module_sugg = $this->poll_sugg;
									$resourceType = 'poll';
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/poll/modContentId/' . $row_mix_fetch->resource_id . '/sugg_type/poll';
								break;
								case 'sitepage_page':
									$module_sugg = $this->page_sugg;
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/sitepage/modContentId/' . $row_mix_fetch->resource_id;
									$resourceType = 'sitepage_page';
								break;
								case 'list_listing':
									$module_sugg = $this->list_sugg;
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/list/modContentId/' . $row_mix_fetch->resource_id;
									$resourceType = 'list_listing';
								break;
								case 'event':
									$module_sugg = $this->event_sugg;
									$resourceType = 'event';
									$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/event/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'forum_topic':
								$module_sugg = $this->forum_sugg;
								$resourceType = 'forum_topic';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/forum/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepagevideo_video':
								$module_sugg = $this->pagevideo_sugg;
								$resourceType = 'sitepagevideo_video';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_video/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepagenote_note':
								$module_sugg = $this->pagenote_sugg;
								$resourceType = 'sitepagenote_note';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_note/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepagepoll_poll':
								$module_sugg = $this->pagepoll_sugg;
								$resourceType = 'sitepagepoll_poll';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_poll/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepageevent_event':
								$module_sugg = $this->pageevent_sugg;
								$resourceType = 'sitepageevent_event';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_event/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepage_album':
								$module_sugg = $this->pagealbum_sugg;
								$resourceType = 'sitepage_album';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_album/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'recipe':
								$module_sugg = $this->recipe_sugg;
								$resourceType = 'recipe';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/recipe/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepagedocument_document':
								$module_sugg = $this->pagedocument_sugg;
								$resourceType = 'sitepagedocument_document';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_document/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepagemusic_playlist':
								$module_sugg = $this->pagemusic_sugg;
								$resourceType = 'sitepagemusic_playlist';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_music/modContentId/' . $row_mix_fetch->resource_id;
								break;
								case 'sitepagereview_review':
								$module_sugg = $this->pagereview_sugg;
								$resourceType = 'sitepagereview_review';
								$sugg_url = $base_url . '/suggestion/index/switch-popup/modName/page_review/modContentId/' . $row_mix_fetch->resource_id;
								break;
							}

							// Making a 'URL' for "Suggest to a friend" in the case of "All Modules".
							if ( !empty($module_sugg) && !empty($this->show_link_permition) && $resourceType ) {
								$sugg_url = $sugg_url;
								$sugg_label = '<a href="javascript:void(0);" onclick="Smoothbox.open (\'' . $sugg_url . '\');" class="buttonlink icon_type_suggestion_likes">' . $this->translate("Suggest to Friends") . '</a>';
							}	else {
								$sugg_label = '';
							}
							$show_message_link = '';
							$num_of_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($row_mix_fetch->resource_type, $row_mix_fetch->resource_id );
							if( !empty( $this->message_link_auth ) && ( $num_of_like > 1 ) ) {
								$show_message_link = '<a href="'.$this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'compose', 'resource_type' => $row_mix_fetch->resource_type, 'resource_id' => $this->viewer_id), 'default', true).'" onclick="sendMessage(this);return false;" class="buttonlink icon_type_message_likes right-link">' . $this->translate("Message All Who Like") . '</a>';
							}
						?>
						<?php echo $sugg_label; ?>
					</span>
					<span style="clear:both;float:right;margin-top:5px;">
						<?php echo $show_message_link; ?>
					</span>
				</div>
				<?php  $num_of_like = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($row_mix_fetch->resource_type, $row_mix_fetch->resource_id ); ?>
				<?php  $like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike( $row_mix_fetch->resource_type,$row_mix_fetch->resource_id ); ?>

					<div class="item_details">
						<div class="item_title">
				<?php //} ?>
                 <?php  //echo $this->htmlLink($row_mix_fetch->getHref(), $title1, array('title'=> $title1));
            // } else {
								//echo $this->htmlLink($row_mix_fetch->getHref(), $row_mix_fetch->getTitle(), array('title'=> $row_mix_fetch->getTitle() , 'target' => '_blank'));
             // }
             ?>
						<?php if( $flag ) {
							echo $this->htmlLink($row_mix_fetch->getHref(), $row_mix_fetch->getTitle(), array('title'=> $row_mix_fetch->getTitle(), 'class'=>"thumbs_photo seao_common_add_tooltip_link", 'onclick' => "openSeaocoreLightBox('$href');return false;", 'rel'=> $row_mix_fetch->resource_type.' '.$row_mix_fetch->resource_id ));	
						} else {		
						  echo $this->htmlLink($row_mix_fetch->getHref(), $row_mix_fetch->getTitle(), array('title'=> $row_mix_fetch->getTitle() , 'target' => '_blank', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $row_mix_fetch->resource_type.' '.$row_mix_fetch->resource_id));
						} ?>
					</div>
	      	<div class="item_stat" id = "my-friend_<?php echo $row_mix_fetch->resource_type;?>_num_of_like_<?php echo $row_mix_fetch->resource_id;?>">
	      	  <?php if ( !empty($this->action_type) ) {
               echo $this->htmlLink(array('route' => $this->action_type, 'resource_type' => $row_mix_fetch->resource_type, 'resource_id' => $row_mix_fetch->resource_id,'call_status' => 'public'), $this->translate(array('%s like', '%s likes', $num_of_like), $this->locale()->toNumber($num_of_like)), array('class' => 'likes_viewall_link', 'onclick' => 'showusers(this);return false;' , 'id' => 'likes_viewall_link_' . $row_mix_fetch->resource_id)) ;
              }	else {
                echo $this->translate(array('%s like', '%s likes', $num_of_like), $this->locale()->toNumber($num_of_like)) ;
              }
            ?>
					</div>
				</div>
			</div>
		<?php }
  } else { ?>
      <div class="tip" style="margin:10px 0 0 270px;"><span><?php echo $this->translate('No items could be found.') ?></span></div>
   <?php }
    } else { ?>
				<div class="tip"><span><?php echo $this->translate('No items could be found.') ?></span></div>
    	<?php } ?>

   <?php if( !empty($this->paginator) && $this->paginator->count() > 1 ): ?>
    <div class="paging">
      <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginateapplikes(applikepage - 1)',
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginateapplikes(applikepage + 1)',
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
<?php if (empty($this->isajaxrequest)) {?>
</div>
<?php } ?>
<?php } else {?>
  <div class="tip" style="margin:10px 0 0 270px;"><span><?php echo $this->translate('No items could be found.') ?></span></div>
<?php } ?>
<script type="text/javascript">
  function showusers (thisobj) {

    Smoothbox.open(thisobj.href);
    return false;
 }
</script>