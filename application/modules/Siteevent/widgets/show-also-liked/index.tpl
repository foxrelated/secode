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

<ul class="seaocore_sidebar_list">
    <?php foreach ($this->paginator as $siteevent_video): ?>
        <?php
        $this->partial()->setObjectKey('siteevent_video');
        echo $this->partial('application/modules/Siteevent/views/scripts/partialWidget.tpl', $siteevent_video);
        ?>		    
        <?php
        $siteevent->event_title = Engine_Api::_()->getItem('siteevent_event', $siteevent_video->event_id);
        $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.title.truncation', 18);
        $tmpBody = strip_tags($siteevent->event_title);
        $event_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
        ?>
        <?php echo $this->translate("in ") . $this->htmlLink($siteevent_video->getHref(), $event_title, array('title' => $siteevent->event_title)) ?>    
        </div>
        <div class="seaocore_sidebar_list_details clr"> 
            <?php echo $this->translate(array('%s like', '%s likes', $siteevent_video->like_count), $this->locale()->toNumber($siteevent_video->like_count)) ?>,
            <?php echo $this->translate(array('%s view', '%s views', $siteevent_video->view_count), $this->locale()->toNumber($siteevent_video->view_count)) ?>
        </div>
        </div>
        </li>
    <?php endforeach; ?>
</ul>