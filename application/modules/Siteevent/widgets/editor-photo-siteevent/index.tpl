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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css'); ?>

<div class="siteevent_editor_profile_info">
    <div class="siteevent_editor_profile_photo">
        <?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile', '', array('align' => 'center'))) ?>
    </div>

    <?php if (!$this->user->isSelf($this->viewer()) && $this->user->email): ?>
        <div class="siteevent_editor_event_stat"><b><?php echo $this->htmlLink(array('route' => 'siteevent_review_editor', 'action' => 'editor-mail', 'user_id' => $this->user->user_id), $this->translate('Email %s', $this->user->getTitle()), array('class' => 'smoothbox siteevent_icon_send buttonlink')) ?></b></div>
    <?php endif; ?>

    <div class="siteevent_editor_event_stat">
        <?php echo $this->htmlLink($this->user->getHref(), $this->translate('View full profile'), array('class' => 'siteevent_icon_editor_profile buttonlink')); ?></b>
    </div>

    <div class="siteevent_editor_event_stat">
        <?php echo $this->htmlLink(array('route' => "siteevent_review_editor", 'action' => 'home'), $this->translate('View all Editors'), array('class' => 'siteevent_icon_editor buttonlink')) ?>
    </div>    
</div>	