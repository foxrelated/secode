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
<?php
$locale = Engine_Api::_()->user()->getViewer()->locale;
    try {
      $locale = Zend_Locale::findLocale($locale);
    } catch( Exception $e ) {
      $locale = 'en_US';
    }
$locale = str_replace('_', '-',$locale);
?>


<script type="text/javascript">
    var currencyValue = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')?>';
    var localeValue = '<?php echo $locale; ?>';
    var current_occurrence_id = '<?php echo $this->defaultoccurrence_id; ?>';
    var capacityApplicable = '<?php echo $this->capacityApplicable; ?>';
</script>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/scripts/core.js');?>
<?php

$this->headTranslate(array(
    'Please select at least one ticket to proceed.'
));

// IF NO PAYMENT GATEWAY ENABLE BY THE SITEADMIN
if (empty($this->count)):
  ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("No tickets are available with this event."); ?>
    </span>
  </div> 
  <?php
  return;
endif;
?>
<?php $baseUrl = $this->layout()->staticBaseUrl; ?>
<?php $this->headLink()->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>
<?php $this->headLink()->prependStylesheet($baseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); ?>
<script type="text/javascript">
  var params = {
    requestParams:<?php echo json_encode($this->params) ?>,
    responseContainer: $$('.layout_siteeventticket_tickets_buy')
  }
</script>
<!--START OCCURRENCES-->
<a id="siteevent_profile_tickets_anchor"></a>

<?php
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
$count = 0;
$tempCartInfoForCoupon = array();
$event_id = $this->event_id;
?>
<?php  
//REDIRECTION TO CHECKOUT PAGE / BUYER DETAIL PAGE
if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.detail.step', '1')){
  $redirectionUrl = $this->url(array('action' => 'buyer-details', 'event_id' => $this->event_id), "siteeventticket_order", true);
}else{
  $redirectionUrl = $this->url(array('action' => 'checkout', 'event_id' => $this->event_id), "siteeventticket_order", true); 
}
?>
<form method="post" action="<?php echo $redirectionUrl; ?>" id ="buy_tickets_form">
  <?php //SHOW EVENT OCCURRENCE DATE DROP-DOWN FOR FILTERING GUESTS  ?>

  <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>
    <div>
      <span class="siteevent_members_search_label"><?php echo $this->translate('Available Dates'); ?></span>
      <select onchange="ticketChangeOccurrence(this.value);" id='date_filter_occurrence' name="date_filter_occurrence">
        <?php
        $noAllOccurrencesField = 1;
        $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo, $noAllOccurrencesField);
        foreach ($filter_dates as $key => $date):
          ?> 
          <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
        <?php endforeach;
        ?>
      </select>
    </div>
    <br/>
  <?php endif; ?>
  <!--END OCCURRENCES-->
  <div class="siteevent_profile_loading_image" style="display: none;"></div>
  <!--TABLE STRUCTURE-->
  <div class="siteevent_detail_table" id="seticket_detail_table">
    <table class="mbot10">
      <tr class="siteevent_detail_table_head">
        <th><?php echo $this->translate("Ticket Name") ?></th>
        <th id="claimed_column"><?php echo $this->translate("Claimed Tickets") ?></th>
        <th><?php echo $this->translate("Ticket sale will be closed on") ?></th>
        <th><?php echo $this->translate("Price") ?></th>                
        <th><?php echo $this->translate("Qty") ?></th>
        <!--<th></th>-->
      </tr>

      <?php
      $claimed_column_display = false; 
      $ticket_for_sale = 0;
      foreach ($this->paginator as $item):
          $item->description = $item->description ? $item->description : $item->title;
        ?>
        <tr  id="ticket_rows_<?php echo $item->ticket_id ?>" class="ticket_row" title="<?php echo $this->translate($item->description); ?>"> 
          <td class="<?php if ($item->status == 'hidden'): ?>seaocore_txt_light<?php endif; ?>">
            <?php echo $this->translate(ucfirst($item->title)) ?>
          </td>
          <td class="claimed_column_td <?php if ($item->status == 'hidden'): ?>seaocore_txt_light<?php endif; ?>">
            <!--TOTAL SOLD TICKETS CALCULATED-->
            <?php
            $ticket_sold = 0;
            if (!empty($item->ticket_id_sold)):
              $ticketDetailArray = Zend_Json_Decoder::decode($item->ticket_id_sold);
              $ticket_sold = $ticketDetailArray['tid_' . $item->ticket_id];
            endif;
            ?>
            <?php if ($item->is_claimed_display): $claimed_column_display = true;?>
              <?php if($item->price > 0): ?>
                <?php echo $this->translate("%s of %s sold", $ticket_sold, $item->quantity);?>
              <?php else: ?>
                <?php echo $this->translate("%s of %s claimed", $ticket_sold, $item->quantity);?>
              <?php endif; ?>
            <?php else: ?>
              <?php echo $this->translate("-"); ?>
            <?php endif; ?>
          </td>
          <td class="<?php if ($item->status == 'hidden'): ?>seaocore_txt_light<?php endif; ?>">
            <?php if ($item->is_same_end_date): ?>
              <?php $sellClosedOn = $item->starttime; ?>
            <?php elseif ($item->sell_endtime > $item->endtime): ?>
              <?php $sellClosedOn = $item->endtime; ?>
            <?php else: ?>
              <?php $sellClosedOn = $item->sell_endtime; ?>
            <?php endif; ?>
            <?php echo $this->locale()->toDateTime($sellClosedOn);?>
          </td>
          <td class="<?php if ($item->status == 'hidden'): ?>seaocore_txt_light<?php endif; ?>">
            <input type="hidden" id = "price_column_<?php echo $item->ticket_id ?>" class ="price_column" value="<?php echo $item->price ?>" name="ticket_column[<?php echo $item->ticket_id ?>][]" />
            <?php
            if ($item->price > 0):echo $this->locale()->toCurrency($item->price, $currency);
            else: echo $this->translate('Free');
            endif;
            ?>
          </td>
          <!--DROPDOWN FOR QUANTITY SELECT-->          
          <td class="<?php if ($item->status == 'hidden'): ?>seaocore_txt_light<?php endif; ?>">
            <?php if ($item->status == 'open'): ?>
              <!--TICKET SELLING NOT STARTED-->
              <?php if ($sellClosedOn < date('Y-m-d H:i:s')): ?>
                <b class="seaocore_txt_red"><?php echo $this->translate("Sale ended"); ?></b>
              <?php elseif ($item->sell_starttime > date('Y-m-d H:i:s')): ?>
                <?php echo $this->translate("Sale starts on"); ?>
                <?php echo $this->locale()->toDateTime($item->sell_starttime); ?>
              <?php else: ?>
                <!--IF TICKETS NOT AVAILABLE-->
                <?php
                if ($ticket_sold >= $item->quantity):
                  echo '<b class="seaocore_txt_red">' . $this->translate("Sold Out") . '</b>';
                else: 
                  $ticket_for_sale = 1;
                  ?>
                  <?php
                  $min = $item->buy_limit_min;
                  $max = $item->buy_limit_max;
                  $remainingsTickets = $item->quantity - $ticket_sold;
                  if ($min > $remainingsTickets):
                    $min = $remainingsTickets;
                  endif;
                  if ($max > $remainingsTickets):
                    $max = $remainingsTickets;
                  endif;
                  if ($min == 0):
                    $min = 1;
                  endif;
                  ?>
                  <select style="min-width: 55px;" id='ticket_quantity_<?php echo $item->ticket_id ?>' onchange = "calculateGrandTotal('<?php echo $this->tax_rate; ?>',currencyValue, localeValue,1);" name="ticket_column[<?php echo $item->ticket_id ?>][]" class="ticket_dropdown" data-price="<?php echo $item->price ?>" data-ticket_id = '<?php echo $item->ticket_id ?>'>
                    <option selected='selected' value="0"><?php echo "0"; ?></option>         
                  <?php for ($i = $min; $i <= $max; $i++) { ?> 
                      <option  value="<?php echo $i; ?>"><?php echo $i; ?></option>             
                  <?php } ?>
                  </select>
                  <input name="ticket_column[<?php echo $item->ticket_id ?>][coupon_id]" class="ticket_details" id='ticket_detail_<?php echo $item->ticket_id ?>' type="hidden" value="" data-couponId="" data-couponDiscounType="" data-couponDiscountAmount="">
                  
                  <?php 
                  $tempCartInfoForCoupon[$event_id]['ticket_ids'][$item->ticket_id] = $item->ticket_id; 
                  $tempCartInfoForCoupon[$event_id]['unitPrice'][$item->ticket_id] = $item->price;
                  ?>                  
                  
                <?php endif; ?>
              <?php endif; ?>              
  <?php else: ?>
    <?php echo $this->translate($item->status); ?>
    <?php $count++; ?>
  <?php endif;?>
                  
          </td>
        </tr>
          <?php endforeach; ?>
    </table>
    
    <div class="ticket_price_details">
      <?php if ($this->showCouponSection) : ?>
          <div class="bold">
            <!-- Apply Coupon Code -->
              <div class="fleft">
                <div class="mbot5">
                  <label><?php echo $this->translate("Enter Coupon Code"); ?></label>
                </div>
                <div class="mbot5">
                    <input type = 'text' id='coupon_code_value' name='coupon_code' value = "" autocomplete="off" />
                    <a href="javascript:void(0);" onclick="applyCouponcode(<?php echo $event_id ?>);" id="update_shopping_cart" class="mleft5 f_small">
                    <?php echo $this->translate("Apply Coupon") ?>
                    </a>
                    <div id="apply_coupon_spinner_<?php echo $event_id ?>" style="display: inline-block;"></div>
                </div>
                <span id='coupon_error_msg' class="seaocore_txt_red f_small"></span>
                <span id='coupon_success_msg' class="f_small" style="color:#006804;"></span>
                
              </div>
          </div>
      <?php endif; ?>
      <div class="invoice_ttlamt_box_wrap fright mbot10">
      	<div class="invoice_ttlamt_box fleft">
          <div class="clr">
            <div class="invoice_order_info fleft"><?php echo $this->translate("Sub Total "); ?></div>
            <div class="fright" id="dynamic_subtotal"></div>      
          	<input id="subtotal" type="hidden" value="" name="subtotal">
          </div>
          <?php if($this->showCouponSection): ?>  
            <div class="clr" id="discount_row">
              <div class="invoice_order_info fleft"><?php echo $this->translate("Discount Total "); ?></div>
              <div class="fright" id="dynamic_discounttotal"></div>      
              <input id="discounttotal" type="hidden" value="" name="discounttotal">
            </div>
          <?php endif; ?>              
          <?php if ($this->tax_rate): ?>
            <div class="clr" id="tax_row">
              <div class="invoice_order_info fleft"><?php echo $this->translate('Tax (%s%%)', $this->tax_rate); ?></div>
              <div class="fright" id="dynamic_tax"></div>      
            	<input id="tax" type="hidden" value="" name="tax">
            </div>
          <?php endif; ?>
          <div class="clr invoice_grand_ttlamt">
            <div class="fleft"><strong><?php echo $this->translate("Grand Total "); ?></strong></div>
            <div class="fright" id="dynamic_grandtotal"></div>      
            <input id="grandtotal" type="hidden" value="" name="grandtotal">
          </div>  
        </div>  
      </div>
		</div>
    
    

<?php if ($count < $this->count): ?>
      
    <!--ORDER NOW BUTTON-->
    <div id ="orderNowButton" class="ticket_price_details clr" <?php if(!empty($this->capacityApplicable)) { echo "style='display:none;'";} ?>>
      <!--WARNING MESSAGE IF ALL DROPDOWNS SETS 0 VALUE-->
      <div> <span id="no_ticket_selected" class="seaocore_txt_red f_small"></span> </div>
      <button type="button" onclick="validateTicketInformation()" class="fright mleft10">
      <?php echo $this->translate("Book Now"); ?>
      </button>
    </div>
    <!--ORDER NOW BUTTON-->   

<?php endif; ?>
    
    <?php if(!empty($this->capacityApplicable)): ?>
        <div id="eventIsFull" class="ticket_price_details clr fright" style="display:none;">
            <div class="siteevent_event_status_box event_full " style="background-color:#fe0000;"><b><?php echo $this->translate("Event is Full"); ?></b></div>
            <div class="txt_center"><a href="<?php echo $this->url(array('controller' => 'waitlist', 'action' => 'join', 'event_id' => $this->siteevent->event_id, 'occurrence_id' => $this->defaultoccurrence_id), 'siteevent_extended'); ?>" class="smoothbox f_small"><span><?php echo $this->translate("Add me to waitlist"); ?></span></a></div>
        </div>
    <?php endif; ?>
        
  </div>
</form>

<script type="text/javascript">

  var tax_rate = "<?php echo $this->tax_rate; ?>";
  
  en4.core.runonce.add(function () {
    calculateGrandTotal(tax_rate, currencyValue, localeValue, 1);
    
<?php if ($claimed_column_display == false): ?>
      $('claimed_column').setStyle('display', 'none');
      $$('.claimed_column_td').each(function (elem) {
        $(elem).setStyle('display', 'none');
      });
<?php endif; ?>
<?php if ($ticket_for_sale == 0):  ?> 
      $$('.ticket_price_details').each(function (elem) {
        $(elem).setStyle('display', 'none');
      });
<?php endif; ?>
  });
var ticket_for_sale = '<?php echo $ticket_for_sale; ?>';

  function ticketChangeOccurrence(occurrenceid) { 
    var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
    $$('.siteevent_profile_loading_image').setStyle('display', 'block');
    $$('.siteevent_detail_table').setStyle('display', 'none');
    en4.core.request.send(new Request.HTML({
      'url': url,
      'data': $merge({
        'format': 'html',
        'subject': en4.core.subject.guid,
        'event_id': '<?php echo $this->event_id ?>',
        'loaded_by_ajax': 1,
        'defaultoccurrence_id': '<?php echo $this->defaultoccurrence_id; ?>',
      }, params.requestParams, {occurrence_id: occurrenceid}),
      onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
        $('siteevent_profile_tickets_anchor').getParent().innerHTML = responseHTML;
        en4.core.runonce.trigger();
        current_occurrence_id = occurrenceid;
        if(capacityApplicable != 0 && ticket_for_sale != 0) {
            checkTicketAvailability(occurrenceid);
        }
      }

    }));
  }

  function applyCouponcode(event_id)
  {
    if ($("coupon_code_value").value == '') {
      $('coupon_error_msg').innerHTML = '<?php echo $this->translate("Please enter a coupon code."); ?>';
      return;
    }
    
    var totalQuantityArray = [];
    totalQuantityArray['quantity'] = [];
    $$('.ticket_dropdown').each(function (elem) {
        if($(elem).value != 'undefined') {
            totalQuantityArray['quantity'][elem.get('data-ticket_id')] = $(elem).value;
        }
    });    

    en4.core.request.send(new Request.JSON({
      url: "<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'coupon', 'action' => 'apply-coupon'), 'default', true); ?>",
      method: 'post',
      data: {
        format: 'json',
        coupon_code: document.getElementById("coupon_code_value").value,
        cart_info: '<?php echo json_encode($tempCartInfoForCoupon); ?>',
        event_id: event_id,
        totalQuantityArray: totalQuantityArray['quantity'],
        dynamic_subtotal: $('subtotal').value
      },
      onRequest: function () {
        document.getElementById('coupon_error_msg').innerHTML = '';
        document.getElementById('coupon_success_msg').innerHTML = '';
        document.getElementById("apply_coupon_spinner_" + event_id).innerHTML = '<img style="height:12px;" src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Siteeventticket/externals/images/loading.gif" />';
      },
      onSuccess: function (responseJSON) {

        document.getElementById("apply_coupon_spinner_" + event_id).innerHTML = '';
        if (responseJSON.coupon_error_msg) {
          document.getElementById('coupon_error_msg').innerHTML = responseJSON.coupon_error_msg;

          $$('.ticket_dropdown').each(function (elem) {
            $('ticket_detail_' + elem.get('data-ticket_id')).set('value', '');
            $('ticket_detail_' + elem.get('data-ticket_id')).set('data-couponId', '');
            $('ticket_detail_' + elem.get('data-ticket_id')).set('data-couponDiscountType', '');
            $('ticket_detail_' + elem.get('data-ticket_id')).set('data-couponDiscountAmount', '');
          });

        } else if (responseJSON.cart_coupon_applied) {
            
          var ticketsIds = responseJSON.ticketIds;
          document.getElementById('coupon_success_msg').innerHTML = responseJSON.coupon_success_msg;
          Object.each(ticketsIds, function (ticketId, index) {
            $('ticket_detail_' + index).set('value', ticketId.coupon_id);
            $('ticket_detail_' + index).set('data-couponId', ticketId.coupon_id);
            $('ticket_detail_' + index).set('data-couponDiscountType', ticketId.coupon_type);
            $('ticket_detail_' + index).set('data-couponDiscountAmount', ticketId.coupon_amount);
          });
        }
        calculateGrandTotal(tax_rate, currencyValue, localeValue, 0);
      }
    }));
  }
  
  window.addEvent('domready', function() {
    if(capacityApplicable != 0 && ticket_for_sale != 0) {
        checkTicketAvailability(current_occurrence_id);
    }
  });
</script>