<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _activitySettings.tpl 6590 2014-11-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $subjectType ='';?>
<?php if($this->subject() && $this->subject()->getType() && isset($this->subject()->listingtype_id)):?>
  <?php $subjectType .= $this->subject()->getType() . '_' . $this->subject()->listingtype_id;?>
<?php endif;?>
<?php $photoLightboxComment = 0; ?>
<?php if (isset($this->canComment)): ?>
    <?php $canComment = $this->canComment; ?>
<?php endif; ?>
<?php $photoEnabled = false; ?>
<?php $smiliesEnabled = false; ?>
<?php $activityTaggingContent = false; ?>
<?php $taggingEnabled = false; ?>
<?php $showAsNested = 1; ?>
<?php $showAsLike = 1; ?>
<?php $showComposerOptions = array("addLink","addPhoto", "addSmilies"); ?>
<?php $showDislikeUsers = 0; ?>
<?php $showLikeWithoutIcon = 0; ?>
<?php $showLikeWithoutIconInReplies = 0; ?>
<?php

if ($this->subject()):
    $subject = $this->subject();
    if($subjectType) {
        $type = $subjectType;
    } else {
        $type = $subject->getType();
    }
    if($subjectType == 'core_comment' || $subjectType == 'activity_comment') {
      $type = $subject->resource_type;  
    } else {
      $type = $subjectType == 'user' ? 'advancedactivity' : $subjectType;
    }
else:
    $type = 'advancedactivity';
endif;

?>
<?php
$type_array = array('album_photo', 'blog', 'video', 'album', 'group', 'poll', 'forum', 'music_playlist');
if (in_array($type, $type_array)): 
    $nested_comment_params = Engine_Api::_()->nestedcomment()->getCommentWidgetParams(array('resource_type' => $type));
else:
    $nested_comment_params = Engine_Api::_()->nestedcomment()->getParams(array('resource_type' => $type));
endif;
if ($nested_comment_params) {
    $taggingContent = $nested_comment_params['taggingContent'];
    $showAsNested = $nested_comment_params['showAsNested'];
    $showAsLike = $nested_comment_params['showAsLike'];
    $loaded_by_ajax = isset($nested_comment_params['loaded_by_ajax']) ? $nested_comment_params['loaded_by_ajax'] : 1;
    $showComposerOptions = $nested_comment_params['showComposerOptions'];
    $showDislikeUsers = $nested_comment_params['showDislikeUsers'];
    $showLikeWithoutIcon = $nested_comment_params['showLikeWithoutIcon'];
    $showLikeWithoutIconInReplies = $nested_comment_params['showLikeWithoutIconInReplies'];
};

?>
<?php if ($nested_comment_params && $nested_comment_params['showComposerOptions']): ?>
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) : ?>
        <?php $photoEnabled = in_array("addPhoto", $nested_comment_params['showComposerOptions']); ?>
    <?php endif; ?>
    <?php $smiliesEnabled = in_array("addSmilies", $nested_comment_params['showComposerOptions']); ?>
<?php endif; ?>
<?php if ($nested_comment_params && $nested_comment_params['taggingContent']): ?>
    <?php $taggingEnabled = in_array("friends", $nested_comment_params['taggingContent']); ?>
<?php endif; ?>

<?php

$this->aaf_comment_like_box = isset($nested_comment_params['aaf_comment_like_box']) ? $nested_comment_params['aaf_comment_like_box'] : 1;
?>
<?php if ($this->aaf_comment_like_box && $showLikeWithoutIcon != 3): ?>
    <?php $showLikeWithoutIcon = 1; ?>
<?php endif; ?>

<?php 

if(!Engine_Api::_()->seaocore()->checkEnabledNestedComment($type)) { 
    $showAsLike = 1;
}
?>