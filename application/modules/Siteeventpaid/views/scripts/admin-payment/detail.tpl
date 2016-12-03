<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="payment_transaction_detail_headline">
  <?php echo "Transaction Details"; ?>
</h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo 'Transaction ID'; ?>
  </dd>
  <dt>
  <?php echo $this->locale()->toNumber($this->transaction->transaction_id) ?>
  </dt>
  <dd>
    <?php echo 'User Name'; ?>
  </dd>
  <dt>
  <?php if ($this->user && $this->user->getIdentity()): ?>
    <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?>
    <?php //echo $this->user->__toString() ?>
    <?php if (!_ENGINE_ADMIN_NEUTER): ?>
      <?php
      echo '(%1$s)', '<a href="mailto:' .
      $this->escape($this->user->email) . '">' . $this->user->email . '</a>';
      ?>
    <?php endif; ?>
  <?php else: ?>
    <i><?php echo 'Deleted Event Owner'; ?></i>
<?php endif; ?>
  </dt>
  <dd>
<?php echo 'Event Title'; ?>
  </dd>
  <dt>
  <a href="<?php echo $this->url(array('event_id' => $this->siteevent->event_id, 'slug' => $this->siteevent->getSlug()), "siteevent_entry_view"); ?>"  target='_blank' title="<?php echo ucfirst($this->siteevent->title); ?>">
<?php echo $this->siteevent->title; ?></a>
  </dt>
  <dd>
<?php echo 'Payment Gateway'; ?>
  </dd>
  <dt>
  <?php if ($this->gateway): ?>
    <?php echo $this->gateway->title; ?>
  <?php else: ?>
    <i><?php echo 'Unknown Gateway'; ?></i>
<?php endif; ?>
  </dt>

  <dd>
<?php echo 'Payment Type'; ?>
  </dd>
  <dt>
<?php echo ucfirst($this->transaction->type); ?>
  </dt>

  <dd>
<?php echo 'Payment State'; ?>
  </dd>
  <dt>
<?php echo ucfirst($this->transaction->state); ?>
  </dt>

  <dd>
<?php echo 'Payment Amount'; ?>
  </dd>
  <dt>
  <?php echo $this->locale()->toCurrency($this->transaction->amount, $this->transaction->currency) ?>
<?php echo '(%s)', $this->transaction->currency; ?>
  </dt>

  <dd>
<?php echo 'Gateway Transaction ID'; ?>
  </dd>
  <dt>
  <?php if (!empty($this->transaction->gateway_transaction_id)): ?>
    <?php
    echo $this->htmlLink(array(
     'route' => 'admin_default',
     'module' => 'siteeventpaid',
     'controller' => 'payment',
     'action' => 'detail-transaction',
     'transaction_id' => $this->transaction->transaction_id,
        ), $this->transaction->gateway_transaction_id, array(
     //'class' => 'smoothbox',
     'target' => '_blank',
    ))
    ?>
  <?php else: ?>
    -
<?php endif; ?>
  </dt>

    <?php if (!empty($this->transaction->gateway_parent_transaction_id)): ?>
    <dd>
  <?php echo 'Gateway Parent Transaction ID'; ?>
    </dd>
    <dt>
    <?php
    echo $this->htmlLink(array(
     'route' => 'admin_default',
     'module' => 'communityad',
     'controller' => 'payment',
     'action' => 'detail-transaction',
     'transaction_id' => $this->transaction->transaction_id,
     'show-parent' => 1,
        ), $this->transaction->gateway_parent_transaction_id, array(
     //'class' => 'smoothbox',
     'target' => '_blank',
    ))
    ?>
    </dt>
<?php endif; ?>

  <dd>
<?php echo 'Gateway Order ID'; ?>
  </dd>
  <dt>
  <?php if (!empty($this->transaction->gateway_order_id)): ?>
    <?php
    echo $this->htmlLink(array(
     'route' => 'admin_default',
     'module' => 'communityad',
     'controller' => 'payment',
     'action' => 'detail-order',
     'transaction_id' => $this->transaction->transaction_id,
        ), $this->transaction->gateway_order_id, array(
     //'class' => 'smoothbox',
     'target' => '_blank',
    ))
    ?>
  <?php else: ?>
    -
<?php endif; ?>
  </dt>
  <dd>
<?php echo 'Date'; ?>
  </dd>
  <dt>
<?php echo $this->locale()->toDateTime($this->transaction->timestamp) ?>
  </dt>
  <dd>
<?php echo 'Options'; ?>
  </dd>
  <dt>
  <?php
  if ($this->order && !empty($this->order->source_id) &&
      $this->order->source_type == 'payment_subscription'):
    ?>
    <?php
    echo $this->htmlLink(array(
     'reset' => false,
     'controller' => 'subscription',
     'action' => 'detail',
     'subscription_id' => $this->order->source_id,
     'transaction_id' => null,
        ), 'Related Subscription', array(
     'target' => '_parent'
    ))
    ?>
  <?php else: ?>
    -
<?php endif; ?>
  </dt> 
  <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>
</dl>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>