<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list-reply.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $actions = $this->actions; ?>
<?php 
  include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
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
    <li onclick ="sm4.activity.getOlderReplies(this, '<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo ($this->page + 1) ?>', '<?php echo $this->action_id ?>', '<?php echo $this->action_id ?>', '<?php echo $this->comment_id ?>');">
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Load Previous Replies'), array()) ?>
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
    <?php $this->comment = $comment;?>
    <li id="reply-<?php echo $comment->comment_id ?>">
      <?php 
        include APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/view.tpl';
      ?>
    </li>
  <?php endfor; ?>
<?php endif; ?>