<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>

<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteevent_overview_siteevent')
        };
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
    <?php if (!empty($this->overview) && $this->siteevent->owner_id == $this->viewer_id): ?>
        <div class="seaocore_add">
            <a href='<?php echo $this->url(array('action' => 'overview', 'event_id' => $this->siteevent->event_id), "siteevent_specific", true) ?>'  class="icon_siteevents_overview buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
        </div>
    <?php endif; ?>

    <div>
        <?php if (!empty($this->overview)): ?>
            <div class="siteevent_profile_overview">
                <?php echo $this->overview ?>
            </div>
        <?php else: ?>
            <div class="tip">
                <span>
                    <?php $url = $this->url(array('action' => 'overview', 'event_id' => $this->siteevent->event_id), "siteevent_specific", true) ?>
                    <?php echo $this->translate('You have not composed an overview for your event. %1$sClick here%2$s to compose it from the Dashboard of your event.', "<a href='$url'>", "</a>"); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
//CHECK IF THE FACEBOOK PLUGIN IS ENABLED AND ADMIN HAS SET ONLY SHOW FACEBOOK COMMENT BOX THEN WE WILL NOT SHOW THE SITE COMMENT BOX.
$fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
$success_showFBCommentBox = 0;

if (!empty($fbmodule) && !empty($fbmodule->enabled) && $fbmodule->version > '4.2.7p1') {

    $success_showFBCommentBox = Engine_Api::_()->facebookse()->showFBCommentBox('siteevent');
}
?>

<?php if (empty($this->isAjax) && $this->showComments && $success_showFBCommentBox != 1): ?>
    <?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listNestedComment.tpl';
    ?>
<?php endif; ?>

<?php if (empty($this->isAjax) && $success_showFBCommentBox != 0  && $this->showComments): ?>
    <?php echo $this->content()->renderWidget("Facebookse.facebookse-comments", array("type" => $this->siteevent->getType(), "id" => $this->siteevent->event_id, 'task' => 1, 'module_type' => 'siteevent', 'curr_url' => ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->siteevent->getHref())); ?>
<?php endif; ?>  