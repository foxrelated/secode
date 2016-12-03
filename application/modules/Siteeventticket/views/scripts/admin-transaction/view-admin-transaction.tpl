<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-admin-transaction.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo 'Transaction Details'; ?></h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo 'Transaction ID'; ?>
  </dd>
  <dt>
  <?php echo $this->locale()->toNumber($this->transaction_id); ?>
  </dt>

  <dd>
    <?php echo 'Event Id' ?>
  </dd>
  <dt>
  <?php echo $this->htmlLink($this->siteevent->getHref(), $this->locale()->toNumber($this->event_id), array('target' => '_blank')) ?>
  </dt>

  <dd>
    <?php echo 'Event Name'; ?>
  </dd>
  <dt>
  <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle(), array('target' => '_blank')); ?>
  </dt>

  <dd>
    <?php echo 'Owner Name'; ?>
  </dd>
  <dt>
  <?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('target' => '_blank')); ?>
  </dt>

  <dd>
    <?php echo 'Payment Gateway'; ?>
  </dd>
  <dt>
  <?php if ($this->payment_gateway): ?>
    <?php echo $this->payment_gateway; ?>
  <?php else: ?>
    <i><?php echo 'Unknown Gateway'; ?></i>
  <?php endif; ?>
  </dt>

  <dd>
    <?php echo 'Payment Type'; ?>
  </dd>
  <dt>
  <?php echo ucfirst($this->payment_type) ?>
  </dt>

  <dd>
    <?php echo 'Payment State'; ?>
  </dd>
  <dt>
  <?php echo ucfirst($this->payment_state); ?>
  </dt>

  <dd>
    <?php echo 'Response Amount'; ?>
  </dd>
  <dt>
  <?php echo $this->payment_amount; ?>
  </dt>

  <dd>
    <?php echo 'Gateway Transaction ID'; ?>
  </dd>
  <dt>
  <?php if (!empty($this->gateway_transaction_id)): ?>
    <?php
    echo $this->htmlLink(array(
     'route' => 'admin_default',
     'module' => 'siteeventticket',
     'controller' => 'payment',
     'action' => 'detail-transaction',
     'transaction_id' => $this->transaction_id,
        ), $this->gateway_transaction_id, array(
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
  <?php echo gmdate('M d,Y, g:i A', strtotime($this->date)) ?>
  </dt>

</dl>

<div class='buttons'>
  <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Close"; ?></button>
</div>