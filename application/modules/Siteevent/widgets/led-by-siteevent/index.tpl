<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Event.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?> 

<?php
$itemTypeValue = $this->siteevent->getParent()->getType();
$eventOwnerLeader = 0;
if ($itemTypeValue == 'sitereview_listing') {
    $item = Engine_Api::_()->getItem('sitereview_listing', $this->siteevent->getParent()->getIdentity());
    $itemTypeValue = $itemTypeValue . $item->listingtype_id;
    $eventOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1);
} elseif ($itemTypeValue != 'user') {
    $eventOwnerLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1);
}

if (!$eventOwnerLeader)
    $itemTypeValue = 'user';
    
?>

<ul class="siteevent_side_widget siteevent_profile_side_event">
    <?php if ($itemTypeValue != 'user'): ?>
        <?php if (!empty($this->item)) : ?>
            <li>
                <?php echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'thumb.icon')); ?>
                <div class="siteevent_profile_side_event_info">
                    <div class="siteevent_profile_side_event_title">
                        <?php echo $this->htmlLink($this->item->getHref(), $this->item->getTitle()); ?>
                    </div>
                    <?php if ($this->siteevent->getParent()->getType() != 'user'): ?>
                        <div class="siteevent_listings_stats seaocore_txt_light f_small">
                            <?php echo $this->translate($this->title); ?>
                            <?php echo $this->translate(array('%s like', '%s likes', $this->item->like_count), $this->locale()->toNumber($this->item->like_count)) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endif; ?>
    <?php else: ?>
        <?php $leaders = $this->siteevent->getLedBys(false); ?>
        <li class="siteevent_profile_info_host">
            <?php echo $this->htmlLink($this->siteevent->getOwner()->getHref(), $this->itemPhoto($this->siteevent->getOwner(), 'thumb.icon')); ?>

            <div class="siteevent_profile_side_event_info">
                <div class="siteevent_profile_side_event_title">
                    <?php echo $this->htmlLink($this->siteevent->getOwner()->getHref(), $this->siteevent->getOwner()->getTitle()); ?>
                </div>
                <div class="siteevent_listings_stats">
										<?php if ($this->viewer()->getIdentity() != $this->siteevent->getOwner()->getIdentity()) : ?>
											<a class="mright5 fleft smoothbox" href='<?php echo $this->url(array('action' => 'messageowner', 'event_id' => $this->siteevent->getIdentity(), 'leader_id' => $this->siteevent->getOwner()->getIdentity()), "siteevent_specific", true) ?>'  title="<?php echo $this->translate("Contact"); ?>" title="<?php echo $this->translate("Contact"); ?>">
													<i class="siteevent_icon_strip siteevent_icon siteevent_icon_msg"></i>
											</a>
										<?php endif;?>
                    <?php $tab_id = Engine_Api::_()->siteevent()->getTabId('siteevent.profile-siteevent', 'user_profile_index'); ?>
                    <?php $href = $tab_id ? $this->siteevent->getOwner()->getHref() . '/tab/' . $tab_id : $this->siteevent->getOwner()->getHref(); ?>
                    <a class="fleft" href="<?php echo $href ?>" title="<?php echo $this->translate("Show all events"); ?>">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_calendar"></i>
                    </a>
                </div>
            </div>
        </li>
        <?php foreach ($leaders as $leader): ?>
            <?php if ($leader->getOwner()->getIdentity() == $this->siteevent->getOwner()->getIdentity()) : ?>
                <?php continue; ?>
            <?php endif; ?>
            <li class="siteevent_profile_info_host">
                <?php echo $this->htmlLink($leader->getOwner()->getHref(), $this->itemPhoto($leader->getOwner(), 'thumb.icon')); ?>
                <div class="siteevent_profile_side_event_info">
                <div class="siteevent_profile_side_event_title">
                    <?php echo $this->htmlLink($leader->getOwner()->getHref(), $leader->getOwner()->getTitle()); ?>
                </div>
                <div class="siteevent_listings_stats">
                    <a class="mright5 fleft smoothbox" href='<?php echo $this->url(array('action' => 'messageowner', 'event_id' => $this->siteevent->getIdentity(), 'leader_id' => $leader->getOwner()->user_id), "siteevent_specific", true) ?>'  title="<?php echo $this->translate("Contact"); ?>" title="<?php echo $this->translate("Contact"); ?>">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_msg"></i>
                    </a>
                    <?php $tab_id = Engine_Api::_()->siteevent()->getTabId('siteevent.profile-siteevent', 'user_profile_index'); ?>
                    <?php $href = $tab_id ? $leader->getOwner()->getHref() . '/tab/' . $tab_id : $leader->getOwner()->getHref(); ?>
                    <a class="fleft" href="<?php echo $href ?>" title="<?php echo $this->translate("Show all events"); ?>">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_calendar"></i>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>