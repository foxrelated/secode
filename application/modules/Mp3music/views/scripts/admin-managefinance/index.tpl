 <h2><?php echo $this->translate("Mp3 Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<style type="text/css">
.table {
padding-bottom: 10px;
}
.table_bottom {
    border-top: medium none;
}
.table_clear, .table_bottom {
    background: none repeat scroll 0 0 #DFE4EE;
    border: 1px solid #DFE4EE;
    line-height: 32px;
    padding: 4px;
    position: relative;
    text-align: right;
}
.table_left {
    float: left;
    padding: 5px;
    position: relative;
    text-align: right;
    width: 25%;
    font-weight: bold;
}
.table_right {
    background: none repeat scroll 0 0 #FFFFFF;
    margin-left: 26%;
    padding: 5px;
}
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
.table_header {
    background: none repeat scroll 0 0 #495A77;
    color: #FFFFFF;
    font-size: 11pt;
    font-weight: bold;
    padding: 5px;
}
table {
   -moz-border-bottom-colors: none;
    -moz-border-image: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background: none repeat scroll 0 0 #EFEFEF;
    border-color: #CCCCCC -moz-use-text-color #CCCCCC #CCCCCC;
    border-style: solid none solid solid;
    border-width: 1px medium 1px 1px;
    width: 100%;
}
.item_is_active_holder {
    height: 30px;
    position: relative;
}
.item_is_active {
    background: none repeat scroll 0 0 #E3F6E5;
    border: 1px solid #B4E3B9;
    cursor: default;
    left: 0;
}
.item_is_active, .item_is_not_active {
    display: block;
    padding: 4px 8px 4px 4px;
    position: absolute;
    width: 50px;
}
.item_is_not_active {
    background: none repeat scroll 0 0 #F6E3E3;
    border: 1px solid #E3B4B4;
    cursor: default;
    left: 0;
    margin-left: 64px;
}
.button {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #B2B2B2;
    color: #2D2E30;
    cursor: pointer;
    font-size: 9pt;
    margin: 0;
    padding: 4px;
    font-weight: bold;
    vertical-align: middle;
}
.button:hover
{
    border: 1px solid #495A77;
}
td {
    border-right: 1px solid #CCCCCC;
    padding: 6px 4px;
    vertical-align: top;
}

.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
}
</style>   
<?php if($this->message != ''):   ?>
<div class = "message"><?php echo $this->message;?>  </div>
<?php endif; ?>
<div class="table_header">                                     
    <?php echo $this->translate("Admin Payment Account") ?>  
</div>
<form action="" method="post">
<div class="table">
    <div class="table_left">
            <?php echo $this->translate("Account Payment: ") ?>
        </div>
        <div class="table_right">
            <input type="text" name="val[account_username]" id="payment" size="40" maxlength="150" value="<?php echo $this->adminAccount['account_username'];?>" />
        </div>
        
        <div class="clear"></div>

    <div class="table_left">
            <?php echo $this->translate("Total Amount: ") ?> 
        </div>
        <div class="table_right">
            <font color="red"><?php echo $this->su;?> <?php echo $this->currency;?></font>
        </div>
        
        <div class="clear"></div>
      <div class="table_bottom">
        
        <input type="submit" name="savechange" id="savechange" class="button" value="Save Changes"/>
        <input type="hidden" name="val[paymentaccount_id]" id="paymentaccount_id" value="<?php echo $this->adminAccount['paymentaccount_id'];?>"/>
        <input type="hidden" name="val[payment_type]" id="payment_type" value="1"/> 
    </div>
</div>
</form>
<div class="table_header">
    <?php echo $this->translate("Manage Payment Request") ?>      
</div>
   
<div class="table">
<form action="" method="post">
<div class="table_left">
            <?php echo $this->translate("User Account: ") ?>      
        </div>
        <div class="table_right">
            <input type="text" name="user" id="payment" size="40" maxlength="150" value="<?php echo $this->user;?>" />
        </div>
        
        <div class="clear"></div>
<div class="table_left">
            <?php echo $this->translate("Status: ") ?>       
        </div>
        <div class="table_right">
            <select name="option_select">
                <option value="-2" <?php if ($this->option == -2):?> selected <?php endif;?>>All</option>
                <option value="0" <?php if ($this->option == 0):?>selected<?php endif;?>>Pending</option>
                <option value="1" <?php if ($this->option == 1):?>selected<?php endif;?>>Succ</option>
                <option value="-1" <?php if ($this->option == -1):?>selected<?php endif;?>>Failed</option>
                
            </select>
        </div>
        
        <div class="clear"></div>
   <div class="table_bottom">
        
        <input type="submit" name="fitter" id="fitter" class="button" value="Fitter"/>
    </div>
</form>
<?php if (count($this->accounts)>0):?>
<?php echo  $this->paginationControl($this->accounts); ?>
     <table>
        <thead>
            <td style="text-align:center" align="center">User ID</td>
            <td style="text-align:center" align="center">User Account</td>
            <td style="text-align:center" align="center">Payment Account</td>
            <td style="text-align:center" align="center">Total Amount(<?php echo $this->currency;?>)</td>
            <td style="text-align:center" align="center">Payment Request(<?php echo $this->currency;?>)</td>
            <td style="text-align:center" align="center">Accept</td>           
        </thead>
        <tbody>
            <?php foreach ($this->accounts as $acc):
            	$user = Engine_Api::_() -> getItem('user', $acc->user_id);?>
             <tr>
                <td class="stat_number" style="color: red;text-align:center" align="center" ><?php echo $acc->user_id; ?> </td>
                <td class="stat_number" style="color: red;text-align:center" align="center" >
                <?php echo $this->htmlLink(
                           $this->url(array('id'=>$user->user_id,'username'=>$user->getTitle()), 'mp3music_cart_viewtransaction'),
                            $user->getTitle(),
                            array('class'=>'smoothbox') );   ?>  
                            </td>
               
                <td class="stat_number" style="color: red;text-align:center" align="center" > <?php echo $acc->account_username; ?>   </td>
                <td class="stat_number" style="color: red;text-align:center" align="center" > <?php echo $acc->total_amount; ?> </td>
                <td class="stat_number" style="color: red;text-align:center" align="center" > <?php echo $acc->request_amount; ?>   </td>
                <?php if ($acc->request_status == 0): ?>
                     <td class="stat_number" style="color: red;text-align:center" align="center" >
                     <?php if($acc->request_amount >0):?>
                     <a href="<?php echo $this->url(array('id'=>$acc->paymentrequest_id,'status'=>1), 'mp3music_admin_main_request')?>"   title="Confirm Request">Yes</a> | <a href="<?php echo $this->url(array('id'=>$acc->paymentrequest_id,'status'=>0), 'mp3music_admin_main_request')?>"   title="Confirm Request">No</a>
                     <?php endif; ?> </td>
                <?php else: ?>
                    <?php if ($acc->request_status == -1):?>
                       <td align="center" style="color: red;text-align:center"> Failed</td>
                    <?php else: ?>
                        <td align="center"  style="color: red;text-align:center">Succ</td>
                    <?php endif; ?>
                
                <?php endif; ?>
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
</div>