<div class="headline">
  <h2>
    <?php echo $this->translate('Deals');?>
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
  <?php
  function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
	  $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
      return $http.$_SERVER['HTTP_HOST'].$server_info."/";
 } 
 $item = $this->deal;      
  ?> 
<script type="text/javascript"> 
var fr  = null;
var is_already = true;
 function makeBill(f)
{
    var numbers = $('number').value;
    if(f == null || f == undefined && is_already == false){     
      fr.submit();
       
    }else{
         fr =  f;
         is_already = false;
         new Request.JSON({
          url: '<?php echo $this->url(array("module"=>"groupbuy","controller"=>"index","action"=>"makebill-buy"), "default") ?>',
          data: {
            'format': 'json',
            'deal' : <?php echo $item->deal_id; ?>,
            'numbers' : numbers
          },
          'onComplete':function(responseObject)
            {  
                makeBill();
            }
        }).send();
        return false; 
    }   
    return true;
}
function numberChange()
{
    var numbers = $('number').value;
    if(numbers != "")
    {
        if(isNumber(numbers))
        {
            numbers = parseInt(numbers);
           
            if(numbers <= 0)
            {
                alert('<?php echo $this->translate("Number of deal should be greater than 0!") ?>');
                $('number').value = 1;
                var total = <?php echo $item->price?>;
                $('total').innerHTML = total.toFixed(2);
                $('amount').value = total.toFixed(2);
            }
            else
            {
                var total = numbers * <?php echo $item->price?>;
                $('total').innerHTML = total.toFixed(2);
                //$('amount').value = total.toFixed(2);
            }
             if($('quantity_paypal'))
            {
                $('quantity_paypal').value = numbers;
            }
        }
        else
        {
            $('number').value = 1;
            var total = <?php echo $item->price?>;
            $('total').innerHTML = total.toFixed(2);
            $('amount').value = total.toFixed(2);
            alert('<?php echo $this->translate("Number of deal is invalid!") ?>');
        }
    }
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

 </script>
  <h2>
    <?php echo $this->translate('Deal information');?>
  </h2>
<div class="table">    
                <table width="100%">
                      <tr>
                          <td valign='top' width='1' style=' text-align: center; padding-top:6px;  padding-bottom:6px; text-align: center;'>
                           <a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>"><img src="<?php if($item->getPhotoUrl("thumb.profile") != ""): echo $item->getPhotoUrl("thumb.profile"); else: echo 'application/modules/Groupbuy/externals/images/nophoto_product_thumb_profile.png'; endif;?>" style = "max-width:250px;max-height:250px" /></a>
                        </td>
                      <td valign='top' class="contentbox" style="width: auto; padding-left: 30px;">
                      <strong id="title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </strong>
                         <div id="body" style="padding-top:5px;" class="auction_list_description">
                          <?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>350) echo "..."; ?>
                          </div>
                         <?php if($item->price > 0): ?>
                       <div style="padding-top:5px;" >
                       <span style="font-weight: bold;"> <?php echo $this->translate('Price: ');?></span> <font color="red" style="font-weight: bold;" ><span id="price"><?php echo number_format($item->price,2);?> </span><?php echo $this->currency ?></font>
                       </div>
                       <div style="padding-top: 5px;">
                       <span style="font-weight: bold;"><?php echo $this->translate('Quantity: ') ?></span><input onkeyup="numberChange()" type="text" id="number" value="1"/>
                       </div>
                       <div style="padding-top: 5px;">
                       <span style="font-weight: bold;"><?php echo $this->translate('Total: ') ?></span><font color="red" style="font-weight: bold;" ><span id="total"><?php echo number_format($item->price,2);?> </span> <?php echo $this->currency ?></font>
                       </div>
                       <?php endif; ?>
                        <br/>
                        <?php if($item->price > 0): ?>
                        <form action="<?php echo $this->paymentForm;?>" method="post" name="cart_form" onsubmit="return makeBill(this);">
                        <div class="p_4">
                           <button  name="minh" type="submit" style="float: left; " ><?php echo $this->translate('Pay with paypal');?></button>
                           <input TYPE="hidden" NAME="cmd" VALUE="_xclick"/>
                           <input TYPE="hidden" NAME="business" VALUE="<?php echo $this->receiver['email']?>"/>
                           <input TYPE="hidden" id="amount" NAME="amount" VALUE="<?php echo $item->price;?>"/>
                           <input TYPE="hidden" NAME="currency_code" VALUE="<?php echo $this->currency;?>"/>
                           <input TYPE="hidden" NAME="description" VALUE="Pay auction"/>
                           <input type="hidden" id = "item_name" name="item_name" value="<?php echo $item->title  ?>">
                           <input type="hidden" id = "quantity_paypal" name="quantity" value="1">
                           <input type="hidden" name="notify_url" value="<?php echo $this->paramPay['ipnNotificationUrl']?>"/>
                           <input type="hidden" name="return" value="<?php echo $this->paramPay['returnUrl']?>"/>
                           <input type="hidden" name="cancel_return" value="<?php echo $this->paramPay['cancelUrl']?>"/>
                           <input type="hidden" name="no_shipping" value="1"/>
                           <input type="hidden" name="no_note" value="1"/>
                        
                          <div style="float: left; margin-top: 7px; padding-left: 10px;">
                            <?php echo $this->translate('Or ') ?>  
                           <?php echo $this->htmlLink(array(
                              'action' => '',
                                'route' => 'groupbuy_general',
                            ), $this->translate('Cancel'), array(
                              'style' => 'font-weight: bold;',
                            )) ?>
                            </div>  
                           <div>
                           
                        </div>
                    </form>
                    <?php else: ?>
                    <div style="padding-top:5px;" >
                       <span style="font-weight: bold;"> <?php echo $this->translate('Price: ');?></span> <font color="red" style="font-weight: bold;" ><span id="price"><?php echo number_format($item->price,2);?> </span><?php echo $this->currency ?></font>
                       </div>
                      <div style="margin-top: 7px;"> 
                     <?php echo $this->htmlLink(array(
                              'action' => '',
                                'route' => 'groupbuy_general',
                            ), $this->translate('Cancel'), array(
                              'style' => 'font-weight: bold;',
                            )) ?>
                    <?php endif; ?>
                    </div>
                    </td> 
                </tr>
                </table>      
 </div>
