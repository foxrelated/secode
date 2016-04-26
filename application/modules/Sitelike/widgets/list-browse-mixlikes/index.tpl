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

<?php if($this->loaded_by_ajax): ?>
  <script type="text/javascript">
    var params = {
      requestParams :<?php echo json_encode($this->params) ?>,
      responseContainer :$$('.layout_sitelike_list_browse_mixlikes')
    }
    en4.sitelike.ajaxTab.attachEvent('<?php echo $this->identity ?>',params);
  </script>
<?php endif;?>
<?php if($this->showContent): ?> 
<script type="text/javascript">
	var active_tab = '<?php echo $this->active_tab;?>';
	var applikepage = <?php echo sprintf('%d', $this->current_page) ?>;
	var url_browsemixinfo = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;

	var browse_mixinfo_likes = function(resource_id, resource_type) {
	var content_type = 'browsemixinfo';
		//SENDING REQUEST TO AJAX
		var request = en4.sitelike.do_like.createLike(resource_id, resource_type,content_type);
		//RESPONCE FROM AJAX
		request.addEvent('complete', function(responseJSON) {
			if(responseJSON.like_id )
			{
				$(resource_type + '_browselike_'+ resource_id).value = responseJSON.like_id;
				$(resource_type + '_browsemost_likes_'+ resource_id).style.display = 'none';
				$(resource_type + '_browseunlikes_'+ resource_id).style.display = 'block';
				$(resource_type +'_browsenum_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
			}
			else
			{
				$(resource_type +'_browselike_'+ resource_id).value = 0;
				$(resource_type +'_browsemost_likes_'+ resource_id).style.display = 'block';
				$(resource_type +'_browseunlikes_'+ resource_id).style.display = 'none';
				$(resource_type +'_browsenum_of_like_'+ resource_id).innerHTML = responseJSON.num_of_like;
			}
		});
	}
</script>

<a id="dynamic_app_like_anchor" class="pabsolute"></a>
<?php //if (empty($this->ajaxrequest)) :?>
  <div id="mixinfo_dyanamic_code" class="layout_sitelike_browse_list_like_widget layout_core_container_tabs">
    <div class="tabs_alt tabs_parent">
			<ul id="main_tabs">
				<?php if ($this->active_tab == 1) {  ?>
					<li class = 'active'>
					<a href="javascript:void(0)" id = 'browse_mixinfo_likes_tab1' onclick="javascript:show_browse_mixinfo(1);">
				<?php } else { ?>
					<li class = ''>
					<a href="javascript:void(0)" id = 'browse_mixinfo_likes_tab1' onclick="javascript:show_browse_mixinfo(1);">
				<?php } ?>
				<?php  echo $this->translate('Recent'); ?>
				</a>
				</li>

				<?php if ($this->active_tab == 2) {  ?>
					<li class = 'active' >
					<a href="javascript:void(0)" id = 'browse_mixinfo_likes_tab2' onclick="javascript:show_browse_mixinfo(2);">
				<?php } else { ?>
					<li class = '' id = 'browse_mixinfo_likes_tab2' onclick="javascript:show_browse_mixinfo(2);">
					<a href="javascript:void(0)" id = 'browse_mixinfo_likes_tab2' onclick="javascript:show_browse_mixinfo(2);">
				<?php } ?>
				<?php echo $this->translate("Most Popular "); ?>
				</a>
				</li>

				<?php  if ($this->active_tab == 3) { ?>
					<li class = 'active'>
					<a href="javascript:void(0)" id = 'browse_mixinfo_likes_tab3' onclick="javascript:show_browse_mixinfo(3);">
				<?php } else {  ?>
					<li class = ''>
					<a href="javascript:void(0)" id = 'browse_mixinfo_likes_tab3' onclick="javascript:show_browse_mixinfo(3);">
				<?php }?>
				<?php echo $this->translate('Random'); ?>
				</a>
				</li>
			</ul>
    </div>
    <ul id="browse_mixinfo_global_content">
    <?php //endif; ?>
    <?php if( count($this->mix_object) > 0 ) { ?>
      <?php 
        $infocount = 1;
	      foreach ($this->mix_object as $row_mix_fetch)	{
					$show_like = 0;
        $item = $row_mix_fetch['object'][0];
          if ($infocount < $this->pagelimit) {
						switch ($row_mix_fetch['type'])	{
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
							  $column_array = array('title_items', 'resource_id');
							  $getResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getResults(array('resource_type' => $row_mix_fetch['type'], 'column_name' => $column_array));
								$module_id = $getResults[0]['resource_id'];
								$viewtitle =  "View ".$getResults[0]['title_items'];
								
 								if ($viewtitle == 'View Member') { $view_title = '';  } else { $view_title = "View ".$getResults[0]['title_items']; }
                if ($row_mix_fetch['type'] == 'user') {
									$icon = '';
									$module_class = "";
                } else  {
									$icon = 'icon_type_'.strtolower($item->getModuleName()).'_likes'.'  item_icon_'.$row_mix_fetch['type'].' ';
									$module_class = "buttonlink $icon";
                }
							break;
						}
						include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/browseMixWidget.tpl' ;

						if(!empty($show_like)) {
							if(!empty($item))	{
								$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($module_type,$module_type_id);
								if($like_ids) {
									if (!empty($like_ids[0]['like_id']))	{
										$unlike_show = "display:block";
									}	else	{
										$unlike_show = "display:none;";
									}
									?>
									<?php
										if(!empty( $this->like_setting_button))	{
											$like_setting_button = $unlike_show;
										}	else	{
											$like_setting_button = "display:none;";
										}
									?>
									<?php if(!empty($this->viewer_id)): ?>
										<div class="sitelike_button" id= "<?php echo $module_type ?>_browseunlikes_<?php echo $module_type_id;?>" style ='<?php echo $like_setting_button;?>' >
											<a href = "javascript:void(0);" onclick = "browse_mixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type ?>' )">
												<i class="like_thumbdown_icon"></i>
												<span><?php echo $this->translate('Unlike') ?></span>
											</a>
										</div>
										<div class="sitelike_button" id= "<?php echo $module_type ?>_browsemost_likes_<?php echo $module_type_id;?>" style ='display:<?php echo $like_ids[0]["like_id"] ?"none":"block"?>' >
											<a href = "javascript:void(0);" onclick = "browse_mixinfo_likes('<?php echo $module_type_id; ?>', '<?php echo $module_type; ?>' )">
												<i class="like_thumbup_icon"></i>
												<span><?php echo $this->translate('Like');  ?></span>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</li>
							<input type ="hidden" id = "<?php echo $module_type;?>_browselike_<?php echo $module_type_id;?>" value = "<?php echo $like_ids[0]['like_id'] ? $like_ids[0]['like_id'] :0; ?>"  />
				<?php } } ?>
			<?php }
					}
					$infocount++; ?>
	<?php } ?>
<?php } else {  ?>
   <div class="tip"><span><?php echo $this->translate('No items could be found.') ?></span></div>
<?php } ?>
</ul>
</div>
<?php endif; ?>