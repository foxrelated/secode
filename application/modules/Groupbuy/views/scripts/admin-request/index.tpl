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
    <?php echo $this->translate("Manage Payment Request") ?>      
</h2>
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br/>
<?php if (count($this->accounts)>0):?>
<div style="overflow: auto;">  
     <table class='admin_table'>
        <thead>
        <tr>
            <th style='width: 1%;'><?php echo $this->translate("User ID") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("User Account") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Payment Account") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Total Amount") ?>(<?php echo $this->currency;?>)</th>
            <th style='width: 1%;'><?php echo $this->translate("Payment Request") ?>(<?php echo $this->currency;?>)</th>
            <th style='width: 1%;'><?php echo $this->translate("Reason") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Accept") ?></th>           
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
               
                <td> <?php echo $acc->account_username; ?>   </td>
                <td> <?php echo $this->currencyadvgroup($acc->total_amount,$acc->currency); ?> </td>
                <td> <?php echo $this->currencyadvgroup($acc->request_amount,$acc->currency); ?>   </td>
                <td> <?php echo $acc->request_reason; ?>   </td>
                <?php if ($acc->request_status == 0): ?>
                     <td>
                     <?php if($acc->request_amount >0):?>
                     
                    <a href="<?php echo $this->url(array('id'=>$acc->paymentrequest_id,'status'=>1), 'groupbuy_admin_request-payment')?>"   title="Confirm Request"> <?php echo $this->translate("Yes") ?></a> | <a href="<?php echo $this->url(array('id'=>$acc->paymentrequest_id,'status'=>0), 'groupbuy_admin_request-payment')?>"   title="Confirm Request"> <?php echo $this->translate("No") ?></a> 
                      
                     <!--  <a class='smoothbox' href='<?php echo $this->url(array('module'=>'groupbuy','controller'=>'index','action' => 'update', 'id' => $acc->paymentrequest_id, 'status' => '1','message'=>$this->translate("I have paid for your request."),'total_amount'=>$acc->total_amount),'default',true);?>'>
                        <?php echo $this->translate("Yes") ?>
                      </a>
                      |
                      <a class='smoothbox' href='<?php echo $this->url(array('module'=>'groupbuy','controller'=>'index','action' => 'update', 'id' => $acc->paymentrequest_id, 'status' => '-1','message'=>$this->translate("I don't accept your request because of some reasons..."),'total_amount'=>$acc->total_amount),'default',true);?>'>
                        <?php echo $this->translate("No") ?>
                      </a>
                      --> 
                     <?php endif; ?> </td>
                <?php else: ?>
                    <?php if ($acc->request_status == -1):?>
                       <td style="color: red;"> <?php echo $this->translate('Failed') ?></td>
                    <?php elseif($acc->request_status == -2): ?>
                        <td style="color: blue;"><?php echo $this->translate('Waiting') ?></td>
                    <?php elseif($acc->request_status == -3): ?>
                        <td style="color: red;"><?php echo $this->translate('Denied') ?></td>
                    <?php elseif($acc->request_status == 1): ?>
                        <td style="color: blue;"><?php echo $this->translate('Success') ?></td>   
                    <?php endif; ?>
                
                <?php endif; ?>
            </tr>
           
           <?php endforeach; ?>
            
        </tbody>
        <tfoot>
            <td colspan="7">&nbsp;</td>
        </tfoot>
    </table>
 </div>   
<?php echo  $this->paginationControl($this->accounts, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->values,
    )); ?>
<?php else: ?>
   <?php echo $this->translate("There is no request for payment.") ?>                 
<?php endif; ?>
