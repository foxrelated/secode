<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Nestedcomment
* @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: _editReply.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<form method="post" action="" class="activity-comment-form" enctype="application/x-www-form-urlencoded" id="activity-reply-edit-form-<?php echo $reply->comment_id;?>">
    <textarea rows = "1" id="activity-reply-edit-body-<?php echo $reply->comment_id;?>" name="body"></textarea>
    <button type="submit" id="activity-reply-edit-submit-<?php echo $reply->comment_id;?>" name="submit" <?php if($this->commentShowBottomPost):?> style="display: none;" <?php endif;?>><?php echo $this->translate("Edit");?></button>
    <input type="hidden" id="activity-reply-edit-id-<?php echo $reply->comment_id;?>" value="<?php echo $reply->comment_id;?>" name="comment_id">
    <input type="hidden" value='<?php echo $action->action_id ?>' name='action_id'>
</form>