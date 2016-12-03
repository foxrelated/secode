<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _siteAdminRelatedTransaction.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
   
<?php $countPagination = count($this->paginator); ?>
<?php if(empty($this->call_same_action)) : ?>
<div class="clr">
  
    <h4><?php echo $this->translate('Payments to Site Administrator') ?></h4>
    <p class="mbot10"><?php echo $this->translate("Browse through the transactions made by you to the site administrator for paying commission in response to the sales made from this website. The search box below will search through the transaction date, bill amount and payment status."); ?></p>
    <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
      <form method="post" class="field_search_criteria" id="filter_form">
        <div>
          <ul>
            <li id="integer-wrapper">
              <?php 
                //MAKE THE STARTTIME AND ENDTIME FILTER
                $starttime = $this->locale()->toDateTime(time());
                $attributes = array();
                $attributes['dateFormat'] = $this->locale()->useDateLocaleFormat(); //'ymd';
                $attributes['is_ajax'] = 1;

                $form = new Engine_Form_Element_CalendarDateTime('starttime');
                $attributes['options'] = $form->getMultiOptions();
                $attributes['id'] = 'starttime';

                if( !empty($this->starttime) ) :
                  $attributes['starttimeDate'] = $this->starttime;
                endif;

                echo '<span>'.$this->translate('Duration').'</span>';
                echo '<div class="form-element">';
                echo $this->formCalendarDateTimeElement('starttime', $starttime, array_merge(array('label' => 'From'), $attributes), $attributes['options'] );
                if( !empty($this->endtime) ) :
                  $attributes['endtimeDate'] = $this->endtime;
                endif;
                $attributes['starttimeDate'] = '';
                echo $this->formCalendarDateTimeElement('endtime', $starttime, array_merge(array('label' => 'To'), $attributes), $attributes['options'] );
                echo '</div>';
              ?>
            </li>  
            <li id="integer-wrapper">
              <label>
                <?php echo $this->translate("Bill Amount") ?>
              </label>
              <div class="form-element">
                <input type="text" name="bill_min_amount" id="bill_min_amount" placeholder="min"/>	      
              </div>
              <div class="form-element">
                <input type="text" name="bill_max_amount" id="bill_max_amount" placeholder="max"/> 	      
              </div>
            </li>
            <li>
              <span>
                <label>
                  <?php echo $this->translate("Payment Status") ?>	
                </label>
              </span>
              <select id="payment" name="payment" >
                <option value="0" ></option>
                <option value="1" ><?php echo $this->translate("Yes") ?></option>
                <option value="2" ><?php echo $this->translate("No") ?></option>
              </select>
            </li>
            <li class="clear mtop10">
              <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button> 
              <span id="search_spinner" style="display:inline-block; vertical-align: middle;"></span>
            </li>
          </ul>
        </div>
      </form>
    </div>
    
  <div id="payment_request_table">
    <?php endif; ?>
   
    <?php if ($countPagination): ?>
      <div><span><?php echo $this->translate('%s bill payment(s) found.', $this->total_item) ?></span></div>
    <?php endif; ?>
    <div id="manage_order_tab">

      <?php if ($countPagination): ?> 
        <div class="product_detail_table sitestoreproduct_data_table fleft">
          <table>
            <tr class="product_detail_table_head">
              <th><?php echo $this->translate("Bill Id") ?></th>
              <th><?php echo $this->translate("Amount") ?></th>
              <th><?php echo $this->translate("Message") ?></th>
              <th><?php echo $this->translate("Remaining Amount") ?></th>
              <th><?php echo $this->translate("Date") ?></th>
              <th><?php echo $this->translate("Payment") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php
            foreach ($this->paginator as $payment) :
              if ($payment->status != 'active'):
                $payment_status = $this->translate('No');
              else:
                $payment_status = $this->translate('Yes');
              endif;
              ?>        
              <tr>
                <td><?php echo $payment->storebill_id ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->amount) ?></td>
                <td><?php echo $this->string()->truncate($this->string()->stripTags(empty($payment->message) ? '-' : $payment->message), 30) ?></td>
                <td><?php echo Engine_Api::_()->sitestoreproduct()->getPriceWithCurrency($payment->remaining_amount) ?></td>
                <td><?php echo gmdate('M d,Y, g:i A',strtotime($payment->creation_date)) ?></td>
                <td class="txt_center"><?php echo $this->translate("%s", $payment_status) ?></td>
                <td class="txt_center">
                  <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'bill-details', 'bill_id' => $payment->storebill_id, 'store_id' => $this->store_id)) ?>')"><?php echo $this->translate("details") ?></a>
                  <?php if( $payment_status == 'No' ) : ?>
<!--                    | <a href="javascript:void(0)" onclick="Smoothbox.open('<?php //echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'edit-store-bill', 'bill_id' => $payment->storebill_id, 'store_id' => $this->store_id)) ?>')"><?php //echo $this->translate("edit") ?></a>-->
                    | <a href="javascript:void(0)" onclick="makeBillPayment('<?php echo $this->url(array('module' => 'sitestoreproduct', 'controller' => 'product', 'action' => 'bill-process', 'bill_id' => $payment->storebill_id, 'store_id' => $this->store_id)) ?>')"><?php echo $this->translate("make payment") ?></a>
                    
                  <?php endif; ?>
                </td>   
              </tr>
            <?php endforeach; ?>  
          </table>
        </div>
      </div>

      <div>
        <div id="store_payment_request_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="payment_spinner_prev"></span>
        </div>

        <div id="store_payment_request_next" class="paginator_next">
          <span id="payment_spinner_next"></span>
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>

        <?php
      else:
        echo '<div class="tip">
          <span>
            ' . $this->translate("You have not paid any commission yet.") . '
          </span>
        </div>';
      endif;
      ?>
    </div>
    <?php if (empty($this->call_same_action)) : ?>
    </div>
  </div>
<?php endif; ?>

<script type="text/javascript">

function makeBillPayment(url)
{
  window.location = url;
}
        en4.core.runonce.add(function() {

          var anchor = document.getElementById('manage_order_tab').getParent();
<?php if ($countPagination): ?>
            document.getElementById('store_payment_request_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('store_payment_request_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('store_payment_request_previous').removeEvents('click').addEvent('click', function() {
              $('payment_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

              var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 54, 'method' => 'store-transaction', 'tab' => $this->tab, 'page' => $this->paginator->getCurrentPageNumber() - 1), 'sitestore_store_dashboard', true); ?>';

              if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                history.pushState({}, document.title, tempPaymentPaginationUrl);
              }

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/store-transaction/store_id/' + <?php echo sprintf('%d', $this->store_id) ?> + '/tab/' + <?php echo $this->tab ?>,
                data: {
                  format: 'html',
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  bill_min_amount: $('bill_min_amount').value,
                  bill_max_amount: $('bill_max_amount').value,
                  payment: $('payment').value,
                  starttime : $('calendar_output_span_starttime-date').innerHTML,
                  endtime : $('calendar_output_span_endtime-date').innerHTML,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('payment_spinner_prev').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

            $('store_payment_request_next').removeEvents('click').addEvent('click', function() {
              $('payment_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

              var tempPaymentPaginationUrl = '<?php echo $this->url(array('action' => 'store', 'store_id' => $this->store_id, 'type' => 'product', 'menuId' => 54, 'method' => 'store-transaction', 'tab' => $this->tab, 'page' => $this->paginator->getCurrentPageNumber() + 1), 'sitestore_store_dashboard', true); ?>';

              if (tempPaymentPaginationUrl && typeof history.pushState != 'undefined') {
                history.pushState({}, document.title, tempPaymentPaginationUrl);
              }

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'sitestoreproduct/product/store-transaction/store_id/' + <?php echo sprintf('%d', $this->store_id) ?> + '/tab/' + <?php echo $this->tab ?>,
                data: {
                  format: 'html',
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  bill_min_amount: $('bill_min_amount').value,
                  bill_max_amount: $('bill_max_amount').value,
                  payment: $('payment').value,
                  starttime : $('calendar_output_span_starttime-date').innerHTML,
                  endtime : $('calendar_output_span_endtime-date').innerHTML,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('payment_spinner_next').innerHTML = '';
                  
                }
              }), {
                'element': anchor
              })
            });

<?php endif; ?>

          $('filter_form').removeEvents('submit').addEvent('submit', function(e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitestoreproduct/externals/images/loading.gif" />';

            en4.core.request.send(new Request.HTML({
              url: en4.core.baseUrl + 'sitestoreproduct/product/store-transaction/store_id/' + <?php echo sprintf('%d', $this->store_id) ?> + '/tab/' + <?php echo $this->tab ?>,
              method: 'POST',
              data: {
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  bill_min_amount: $('bill_min_amount').value,
                  bill_max_amount: $('bill_max_amount').value,
                  payment: $('payment').value,
                  starttime : $('calendar_output_span_starttime-date').innerHTML,
                  endtime : $('calendar_output_span_endtime-date').innerHTML
              },
              onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('search_spinner').innerHTML = '';
              }
            }), {
              'element': anchor
            })
          });
        });
</script>