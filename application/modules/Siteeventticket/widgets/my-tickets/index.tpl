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

<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/scripts/core.js'); ?>

<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css');
?>

<?php if (empty($this->isajax)): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl'; ?>
<?php endif; ?>

<?php
$orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
$eventTableObj = Engine_Api::_()->getDbtable('events', 'siteevent');
$paginationCount = @count($this->paginator);
?>

<div id="siteevent_manage_event">
  <?php if (empty($this->call_same_action)) : ?>
    <div><h3><?php echo $this->translate('My Tickets') ?></h3></div>
  <?php endif; ?>

  <!--NAVIGATION, PAST & CURRENT-->
  <div id="manage_order_tab">
  	<div class="tabs_alt">
    <ul class="main_tabs">
    	<li <?php if ($this->viewType == 'current') echo 'class="active"'; ?>>
        <a href="javascript:void(0);" onclick="filter_tickets('current');" >	
          <?php echo $this->translate('Current Orders'); ?>
          <span class="tab_alt_count"><?php echo $this->locale()->toNumber($this->totalUpcomingOrderCount); ?></span>
        </a>
      </li>
      <li <?php if ($this->viewType == 'past') echo 'class="active"'; ?>>
        <a href="javascript:void(0);" onclick="filter_tickets('past');" >
          <?php echo $this->translate('Past Orders'); ?>
          <span class="tab_alt_count"><?php echo $this->locale()->toNumber($this->totalPastOrderCount); ?></span>
        </a>
      </li>
    </ul>
    </div>
    
    <div id="manage_tickets">
      <?php if ($paginationCount): ?>    
        <article class="siteevent_orders" id="siteevent_browse_list">   
          <?php if ($this->paginator->getTotalItemCount() > 0): ?>
            <?php $prev_date = 0; ?>
            <?php foreach ($this->paginator as $order): ?>
                <?php $eventObj = Engine_Api::_()->getItem('siteevent_event', $order->event_id); ?>
                <?php if( !($eventObj instanceof Core_Model_Item_Abstract) ):?>
                  <?php continue; ?>
                <?php endif; ?>  
                <div class="siteevent_order_list" id="userlist_list_<?php echo $order->order_id; ?>">
                <?php $occurrenceObj = Engine_Api::_()->getItem('siteevent_occurrence', $order->occurrence_id); ?>
                <!--Photo-->
                <section class="siteevent_order_image">
                    <div> 
                        <?php echo $this->htmlLink($eventObj->getHref(), $this->itemPhoto($eventObj, 'thumb.icon', '', array('align' => 'center'))) ?>
                    </div>
                </section>
                
                <!--Details-->
                <section class="siteevent_order_info">
                  <div>              
                    <div class='siteevent_order_heading'>
                      <?php if( $eventObj instanceof Core_Model_Item_Abstract ):?>  
                        <?php echo $this->htmlLink($eventObj->getHref(), $eventObj->getTitle()) ?>   
                      <?php else: ?>
                        <?php echo $this->translate("Event Deleted") ?>
                      <?php endif; ?>
                    </div>
                    <p class="seaocore_txt_light">
                      <?php
                      echo $this->locale()->toDate($order->creation_date, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($order->creation_date, array('size' => $datetimeFormat));
                      echo " - ";
                      echo $this->locale()->toEventTime($order->creation_date, array('size' => $datetimeFormat));
                      ?>
                    </p>
                    <?php $ticketDetails = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket')->getOrderTicketsDetail(array('order_id' => $order->order_id, 'columns' => array('title', 'quantity'))); ?>
                    <?php foreach($ticketDetails as $ticketDetail): ?>
                        <p class="seaocore_txt_light">
                            <i><?php echo "$ticketDetail->title : $ticketDetail->quantity"?></i>
                        </p>    
                    <?php endforeach; ?>
                  </div>
                </section>
                
                <!--Options-->
                <section class="siteevent_order_option">
                  <div>
                    <a class="seaocore_icon_view" href="<?php echo $this->url(array('action' => 'view', 'order_id' => $order->order_id, 'event_id' => $order->event_id), 'siteeventticket_order', true) ?>"><?php echo $this->translate("View Order"); ?>
                    </a> 
                   </div>
  
                    <?php 
                    //CHECKS MOVED IN FUNCTION 
                    $showPrintLinks = $order->showPrintLink();
                    if ($showPrintLinks) :
                      ?>
                        <?php $tempPrint = $this->url(array('action' => 'print-ticket', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($order->order_id), 'event_id' => $order->event_id), 'siteeventticket_order', true); ?>
                        <div><a class="seaocore_icon_print" href="<?php echo $tempPrint; ?>" target="_blank"><?php echo $this->translate("Print Tickets"); ?></a></div>
                        
                        <?php if($this->showSendTicketLink): ?><div>
                            <?php echo $this->htmlLink($this->url(array('action' => 'send-email', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($order->order_id), 'event_id' => $order->event_id), 'siteeventticket_order', true), $this->translate('Email Order'), array('class' => 'smoothbox siteevent_icon_send')) ?></div><?php endif; ?>
  
                    <?php endif; ?>
                 
                </section>  
              </div>

            <?php endforeach; ?>
          <?php endif; ?>
        </article>   
      <?php else: ?>
        <?php echo '<div class="tip"><span>' . $this->translate('There are no tickets to display.') . '</span></div>';
      endif; ?>
    </div>
  </div>
</div>