<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>

<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
      <div id="siteevent_manage_order_content"> 
      <?php endif; ?> 
      <?php
      $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/scripts/core.js');
      $orderTicketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
      $paginationCount = @count($this->paginator);
      ?>
      <?php if (empty($this->call_same_action)) : ?>
        <div class="siteeventticket_manage_event">

          <h3 class="mbot10"><?php echo $this->translate('Manage Orders') ?></h3>
          <p class="mbot10"><?php echo $this->translate("Here, you can manage all the ticket orders placed for your event, using the search form below. This search form will help you in finding specific ticket order entries, and leaving it empty will show all the ticket orders placed for your event. You can also view order details and print invoices."); ?></p>
          <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
            <form method="post" class="field_search_criteria" id="filter_form">
              <div>
                <ul>
                  <li>
                    <span><label> <?php echo $this->translate("Order Id (#)") ?></label></span>
                    <input type="text" name="order_id" id="order_id" /> 
                  </li>
                  <li>
                    <span><label><?php echo $this->translate("Buyer Name") ?></label></span>
                    <input type="text" name="username" id="username"/> 
                  </li>      
                  <li id="integer-wrapper">
                    <span><label><?php echo $this->translate("Order Date : ex (2000-12-25)") ?></label></span>
                    <div class="form-element"> 
                      <input type="text" name="creation_date_start" id="creation_date_start" placeholder="<?php echo $this->translate("from"); ?>"/> 
                    </div>
                    <div class="form-element"> 
                      <input type="text" name="creation_date_end" id="creation_date_end" placeholder="<?php echo $this->translate("to"); ?>"/> 
                    </div>
                  </li>
                  <li id="integer-wrapper">
                    <label><?php echo $this->translate("Order Total") ?></label>
                    <div class="form-element">
                      <input type="text" name="order_min_amount" id="order_min_amount" placeholder="min"/>
                    </div>
                    <div class="form-element">
                      <input type="text" name="order_max_amount" id="order_max_amount" placeholder="max"/> 	      
                    </div>
                  </li>
                  <li id="integer-wrapper">
                    <label><?php echo $this->translate("Commission") ?></label>
                    <div class="form-element">
                      <input type="text" name="commission_min_amount" id="commission_min_amount" placeholder="min"/>
                    </div>
                    <div class="form-element">
                      <input type="text" name="commission_max_amount" id="commission_max_amount" placeholder="max"/> 	      
                    </div>
                  </li>
                  <li>
                    <span><label><?php echo $this->translate("Status") ?>	</label></span>
                    <select id="order_status" name="order_status" >
                      <option value="0" ></option>
                      <?php
                      for ($index = 0; $index < 3; $index++):
                        echo '<option value="' . ($index + 1) . '">' . $this->translate($this->getTicketOrderStatus($index)) . '</option>';
                      endfor;
                      ?>
                    </select>
                  </li>
                  <li class="clear mtop10">
                    <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>        
                  </li>
                  <li>
                    <span id="search_spinner"></span>
                  </li>
                </ul>
              </div>
            </form>
          </div>


          <div id="manage_order_pagination">  <?php endif; ?>
          <?php if ($paginationCount): ?>
            <div class="mbot5">
              <?php echo $this->translate('%s order(s) found.', $this->total_item) ?>
            </div>
          <?php endif; ?>
          <div id="manage_order_tab">
            <?php if ($paginationCount): ?>
              <div class="siteevent_detail_table">
                <table>
                  <tr class="siteevent_detail_table_head">
                    <th class="txt_center"><?php echo $this->translate('Order Id') ?></th>
                    <th><?php echo $this->translate('Buyer') ?></th>
                    <th><?php echo $this->translate('Order Date') ?></th>
                    <th class="txt_center"><?php echo $this->translate('Qty') ?></th>
                    <th class="txt_right"><?php echo $this->translate('Order Total') ?></th>
                    <th class="txt_right"><?php echo $this->translate('Commision') ?></th>
                    <th><?php echo $this->translate('Status') ?></th>
                    <th class="txt_center"><?php echo $this->translate('Payment') ?></th>
                    <th><?php echo $this->translate('Options') ?></th>
                  </tr>	
                  <?php foreach ($this->paginator as $item): 
                    $showPrintLinks = $item->showPrintLink();?>
                    <?php
                    if ($item->order_status == 3) :
                      $payment_status = $this->translate('marked as non-payment');
                    elseif ($item->payment_status == 'active') :
                      $payment_status = 'Yes';
                    else:
                      $payment_status = 'No';
                    endif;
                    ?>
                    <tr>
                      <td><a href="javascript:void(0)" onclick="manage_event_dashboard(55, 'view/order_id/<?php echo $item->order_id; ?>', 'order')"><?php echo "#" . $item->order_id; ?></a></td>
                      <td>
                         <?php if(Engine_Api::_()->getItem('user', $item->user_id)->getIdentity()): ?> 
                            <?php echo $this->htmlLink($item->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getOwner()->getTitle(), 10), array('title' => $item->getOwner()->getTitle(), 'target' => '_blank')); ?>
                         <?php else: ?> 
                          --
                         <?php endif; ?> 
                      </td>             
                      <td><?php echo $this->locale()->toDateTime($item->creation_date); ?></td>
                      <td class="txt_center"><?php echo $this->locale()->toNumber($item->ticket_qty); ?></td>
                      <td class="txt_right"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->grand_total); ?></td>
                      <td class="txt_right"><?php echo Engine_Api::_()->siteeventticket()->getPriceWithCurrency($item->commission_value); ?></td>
                      <td>
                        <?php $tempStatus = $this->getTicketOrderStatus($item->order_status, true); ?>
                        <?php if (!empty($this->canEdit)) : ?>
                          <?php if ($item->order_status == 3) : ?>
                            <i>-</i>
                          <?php else: ?>
                            <div style="min-width:100px">
                              <div class="<?php echo $tempStatus['class'] ?> fleft" id="current_order_status_<?php echo $item->order_id ?>">
                                <i><?php echo $tempStatus['title']; ?></i>
                              </div>
                            </div>
                          <?php endif; ?>
                        <?php else: ?>
                          <?php if ($item->order_status == 3) : ?>
                            <i>-</i>
                          <?php else: ?>
                            <div class="<?php echo $tempStatus['class'] ?> fleft">
                              <i><?php echo $tempStatus['title']; ?></i>
                            </div>
                          <?php endif; ?>
                        <?php endif; ?>
                      </td>
                      <td class="txt_center"><?php echo $this->translate($payment_status); ?></td>             
                      <td>
                        <?php if (empty($this->isPaymentToSiteEnable) && !empty($this->canEdit)) : ?>
                          <?php if ($item->non_payment_admin_reason != 1) : ?>
                            <?php if (!empty($item->direct_payment) && (($item->gateway_id == 3 && empty($item->order_status)) || (($item->gateway_id == 4 || $item->gateway_id == 2) && $item->order_status == 1) )) : ?>
                              <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'payment-approve', 'order_id' => $item->order_id, 'gateway_id' => $item->gateway_id), 'default', true) ?>');">
                                <?php echo $this->translate("approve payment") ?>
                              </a> |
                            <?php elseif (!empty($item->direct_payment) && !empty($item->direct_payment) && ( $item->gateway_id == 4 || ( ($item->gateway_id == 2 || $item->gateway_id == 3) && $item->payment_status == 'active') )): ?>
          <!--                              <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'approve-remaining-amount-payment', 'order_id' => $item->order_id, 'gateway_id' => $item->gateway_id), 'default', true) ?>');">
                              <?php echo $this->translate("approve remaining amount payment") ?>
                              </a> |-->
                            <?php endif; ?>
                            <?php if (!empty($item->direct_payment) && ($item->order_status < 2)) : ?>
                              <?php if (empty($item->non_payment_seller_reason)) : ?>
                                <a href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'non-payment-order', 'order_id' => $item->order_id), 'default', true) ?>');"><?php echo $this->translate("mark as non-payment") ?></a> |
                              <?php endif; ?>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php endif; ?>

                        <a style="display:none;" href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'order', 'action' => 'detail', 'order_id' => $item->order_id), 'default', true) ?>')"><?php echo $this->translate("details") ?></a> 
                        <a href="javascript:void(0)" onclick="manage_event_dashboard(55, 'view/order_id/<?php echo $item->order_id; ?>', 'order')"><?php echo $this->translate("view") ?></a> 
                        <?php if ($showPrintLinks): ?>          
                          | <?php $tempInvoice = $this->url(array('action' => 'print-invoice', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($item->order_id)), 'siteeventticket_order', true);?><a href="<?php echo $tempInvoice; ?>" target="_blank"><?php echo $this->translate("print invoice") ?></a>
                          | <?php $tempPrint = $this->url(array('action' => 'print-ticket', 'order_id' => Engine_Api::_()->siteeventticket()->getDecodeToEncode($item->order_id)), 'siteeventticket_order', true); ?><a href="<?php echo $tempPrint; ?>" target="_blank"><?php echo $this->translate("print tickets"); ?></a>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </table>
              </div>
            </div>
            <div class="clr dblock siteeventticket_data_paging">
              <div id="event_manage_order_previous" class="paginator_previous siteeventticket_data_paging_link">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                 'onclick' => '',
                 'class' => 'buttonlink icon_previous'
                ));
                ?>
                <span id="manage_spinner_prev"></span>
              </div>

              <div id="event_manage_order_next" class="paginator_next siteeventticket_data_paging_link">
                <span id="manage_spinner_next"></span>
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                 'onclick' => '',
                 'class' => 'buttonlink_right icon_next'
                ));
                ?>
              </div>

            <?php else: ?>
              <div class="tip"><span>
                  <?php echo $this->translate('There are no orders placed in this event yet.') ?>
                </span></div>
            <?php endif; ?>
          </div>
          <?php if (empty($this->call_same_action)) : ?>
          </div>
        </div>
      <?php endif; ?>

      <script type="text/javascript">
        en4.core.runonce.add(function () {

<?php if (!empty($this->newOrderStatus)) : ?>
            document.getElementById('order_status').selectedIndex = <?php echo $this->newOrderStatus ?>;
<?php endif; ?>
          var anchor = document.getElementById('manage_order_tab').getParent();
<?php if ($paginationCount): ?>
            document.getElementById('event_manage_order_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('event_manage_order_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
            $('event_manage_order_previous').removeEvents('click').addEvent('click', function () {
              $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'siteeventticket/order/manage/event_id/' + <?php echo sprintf('%d', $this->event_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  order_id: $('order_id').value,
                  username: $('username').value,
                  creation_date_start: $('creation_date_start').value,
                  creation_date_end: $('creation_date_end').value,
                  order_min_amount: $('order_min_amount').value,
                  order_max_amount: $('order_max_amount').value,
                  commission_min_amount: $('commission_min_amount').value,
                  commission_max_amount: $('commission_max_amount').value,
                  order_status: $('order_status').value,
                  event_id: <?php echo sprintf('%d', $this->event_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('manage_spinner_prev').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

            $('event_manage_order_next').removeEvents('click').addEvent('click', function () {
              $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'siteeventticket/order/manage/event_id/' + <?php echo sprintf('%d', $this->event_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  order_id: $('order_id').value,
                  username: $('username').value,
                  creation_date_start: $('creation_date_start').value,
                  creation_date_end: $('creation_date_end').value,
                  order_min_amount: $('order_min_amount').value,
                  order_max_amount: $('order_max_amount').value,
                  commission_min_amount: $('commission_min_amount').value,
                  commission_max_amount: $('commission_max_amount').value,
                  order_status: $('order_status').value,
                  event_id: <?php echo sprintf('%d', $this->event_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('manage_spinner_next').innerHTML = '';

                }
              }), {
                'element': anchor
              })
            });
<?php endif; ?>

          $('filter_form').removeEvents('submit').addEvent('submit', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

            en4.core.request.send(new Request.HTML({
              url: en4.core.baseUrl + 'siteeventticket/order/manage',
              method: 'POST',
              data: {
                search: 1,
                subject: en4.core.subject.guid,
                call_same_action: 1,
                order_id: $('order_id').value,
                username: $('username').value,
                creation_date_start: $('creation_date_start').value,
                creation_date_end: $('creation_date_end').value,
                order_min_amount: $('order_min_amount').value,
                order_max_amount: $('order_max_amount').value,
                commission_min_amount: $('commission_min_amount').value,
                commission_max_amount: $('commission_max_amount').value,
                order_status: $('order_status').value,
                event_id: <?php echo sprintf('%d', $this->event_id) ?>
              },
              onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('search_spinner').innerHTML = '';
              }
            }), {
              'element': anchor
            })
          });
        });
      </script>
      <?php if (!$this->only_list_content): ?>
      </div>
    </div>	
  </div>	
<?php endif; ?>
</div>