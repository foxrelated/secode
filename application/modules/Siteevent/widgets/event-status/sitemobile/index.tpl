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

<?php if ($this->siteevent->closed): ?>
    <!--Cancelled Event Start-->
    <div class="siteevent_event_status">
        <div class="siteevent_event_status_box event_canceled t_l">
            <b><?php echo $this->translate("Event has cancelled."); ?></b>
        </div>
    </div>
    <!--Cancelled Event End-->
<?php elseif ($this->isEventFinished): ?>
    <div class="siteevent_event_status">
        <div class="siteevent_event_status_box event_canceled t_l">
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
      <div class="siteevent_event_status_box event_open t_l">
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
       <?php if ($this->showButton && $this->viewer->getIdentity()): ?>
            <?php $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->siteevent->event_id, 'DESC', $this->occurrence_id); ?>
            <?php if (null === $this->siteevent->membership()->getRow($this->viewer) && strtotime($endDate) > time()): ?>
                <?php if ($this->siteevent->membership()->isResourceApprovalRequired()): ?>
                    <div class="clr t_l">
                      <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox sm-ui-btn" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                        <i class="ui-icon-plus"></i>
                        <span><?php echo $this->translate("Request Invite"); ?></span>
                      </a>
                    </div>
                <?php else: ?>
                  <?php if (strtotime($endDate) > time()) : ?>
                    <div class="clr t_l">  
                      <a href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox sm-ui-btn" data-role='button' data-inset='false' data-mini='true' data-corners='false' data-shadow='true'>
                        <i class="ui-icon-plus"></i>
                        <span><?php echo $this->translate("Join Event"); ?></span>
                      </a>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>  
    </div>
    <!--Open Event End-->
<?php endif; ?>