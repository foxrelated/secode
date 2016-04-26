<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _smiliesComment.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>               
<?php $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);?>
<div id="emoticons-nested-comment-icons" style="display:none;">
    <span id="emoticons-nested-comment-button" class="adv_post_smile" onclick="setNestedCommentEmoticonsBoard(this);" style="display:block;" title="<?php echo $this->translate('Insert Emoticons') ?>">
        <p class="adv_post_compose_menu_show_tip adv_composer_tip">
          <?php echo $this->translate("Insert Emoticons") ?>
          <img alt="" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />
        </p>
        <span id="emoticons-nested-comment-board"  class="seaocore_comment_embox seaocore_comment_embox_closed" >
          <span class="seaocore_comment_embox_arrow"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/tooltip_arrow_top.png" alt="" /></span>
            <span class="seaocore_comment_embox_title">
                <span class="fleft" id="emotion_nested_comment_label"></span>
                <span class="fright"id="emotion_nested_comment_symbol" ></span>
            </span>
            <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key=>$tag):?>         
                <span class="seaocore_comment_embox_icon" onmouseover='setNestedCommentEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/","$3", $tag)))?>","<?php echo $this->string()->escapeJavascript($tag_key)?>", this)' onclick='addNestedCommentEmotionIcon("<?php echo $this->string()->escapeJavascript($tag_key)?>", this)'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/","$3", $tag))."&nbsp;".$tag_key; ?>">
                    <?php echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"".$this->layout()->staticBaseUrl."application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag); ?>
                </span>
            <?php endforeach;?>
        </span>					
    </span>
</div>