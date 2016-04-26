<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _eventNonPhoto.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<span class='feed_attachment_<?php echo $this->item->getType() ?>'>
    <div>
        <?php $attribs = Array(); ?>
        <div>
            <div class='feed_item_link_title'>
                <?php
                echo $this->htmlLink($this->item->getHref(), $this->item->getTitle() ? $this->item->getTitle() : '', $attribs);
                ?>
            </div>
            <div class='feed_item_link_desc'>
                <?php echo $this->viewMore($this->item->getDescription()) ?>
            </div>
        </div>
    </div>
</span>