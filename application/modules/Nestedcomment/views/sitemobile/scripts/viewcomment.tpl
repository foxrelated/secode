<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: viewcomment.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
  $this->headTranslate(array(
     'Write a comment...',
     'Unlike',
     '% likes this',
     'like',    
     '% like',
     'Like'
   
  ));
?>

<?php //GET VERSION OF SITEMOBILE APP.
  $RemoveClassDone = true;
  if(Engine_Api::_()->sitemobile()->isApp()) {
    if(!Engine_Api::_()->nestedcomment()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemobileapp')->version, '4.8.6p1'))
      $RemoveClassDone = false;
  } 
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
?>
<?php
$action = $this->action;

$canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
        !empty($this->commentForm) );
?>



<div id='comment-activity-item-<?php echo $action->action_id ?>' class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper" style="<?php echo $this->translate(($action->commentable) ? 'display:block' : 'display:block;') ?>">

  <?php if ($action->getTypeInfo()->commentable): // Comments - likes  ?>
  
    <div class="" id="showhide-comments-<?php echo $action->action_id ?>" style="display:block">
      <?php $commentCount = $action->getComments($this->viewAllComments, true);
        if(!$commentCount)
            $commentCount = 0;
      ?>
      <div class="sm-ui-popup-top ui-header ui-bar-a">
        <a href="javascript:void(0);" data-role="button" data-iconpos="notext" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ui-btn-right ps-close-popup"></a>
        <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate(array('%s comment', '%s comments', $commentCount), $this->locale()->toNumber($commentCount)); ?></h2>
      </div>
         
      <div class="sm-ui-popup-container" style="bottom:95px;">
        <div class="comments">
          <ul class="viewcomment">
            <?php if ($action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers()) > 0)): ?>
              <li class="" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-activity-item-' + <?php echo $action->action_id ?>).css('display', 'block');">
                
                <?php if($showAsLike) {
                        $showLikeWithoutIcon=1;
                     }    
                    if($showLikeWithoutIcon != 3 ):?>
                    <a href="javascript:void(0);">
                    	<i class="ui-icon ui-icon-thumbs-up-alt"></i>
                      <?php echo $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount())) ?>                            
                    </a>
                <?php else:?>
                    <a href="javascript:void(0);">
                      <i class="ui-icon ui-icon-chevron-up"></i>
                      <?php echo $this->translate(array('%s person voted up this', '%s people voted up this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount())) ?>                            
                    </a>
                <?php endif;?>
                <a href="javascript:void(0);"  class="comment_likes ui-link-inherit fright">
                  <i class="ui-icon icon-right fright ui-icon-arrow-r"></i>
                </a>

              </li>	
            <?php endif; ?>

            <?php if(!$showAsLike):?> 
                <?php if ($action->dislikes()->getDisLikeCount() > 0 && (count($action->dislikes()->getAllDisLikesUsers()) > 0)): ?>
                  <?php if($showDislikeUsers):?> 
                    <li class="comments_dislikes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#dislike-activity-item-' + <?php echo $action->action_id ?>).css('display', 'block');">
                      <a href="javascript:void(0);">
                      
                        <?php if($showLikeWithoutIcon != 3):?>  
                          <i class="ui-icon ui-icon-thumbs-down-alt"></i>
                            <?php echo $this->translate(array('%s person dislikes this', '%s people dislike this', $action->dislikes()->getDisLikeCount()), $this->locale()->toNumber($action->dislikes()->getDisLikeCount())) ?>   
                          <?php else:?>
                          <i class="ui-icon ui-icon-chevron-down"></i>
                          <?php echo $this->translate(array('%s person voted down this', '%s people voted down this', $action->dislikes()->getDisLikeCount()), $this->locale()->toNumber($action->dislikes()->getDisLikeCount())) ?> 
                          <?php endif;?>
                      </a>
                      <a href="javascript:void(0);"  class="comment_dislikes ui-link-inherit fright">
                        <i class="ui-icon icon-right fright ui-icon-arrow-r"></i>
                      </a>
                    </li>	
                  <?php else:?>
                    <li class="comments_dislikes" onclick="">
                      <i class="ui-icon ui-icon-thumbs-down-alt"></i>
                      <?php if($showLikeWithoutIcon != 3):?>  
                        <?php echo $this->translate(array('%s person dislikes this', '%s people dislike this', $action->dislikes()->getDisLikeCount()), $this->locale()->toNumber($action->dislikes()->getDisLikeCount())) ?> 
                      <?php else:?>
                      <?php echo $this->translate(array('%s person voted down this', '%s person voted down this', $action->dislikes()->getDisLikeCount()), $this->locale()->toNumber($action->dislikes()->getDisLikeCount())) ?> 
                      <?php endif;?>
                    </li>	
                  <?php endif;?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($action->getComments($this->viewAllComments, true) > 5 || $this->viewAllComments): ?>
              <li class="comments_likes" onclick="sm4.activity.getOlderComments(this, '<?php echo $action->getObject()->getType() ?>', '<?php echo $action->getObject()->getIdentity() ?>', '2', '<?php echo $action->action_id; ?>');">
                <a href="javascript:void(0);" ><?php echo $this->translate('Load Previous Comments') ?></a>
              </li>
            <?php endif; ?>
            <?php if ($action->getComments($this->viewAllComments, true) > 0): ?>
              <?php foreach ($action->getComments($this->viewAllComments) as $comment): ?>
              
                <li id="comment-<?php echo $comment->comment_id ?>">
                   
                    <div id="comment_information-<?php echo $comment->comment_id ?>" style="display:block"> 
                        <?php if ($this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ($this->viewer()->getIdentity() == $comment->poster_id) || $this->activity_moderate )): ?>
                            <div class="feed_items_options_btn">      
                              <a href="javascript:void(0);" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideDown(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideUp(500);" data-role="button" data-icon="carat-d" data-iconpos="notext" data-theme="c" data-inline="true" class="ui-link ui-btn ui-btn-c ui-icon-carat-d ui-btn-icon-notext ui-btn-inline ui-shadow ui-corner-all" role="button"></a>
                            </div>  
                        <?php endif;?>
                    <div class="comments_author_photo">
                      <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())) ?>
                    </div>
                  <div class="comments_info">
                    <div class='comments_author'>
                      <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
                    </div>
                    <?php $item = $comment;?>
                    <div class="comments_body">
                     <?php 
                        include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_commentBody.tpl';
                     ?> 
                    </div>
                      <?php if (!empty($item->attachment_type) && null !== ($attachment = $this->item($item->attachment_type, $item->attachment_id))): ?>
                      <div class="seaocore_comments_attachment" id="seaocore_comments_attachment">
                        <div class="seaocore_comments_attachment_photo">
                          <?php if (null !== $attachment->getPhotoUrl()): ?>
                           <?php echo $this->htmlLink($attachment->getHref(), $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle(), array('class' => 'thumbs_photo')), array('data-linktype'=> 'photo-gallery')) ?>
                           <?php endif; ?>
                        </div>
                      </div>
                      <?php endif; ?>	
                      
                    <div class="comments_date">
                      <?php echo $this->timestamp($comment->creation_date); ?>  
                        
                       <span class="sep">-</span>
                      <?php if($showAsNested):?>
                         
                        <?php
                        if ($canComment):?>
                          <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewreply', 'action_id' => $action->getIdentity(),'writereply'=>'true', 'comment_id' => $comment->comment_id), 'default', 'true'); ?>" , "replypopup", <?php echo $action->getIdentity();?>)'>
                              <span><?php echo $this->translate('Reply'); ?></span>
                          </a>   
                          -
                        <?php endif;?> 
                      <?php endif;?> 
                        
                      <?php if($showAsLike):?>
                      <?php
                      if ($canComment):
                        $isLiked = $comment->likes()->isLike($this->viewer());
                        ?>
                        <?php if (!$isLiked): ?>
                          <a href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"> <?php echo $this->translate('Like') ?></a> 
                        <?php else: ?>
                          <a href="javascript:void(0)" onclick="sm4.activity.unlike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"><?php echo $this->translate('Unlike') ?>
                          </a> 
                        <?php endif ?>
                      <?php endif ?>
                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                        <span class="sep">-</span> 
                        <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.comment_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">
                          <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
<!--                            <span class="ui-icon ui-icon-thumbs-up-alt"><?php //echo $comment->likes()->getLikeCount();?></span>-->
                        </a>
                      <?php endif; ?>
                      <?php else:?>
                      
                      <?php if($canComment):?>
                          <?php $isLiked = $comment->likes()->isLike($this->viewer());?>
                          <?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if(!$isLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
                            <?php else:?>
                               <a href="javascript:void(0)" class="comment_likes" onclick="sm4.activity.changeLikeDislikeColor()"><?php echo $this->translate('Like') ?></a> 
                               <span class="sep">-</span> 
                            <?php endif;?>
                          <?php else:?>
                            <?php if ($comment->likes()->getLikeCount() > 0): ?>
                                <?php if($showLikeWithoutIconInReplies == 3):?>
                                    <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.comment_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">    
                                        <span><?php echo $comment->likes()->getLikeCount(); ?></span>
                                    </a>
                                <?php endif;?> 
                            <?php endif ?>
                      
                            <?php if(!$isLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.activity.like(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_likes ui-icon ui-icon-angle-up"></a> <span class="sep">-</span> 
                            <?php else:?>
                               <a href="javascript:void(0)" class="comment_likes ui-icon ui-icon-angle-up" onclick="sm4.activity.changeLikeDislikeColor()"></a> 
                               <span class="sep">-</span> 
                            <?php endif;?>
                          <?php endif;?>
                       <?php endif;?>
                       
                       <?php if($canComment):?>
                         
                          <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($comment, $this->viewer());?>
                          
                          <?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if(!$isDisLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.activity.dislike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_dislikes"> <?php echo $this->translate('Dislike') ?></a> 
                              
                              <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                              <span class="sep">-</span> 
                              <?php endif;?>
                            <?php else:?>
                              <a href="javascript:void(0)" class="comment_dislikes" onclick="sm4.activity.changeLikeDislikeColor()"> <?php echo $this->translate('Dislike') ?> </a>
                                  <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                    <span class="sep">-</span> 
                                  <?php endif;?>
                                  
                                  
                            <?php endif;?>
                          <?php else:?>
                          
                             <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                <?php if($showLikeWithoutIconInReplies == 3):?>
                                    <a href="javascript:void(0);" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>" class="comments_comment_dislikes" <?php if($showDislikeUsers):?>  onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#dislike-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.comment_dislikes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)" <?php endif;?>>
                                      
                                        <span><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></span>
                                    </a>
                                <?php endif;?>
                             <?php endif ?>
                        
                             <?php if(!$isDisLiked):?>
                              <a href="javascript:void(0)" onclick="sm4.activity.dislike(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="comment_dislikes ui-icon ui-icon-angle-down"></a> 
                            <?php else:?>
                              <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="comment_dislikes ui-icon ui-icon-angle-down"></a>
                            <?php endif;?>
                          <?php endif;?>
                       <?php endif;?>
                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                        <?php if($showLikeWithoutIconInReplies != 3):?>
                            <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.comment_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">
                              <span class="ui-icon ui-icon-thumbs-up-alt"><?php echo $comment->likes()->getLikeCount(); ?></span>
                            </a>  
                                  <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                    <span class="sep">-</span> 
                                  <?php endif;?>
                        <?php endif;?> 
                        
                      <?php endif; ?>
                      
                        <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                            <?php if($showLikeWithoutIconInReplies != 3):?>
                                <a href="javascript:void(0);" id="comments_comment_dislikes_<?php echo $comment->comment_id ?>" class="comments_comment_dislikes" <?php if($showDislikeUsers):?>onclick="$('#comment-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#dislike-comment-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.comment_dislikes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)" <?php endif;?>>
                                    <span class="ui-icon ui-icon-thumbs-down-alt"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment );?></span>
                                </a>
                            <?php endif;?>
                        <?php endif; ?>
                      <?php endif; ?>
                      
                    </div>
                    <?php if($showAsNested):?>    
                  <!--<div class="comments_date"> -->  
                    <?php $replyCount = $action->getReplies($comment->getIdentity(), $this->viewAllComments, true);?>  
                    <?php if($replyCount):?>  
                    <div class="feed_item_option" id="reply_list_<?php echo $comment->getIdentity();?>">
                    	<div role="navigation" class="ui-navbar" data-role="navbar" data-inset="false">
                      	<ul class="ui-grid-b">	
                        	<li id="reply_link" style="margin:5px 0 0;">
                      			<a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewreply', 'action_id' => $action->getIdentity(), 'comment_id' => $comment->getIdentity(),'writereply'=>'true'), 'default', 'true'); ?>", "replypopup", <?php echo $action->getIdentity();?>)' class="feed_replies">
                  						<span><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></span>
                       			</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <?php else:?>
                  <div class="feed_item_option" id="reply_list_<?php echo $comment->getIdentity();?>" style="display:none;">
                    	<div role="navigation" class="ui-navbar" data-role="navbar" data-inset="false">
                      	<ul class="ui-grid-b">	
                        	<li id="reply_link" style="margin:5px 0 0;">
                      			<a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'viewreply', 'action_id' => $action->getIdentity(), 'comment_id' => $comment->getIdentity(),'writereply'=>'true'), 'default', 'true'); ?>", "replypopup", <?php echo $action->getIdentity();?>)' class="feed_replies">
                  						<span><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></span>
                       			</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  <?php endif;?>
                  
                  
                    <?php endif;?>  
                  </div>
                 
                    <!--</div>-->
                    </div>
                    <?php if ($this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ($this->viewer()->getIdentity() == $comment->poster_id) || $this->activity_moderate )): ?>
                        <div id="comment-option-<?php echo $comment->comment_id ?>" class="feed_item_option_box" style="display: none;">
                            <div class="feed_overlay"></div>   
                             <a class="ui-btn-default ui-link" href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'edit', 'action_id' => $action->getIdentity(),'comment_id' => $comment->comment_id, 'perform' => 'comment-edit'), 'default', 'true'); ?>" , "editpopup_<?php echo $comment->comment_id?>", <?php echo $action->getIdentity();?>)'>
                                <span><?php echo $this->translate('Edit'); ?></span>
                            </a>  
                            <a class="ui-btn-default ui-link" href="javascript:void(0);" data-url="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id, 'comment_id' => $comment->comment_id), 'default', 'true'); ?>" onclick="javascript:sm4.activity.activityremove(this);" data-message="<?php echo $comment->comment_id ?>-<?php echo $action->action_id ?>"><?php echo $this->translate('Delete'); ?></a>
                             <a href="#" class="ui-btn-default ui-link" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideUp(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideDown(500);"><?php echo $this->translate('Cancel'); ?></a>
                        </div> 
                    <?php endif;?> 
                </li>                         
              <?php endforeach; ?>
            <?php else : ?>
              <li>
                <div class="no-comments">
                  <i class="ui-icon ui-icon-comment-alt"></i>
                  <span><?php echo $this->translate('No Comments') ?></span>
                </div>	
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      </div>
    <?php
    if ($canComment) :
      $this->commentForm->setActionIdentity($action->action_id)
      ?>
      
      <div style="display:none;" class="sm-comments-post-comment-form"  id="hide-commentform-<?php echo $action->action_id ?>">
        
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td class="sm-cmf-left">
                <?php echo $this->commentForm->render(); ?>
            </td>
            <td>
              <button class="ui-btn-default ui-btn-c" data-role="none" type="submit"  onclick="sm4.activity.attachComment($('#activity-comment-form-<?php echo $action->action_id ?>'));" style="background-color:transparent !important;"><i class="ui-icon ui-icon-post"></i></button>
            </td>
          </tr>
        </table>		
        
        <?php if($photoEnabled || $smiliesEnabled || $taggingEnabled):?>  
        <div class="cont-sep t_l b_medium"></div>
            <div id="activitypost-container-temp" action-id='<?php echo "activity-comment-form-$action->action_id" ;?>'>
            	<div class="compose_buttons">
                <?php if($photoEnabled):?>
                    <div id="composer-nested-comment-options" class="fleft">
                      <!--<div id="smactivityoptions-popup" class="sm-post-composer-options">-->
                          <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
                              <a href="javascript:void(0);" onclick="return sm4.activity.composer.showCommentPluginForm(this, 'photo', '#activity-comment-body-<?php echo $action->action_id ?>');" class="ui-link-inherit">
                                <i class="cm-icons cm-icon-photo"></i>
                              </a> 
                          <?php endif; ?>
                        
                      <!--</div>-->     
                    </div>
                <?php endif; ?>
                <?php if($smiliesEnabled):?>
                    <?php $enableSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy"));
                        $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
                        if (in_array("withtags", $enableSettings)):
                          ?>
                          <a href="javascript:void(0);" data-role="none" id="emoticons-button"  class="emoticons-button"  onclick="setEmoticonsBoard();
                              sm4.activity.statusbox.toggleEmotions($(this));" >
                            <i class="cm-icons cm-icon-emoticons"></i>
                          </a>
                     <?php endif; ?>
                <?php endif; ?>
                <?php if($taggingEnabled):?>
                <a id="comment-add-people" href="javascript:void(0);" data-role="none" onclick="          sm4.activity.composer.showCommentPluginForm(this, 'addpeople', '#activity-comment-body-<?php echo $action->action_id ?>');">
                  <i class="cm-icons cm-icon-user"></i>
                </a>
                <?php endif; ?>
              </div>
              <div id="comment_options_box">
                <?php if($smiliesEnabled):?>
                    <div id="emoticons-board" class="compose_embox_cont ui-page-content <?php if(Engine_Api::_()->sitemobile()->isApp()) echo 'compose-footer';?>" style="display:none;">
                        <div class="sm-seaocore-embox">
                          <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
                          <?php $comment_box_id = "#activity-comment-body-$action->action_id" ?>
                          <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                            <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIconNestedComment("<?php echo $this->string()->escapeJavascript($tag_key) ?>", "<?php echo $comment_box_id;?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
                              echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                              ?></span>
                          <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if($taggingEnabled):?>
                  <div id="adv_comment_post_container_tagging" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
                      <div id="aff_comment_mobile_aft_search-element">
                        <div class="sm-post-search-fields">
                          <table width="100%">
                            <tr>
                              <td class="sm-post-search-fields-left">
                                <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_comment_mobile_aft_search" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                              </td>
                            </tr>
                          </table>			
                          <span role="status" aria-live="polite"></span>
                        </div>
                        <div id="toCommentValues-temp-wrapper" style="border:none;display:none;">
                          <div id="toCommentValues-temp-element">
                            <input type="hidden" id="toCommentValues-temp" value=""  name="toValues-temp" />
                            <input type="hidden" id="toCommentValues" value=""  name="toValues" />
                          </div>
                        </div>
                      </div>
                  </div>
                  
                  <div id="toCommentValuesdone-wrapper" style="display:none;"></div>
                  <div class="ui-header o_hidden" id="ui-post-comment-header-addpeople" style="display:none;">
                      <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-left ui-btn-default" onclick="$('#aff_comment_mobile_aft_search').val('');
                              sm4.activity.composer.addpeople.addCommentFriends('#activity-comment-form-<?php echo $action->action_id ?>');
                              return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                      <?php  if (!Engine_Api::_()->sitemobile()->isApp()) :?>
                      <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_comment_mobile_aft_search').val(''); sm4.activity.toggleCommentPostArea(this, false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
                      <?php else: ?>
                       <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_comment_mobile_aft_search').val(''); sm4.activity.toggleCommentPostArea(this, false, 'addpeople');" ></a>
                       <?php endif; ?>
                  </div>
                <?php endif; ?>
               </div>
            </div> 
            <?php endif; ?>
        	
        <div style="display:none;"> 
          <script type="text/javascript">
              sm4.core.runonce.add(function(){
                $('#activity-comment-body-<?php echo $action->action_id ?>').autoGrow();  
                $('.sm-comments-post-comment-<?php echo $action->action_id ?>').on('vclick',function(){
                sm4.activity.toggleCommentArea(this, '<?php echo $action->action_id ?>');
              });
              sm4.activity.toggleCommentArea($('.sm-comments-post-comment-<?php echo $action->action_id ?>'), '<?php echo $action->action_id ?>');                 

              });                   
          </script>
        </div>
      </div>
      <div class="sm-comments-post-comment sm-comments-post-comment-<?php echo $action->action_id ?>" >
        <div>
          <input type="text" placeholder="<?php echo $this->translate('Write a comment...'); ?>" data-role="none" class="ui-input-field" />
        </div> 
      </div>
  <?php endif;?>
  
  <?php endif; ?>

</div> <!-- End of Comment Likes -->

<div id='like-activity-item-<?php echo $action->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='like-comment-item-<?php echo $action->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='dislike-activity-item-<?php echo $action->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div id='dislike-comment-item-<?php echo $action->action_id ?>' class="feed_item_show_comments_likes_wrapper" style="display:none;"></div>
<div style="display:none;">        
  <script type="text/javascript">
    var action_id = '<?php echo $action->action_id ?>';
     sm4.core.runonce.add(function(){
      $('.ps-close-popup').on('click', function() {          
         sm4.core.closefunctions.trigger();             
           
      });
    });
    sm4.core.closefunctions.add(function(){
      closeFeedPopup();
    });
    
    var closeFeedPopup = function(target) {

      if(typeof $('#comment-activity-item-' + action_id).html() == 'undefined') return; 
        sm4.activity.preloadedCommentArray['activity-comments_' + action_id].find('#comment-activity-item-' + action_id).html($('.ps-close-popup').closest('#feedsharepopup').find('#comment-activity-item-' + action_id).html());
     <?php if($RemoveClassDone):?>   
        $('.ui-page-active').removeClass('dnone');
     <?php else : ?>
       $('.ui-page-active').removeClass('pop_back_max_height');
     <?php endif;?>  
     
     $('.ps-close-popup').closest('#feedsharepopup').remove();
     $.mobile.silentScroll(parentScrollTop);  
    }
    sm4.activity.getLikeUsers('<?php echo $action->action_id ?>', false, 1); 
    sm4.activity.getDisLikeUsers('<?php echo $action->action_id ?>', false, 1); 
    sm4.activity.initialize($.mobile.activePage.find('#activity-comment-body-<?php echo $action->action_id ?>'), true);
  </script>  
</div>