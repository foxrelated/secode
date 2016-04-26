<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $edit_comment_id = $this->nested_comment_id . '_' . $comment->comment_id;?>
<form method="post" action="" action-id="<?php echo $edit_comment_id;?>" enctype="application/x-www-form-urlencoded" id='comments-form_<?php echo $edit_comment_id;?>'>
			<textarea id="<?php echo $edit_comment_id;?>" cols="1" rows="1" name="body" placeholder="<?php echo $this->escape($this->translate('Write a comment...')) ?>"></textarea>
			<?php if( $this->viewer() && $this->subject()): ?>
				<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
			<?php endif; ?>
			<input type="hidden" name="type" value="<?php echo $this->subject()->getType();?>" id="type">
			<input type="hidden" name="identity" value="<?php echo $this->subject()->getIdentity();?>" id="identity"><input type="hidden" name="parent_comment_id" value="<?php echo $this->parent_comment_id;?>" id="parent_comment_id">
			
      <input type="hidden" name="comment_id" value="<?php echo $comment->comment_id;?>" id="comment_id">
        <div id="compose-containe-menu-items_<?php echo $edit_comment_id; ?>" class="compose-menu <?php if($this->nestedCommentPressEnter):?> inside-compose-icons <?php endif;?> <?php if($this->showSmilies && $this->nestedCommentPressEnter):?> inside-smile-icon <?php endif;?>">
            <?php if($this->nestedCommentPressEnter):?>
              <button id="submit" type="submit" style="display: none;"><?php echo $this->translate("Post Comment") ?></button>
             <?php else:?>
                <button id="submit" type="submit" style="display: inline-block;"><?php echo $this->translate("Post Comment") ?></button>
                <div id="composer_container_icons_<?php echo $edit_comment_id; ?>"></div>
             <?php endif;?>
        </div>
    
</form>

<script type="text/javascript">
    
    function closeEdit(type, id, comment_id, parent_comment_id) {
       $('close_edit_box-'+ comment_id).style.display = 'none';
       if($('seaocore_edit_comment_' + comment_id))
        $('seaocore_edit_comment_' + comment_id).style.display = 'none';
       if($('seaocore_comment_data-' + comment_id))
        $('seaocore_comment_data-' + comment_id).style.display = 'block';
    
       $('comments-form_' + type + '_' + id + '_' + parent_comment_id + '_' + comment_id).style.display = 'none';
    }
   
</script>

<?php if($this->showSmilies && !$this->nestedCommentPressEnter): ?>
<style type="text/css">
	#nested-comment-compose-link-activator, #nested-compose-link-menu span {
		margin-right: 28px;
	}
</style>
<?php endif; ?>
<?php if($this->showSmilies && !in_array('addLink', $this->showComposerOptions) && !$this->nestedCommentPressEnter): ?>
<style type="text/css">
	#nested-comment-compose-photo-activator, #nested-compose-link-menu span {
		margin-right: 28px;
	}
</style>
<?php endif; ?>
<?php if(!$this->nestedCommentPressEnter): ?>
<style type="text/css">
	.seaocore_replies .compose-menu {
		width: 100%;
		display: inline-table;
	}
</style>
<?php endif; ?>

<?php if(!$this->showSmilies):?>
<style type="text/css">
.seaocore_replies_info .compose-menu.inside-compose-icons{
    right:0px;
}
</style>
<?php endif; ?>