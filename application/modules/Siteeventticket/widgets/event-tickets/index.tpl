<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class = 'siteevent_package_page o_hidden mbot15'>
  <div class='siteeventticket_list'>             
    <?php if (!empty($this->count)): ?>
      <!--<h3><?php echo $this->translate('Available Tickets') ?></h3> -->
      <ul class="siteeventticket_list_wrap o_hidden">                    
        <?php foreach ($this->paginator as $item): ?>
          <li>
            <div class="o_hidden">
              <div class="siteeventticket_price fleft">
                <?php
                if ($item->price > 0):echo $this->locale()->toCurrency($item->price, $currency);
                else: echo $this->translate('Free');
                endif;
                ?>
              </div>
              <?php if($this->showTicketStatus && $item->status == 'closed'): ?>  
                <div class="fright"><?php echo $this->translate('Closed');?></div>
              <?php endif; ?>
            </div>
            <div class="siteeventticket_title" title="<?php echo $this->translate($item->title); ?>"><?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 25)); ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>  
  </div>
  <!--ADD BUTTON-->
  
  <?php if(Engine_Api::_()->siteeventticket()->bookNowButton($this->siteevent) && ($this->siteevent->isRepeatEvent() || (!$this->siteevent->isRepeatEvent() && !$this->isEventFull))): ?>
  
    <div>
      <?php if(!empty($this->occurrence_id)): ?>
        <?php echo $this->htmlLink(array('route' => "siteeventticket_ticket", 'action' => 'buy', "event_id" => $this->event_id, 'occurrence_id' => $this->occurrence_id), $this->translate('Book Now'), array('class' => "siteevent_buttonlink"));?>
      <?php else: ?>  
        <?php echo $this->htmlLink(array('route' => "siteeventticket_ticket", 'action' => 'buy', "event_id" => $this->event_id), $this->translate('Book Now'), array('class' => "siteevent_buttonlink"));?>
      <?php endif; ?>  
    </div>  
  
  <?php elseif($this->showEventFullStatus && $this->isEventFull):?>
        <div class="siteevent_event_status">
            <div class="siteevent_event_status_box event_full mtop10">
                <b>
                    <span><?php echo $this->translate("Event is Full"); ?></span>
                </b>
            </div>
        </div>

        <?php if($this->viewer_id): ?>
            <div class="f_small mtop5 txt_center">
              <?php if(!$this->waitlist_id && $this->viewer_id != $this->siteevent->owner_id): ?>    
                <a href="<?php echo $this->url(array('controller' => 'waitlist', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->occurrence_id), 'siteevent_extended'); ?>" class="smoothbox">
                <?php echo $this->translate("Add me to waitlist"); ?>
                </a>
              <?php elseif($this->waitlist_id): ?>
                <?php echo $this->translate("You are added to the waitlist."); ?>                
              <?php endif; ?>  
              <?php $totalEventsInWaiting = Engine_Api::_()->getDbTable('waitlists', 'siteevent')->getColumnValue(array('occurrence_id' => $this->occurrence_id, 'columnName' => 'COUNT(*) AS totalEventsInWaiting'));?>
              <?php if($totalEventsInWaiting): ?>  
                <?php echo $this->translate(" (%s member(s) in waitlist)", $totalEventsInWaiting);?>
              <?php endif; ?>  
            </div>
        <?php endif; ?>  
    <?php endif; ?>
</div>
