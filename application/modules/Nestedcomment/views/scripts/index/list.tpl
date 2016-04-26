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

<?php foreach ($action->getReplies($this->comment_id) as $reply): ?>
                
    <li id="reply-<?php echo $reply->comment_id ?>" class="reply<?php echo $this->comment_id;?>">
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
          <?php
          if ($this->viewer()->getIdentity() &&
                  (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                  ("user" == $reply->poster_type && $this->viewer()->getIdentity() == $reply->poster_id) ||
                  ("user" !== $reply->poster_type && Engine_Api::_()->getItemByGuid($reply->poster_type . "_" . $reply->poster_id)->isOwner($this->viewer())) ||
                  $this->activity_moderate )):
            ?>


            <?php /* echo $this->htmlLink(array(
              'route'=>'default',
              'module'    => 'advancedactivity',
              'controller'=> 'index',
              'action'    => 'delete',
              'action_id' => $action->action_id,
              'comment_id'=> $reply->comment_id,
              ),'', array('class' => 'smoothbox
              aaf_icon_remove','title'=>$this->translate('Delete Reply'))) */ ?>
            <a href="javascript:void(0);" class="aaf_icon_remove" title="<?php
            echo
            $this->translate('Delete Reply')
            ?>" onclick="deletereply('<?php
               echo
               $action->action_id
               ?>', '<?php echo $reply->comment_id ?>', '<?php
               echo
               $this->escape($this->url(array('route' => 'default',
                           'module' => 'advancedactivity', 'controller' => 'index', 'action' => 'delete')))
               ?>')"></a>
             <?php endif; ?>
        </span>
        <span class="comments_body" id="reply_body_<?php echo $reply->comment_id ?>">
            <?php 
                include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_replyBody.tpl';
            ?>   
        </span>
          <div id="reply_edit_<?php echo $reply->comment_id ?>" style="display: none;"><?php include APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/_editReply.tpl' ?>
          </div>

          <?php if (!empty($reply->attachment_type) && null !== ($attachment = $this->item($reply->attachment_type, $reply->attachment_id))): ?>
        <div class="seaocore_comments_attachment" id="seaocore_comments_attachment_<?php echo $reply->comment_id ?>">
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
          <li class="comments_timestamp">
            <?php echo $this->timestamp($reply->creation_date); ?>
          </li>
          <?php
          if ($canComment):
            $isLiked = $reply->likes()->isLike($this->viewer());
            ?>
            <li class="comments_like"> 
              &#183;
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
          <?php endif ?>
          <?php if ($reply->likes()->getLikeCount() > 0): ?>
            <li class="comments_likes_total"> 
              &#183;
              <a href="javascript:void(0);" id="replies_reply_likes_<?php echo $reply->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                <?php echo $this->translate(array('%s likes this', '%s like this', $reply->likes()->getLikeCount()), $this->locale()->toNumber($reply->likes()->getLikeCount())) ?>
              </a>
            </li>
          <?php endif ?>

          <span>&#183;</span>

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
                 <a href='javascript:void(0);' title="<?php echo $this->translate('Edit') ?>" onclick="en4.nestedcomment.nestedcomments.showReplyEditForm('<?php echo $reply->comment_id?>');"><?php echo $this->translate('Edit'); ?>

             </a>
        </ul>
      </div>
    </li>
<?php endforeach;?>