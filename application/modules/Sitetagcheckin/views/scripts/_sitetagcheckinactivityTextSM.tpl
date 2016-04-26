<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _sitetagcheckinactivitytext.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $advancedactivityEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
$settings = Engine_Api::_()->getApi('settings', 'core');
$advancedactivity_composer_type = $settings->getSetting('advancedactivity.composer.type', 0);
$infotooltip = $settings->getSetting('advancedactivity.info.tooltips', 1);
   $this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css');
   $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
            . 'application/modules/Seaocore/externals/styles/style_infotooltip.css');
?>
<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/scripts/activity_core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js') ?>

<?php if( empty($this->actions) ) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
   $feedactions = $this->actions;
} ?>

<?php 
$is_mobile = Engine_Api::_()->seaocore()->isMobile();
if(empty($this->show_map)) {
  $feedactions = $this->actions;
} else if($this->show_map == 2) {
	$feedactions = $this->actions;
}
?>
<?php if (empty($this->noList)) : ?>
<script type="text/javascript">

var sitetagcheckin_id = '<?php echo $this->sitetagcheckin_id;?>';
</script>
<?php endif;?>

<?php if(empty($this->isajax) && ($this->show_map == 1)):?>
	<div id="sitetagcheckin_feed_items" class="stcheckin_feeds">
<?php endif;?>

	<?php if( !$this->getUpdate ): ?>
	<ul class='feeds' id="activity-feed-<?php echo $this->sitetagcheckin_id;?>">
	<?php endif ?>
		
	<?php $this->commentShowBottomPost = 1;
		foreach( $feedactions as $action ):

	// (goes to the end of the file)
			try { // prevents a bad feed item from destroying the entire page
				// Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
				if( !$action->getTypeInfo()->enabled ) continue;
				if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
				if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
				
				ob_start();
			?>
			<!--map header start-->
      <?php $params="";?>
      <?php if(isset($action->locationparams)):?>
      <?php  $params = json_decode($action->locationparams, true)?>
      <?php if($this->show_map == 1) :?>
      	<div class="stcheckin_map_tip_header">
				<?php if(!empty($params) && isset($params['checkin']) && !empty($params['checkin']['resource_guid'])) : ?>
					<?php $getItem = Engine_Api::_()->getItemByGuid($params['checkin']['resource_guid']);?>
					<div class="stcheckin_map_tip_header_img">
						<?php echo $this->htmlLink($getItem->getHref(), $this->itemPhoto($getItem, 'thumb.icon','')); ?>
					</div>
					<div class="stcheckin_map_tip_header_info">
						<div class="stcheckin_map_tip_header_title">
							<?php echo $this->htmlLink($getItem->getHref(), $getItem->getTitle()); ?>
						</div>
						<div class="stcheckin_map_tip_header_stat seaocore_txt_light">
							<?php echo ucfirst($getItem->getShortType());?> 
								<?php if(Engine_Api::_()->seaocore()->getCategory($getItem->getType(), $getItem)):?>
									&raquo; 
									<?php echo Engine_Api::_()->seaocore()->getCategory($getItem->getType(), $getItem);	?>
								<?php endif;?>
						</div>
					</div>		
				<?php else:?>
					<?php if(isset($params['checkin']['label'])): ?>
						<div class="stcheckin_map_tip_header_img">
							<img src="./application/modules/Sitetagcheckin/externals/images/map.png" alt="" align="right" class="thumb_icon" />
						</div>
						<div class="stcheckin_map_tip_header_info">
							<div class="stcheckin_map_tip_header_title">               
                <?php if($params['checkin']['type'] == 'just_use'):?>
									<?php  echo $this->htmlLink('https://maps.google.com/?q=' . urlencode($params['checkin']['label']), $params['checkin']['label'], array('target' => '_blank')); ?>
                <?php elseif($params['checkin']['type'] == 'place'): ?>
                 <?php if(!$is_mobile) :?>
                 <?php  echo $this->htmlLink($this->url(array('guid' => $action->getGuid(),'format'=>'smoothbox'), 'sitetagcheckin_viewmap', true), $params['checkin']['label'], array('class' => 'smoothbox')); ?>
                 <?php else:?>
									 <?php  echo $this->htmlLink($this->url(array('guid' => $action->getGuid()), 'sitetagcheckin_viewmap', true), $params['checkin']['label'], array()); ?> 
                 <?php endif;?>
                <?php endif;?>
							</div>
						</div>		
					<?php endif;?>
				<?php endif;?>
				</div>
      <?php endif;?>
      <?php endif;?>
      <!--map header end-->
		<?php if( !$this->noList ): ?>
      
      
      
      <li id="activity-item-<?php echo $this->sitetagcheckin_id;?>-<?php echo $action->action_id ?>" style="position:relative;"><?php endif; ?>
			<?php $this->commentForm->setActionIdentity($action->action_id, $this->sitetagcheckin_id) ?>
			<script type="text/javascript">

				(function(){
					var action_id = '<?php echo $action->action_id ?>';     
					
				})();
			</script>
      <div id="main-feed-<?php echo $action->action_id ?>">
      <div class="feed_item_header">
      <?php if($this->show_map != 1) :?>
			<?php if( $this->viewer()->getIdentity() && (
						$this->activity_moderate || (
							$this->allow_delete && (
								('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
								('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
							)
						)
				) ): ?>
       <div class="feed_items_options_btn">        
	            <a href="javascript:void(0);" onclick="sm4.activity.showOptions('<?php echo $action->action_id ?>')" data-role="button" data-icon="cog" data-iconpos="notext" data-theme="c" data-inline="true"></a>
	     </div>
      	<?php endif; ?>
		 <?php endif; ?>
			<div class='feed_item_photo'> <?php echo  $this->htmlLink($action->getSubject()->getHref(),
				$this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()),  array('class'=>'sea_add_tooltip_link', 'rel'=>$action->getSubject()->getType().' '.$action->getSubject()->getIdentity())
			)  ?>
     </div>
       <div class="feed_item_status">
        <?php // Main Content ?>
        <div class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
          <?php $tagContent="";?>
          <?php if($advancedactivityEnabled):?>
            <?php 
              $content =  $this->getContent($action);
             ?>
           <?php else:?>
           <?php   $content =  $action->getContent(); ?>
          <?php endif;?>
				  <?php $contentString = $this->getSitetagCheckin($action,$content, 1);?>
				  <?php echo $contentString[0] ?>
        </div>  
      </div>
      </div>
		 
     
		 <div class='feed_item_body'>
					

      <?php if($advancedactivityEnabled) :?>
      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments <?php echo (count($action->getAttachments()) ==3 ? 'feed_item_aaf_photo_attachments' :'')?>'>
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = $this->getRichContent(current($action->getAttachments())->item)) ): ?>
              <?php echo $richContent; ?>
            <?php else: ?>
              <?php $isIncludeFirstAttachment=false;?>
              <?php foreach( $action->getAttachments() as $attachment ): ?>
              
                <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                  <div>
                    <?php if( $attachment->item->getPhotoUrl() ): ?>
                      <?php
                        if ($attachment->item->getType() == "core_link")
                        {
                          $attribs = Array('target'=>'_blank');
                        }
                        else
                        {
                          $attribs = Array();
                        }
                      ?>       
                      
                       <?php if(strpos($attachment->meta->type, '_photo')): ?>
                       <?php $attribs['data-linktype']='photo-gallery'; ?>
                          <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>

                        <?php else: ?>
                         <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                       <?php endif;?>
                    <?php endif; ?>
                    
                    <div>
                      <div class='feed_item_link_title'>
                        <?php
                          if ($attachment->item->getType() == "core_link")
                          {
                            $attribs = Array('target'=>'_blank');
                          }
                          else
                          {
                            $attribs = array('class'=>'sea_add_tooltip_link', 'rel'=>$attachment->item->getType().' '.$attachment->item->getIdentity());
                          }
                          echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                        ?>
                      </div>
                      <div class='feed_item_link_desc'>
                        <?php if ($attachment->item->getType() == "activity_action"):                    
                             echo $this->getContent($attachment->item,true);
                          else:                          
                           echo $this->viewMore($attachment->item->getDescription()); 
                          endif; ?>
                      </div>
                    </div>
                  </div>
                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                  <div class="feed_attachment_photo">
		                <?php $attribs = Array('class' => 'feed_item_thumb aaf-feed-photo'); ?>           
                    
                    <?php if($attachment->item->getType()=='album_photo' || $attachment->item->getType()=='advalbum_photo'):?>
                    <?php $attribs['data-linktype']='photo-gallery'; ?>
                    <?php $count = count($action->getAttachments());?>
                    <?php
                      switch ($count):
                        case 1:
                          $photoContent = $this->itemPhoto($attachment->item, 'thumb.feed', $attachment->item->getTitle(), array('class' => "aaf-feed-photo-1"));
                          break;
                        case 2:
                          $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.feed').');" class="aaf-feed-photo-2"></span>';
                          break;
                        case 3:
                          if(!$isIncludeFirstAttachment):
                          $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.feed').');" class="aaf-feed-photo-3-big" ></span>'; 
                          else:
                            $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.normal').');" class="aaf-feed-photo-3-small" ></span>';                             
                          endif;
                          break;
                        default :
                          $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.normal').');" class="aaf-feed-photo-4"></span>';
                      endswitch;
                      echo $this->htmlLink($attachment->item->getHref(), $photoContent, $attribs);
                    ?>
                    <?php else: ?>                    
                   <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                    <?php endif; ?>
                  </div>
                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                <?php endif; ?>
                </span>
                <?php $isIncludeFirstAttachment= true;?>
              <?php endforeach; ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
        <?php else:?>
					<?php // Attachments ?>
					<?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
						<div class='feed_item_attachments'>
							<?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
								<?php if( count($action->getAttachments()) == 1 &&
												null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>
									<?php echo $richContent; ?>
								<?php else: ?>
									<?php foreach( $action->getAttachments() as $attachment ): ?>
										<span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
										<?php if( $attachment->meta->mode == 0 ): // Silence ?>
										<?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
											<div>
												<?php 
													if ($attachment->item->getType() == "core_link")
													{
														$attribs = Array('target'=>'_blank');
													}
													else
													{
														$attribs = Array();
													} 
												?>


												<?php if(SEA_ACTIVITYFEED_LIGHTBOX && strpos($attachment->meta->type, '_photo')):?>
													<?php $attribs=@array_merge($attribs, array('onclick'=>'openSeaocoreLightBox("'.$attachment->item->getHref().'");return false;'));?>
												<?php endif;?>


												<?php if( $attachment->item->getPhotoUrl() ): ?>
													<?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
												<?php endif; ?>


												<div>
													<div class='feed_item_link_title'>
														<?php
															echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
														?>
													</div>
													<div class='feed_item_link_desc'>
														<?php echo $this->viewMore($attachment->item->getDescription()) ?>
													</div>
												</div>

											</div>
										<?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
											<div class="feed_attachment_photo">
												<?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb')) ?>
											</div>
										<?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
											<?php echo $this->viewMore($attachment->item->getDescription()); ?>
										<?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
										<?php endif; ?>
										</span>
									<?php endforeach; ?>
									<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
        <?php endif;?>
				<?php // Icon, time since, action links ?>
				<?php
					$icon_type = 'activity_icon_'.$action->type;
					list($attachment) = $action->getAttachments();
					if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
						$icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
					endif;
					$canComment = ( $action->getTypeInfo()->commentable &&
							$this->viewer()->getIdentity() &&
							Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
							!empty($this->commentForm)  && (!isset ($action->commentable) || $action->commentable) );
				?>

				<?php if(isset($action->params['checkin'])):?>
					<?php if($action->params['checkin']['type'] == 'Page'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitepage'>
					<?php elseif($action->params['checkin']['type'] == 'Business'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitebusiness'>
					<?php elseif($action->params['checkin']['type'] == 'Group'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitegroup'>
					<?php elseif($action->params['checkin']['type'] == 'Store'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitestore'>
					<?php elseif($action->params['checkin']['type'] == 'Event'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_siteevent'>
					<?php else:?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitetagcheckin'>
					<?php endif;?>
        <?php endif;?>
					
				</div>

			
			</div>
              
       <div class="feed_item_btm">
						<span class="feed_item_date">
							<?php echo $this->timestamp($action->getTimeValue()) ?>
						</span>
						<?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
							<span class="sep">-</span>
              <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
								<i class="ui-icon ui-icon-thumbs-up"></i>
								<span><?php echo $this->translate(array('%s like', '%s likes', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount())); ?></span>
							</a>	
							<?php if ($action->comments()->getCommentCount() > 0) :  echo  '<span class="sep">-</span>'?> 
							<a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
								<i class="ui-icon ui-icon-comment"></i>
								<span><?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())); endif; ?></span>
							</a>
						<?php elseif ($action->comments()->getCommentCount() > 0) :?>
							<span class="sep">-</span>
              <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' >
								<i class="ui-icon ui-icon-comment"></i>
								<span><?php echo $this->translate(array('%s comment', '%s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())); ?></span>
							</a>
						<?php endif; ?>
					</div>
              
           <div class="feed_item_option">
            <?php if ($canComment || ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity() && $action->shareable && (($action->shareable > 1 && $action->shareable < 5) || ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()))))):?>          
          	<div data-role="navbar" data-inset="false">
		          <ul>
		            <?php if ($canComment): ?>
		              <?php if ($action->likes()->isLike($this->viewer())): ?>
		              	<li>
		               		<a href="javascript:void(0);" onclick="javascript:sm4.sitetagcheckin.unlike('<?php echo $action->action_id ?>', null, '<?php echo $this->sitetagcheckin_id;?>');">
		               			<i class="ui-icon ui-icon-thumbs-down"></i>
		               			<span><?php echo $this->translate('Unlike') ?></span>
		               		</a>
		               	</li>
		              <?php else: ?>
		              	<li> 
		               		<a href="javascript:void(0);" onclick="javascript:sm4.sitetagcheckin.like('<?php echo $action->action_id ?>', null, '<?php echo $this->sitetagcheckin_id;?>');">
		               			<i class="ui-icon ui-icon-thumbs-up"></i>
		               			<span><?php echo $this->translate('Like') ?></span>
		               		</a>
		               	</li>
		              <?php endif; ?>
		              <?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes  ?>
		              	<li>
		               		<a href="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), 'default', 'true'); ?>">
		               			<i class="ui-icon ui-icon-comment"></i>
		               			<span><?php echo $this->translate('Comment'); ?></span>
		               		</a>
		               	</li>
		              <?php else: ?>
                     <li>
                       <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity()), 'default', 'true'); ?>" , "feedsharepopup")'>
                        <i class="ui-icon ui-icon-comment"></i>
                        <span><?php echo $this->translate('Comment'); ?></span>
                      </a>
                    </li>
		              <?php endif; ?>
	            	<?php endif; ?>
	
		            <?php // Share  ?>
		            <?php if ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity() && $action->shareable): ?>
		              <?php if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())): ?>
			              <li>
			              	<a href="javascript:void(0);" onclick ='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
			              		<i class="ui-icon ui-icon-share-alt"></i>
			              		<span><?php echo $this->translate('Share'); ?></span>
			              	</a>
			              </li>
		              <?php elseif ($action->getTypeInfo()->shareable == 2): ?>
		              	<li>
                      <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' >
		               			<i class="ui-icon ui-icon-share-alt"></i>
			              		<span><?php echo $this->translate('Share'); ?></span>
		               		</a>
		               	</li>
		              <?php elseif ($action->getTypeInfo()->shareable == 3): ?>
		               	<li>
                      <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")'>
		               			<i class="ui-icon ui-icon-share-alt"></i>
			              		<span><?php echo $this->translate('Share'); ?></span>
		               		</a>
		               	</li>
		              <?php elseif ($action->getTypeInfo()->shareable == 4): ?>
		              	<li> 
                      <a href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' >
		              			<i class="ui-icon ui-icon-share-alt"></i>
			              		<span><?php echo $this->translate('Share'); ?></span>
		              		</a>
		              	</li>
		              <?php endif; ?>
		            <?php endif; ?>
          		</ul>
          	</div>
            <?php endif;?>
          </div>   
            </div>    
              
           <div id="feed-options-<?php echo $action->action_id ?>" class="feed_item_option_box" style="display:none">
           <?php if($this->show_map != 1) :?>
			<?php if( $this->viewer()->getIdentity() && (
						$this->activity_moderate || (
							$this->allow_delete && (
								('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
								('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
							)
						)
				) ): ?>
				<div class="feed_item_option_delete stcheckin_feed_delete_btn">
							<a href="javascript:void(0);" title="" class="ui-btn-default ui-btn-danger" onclick="javascript:sm4.sitetagcheckin.activityremove(this);" data-url="<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'activity', 'action' => 'delete', 'action_id' => $action->action_id), 'default', 'true'); ?>" data-message="0-<?php echo $action->action_id ?>">
						<?php echo $this->translate('Delete Feed') ?>
					</a>
				</div>
			<?php endif; ?>
		 <?php endif; ?>
            <a href="#" class="ui-btn-default" onclick="sm4.activity.hideOptions('<?php echo $action->action_id ?>');">
							<?php echo $this->translate("Back");?>
						</a>
            
        </div>   
              
              
      <?php if($this->show_map == 1) :?>
				<?php if(isset($action->resource_type) && ($action->resource_type == 'sitepage_album' || $action->resource_type == 'advalbum_album' || $action->resource_type == 'sitebusiness_album' || $action->resource_type == 'sitestore_album' || $action->resource_type == 'sitegroup_album')) : ?>
					<div class="sitetag_checkin_map_link">
						<?php $getItem = Engine_Api::_()->getItem($action->resource_type, $action->resource_id); ?>
						<?php if($action->resource_type == 'sitepage_album'):?>
							<?php $itemTable = "sitepage_photo";?>
							<?php $getPhotoId = $getItem->photo_id;?>
							<?php $getPhotoUrl = Engine_Api::_()->sitetagcheckin()->getPhotoUrl($getPhotoId, $itemTable); ?>
						<?php elseif($action->resource_type == 'sitebusiness_album'):?>
							<?php $itemTable = "sitebusiness_photo";?>
							<?php $getPhotoId = $getItem->photo_id;?>
							<?php $getPhotoUrl = Engine_Api::_()->sitetagcheckin()->getPhotoUrl($getPhotoId, $itemTable); ?>
						<?php elseif($action->resource_type == 'sitegroup_album'):?>
							<?php $itemTable = "sitegroup_photo";?>
							<?php $getPhotoId = $getItem->photo_id;?>
							<?php $getPhotoUrl = Engine_Api::_()->sitetagcheckin()->getPhotoUrl($getPhotoId, $itemTable); ?>
						<?php elseif($action->resource_type == 'sitestore_album'):?>
							<?php $itemTable = "sitestore_photo";?>
							<?php $getPhotoId = $getItem->photo_id;?>
							<?php $getPhotoUrl = Engine_Api::_()->sitetagcheckin()->getPhotoUrl($getPhotoId, $itemTable); ?>
						<?php elseif($action->resource_type == 'advalbum_album'):?>
							<?php $itemTable = "advalbum_photo";?>
							<?php $getPhotoId = $getItem->photo_id;?>
							<?php $getPhotoUrl = Engine_Api::_()->getItem($itemTable, $getPhotoId)->getHref(); ?>
						<?php else:?>
							<?php $itemTable = "album_photo";?>
							<?php $getPhotoId = $getItem->photo_id;?>
							<?php $getPhotoUrl = Engine_Api::_()->getItem($itemTable, $getPhotoId)->getHref(); ?>
						<?php endif;?>
						<a href="<?php echo $getPhotoUrl?>" onclick="openSeaocoreLightBox('<?php echo $getPhotoUrl?>');return false;" class="thumbs_photo buttonlink sitetagcheckin_icon_photo">
							<span><?php echo $this->translate("View Album");?></span>
						</a>
					</div> 
				<?php endif;?>
      <?php endif;?>
		<?php //if( !$this->noList ): ?></li><?php //endif; ?>

	<?php
				ob_end_flush();
			} catch (Exception $e) {
				ob_end_clean();
				if( APPLICATION_ENV === 'development' ) {
					echo $e->__toString();
				}
			};
		endforeach;
	?>

	<?php if( !$this->getUpdate ): ?>
	  </ul>
	<?php endif ?>

  <?php if($this->show_map == 1) :?>
		

		<?php if( $feedactions->getTotalItemCount() > 1 ):?>
			<div class="sitetag_checkin_map_tip_paging">
				<?php if( $feedactions->getCurrentPageNumber() > 1 ): ?>
					<div id="user_group_members_previous" class="paginator_previous">
            <span class="ui-icon ui-icon-chevron-left"></span>
						<?php 
               $prevpage = sprintf('%d', $feedactions->getCurrentPageNumber()) - 1;
               echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
							'onclick' => 'sm4.sitetagcheckin.paginateFeeds(' .$prevpage . ',' . $this->location_id .', "'. $this->location .'", "'. $this->category. '", "' . $this->url(array('action' => 'get-location-photos'), 'sitetagcheckin_general', true) . '" )',
							'class' => 'buttonlink icon_previous'
						)); ?>
					</div>
				<?php endif; ?>
				<?php if( $feedactions->getCurrentPageNumber() < $feedactions->count() ): ?>
					<div id="user_group_members_next" class="paginator_next">
						<?php 
               $nextpage = sprintf('%d', $feedactions->getCurrentPageNumber()) + 1;
               echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
							'onclick' => 'sm4.sitetagcheckin.paginateFeeds(' .$nextpage . ',' . $this->location_id .', "'. $this->location .'", "'. $this->category. '", "' . $this->url(array('action' => 'get-location-photos'), 'sitetagcheckin_general', true) . '" )',
							'class' => 'buttonlink_right icon_next'
						)); ?>
            <span class="ui-icon ui-icon-chevron-right"></span>
					</div>
				<?php endif; ?>
			</div>
			<div id="show-background-pagination-image-feeds" style="display:none"> 
				<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" /></center>
			</div>
		<?php endif; ?>
	<?php endif; ?>

<?php if(empty($this->isajax) && ($this->show_map == 1)):?>
	</div>
<?php endif;?>
<?php if (empty($this->getUpdate)) : ?>
<script type="text/javascript">
			sm4.core.photoGallery.set($('#activity-item-<?php echo $this->sitetagcheckin_id;?>-<?php echo $action->action_id ?>'));
		</script>
<?php endif;?>