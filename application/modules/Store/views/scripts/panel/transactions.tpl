<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: products.tpl  17.09.11 11:57 TeaJay $
 * @author     Taalay
 */
?>

<div class="layout_left">
    <?php if (!$this->isPublic): ?>
        <div id='panel_options'>
            <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
            echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->setPartial(array('_navIcons.tpl', 'core'))
                ->render()
            ?>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->filterValues['order'] ?>';
  var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
  var changeOrder = function (order, default_direction) {
    // Just change direction
    if (order == currentOrder) {
      $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('direction').value = default_direction;
    }
    $('search_form').submit();
  }
</script>


<div class="layout_middle">

  <div class="he-items">
    <h3>
      <?php echo $this->translate('My Transactions');?>
    </h3>

  <p class="flying-text-imp">
    <?php echo $this->translate("STORE_VIEWS_SCRIPTS_STATISTICS_TRANSACTIONS_DESCRIPTION") ?>
  </p>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>

<div class="he-item-list-bg">

  <div><?php echo $this->formFilter->render($this) ?></div>

  <div class="store-list-result">
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
    </div>
  </div>

  <br/>

  <table class='table store-product-list'>
    <thead>
    <tr>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('orderitem_id', 'DESC');">
          <?php echo $this->translate("ID") ?>
        </a>
      </th>
      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('name', 'ASC');">
          <?php echo $this->translate("Product") ?>
        </a>
      </th>
      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
          <?php echo $this->translate("Member") ?>
        </a>
      </th>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'DESC');">
          <?php echo $this->translate("Status") ?>
        </a>
      </th>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('qty', 'DESC');">
          <?php echo $this->translate("STORE_Quantity") ?>
        </a>
      </th>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('total_amt', 'DESC');">
          <?php echo $this->translate("Total Amount") ?>
        </a>
      </th>
      <th style="display: none;" class='table_short'>
        <?php echo $this->translate("Gateway fee") ?>
      </th>
      <th style="display: none;" class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('commission_amt', 'DESC');">
          <?php echo $this->translate("Commission") ?>
        </a>
      </th>
      <th style="width: 100px">
        <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
          <?php echo $this->translate("Date") ?>
        </a>
      </th>
      <th class='table_short'>
        <?php echo $this->translate("Options") ?>
      </th>
    </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item):
      $user    = @$this->users[$item->user_id];
      $order   = @$this->orders[$item->order_id];
      /**
       * @var $product Store_Model_Product
       */
      $product = isset($this->products[$item->item_id])?@$this->products[$item->item_id]:null;
      ?>
        <tr>
          <td><?php echo $item->orderitem_id ?></td>
          <td>
            <?php echo (($product instanceof Store_Model_Product)? $product->__toString() : '<i>' . $item->name . '</i>') ?>
          </td>
          <td>
            <?php echo ($user ? $user->__toString() : '<i>' . $this->translate('Deleted or Unknown Member') . '</i>') ?>
          </td>
          <td>
            <?php echo $this->translate(ucfirst($item->status)); ?>
          </td>
          <td>
            <?php echo $this->locale()->toNumber($item->qty); ?>
          </td>
          <td>
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price"><?php echo $this->api->getCredits($item->total_amt * $item->qty); ?></span>
              </span>
            <?php else : ?>
              <?php echo str_replace("US$","OGV",$this->locale()->toCurrency(($item->total_amt * $item->qty), $item->currency)) ?>
              
            <?php endif; ?>
          </td>

          <td style="display: none;">
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price"><?php echo $this->api->getCredits(0); ?></span>
              </span>
            <?php else : ?>
              &ndash;<?php echo $this->locale()->toCurrency(($item->getGatewayFee()), $item->currency) ?>
              
            <?php endif; ?>
          </td>

          <td style="display: none;">
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits($item->commission_amt * $item->qty); ?></span>
              </span>
            <?php else : ?>
              &ndash;<?php echo $this->locale()->toCurrency(($item->commission_amt * $item->qty), $item->currency) ?>
              
            <?php endif; ?>
          </td>
          <td>
            <?php echo $this->locale()->toDateTime($item->timestamp) ?>
          </td>
          <td>
            <a href='<?php echo $this->url(array('controller' => 'transactions', 'action' => 'detail', 'orderitem_id' => $item->orderitem_id), 'store_extended', true);?>'>
              <?php echo $this->translate("Details") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<br/>


    <?php else: ?>
<div class="tip"> <span><?php echo $this->translate('STORE_There no transaction has been done yet.');?> </span>  </div>
  <?php endif; ?>
</div>
</div>



    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query'       => $this->filterValues,
        'pageAsQuery' => true,
      )); ?>
    </div>

</div>

