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
<?php $this->headTranslate(array('Write a comment...', 'Write a reply...', 'Attach a Photo', 'Post a comment...', 'Post a reply...')); ?> 
<?php

$this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
    'Add Photo',
    'Add Link',
    'Select File',
    'cancel',
    'Loading...',
    'Unable to upload photo. Please click cancel and try again',
    'Last',
    'Next',
    'Attach',
    'Loading...',
    'Don\'t show an image',
    'Choose Image:',
    '%d of %d',
));
?> 
<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Nestedcomment/View/Helper', 'Nestedcomment_View_Helper'); ?>
<?php $photoEnabled = false; ?>
<?php $smiliesEnabled = false; ?>
<?php $activityTaggingContent = false; ?>
<?php $row = Engine_Api::_()->seaocore()->checkEnabledNestedComment('advancedactivity', array('checkModuleExist' => true)); ?>
<?php if ($row): ?>
    <?php $advancedactivityParams = $row->params; ?>
    <?php $decodedParams = Zend_Json_Decoder::decode($advancedactivityParams); ?>
    <?php if(!empty($decodedParams['showComposerOptions'])):?>
        <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) : ?>
            <?php $photoEnabled = in_array("addPhoto", $decodedParams['showComposerOptions']); ?>
        <?php endif; ?>
        <?php $smiliesEnabled = in_array("addSmilies", $decodedParams['showComposerOptions']); ?>
    <?php endif;?>
    <?php $showAsLike = $decodedParams['showAsLike']; ?>
    <?php $showAsNested = isset($decodedParams['showAsNested']) ? $decodedParams['showAsNested'] : 1; ?>
    <?php $defaultViewReplyLink = isset($decodedParams['defaultViewReplyLink']) ? $decodedParams['defaultViewReplyLink'] : 0; ?>
    <?php $showDislikeUsers = $decodedParams['showDislikeUsers']; ?>
    <?php $showLikeWithoutIcon = $decodedParams['showLikeWithoutIcon']; ?>
    <?php $showLikeWithoutIconInReplies = $decodedParams['showLikeWithoutIconInReplies']; ?>
    <?php $taggingEnabled = $activityTaggingContent = implode($decodedParams['taggingContent'], ","); ?>
    <?php $this->commentShowBottomPost = $decodedParams['advancedactivity_comment_show_bottom_post']; ?>
    <?php $this->aaf_comment_like_box = $decodedParams['aaf_comment_like_box']; ?>
    <?php if ($this->aaf_comment_like_box && $showLikeWithoutIcon != 3): ?>
        <?php $showLikeWithoutIcon = 1; ?>
    <?php endif; ?>
<?php endif; ?>


<?php

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composernestedcomment.js');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_nested_comment_tag.js');

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/comment_photo.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js');
        $this->videoPlayerJs();

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Seaocore/externals/styles/style_infotooltip.css')
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Nestedcomment/externals/styles/style_nestedcomment.css');

if(!$this->isMobile){
        $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Advancedactivity/externals/styles/style_statusbar.css');
}
?>