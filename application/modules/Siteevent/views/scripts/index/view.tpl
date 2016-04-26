<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css') ?>

<div class="layout_left">
    <div class="siteevent_profile_cover_photo_wrapper" style="margin-bottom:15px; ">
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

            <?php echo $this->itemPhoto($this->siteevent, 'thumb.profile', '', array('align' => 'center')); ?>
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
    <?php $isEventFull = $this->siteevent->isEventFull(array('occurrence_id' => $this->occurrence_id));?>
    <?php if($isEventFull): ?>
    
    <?php else: ?>
    
        <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
            <?php $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $this->occurrence_id);    ?>
            <div class="siteevent_event_status siteevent_side_widget">
                <?php  $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->siteevent->event_id, 'DESC', $this->occurrence_id);?>
                <?php $row = $this->siteevent->membership()->getRow($this->viewer); ?>
                <?php if (null === $row && !$this->siteevent->closed && (strtotime($endDate) > time())): ?>
                    <?php if ($this->siteevent->membership()->isResourceApprovalRequired() && $this->viewer->getIdentity() && empty($occurrence->waitlist_flag)): ?>
                        <div class="siteevent_profile_event_info_btns txt_center">
                            <div class="seaocore_button">
                                <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Request Invite"); ?></span></a>
                            </div>
                        </div>
                        <?php
                    //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN EVENT LINK.
                    elseif (!$this->siteevent->closed && empty($occurrence->waitlist_flag)):
                        ?>
                        <?php

                        if ((strtotime($endDate) > time()) && $this->siteevent->isViewableByNetwork() && $this->viewer->getIdentity()) :
                            ?>
                            <div class="siteevent_profile_event_info_btns txt_center">  
                                <div class="seaocore_button">
                                    <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Join Event"); ?></span></a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php elseif ($row->active): ?>
                    <div class="siteevent_profile_event_info_btns txt_center">
                        <div class="seaocore_button">
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'leave', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Leave Event"); ?></span></a>
                        </div>
                    </div>
                <?php elseif (!$row->resource_approved && $row->user_approved): ?>  
                    <div class="siteevent_profile_event_info_btns txt_center">
                        <div class="seaocore_button">
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Cancel Invite Request"); ?></span></a>
                        </div>
                    </div>
                <?php elseif (!$row->user_approved && $row->resource_approved && strtotime($endDate) > time()): ?>
                    <div class="siteevent_profile_event_info_btns txt_center">
                        <div class="seaocore_button">
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Accept Event Invite"); ?></span></a>
                        </div>
                    </div>
                    <div class="siteevent_profile_event_info_btns txt_center mtop10">  
                        <div class="seaocore_button">
                            <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'reject', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Ignore Event Invite"); ?></span></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="layout_middle">
    <div id='profile_status'>
        <h2>
            <?php echo $this->siteevent->getTitle() ?>
        </h2>
    </div>
    
    <?php  $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->siteevent->event_id, 'DESC', $this->occurrence_id);?>
    <?php $row = $this->siteevent->membership()->getRow($this->viewer); ?>
    <?php if (null === $row && !$this->siteevent->closed && (strtotime($endDate) > time())): ?>
        <?php if ($this->siteevent->membership()->isResourceApprovalRequired() && $this->viewer->getIdentity()): ?>
          <?php $url = $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended');?>
          
            <?php if($this->siteevent->body):?> 
            <div class='tip'> 
                <span>
                    <?php //echo $this->translate('You would not able to view this event because you are not member of this event. If, you like to view this event then you have to send the request to join the event. Please %1$sclick here%2$s to send the request.', "<a href='$url' class='smoothbox'>", "</a>"); ?>
                    <?php echo $this->siteevent->body;?>
                </span>
                </div>
            <?php endif;?>
          
          <?php
            //CHECK IF THE EVENT IS PAST EVENT THEN WE WILL NOT SHOW JOIN EVENT LINK.
            elseif (!$this->siteevent->closed):
                ?>
                <?php
               
                if ((strtotime($endDate) > time()) && $this->siteevent->isViewableByNetwork() && $this->viewer->getIdentity()) :
                    ?>
                    <div class='tip'> 
                <span>
                    <?php //echo $this->translate('You would not able to view this event because you are not member of this event. If, you like to view this event then you have to send the request to join the event. Please %1$sclick here%2$s to send the request.', "<a href='$url' class='smoothbox'>", "</a>"); ?>
                    <?php echo $this->siteevent->body;?>
                </span>
                </div>
                <?php endif; ?>
        <?php endif;?>
     <?php endif;?> 
     
     <?php if (!$row->user_approved && $row->resource_approved && strtotime($endDate) > time()): ?> 
      
      <div class='tip'> 
        <span>
            <?php if($this->siteevent->body):?>  
            <?php //echo $this->translate('You have been invited to view this event. Please accept/ignore the event request clicking on a button in the left side of this page.'); ?>
            <?php echo $this->siteevent->body;?>
            <?php endif;?>
        </span>
      </div>
                
     <?php elseif(!$row->resource_approved && $row->user_approved):?>
        
     <div class='tip'> 
        <span>
            <?php echo $this->siteevent->body;?>
        </span>
      </div>
     
      <?php endif;?> 
    <?php if ($this->siteevent->closed): ?>
        <div class="tip"> 
            <span> <?php echo $this->translate('This event has been cancelled.'); ?> </span>
        </div>  
    <?php endif; ?>
    
    <?php if(!$this->viewer->getIdentity()): ?>
        <div class="tip">
            <span> 
                <?php echo $this->translate('Please %1$slogin%2$s to view this event.', '<a href="' . $this->url(array(), "user_login") . '">', '</a>'); ?>
            </span>
        </div>    
    <?php endif; ?>

    <?php if ($this->overview && !empty($this->can_edit_overview) && !empty($this->can_edit)): ?>
        <div class="generic_layout_container layout_core_container_tabs">
            <div class='tabs_alt tabs_parent'>
                <ul class="main_tabs">
                    <li id="overview" class="active">
                        <?php echo $this->translate('Overview') ?>
                    </li>
                </ul>
            </div>
            <div id="overviewcontentshow" >
                <?php echo $this->content()->renderWidget("siteevent.overview-siteevent", array()); ?>
            </div>
        </div>
    <?php endif; ?>
</div>        