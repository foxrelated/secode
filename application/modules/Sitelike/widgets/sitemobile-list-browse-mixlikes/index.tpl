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
<?php $this->headScriptSM()->prependFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/sitemobile/core.js'); ?>


<div id="mixinfo_dyanamic_code" class="sm-content-list">
  <?php if( count($this->mix_object) > 0 ): $infocount = 1;?>
		<ul id="browse_mixinfo_global_content"  data-role="listview" data-icon="none">
			<?php foreach ($this->mix_object as $row_mix_fetch)	:?>
				<?php $show_like = 0;$item = $row_mix_fetch['object'][0];?>
				<?php if ($infocount < $this->pagelimit):?>
				<?php switch ($row_mix_fetch['type'])	{
							case 'group':
									$module_id = 'group_id';
									$module_name = 'group';
									$module_title = 'Join Group';
									$module_class = 'icon_group_join';
									$view_title = 'View Group';
							break;
							case 'group_photo':
									$module_id = 'photo_id';
									$module_name = 'group';
									$id = 'group_id';
									$module_class = 'icon_type_photo_likes';
									$view_title = 'View Group Photo';
							break;
							case 'event':
									$module_id = 'event_id';
									$module_name = 'event';
									$module_title = 'Join Event';
									$module_class = 'icon_event_join';
									$view_title = 'View Event';
							break;
							case 'event_photo':
									$module_id = 'photo_id';
									$module_name = 'event';
									$id = 'event_id';
									$module_class = 'icon_type_photo_likes';
									$view_title = 'View Event Photo';
							break;
							case 'album_photo':
									$module_id = 'photo_id';
									$module_name = 'album';
									$id = 'album_id';
									$module_class = 'icon_type_album_likes';
									$view_title = 'View Album Photo';
							break;
							case 'sitepage_photo':
									$module_id = 'photo_id';
									$module_name = 'sitepage_album';
									$id = 'album_id';
									$module_class = 'icon_type_album_likes';
									$view_title = 'View Page Album Photo';
							break;
							default:
								$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
								$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $row_mix_fetch['type']));
								$module_id = $sub_status_select->resource_id;
								$viewtitle =  "View ".$sub_status_select->title_items;
								if ($viewtitle == 'View Member') { $view_title = '';  } else { $view_title = "View ".$sub_status_select->title_items; }
								if ($row_mix_fetch['type'] == 'user') {
									$icon = '';
									$module_class = "";
								} else  {
									$icon = 'icon_type_'.strtolower($item->getModuleName()).'_likes'.'  item_icon_'.$row_mix_fetch['type'].' ';
									$module_class = "buttonlink $icon";
								}
							break;
						}	
				?>
				
				<?php if (!empty($item)):?>
					<?php $show_like = 1;?>
					<?php $module_type = $row_mix_fetch['type'];?>
					<?php $module_type_id = $item->$module_id;?>
					<?php $itemTitle=$item->getTitle();?>
					<?php if (empty ($itemTitle) && substr($row_mix_fetch['type'], -6) == "_photo"):  ?>
						<?php 
							$parent=$item->getParent();
							$itemTitle= $parent->getTitle();
							if(empty ($itemTitle)):
								$parent=$parent->getParent();
								$itemTitle= $parent->getTitle();
							endif;
							$itemTitle=$this->translate("%s's photo", $itemTitle);
						?>
					<?php endif;?>

						<?php
              $liked_item = 0;
							$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($module_type,$module_type_id);
							if (!empty($like_ids[0]['like_id']))	{
								$unlike_show = "display:block";
								$like_show = "display:none";
								$like_id = $like_ids[0]['like_id'];
                $liked_item=1;
							}	else	{
								$unlike_show = "display:none;";
								$like_show = "display:block;";
								$like_id = 0;
                $liked_item=0;
							}
						?>
						<?php
							if(!empty( $this->like_setting_button))	{
								$like_setting_button = $unlike_show;
							}	else	{
								$like_setting_button = "display:none;";
							}

						?>
            
						<li data-inset="true" id="li_<?php echo $module_type;?>_like_<?php echo $module_type_id;?>" <?php if(!empty($this->viewer_id) && ($this->like_setting_button || $like_id == 0)) : ?> data-icon="<?php echo $like_id > 0 ? "thumbs-down": "thumbs-up"  ?>" <?php endif;?> >
							<a href="<?php echo $item->getHref() ?>">
                <?php if ($item->getPhotoUrl()): ?>
                  <?php echo $this->itemPhoto($item, 'thumb.icon');?>
                <?php elseif(strstr($row_mix_fetch['type'], 'sitestore')):?>
                  <?php echo $this->itemPhoto($item, 'thumb.icon');?>
                <?php else:?>
                  <?php echo $this->itemPhoto($item->getOwner(), 'thumb.icon');?>
                <?php endif;?>
								<div class="ui-list-content">
									<?php $truncatetitle = Engine_String::strlen($itemTitle) > 17 ? Engine_String::substr($itemTitle, 0, 17) . '..' : $itemTitle ?>
									<h3><?php echo $this->translate($truncatetitle) ?></h3>
									<p id = "<?php echo $module_type; ?>_num_of_like_<?php echo $module_type_id; ?>">
										<?php echo $this->translate(array('%s like', '%s likes', $row_mix_fetch['limit']), $this->locale()->toNumber($row_mix_fetch['limit'])); ?>
									</p>
									<p>
										<?php
											if (($row_mix_fetch['type'] == 'event' || $row_mix_fetch['type'] == 'group') && ($this->filter != "past") && $this->viewer()->getIdentity() && !$item->membership()->isMember($this->viewer(), null)) :
												echo $this->translate("$module_title");
											else:
												echo $this->translate($view_title);
											endif;
											?>
									</p>
								</div>
							</a>
              <?php if(!empty($this->viewer_id) && ($this->like_setting_button || $like_id == 0)) :?>
								<a href = "javascript:void(0);" onclick="sm4.sitelike.do_like.createLike('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>', 'browsemixinfo' )"></a>
								<div class="ui-item-member-action">
									<p class="item-listlike-button">
										<?php if(!empty($this->viewer_id)) { ?>
											<a class="fright" href = "javascript:void(0);" onclick = "sm4.sitelike.do_like.createLike('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>', 'browsemixinfo' )" id= "<?php echo $module_type ?>_unlikes_<?php echo $module_type_id;?>" style ='<?php echo $like_setting_button;?>'>
												<i class="ui-icon ui-icon-thumbs-down"></i>
											</a>
											<a class="fright" href = "javascript:void(0);" onclick = "sm4.sitelike.do_like.createLike('<?php echo $module_type_id; ?>', '<?php echo $module_type; ?>', 'browsemixinfo' )" id= "<?php echo $module_type ?>_most_likes_<?php echo $module_type_id;?>" style ='<?php echo $like_show;?>'>
												<i class="ui-icon ui-icon-thumbs-up"></i>
											</a>
										<?php } ?>
									</p>
									<input type ="hidden" id = "<?php echo $module_type;?>_like_<?php echo $module_type_id;?>" value = "<?php echo $like_id; ?>"  />
								</div>
              <?php endif;?>
						</li>
				<?php endif;?>
       <?php endif;?>
			<?php $infocount++;endforeach;?>
		<ul>
  <?php else:?>
    <div class="tip"><span><?php echo $this->translate('No items could be found.') ?></span></div>
  <?php endif;?>
</div>