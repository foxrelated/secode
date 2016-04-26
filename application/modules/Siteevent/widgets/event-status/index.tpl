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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php if ($this->siteevent->closed): ?>
    <!--Cancelled Event Start-->
    <div class="siteevent_event_status">
        <div class="siteevent_event_status_box event_canceled">
            <b><?php echo $this->translate("Event has cancelled."); ?></b>
        </div>
    </div>
    <!--Cancelled Event End-->
<?php elseif ($this->isEventFinished): ?>
    <div class="siteevent_event_status">
        <div class="siteevent_event_status_box event_canceled">
            <b>
                <?php if ($this->isLastOccurrenceEnd || empty($this->siteevent->repeat_params)): ?>
                    <?php echo $this->translate("Event has ended."); ?>
                <?php elseif ($this->siteevent->repeat_params): ?>
                    <?php echo $this->translate("This occurrence has ended."); ?>
                <?php endif; ?> 
            </b>
            <?php if ($this->siteevent->repeat_params && $this->nextOccurrenceDate['starttime'] && !$this->isLastOccurrenceEnd): ?>
                <span class="sub_txt">
                    <?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
                    <?php $date = $this->locale()->toEventDateTime($this->nextOccurrenceDate['starttime'], array('size' => $datetimeFormat)); ?>
                    <?php echo $this->translate("Next Occurrence:"); ?>
                    <?php echo $this->htmlLink($this->siteevent->getHref(array('occurrence_id' => $this->next_occurrence_id)), $date) ?> 
                </span>

            <?php endif; ?> 
        </div>
    </div>    
<?php else: ?>
    <!--Open Event Start-->
    <div class="siteevent_event_status">
        <div class="siteevent_event_status_box event_open">
            <b>
                <?php if ($this->futureEvent): ?>

                    <?php if (!$this->isFirstOccurrenceStart || empty($this->siteevent->repeat_params)): ?>
                        <?php echo $this->translate("Event has not started."); ?>
                    <?php else: ?>
                        <?php echo $this->translate("This occurrence has not started."); ?>
                    <?php endif; ?> 

                <?php else: ?>

                    <?php if ($this->siteevent->repeat_params): ?>
                        <?php echo $this->translate("This occurrence is ongoing."); ?>
                    <?php else: ?>
                        <?php echo $this->translate("Event is ongoing."); ?>
                    <?php endif; ?> 

                <?php endif; ?>
            </b>

        </div>
        
        <?php if($this->isEventFull && $this->showEventFullStatus): ?>
            <div class="siteevent_event_status_box event_full mtop10">
                <b>
                    <span><?php echo $this->translate("Event is Full"); ?></span>
                </b>
            </div>
          
            <?php if($this->viewer_id): ?>
                <div class="f_small mtop5 txt_center">
                  <?php if(!$this->waitlist_id && $this->viewer_id != $this->siteevent->owner_id): ?>  
                    <a href="<?php echo $this->url(array('controller' => 'waitlist', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox">
                    <?php echo $this->translate("Add me to waitlist"); ?>
                    </a>
                  <?php elseif($this->inWaitlist): ?>
                    <?php echo $this->translate("You are added to the waitlist."); ?>  
                  <?php endif; ?>
                  <?php $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('occurrence_id' => $this->occurrence_id, 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));?>
                  <?php if($totalEventsInWaiting): ?>  
                    <?php echo $this->translate(" (%s member(s) in waitlist)", $totalEventsInWaiting);?>
                  <?php endif; ?>  
                </div>
            <?php endif; ?>
        <?php endif; ?>           

        <?php if ($this->showButton && !$this->isEventFull): ?>
            <?php $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $this->occurrence_id);?>
            <?php if(Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>

            <?php elseif(!Engine_Api::_()->siteevent()->isTicketBasedEvent() && $this->viewer_id && empty($occurrence->waitlist_flag)): ?>
                <?php $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->siteevent->event_id, 'DESC', $this->occurrence_id); ?>
                <?php if (null === $this->siteevent->membership()->getRow($this->viewer) && strtotime($endDate) > time()): ?>
                    <?php if ($this->siteevent->membership()->isResourceApprovalRequired()): ?>
                        <div class="siteevent_profile_event_info_btns txt_center mtop10">
                            <div class="seaocore_button">
                                <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Request Invite"); ?></span></a></div>
                        </div>
                    <?php else: ?>
                        <div class="siteevent_profile_event_info_btns txt_center mtop10">
                            <div class="seaocore_button">

                                <?php if (strtotime($endDate) > time()) : ?>
                                    <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox"><span><?php echo $this->translate("Join Event"); ?></span></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <!--Open Event End-->
<?php endif; ?>