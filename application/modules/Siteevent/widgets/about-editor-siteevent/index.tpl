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

<ul class="siteevent_profile_side_event siteevent_side_widget">
    <li>
        <?php echo $this->htmlLink($this->editor->getHref(), $this->itemPhoto($this->user, 'thumb.icon'), array('class' => 'popularmembers_thumb', 'title' => $this->user->getTitle()), array('title' => $this->user->getTitle())) ?>
        <div class='siteevent_profile_side_event_info'>
            <div class='siteevent_profile_side_event_title'>
                <?php echo $this->translate("By %s", $this->htmlLink($this->editor->getHref(), $this->user->getTitle(), array('title' => $this->user->username))); ?>
            </div>
            <div class='siteevent_profile_side_event_stats'>
                <?php echo $this->viewMore($this->editor->details, 64); ?>
            </div>
        </div>
    </li>
</ul>


