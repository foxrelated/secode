 <h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>  
<?php
  function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
	  $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
      return $http.$_SERVER['HTTP_HOST'].$server_info."/";
 }       
  ?> 
    <?php
 $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Groupbuy/externals/styles/main.css');

       ?>
<script type="text/javascript"> 
var fr  = null;
var is_already = true;   
function makeRequest(f)
{
    if(f == null || f == undefined && is_already == false){     
        is_already = true;
        fr.submit();  
    }else{
         fr =  f;
         is_already = false;
         var message = f.message.value;
         var request_id = f.request_id.value;
         new Request.JSON({
          url: '<?php echo $this->url(array("module"=>"groupbuy","controller"=>"index","action"=>"makerequest"), "default") ?>',
          data: {
            'format': 'json',
            'message':  message ,
            'request_id':  request_id
            
          },
          'onComplete':function(responseObject)
            {  
                makeRequest();
            }
        }).send();
        return false; 
    }   
    return true;
}  
 </script>
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
    margin-top: -3px;
    padding: 3px;
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
  height: 65px;
  }
</style>
  <h2>
    <?php echo $this->translate('Confirm Request');?>
  </h2>
<?php if ($this->is_adaptive_payment == 1):?>                        
                     <form action="<?php echo $this->paymentForm;?>" method="post">
                  <?php else: ?>                          
                        <?php if ($this->status == 1):?>
                            <form action="<?php echo $this->paymentForm;?>" method="post" onsubmit="return makeRequest(this)">
                        <?php else: ?>
                            <form action="<?php echo $this->paymentForm;?>" method="post">
                        <?php endif; ?>
                  <?php endif; ?>
 <table width="100%">
    <tr>
                 
        <td width="60%">
            <div class="cart_id" style="height: 270px;overflow: scroll;">
                <table>
                    <tr>
                         <td><?php echo $this->translate('Leave the message for this request');?> </td>
                    </tr>
                      
                    <tr>
                       <td> <textarea cols="50" rows="12" id="message" name="pay[message]"><?php if ($this->status == 1): echo $this->translate('I have paid for your request.'); else: echo $this->translate("I don't accept your request because of some reasons..."); endif; ?></textarea> </td>
                    </tr>
                    

                </table>
            </div>
        </td>
        <td width="40%" align="left" style="vertical-align:text-top;">
            <div style="position: ;">
                    <div class="p_4">
                    <?php echo $this->translate('Reason'); ?>:<span>
                        <?php if($this->account['request_reason'] != ''): echo $this->account['request_reason']; else: echo "N/A"; endif;?>
                    </span>
                    </div>
                     <div class="p_4">
                   <?php echo $this->translate('Payment Request'); ?>:<span style="color: red;">
                        <?php echo $this->currencyadvgroup($this->account['request_amount'],$this->account['currency']);?>
                    </span>
                    </div>
                    <div class="p_4">
                      <?php echo $this->translate('Pay to account'); ?>:  <?php echo $this->account['account_username']?> 
                    </div>
                     <?php if ($this->status == 1): ?>  
                       <div class="p_4" style="padding-top: 5px;">  
                     <?php echo $this->translate('by using'); ?>
                                        <input type="hidden" value="<?php echo $this->account['paymentrequest_id'];?>" id ="request_id" name ="pay[request]">
                                       <select name="gateway_method" onchange="javascript:changeGateWay()" id="gateway_method">
                                            <option value="paypal" selected="selected" ><?php echo $this->translate("Paypal"); ?></option>
                                       </select> 
                                       <input type="submit" class="button" name="submit_p" value="<?php echo $this->translate('Send Money')?>" >
                                       <div class="p_4" style="color: red"><?php echo $this->translate('Warning: You must return to SE admin site from paypal site to complete the transaction (request transaction).');?></div>
                                       <input TYPE="hidden" NAME="cmd" VALUE="_xclick">
                                       <input TYPE="hidden" NAME="business" VALUE=" <?php echo $this->receiver['email'];?>">
                                       <input TYPE="hidden" NAME="amount" VALUE="<?php echo $this->receiver['amount'];?>">
                                       <input TYPE="hidden" NAME="currency_code" VALUE="<?php echo $this->account['currency'];?>">
                                       <input TYPE="hidden" NAME="description" VALUE="Buy music">
                                       <input type="hidden" name="notify_url" value="<?php echo $this->paramPay['ipnNotificationUrl'];?>"/>
                                       <input type="hidden" name="return" value="<?php echo $this->paramPay['returnUrl'];?>"/>
                                       <input type="hidden" name="cancel_return" value="<?php echo $this->paramPay['cancelUrl'];?>"/>
                                       <input type="hidden" name="no_shipping" value="1"/>
                                       <input type="hidden" name="no_note" value="1"/>
                                   
                                   
                                   <div>
                       </div>    
                              
                        <?php else: ?>
                        <div class="p_4" style="padding-top: 10px;"> 
                            <input type="hidden" value="<?php echo $_SESSION['payment_sercurity_adminpayout'];?>" name ="pay[sercurity]">
                                <input type="hidden" value="<?php echo $this->account['account_username'];?>" name ="pay[receiver]">
                                <input type="hidden" value="<?php echo $this->account['paymentrequest_id'];?>" name ="pay[request]">
                                <input type="hidden" value="checkout" name ="pay[task]">
                                <input type="hidden" value="<?php echo $this->status;?>" name ="pay[is_accept]">
                                <input type="hidden" value="paypal" name ="pay[gateway]" id="pay_gate_way">  
                            <input type="submit" class="button" value="<?php echo $this->translate('I do not accept this request') ?>" name="pay[cancel]">
                            </div>    
                        <?php endif; ?>
                     

                
           
        </div>
        </td>
         
    </tr>
 </table>
</form>          