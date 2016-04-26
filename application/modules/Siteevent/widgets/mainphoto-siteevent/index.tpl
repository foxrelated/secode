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

<div class="siteevent_profile_cover_photo_wrapper prelative">
    <?php if (!empty($this->siteevent->featured) && !empty($this->featuredLabel)): ?> 
        <i title="<?php echo $this->translate('FEATURED'); ?>" class="siteevent_list_featured_label"></i>
    <?php endif; ?>
    <div class='siteevent_profile_cover_photo <?php if ($this->can_edit): ?>siteevent_photo_edit_wrapper<?php endif; ?>'>
        <?php if (!empty($this->can_edit)) : ?>
            <a class='siteevent_photo_edit' href="<?php echo $this->url(array('action' => 'change-photo', 'event_id' => $this->siteevent->event_id), "siteevent_dashboard", true) ?>">
                <i class="siteevent_icon"></i>
                <?php echo $this->translate('Change Picture'); ?>
            </a>
        <?php endif; ?>
        <?php if ($this->siteevent->newlabel): ?>
            <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
        <?php endif; ?>

        <?php echo $this->itemPhoto($this->siteevent, 'thumb.main', '', array('align' => 'center')); ?>
    </div>
    <?php if (!empty($this->siteevent->sponsored) && !empty($this->sponsoredLabel)): ?>
        <div class="siteevent_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>;'>
            <?php echo $this->translate('SPONSORED'); ?>
        </div>
    <?php endif; ?>
    <?php if ($this->ownerName): ?>
        <div class='siteevent_profile_cover_name'>
            <?php echo $this->htmlLink($this->siteevent->getOwner()->getHref(), $this->siteevent->getOwner()->getTitle()) ?>
        </div>
    <?php endif; ?>
</div>

