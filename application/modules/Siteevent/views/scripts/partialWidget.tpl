<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialWidget.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<li> 
    <?php
    echo $this->htmlLink(
            $this->siteevent_video->getHref(), $this->itemPhoto($this->siteevent_video, 'thumb.icon', $this->siteevent_video->getTitle()), array('class' => 'list_thumb', 'title' => $this->siteevent_video->getTitle())
    )
    ?>
    <div class='seaocore_sidebar_list_info'>
        <div class='seaocore_sidebar_list_title'>
            <?php echo $this->htmlLink($this->siteevent_video->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->siteevent_video->getTitle(), 16), array('title' => $this->siteevent_video->getTitle(), 'class' => 'siteevent_video_title')); ?> 	
        </div>
        <div class='seaocore_sidebar_list_details'>