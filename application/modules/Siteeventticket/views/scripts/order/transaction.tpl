<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: transaction.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>

<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content" id="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
      <div> 
      <?php endif; ?>  
      <?php $paginationCount = count($this->paginator); ?>
      <?php if (empty($this->call_same_action)) : ?>
        <div class="siteeventticket_manage_event siteeventticket_Transactions">
          <h3 class="mbot10">
            <?php echo $this->translate('Transactions') ?>
          </h3>
          <p class="mbot10">
            <?php echo $this->translate("Browse through the transactions made by our site administrators to make payments in response to your payment requests. The search box below will search through the transaction date, response amount and state. You can also use the filters below to filter the transactions."); ?>
          </p>
          <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
            <form method="post" class="field_search_criteria" id="filter_form">
              <div>
                <ul>
                  <li>
                    <span>
                      <label>
                        <?php echo $this->translate("Transaction Date : ex (2000-12-25)") ?>
                      </label>
                    </span>
                    <input type="text" name="date" id="date" />
                  </li>  

                  <li id="integer-wrapper">
                    <label>
                      <?php echo $this->translate("Response Amount") ?>
                    </label>
                    <div class="form-element">
                      <input type="text" name="response_min_amount" id="response_min_amount" placeholder="min"/>
                    </div>
                    <div class="form-element">
                      <input type="text" name="response_max_amount" id="response_max_amount" placeholder="max"/>
                    </div>
                  </li>

                  <?php if (!empty($this->transaction_state)) : ?>
                    <li>
                      <span>
                        <label>
                          <?php echo $this->translate("State") ?>	
                        </label>
                      </span>
                      <select id="state" name="state">
                        <option value="0" ></option>
                        <?php foreach ($this->transaction_state as $state) : ?>
                          <option value="<?php echo $state ?>" <?php if ($this->state == "$state") echo "selected"; ?> ><?php echo $this->translate("%s", ucfirst($state)) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </li>    
                  <?php endif; ?>

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


          <div id="transaction_pagination">
          <?php endif; ?>
          <?php if ($paginationCount): ?>
            <div class="mbot5">
              <?php echo $this->translate('%s record(s) found.', $this->total_item) ?>
            </div>
          <?php endif; ?>
          <div id="manage_transaction_tab">
            <?php if ($paginationCount): ?>
              <div class="siteevent_detail_table">
                <table>
                  <tr class="siteevent_detail_table_head">
                    <th><?php echo $this->translate('Request Id') ?> </th>
                    <th><?php echo $this->translate('Gateway') ?></th>
                    <th><?php echo $this->translate('Type') ?></th>
                    <th><?php echo $this->translate('State') ?></th>
                    <th><?php echo $this->translate('Response Amount') ?></th>
                    <th><?php echo $this->translate('Date') ?></th>
                    <th class="txt_center"><?php echo $this->translate('Options') ?></th>
                  </tr>
                  <?php
                  foreach ($this->paginator as $event_transaction):
                    if ($event_transaction['gateway_id'] == 3) :
                      $event_transaction['state'] = '-';
                    endif;
                    $payment_gateway = Engine_Api::_()->siteeventticket()->getGatwayName($event_transaction['gateway_id']);
                    $detail_url = $this->url(array(
                     "action" => "view-transaction-detail",
                     "event_id" => $this->event_id,
                     "request_id" => $event_transaction['request_id'],
                     "transaction_id" => $event_transaction['transaction_id'],
                     "payment_gateway" => $payment_gateway,
                     "payment_type" => $event_transaction['type'],
                     "payment_state" => $event_transaction['state'],
                     "response_amount" => $event_transaction['response_amount'],
                     "response_date" => $event_transaction['response_date'],
                     "gateway_transaction_id" => $event_transaction['gateway_profile_id'],
                        ), "siteeventticket_order", true);
                    ?>
                    <tr>
                      <td><?php echo $event_transaction['request_id'] ?></td>
                      <td><?php echo $payment_gateway ?></td>
                      <td><?php echo $event_transaction['type'] ?></td>
                      <td><?php echo $event_transaction['state'] ?></td>
                      <td><?php echo $this->locale()->toCurrency($event_transaction['response_amount'], Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></td>
                      <td><?php echo gmdate('M d,Y, g:i A', strtotime($event_transaction['response_date'])) ?></td>
                      <td class="event_actlinks txt_center"><a class="siteevent_icon_detail" href="javascript:void(0)" onclick="Smoothbox.open('<?php echo $detail_url; ?>')" title="<?php echo $this->translate("Details") ?>"></a></td>
                    </tr>
                  <?php endforeach; ?>
                </table>
              </div>

              <div>
                <div id="event_transaction_previous" class="paginator_previous">
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array('onclick' => '', 'class' => 'buttonlink icon_previous')); ?>
                  <span id="transaction_spinner_prev"></span>
                </div>

                <div id="event_transaction_next" class="paginator_next">
                  <span id="transaction_spinner_next"></span>
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array('onclick' => '', 'class' => 'buttonlink_right icon_next')); ?>
                </div>
              </div>
            <?php else : ?>
              <div class="tip">
                <span>
                  <?php echo $this->translate('There are no transaction available yet.') ?>
                </span>
              </div>
            <?php endif; ?>
          </div>
          <?php if (empty($this->call_same_action)) : ?>
          </div>
        </div>
      <?php endif; ?>

      <script type="text/javascript">
        en4.core.runonce.add(function () {

          var anchor = document.getElementById('manage_transaction_tab').getParent();
<?php if ($paginationCount): ?>
            document.getElementById('event_transaction_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('event_transaction_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('event_transaction_previous').removeEvents('click').addEvent('click', function () {
              $('transaction_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'siteeventticket/order/transaction/event_id/' + <?php echo sprintf('%d', $this->event_id) ?>,
                data: {
                  format: 'html',
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  search: 1,
                  date: $('date').value,
                  response_min_amount: $('response_min_amount').value,
                  response_max_amount: $('response_max_amount').value,
                  state: $('state').value,
                  event_id: <?php echo sprintf('%d', $this->event_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('transaction_spinner_prev').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

            $('event_transaction_next').removeEvents('click').addEvent('click', function () {
              $('transaction_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'siteeventticket/order/transaction/event_id/' + <?php echo sprintf('%d', $this->event_id) ?>,
                data: {
                  format: 'html',
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  search: 1,
                  date: $('date').value,
                  response_min_amount: $('response_min_amount').value,
                  response_max_amount: $('response_max_amount').value,
                  state: $('state').value,
                  event_id: <?php echo sprintf('%d', $this->event_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('transaction_spinner_next').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });
<?php endif; ?>

          $('filter_form').removeEvents('submit').addEvent('submit', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif" />';

            var stateValue = '';
            if ($('state'))
              stateValue = $('state').value;

            en4.core.request.send(new Request.HTML({
              url: en4.core.baseUrl + 'siteeventticket/order/transaction',
              method: 'POST',
              data: {
                search: 1,
                subject: en4.core.subject.guid,
                call_same_action: 1,
                date: $('date').value,
                response_min_amount: $('response_min_amount').value,
                response_max_amount: $('response_max_amount').value,
                state: stateValue,
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