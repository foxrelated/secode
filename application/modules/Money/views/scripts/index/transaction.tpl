<?php ?>
<div class="headline">
    <h2>
        <?php echo $this->translate('E-Money'); ?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>
<div class="layout_right">
    <?php echo $this->form->render($this) ?>
</div>

<div class="layout_middle">
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <div class="money_table_form">
            <table class="money_table" width="100px">
                <thead>
                <th><?php echo $this->translate('ID') ?></th>
                <th><?php echo $this->translate('Date') ?></th>
                <th><?php echo $this->translate('Gateway') ?></th>
                <th><?php echo $this->translate('Type') ?></th>
                <th><?php echo $this->translate('Recipient') ?></th>
                <th><?php echo $this->translate('Body') ?></th>
                <th><?php echo $this->translate('Amount') ?></th>
                <th><?php echo $this->translate('Currency') ?></th>

                </thead>
                <?php foreach ($this->paginator as $item): ?>
                    <tr>
                        <td><?php echo $item->transaction_id ?></td>

                        <td><?php echo $this->timestamp($item->timestamp) ?></td>
                        <td>
                            <?php echo $this->translate('E_MONEY_'.$item->gateway_id);?>
                        </td>
                        <td>
                            <?php
                            // 1 Пополнил баланс paypal
                            // 2 Пополнил баланс Webmoney
                            // 3 Пополнил баланс Cart
                            // 4 Вывод Paypal
                            switch ($item->type) {
                                case 1:
                                    $gateway = 'Filled up the balance with Paypal';
                                    break;
                                case 2:
                                    $gateway = 'Filled up the balance with WebMoney';
                                    break;
                                case 3:
                                    $gateway = 'Filled up the balance with 2checkout';
                                    break;
                                case 4:
                                    $gateway = 'Derivation of the balance with Paypal';
                                    break;
                                case 5:
                                    $gateway = 'Derivation of the balance with WebMoney';
                                    break;
                                case 6:
                                    $gateway = 'Transferred money to a friend';
                                    break;
                                case 7:
                                    $gateway = 'Received money from a friend';
                                    break;
                                case 8:
                                    $gateway = 'Derivation of the balance with 2checkout';
                                    break;
                                case 10:
                                    $order = $this->item('money_order', $item->order_id);
                                    if ($order) {
                                        $source = $this->item($order->source_type, $order->source_id);
                                        $gateway = 'Paid Item ' . $source->title;
                                    }

                                    break;
                                case 11:
                                    $gateway = 'Filled up the balance with LiqPay';
                                    break;
                                default:
                                    $gateway = '';
                                    break;
                            }
                            echo $this->translate($gateway);
                            ?>
                        </td>
                        <td>
                            <?php if ($item->gateway_parent_transaction_id): ?>
                                <?php echo $this->htmlLink($this->item('user', $item->gateway_parent_transaction_id)->getHref(), $this->item('user', $item->gateway_parent_transaction_id)->username, array('target' => '_blank')) ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item->body ?></td>
                        <td><?php echo $item->amount ?></td>
                        <td><?php echo $item->currency ?></td>
                    </tr>    
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="tip">
                <span><?php echo $this->translate('Transactions have not yet done') ?></span>
            </div>    
        <?php endif; ?>
    </div>  
    <br>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    ));
    ?>
</div>

