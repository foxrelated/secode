<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php
    $this->headTranslate(array(
        'Are you sure you want to delete this?',
    ));
    ?>
    <?php 
        include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
    ?>
    <?php $replyCount = $this->comments->getTotalItemCount(); ?>

<?php if (!$this->page): ?>
  <div id="replies_<?php echo $this->comment_id;?>">
<?php endif; ?>
    <div class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper">
        <div class="">
            <div class="sm-ui-popup-top ui-header ui-bar-a">
                <a href="javascript:void(0);" data-iconpos="notext" data-role="button" data-icon="remove" data-corners="true" data-shadow="true" data-iconshadow="true" class="ps-close-popup close-feedsharepopup ui-btn-right" ></a>
                <h2 class="ui-title" id="count-feedcomments_<?php echo $this->comment_id;?>"><?php echo $this->translate(array('%s reply', '%s replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></h2>
            </div>
            <div class="sm-ui-popup-container" style="bottom:95px;">
                <div class="comments">
                    <ul class="viewcomment">
                        <?php if ($replyCount): ?>
                            
  				<?php if ($this->page && $this->comments->getCurrentPageNumber() > 1): ?>
                                    <li>
                                      <div> </div>
                                      <div class="comments_viewall">
                                        <?php
                                        echo $this->htmlLink('javascript:void(0);', $this->translate('View previous replies'), array(
                                            'onclick' => 'sm4.core.comments.loadReplies("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page - 1) . '", "' . $this->comment_id . '")'
                                        ));
                                        ?>
                                      </div>
                                    </li>
                        <?php endif; ?>
  
                        <?php if (!$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
                        <li>
                          <div> </div>
                          <div class="comments_viewall">
                            <?php
                            echo $this->htmlLink('javascript:void(0);', $this->translate('View more replies'), array(
                                'onclick' => 'sm4.core.comments.loadReplies("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->comments->getCurrentPageNumber()) . '", "' . $this->comment_id . '")'
                            ))
                            ?>
                          </div>
                        </li>
        	<?php endif; ?>
  
        <?php 
        // Iterate over the comments backwards (or forwards!)
        $comments = $this->comments->getIterator();
        if ($this->page):
          $i = 0;
          $l = count($comments) - 1;
          $d = 1;
          $e = $l + 1;
        else:
          $i = count($comments) - 1;
          $l = count($comments);
          $d = -1;
          $e = -1;
        endif;
        for (; $i != $e; $i += $d):
          $comment = $comments[$i];
          $poster = $this->item($comment->poster_type, $comment->poster_id);
          $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
          ?>
         <li id="comment-<?php echo $comment->comment_id ?>">
               <div id="comment_information-<?php echo $comment->comment_id ?>" style="display:block">  
               <div class="feed_items_options_btn">      
                    <a href="javascript:void(0);" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideDown(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideUp(500);" data-role="button" data-icon="carat-d" data-iconpos="notext" data-theme="c" data-inline="true" class="ui-link ui-btn ui-btn-c ui-icon-carat-d ui-btn-icon-notext ui-btn-inline ui-shadow ui-corner-all" role="button"></a>
               </div> 
            <div class="comments_author_photo">
              <?php
              echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
              )
              ?>
            </div>
            <div class="comments_info">
                <div class='comments_author'>
                <?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?>
                </div>
                <div class="comments_body">
                    <?php $item = $comment;?>
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
                  <?php if (isset($this->form)): ?>
                    <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'view','comment_id' => $comment->comment_id, 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>" , "replypopup_<?php echo $comment->comment_id;?>", <?php echo $comment->getIdentity();?>)'>
                        <span><?php echo $this->translate('Reply'); ?></span>
                    </a>   
                    -
                <?php endif;?> 
              <?php endif;?> 
               
              	<?php if($showAsLike):?>    
                  <?php
                  if ($this->canComment):
                    $isLiked = $comment->likes()->isLike($this->viewer());
                    ?>
                    <?php if (!$isLiked): ?>
                      <a href="javascript:void(0)" onclick="sm4.core.comments.likeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes">
                        <?php echo $this->translate('Like') ?>
                      </a>
                      
                    <?php else: ?>
                      <a href="javascript:void(0)" onclick="sm4.core.comments.unlikeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes">
                        <?php echo $this->translate('Unlike') ?>
                      </a>
                    <?php endif ?>
                  <?php endif ?>
                                                          
                  <?php if ($comment->likes()->getLikeCount() > 0): ?>
                    <span class="sep">-</span>
                    <a id="replies_reply_likes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="replies_reply_likes"><?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?></a>
                <?php endif ?>
                <?php else:?>
                  <?php if($canComment):?>
                    <?php $isLiked = $comment->likes()->isLike($this->viewer());?>
                    <?php if($showLikeWithoutIconInReplies != 3):?>
                      <?php if(!$isLiked):?>
                        <a href="javascript:void(0)" onclick="sm4.core.comments.likeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes"> <?php echo $this->translate('Like') ?></a> <span class="sep">-</span> 
                      <?php else:?>
                         <a href="javascript:void(0)" class="reply_likes" onclick="sm4.activity.changeLikeDislikeColor()"><?php echo $this->translate('Like') ?></a> 
                         <span class="sep">-</span> 
                      <?php endif;?>
                    <?php else:?>
                      <?php if ($comment->likes()->getLikeCount() > 0): ?>
                          <?php if($showLikeWithoutIconInReplies == 3):?>
                              <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $comment->comment_id ?>" class="replies_reply_likes" onclick="sm4.core.comments.reply_likes(<?php echo sprintf("'%d'", $comment->comment_id) ?>)">
                                <?php echo $this->locale()->toNumber($comment->likes()->getLikeCount()); ?>
                              </a>
                          <?php endif;?> 
                      <?php endif ?>
                
                      <?php if(!$isLiked):?>
                        <a href="javascript:void(0)" onclick="sm4.core.comments.likeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="reply_likes ui-icon ui-icon-angle-up"></a> <span class="sep">-</span> 
                      <?php else:?>
                         <a href="javascript:void(0)" class="reply_likes ui-icon ui-icon-angle-up" onclick="sm4.activity.changeLikeDislikeColor()"></a> 
                         <span class="sep">-</span> 
                      <?php endif;?>
                    <?php endif;?>
                 <?php endif;?>
                  
                <?php if($canComment):?>
                 
                 	<?php $isDisLiked = Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->isDislike($comment, $this->viewer());?>
                 	<?php if($showLikeWithoutIconInReplies != 3):?>
                            <?php if(!$isDisLiked):?>
                                <a href="javascript:void(0)" onclick="sm4.core.comments.dislikeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="reply_dislikes"> <?php echo $this->translate('Dislike') ?></a> 
                                <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                    <span class="sep">-</span> 
                                  <?php endif;?>
                            <?php else:?>
                                <a href="javascript:void(0)" class="reply_dislikes" onclick="sm4.activity.changeLikeDislikeColor()"> <?php echo $this->translate('Dislike') ?> </a>
                                <?php if ($comment->likes()->getLikeCount() || Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                    <span class="sep">-</span> 
                                  <?php endif;?> 
                            <?php endif;?>
                            <?php else:?>
                               <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                                <?php if($showLikeWithoutIconInReplies == 3):?>
                                  <?php if($showDislikeUsers):?>
                                    <a href="javascript:void(0);" id="replies_reply_dislikes_<?php echo $comment->comment_id ?>" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "replypopup_"+<?php echo $comment->getIdentity();?>)' class="replies_reply_dislikes"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></a>
                                    <?php else:?>
                                    <a class="replies_reply_dislikes" id="replies_reply_dislikes_<?php echo $comment->comment_id ?>"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></a>
                                    <?php endif;?>
                                <?php endif;?>
                             <?php endif ?>
                     <?php if(!$isDisLiked):?>
                        <a href="javascript:void(0)" onclick="sm4.core.comments.dislikeReply(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)" class="reply_dislikes ui-icon ui-icon-angle-down"></a>
                     <?php else:?>
                        <a href="javascript:void(0)" onclick="sm4.activity.changeLikeDislikeColor()" class="reply_dislikes ui-icon ui-icon-angle-down"></a>
                     <?php endif;?>
                  <?php endif;?>
               	
               	<?php endif;?>
                
                <?php if ($comment->likes()->getLikeCount() > 0): ?>
                    <?php if($showLikeWithoutIconInReplies != 3):?>
                         <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-like-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>", "feedsharepopup")' class="replies_reply_likes"><span class="ui-icon ui-icon-thumbs-up-alt"><?php echo $comment->likes()->getLikeCount(); ?></span></a>
                        <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                            <span class="sep">-</span> 
                        <?php endif;?>
                    <?php endif;?> 
                <?php endif; ?>
              
                <?php if (Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment ) > 0): ?>
                    <?php if($showLikeWithoutIconInReplies != 3):?>
                        <?php if($showDislikeUsers):?>
                            <a href="javascript:void(0);" id="replies_reply_dislikes_<?php echo $comment->comment_id ?>" href="javascript:void(0);" onclick='sm4.activity.openPopup("<?php echo $this->url(array('module' => 'advancedactivity', 'controller' => 'index', 'action' => 'get-all-dislike-user', 'type' => $comment->getType(), 'id' => $comment->getIdentity()), 'default', 'true'); ?>",  "feedsharepopup")' class="replies_reply_dislikes"><span class="ui-icon ui-icon-thumbs-down-alt"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></span></a>
                        <?php else:?>
                            <a class="replies_reply_dislikes" id="replies_reply_dislikes_<?php echo $comment->comment_id ?>"><span class="ui-icon ui-icon-thumbs-down-alt"><?php echo $this->locale()->toNumber(Engine_Api::_()->getDbtable('dislikes', 'nestedcomment')->getDislikeCount( $comment )); ?></span></a>
                        <?php endif;?>
                    <?php endif;?>
                <?php endif; ?>
                
              <?php endif; ?>
                
              </div>
              <?php $replyCount = Engine_Api::_()->getDbtable('comments', 'nestedcomment')->getReplyCount($this->subject(), $comment->getIdentity());?>  
            <?php if($replyCount):?>  
                <div class="feed_item_option" id="reply_list_<?php echo $comment->getIdentity();?>">
                    <div role="navigation" class="ui-navbar" data-role="navbar" data-inset="false">
                    <ul class="ui-grid-b">	
                        <li id="reply_link" style="margin:5px 0 0;">
                            <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'view', 'comment_id' => $comment->getIdentity(), 'type' => $comment->resource_type, 'id' => $comment->resource_id), 'default', 'true'); ?>", "replypopup_<?php echo $comment->comment_id;?>", <?php echo $comment->getIdentity();?>)' class="feed_replies">
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
                                <a href="javascript:void(0);" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'view', 'comment_id' => $comment->getIdentity(), 'type' => $this->comment->resource_type, 'id' => $this->comment->resource_id), 'default', 'true'); ?>", "replypopup_<?php echo $comment->getIdentity();?>", <?php echo $comment->getIdentity();?>)' class="feed_replies"><span><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                 </div>
            <?php endif;?>  
            </div>
        </div>
        <?php if ($canDelete): ?>     
            <div id="comment-option-<?php echo $comment->comment_id ?>" class="feed_item_option_box" style="display: none;">
            <div class="feed_overlay"></div>
            <a href="javascript:void(0);" class="ui-btn-default ui-link" onclick='ActivityAppReplyPopup("<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'reply', 'action' => 'edit','comment_id' => $comment->comment_id, 'perform' => 'comment-edit', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', 'true'); ?>" , "editpopup_<?php echo $comment->getIdentity()?>", <?php echo $comment->getIdentity();?>)'>
                    <span><?php echo $this->translate('Edit'); ?></span>
            </a>  
            <a href="javascript:void(0);" class="ui-btn-default ui-link" onclick="sm4.core.comments.deleteReply('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
              <?php echo $this->translate('Delete') ?>
            </a>
            <a href="#" class="ui-btn-default ui-link" onclick="$('#comment-option-<?php echo $comment->comment_id ?>').slideUp(500); $('#comment_information-<?php echo $comment->comment_id ?>').slideDown(500);"><?php echo $this->translate('Cancel'); ?></a>
            </div>
            <?php endif; ?>
        </li>
        
      <?php endfor; ?>

      <?php if ($this->page && $this->comments->getCurrentPageNumber() < $this->comments->count()): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('View later replies'), array(
                'onclick' => 'sm4.core.comments.loadReplies("' . $this->subject()->getType() . '", "' . $this->subject()->getIdentity() . '", "' . ($this->page + 1) . '", "' . $this->comment_id . '")'
            ))
            ?>
          </div>
        </li>
      <?php endif; ?>
                        <?php else: ?>
                            <li id="no-comments">
                                <div class="no-comments">
                                    <i class="ui-icon ui-icon-comment-alt"></i>
                                    <span><?php echo $this->translate('No Replies') ?></span>
                                </div>	
                            </li>
                        <?php endif; ?>
                    </ul>
               
            </div>
        </div>
            <div data-role="page" id="popupDialog-Reply_<?php echo $this->subject()->getIdentity() ?>" data-overlay-theme="a" data-theme="c" data-dismissible="false" style="margin: 30% 15%;max-width: 70%;min-height: auto;display:none;" class="ui-corner-all ui-popup-container ui-popup-active">
                <div data-role="header" data-theme="a" class="ui-corner-top">
                    <h1><?php echo $this->translate('Delete Reply?'); ?></h1>
                </div>
                <div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
                    <h3 class="ui-title"></h3>
                    <p><?php echo $this->translate('Are you sure that you want to delete this reply? This action cannot be undone.'); ?></p>          
                    <a href="#" data-role="button" data-inline="true"  data-transition="flow" data-theme="b" onclick="javascript:sm4.core.comments.deleteReply('', '', '')"><?php echo $this->translate("Delete");?></a>
                    <a href="javascript:void(0);" data-inline="true" data-iconpos="notext" data-role="button"  data-corners="true" data-theme="c" data-shadow="true" data-iconshadow="true" onclick="$('#popupDialog-Reply_<?php echo $this->subject()->getIdentity() ?>').css('display', 'none');" ><?php echo $this->translate("Cancel");?></a>
                </div>   
            </div>
        <?php if (isset($this->form)): ?>  
            <div class="sm-comments-post-comment-form">
                
                    <table cellspacing="0" cellpadding="0">
                        <tr>    
                            <td class="sm-cmf-left">
                                <form id="reply-form_<?php echo $this->comment_id;?>" enctype="application/x-www-form-urlencoded" action="" method="post" data-ajax="false">
                                    <?php
                                    foreach ($this->form->getElements() as $key => $value):
                                        if ($key != "submit") : echo $this->form->$key;
                                        endif;
                                    endforeach;
                                    ?>
                                </form>
                            </td>
                            <td>
                                <button class="ui-btn-default ui-btn-c" data-role="none" type="submit" id="submit" name="submit" onclick="sm4.core.comments.attachCreateReply($('#reply-form_'+<?php echo $this->comment_id;?>)); return false;" style="background-color:transparent !important;"><i class="ui-icon ui-icon-post"></i></button>
                            </td>
                        </tr>
                    </table>
                    <?php if ($photoEnabled || $smiliesEnabled || $taggingEnabled): ?>  
                        <div class="cont-sep t_l b_medium"></div>
                        <div id="activitypost-container-temp" action-id='reply-form_<?php echo $this->comment_id;?>' action-title='reply'>
                            <div class="compose_buttons">
                                <?php if ($photoEnabled): ?>
                                    <div id="composer-nested-reply-options_<?php echo $this->comment_id;?>" class="fleft">
                                        <!--<div id="smactivityoptions-popup" class="sm-post-composer-options">-->
                                        <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
                                            <a href="javascript:void(0);" onclick="return sm4.activity.composer.showReplyPluginForm(this, 'photo', '#body', '<?php echo $this->comment_id;?>');" class="ui-link-inherit">
                                                <i class="cm-icons cm-icon-photo"></i>
                                            </a> 
                                        <?php endif; ?>

                                        <!--</div>-->     
                                    </div>
                                <?php endif; ?>
                                <?php if ($smiliesEnabled): ?>
                                    <?php
                                    $enableSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy"));
                                    $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
                                    if (in_array("withtags", $enableSettings)):
                                        ?>
                                        <a href="javascript:void(0);" data-role="none" id="emoticons-reply-button_<?php echo $this->comment_id;?>"  class="emoticons-button"  onclick="setEmoticonsReplyBoard(<?php echo $this->comment_id;?>);
                                                  sm4.activity.statusbox.toggleReplyEmotions($(this), '<?php echo $this->comment_id;?>');" >
                                            <i class="cm-icons cm-icon-emoticons"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($taggingEnabled): ?>
                                                        <a id="comment-add-people_<?php echo $this->comment_id;?>" href="javascript:void(0);" data-role="none" onclick="sm4.activity.composer.showReplyPluginForm(this, 'addpeople', '#body', '<?php echo $this->comment_id;?>');">
                                                            <i class="cm-icons cm-icon-user"></i>
                                                        </a>
                                <?php endif; ?>
                            </div>
                            <div id="reply_options_box_<?php echo $this->comment_id;?>">
                                <?php if ($smiliesEnabled): ?>
                                    <div id="emoticons-reply-board_<?php echo $this->comment_id;?>" class="compose_embox_cont ui-page-content <?php if (Engine_Api::_()->sitemobile()->isApp()) echo 'compose-footer'; ?>" style="display:none;">
                                        <div class="sm-seaocore-embox">
                                            <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
                                                <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
                                                    <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>", "<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIconReply("<?php echo $this->string()->escapeJavascript($tag_key) ?>", "#body", "<?php echo $this->comment_id;?>")' title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>">
                                                        <?php
                                                        echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
                                                        ?>
                                                    </span>
                                                <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

            <?php if ($taggingEnabled): ?>
                                    <div id="adv_reply_post_container_tagging_<?php echo $this->comment_id;?>" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
                                        <div id="aff_reply_mobile_aft_search_<?php echo $this->comment_id;?>-element">
                                            <div class="sm-post-search-fields">
                                                <table width="100%">
                                                    <tr>
                                                        <td class="sm-post-search-fields-left">
                                                            <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_reply_mobile_aft_search_<?php echo $this->comment_id;?>" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                                                        </td>
                                                    </tr>
                                                </table>			
                                                <span role="status" aria-live="polite"></span>
                                            </div>
                                            <div id="toReplyValues-temp_<?php echo $this->comment_id;?>-wrapper" style="border:none;display:none;">
                                                <div id="toReplyValues-temp_<?php echo $this->comment_id;?>-element">
                                                    <input type="hidden" id="toReplyValues-temp_<?php echo $this->comment_id;?>" value=""  name="toValues-temp" />
                                                    <input type="hidden" id="toReplyValues_<?php echo $this->comment_id;?>" value=""  name="toValues" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="toReplyValuesdone-wrapper_<?php echo $this->comment_id;?>" style="display:none;"></div>
                                    <div class="ui-header o_hidden" id="ui-post-reply-header-addpeople_<?php echo $this->comment_id;?>" style="display:none;">
                                        <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-left ui-btn-default" onclick="$('#aff_reply_mobile_aft_search_'+<?php echo $this->comment_id;?>).val('');
                                          sm4.activity.composer.addpeople.addReplyFriends('#reply-form_'+<?php echo $this->comment_id;?>, '<?php echo $this->comment_id;?>');
                                          return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                                        <?php if (!Engine_Api::_()->sitemobile()->isApp()) : ?>
                                            <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_reply_mobile_aft_search_'+<?php echo $this->comment_id;?>).val('');sm4.activity.toggleReplyPostArea('<?php echo $this->comment_id;?>');"><?php echo $this->translate('Cancel'); ?></a>
                                        <?php else: ?>
                                            <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_reply_mobile_aft_search_'+<?php echo $this->comment_id;?>).val('');sm4.activity.toggleReplyPostArea( '<?php echo $this->comment_id;?>');" ></a>
                                    <?php endif; ?>
                                    </div>
            <?php endif; ?>
                            </div>
                        </div> 
        <?php endif; ?>
                
            </div>	
    <?php endif; ?>
    </div>
    </div>
<?php if (!$this->page): ?>
  </div>
<?php endif; ?>        
<?php else: ?>

<?php endif; ?>