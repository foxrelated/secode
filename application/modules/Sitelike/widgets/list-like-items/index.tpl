 <?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _classifideslikes.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitelike/externals/scripts/core.js'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php include APPLICATION_PATH . '/application/modules/Sitelike/views/scripts/settings_css.tpl' ; ?>
<?php $flag = 0; ?>
<?php  $id = $this->id; ?>
<script type="text/javascript">
 var url = '<?php  echo $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>';
</script>

<?php if (empty($this->ajaxrequest)) : ?>
<ul id="<?php echo $this->resource_type ?>_dyanamic_code" class="layout_seaocore_sidebar_tabbed_widget layout_sitelike_list_like_widget">
  <?php  if(!empty($this->likesetting['tab1_name']) || !empty($this->likesetting['tab2_name']) || !empty($this->likesetting['tab3_name'])) { ?>
  <li class="seaocore_tabs_alt">
    <ul>
			<?php  if(!empty($this->likesetting['tab1_entries']) && !empty($this->likesetting['tab1_name'])) { ?>
				<?php if ($this->active_tab == 1) {  ?>
					<li class = 'active' id = '<?php echo $this->resource_type ?>_likes_tab1' onclick="show_duration_likes('<?php echo $this->resource_type ?>_likes_tab' , 1, '<?php echo                        $this->resource_type ?>_global_content', '<?php echo $this->resource_type ?>')">
				<?php } else {  ?>
					<li class = '' id = '<?php echo $this->resource_type ?>_likes_tab1' onclick="show_duration_likes('<?php echo $this->resource_type ?>_likes_tab' , 1, '<?php echo                            $this->resource_type ?>_global_content', '<?php echo $this->resource_type ?>')">
				<?php }
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
			<?php } ?>

			<?php if(!empty($this->likesetting['tab2_entries']) && !empty($this->likesetting['tab2_name'])) { ?>
				<?php if ($this->active_tab == 2) { ?>
					<li class = 'active' id = '<?php echo $this->resource_type ?>_likes_tab2' onclick="show_duration_likes('<?php echo $this->resource_type ?>_likes_tab' , 2, '<?php echo                            $this->resource_type ?>_global_content', '<?php echo $this->resource_type ?>')">
				<?php } else { ?>
					<li class = '' id = '<?php echo $this->resource_type ?>_likes_tab2' onclick="show_duration_likes('<?php echo $this->resource_type ?>_likes_tab' , 2, '<?php echo                            $this->resource_type ?>_global_content', '<?php echo $this->resource_type ?>')">
					<?php }
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
			<?php } ?>

			<?php if(!empty($this->likesetting['tab3_entries']) && !empty($this->likesetting['tab3_name'])) {  ?>
				<?php  if ($this->active_tab == 3) {  ?>
					<li class = 'active' id = '<?php echo $this->resource_type ?>_likes_tab3' onclick="show_duration_likes('<?php echo $this->resource_type ?>_likes_tab' , 3, '<?php echo                            $this->resource_type ?>_global_content', '<?php echo $this->resource_type ?>')">
				<?php } else {  ?>
					<li class = '' id = '<?php echo $this->resource_type ?>_likes_tab3' onclick="show_duration_likes('<?php echo $this->resource_type ?>_likes_tab' , 3, '<?php echo                            $this->resource_type ?>_global_content', '<?php echo $this->resource_type ?>')">
					<?php }
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
			<?php } ?>
    </ul>
  </li>
<?php } ?>
<li class="sitelike_tabs_content">
	<ul id="<?php echo $this->resource_type ?>_global_content">
<?php endif; ?>

<?php if( $this->likesetting['view_layout'] != 1) { ?>
	<div class="jq-checkpoints">
		<ul style="padding:0;">
<?php } ?>
  <?php if( count($this->paginator) > 0  ) {  ?>
    <?php foreach($this->paginator as $item ):  ?>
      <?php
        $href = '';
        if ($this->resource_type == 'sitepage_photo' || $this->resource_type == 'sitepagenote_photo' || $this->resource_type == 'list_photo' || $this->resource_type == 'recipe_photo' || $this->resource_type == 'group_photo' || $this->resource_type == 'event_photo' || $this->resource_type == 'album_photo' || $this->resource_type == 'sitebusiness_photo' || $this->resource_type == 'sitebusinessnote_photo')
        {
          $count =  Engine_Api::_()->sitelike()->getTotalCount($this->resource_type);
          if($this->resource_type == 'sitepage_photo' || $this->resource_type == 'sitebusiness_photo' ) {
            $href = $item->getHref() . '/type/' . 'like_count'.'/count/' . $count.'/urlaction/likes';    
          } else {
            $href = $item->getHref() . '/type/' . 'like_count'.'/count/' . $count;  
          }
          if(SEA_DISPLAY_LIGHTBOX) {
            $flag = 1;
          } 
          else {
            if(SEA_LIKE_LIGHTBOX) {
              $flag = 1;
            }
          }
        }       
      ?>
      
      <?php 
        $likeCount = Engine_Api::_()->getApi('like', 'seaocore')->likeCount( $this->resource_type , $item->$id );
        
				$like_ids = Engine_Api::_()->getApi('like', 'seaocore')->hasLike( $this->resource_type ,$item->$id );
				if (!empty($like_ids[0]['like_id'])) {
					$unlike_show = "display:block";
				} else {
					$unlike_show = "display:none;";
				}

				if(!empty( $this->like_setting_button)) {
					$like_setting_button = $unlike_show;
				} else {
					$like_setting_button = "display:none;";
				}
			?>
      <?php 
				//HERE WE CAN CHECK WHEN THE VIEW LAYOUT VALUES IS ONE THEN THIS CAN SHOW IN LIST  FORMAT
				if( $this->likesetting['view_layout'] == 1) {
			?>
      <li>
				<div class='sitelike_thumb'>
					<?php
						if ($this->resource_type == 'blog' || $this->resource_type == 'forum_topic' || $this->resource_type == 'poll' || $this->resource_type == 'sitepagepoll_poll' )
						{
							echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('title'=>$item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id));
						}
				else  if($this->resource_type == 'document') {
       echo $this->htmlLink($item->getHref(), '<img src="'. $item->thumbnail .'" class="photo" />', array('title' => $item->document_title, 'class' => 'seao_common_add_tooltip_link',  'rel'=> $this->resource_type.' '.$item->$id) ) ; 
       }
				else  if($this->resource_type == 'sitepagedocument_document') {
          echo $this->htmlLink($item->getHref(), '<img src="'. $item->thumbnail .'" class="photo" />', array('title' => $item->sitepagedocument_title, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id) ) ; 
        }    
                
        elseif( $flag ) {
          echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title'=>$item->getTitle(), 'class'=>"thumbs_photo seao_common_add_tooltip_link", 'onclick' => "openSeaocoreLightBox('$href');return false;", 'rel'=> $this->resource_type.' '.$item->$id ));
        } else {
          echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title'=> $item->getTitle(),'onclick'=>'sitelikeAttachClickEvent($(this),"'.$this->duration.'"); return false;', 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id,));
        } 
        
        ?>
        </div>
				<div class='sitelike_info'>
					<div class='sitelike_title'>
						<?php if( $flag ) {
							echo $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation($item->getTitle()), array('title'=> $item->getTitle(), 'class'=>"thumbs_photo seao_common_add_tooltip_link", 'onclick' => "openSeaocoreLightBox('$href');return false;" , 'rel'=> $this->resource_type.' '.$item->$id ));
						} else {			
              echo $this->htmlLink($item->getHref(), Engine_Api::_()->sitelike()->turncation($item->getTitle()), array('title'=> $item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id));
						} ?>
				</div>
					<div class='sitelike_stats' id = "<?php echo $this->resource_type ?>_num_of_like_<?php echo $item->$id;?>">
						<?php 	echo $this->translate(array('%s like', '%s likes', $likeCount), $this->locale()->toNumber($likeCount));
						?>
					</div>
          <?php if(!empty($this->viewer_id)) {  ?>
          <div class="sitelike_button" id="<?php echo $this->resource_type ?>_unlikes_<?php echo $item->$id;?>" style ='<?php echo $like_setting_button;?>' >
            <a href = "javascript:void(0);" onclick = "app_likes('<?php echo $item->$id; ?>', '<?php echo $this->resource_type ?>', '<?php echo $this->resource_type ?>');">
              <i class="like_thumbdown_icon"></i>
              <span><?php echo $this->translate('Unlike');?></span>
            </a>
          </div>
          <?php if(isset($like_ids[0])): ?>
          <div class="sitelike_button" id="<?php echo $this->resource_type ?>_most_likes_<?php echo $item->$id;?>" style ='display:<?php echo $like_ids[0]["like_id"] ?"none":"block"?>' >
            <a href = "javascript:void(0);" onclick = "app_likes('<?php echo $item->$id; ?>', '<?php  echo $this->resource_type ?>', '<?php echo $this->resource_type ?>');">
              <i class="like_thumbup_icon"></i>
              <span><?php echo $this->translate('Like');  ?></span>
            </a>
          </div>
          <?php endif; ?>
					<?php } ?>
					<?php if(isset($like_ids[0])): ?>
					<input type ="hidden" id = "<?php echo $this->resource_type ?>_like_<?php echo $item->$id;?>" value = "<?php echo $like_ids[0]['like_id'] ? $like_ids[0]['like_id'] :0; ?>" />
					<?php endif; ?>
				</div>
      </li>
      <?php }
			//IN THIS CONDITION IS FAIL WHEN THIS IS SHOW IN THUMB MAIL FORMAT
			else
			{
				echo '<li class="likes_view_thumb">';
				if ($this->resource_type == 'blog' || $this->resource_type == 'forum_topic' || $this->resource_type == 'poll' || $this->resource_type == 'sitepagepoll_poll' )
				{
					echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('title'=>$item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id));
				}
				else  if($this->resource_type == 'document') {
       echo $this->htmlLink($item->getHref(), '<img src="'. $item->thumbnail .'" class="photo" />', array('title' => $item->document_title, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id) ) ; }
				else  if($this->resource_type == 'sitepagedocument_document') {
         echo $this->htmlLink($item->getHref(), '<img src="'. $item->thumbnail .'" class="photo" />', array('title' => $item->sitepagedocument_title, 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id) ) ;          
       }
        
       else if( $flag ) {
          echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title'=>$item->getTitle(), 'class'=>"thumbs_photo seao_common_add_tooltip_link", 'onclick' => "openSeaocoreLightBox('$href');return false;", 'rel'=> $this->resource_type.' '.$item->$id ));
        } else {
          echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('title'=>$item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id));
        } 
        
			?>
      <!--tooltip start here-->
      <div class="jq-checkpointSubhead">
        <div class="sitelikes_tooltip_content_outer">
          <div class="sitelikes_tooltip_content_inner">
            <div class="tooltip_arrow">
              <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitelike/externals/images/tooltip_arrow_top.png' alt="" />
            </div>
            <div class="tooltip_sitelike_title">
							<?php $title1 = $item->getTitle();?>
							<?php $truncatetitle = $title1; ?>
              <?php echo $this->htmlLink($item->getHref(),$truncatetitle, array('title'=>$item->getTitle(), 'class' => 'seao_common_add_tooltip_link', 'rel'=> $this->resource_type.' '.$item->$id)) ;?>
            </div>
            <div id = "<?php echo $this->resource_type ?>_num_of_like_<?php echo $item->$id;?>" class="tooltip_sitelikes_stat">
              <?php echo $this->translate(array('%s like', '%s likes', $likeCount), $this->locale()->toNumber($likeCount));
              ?>
            </div>

            <?php if(!empty($this->viewer_id)) { ?>
						<div class = "sitelike_button" id =  "<?php echo $this->resource_type ?>_unlikes_<?php echo $item->$id;?>" style ='<?php echo $like_setting_button;?>' >
							<a href = "javascript:void(0);" onclick = "app_likes('<?php echo $item->$id; ?>', '<?php echo $this->resource_type ?>', '<?php echo $this->resource_type ?>');">
								<i class="like_thumbdown_icon"></i>
								<?php echo $this->translate('Unlike') ?>
							</a>
            </div>
            <?php if(isset($like_ids[0])): ?>
						<div class = "sitelike_button" id =  "<?php echo $this->resource_type ?>_most_likes_<?php echo $item->$id;?>" style ='display:<?php echo $like_ids[0]["like_id"] ?"none":"block"?>' >
							<a href = "javascript:void(0);" onclick = "app_likes('<?php echo $item->$id; ?>', '<?php echo $this->resource_type ?>', '<?php echo $this->resource_type ?>');">
								<i class="like_thumbup_icon"></i>
								<span><?php echo $this->translate('Like') ?></span>
							</a>
						</div>
						<?php endif; ?>
            <?php } ?>
            <?php if(isset($like_ids[0])): ?>
            <input type ="hidden" id = "<?php echo $this->resource_type ?>_like_<?php echo $item->$id;?>" value = "<?php echo $like_ids[0]['like_id'] ? $like_ids[0]['like_id'] :0; ?>" />
            <?php endif; ?>
          </div>
        </div>
      </div>
      <!--tooltip end here-->
    <?php echo '</li>';
  } ?>
  <?php endforeach; ?>
  <?php }
  else
  { ?>
   <div class="tip"><span><?php echo $this->translate('No entry could be found.') ?></span></div>
  <?php
  }
?>
  <?php if( $this->likesetting['view_layout'] != 1) { ?>
  </ul></div>
  <?php } ?>

<?php if (empty($this->ajaxrequest)) :?>
		</ul> 
	</li>
</ul>
<?php endif;?>