<?php ?>
<h2>
    <?php echo $this->translate("E-money") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php echo $this->form->render($this) ?>
<div class="clear"></div>
<br>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <table class="admin_table">
        <thead>
        <th><?php echo $this->translate('ID') ?></th>
        <th><?php echo $this->translate('User Name') ?></th>
        <th><?php echo $this->translate('Date') ?></th>
        <th><?php echo $this->translate('Gateway') ?></th>
        <th><?php echo $this->translate('Body')?>
        <th><?php echo $this->translate('Amount') ?></th>
        <th><?php echo $this->translate('Currency') ?></th>
    </thead>
    <?php foreach ($this->paginator as $item): ?>
        <tr>
            <td><?php echo $item->transaction_id ?></td>
            <td><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td><?php echo $this->timestamp($item->timestamp) ?></td>
            <td>
                <?php
                switch ($item->gateway_id) {
                    case 1:
                        $gateway = 'PayPal';
                        break;
                    case 2:
                        $gateway = 'WebMoney';
                        break;
                    case 3:
                        $gateway = '2Checkout';
                        break;
                    case 7:
                        $gateway = 'Friend';
                        break;
                    case 4:
                        $gateway = 'LiqPay';
                        break;
                    default :
                        $gateway = 'Friend';
                        break;
                }
                echo $this->translate($gateway);
                ?>
                
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
<br>
<?php
echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
        //'params' => $this->formValues,
));
?>


