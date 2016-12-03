<?php ?>
<script type="text/javascript">
    function pay(issue_id){
        var url = '<?php echo $this->url(array('action' => 'paymoney')) ?>';
        var request = new Request({
            url : url,
            data : {
              id: issue_id   
            },
            'onSuccess' : function(){
                parent.location.reload();
                parent.Smoothbox.close();
            }
        });
        request.send();
    
    }
    
    function refuse(issue_id){
        var url = '<?php echo $this->url(array('action' => 'refuse')) ?>';
        var request = new Request({
            url : url,
            data : {
              id: issue_id   
            },
            'onSuccess' : function(){
                parent.location.reload();
                parent.Smoothbox.close();
                
            }
        });
        request.send();
    }
</script>    
<div class="global_form_popup admin_member_stats">
    <h3><?php echo $this->translate('Pay money') ?></h3>
    <ul>
        <li>
            <?php $user = $this->item('user', $this->issue->user_id); ?>
            <?php echo $this->itemPhoto($user, 'thumb.icon', $user->getTitle()) ?>
        </li>    
        <li>
            <?php echo $this->translate('Amount with commission') ?>
            <span><?php echo $this->issue->amount ?></span>
        </li>
            
        <li>
            <?php echo $this->translate('Gateway')?>
            <span><?php
                switch($this->issue->gateway_id){
                    case 1:
                        $gateway = 'Paypal';
                        break;
                    case 2:
                        $gateway = 'WebMoney';
                        break;
                    default :
                        $gateway = 'Unknown';       
                }
                echo $gateway;
                ?>
                </span>
        </li>    
        <li>
            <?php echo $this->translate('Purse') ?>
            <span><?php echo $this->issue->purse ?></span>
        </li>    
    </ul>
    <br/>

    <button type="submit" onclick="pay(<?php echo $this->issue->issue_id ?>)" name="close_button" value="Close"><?php echo $this->translate('to Pay') ?></button>
    <button type="submit" onclick="refuse(<?php echo $this->issue->issue_id ?>)" name="close_button" value="Close"><?php echo $this->translate('Refuse') ?></button>
    <button type="submit" onclick="parent.Smoothbox.close();return false;" name="close_button" value="Close"><?php echo $this->translate('Close') ?></button> 
</div>