<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: admin-transaction.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function (order, default_direction) {
    if (order == currentOrder) {
      $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
    }
    else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }



</script>

<h2 class="fleft">
  <?php echo 'Advanced Events Plugin'; ?>
</h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<?php if (Engine_Api::_()->hasModuleBootstrap('siteeventpaid') || Engine_Api::_()->hasModuleBootstrap('siteeventticket')): ?>
  <div class='tabs'>
    <ul class="navigation">
      <?php if (Engine_Api::_()->hasModuleBootstrap('siteeventpaid')): ?>
        <li>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'payment', 'action' => 'index'), 'Events - Package Related Transactions', array())
          ?>
        </li>
      <?php endif; ?>
      <?php if (Engine_Api::_()->hasModuleBootstrap('siteeventticket')): ?>
        <li>
          <?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'transaction', 'action' => 'index'), 'Tickets - Order Related Transactions', array())
          ?>
        </li>		
        <li class="active">
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'transaction', 'action' => 'admin-transaction'), 'Tickets - Payments to Sellers') ?>
        </li>
      <?php endif; ?>
    </ul>
  </div>
<?php endif; ?>

<div class='settings clr'>
  <h3><?php echo "Tickets - Payments to Sellers"; ?></h3>
  <p class="description"><?php echo 'Browse through the transactions made by you to make payments to the sellers on your site. The search box below will search through the event name, owner name, transaction date, amount, gateway and state. You can also use the filters below to filter the transactions.'; ?></p>
</div>

<div class="admin_search siteeventticket_admin_search">
  <div class="search">
    <form method="post" class="global_form_box" action="">
      <input type="hidden" name="post_search" /> 
      <div>
        <label>
          <?php echo "Event Name" ?>
        </label>
        <?php if (empty($this->title)): ?>
          <input type="text" name="title" /> 
        <?php else: ?>
          <input type="text" name="title" value="<?php echo $this->title ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Owner Name" ?>
        </label>
        <?php if (empty($this->username)): ?>
          <input type="text" name="username" /> 
        <?php else: ?>
          <input type="text" name="username" value="<?php echo $this->username ?>"/>
        <?php endif; ?>
      </div>


      <div>
        <label>
          <?php echo "Transaction Date: ex (2000-12-25)" ?>
        </label>
        <?php if (empty($this->date)): ?>
          <input type="text" name="date" /> 
        <?php else: ?>
          <input type="text" name="date" value="<?php echo $this->date ?>"/>
        <?php endif; ?>
      </div>

      <div>
        <label>
          <?php echo "Amount" ?>
        </label>
        <div>
          <?php if ($this->response_min_amount == ''): ?>
            <input type="text" class="input_field_small" name="response_min_amount" placeholder="min"/> 
          <?php else: ?>
            <input type="text" class="input_field_small" name="response_min_amount" placeholder="min" value="<?php echo $this->response_min_amount ?>"/>
          <?php endif; ?>

          <?php if ($this->response_max_amount == ''): ?>
            <input type="text" class="input_field_small" name="response_max_amount" placeholder="max"/> 
          <?php else: ?>
            <input type="text" class="input_field_small" name="response_max_amount" placeholder="max" value="<?php echo $this->response_max_amount ?>"/>
          <?php endif; ?>
        </div>

      </div>

      <div>
        <label>
          <?php echo "Gateway" ?>	
        </label>
        <select id="" name="gateway_id">
          <option value="0" ></option>
          <option value="1" <?php if ($this->gateway_id == 1) echo "selected"; ?> ><?php echo "2Checkout" ?></option>
          <option value="2" <?php if ($this->gateway_id == 2) echo "selected"; ?> ><?php echo "PayPal" ?></option>
          <option value="3" <?php if ($this->gateway_id == 3) echo "selected"; ?> ><?php echo "By Cheque" ?></option>
        </select>
      </div>

      <div>
        <label>
          <?php echo "State" ?>	
        </label>
        <select id="" name="state">
          <option value="0" ></option>
          <option value="failed" <?php if ($this->state == "failed") echo "selected"; ?> ><?php echo "Failed" ?></option>
          <option value="okay" <?php if ($this->state == "okay") echo "selected"; ?> ><?php echo "Okay" ?></option>
          <option value="pending" <?php if ($this->state == "pending") echo "selected"; ?> ><?php echo "Pending" ?></option>
        </select>
      </div>

      <div class="clear mtop10">
        <button type="submit" name="search" ><?php echo "Search" ?></button>
      </div>

    </form>
  </div>
</div>
<br />

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>


<div class='admin_members_results'>
  <?php
  if (!empty($this->paginator)) {
    $counter = $this->paginator->getTotalItemCount();
  }
  if (!empty($counter)):
    ?>
    <div class="">
      <?php echo $this->translate(array('%s transaction found.', '%s transactions found.', $counter), $this->locale()->toNumber($counter)) ?>
    </div>
  <?php else: ?>
    <div class="tip"><span>
        <?php echo "No results were found." ?></span>
    </div>
  <?php endif; ?> 
</div>
<br />

<?php if (!empty($counter)): ?>

  <table class='admin_table'>
    <thead>
      <tr>
        <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');"><?php echo 'ID'; ?></a></th>

        <?php $class = ( $this->order == 'request_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('request_id', 'DESC');"><?php echo 'Request Id'; ?></a></th>

        <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo 'Event Name'; ?></a></th>

        <?php $class = ( $this->order == 'username' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo 'Owner Name'; ?></a></th>

        <?php $class = ( $this->order == 'gateway_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'DESC');"><?php echo 'Gateway'; ?></a></th>

        <th class='admin_table_short'><?php echo "Type" ?></th>

        <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');"><?php echo 'State'; ?></a></th>

        <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
        <th class="<?php echo $class ?>" ><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');"><?php echo 'Amount'; ?></a></th>

        <th class='admin_table_short'><?php echo "Date" ?></th>
        <th class='admin_table_short'><?php echo "Options" ?></th>
      </tr>
    </thead>
    <?php
    foreach ($this->paginator as $transaction):
      $gateway_name = Engine_Api::_()->siteeventticket()->getGatwayName($transaction->gateway_id);

      $response_amount = Engine_Api::_()->siteeventticket()->getPriceWithCurrency($transaction->response_amount);
      $view_url = $this->url(array(
       'module' => 'siteeventticket',
       'controller' => 'transaction',
       'action' => 'view-admin-transaction',
       'request_id' => $transaction->request_id,
       'event_id' => $transaction->event_id,
       'payment_gateway' => $gateway_name,
       'payment_type' => $transaction->type,
       'payment_state' => $transaction->state,
       'payment_amount' => $response_amount,
       'date' => $transaction->response_date,
       'gateway_transaction_id' => $transaction->gateway_profile_id,
       'transaction_id' => $transaction->transaction_id,
          ), 'admin_default', true);
      ?>
      <tr>
        <td class='admin_table_short'><?php echo $transaction->transaction_id ?></td>
        <td class='admin_table_short'><?php echo $transaction->request_id ?></td>
        <?php $eventItem = $this->item('siteevent_event', $transaction->event_id); ?>
        <td class='admin_table_short'>
          <?php if (empty($eventItem)): ?>
            <i>Event Deleted</i>
          <?php else: ?>
            <?php echo $this->htmlLink($eventItem->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($eventItem->getTitle(), 10), array('title' => $eventItem->getTitle(), 'target' => '_blank')) ?>
          <?php endif; ?>
        </td>
        <td class='admin_table_short'><?php echo $this->htmlLink($transaction->getOwner()->getHref(), $this->string()->truncate($this->string()->stripTags($transaction->getOwner()->getTitle()), 10), array('title' => $transaction->getOwner()->getTitle(), 'target' => '_blank')) ?></td>
        <td class='admin_table_short'><?php echo $gateway_name ?></td>
        <td class='admin_table_short'><?php echo $transaction->type ?></td>
        <td class='admin_table_short'><?php echo $transaction->state ?></td>
        <td class='admin_table_short'><?php echo $response_amount ?></td>
        <td class='admin_table_short'><?php echo gmdate('M d,Y, g:i A', strtotime($transaction->response_date)) ?></td>
        <td class='admin_table_short'>
          <?php if (empty($eventItem)): ?>
            <?php echo '-'; ?>
          <?php else: ?>
            <?php echo '<a href="javascript:void(0)" onClick="Smoothbox.open(\'' . $view_url . '\')">details</a>' ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <br />
  <div>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
     'pageAsQuery' => true,
     'query' => $this->formValues,
    ));
    ?>
  </div>
  <br />
<?php endif; ?>