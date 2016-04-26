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

<h2><?php echo $this->translate('Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments') ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'nestedcomment', 'controller' => 'module', 'action' => 'index'), $this->translate("Back to Manage Modules for Nested Comments"), array('class' => 'seaocore_icon_back buttonlink')) ?>

<br style="clear:both;" /><br />
<div class="seaocore_settings_form">
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">

    en4.core.runonce.add(function () {
        hideOptions('<?php echo $this->showAsLike; ?>');
        //showHideRepliesOptions('<?php //echo $this->showAsNested; ?>');
    })

    function hideOptions(option) {
        var form = document.getElementsByClassName("global_form");
        var RadioButton = form[0].elements["showAsNested"];
        if (option == 1) {
            $('showDislikeUsers-wrapper').style.display = 'none';
            $('showLikeWithoutIcon-wrapper').style.display = 'none';
            $('showLikeWithoutIconInReplies-wrapper').style.display = 'none';
            $("defaultViewReplyLink-wrapper").style.display = 'none';
        } else {
            $('showDislikeUsers-wrapper').style.display = 'block';
            $('showLikeWithoutIcon-wrapper').style.display = 'block';
            $('showLikeWithoutIconInReplies-wrapper').style.display = 'block';
            $("defaultViewReplyLink-wrapper").style.display = 'block';
//            $('showDislikeUsers-wrapper').style.display = 'block';
//            if (typeof RadioButton == 'undefined') {
//                $('showLikeWithoutIcon-wrapper').style.display = 'block';
//            } else {
//                //$('showLikeWithoutIcon-wrapper').style.display = 'block'
//            }
//
//            var RadioButtonshowAsNested = form[0].elements["showAsNested"];
//            
//            if (RadioButtonshowAsNested && getRadioButtonValue(RadioButtonshowAsNested) == 0) {
//                $('showLikeWithoutIconInReplies-wrapper').style.display = 'block';
//                $("defaultViewReplyLink-wrapper").style.display = 'block';
//            }

        }

        //if (RadioButton && getRadioButtonValue(RadioButton) == 0 && getRadioButtonValue(form[0].elements["showAsLike"]) == 0) {
           // $('showLikeWithoutIcon-wrapper').style.display = 'block';
       // }
    }

    function getRadioButtonValue(radioButton) {
        for (var i = 0; i < radioButton.length; i++)
        {
            if (radioButton[i].checked)
            {
                return radioButton[i].value;
            }
        }

        return '';
    }

    function hideFeedOptions(option) {

        var form = document.getElementsByClassName("global_form");
        var RadioButton = form[0].elements["aaf_comment_like_box"];

        if (RadioButton && getRadioButtonValue(RadioButton) == 1) {
            $('showLikeWithoutIcon-wrapper').style.display = 'none';
        } else {
            $('showLikeWithoutIcon-wrapper').style.display = 'block';
        }

        if (RadioButton && getRadioButtonValue(form[0].elements["showAsLike"]) == 1) {
            $('showLikeWithoutIcon-wrapper').style.display = 'none';
        }

    }

    function showHideRepliesOptions(option) {
        var form = document.getElementsByClassName("global_form");
        var RadioButton = form[0].elements["showAsLike"];
        var RadioButtonShowAsNested = form[0].elements["showAsNested"];
        if (option == 1 && (RadioButton && getRadioButtonValue(RadioButton) == 0) && (RadioButtonShowAsNested && getRadioButtonValue(RadioButtonShowAsNested) == 1)) {
            $("showLikeWithoutIconInReplies-wrapper").style.display = 'block';
            $("defaultViewReplyLink-wrapper").style.display = 'block';
        } else {
            $("showLikeWithoutIconInReplies-wrapper").style.display = 'none';
            $("defaultViewReplyLink-wrapper").style.display = 'none';
        }
    }
</script>    