<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $comment = $this->comment;?>
<div id="comment_information-<?php echo $comment->comment_id ?>" style="display:block"> 
    <?php if ($this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ($this->viewer()->getIdentity() == $comment->poster_id) || $this->activity_moderate )): ?>
        <div class="feed_items_options_btn">      
          <a href="javascript:void(0);" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideDown(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideUp(500);" data-role="button" data-icon="carat-d" data-iconpos="notext" data-theme="c" data-inline="true" class="ui-link ui-btn ui-btn-c ui-icon-carat-d ui-btn-icon-notext ui-btn-inline ui-shadow ui-corner-all" role="button"></a>
        </div>  
    <?php endif;?>

<div class="comments_author_photo">
  <?php echo $this->htmlLink($this->item($this->comment->poster_type, $this->comment->poster_id)->getHref(), $this->itemPhoto($this->item($this->comment->poster_type, $this->comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())) ?>
</div>
<div class="comments_info">
  <div class='comments_author'>
    <?php echo $this->htmlLink($this->item($this->comment->poster_type, $this->comment->poster_id)->getHref(), $this->item($this->comment->poster_type, $this->comment->poster_id)->getTitle()); ?>
  </div>
  <?php $item = $comment = $this->comment;?>
  <div class="comments_body">
  <?php //echo $this->smileyToEmoticons($this->viewMore($comment->body)) ?>
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
    <?php if($showAsLike):?>
    <?php
    if ($canComment):
      $isLiked = $comment->likes()->isLike($this->viewer());
      ?>
      <?php if (!$isLiked): ?>
        <a href="javascript:void(0)" onclick="sm4.activity.likeReply(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
      <?php else: ?>
        <a href="javascript:void(0)" onclick="sm4.activity.unlikeReply(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes"><?php echo $this->translate('Unlike') ?>
        </a> <span class="sep">-</span> 
      <?php endif ?>
    <?php endif ?>
   
      
    <?php if ($comment->likes()->getLikeCount() > 0): ?>
                        <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $comment->comment_id ?>" class="replies_reply_likes" onclick="$('#reply-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-reply-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.reply_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">
                          <?php //echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                            <span class="ui-icon ui-icon-thumbs-up-alt"><?php echo $comment->likes()->getLikeCount();?></span>
                        </a> <span class="sep">-</span> 
                      <?php endif; ?>
                        
    <?php else:?>

    <?php if($canComment):?>
        <?php $isLiked = $comment->likes()->isLike($this->viewer());?>
        <?php if($showLikeWithoutIconInReplies != 3):?>
          <?php if(!$isLiked):?>
            <a href="javascript:void(0)" onclick="sm4.activity.likeReply(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
          <?php else:?>
             <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="reply_likes"><?php echo $this->translate('Like') ?></a> 
             <span class="sep">-</span> 
          <?php endif;?>
        <?php else:?>
          <?php if ($comment->likes()->getLikeCount() > 0): ?>
              <?php if($showLikeWithoutIconInReplies == 3):?>
                  
             <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $comment->comment_id ?>" class="replies_reply_likes" onclick="$('#reply-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-reply-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.reply_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">    
                                        <span><?php echo $comment->likes()->getLikeCount(); ?></span>
                                    </a>
             
              <?php endif;?> 
          <?php endif; ?>

          <?php if(!$isLiked):?>
            <a href="javascript:void(0)" onclick="sm4.activity.likeReply(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes ui-icon ui-icon-angle-up"></a> <span class="sep">-</span> 
          <?php else:?>
             <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="reply_likes ui-icon ui-icon-angle-up"></a> 
             <span class="sep">-</span> 
          <?php endif;?>
        <?php endif;?>
     <?php endif;?>

     <?php if($canComment):?>

        <?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($comment, $this->viewer());?>

        <?php if($showLikeWithoutIconInReplies != 3):?>
          <?php if(!$isDisLiked):?>
            <a href="javascript:void(0)" onclick="sm4.activity.dislikeReply(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="reply_dislikes"> <?php echo $this->translate('Dislike') ?></a> <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                              <span class="sep">-</span> 
                              <?php endif;?> 
          <?php else:?>
            <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="reply_dislikes"> <?php echo $this->translate('Dislike') ?> </a>
                <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                              <span class="sep">-</span> 
                              <?php endif;?>
          <?php endif;?>
        <?php else:?>

           <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
              <?php if($showLikeWithoutIconInReplies == 3):?>
                  <a href="javascript:void(0);" id="replies_reply_dislikes_<?php echo $comment->comment_id ?>" class="replies_reply_dislikes" <?php if($showDislikeUsers):?>  onclick="$('#reply-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#dislike-reply-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.reply_dislikes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)" <?php endif;?>>
                    <span><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></span>
                  </a>
              <?php endif;?>
           <?php endif ?>

           <?php if(!$isDisLiked):?>
            <a href="javascript:void(0)" onclick="sm4.activity.dislikeReply(<?php echo sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()) ?>)" class="reply_dislikes ui-icon ui-icon-angle-down"></a>
          <?php else:?>
            <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="reply_dislikes ui-icon ui-icon-angle-down"></a>
            
          <?php endif;?>
        <?php endif;?>
     <?php endif;?>
    <?php if ($comment->likes()->getLikeCount() > 0): ?>
      <?php if($showLikeWithoutIconInReplies != 3):?>
          <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $comment->comment_id ?>" class="replies_reply_likes" onclick="$('#reply-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#like-reply-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.reply_likes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)">
            <span class="ui-icon ui-icon-thumbs-up-alt"><?php echo $comment->likes()->getLikeCount(); ?></span>
          </a>  <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                    <span class="sep">-</span> 
                                  <?php endif;?> 
      <?php endif;?> 

    <?php endif ?>

      <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
          <?php if($showLikeWithoutIconInReplies != 3):?>
              <a href="javascript:void(0);" id="replies_reply_dislikes_<?php echo $comment->comment_id ?>" class="replies_reply_dislikes" <?php if($showDislikeUsers):?>onclick="$('#reply-activity-item-' + <?php echo $action->action_id ?>).css('display', 'none');$('#dislike-reply-item-' + <?php echo $action->action_id ?>).css('display', 'block');sm4.activity.reply_dislikes('<?php echo $action->action_id ?>','<?php echo $comment->getIdentity(); ?>', 1)" <?php endif;?>>
                <span class="ui-icon ui-icon-thumbs-down-alt"><?php echo Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment );?></span>
              </a>
          <?php endif;?>
      <?php endif ?>
    <?php endif ?>
  </div>
</div>

</div>
<?php if ($this->viewer()->getIdentity() && (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) || ($this->viewer()->getIdentity() == $comment->poster_id) || $this->activity_moderate )): ?>
    <div id="comment-option-<?php echo $comment->comment_id ?>" class="feed_item_option_box" style="display: none;">
        <div class="feed_overlay"></div>   
         <a class="ui-btn-default ui-link" href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'edit', 'action_id' => $action->getIdentity(),'comment_id' => $comment->comment_id, 'perform' => 'reply-edit'), 'default', 'true'); ?>" , "editpopup_<?php echo $comment->comment_id?>", <?php echo $action->getIdentity();?>)'>
            <span><?php echo $this->translate('Edit'); ?></span>
        </a>  
        <a class="ui-btn-default ui-link" href="javascript:void(0);" data-url="<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id, 'comment_id' => $comment->comment_id), 'default', 'true'); ?>" onclick="javascript:sm4.activity.replyremove(this);" data-message="<?php echo $comment->comment_id ?>-<?php echo $action->action_id ?>"><?php echo $this->translate('Delete'); ?></a>
         <a href="#" class="ui-btn-default ui-link" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideUp(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideDown(500);"><?php echo $this->translate('Cancel'); ?></a>
    </div> 
<?php endif;?> 