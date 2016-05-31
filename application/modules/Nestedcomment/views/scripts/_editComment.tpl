<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Nestedcomment
* @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _editComment.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<form method="post" action="" class="activity-comment-form" enctype="application/x-www-form-urlencoded" id="activity-comment-edit-form-<?php echo $comment->comment_id;?>">
    <textarea rows = "1" id="activity-comment-edit-body-<?php echo $comment->comment_id;?>" name="body"></textarea>
    <button type="submit" id="activity-comment-edit-submit-<?php echo $comment->comment_id;?>" class="mtop5" name="submit" <?php if($this->commentShowBottomPost && !$this->isMobile):?> style="display: none;" <?php endif;?> ><?php echo $this->translate("Edit");?></button>
    <input type="hidden" id="activity-comment-edit-id-<?php echo $comment->comment_id;?>" value="<?php echo $comment->comment_id;?>" name="comment_id">
    <input type="hidden" value='<?php echo $action->action_id ?>' name='action_id'>
</form>