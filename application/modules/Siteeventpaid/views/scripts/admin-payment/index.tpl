<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
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

<?php if (Engine_Api::_()->hasModuleBootstrap('siteeventticket')): ?>
  <div class='tabs'>
    <ul class="navigation">
      <?php if (Engine_Api::_()->hasModuleBootstrap('siteeventpaid')): ?>
        <li class="active">
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventpaid', 'controller' => 'payment', 'action' => 'index'), 'Events - Package Related Transactions', array())
          ?>
        </li>
      <?php endif; ?>
      <?php if ($this->paymentToSiteadmin) : ?>
        <li>
          <?php
          echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'transaction', 'action' => 'index'), 'Tickets - Order Related Transactions', array())
          ?>
        </li>		
        <li>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'transaction', 'action' => 'admin-transaction'), 'Tickets - Payments to Sellers') ?>
        </li>
      <?php else: ?>
       <li>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteeventticket', 'controller' => 'transaction', 'action' => 'order-commission-transaction'), 'Tickets - Order Commission Related Transactions') ?>
        </li>
      <?php endif; ?>
    </ul>
  </div>
<?php endif; ?>

<h3><?php echo "Events - Package Related Transactions" ?></h3>
<p>
  <?php echo "Browse through the transactions made by users for events. The search box below will search through the user names, event titles, emails, transaction IDs and order IDs. You can also use the filters below to filter the transactions."; ?>
</p>
<br />

<?php if (!empty($this->error)): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>
  <br />
  <?php return;
endif;
?>
<?php if (Engine_Api::_()->hasModuleBootstrap('payment')): ?>
  <?php if (!Engine_Api::_()->siteevent()->hasPackageEnable()): ?>
    <div class="tip">
      <span >     
    <?php echo "This transaction only for packages."; ?>
      </span>
    </div>
  <?php endif; ?>

    <?php //if( $this->paginator->getTotalItemCount() > 0 ):   ?>
  <div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
  </div>
  <?php //endif;  ?>
  <br />
  <script type="text/javascript">
    var currentOrder = '<?php echo $this->filterValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
    var changeOrder = function (order, default_direction) {
      // Just change direction
      if (order == currentOrder) {
        $('direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
      } else {
        $('order').value = order;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
  </script>

  <div class='admin_results'>
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
  <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
    </div>
    <div>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
       'query' => $this->filterValues,
       'pageAsQuery' => true,
      ));
      ?>
    </div>
  </div>
  <br />
  <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <table class='admin_table'>
      <thead>
        <tr>
    <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 5%;" class="<?php echo $class ?>">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
    <?php echo "ID"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'event_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th class="<?php echo $class ?>" style="width: 5%;">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('event_id', 'ASC');">
    <?php echo "Event Id"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th class="<?php echo $class ?>" style="width: 15%;">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">
    <?php echo "Event Title"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th class="<?php echo $class ?>" style="width: 15%;">
            <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
    <?php echo "User Name"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'gateway_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'ASC');">
    <?php echo "Gateway"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'type' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('type', 'DESC');">
    <?php echo "Type"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');">
    <?php echo "State"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'amount' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 10%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');">
    <?php echo "Amount"; ?>
            </a>
          </th>
    <?php $class = ( $this->order == 'timestamp' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
          <th style="width: 15%;" class='admin_table_centered <?php echo $class ?>'>
            <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
    <?php echo "Date"; ?>
            </a>
          </th>
          <th style="width: 10%;" class='admin_table_options'>
    <?php echo "Options"; ?>
          </th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($this->paginator as $item):
          $user = @$this->users[$item->user_id];
          $gateway = @$this->gateways[$item->gateway_id];
          ?>
          <tr>
            <td><?php echo $item->transaction_id ?></td>
            <td><?php echo $item->event_id ?></td>
            <td>
              <a href="<?php echo $this->url(array('event_id' => $item->event_id, 'slug' => $item->getSlug()), "siteevent_entry_view") ?>"  target='_blank'>
      <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), 10) ?></a>
            </td>
            <td class='admin_table_bold'>
      <?php echo ( $user ? $user->__toString() : '<i>' . 'Deleted or Unknown Owner' . '</i>' ) ?>
            </td>
            <td class='admin_table_centered'>
      <?php echo ( $gateway ? $gateway->title : '<i>' . 'Unknown Gateway' . '</i>' ) ?>
            </td>
            <td class='admin_table_centered'>
      <?php echo ucfirst($item->type) ?>
            </td>
            <td class='admin_table_centered'>
      <?php echo ucfirst($item->state) ?>
            </td>
            <td class='admin_table_centered'>
              <?php echo $this->locale()->toCurrency($item->amount, $item->currency) ?>
              <?php echo $this->translate('(%s)', $item->currency); ?>
            </td>
            <td class='admin_table_centered'>
      <?php echo $this->locale()->toDateTime($item->timestamp) ?>
            </td>
            <td class='admin_table_options'>
              <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $item->transaction_id)); ?>'>
      <?php echo "details"; ?>
              </a>
            </td>
          </tr>
    <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>