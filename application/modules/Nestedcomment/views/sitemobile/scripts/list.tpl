<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $actions = $this->actions; ?>
<?php 
  include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
?>

<?php if ($this->comments->getTotalItemCount() > 0): // COMMENTS -------   ?>
  <?php $action = $this->action; ?>
  <?php
  $action = $this->action;

  $canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
          $this->viewer()->getIdentity() &&
          Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));
  ?>
  <?php if ($this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
    <li onclick ="sm4.activity.getOlderComments(this, '<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo ($this->page + 1) ?>', '<?php echo $this->action_id ?>');">
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Load Previous Comments'), array()) ?>
      </div>
    </li>
  <?php endif; ?>
  <?php
  // Iterate over the comments backwards (or forwards!)
  $comments = $this->comments->getIterator();

  $i = count($comments) - 1;
  $l = count($comments);
  $d = -1;
  $e = -1;

  for (; $i != $e; $i += $d):
    $comment = $comments[$i];
    $poster = $this->item($comment->poster_type, $comment->poster_id);
    $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
    ?>
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
  <?php endfor; ?>
<?php endif; ?>