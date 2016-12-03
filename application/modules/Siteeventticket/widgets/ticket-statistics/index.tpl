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
	
<div>
  <ul>
    <li class="clr">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Tickets sold worth'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php  echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->event_statistics['sub_total']); ?> </div>
    </li>
    <li class="clr mtop5">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Total Commission'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php  echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->event_statistics['commission']); ?> </div>
    </li>
    <li class="clr mtop5">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Your Tax Collection'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php  echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($this->event_statistics['tax_amount']); ?> </div>
    </li>

    <br><li class="clr mtop10"><h3><?php echo $this->translate("Event Orders"); ?></h3></li>
    <li class="clr">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Total Orders'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php echo empty($this->total_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_event_dashboard (55, "manage", "order");\' id="id_55">'.$this->total_orders.' </a>' ?> </div>
    </li>
    <li class="clr mtop5">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Orders with payment approval pending'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php echo empty($this->approval_pending_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_event_dashboard (55, "manage/search/1/status/1", "order");\' id="id_55">'.$this->approval_pending_orders.' </a>' ?> </div>
    </li>
    <li class="clr mtop5">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Orders with payment pending'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php echo empty($this->payment_pending_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_event_dashboard (55, "manage/search/1/status/2", "order");\' id="id_55">'.$this->payment_pending_orders.' </a>' ?> </div>
    </li>
    <li class="clr mtop5">
      <div class="event_statistics_info fleft"> <b><?php echo $this->translate('Completed Orders'); ?> </b> </div>: &nbsp;
      <div class="fright"><?php echo empty($this->complete_orders) ? '0' : '<a class="selected" href="javascript:void(0);" onclick=\'manage_event_dashboard (55, "manage/search/1/status/3", "order");\' id="id_55">'.$this->complete_orders.' </a>' ?> </div>
    </li>
  </ul>
</div>