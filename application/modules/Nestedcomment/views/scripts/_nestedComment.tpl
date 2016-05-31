<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _nestedComment.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 

        $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css')
                ->appendStylesheet($this->layout()->staticBaseUrl
                        . 'application/modules/Nestedcomment/externals/styles/style_nestedcomment.css');
        $this->headTranslate(array('Write a comment...', 'Write a reply...', 'Attach a Photo', 'Post a comment...', 'Post a reply...'));
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composernestedcomment.js');
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_nested_comment_tag.js');
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/comment_photo.js');
        $this->headScript()
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/core.js')
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer.js')
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_tag.js')
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/like.js')
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_photo.js')
                ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Nestedcomment/externals/scripts/composer_link.js');
        ?>
<?php $photoLightboxComment = 0;?>
<?php $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();?>
<?php if((isset($params['lightbox_type']) &&  $params['lightbox_type'] == 'photo') || isset($params['action']) && $params['action'] == 'light-box-view'): ?> 
  <?php $photoLightboxComment = 1;?>
<?php endif;?>

<div class="layout_nestedcomment_comments">
<?php 

    if(isset($params['moduleName']) && $params['moduleName'])  {
    $nested_comment_params = Engine_Api::_()->nestedcomment()->getParams(array('resource_type' => $subjectType, 'moduleName' => $params['moduleName']));
    } else {
    $nested_comment_params = Engine_Api::_()->nestedcomment()->getParams(array('resource_type' => $subjectType));
    }
    if($nested_comment_params) {
        $taggingContent = $nested_comment_params['taggingContent'];
        $showAsNested = $nested_comment_params['showAsNested'];
        $showAsLike = $nested_comment_params['showAsLike'];
        $loaded_by_ajax = $nested_comment_params['loaded_by_ajax'];
        $showComposerOptions = $nested_comment_params['showComposerOptions'];
        $showDislikeUsers = $nested_comment_params['showDislikeUsers'];
        $showLikeWithoutIcon = $nested_comment_params['showLikeWithoutIcon'];
        $showLikeWithoutIconInReplies = $nested_comment_params['showLikeWithoutIconInReplies'];
        
        if(!isset($nested_comment_params['commentsorder'])) {
          $commentsorder = 1;
        } else {
          $commentsorder = $nested_comment_params['commentsorder'];
        } 
        
        $nestedCommentPressEnter = Engine_Api::_()->getApi('settings', 'core')->getSetting('nestedcomment.comment.pressenter', 1);
            echo $this->content()->renderWidget("nestedcomment.comments", array("type" => $this->subject()->getType(), "id" => $this->subject()->getIdentity(), "taggingContent" => $taggingContent, 'showAsNested' => $showAsNested, 'showAsLike' => $showAsLike, 'showDislikeUsers' => $showDislikeUsers, 'showLikeWithoutIcon' => $showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies, 'nestedCommentPressEnter' => $nestedCommentPressEnter, 'showComposerOptions' => $showComposerOptions, 'loaded_by_ajax' => $loaded_by_ajax, 'is_ajax_load' => true, 'commentsorder' => $commentsorder, 'photoLightboxComment' => $photoLightboxComment));
    }
?>
</div>