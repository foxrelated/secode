<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getreply.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $actions = $this->actions; ?>

<?php 
foreach ($actions as $action):
  $comment = $action->comments()->getComment($this->comment_id);
  
  $canComment = ( $action->getTypeInfo()->commentable && $action->commentable &&
          $this->viewer()->getIdentity() &&
          Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') );
endforeach;
?>
<?php $old_comment_id = $this->old_comment_id;?>
<?php $replyCount = $action->getReplies($old_comment_id, $this->viewAllComments, true);?>
<div style="display:none">
<script type="text/javascript">
<?php if ($action): ?>
    $('#count-feedreplies').html("<?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount)); ?>");

     $('#reply_list_' + <?php echo $old_comment_id;?>).css('display', '');
     $('#reply_list_' + <?php echo $old_comment_id;?>).find('#reply_link a').html("<span><?php echo $this->translate(array('%s Reply', '%s Replies', $replyCount), $this->locale()->toNumber($replyCount))?></span>");
      
<?php endif; ?>
</script>
</div>
 <?php $this->comment = $item = $comment;?>
<?php 
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/view.tpl';
?>  
<div style="display:none;"> 
    <script type="text/javascript">
        sm4.core.runonce.add(function(){
          $('#activity-reply-body-<?php echo $this->old_comment_id ?>').autoGrow();          
          $('.sm-comments-post-comment-<?php echo $this->old_comment_id ?>').on('click',function(){
          sm4.activity.toggleReplyArea(this, '<?php echo $this->old_comment_id ?>');
        });
        sm4.activity.toggleReplyArea($('.sm-comments-post-comment-<?php echo $this->old_comment_id ?>'), '<?php echo $this->old_comment_id ?>'); 
        });                   
    </script>
</div>