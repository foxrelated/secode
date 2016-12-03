<?php ?>
<div class="headline">
    <h2>
        <?php echo $this->translate('Money'); ?>
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
<?php if($this->paginator->getTotalItemCount() > 0):?>
<table class="admin_table">
    <thead>
        <th><?php echo $this->translate('User Name')?></th>
        <th><?php echo $this->translate('Amount')?></th>
        <th><?php echo $this->translate('Purse')?></th>
        <th><?php echo $this->translate('Gateway')?></th>
        <th><?php echo $this->translate('Options')?></th>
    </thead>
    <?php foreach($this->paginator as $item):?>
        <tr>
            <?php $user = $this->item('user', $item->user_id); ?>
            <td><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></td>
            <td><?php echo $item->amount?></td>
            <td><?php echo $item->purse?></td>
            <td><?php
                switch($item->gateway_id){
                    case 1:
                        $gateway = 'Paypal';
                        break;
                    case 2:
                        $gateway = 'WebMoney';
                        break;
                    case 3:
                        $gateway = '2checkout';
                        break;
                    case 4:
                        $gateway = 'LiqPay';
                        break;
                    default :
                        $gateway = 'Unknown';       
                }
                echo $gateway;
                ?></td>
            <td>
                <?php if($item->enable == 1):?>
                <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'money', 'controller' => 'issue', 'action' => 'pay', 'issue_id' => $item->issue_id), $this->translate('to Pay'), array('class'=>'smoothbox'))?>
                <?php elseif($item->enable == 3):?>
                    <?php echo $this->translate('refuse')?>
                <?php else:?>
                    <?php echo $this->translate('approve')?>
                <?php endif;?>
            </td>
        </tr>    
    <?php endforeach;?>
</table>
<?php else:?>
    <div class="tip">
        <span><?php echo $this->translate('No one has left a request for withdrawal.')?></span>
    </div>    
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
