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

<div class='siteevent_profile_cover_photo_wrapper'>
    <div class="siteevent_profile_cover_photo">
        <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
    </div>
    <div class="siteevent_profile_cover_name">
        <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
    </div>
</div>