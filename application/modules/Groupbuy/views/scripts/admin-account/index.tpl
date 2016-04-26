 <h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
 height: 65px;
}
</style>   
 
<h2>
    <?php echo $this->translate("Manage Finance Accounts") ?>      
</h2>

<?php if (count($this->accounts)>0):?>
     <table class="admin_table">
        <thead>
        <tr>
            <th><?php echo $this->translate('User ID');?></th>
            <th><?php echo $this->translate('User Account');?></th>
            <th><?php echo $this->translate('Payment Account');?></th>
            <th><?php echo $this->translate('Total Amount');?></th>
            <th><?php echo $this->translate('Currency');?></th>
            <th><?php echo $this->translate('Options');?></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($this->accounts as $acc):?>
             <tr>
                <td><?php echo $acc->user_id; ?> </td>
                <td>
                <?php
                $user_name = $acc->username; 
                if(!$user_name):
                    $user = Engine_Api::_()->getItem('user',$acc->user_id);
                    if(!$user->displayname):
                        $user_name = $this->translate("Profile - ").$user->user_id;
                    else:
                        $user_name = $user->displayname;
                    endif;
                endif;
                echo $this->htmlLink(
                           $this->url(array('id'=>$acc->user_id,'username'=>$user_name), 'groupbuy_viewtransaction'),
                            $user_name,
                            array('class'=>'smoothbox') );   ?>  
                            </td>
               
                <td> <?php echo $acc->account_username; ?> </td>
                <td> <?php echo $this->currencyadvgroup($acc->total_amount,$acc->currency) ?> </td>
                <td> <?php if($acc->currency):
                    echo $acc->currency; 
                else:
                    echo Engine_Api::_()->groupbuy()->getDefaultCurrency();
                endif;?> </td>
                <td>
                 <a class='smoothbox' href='<?php echo $this->url(array('action' => 'plusamount', 'id' => $acc->user_id));?>'>
                <?php echo $this->translate("+ money") ?>
                </a> |
                 <a class='smoothbox' href='<?php echo $this->url(array('action' => 'examount', 'id' => $acc->user_id));?>'>
                <?php echo $this->translate("- money") ?>
                </a>
                </td>      
            </tr>
           
           <?php endforeach; ?>
            
        </tbody>
        <tfoot>
            <td colspan="7">&nbsp;</td>
        </tfoot>
    </table>   
<?php echo  $this->paginationControl($this->accounts); ?>
<?php else: ?>
   <?php echo $this->translate("There is no finance accounts.") ?>                 
<?php endif; ?>
