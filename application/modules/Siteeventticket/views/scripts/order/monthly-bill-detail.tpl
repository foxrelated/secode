<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: monthly-bill-detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>

<?php $countPagination = count($this->paginator); ?>
<?php if (empty($this->search)) : ?>
  <div class="siteeventticket_payment_to_me">
    <h3>
      <?php echo $this->translate("%1sYour Bill%2s &raquo %s %s", '<a href="javascript:void(0)" onclick="manage_event_dashboard(56, \'your-bill\', \'order\');">', '</a>', $this->monthName, $this->year) ?>
    </h3>
    <p class="mbot10 mtop5">
      <?php echo $this->translate('Below, you can view the details of your commissions bill for the month of %s %s.', $this->monthName, $this->year); ?>
    </p>

    <div id="payment_request_table">
    <?php endif; ?>
    <?php if ($countPagination): ?>
      <div><span><?php echo $this->translate('%s order(s) found.', $this->total_item) ?></span></div>
    <?php endif; ?>
    <div id="monthly_bill_detail">
      <?php if ($countPagination): ?>
        <div class="siteevent_detail_table">
          <table>
            <tr class="siteevent_detail_table_head">
              <th><?php echo $this->translate("Order Id") ?></th>
              <th><?php echo $this->translate("Order Total") ?></th>
              <th><?php echo $this->translate("Commission") ?></th>
              <th><?php echo $this->translate("Ticket Count") ?></th>
              <th><?php echo $this->translate("Payment") ?></th>
              <th><?php echo $this->translate("Order Date") ?></th>
              <th class="txt_center"><?php echo $this->translate("Options") ?></th>
            </tr>
            <?php foreach ($this->paginator as $payment) : ?>        
              <tr>
                <td>
                  <a href="javascript:void(0)" onclick="manage_event_dashboard(55, 'view/order_id/<?php echo $payment->order_id; ?>', 'order')">
                    <?php echo '#' . $payment->order_id ?>
                  </a>
                </td>
                <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($payment->grand_total) ?></td>
                <td><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($payment->commission_value) ?></td>
                <td><?php echo $payment->ticket_qty ?></td>
                <td>
                  <?php if ($payment->payment_status == 'not_paid') : ?>
                    <i class="seaocore_txt_red"><?php echo $this->translate("marked as non-payment") ?></i>
                  <?php elseif ($payment->payment_status == 'active') : ?>
                    <?php echo $this->translate("Yes") ?>
                  <?php elseif ($payment->payment_status != 'active') : ?>
                    <?php echo $this->translate("No") ?>
                  <?php endif; ?>
                </td>
                <td><?php echo gmdate('M d,Y, g:i A', strtotime($payment->creation_date)) ?></td>
                <td class="event_actlinks txt_center">
                  <a href="javascript:void(0)" onclick="manage_event_dashboard(55, 'view/order_id/<?php echo $payment->order_id; ?>', 'order')" title="<?php echo $this->translate("details") ?>" class="siteevent_icon_detail">
                    
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>  
          </table>
        </div>
      </div>

      <div>
        <div id="event_monthly_bill_detail_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
           'onclick' => '',
           'class' => 'buttonlink icon_previous'
          ));
          ?>
          <span id="bill_detail_spinner_prev"></span>
        </div>

        <div id="event_monthly_bill_detail_next" class="paginator_next">
          <span id="bill_detail_spinner_next"></span>
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
            ' . $this->translate("You have not any bill payment in this month.") . '
          </span>
        </div>';
      endif;
      ?>
    </div>
    <?php if (empty($this->search)) : ?>
    </div>
  </div>
<?php endif; ?>
<script type="text/javascript">

  en4.core.runonce.add(function () {

    var anchor = document.getElementById('monthly_bill_detail').getParent();
<?php if ($countPagination): ?>
      document.getElementById('event_monthly_bill_detail_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
      $('event_monthly_bill_detail_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

      $('event_monthly_bill_detail_previous').removeEvents('click').addEvent('click', function () {
        $('bill_detail_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';
        
        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + 'siteeventticket/order/monthly-bill-detail/event_id/' + <?php echo sprintf('%d', $this->event_id) ?>,
          data: {
            format: 'html',
            search: 1,
            month: '<?php echo $this->month ?>',
            year: '<?php echo $this->year ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
          },
          onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('bill_detail_spinner_prev').innerHTML = '';
          }
        }), {
          'element': anchor
        })
      });

      $('event_monthly_bill_detail_next').removeEvents('click').addEvent('click', function () {
        $('bill_detail_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

        en4.core.request.send(new Request.HTML({
          url: en4.core.baseUrl + 'siteeventticket/order/monthly-bill-detail/event_id/' + <?php echo sprintf('%d', $this->event_id) ?>,
          data: {
            format: 'html',
            search: 1,
            month: '<?php echo $this->month ?>',
            year: '<?php echo $this->year ?>',
            page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
          },
          onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('bill_detail_spinner_next').innerHTML = '';
          }
        }), {
          'element': anchor
        });
      });

<?php endif; ?>

  });
</script>