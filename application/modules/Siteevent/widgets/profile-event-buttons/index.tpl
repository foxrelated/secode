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
<div class="event_profile_buttons">
  
    <?php if (Engine_Api::_()->siteevent()->hasTicketEnable() && !empty($this->showButtons) && in_array('mytickets', $this->showButtons) && $this->viewer_id): ?>
        <a href='<?php echo $this->url(array('action' => 'my-tickets'), "siteeventticket_order", true) ?>'  class='siteevent_buttonlink mbot5'><?php echo $this->translate('My Tickets'); ?></a>
    <?php endif; ?>  

    <?php if (!empty($this->showButtons) && in_array('uploadPhotos', $this->showButtons) && $this->allowed_upload_photo && $this->allowPhotoVideo && $this->viewer_id): ?>
        <a href='<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'content_id' => Engine_Api::_()->siteevent()->getTabId('siteevent.photos-siteevent')), "siteevent_photoalbumupload", true) ?>'  class='siteevent_buttonlink mbot5'><?php echo $this->translate('Upload Photos'); ?></a>
    <?php endif; ?>

    <?php if (!empty($this->showButtons) && in_array('uploadVideos', $this->showButtons) && $this->allowed_upload_video && $this->allowPhotoVideo && $this->viewer_id): ?>
	
        <?php if ($this->type_video): ?>
            <a href='<?php echo $this->url(array('action' => 'index', 'event_id' => $this->siteevent->event_id, 'content_id' => Engine_Api::_()->siteevent()->getTabId('siteevent.video-siteevent')), "siteevent_video_upload", true) ?>'  class='siteevent_buttonlink mbot5'><?php echo $this->translate('Add Videos'); ?></a>
        <?php else: ?>
            <?php echo $this->htmlLink(array('route' => "siteevent_video_create", 'event_id' => $this->siteevent->event_id, 'content_id' => Engine_Api::_()->siteevent()->getTabId('siteevent.video-siteevent')), $this->translate('Add Videos'), array('class' => 'siteevent_buttonlink mbot5')) ?>
        <?php endif; ?>

    <?php endif; ?>    
            
    <?php if (!empty($this->showButtons) && in_array('signIn', $this->showButtons) && !$this->viewer_id): ?>
        <a href='<?php echo $this->url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), "user_login", true) ?>'  class='siteevent_buttonlink mbot5'><?php echo $this->translate('SIGN IN'); ?></a>
    <?php endif; ?>      

    <?php if (!empty($this->showButtons) && in_array('signUp', $this->showButtons) && !$this->viewer_id): ?>
        <a href='<?php echo $this->url(array(), "user_signup", true) ?>'  class='siteevent_buttonlink mbot5'><?php echo $this->translate('SIGN UP'); ?></a>
    <?php endif; ?>     
</div>


