 <h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<style type="text/css">
div.message {
    background: none repeat scroll 0 0 #FEFBD9;
    border: 1px solid #EEE9B5;
    color: #6B6B6B;
    font-size: 10pt;
    font-weight: bold;
    margin: 4px;
    padding: 4px;
    position: relative;
}
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
<?php if($this->message != ''):   ?>
<div class = "message"><?php echo $this->message;?>  </div>
<?php endif; ?>
<h2>
    <?php echo $this->translate("Manage Payment Refund") ?>      
</h2>
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br/>
<?php if (count($this->accounts)>0):?>
     <table class="admin_table">
        <thead>
        <tr>
            <th style='width: 1%;'><?php echo $this->translate("User ID") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("User Account") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Payment Account") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Payment Refund") ?> (<?php echo $this->currency;?>)</th>
             <th style='width: 1%;'><?php echo $this->translate("Reason") ?></th>
            <!-- <th style='width: 1%;'><?php  echo $this->translate("Accept") ?></th> -->
         </tr>           
        </thead>
        <tbody>
            <?php foreach ($this->accounts as $acc):?>
             <tr>
                <td><?php echo $acc->user_id; ?> </td>
                <td>
                <?php echo $this->htmlLink(
                           $this->url(array('id'=>$acc->user_id,'username'=>$acc->username), 'groupbuy_viewtransaction'),
                            $acc->username,
                            array('class'=>'smoothbox') );   ?>  
                            </td>
               
                <td> <?php echo $acc->account_username; ?>   </td>
                <td> <?php echo $this->currencyadvgroup($acc->request_amount); ?>   </td>
                <td> <?php echo $acc->request_reason; ?>   </td>
                <!-- <?php if ($acc->request_status == 0): ?>
                     <td>
                     <?php if($acc->request_amount >0):?>
                     <a href="<?php echo $this->url(array('id'=>$acc->paymentrequest_id,'status'=>1), 'groupbuy_admin_refund-payment')?>"   title="Confirm Request">Yes</a> | <a href="<?php echo $this->url(array('id'=>$acc->paymentrequest_id,'status'=>0), 'groupbuy_admin_refund-payment')?>"   title="Confirm Request">No</a>
                     <?php endif; ?> </td>
                <?php else: ?>
                    <?php if ($acc->request_status == -1):?>
                       <td style="color: red;text-align:center"> Failed</td>
                    <?php else: ?>
                        <td style="color: red;text-align:center">Succ</td>
                    <?php endif; ?>
                
                <?php endif; ?> -->
            </tr>
           
           <?php endforeach; ?>
            
        </tbody>
        <tfoot>
            <td colspan="7">&nbsp;</td>
        </tfoot>
    </table>   
<?php echo  $this->paginationControl($this->accounts); ?>
<?php else: ?>
   <?php echo $this->translate("There is no request for payment.") ?>                 
<?php endif; ?>
