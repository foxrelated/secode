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

<div class="ynaffiliate_table">
    <h3><?php echo $this->translate("List of affiliate purchases") ?></h3>
    <?php echo $this->form->render($this); ?>

    <div class="ynaffiliate_list_count_block">
        <ul class="ynaffiliate_list_count_items">
            <li class="ynaffiliate_approved">
                <span><?php echo $this->translate('Total approved commissions:') ?></span> <?php echo $this->approved_commission_count; ?>
            </li>

            <li class="ynaffiliate_commission">
                <span><?php echo $this->translate('Total waiting commissions:') ?></span>  <?php echo $this->waiting_commission_count; ?>
            </li>

            <li class="ynaffiliate_deplaying">
                <span><?php echo $this->translate('Total delayed commissions:') ?></span> <?php echo $this->delaying_commission_count; ?>
            </li>
        </ul>
    </div>

    <?php if( count($this->paginator) > 0 ): ?>
    <div class="ynaffiliate_table_scroll">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <thead class="table_thead">
                <tr class="table_th_row">
                        <th class="table_th">#</th>
                        <th class="table_th"><?php echo $this->translate('Purchased Date'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Client Name'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Purchased Type'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Total Amount'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Commission Rate'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Commission Amount'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Commission Points'); ?></th>
                        <th class="table_th"><?php echo $this->translate("Client's relations"); ?></th>
                        <th class="table_th"><?php echo $this->translate('Reason'); ?></th>
                        <th class="table_th"><?php echo $this->translate('Status') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->paginator as $item): ?>
                    <tr>
                        <td class="table_td">
                            <?php echo $item->commission_id; ?>
                        </td>
                        <td class="table_td">
                            <?php echo $this->locale()->toDateTime($item->purchase_date); ?>
                        </td>
                        <td>
                            <?php echo Engine_Api::_()->getItem('user',$item->from_user_id);     ?>
                        </td>
                        <td class="table_td">
                            <?php  $Rules = new Ynaffiliate_Model_DbTable_Rules;
                                                $rules = $Rules->getRuleById($item->rule_id);
                            echo $this->translate($rules->rule_title);
                            ?>
                        </td>
                        <td class="table_td">
                            <?php echo round($item->purchase_total_amount, 2);?>
                        </td>
                        <td class="table_td">
                            <?php if ($item->commission_type == 0) {
                            echo $item->commission_rate.'%';
                            }
                            else {
                            echo $item->commission_rate.' '.$item->purchase_currency;
                            } ?>
                        </td>
                        <td class="table_td">
                            <?php echo round($item->commission_amount, 2); ?>
                        </td>
                        <td class="table_td">
                            <?php echo round($item->commission_points, 2); ?>
                        </td>
                        <td class="table_td">
                            <?php $clientIds = Engine_Api::_()->ynaffiliate()->getRelationshipIds($item->from_user_id);
                            foreach ($clientIds as $clientId) {
                            echo Engine_Api::_()->getItem('user', $clientId);
                            echo ' > ';
                            }
                            echo $this->translate('Me');
                            echo ' (' . $this->translate('level') . ' ' .count($clientIds) . ')';
                            ?>
                        </td>
                        <td class="table_td">
                            <?php echo $item->reason; ?>
                        </td >
                        <td class="table_td">
                            <?php    echo $this->translate(ucfirst($item->approve_stat));?>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        <div>
            <?php  echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => false,
            'query' => $this->formValues,
            ));     ?>
        </div>
        <!--    <div style=" color: #5F93B4;"><?php echo $this->translate("Total:") ;?></div>-->
        <?php else:?>
        <div class="tip">
            <span>
                <?php echo $this->translate("You have no commission yet.") ?>
            </span>
        </div>
        <?php endif; ?>
    </div>