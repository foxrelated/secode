<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _nestedcomment_activityText.tpl 6590 2014-11-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/_activitySettings.tpl';
?>
<?php $sharesTable = Engine_Api::_()->getDbtable('shares', 'advancedactivity'); ?>
<?php

if (empty($this->actions)) {
    echo $this->translate("The action you are looking for does not exist.");
    return;
} else {
    $actions = $this->actions;
}
?>
<script type="text/javascript">
  var CommentLikesTooltips;
  var photoEnabled = '<?php echo $photoEnabled ?>';
  var smiliesEnabled = '<?php echo $smiliesEnabled ?>';
  <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum') && Engine_Api::_()->nestedcomment()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitealbum')->version, '4.8.4')): ?>
			var requestOptionsURLNestedComment = en4.core.baseUrl + 'sitealbum/album/compose-upload/type/comment';
			var fancyUploadOptionsURLNestedComment = en4.core.baseUrl + 'sitealbum/album/compose-upload/format/json/type/comment';
    <?php else: ?>
			var requestOptionsURLNestedComment = en4.core.baseUrl + 'nestedcomment/album/compose-upload/type/comment';
			var fancyUploadOptionsURLNestedComment = en4.core.baseUrl + 'nestedcomment/album/compose-upload/format/json/type/comment';
    <?php endif; ?>
    var allowQuickComment= '<?php echo ($this->isMobile||!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0: 1 ;?>';
    var allowQuickReply = '<?php echo ($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>';
  en4.core.runonce.add(function() {
    en4.core.language.addData({
      "Stories from %s are hidden now and will not appear in your Activity Feed anymore.":"<?php echo $this->string()->escapeJavascript($this->translate("Stories from %s are hidden now and will not appear in your Activity Feed anymore.")); ?>"
    });
    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'get-likes'), 'default', true) ?>';
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
        'x' : 20,
        'y' : 10
      }
    });
    // Enable links in comments
    $$('.comments_body').enableLinks();     
  });   
</script>

<?php $advancedactivityCoreApi = Engine_Api::_()->advancedactivity();
$advancedactivitySaveFeed = Engine_Api::_()->getDbtable('saveFeeds', 'advancedactivity'); ?>
<?php
foreach ($actions as $action): // (goes to the end of the file)
  try { // prevents a bad feed item from destroying the entire page
    // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
    if (!$action->getTypeInfo()->enabled)
      continue;
    if (!$action->getSubject() || !$action->getSubject()->getIdentity())
      continue;
    if (!$action->getObject() || !$action->getObject()->getIdentity())
      continue;
    ob_start();
    ?>
    <?php $item = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject(); ?>

 <?php if($this->onViewPage): $actionBaseId="view-".$action->action_id; else:$actionBaseId=$action->action_id;endif;?>
    <?php $this->commentForm->setActionIdentity($actionBaseId);
    $this->commentForm->action_id->setValue($action->action_id);?>
    <script type="text/javascript">
      (function(){

        en4.core.runonce.add(function(){
          
          $('<?php echo $this->commentForm->body->getAttrib('id') ?>').autogrow();  
          
          
         // setTimeout(showCommentBox("<?php echo $this->commentForm->getAttrib('id')?>", "<?php echo $this->commentForm->body->getAttrib('id')?>"), 5000);
          
           if(allowQuickComment == '1' && <?php echo $this->submitComment ? '1': '0' ?>){ 
              //document.getElementById("<?php echo $this->commentForm->getAttrib('id') ?>").style.display = "";
              //document.getElementById("<?php echo $this->commentForm->submit->getAttrib('id') ?>").style.display = "none";
              if(document.getElementById("feed-comment-form-open-li_<?php echo $actionBaseId ?>")){
                document.getElementById("feed-comment-form-open-li_<?php echo $actionBaseId ?>").style.display = "block";}  
              //document.getElementById("<?php echo $this->commentForm->body->getAttrib('id') ?>").focus();
            }
            <?php if( Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.comment.like.box', 0)):?>$('comment-likes-ac_activityCommentstivityboox-item-<?php echo $action->action_id;?>').toggle();  <?php endif; ?>
            
        });
      })();
    </script>
    <?php if ($this->allowEdit && !empty($action->privacy) && in_array($action->getTypeInfo()->type, array("post", "post_self", "status", 'sitetagcheckin_add_to_map', 'sitetagcheckin_content', 'sitetagcheckin_status', 'sitetagcheckin_post_self', 'sitetagcheckin_post', 'sitetagcheckin_checkin', 'sitetagcheckin_lct_add_to_map')) && $this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id))): ?>


      <?php $privacy = $action->privacy; ?>
      <?php if (in_array($privacy, array("everyone", "networks", "friends", "onlyme"))): ?>
        <?php
        $privacy_icon_class = "aaf_icon_feed_" . $privacy;
        if (isset($this->privacyDropdownList[$privacy])):
          $privacy_titile = $this->privacyDropdownList[$privacy];
        endif;
        ?>
      <?php else: ?>
        <?php
        $privacy_array = explode(",", $privacy);
        foreach ($privacy_array as $value):
          if (isset($this->privacyDropdownList[$value])):
            $privacy_titile_array[] = $this->privacyDropdownList[$value];
          endif;
        endforeach;
        ?>
        <?php
        $privacy_icon_class = (count($privacy_titile_array) > 1) ? "aaf_icon_feed_custom" : "aaf_icon_feed_list";
        $privacy_titile = join(", ", $privacy_titile_array);
        ?>
      <?php endif; ?>
    <?php endif; ?>



    <?php // Icon, time since, action links ?>
    <?php
    $icon_type = 'activity_icon_' . $action->type;
    list($attachment) = $action->getAttachments();
    if (is_object($attachment) && $action->attachment_count > 0 && $attachment->item):
      $icon_type .= ' item_icon_' . $attachment->item->getType() . ' ';
    endif;
    $canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment') &&
            !empty($this->commentForm) );
    ?>

    <?php if (is_array($action->params) && isset($action->params['checkin']) && !empty($action->params['checkin'])): ?>
      <?php if (isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Page'): ?>
        <?php $icon_type = "item_icon_sitepage"; ?>
      <?php elseif (isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Business'): ?>
        <?php $icon_type = "item_icon_sitebusiness"; ?>
      <?php elseif (isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Sitegroup'): ?>
        <?php $icon_type = "item_icon_sitegroup"; ?>
      <?php elseif (isset($action->params['checkin']['type']) && $action->params['checkin']['type'] == 'Sitestore'): ?>
        <?php $icon_type = "item_icon_sitestore"; ?>
      <?php else: ?>
        <?php $icon_type = "item_icon_sitetagcheckin"; ?>
      <?php endif; ?>
    <?php endif; ?>

<div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>
              <ul>         
                <?php if ($canComment): ?>
                  <?php if($showAsLike):?>
                    <?php if ($action->likes()->isLike($this->viewer())): ?>
                      <li class="feed_item_option_unlike">              
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick' => 'javascript:en4.advancedactivity.unlike(this,' . $action->action_id . ');', 'action-title' => $this->translate('Like'))) ?>
                        <span>&#183;</span>
                      </li>
                    <?php else: ?>
                      <li class="feed_item_option_like nstcomment_wrap">              	
                        <?php
                        echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick' => 'javascript:en4.advancedactivity.like(this,' . $action->action_id . ');', 'action-title' => $this->translate('Unlike')))
                        ?>
                        <span>&#183;</span>
                      </li>
                    <?php endif; ?>
                  
                  <?php else :?>
                      <?php if (!$action->likes()->isLike($this->viewer())): ?>
                        <li class="feed_item_option_like nstcomment_wrap"> 

                            <?php if(!$showLikeWithoutIcon):?>     
                                <?php
                                echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick' => 'javascript:en4.advancedactivity.like(this,' . $action->action_id . ');', 'action-title' => '<img src="application/modules/Core/externals/images/loading.gif" />', 'class' => 'nstcomment_like'))
                                ?>
                            <span>&#183;</span>
                            <?php else:?>
                            
                                <?php if($showLikeWithoutIcon == 3):?>
                                <?php
                                //echo $this->htmlLink('javascript:void(0);', $this->translate('Vote up'), array('onclick' => 'javascript:en4.advancedactivity.like(this,' . $action->action_id . ');', 'action-title' => '<img src="application/modules/Core/externals/images/loading.gif" />'))
                                ?> 
                             
                                
                                <?php if ($action->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'activity-like', 'action_id' => $action->action_id, 'call_status' => 'public', 'showLikeWithoutIcon' => 3), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $action->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                                       
                                <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this, '<?php echo $action->action_id ?>');" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up.png" />
                                         </a>
                                <span>|</span>
                                <?php else:?>
                                 <?php
                                echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick' => 'javascript:en4.advancedactivity.like(this,' . $action->action_id . ');', 'action-title' => '<img src="application/modules/Core/externals/images/loading.gif" />'))
                                ?>
                                <span>&#183;</span>
                                <?php endif;?>
                            <?php endif;?>
                            
                        </li>
                      <?php else :?>
                         <li class="nstcomment_wrap feed_item_option_like"> 
                             
                             
                            <?php if($showLikeWithoutIcon != 3):?>     
                                <?php //SHOW ICON WITH LIKE?>
                                    <?php if(!$showLikeWithoutIcon):?>
                                        <img  src="application/modules/Nestedcomment/externals/images/like_light.png" />
                                    <?php endif;?>
                                <?php
                                //DISABLE LINK
                                echo $this->translate('Like');
                                ?>
                                <span>&#183;</span>
                            <?php else:?>
                              <?php if ($action->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'activity-like',  'action_id' => $action->action_id, 'call_status' => 'public', 'showLikeWithoutIcon' => 3), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $action->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                              <img  src="application/modules/Nestedcomment/externals/images/arrow-up_light.png" />
                              <span>|</span>
                            <?php endif;?>
                            
                        </li>
                      <?php endif;?>
                      
                       <?php if (!$action->dislikes()->isDislike($this->viewer())):?>
                        <li class="feed_item_option_unlike nstcomment_wrap">  
                            
                         <?php if(!$showLikeWithoutIcon):?>       
                         <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Dislike'), array('onclick' => 'javascript:en4.advancedactivity.unlike(this,' . $action->action_id . ');', 'action-title' => '<img src="application/modules/Core/externals/images/loading.gif" />', 'class' => 'nstcomment_unlike')); ?>
                         
                         <?php else:?>
                         
                          <?php if($showLikeWithoutIcon != 3):?>
                          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Dislike'), array('onclick' => 'javascript:en4.advancedactivity.unlike(this,' . $action->action_id . ');', 'action-title' => '<img src="application/modules/Core/externals/images/loading.gif" />')); ?>
                          <?php else:?>
                          <?php //echo $this->htmlLink('javascript:void(0);', $this->translate('Dislike'), array('onclick' => 'javascript:en4.advancedactivity.unlike(this,' . $action->action_id . ');', 'action-title' => '<img src="application/modules/Core/externals/images/loading.gif" />')); ?>
                          <?php if ($action->dislikes()->getDisLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'activity-dislike', 'action_id' => $action->action_id, 'call_status' => 'public', 'showLikeWithoutIcon' => 3), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $action->dislikes()->getDisLikeCount();?></a>
                                       <?php endif ?>
                           <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this, '<?php echo $action->action_id ?>');" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down.png" />
                                         </a>
                          
                          <?php endif;?>
                          
                         <?php endif;?>
                         <span>&#183;</span>
                       </li>
                      <?php else:?>
                        <li class="feed_item_option_unlike nstcomment_wrap"> 
                         
                            <?php if($showLikeWithoutIcon != 3):?>     
                                <?php //SHOW ICON WITH LIKE?>
                                    <?php if(!$showLikeWithoutIcon):?>
                                        <img  src="application/modules/Nestedcomment/externals/images/dislike_light.png" />
                                    <?php endif;?>
                                <?php
                                //DISABLE LINK
                                echo $this->translate('Dislike');
                                ?>
                            <?php else:?>
                              <?php if ($action->dislikes()->getDisLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'activity-dislike', 'action_id' => $action->action_id, 'call_status' => 'public', 'showLikeWithoutIcon' => 3), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $action->dislikes()->getDisLikeCount();?></a>
                                       <?php endif ?>
                              <img  src="application/modules/Nestedcomment/externals/images/arrow-down_light.png" title="<?php echo $this->translate('Vote down');?>"/>
                            <?php endif;?>
                          <span>&#183;</span>
                        </li>
                      <?php endif;?>
                      
                  <?php endif;?>
          <?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes   ?>
            <li class="feed_item_option_comment">               
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Comment'), array(
                  'class' => 'smoothbox', 'title' => $this->translate('Leave a comment')
              ))
              ?>
              <span>&#183;</span>
            </li>
          <?php else: ?>
            <li class="feed_item_option_comment">                
              <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array( 'onclick' =>'showCommentBox("' . $this->commentForm->getAttrib('id') . '", "' . $this->commentForm->body->getAttrib('id') . '"); 
document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "' . (($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
 if(document.getElementById("feed-comment-form-open-li_' . $actionBaseId . '")){
document.getElementById("feed-comment-form-open-li_' . $actionBaseId . '").style.display = "none";}  
document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();document.getElementById("' . "comment-likes-activityboox-item-$actionBaseId" . '").style.display = "block";', 'title' => $this->translate('Leave a comment'))) ?>
              <span>&#183;</span>
            </li>
          <?php endif; ?>
        <?php endif; ?>
        <?php if (in_array($action->getTypeInfo()->type, array('signup', 'friends', 'friends_follow'))): ?>    
          <?php $userFriendLINK = $this->aafUserFriendshipAjax($action); ?>
          <?php if ($userFriendLINK): ?>
            <li class="feed_item_option_add_tag"><?php echo $userFriendLINK; ?>
              <span>&#183;</span></li>  
          <?php endif; ?>
        <?php endif; ?>    
        <?php
        if ($this->viewer()->getIdentity() && (
                'user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) && $advancedactivityCoreApi->hasFeedTag($action)
        ):
          ?>
          <li class="feed_item_option_add_tag">             
            <?php
            echo $this->htmlLink(array(
                'route' => 'default',
                'module' => 'advancedactivity',
                'controller' => 'feed',
                'action' => 'tag-friend',
                'id' => $action->action_id
                    ), $this->translate('Tag Friends'), array('class' => 'smoothbox', 'title' =>
                $this->translate('Tag more friends')))
            ?>
            <span>&#183;</span>
          </li>
        <?php elseif ($this->viewer()->getIdentity() && $advancedactivityCoreApi->hasMemberTagged($action, $this->viewer())): ?>  
          <li class="feed_item_option_remove_tag">             
            <?php
            echo $this->htmlLink(array(
                'route' => 'default',
                'module' => 'advancedactivity',
                'controller' => 'feed',
                'action' => 'remove-tag',
                'id' => $action->action_id
                    ), $this->translate('Remove Tag'), array('class' => 'smoothbox'))
            ?>
            <span>&#183;</span>
          </li>
        <?php endif; ?>


        <?php // Share ?>
        <?php if ($action->getTypeInfo()->shareable && $action->shareable && $this->viewer()->getIdentity()): ?>
          <?php if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())): ?>
            <li class="feed_item_option_share">               
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' =>
                  $attachment->item->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', "not_parent_refresh" => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php elseif ($action->getTypeInfo()->shareable == 2): ?>
            <li class="feed_item_option_share">               
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $subject->getType(), 'id' =>
                  $subject->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', "not_parent_refresh" => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php elseif ($action->getTypeInfo()->shareable == 3): ?>
            <li class="feed_item_option_share">                
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $object->getType(), 'id' =>
                  $object->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php elseif ($action->getTypeInfo()->shareable == 4): ?>
            <li class="feed_item_option_share">                
              <?php
              echo $this->htmlLink(array('route' => 'default', 'module' => 'seaocore',
                  'controller' => 'activity', 'action' => 'share', 'type' => $action->getType(), 'id' =>
                  $action->getIdentity(), 'action_id' => $action->getIdentity(), 'format' => 'smoothbox', 'not_parent_refresh' => 1), $this->translate('ADVACADV_SHARE'), array('class' => 'smoothbox', 'title' => $this->translate('Share this by re-posting it with your own message.')))
              ?>
              <span>&#183;</span>
            </li>
          <?php endif; ?>
        <?php endif; ?>
          <?php if ($canComment && $this->aaf_comment_like_box): ?> 
                  <?php $likeCount = $action->likes()->getLikeCount(); ?>
                  <?php $commentCount = $action->comments()->getCommentCount() ?> 
                  <?php $dislikeCount = $action->dislikes()->getDislikePaginator()->getTotalItemCount(); ?>
                  <?php if ($likeCount || $commentCount || $dislikeCount): ?>
                    <li class="like_comment_counts" onclick="$('comment-likes-activityboox-item-<?php echo $actionBaseId ?>').toggle()">
                      <?php if ($likeCount): ?>
                        <span class="nstcomment_like"><?php echo $this->locale()->toNumber($likeCount); ?></span>
                      <?php endif; ?>
                      
                       <?php if ($dislikeCount && !$showAsLike): ?>
                        <span class="nstcomment_unlike"><?php echo $this->locale()->toNumber($dislikeCount); ?></span>
                      <?php endif; ?>
                      
                      <?php if ($commentCount): ?>
                        <span class="comment_icon"><?php echo $this->locale()->toNumber($commentCount); ?></span>
                      <?php endif; ?>
                      <span>&#183;</span>
                    </li>
                  <?php endif; ?>
                <?php endif; ?>
        <li>
          <?php echo $this->timestamp($action->getTimeValue()) ?>             
        </li>
        <?php if (!empty($privacy_icon_class) && !empty($privacy_titile)): ?>
          <li>
            <span>&#183;</span>
            <span class = "<?php echo $privacy_icon_class ?> feed_item_privacy">
              <p class="adv_item_privacy_tip">
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" alt="" />
                <?php echo $this->translate("Shared with %s", $this->translate($privacy_titile)) ?>
              </p>
            </span>            
          </li>
        <?php endif; ?>
      </ul>
    </div>


    <?php if (($action->getTypeInfo()->shareable && $action->shareable && ($share = $sharesTable->countShareOfItem(array('parent_action_id' => $action->getIdentity()))) > 0) || ($action->getTypeInfo()->commentable && $action->commentable)) : // Comments - likes -share    ?>
    <div class='comments' id='comment-likes-activityboox-item-<?php echo $actionBaseId ?>'>
        <ul class="seao_advcomment">  
          <?php // Share Count  ?>
          <?php if ($action->getTypeInfo()->shareable && $action->shareable && $share > 0): ?>       
            <li class="aaf_share_counts">
              <div></div>
              <div class="comments_likes">
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'advancedactivity', 'controller' => 'index', 'action' => 'share-item', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate(array('%s share', '%s shares', $share), $this->locale()->toNumber($share)), array('class' => 'smoothbox seaocore_icon_share aaf_commentbox_icon')) ?>
              </div>
            </li>
          <?php endif; ?>


          <?php if ($action->getTypeInfo()->commentable && $action->commentable): // Comments - likes -share ?>
          <?php if($showLikeWithoutIcon != 3) :?>
                    <?php $this->dislikes = $action->dislikes()->getDislikePaginator(); ?>
                    
                    <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0) || ($this->dislikes->getTotalItemCount() > 0 && !$showAsLike)): ?>
                    <li>
                    <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
                    
                        <span class="comments_likes">
                            <?php if ($action->likes()->getLike($this->viewer()) && $action->likes()->getLikeCount() == 1) :?>
                                <?php echo $this->translate(array('%s like this.', '%s likes this.', $action->likes()->getLikeCount()), $this->aafNCFluentList($action->likes()->getAllLikesUsers(), false, $action)) ?>
                            <?php else:?>
                                <?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->aafNCFluentList($action->likes()->getAllLikesUsers(), false, $action)) ?>
                            <?php endif;?>
                        </span>
                    <?php endif; ?>  
            
                    
                    <?php if ($this->dislikes->getTotalItemCount() > 0 && !$showAsLike):?>
                      <?php if($showDislikeUsers) :?>
                       <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)):?>
                         &nbsp;|&nbsp;
                         <?php endif;?>
                          <span class="comments_likes">
                              <?php if ($action->dislikes()->getDislike($this->viewer()) && $this->dislikes->getTotalItemCount() == 1):?>
                              <?php echo $this->translate(array('%s dislike this.', '%s dislikes this.', $this->dislikes->getTotalItemCount()), $this->aafFluentDisLikeList($action->dislikes()->getAllDislikesUsers(), false, $action)) ?>
                           <?php else:?>
                              <?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', $this->dislikes->getTotalItemCount()), $this->aafFluentDisLikeList($action->dislikes()->getAllDislikesUsers(), false, $action)) ?>
                           <?php endif;?>
                          </span>
                        <?php else:?>
                            <?php echo $this->translate(array('%s person dislikes this.', '%s people dislike this.', $this->dislikes->getTotalItemCount()), $this->locale()->toNumber($this->dislikes->getTotalItemCount()));?>
                        <?php endif;?>
                    <?php endif; ?>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?> 
            <?php $this->viewAllComments = false;?>        
            <?php if ($action->comments()->getCommentCount() > 0):?>
              <?php if ($action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
                <li>
                  <div></div>
                  <div class="comments_viewall" id="comments_viewall">
                    <?php if (0): ?>
                      <?php
                      echo $this->htmlLink($item->getHref(array('action_id' => $action->action_id, 'show_comments' => true)), $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())))
                      ?>
                    <?php else: ?>
                      <?php
                      echo $this->htmlLink('javascript:void(0);', $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()), $this->locale()->toNumber($action->comments()->getCommentCount())), array('onclick' => 'en4.advancedactivity.viewComments(' . $action->action_id . ');'))
                      ?>
                    <?php endif; ?>
                  </div>
                   <div style="display:none;" id="show_view_all_loading">
                     <img src="application/modules/Core/externals/images/loading.gif" alt="Loading" />
                   </div>
                </li>
              <?php endif; ?>
              
              <?php foreach ($action->getComments($this->viewAllComments) as $comment): ?>
              
              <?php
              $this->replyForm->setActionIdentity($comment->comment_id);
              $this->replyForm->comment_id->setValue($comment->comment_id);
              $this->replyForm->action_id->setValue($action->action_id);
              ?>
      
							<script type="text/javascript">
                  (function() {
        
                    en4.core.runonce.add(function()  { 
        
            <?php if ($this->onViewPage): ?>
                        (function() {
            <?php endif; ?>
                        if (!$('<?php echo $this->replyForm->body->getAttrib('id') ?>'))
                          return;
                        $('<?php echo $this->replyForm->body->getAttrib('id') ?>').autogrow();
                        
                       // en4.nestedcomment.nestedcomments.attachReply($('<?php echo $this->replyForm->getAttrib('id') ?>'), allowQuickReply);
        
                        //setTimeout(showReplyBox("<?php echo $this->replyForm->getAttrib('id')?>", "<?php echo $this->replyForm->body->getAttrib('id')?>"), 5000);
                        if (allowQuickReply == '1' && <?php echo $this->submitReply ? '1' : '0' ?>) {
                         // document.getElementById("<?php echo $this->replyForm->getAttrib('id') ?>").style.display = "";
                          //document.getElementById("<?php echo $this->replyForm->submit->getAttrib('id') ?>").style.display = "none";
                          if (document.getElementById("feed-reply-form-open-li_<?php echo $comment->comment_id ?>")) {
                            document.getElementById("feed-reply-form-open-li_<?php echo $comment->comment_id ?>").style.display = "block";
                          }
                          document.getElementById("<?php echo $this->replyForm->body->getAttrib('id') ?>").focus();
                        }
            <?php if ($this->onViewPage): ?>
                        }).delay(1000);
            <?php endif; ?>
                    });
                  })();
                </script>
        
                <li id="comment-<?php echo $comment->comment_id ?>" class="seao_nestcomment">
                    <?php
                                      if ($this->viewer()->getIdentity() &&
                                            (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                            ("user" == $comment->poster_type && $this->viewer()->getIdentity() == $comment->poster_id) ||
                                            ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($this->viewer())) ||
                                            $this->activity_moderate )):
                                        ?>
                	<span class="seaocore_replies_info_op">    	
                    <span class="seaocore_replies_pulldown">
                      <div class="seaocore_dropdown_menu_wrapper">
                        <div class="seaocore_dropdown_menu">
                          <ul>  
                            
                             <?php
                                      if ($this->viewer()->getIdentity() &&
                                            (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                            ("user" == $comment->poster_type && $this->viewer()->getIdentity() == $comment->poster_id) ||
                                            ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($this->viewer())) ||
                                            $this->activity_moderate )):
                                        ?>
                        <li>
                            <?php 
                             $attachMentArray  = array();
                             if (!empty($comment->attachment_type) && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                              <?php if($comment->attachment_type == 'album_photo'):?>
                                <?php $status = true; ?>
                                <?php $photo_id = $attachment->photo_id; ?>
                                <?php $album_id = $attachment->album_id; ?>
                                <?php $src = $attachment->getPhotoUrl(); ?>
                                <?php $attachMentArray = array("status" => $status, "photo_id"=> $photo_id , "album_id" => $attachment->album_id, "src" => $src);?>
                              <?php endif;?>
                            <?php endif;?>
                            <script type="text/javascript">  
                                en4.core.runonce.add(function() {
                                  commentAttachment.editComment['<?php echo $comment->comment_id ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($comment->body);?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
                                });
                                
                                
                            </script>
                             <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="en4.nestedcomment.nestedcomments.showCommentEditForm('<?php echo $comment->comment_id?>', '<?php echo ($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>');"><?php echo $this->translate('Edit'); ?>
                             </a>
                            </li>  
                            <?php endif ?>
                                                                      
                            
                            <?php
                                      if ($this->viewer()->getIdentity() &&
                                            (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                            ("user" == $comment->poster_type && $this->viewer()->getIdentity() == $comment->poster_id) ||
                                            ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($this->viewer())) ||
                                            $this->activity_moderate )):
                                        ?>
      
                                <li>
                              <?php /* echo $this->htmlLink(array(
                                'route'=>'default',
                                'module'    => 'advancedactivity',
                                'controller'=> 'index',
                                'action'    => 'delete',
                                'action_id' => $action->action_id,
                                'comment_id'=> $comment->comment_id,
                                ),'', array('class' => 'smoothbox
                                aaf_icon_remove','title'=>$this->translate('Delete Comment'))) */ ?>
                              <a href="javascript:void(0);" title="<?php echo $this->translate('Delete') ?>" onclick="deletefeed('<?php echo
                    $action->action_id ?>', '<?php echo $comment->comment_id ?>', '<?php
                    echo
                    $this->escape($this->url(array('route' => 'default',
                                'module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete')))
                              ?>')"><?php echo $this->translate('Delete') ?></a>
                                 </li>
                               <?php endif; ?>
                           
                          </ul>
                        </div>
                      </div>
                      <span class="seaocore_comment_dropbox"></span>
                    </span>
                  </span>
                    <?php endif; ?>       
                  <div class="comments_author_photo">
                    <?php
                    echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'sea_add_tooltip_link', 'rel' => $this->item($comment->poster_type, $comment->poster_id)->getType() . ' ' . $this->item($comment->poster_type, $comment->poster_id)->getIdentity())
                    )
                    ?>
                  </div>
                  <div class="comments_info">
                    <span class='comments_author'>
                      <?php
                      echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->item($comment->poster_type, $comment->poster_id)->getType() . ' ' . $this->item($comment->poster_type, $comment->poster_id)->getIdentity())
                      );
                      ?>
                    </span>
                    <span class="comments_body" id="comments_body_<?php echo $comment->comment_id ?>">
                      <?php 
                        include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_commentBody.tpl';
                     ?>
                    
                            <?php //echo $this->allowEmotionsIcon ? $this->smileyToEmoticons($this->viewMore($comment->body)) : $this->viewMore($comment->body); ?>
                          </span>
                      <div id="comment_edit_<?php echo $comment->comment_id ?>" class="mtop5 comment_edit" style="display: none;"><?php include APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/_editComment.tpl' ?>
                              </div>
                      
                        <?php if (!empty($comment->attachment_type) && null !== ($attachment = $this->item($comment->attachment_type, $comment->attachment_id))): ?>
                            <div class="seaocore_comments_attachment" id="seaocore_comments_attachment_<?php echo $comment->comment_id ?>">
                              <div class="seaocore_comments_attachment_photo">
                                <?php if (null !== $attachment->getPhotoUrl()): ?>
                                 <?php if (SEA_ACTIVITYFEED_LIGHTBOX && strpos($comment->attachment_type, '_photo')): ?>
                                      <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('onclick' => 'openSeaocoreLightBox("' . $attachment->getHref() . '");return false;')) ?>
                                       <?php else:?>
                                       <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                       <?php endif;?>
            <?php endif; ?>
                              </div>
                              <div class="seaocore_comments_attachment_info">
                                <div class="seaocore_comments_attachment_title">
            <?php echo $this->htmlLink($attachment->getHref(array('message' => $comment->comment_id)), $attachment->getTitle()) ?>
                                </div>
                                <div class="seaocore_comments_attachment_des">
            <?php echo $attachment->getDescription() ?>
                                </div>
                              </div>
                            </div>
                        <?php endif; ?>	
    
                    <ul class="comments_date">
                      <?php  if ($canComment):?>
                       <?php if($showAsNested):?>
                          <li class="feed_item_option_comment">            
                            <?php
                                echo $this->htmlLink('javascript:void(0);', $this->translate('Reply'), array('onclick' => ' showReplyBox("' . $this->replyForm->getAttrib('id') . '", "' . $this->replyForm->body->getAttrib('id') . '"); 
                  document.getElementById("' . $this->replyForm->submit->getAttrib('id') . '").style.display = "' . (($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
                  if(document.getElementById("feed-reply-form-open-li_' . $comment->comment_id . '")){
                  document.getElementById("feed-reply-form-open-li_' . $comment->comment_id . '").style.display = "none";}  
                  document.getElementById("' . $this->replyForm->body->getAttrib('id') . '").focus();document.getElementById("' . "comment-likes-activityboox-item-$actionBaseId" . '").style.display = "block"; ', 'title' =>
                                    $this->translate('Leave a reply')))
                                  ?>

                            </li>
                            <?php endif;?>
                        <?php if($showAsLike):?>
                        
                            <?php  $isLiked = $comment->likes()->isLike($this->viewer());?>
                            <li class="comments_like"> 
                            <?php if($showAsNested):?>
                                   &#183;
                                <?php endif;?>
                              <?php if (!$isLiked): ?>
                                <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title="<?php echo $this->translate('unlike') ?>">
                                  <?php echo $this->translate('like') ?>
                                </a>
                              <?php else: ?>
                                <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title="<?php echo $this->translate('like') ?>">
                                  <?php echo $this->translate('unlike') ?>
                                </a>
                              <?php endif ?>
                            </li>
                        <?php else:?>
                        
                            <?php  $isLiked = $comment->likes()->isLike($this->viewer());?>
                                
                                    <?php if (!$isLiked): ?>
                                        <li class="comments_like"> 
                                       <?php if($showAsNested):?>
                                   &#183;
                                <?php endif;?>
                                        <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a class='nstcomment_like' href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a class='nstcomment_like' href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>    
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                          <?php if ($comment->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $comment->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                                         <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up.png" />
                                         </a>
                                         <?php endif;?>
                                        </li>
                                    <?php else: ?>
                                        <li class="comments_like nstcomment_wrap"> 
                                    <?php if($showAsNested):?>
                                   &#183;
                                <?php endif;?>
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <img src="application/modules/Nestedcomment/externals/images/like_light.png" />
                                      <?php echo $this->translate('like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <img src="application/modules/Nestedcomment/externals/images/like_light.png" />
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $comment->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                                       <img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up_light.png" />
                                    <?php endif;?>
                                        </li>
                                    <?php endif;?>
                                    
                                <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($comment, $this->viewer())?>
                                 
                                    <?php if (!$isDisLiked): ?>
                                       <li class="comments_unlike"> 
                                        &#183; 
                                         <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>    
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                         
                                         <?php if(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) && !$showAsLike):?>
                                          <?php if($showDislikeUsers) :?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )?></a>

                                            <?php else:?>
                                                <?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )?>
                                            <?php endif;?>   
                                        <?php endif;?>
                                         <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down.png" />
                                         </a>
                                         <?php endif;?>
                                       </li>
                                    <?php else: ?>
                                        <li class="comments_unlike nstcomment_wrap"> 
                                    &#183;  
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <img src="application/modules/Nestedcomment/externals/images/dislike_light.png" />
                                      <?php echo $this->translate('dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <img src="application/modules/Nestedcomment/externals/images/dislike_light.png" />
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                       <?php if(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) && !$showAsLike):?>
                                          <?php if($showDislikeUsers) :?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                               <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )?></a>
                                            <?php else:?>
                                                <?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )?>
                                            <?php endif;?>
                                        <?php endif;?>
                                       <img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down_light.png" />
                                    <?php endif;?>
                                        </li>
                                    <?php endif;?>
                                
                        <?php endif;?>
                        
                      <?php endif ?>
                      
                      <?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if ($comment->likes()->getLikeCount() > 0): ?>
                              <li class="comments_likes_total"> 
                               <?php if($canComment || $this->viewer()->getIdentity()) :?>
                                    &#183;
                                   <?php endif;?> 
                                <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s likes this.', '%s like this.', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount()));?></a>
                      
                              </li>
                            <?php endif ?>
                            
                            <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0 && !$showAsLike): ?>
                              <li class="comments_likes_total"> 
                               <?php if($canComment || $this->viewer()->getIdentity()) :?>
                                    &#183;
                                   <?php endif;?> 
                              <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $comment->getType(), 'resource_id' => $comment->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                              <?php if($showDislikeUsers) :?>
                              <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )))?></a>
                              <?php else:?>
                                <?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )))?>
                              <?php endif;?>
                              </li>
                              <?php endif ?>
                            <?php endif ?>
                            <li class="comments_timestamp">
                                <?php if ((Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0 && !$showAsLike) || ($comment->likes()->getLikeCount() > 0) || ($this->viewer()->getIdentity() && $canComment)): ?>
                                    &#183;
                                <?php endif ?>
                              <?php echo $this->timestamp($comment->creation_date); ?>
                            </li>
                        </ul>
                        
                        <?php if($showAsNested && count($action->getReplies($comment->comment_id))):?>

                    <a <?php if($this->hideReply):?> style="display:none;" <?php else:?> style="display:block;" <?php endif;?>  id="replies_show_<?php echo $comment->comment_id ?>" class="fleft f_small buttonlink activity_icon_reply_seaocore_reply comments_viewall mtop5" href="javascript:void(0);" onclick="en4.nestedcomment.nestedcomments.loadCommentReplies('<?php echo $comment->comment_id;?>');"><?php echo $this->translate(array("View %s Reply", "View %s Replies", count($action->getReplies($comment->comment_id))), count($action->getReplies($comment->comment_id)));?></a>
                  
                    <a  <?php if($this->hideReply):?> style="display:block;" <?php else:?> style="display:none;" <?php endif;?> id="replies_hide_<?php echo $comment->comment_id ?>" class="fleft f_small buttonlink activity_icon_reply_seaocore_reply comments_viewall mtop5 mbot5" href="javascript:void(0);" onclick="en4.nestedcomment.nestedcomments.hideCommentReplies('<?php echo $comment->comment_id;?>');"><?php echo $this->translate(array("Hide %s Reply", "Hide %s Replies", count($action->getReplies($comment->comment_id))), count($action->getReplies($comment->comment_id)));?></a>
                  <?php endif;?>
                  </div>

                    
                  <div class="comments">
                    <ul class="seao_reply">
                         <?php if($showAsNested):?>  
                      <?php foreach ($action->getReplies($comment->comment_id) as $reply): ?>
                        <li id="reply-<?php echo $reply->comment_id ?>" class="reply<?php echo $comment->comment_id;?>" <?php if($this->hideReply):?> style="display:inline-block;" <?php else:?> style="display:none;" <?php endif;?>>
                             <?php
                                      if ($this->viewer()->getIdentity() &&
                                            (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                            ("user" == $reply->poster_type && $this->viewer()->getIdentity() == $reply->poster_id) ||
                                            ("user" !== $reply->poster_type && Engine_Api::_()->getItemByGuid($reply->poster_type . "_" . $reply->poster_id)->isOwner($this->viewer())) ||
                                            $this->activity_moderate )):
                                        ?>
                        	<span class="seaocore_replies_info_op">
                            <span class="seaocore_replies_pulldown">
                              <div class="seaocore_dropdown_menu_wrapper">
                                <div class="seaocore_dropdown_menu">
                                  <ul>  
                                    <li>
                                    <?php 
                                       $attachMentArray  = array();
                                       if (!empty($reply->attachment_type) && null !== ($attachment = $this->item($reply->attachment_type, $reply->attachment_id))): ?>
                                        <?php if($reply->attachment_type == 'album_photo'):?>
                                          <?php $status = true; ?>
                                          <?php $photo_id = $attachment->photo_id; ?>
                                          <?php $album_id = $attachment->album_id; ?>
                                          <?php $src = $attachment->getPhotoUrl(); ?>
                                          <?php $attachMentArray = array('status' => $status, 'photo_id' => $photo_id , 'album_id' => $attachment->album_id, 'src' => $src);?>
                                          <?php endif;?>
                                      <?php endif;?>
                                      
                                      <script type="text/javascript">  
                                        en4.core.runonce.add(function() {
                                          replyAttachment.editReply['<?php echo $reply->comment_id ?>'] = { 'body': '<?php echo $this->string()->escapeJavascript($reply->body);?>', 'attachment_body':<?php echo Zend_Json_Encoder::encode($attachMentArray);?>}
                                        });
                                      </script>
                                     <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="en4.nestedcomment.nestedcomments.showReplyEditForm('<?php echo $reply->comment_id?>', '<?php echo ($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? 0 : 1; ?>');"><?php echo $this->translate('Edit'); ?>
                                     </a>	
                                    </li>                                            
                                    
                                    <?php
                                      if ($this->viewer()->getIdentity() &&
                                            (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                            ("user" == $reply->poster_type && $this->viewer()->getIdentity() == $reply->poster_id) ||
                                            ("user" !== $reply->poster_type && Engine_Api::_()->getItemByGuid($reply->poster_type . "_" . $reply->poster_id)->isOwner($this->viewer())) ||
                                            $this->activity_moderate )):
                                        ?>
        <li>
                                      <?php /* echo $this->htmlLink(array(
                                        'route'=>'default',
                                        'module'    => 'advancedactivity',
                                        'controller'=> 'index',
                                        'action'    => 'delete',
                                        'action_id' => $action->action_id,
                                        'comment_id'=> $reply->comment_id,
                                        ),'', array('class' => 'smoothbox
                                        aaf_icon_remove','title'=>$this->translate('Delete Reply'))) */ ?>
                                      <a href="javascript:void(0);" title="<?php echo $this->translate('Delete') ?>" onclick="deletereply('<?php echo $action->action_id ?>', '<?php echo $reply->comment_id ?>', '<?php echo $this->escape($this->url(array('route' => 'default', 'module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete'))) ?>')"><?php echo $this->translate('Delete') ?></a>
                                      </li>
                                       <?php endif; ?>
                                  </ul>
                                </div>
                              </div>
                              <span class="seaocore_comment_dropbox"></span>
                            </span>
                          </span>
                          <?php endif;?>
                          <div class="comments_author_photo">
                            <?php
                            echo $this->htmlLink($this->item($reply->poster_type, $reply->poster_id)->getHref(), $this->itemPhoto($this->item($reply->poster_type, $reply->poster_id), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'sea_add_tooltip_link', 'rel' => $this->item($reply->poster_type, $reply->poster_id)->getType() . ' ' . $this->item($reply->poster_type, $reply->poster_id)->getIdentity())
                            )
                            ?>
                          </div>
                          <div class="comments_info">
                            <span class='comments_author'>
                              <?php
                              echo $this->htmlLink($this->item($reply->poster_type, $reply->poster_id)->getHref(), $this->item($reply->poster_type, $reply->poster_id)->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $this->item($reply->poster_type, $reply->poster_id)->getType() . ' ' . $this->item($reply->poster_type, $reply->poster_id)->getIdentity())
                              );
                              ?>
                            </span>
                            <span class="comments_body" id="reply_body_<?php echo $reply->comment_id ?>">
                                <?php 
                                    include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_replyBody.tpl';
                                ?>
                            </span>
                            <div id="reply_edit_<?php echo $reply->comment_id ?>" style="display: none;" class="reply_edit"><?php include APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/_editReply.tpl' ?>
                            </div>
                              
                            <?php if (!empty($reply->attachment_type) && null !== ($attachment = $this->item($reply->attachment_type, $reply->attachment_id))): ?>
                              <div class="seaocore_comments_attachment" id="seaocore_comments_attachment_<?php echo $comment->comment_id ?>">
                                <div class="seaocore_comments_attachment_photo">
                                  <?php if (null !== $attachment->getPhotoUrl()): ?>
                                   <?php if (SEA_ACTIVITYFEED_LIGHTBOX && strpos($reply->attachment_type, '_photo')): ?>
                                        <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('onclick' => 'openSeaocoreLightBox("' . $attachment->getHref() . '");return false;')) ?>
                                         <?php else:?>
                                         <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo'))) ?>
                                         <?php endif;?>
              <?php endif; ?>
                                </div>
                                <div class="seaocore_comments_attachment_info">
                                  <div class="seaocore_comments_attachment_title">
              <?php echo $this->htmlLink($attachment->getHref(array('message' => $reply->comment_id)), $attachment->getTitle()) ?>
                                  </div>
                                  <div class="seaocore_comments_attachment_des">
              <?php echo $attachment->getDescription() ?>
                                  </div>
                                </div>
                              </div>
                            <?php endif; ?>
                            <ul class="comments_date">
                             
                              <?php if ($canComment):?>
                                      
                                      <?php if($showAsLike):?>
                                            <?php $isLiked = $reply->likes()->isLike($this->viewer());?>
                                            <li class="comments_like"> 
                                              
                                              <?php if (!$isLiked): ?>
                                                <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title="<?php echo $this->translate('unlike') ?>">
                                                  <?php echo $this->translate('like') ?>
                                                </a>
                                              <?php else: ?>
                                                <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title="<?php echo $this->translate('like') ?>">
                                                  <?php echo $this->translate('unlike') ?>
                                                </a>
                                              <?php endif ?>
                                            </li>
                                      <?php else:?>
                                      
                                      <?php $isLiked = $reply->likes()->isLike($this->viewer());?> 
                                         <?php if(!$isLiked) :?>
                                             <li class="comments_like"> 
                                        
                                        <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a class='nstcomment_like' href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('like') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a class='nstcomment_like' href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>    
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                        <?php if ($reply->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $reply->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                                         <a href="javascript:void(0)" onclick="en4.advancedactivity.like(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up.png" />
                                         </a>
                                         <?php endif;?>
                                        </li>
                                       <?php else:?>
                                                <li class="comments_like nstcomment_wrap"> 
                                    
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <img src="application/modules/Nestedcomment/externals/images/like_light.png" />
                                      <?php echo $this->translate('like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('like') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <img src="application/modules/Nestedcomment/externals/images/like_light.png" />
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                        <?php if ($reply->likes()->getLikeCount() > 0): ?>
                                            <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                             <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $reply->likes()->getLikeCount();?></a>
                                       <?php endif ?>
                                       <img title="<?php echo $this->translate('Vote up');?>" src="application/modules/Nestedcomment/externals/images/arrow-up_light.png" />
                                    <?php endif;?>
                                        </li>
                                    <?php endif;?>
                                       <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($reply, $this->viewer())?>
                                       <?php if(!$isDisLiked) :?>
                                       <li class="comments_unlike"> 
                                        &#183; 
                                        <?php if($showLikeWithoutIconInReplies == 0):?>     
                                        <a class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                        <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <?php echo $this->translate('dislike') ?>
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                         <a class='nstcomment_unlike' href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>    
                                         </a>
                                         <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                         <?php if(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply ) && !$showAsLike):?>
                                       <?php if($showDislikeUsers) :?>
                                          <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )?></a>
                                                <?php else:?>
                                                    <?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )?>
                                                 <?php endif;?>
                                        <?php endif;?>
                                         <a href="javascript:void(0)" onclick="en4.advancedactivity.unlike(this,<?php echo sprintf("'%d', %d", $action->getIdentity(), $reply->getIdentity()) ?>);" action-title='<img src="application/modules/Core/externals/images/loading.gif" />'>
                                           <img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down.png" />
                                         </a>
                                         <?php endif;?>
                                        </li>
                                      <?php else:?>
                                        <li class="comments_unlike nstcomment_wrap">
                                        
                                         &#183;  
                                    <?php if($showLikeWithoutIconInReplies == 0):?> 
                                      <img src="application/modules/Nestedcomment/externals/images/dislike_light.png" />
                                      <?php echo $this->translate('dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 1):?>
                                       <?php echo $this->translate('dislike') ?>
                                    <?php elseif($showLikeWithoutIconInReplies == 2):?>
                                      <img src="application/modules/Nestedcomment/externals/images/dislike_light.png" />
                                    <?php elseif($showLikeWithoutIconInReplies == 3):?>
                                      <?php if(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply ) && !$showAsLike):?>
                                       <?php if($showDislikeUsers) :?>
                                          <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )?></a>
                                                <?php else:?>
                                                <?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )?>
                                                <?php endif;?>
                                        <?php endif;?>
                                       <img title="<?php echo $this->translate('Vote down');?>" src="application/modules/Nestedcomment/externals/images/arrow-down_light.png" />
                                    <?php endif;?>
                                        </li>
                                       <?php endif;?>
                                      
                                      <?php endif;?>
                                    <?php endif ?>
                                   <?php if($showLikeWithoutIconInReplies != 3):?>
                                        <?php if ($reply->likes()->getLikeCount() > 0): ?>
                                          <li class="comments_likes_total"> 
                                            <?php if($canComment || $this->viewer()->getIdentity()) :?>
                                    &#183;
                                   <?php endif;?>
                                           <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'likelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                      <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s likes this.', '%s like this.', $reply->likes()->getLikeCount()), $this->locale()->toNumber($reply->likes()->getLikeCount()));?></a>

                                          </li>
                                        <?php endif ?>

                                        <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply ) > 0 && !$showAsLike): ?>
                                            <li class="comments_likes_total"> 
                                              <?php if($canComment || $this->viewer()->getIdentity()) :?>
                                    &#183;
                                   <?php endif;?>
                                              <?php if($showDislikeUsers) :?>
                                                <?php $url = $this->url(array('module' => 'nestedcomment', 'controller' => 'like', 'action' => 'dislikelist', 'resource_type' => $reply->getType(), 'resource_id' => $reply->getIdentity(), 'call_status' => 'public'), 'default', true);?>
                                                <a href="javascript:void(0);" onclick="Smoothbox.open('<?php echo $url;?>')"><?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )))?></a>
                                                <?php else:?>
                                                    <?php echo $this->translate(array('%s dislikes this.', '%s dislike this.', Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )), $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply )))?>
                                                <?php endif;?>
                                            </li>
                                        <?php endif ?>
                                    <?php endif ?>
                                     <li class="comments_timestamp"> 
                                         <?php if ((Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $reply ) > 0 && !$showAsLike) || ($reply->likes()->getLikeCount() > 0) || ($this->viewer()->getIdentity() && $canComment)): ?>
                                    &#183;
                                <?php endif ?>
                                      <?php echo $this->timestamp($reply->creation_date); ?>
                                    </li>
                            </ul>
                          </div>
                      	</li>
                      <?php endforeach;?>
                      <?php endif ?>
                    </ul>
                  </div>
									
                </li> 
                
                <?php if ($canComment && $showAsNested): ?>
                 <?php $replyFormId = $this->replyForm->getAttrib('id');?>
                 <?php $replyFormBodyId = $this->replyForm->body->getAttrib('id');?>
                      	<li id='feed-reply-form-open-li_<?php echo $comment->comment_id ?>' onclick='showReplyBox("<?php echo $replyFormId?>", "<?php echo $replyFormBodyId?>");' <?php echo '
document.getElementById("' . $this->replyForm->submit->getAttrib('id') . '").style.display = "' . (($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
document.getElementById("feed-reply-form-open-li_' . $comment->comment_id . '").style.display = "none";
  document.getElementById("' . $this->replyForm->body->getAttrib('id') . '").focus();' ?>' <?php if (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): ?> style="display:none;"<?php endif; ?> style="display:none;">                  				<div></div>
                          <div class="seaocore_comment_box seaocore_txt_light"><?php echo $this->translate('Write a reply...') ?></div>
                        </li>
                <?php endif;?>
                
                <?php if ($canComment && $showAsNested) echo $this->replyForm->render();?>
                
                <?php endforeach; ?>
                <?php if ($canComment): ?>
                          <?php $commentFormId = $this->commentForm->getAttrib('id');?>
                          <?php $commentFormBodyId = $this->commentForm->body->getAttrib('id');?>
                            <li id='feed-comment-form-open-li_<?php echo $actionBaseId ?>' onclick='<?php echo 'document.getElementById("' . $this->commentForm->getAttrib('id') . '").style.display = "";
    document.getElementById("' . $this->commentForm->submit->getAttrib('id') . '").style.display = "' . (($this->isMobile || !$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) ? "block" : "none") . '";
    document.getElementById("feed-comment-form-open-li_' . $actionBaseId . '").style.display = "none";
      document.getElementById("' . $this->commentForm->body->getAttrib('id') . '").focus();' ?> showCommentBox("<?php echo $commentFormId?>", "<?php echo $commentFormId?>");' <?php if (!$this->commentShowBottomPost || Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): ?> style="display:none;"<?php endif; ?> >                  <div></div>
                              <div class="seaocore_comment_box seaocore_txt_light"><?php echo $this->translate('Write a comment...') ?></div></li>
                        <?php endif; ?>     
              <?php endif; ?>
            <?php endif; ?>  
        </ul>
        <?php if ($canComment) echo $this->commentForm->render(); ?>
      </div>
    <?php endif; ?>
    <?php
    ob_end_flush();
  } catch (Exception $e) {
    ob_end_clean();
    if (APPLICATION_ENV === 'development') {
      echo $e->__toString();
    }
  };
endforeach;
?>


<?php if($smiliesEnabled):?>
<style type="text/css">
	.seao_advcomment + form .compose-body #compose-photo-form-fancy-file,
	.seao_advcomment .compose-body #compose-photo-form-fancy-file{
		right: 23px;
	}
</style>
<?php endif;?>