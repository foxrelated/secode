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

<script type="text/javascript">

  var CommentLikesTooltips;
  en4.core.runonce.add(function() {
//     // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo  $this->string()->escapeJavascript($this->translate('Loading...')) ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'activity', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            //type : 'core_comment',
            action_id : el.getParent('li').getParent('li').getParent('li').get('id').match(/\d+/)[0],
            comment_id : id
          },
          onComplete : function(responseJSON) {
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
        req.send();
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 48,
        'y' : 16
      }
    });
    // Enable links in comments
    $$('.comments_body').enableLinks();


 // Add hover event to get tool-tip
var feedToolTipAAFEnable="<?php if( !empty($advancedactivity_composer_type) ){ echo $infotooltip ? true:false; }else { echo ''; } ?>";
   if(feedToolTipAAFEnable) {
   var show_tool_tip=false;
   var counter_req_pendding=0;
    $$('.sea_add_tooltip_link').addEvent('mouseover', function(event) {  
      var el = $(event.target); 
      ItemTooltips.options.offset.y = el.offsetHeight;
      ItemTooltips.options.showDelay = 0;
        if(!el.hasAttribute("rel")){
                  el=el.parentNode;      
           } 
       show_tool_tip=true;
      if( !el.retrieve('tip-loaded', false) ) {
       counter_req_pendding++;
       var resource='';
      if(el.hasAttribute("rel"))
         resource=el.rel;
       if(resource =='')
         return;
      
        el.store('tip-loaded', true);
        el.store('tip:title', '<div class="" style="">'+
 ' <div class="uiOverlay info_tip" style="width: 300px; top: 0px; ">'+
    '<div class="info_tip_content_wrapper" ><div class="info_tip_content"><div class="info_tip_content_loader">'+
  '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" alt="Loading" /><?php echo $this->translate("Loading ...") ?></div>'+
'</div></div></div></div>'  
);
        el.store('tip:text', '');       
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'feed', 'action' => 'show-tooltip-info'), 'default', true) ?>';
        el.addEvent('mouseleave',function(){
         show_tool_tip=false;  
        });       
     
        var req = new Request.HTML({
          url : url,
          data : {
          format : 'html',
          'resource':resource
        },
        evalScripts : true,
        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {          
            el.store('tip:title', '');
            el.store('tip:text', responseHTML);
            ItemTooltips.options.showDelay=0;
            ItemTooltips.elementEnter(event, el); // Force it to update the text 
             counter_req_pendding--;
              if(!show_tool_tip || counter_req_pendding>0){               
              //ItemTooltips.hide(el);
              ItemTooltips.elementLeave(event,el);
             }           
            var tipEl=ItemTooltips.toElement();
            tipEl.addEvents({
              'mouseenter': function() {
               ItemTooltips.options.canHide = false;
               ItemTooltips.show(el);
              },
              'mouseleave': function() {                
              ItemTooltips.options.canHide = true;
              ItemTooltips.hide(el);                    
              }
            });
            Smoothbox.bind($$(".sea_add_tooltip_link_tips"));
          }
        });
        req.send();
      }
    });
    // Add tooltips
   var window_size = window.getSize()
   var ItemTooltips = new SEATips($$('.sea_add_tooltip_link'), {
      fixed : true,
      title:'',
      className : 'sea_add_tooltip_link_tips',
      hideDelay :200,
      offset : {'x' : 0,'y' : 0},
      windowPadding: {'x':370, 'y':(window_size.y/2)}
    }); 
  }
  if(en4.sitevideoview){
     en4.sitevideoview.attachClickEvent(Array('feed','feed_video_title','feed_sitepagevideo_title','feed_sitebusinessvideo_title','feed_ynvideo_title', 'feed_sitegroupvideo_title', 'feed_sitestorevideo_title'));   
  }
  });
var sitetagcheckin_id = '<?php echo $this->sitetagcheckin_id;?>';
</script>

<?php if(empty($this->isajax) && ($this->show_map == 1)):?>
	<div id="sitetagcheckin_feed_items" class="stcheckin_feeds">
<?php endif;?>

	<?php if( !$this->getUpdate ): ?>
	<ul class='feed' id="activity-feed-<?php echo $this->sitetagcheckin_id;?>">
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
		<?php //if( !$this->noList ): ?><li id="activity-item-<?php echo $this->sitetagcheckin_id;?>-<?php echo $action->action_id ?>" style="position:relative;"><?php //endif; ?>
			<?php $this->commentForm->setActionIdentity($action->action_id, $this->sitetagcheckin_id) ?>
			<script type="text/javascript">

				(function(){
					var action_id = '<?php echo $action->action_id ?>';
          
					en4.core.runonce.add(function(){
						$('activity-comment-body-'+ sitetagcheckin_id + '-' + action_id).autogrow();
						en4.sitetagcheckin.attachComment($('activity-comment-form-'+ sitetagcheckin_id + '-' + action_id), sitetagcheckin_id,'<?php echo ($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0: 1 ;?>');
            
             if(<?php echo $this->submitComment ? '1': '0' ?>){
              document.getElementById("<?php echo $this->commentForm->getAttrib('id') ?>").style.display = "";
              document.getElementById("<?php echo $this->commentForm->submit->getAttrib('id') ?>").style.display = "none";
              if(document.getElementById("checkin-feed-comment-form-open-li_"+sitetagcheckin_id+ "_<?php echo $action->action_id ?>")){
                document.getElementById("checkin-feed-comment-form-open-li_"+sitetagcheckin_id+ "_<?php echo $action->action_id ?>").style.display = "none";}  
              document.getElementById("<?php echo $this->commentForm->body->getAttrib('id') ?>").focus();
            }
					});
				})();
			</script>

			<div class='feed_item_photo'> <?php echo  $this->htmlLink($action->getSubject()->getHref(),
				$this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()),  array('class'=>'sea_add_tooltip_link', 'rel'=>$action->getSubject()->getType().' '.$action->getSubject()->getIdentity())
			)  ?>
     </div>
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
							<a href="javascript:void(0);" title="<?php echo
									$this->translate('Delete Post') ?>" onclick="deletecheckinfeed('<?php echo
									$action->action_id ?>', 0, '<?php echo $this->sitetagcheckin_id?>')"><?php echo $this->translate('Delete') ;?></a>
				</div>
			<?php endif; ?>
		 <?php endif; ?>

		 <div class='feed_item_body'>
				<?php // Main Content ?>
				<span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
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
				</span>	

      <?php if($advancedactivityEnabled) :?>
      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments <?php echo (count($action->getAttachments()) ==3 ? 'feed_item_checkin_photo_attachments' :'')?>'>
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
                      
                       <?php if(SEA_ACTIVITYFEED_LIGHTBOX && strpos($attachment->meta->type, '_photo')):?>
                         <?php $attribs=@array_merge($attribs, array('onclick'=>'openSeaocoreLightBox("'.$attachment->item->getHref().'");return false;'));?>
                       <?php endif;?>
                       <?php if(strpos($attachment->meta->type, '_photo')): ?>
                       <?php $attribs['class']='checkin-feed-photo'; ?>
                         <?php if($attachment->item->getType()=='album_photo' || $attachment->item->getType()=='advalbum_photo'):?>
                         <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.feed', $attachment->item->getTitle(),array('class'=>'checkin-feed-photo-1')), $attribs) ?>
                          <?php else: ?>
                          <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                          <?php endif; ?>
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
		                <?php $attribs = Array('class' => 'feed_item_thumb checkin-feed-photo'); ?>           
                    <?php if(SEA_ACTIVITYFEED_LIGHTBOX && strpos($attachment->meta->type, '_photo')):?>
                      <?php $attribs=@array_merge($attribs, array('onclick'=>'openSeaocoreLightBox("'.$attachment->item->getHref().'");return false;'));?>
		                <?php endif;?>
                    <?php if($attachment->item->getType()=='album_photo' || $attachment->item->getType()=='advalbum_photo'):?>
                    <?php $count = count($action->getAttachments());?>
                    <?php
                      switch ($count):
                        case 1:
                          $photoContent = $this->itemPhoto($attachment->item, 'thumb.feed', $attachment->item->getTitle(), array('class' => "checkin-feed-photo-1"));
                          break;
                        case 2:
                          $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.feed').');" class="checkin-feed-photo-2"></span>';
                          break;
                        case 3:
                          if(!$isIncludeFirstAttachment):
                          $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.feed').');" class="checkin-feed-photo-3-big" ></span>'; 
                          else:
                            $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.normal').');" class="checkin-feed-photo-3-small" ></span>';                             
                          endif;
                          break;
                        default :
                          $photoContent ='<span style="background-image: url('.$attachment->item->getPhotoUrl('thumb.normal').');" class="checkin-feed-photo-4"></span>';
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
					<?php if(isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Page'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitepage'>
					<?php elseif(isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Business'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitebusiness'>
					<?php elseif(isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Group'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitegroup'>
					<?php elseif(isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Store'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitestore'>
					<?php elseif(isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Event'):?>
						<div class='clr feed_item_date feed_item_icon item_icon_siteevent'>
					<?php else:?>
						<div class='clr feed_item_date feed_item_icon item_icon_sitetagcheckin'>
					<?php endif;?>
        <?php endif;?>
					<ul>
						<?php if( $canComment ): ?>
							<?php if( $action->likes()->isLike($this->viewer()) ): ?>
								<li class="feed_item_option_unlike">
									<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.sitetagcheckin.unlike('.$action->action_id.', null, '. "'" . $this->sitetagcheckin_id. "'" . ');')) ?>
									&#183;
								</li>
							<?php else: ?>
								<li class="feed_item_option_like">
									<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.sitetagcheckin.like('.$action->action_id.', null, '."'" .$this->sitetagcheckin_id."'" .');')) ?>
									&#183;
								</li>
							<?php endif; ?>
							<?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
								<li class="feed_item_option_comment">
									<?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), $this->translate('Comment'), array(
										'class'=>'smoothbox',
									)) ?>
                  &#183;
								</li>
							<?php else: ?>
								<li class="feed_item_option_comment">
                  <?php if($advancedactivityEnabled) :?>
										<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'),
										array('onclick'=>'document.getElementById("'.$this->commentForm->getAttrib('id').'").style.display = "";
										document.getElementById("'.$this->commentForm->submit->getAttrib('id').'").style.display = "'.(($this->isMobile ||!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "none":"none").'";
										if(document.getElementById("checkin-feed-comment-form-open-li_'.$this->sitetagcheckin_id.'_'.$action->action_id.'")){
										document.getElementById("checkin-feed-comment-form-open-li_'.$this->sitetagcheckin_id.'_'.$action->action_id.'").style.display = "none";}  
										document.getElementById("'.$this->commentForm->body->getAttrib('id').'").focus();', 'title'=>
										$this->translate('Leave a comment'))) ?>
                <?php else: ?>
								  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('onclick'=>'document.getElementById("'.$this->commentForm->getAttrib('id').'").style.display = ""; document.getElementById("'.$this->commentForm->submit->getAttrib('id').'").style.display = "block"; document.getElementById("'.$this->commentForm->body->getAttrib('id').'").focus();')) ?>
                <?php endif; ?>
								&#183;
               </li>
							<?php endif; ?>


							<?php if( $this->viewAllComments ): ?>
								<script type="text/javascript">
									en4.core.runonce.add(function() {
										document.getElementById('<?php echo $this->commentForm->getAttrib('id') ?>').style.display = "";
										document.getElementById('<?php echo $this->commentForm->submit->getAttrib('id') ?>').style.display = "block";
										document.getElementById('<?php echo $this->commentForm->body->getAttrib('id') ?>').focus();
									});
								</script>
							<?php endif ?>
						<?php endif; ?>

            <?php if($advancedactivityEnabled):?>
							<?php if( $this->viewer()->getIdentity() && (
												'user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) &&                  
												Engine_Api::_()->advancedactivity()->hasFeedTag($action)                  
											): ?>
								<li class="feed_item_option_add_tag">           
									<?php echo $this->htmlLink(array(
										'route' => 'default',
										'module' => 'advancedactivity',
										'controller' => 'feed',
										'action' => 'tag-friend',
										'id' => $action->action_id
									), $this->translate('Tag Friends'), array('class' => 'smoothbox', 'title' =>
										$this->translate('Tag more friends'))) ?>
									&#183;
								</li>
							<?php elseif($this->viewer()->getIdentity() && Engine_Api::_()->advancedactivity()->hasMemberTagged($action, $this->viewer())): ?>  
								<li class="feed_item_option_remove_tag">        
									<?php echo $this->htmlLink(array(
										'route' => 'default',
										'module' => 'advancedactivity',
										'controller' => 'feed',
										'action' => 'remove-tag',
										'id' => $action->action_id
									), $this->translate('Remove Tag'), array('class' => 'smoothbox')) ?>
                  &#183;
								</li>
							<?php endif; ?>
            <?php endif; ?>
						<?php // Share ?>
						<?php if( ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity()) && (!isset($action->shareable) || $action->shareable) ): ?>
							<?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>
								<li class="feed_item_option_share">
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', "not_parent_refresh"=>1), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
                  &#183;
								</li>
							<?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
								<li class="feed_item_option_share">
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(),'action_id'=>$action->getIdentity(), 'format' => 'smoothbox',  "not_parent_refresh"=>1), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
                  &#183;
								</li>
							<?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
								<li class="feed_item_option_share">
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(),'action_id'=>$action->getIdentity(), 'format' => 'smoothbox',  "not_parent_refresh"=>1), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
                  &#183;
								</li>
							<?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
								<li class="feed_item_option_share">
									<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(),'action_id'=>$action->getIdentity(), 'format' => 'smoothbox',  "not_parent_refresh"=>1), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
									&#183;
								</li>
							<?php endif; ?>
						<?php endif; ?>
						<li>
							<?php echo $this->timestamp($action->getTimeValue()) ?>
						</li>
					</ul>
				</div>

				<?php if( $action->getTypeInfo()->commentable  && (!isset ($action->commentable) || $action->commentable)): // Comments - likes ?>
					<div class='comments'>
						<ul>
							<?php if( $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers())>0) ): ?>
								<li>
									<div></div>
									<div class="comments_likes">
										<?php if( $action->likes()->getLikeCount() <= 10 || $this->viewAllLikes ): ?>
											<?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->fluentList($action->likes()->getAllLikesUsers()) )?>
										<?php else: ?>
											<?php echo $this->htmlLink($action->getSubject()->getHref(array('action_id' => $action->action_id, 'show_likes' => true)),
												$this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount()) )
											) ?>
										<?php endif; ?>
									</div>
								</li>
							<?php endif; ?>
							<?php if( $action->comments()->getCommentCount() > 0 ): ?>
								<?php if( $action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
									<li>
										<div></div>
										<div class="comments_viewall">
											<?php if( $action->comments()->getCommentCount() > 5): ?>
												<?php echo $this->htmlLink($action->getSubject()->getHref(array('action_id' => $action->action_id, 'show_comments' => true)),
														$this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
														$this->locale()->toNumber($action->comments()->getCommentCount()))) ?>
											<?php else: ?>
												<?php echo $this->htmlLink('javascript:void(0);',
														$this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
														$this->locale()->toNumber($action->comments()->getCommentCount())),
														array('onclick'=>'en4.sitetagcheckin.viewComments('.$action->action_id.', '.$this->sitetagcheckin_id.');')) ?>
											<?php endif; ?>
										</div>
									</li>
								<?php endif; ?>
								<?php foreach( $action->getComments($this->viewAllComments) as $comment ): ?>
									<li id="comment-<?php echo $this->sitetagcheckin_id?>-<?php echo $comment->comment_id ?>">
										<div class="comments_author_photo">
											<?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(),
												$this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())
											) ?>
										</div>
										<div class="comments_info">
											<span class='comments_author'>
												<?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
												<?php if ( $this->viewer()->getIdentity() &&
																	(('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
																		($this->viewer()->getIdentity() == $comment->poster_id) ||
																		$this->activity_moderate ) ): ?>
													<a href="javascript:void(0);" class="stcheckin_comment_remove" title="<?php echo
													$this->translate('Delete Comment') ?>" onclick="deletecheckinfeed('<?php echo
													$action->action_id ?>', '<?php echo $comment->comment_id ?>', '<?php echo $this->sitetagcheckin_id?>')"></a>
												<?php endif; ?>
											</span>
											<span class="comments_body">
												<?php echo  $this->smileyToEmoticons($this->viewMore($comment->body)) ?>
											</span>
										<ul class="comments_date">
											<li class="comments_timestamp">
												<?php echo $this->timestamp($comment->creation_date); ?>
											</li>
	
												<?php if( $canComment ):
													$isLiked = $comment->likes()->isLike($this->viewer());
												?>
													<li class="sep">-</li>
													<li class="comments_like">
														<?php if( !$isLiked ): ?>
															<a href="javascript:void(0)" onclick="en4.sitetagcheckin.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->sitetagcheckin_id?>')">
																<?php echo $this->translate('like') ?>
															</a>
														<?php else: ?>
															<a href="javascript:void(0)" onclick="en4.sitetagcheckin.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>, '<?php echo $this->sitetagcheckin_id?>')">
																<?php echo $this->translate('unlike') ?>
															</a>
														<?php endif ?>
													</li>
												<?php endif ?>
												<?php if( $comment->likes()->getLikeCount() > 0 ): ?>
													<li class="sep">-</li>
													<li class="comments_likes_total">
														<a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
															<?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
														</a>
													</li>
												<?php endif ?>
											</ul>
										</div>
									</li>
								<?php endforeach; ?>
                <?php if($advancedactivityEnabled) :?>
                  <?php if ($canComment): ?>
                   <li id='checkin-feed-comment-form-open-li_<?php echo $this->sitetagcheckin_id ?>_<?php echo $action->action_id ?>' onclick='<?php echo 'document.getElementById("'.$this->commentForm->getAttrib('id').'").style.display = "";
document.getElementById("'.$this->commentForm->submit->getAttrib('id').'").style.display = "'.(($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "none":"none").'";
document.getElementById("checkin-feed-comment-form-open-li_'.$this->sitetagcheckin_id. '_'.$action->action_id.'").style.display = "none";
  document.getElementById("'.$this->commentForm->body->getAttrib('id').'").focus();'?>' <?php if(!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment):?> style="display:none;"<?php endif;?> >                  <div></div>
                  <div class="seaocore_comment_box seaocore_txt_light"><?php echo $this->translate('Post a comment...') ?></div></li>
                  <?php endif; ?>
              <?php endif; ?>
							<?php endif; ?>
						</ul>
						<?php if( $canComment ) echo $this->commentForm->render() /*
						<form>
							<textarea rows='1'>Add a comment...</textarea>
							<button type='submit'>Post</button>
						</form>
						*/ ?>
					</div>
				<?php endif; ?>
			</div>
      <?php if($this->show_map == 1) :?>
				<?php if(isset($action->resource_type) && ($action->resource_type == 'sitepage_album' || $action->resource_type == 'advalbum_album' || $action->resource_type == 'sitebusiness_album' || $action->resource_type == 'sitegroup_album' || $action->resource_type == 'sitestore_album')) : ?>
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
		<script type="text/javascript">
			var feedPage = <?php echo sprintf('%d', $feedactions->getCurrentPageNumber()) ?>;
			var paginateFeeds = function(page) 
			{
        $('show-background-pagination-image-feeds').style.display = "block";
				var url = '<?php echo $this->url(array('action' => 'get-location-photos'), 'sitetagcheckin_general', true);?>';

				en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'isajax' : '1',
						'page' : page,
            'show_map' : 1,
						'location_id' : '<?php echo $this->location_id;?>',
						'location' : '<?php echo $this->location;?>',
            'category': '<?php echo $this->category;?>',
            'feed_type': 'checkins'
					}
				}), {
					'element' : $('sitetagcheckin_feed_items')
				}, {"force":true});
			}
		</script>

		<?php if( $feedactions->getTotalItemCount() > 1 ):?>
			<div class="sitetag_checkin_map_tip_paging">
				<?php if( $feedactions->getCurrentPageNumber() > 1 ): ?>
					<div id="user_group_members_previous" class="paginator_previous">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
							'onclick' => 'paginateFeeds(feedPage - 1)',
							'class' => 'buttonlink icon_previous'
						)); ?>
					</div>
				<?php endif; ?>
				<?php if( $feedactions->getCurrentPageNumber() < $feedactions->count() ): ?>
					<div id="user_group_members_next" class="paginator_next">
						<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
							'onclick' => 'paginateFeeds(feedPage + 1)',
							'class' => 'buttonlink_right icon_next'
						)); ?>
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

<script type="text/javascript">

function deletecheckinfeed(action_id, comment_id) {
if (comment_id == 0) {

var msg="<div class='aaf_show_popup'><h3>"+ "<?php echo $this->translate('Delete Activity Item?') ?>"+"</h3><p>"+ "<?php echo $this->string()->escapeJavascript($this->translate('Are you sure that you want to delete this activity item? This action cannot be undone.')) ?>"+"</p>"+ "<button type='submit' onclick='content_delete_act_checkin("+action_id+", 0); return false;'>"+ "<?php echo $this->string()->escapeJavascript($this->translate('Delete')) ?>"+"</button>"+ " <?php echo $this->string()->escapeJavascript($this->translate('or')) ?> "+"<a href='javascript:void(0);'onclick='parent.Smoothbox.close();'>"+ "<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>"+"</a></div>"

} else {
var msg="<div class='aaf_show_popup'><h3>"+ "<?php echo $this->string()->escapeJavascript($this->translate('Delete Comment?')) ?>"+"</h3><p>"+ "<?php echo $this->string()->escapeJavascript($this->translate('Are you sure that you want to delete this comment? This action cannot be undone.')) ?>"+"</p>"+ "<button type='submit' onclick='content_delete_act_checkin("+action_id+","+comment_id+"); return false;'>"+ "<?php echo $this->string()->escapeJavascript($this->translate('Delete'))?>"+"</button>"+ " <?php echo $this->string()->escapeJavascript($this->translate('or')) ?> "+"<a href='javascript:void(0);'onclick='parent.Smoothbox.close();'>"+ "<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>"+"</a></div>"
}
Smoothbox.open(msg);
}

var content_delete_act_checkin = function (action_id, comment_id) {
          if(comment_id == 0) { 
          $('activity-item-'+sitetagcheckin_id+'-'+action_id).destroy();
         } else {
            $('comment-'+sitetagcheckin_id+'-'+comment_id).destroy();
         } 
          parent.Smoothbox.close();
  url = en4.core.baseUrl + 'sitetagcheckin/activity/delete';
  var request = new Request.JSON({
    'url' : url,
    'method':'post',
    'data' : {
    'format' : 'json',
    'action_id' : action_id,
    'comment_id' : comment_id,
    'sitetagcheckin_id':sitetagcheckin_id
    }

//     onSuccess : function(responseJSON) {
//           if(comment_id == 0) { 
//           $('activity-item-'+action_id).destroy();
//          } else {
//             $('comment-'+comment_id).destroy();
//          } 
//           parent.Smoothbox.close();
// 
//       }
   });
    request.send();
}

</script>