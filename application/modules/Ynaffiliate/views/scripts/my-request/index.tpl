<script type = "text/javascript">
var currentOrder = '<?php echo $this->formValues['order'] ?>';
var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
var changeOrder = function(order, default_direction){
  // Just change direction
  if( order == currentOrder ) {
    $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
  } else {
    $('order').value = order;
    $('direction').value = default_direction;
  }
  $('filter_form').submit();
}
</script>
<div class="generic_layout_container adv_affiliate_account clearfix">

    <div class="ynaffiliate_div_my_account">
        <h3><?php echo $this->translate('Account'); ?></h3>
        <div class="yntable-item">
            <span class="label-cell"><?php echo $this->translate('Gateway'); ?>:</span>
            <?php echo $this->translate('PayPal'); ?>
        </div>
        <div class="yntable-item">
            <span class="label-cell"><?php echo $this->translate('Display Name'); ?>:</span>
            <?php echo $this->account_name; ?>
        </div>
        <div class="yntable-item">
            <span class="label-cell"><?php echo $this->translate('Email Address'); ?>:</span>
            <?php echo $this->account_email; ?>
        </div>
        <div class="yntable-item">
            <span class="label-cell"><?php echo $this->translate('Selected Currency'); ?>:</span>
            <?php echo $this->selected_currency; ?>
        </div>
        <div class="yntable-item">
            <div class="">
                <a href="<?php echo $this->url(array('action' => 'edit'),'ynaffiliate_account') ?>" title="<?php echo $this->translate('Edit Account'); ?>" ><button  name="edit_account"><?php echo $this->translate('Edit Account'); ?></button></a>
            </div>
        </div>
    </div>

    <div class="ynaffiliate_div_summary clearfix">
        <h3><?php echo $this->translate('Balance'); ?></h3>
        
        <div class="yntable-item-left">
            <div class="yntable-item">
                <span class="label-cell">
                    <?php echo $this->translate('Total commission points'); ?>
                    <p><?php echo $this -> translate('The commissions for sold deals')?></p>
                </span>
                <span id="current_request_money" class="text-blue">
                    <?php  echo $this->locale()->toNumber($this->totalPoints);?>
                </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell">
                    <?php echo $this->translate('Delaying Commission points'); ?>
                    <p><?php echo $this -> translate('The commissions of new transactions have a %s days delay to allow for refunds and disputes', Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.delay', 30))?></p>
                </span>
                <span id="current_request_money" class="text-blue">
                    <?php  echo $this->locale()->toNumber($this->delayingCommissionPoints);?>
                </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell">
                    <?php echo $this->translate('Available points'); ?>
                    <p><?php echo $this -> translate('Total available amount you can request to get real money')?></p>
                </span>
                <span id="current_request_money" class="text-blue">
                    <?php  echo $this->locale()->toNumber($this->availablePoints);?>
                </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell">
                    <?php echo $this->translate('Pending points'); ?>
                    <p><?php echo $this -> translate('Total amount you requested to exchange to real money.')?></p>
                    <p><?php echo $this -> translate('It is pending for approval')?></p>
                </span>
                <span id="current_request_money" class="text-blue">
                    <?php  echo $this->locale()->toNumber($this->currentRequestPoints);?>
                </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell">
                    <?php echo $this->translate('Received points'); ?>
                    <p><?php echo $this -> translate('Total real money you have received')?></p>
                </span>
                <span id="current_request_money" class="text-blue">
                    <?php  echo $this->locale()->toNumber($this->requestedPoints);?>
                </span>
            </div>
        </div>



        <div class="yntable-item-right">
            <div class="yntable-item">
                <span class="label-cell"><?php echo $this->translate('Currency'); ?>:</span>
                <span id="current_request_money" class="text-blue">
                    <?php  echo $this->selected_currency;?>
                </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell"><?php echo $this->translate('Points Conversion Rate'); ?>:</span>
                <span id="current_money" class="text-red">
                    <?php
                    $points_convert_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynaffiliate.pointrate', 1);
                    echo $this->translate('1 '.$this->selected_currency.' = '.round((1/$this->exchange_rate)*$points_convert_rate,2)." ".'point(s)');?>
                </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell"><?php echo $this->translate('Minimum Request points'); ?>:</span>
                <span class="text-red"> <?php echo $this->locale()->toNumber($this->minRequest);  ?>  </span>
            </div>
            <div class="yntable-item">
                <span class="label-cell"><?php echo $this->translate('Maximum Request points'); ?>:</span>
                <span style="color: red;font-weight: bold;"> <?php echo $this->locale()->toNumber($this->maxRequest);  ?> </span>
            </div>
        </div>
    </div>
</div>

<div class="ynaffiliate_table">
    <h3><?php echo $this->translate("Manage Requests") ?></h3>
        <?php echo $this->form->render($this); ?>

    <?php if( count($this->paginator) > 0 ): ?>

    <div class="ynaffiliate_table_scroll">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <thead>
                <tr class="table_th_row">
                    <th class="table_th">
                        <a href="javascript:void(0);" onclick="javascript:changeOrder('request_date', 'ASC');">
                            <?php echo $this->translate('Request Date'); ?>
                        </a>
                    </th>
                    <th class="table_th">
                        <a href="javascript:void(0);" onclick="javascript:changeOrder('request_amount', 'DESC');">
                            <?php echo $this->translate('Request Amount'); ?>
                        </a>
                    </th>

                    <th class="table_th">
                            <?php echo $this->translate('Request Currency'); ?>
                    </th>

                    <th class="table_th">
                        <a href="javascript:void(0);" onclick="javascript:changeOrder('request_points', 'DESC');">
                            <?php echo $this->translate('Request Point'); ?>
                        </a>
                    </th>
                    <th class="table_th">
                        <a href="javascript:void(0);" onclick="javascript:changeOrder('request_status', 'DESC');">
                            <?php echo $this->translate('Status'); ?>
                        </a>
                    </th>
                    <th class="table_th">
                        <?php echo $this->translate('Request Message'); ?>
                    </th>
                    <th class="table_th">
                        <a href="javascript:void(0);" onclick="javascript:changeOrder('response_date', 'DESC');">
                            <?php echo $this->translate('Response Date'); ?>
                        </a>
                    </th>
                    <th class="table_th">
                        <?php echo $this->translate('Response Message'); ?>
                    </th>
                    <th class="table_th">
                        <?php echo $this->translate('Action'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($this->paginator as $item): ?>
                <tr>
                    <td class="table_td">
                        <?php echo $this->locale()->toDateTime($item->request_date); ?>
                    </td>
                    <td class="table_td">
                        <?php echo $this->locale()->toNumber(round($item->request_amount, 2)); ?>
                    </td>
                    <td class="table_td">
                        <?php echo $item->currency; ?>
                    </td>
                    <td class="table_td">
                        <?php echo $this->locale()->toNumber(round($item->request_points, 2)); ?>
                    </td>
                    <td class="table_td">
                        <?php echo ucfirst($item->request_status); ?>
                    </td>
                    <td class="table_td">
                        <?php echo $item-> request_message; ?>
                    </td>
                    <td class="table_td">
                        <?php echo $this->locale()->toDateTime($item-> response_date); ?>
                    </td>
                    <td class="table_td">
                        <?php echo $item-> response_message; ?>
                    </td>
                    <td class="table_td">
                        <?php if($item->request_status == 'waiting'):?>
                        <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'my-account', 'action' => 'cancel-request', 'requestId' => $item -> getIdentity()), 'ynaffiliate_extended') ?>" title="<?php echo $this->translate('Cancel Request'); ?>" ><?php echo $this->translate('Cancel Request'); ?></a>
                        <?php else:?>
                        <?php echo $this -> translate("N/A");?>
                        <?php endif;?>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>

<?php  echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => false,
'query' => $this->formValues,
));     ?>
</div>
<?php else:?>
<div class="tip">
<span>
    <?php echo $this->translate("You have no requests yet.") ?>
</span>
</div>
<?php endif; ?>

<div class="yntable-item">
    <div class="p_4">
        <?php
        if ($this->account_email == ''): ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("Please update your Paypal account in order to request money."); ?>
            </span>
        </div>
        <?php elseif($this->availablePoints < $this->minRequest): ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("Your available amount is not enough to make a request. It has to be larger than %s", $this->minRequest); ?>
            </span>
        </div>
        <?php else:?>
        <?php if ($this->is_admin == 1) : ?>
        <div class="tip">
            <span>
                <?php echo $this->translate("You cannot make request!"); ?>
            </span>
        </div>
        <?php else: ?>
        <a class="smoothbox" href="<?php echo $this->url(array(), 'ynaffiliate_payment_threshold') ?>" title="<?php echo $this->translate('Request Money'); ?>" ><button  name="request"><?php echo $this->translate('Request Money'); ?></button></a>
        <?php endif;?>
        <?php endif; ?>
    </div>
</div>