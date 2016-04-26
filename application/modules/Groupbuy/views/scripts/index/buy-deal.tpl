<div class="headline">
  <h2>
    <?php echo $this->translate('GroupBuy');?>
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
 $itcurrency = $item->getCurrency();      
	$precision = $itcurrency->precision;
 ?> 
<script type="text/javascript"> 
var fr  = null;
var is_already = true;
function update(numbers){
		var final_price = <?php echo $item->final_price?>;
		var total 		= numbers * final_price;
		total = total.toFixed(<?php echo $precision ?>);
		
		// check and update status of value.		
		if($('number')){
			$('number').value = numbers;
		}		
		
		// update amount of COD
		if($('number_buy')){
            $('number_buy').value = numbers;  
		}
		if($('number_buy1')){
            $('number_buy1').value = numbers;  
        }
		if($('total_amount')){
            $('total_amount').value = total;
		}
        if($('total_amount1')){
            $('total_amount1').value = total;
        }
		
		// update total value of this bill.
		if($('total')){
			$('total').innerHTML = total;	
		}
		
		// update TOTAL AMOUNT of PAYPAL
		if($('amount')){
            //$('amount').value = total; 
        }
        if($('amount2')){
            $('amount2').value = total;
            $('quantity').value = numbers;
		}
        if($('amount1')){
            $('amount1').value = total;
        }
        $$('.payment_quantity').each(function(el) {
        	el.value = numbers;
        });
        if($('virtualmoney'))
        {
            if(total > <?php echo $this->current_amount ?>)
            {
                 $('virtualmoney').style.display = 'none';
            }
            else
            {
                $('virtualmoney').style.display = 'inline-block';
            }
        }

	}


function check()
{
    var numbers = $('number').value;
    if(numbers != "")
    {
    	update(numbers);	
        return true;
    }
    else
    {
    	update(1);
        alert('<?php echo $this->translate("Quantity must be 1 or more; we took care of that for you.!") ?>');
        return false;
    }
}
 
function numberChange(msg)
{
	function showMessage(text){
		if(!msg){alert(text);}
	}
    var numbers = $('number').value;

    if(numbers != "")
    {
        if(isNumber(numbers))
        {
          numbers = parseInt(numbers);
		  $('number').value = numbers;
		    if(numbers <= 0 || $('number').value == '')
            {
               update(1);
               showMessage('<?php echo $this->translate("Quantity must be 1 or more; we took care of that for you.!") ?>');
            }
			else
            {
				var maxbought = '<?php echo $item->max_bought;?>';
				if (maxbought != 0) { 
					var checkbought = '<?php echo $item->getMaxBought($this->viewer)?>';
					if  (checkbought == 0) {
						showMessage("You have bought maximum quatity allowed. You cannot buy this deal anymore");
						update(0);
						if ($('buygift')) {
							$('buygift').hide();
						}
						if ($('paypalbutton')) {
							$('paypalbutton').hide();
						}
						if ($('codbutton')) {
							$('codbutton').hide();
						}
					}
					else {
						if (numbers > checkbought) {	
							showMessage("You can only buy " + checkbought + " more deals!");
							update(checkbought);
						}
						else {
							update(numbers);
						}
					}
				}
				else {
					var maxsold = '<?php echo $item->max_sold;?>';
					var currentsold = '<?php echo $item->current_sold;?>';
					var leftsold = maxsold - currentsold;
					if (numbers > leftsold) {	
						showMessage("You can only buy " + leftsold + " more deals!");
						update(leftsold);
					}
					else {
						update(numbers);
					}
				}
            }
		  
		}
        else
        {
        	update(1);
        	showMessage('<?php echo $this->translate("Number of quantity is invalid!") ?>');
        }
    }
    else 
    {
    	$('total').innerHTML = 0;
    	$('amount').value = 0;
    }
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

 </script>
 <?php if($this->canBuy == true && $item->status == 30 && $item->published == 20):?>
  <h2>
    <?php echo $this->translate('Deal information');?>
  </h2>
<div class="table">    
           	<table width="100%">
                 	<tr>
                          <td valign='top' width='1' class="nomobile">
                           	<a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>"><img src="<?php if($item->getPhotoUrl("thumb.normal") != ""): echo $item->getPhotoUrl("thumb.normal"); else: echo 'application/modules/Groupbuy/externals/images/nophoto_deal_thumb_profile.png'; endif;?>" style = "max-width:200px;max-height:150px" /></a>
                        	</td>

                      	<td valign='top' class="contentbox" style="width: auto; padding-left: 10px;">

	            			<strong id="title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </strong>
	                        	<div class="visible-xs">
	                            	<a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>"><img src="<?php if($item->getPhotoUrl("thumb.normal") != ""): echo $item->getPhotoUrl("thumb.normal"); else: echo 'application/modules/Groupbuy/externals/images/nophoto_deal_thumb_profile.png'; endif;?>" style = "max-width:200px;max-height:150px" /></a>
	                     	</div>

                        		<div id="body" style="padding-top:5px;" class="auction_list_description">
                          		<?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>350) echo "..."; ?>
                          	</div>
                          
                       		<div style="padding-top:5px;" >
                       			<span style="font-weight: bold;"> <?php echo $this->translate('Price: ');?></span> 
                       			<span id="price"><?php echo  $this->currencyadvgroup($item->price,$item->currency);?> </span>
                       			<?php // echo $item->currency ?>              				
                  			</div>

	                         <div style="padding-top:5px;" >
	                       		<span style="font-weight: bold;"> <?php echo $this->translate('VAT: ');?></span> <font color="red" style="font-weight: bold;" ><span id="price"><?php echo  $item->vat?> </span>%</font>
	                       	</div>

                       		<div style="padding-top:5px;" >
                       			<span style="font-weight: bold;"> <?php echo $this->translate('Final Price: ');?></span> 
                       			<span color="red" style="font-weight: bold;" >
                       				<span id="price"><?php echo  $this->currencyadvgroup($item->final_price,$item->currency);?> </span><?php //echo $item->currency ?>
             					</span>
                      		 </div>

						<div style="padding-top: 5px;">
							<span style="font-weight: bold;"><?php echo $this->translate('Quantity: ') ?></span><input onkeyup="numberChange(0)" onblur="numberChange(1)" type="text" id="number" value="1"/>
						</div>

						<div style="padding-top: 5px;">
							<span style="font-weight: bold;"><?php echo $this->translate('Total: ') ?></span>
							<font color="red" style="font-weight: bold;" ><?php echo $itcurrency->symbol;?>
								<span id="total"><?php echo $this->currencyadvgroup($item->final_price, $item->currency);?> </span> 
								<?php //echo $item->currency ?>
							</font>
						</div>

						<div style="padding-top: 5px;">
							<span style="font-weight: bold;"><?php echo $this->translate('Available Amount: ') ?></span>
							<font color="red" style="font-weight: bold;" ><?php //echo $itcurrency->symbol;?>
								<span id="current_amount"><?php echo $this->currencyadvgroup($this->current_amount, $this->currency);?> </span> <?php //echo $item->currency ?>
							</font>
						</div>

                       	 	<br/>
						
						<div class="yngroupbuy-deal-button-form">
						
	                        		<?php if($item->price > 0 && $item->status == 30 && $item->published == 20): ?>
		
		                    	<?php if ( !isset($_SESSION['buygift']['buygift']) || ($_SESSION['buygift']['buygift'] != 1) ):?>  


	                            	<button id="buygift" title="<?php echo $this->translate('Buy for Friend') ?>">
								<a  href="javascript:;" style="text-decoration: none;"> 
									<span style="display: block; float: left;">
										<?php echo $this->translate('Buy for Friend') ?> 
									</span>
								</a>
	                           	</button>

		                        <script type="text/javascript">
								$('buygift').addEvent('click', function(){
									var sb_url = '<?php echo $this->url(array('deal_id'=> $item->deal_id,'action'=>'buygift','numberbuy' => 'nbbuy'), 'groupbuy_general', true)?>';
									var str_sb_url = sb_url.replace('nbbuy', $('number').value);
										Smoothbox.open(str_sb_url);
								});
						  	</script>
	                              
	                                      
	                              
							<?php else: ?>
	                        
							<p class="icon_gift">
								<strong style = "height:30px;line-height: 30px;display:block;padding-left: 40px;background:url(application/modules/Groupbuy/externals/images/gift.png) no-repeat left center;">
								<?php echo $this->translate('Gift for: '); echo $_SESSION['buygift']['friend_name'];?>
								</strong>
							</p>
							<br>
							<div>
							<span>
							<?php // echo $this->htmlLink(array('route'=>'groupbuy_general','action'=>'editgift'), $this->translate('Edit'), array(
	       						//  'class'=>'smoothbox',
	       					//)) ?>

	            				<a id ="editgiftinfo" style ="color: #5F93B4; cursor:pointer" ><?php echo $this->translate('Edit');?></a>
											                        <script type="text/javascript">
								  $('editgiftinfo').addEvent('click', function(){
								    var sb_url = '<?php echo $this->url(array('action'=>'editgift','numberbuy' => 'nbbuy'), 'groupbuy_general', true)?>';
									var str_sb_url = sb_url.replace('nbbuy', $('number').value);
								    Smoothbox.open(str_sb_url);
								  });
								  </script>
								</span>
								|
								<span>
								<?php echo $this->htmlLink(array(
	                                  'action' => 'deletegift',
	                                  'route' => 'groupbuy_general',
	                                  ), $this->translate('Cancel'), array(
	                                  'class' => 'smoothbox',
	                                )) ?>
								</span>
								</div>
								<br>

								<?php endif;?>
                                    
		                           <?php $virtual = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.virtualmoney', 0);
		                           if (($item->method == '0' || $item->method == '-2' ) && $virtual == 1 && $this->current_amount >= $item->final_price && $item->currency == $this->currency ) : ?>   
		                         
		                              <form onsubmit="return check();" action="<?php echo selfURL() ?>group-buy/accountmoney/deal/<?php echo $item->deal_id  ?>" method="POST" name="cart_form">
		                               
		                                   <button  name="buydeal" id = "virtualmoney" type="submit" title="<?php echo $this->translate('Pay with account money');?>">
			                                    <a href="javascript:;" style="text-decoration: none;"> 
			                                   <span><?php echo $this->translate('Pay with money');?></span>
			                                  
			                                    </a>
		                                   </button>

		                                   <input TYPE="hidden" id="total_amount1" NAME="total_amount1" VALUE="<?php echo $item->final_price;?>"/> 
		                                   <input TYPE="hidden" id="number_buy1" NAME="number_buy1" VALUE="1"/>
		                                   <input TYPE="hidden" id="amount1" NAME="amount1" VALUE="<?php echo $item->final_price;?>"/>
		                              </form>
		                          
		                           <?php endif;?>
		                           
				                    <?php if ( ($item->method == '0' || $item->method == '-1' ) && (!isset($_SESSION['buygift']['buygift']) || ($_SESSION['buygift']['buygift'] != 1)) ) :?>
				                     <form onsubmit="return check();" action="<?php echo selfURL() ?>group-buy/delivery/deal/<?php echo $item->deal_id  ?>" method="POST" name="cart_form">
				                           <button class="icon_groupbuy_cod"  name="publish" id = "codbutton" type="submit" title="<?php echo $this->translate('Cash on Delivery');?>" >
	                                      <a  href="javascript:;" style="text-decoration: none;"> 
	                                        <span style="display: block; float: left;">
	                                        <?php echo $this->translate('Cash on Delivery');?>      
	                                        </span>
	                                        </a>
	                                       </button>
				                       <input TYPE="hidden" id="total_amount" NAME="total_amount" VALUE="<?php echo $item->final_price;?>"/> 
				                       <input TYPE="hidden" id="number_buy" NAME="number_buy" VALUE="1"/> 
				                       <input TYPE="hidden" id="amount" NAME="amount" VALUE="<?php echo $item->final_price;?>"/>
				                    </form>
				                    
				                   <?php endif;?>
	                    			
	                    			<?php foreach( $this->gateways as $gatewayInfo ):
							              $gateway = $gatewayInfo['gateway'];
							              $plugin = $gatewayInfo['plugin'];
							              ?>
							        <form class="for-yncredit"></form>
					                <?php if ($item->method == '0' || $item->method == $gateway->gateway_id ) : ?>
	                    			<form method="post" action="<?php echo $this->escape($this->url(array('action' => 'update-order'), 'groupbuy_general', true)) ?>"
							        class="global_form" enctype="application/x-www-form-urlencoded">
									    <div>
									      <div>
									        <div class="form-elements">
									          <div id="buttons-wrapper" class="form-wrapper">
									              <button class="adv_payment_gateway_btn" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
									                <?php echo $this->translate('Pay with')." ".$this->translate($gateway->title) ?>
									              </button>
									            <input type="hidden" name="id" value="<?php echo $item -> getIdentity()?>"/>
									            <input type="hidden" class="payment_quantity" name="quantity" value="1"/>
									            <input type="hidden" id="publish" name="publish" value="0"/>
									            <input type="hidden" name="gift_id" value="<?php echo $this->gift_id?>"/>
									          </div>
									        </div>
									      </div>
									    </div>
									  </form>
									  <?php endif;?>
							          <?php endforeach; ?> 
	                    

	                    		<?php endif; ?>
					
						</div>

                    	<br/>

                     	<div>
						<?php echo $this->htmlLink(array(
							'action' => '',
							'route' => 'groupbuy_general',
						), $this->translate('Back'), array(
							'style' => 'font-weight: bold; text-decoration: none;',
							'class'=>'icon_groupbuy_back'
						)) ?>
                       	</div>  

	                    <div id = "maxmsg"  style="display:none">
	                    	<br/><?php echo $this->translate('You have reached max bought allowed for this deal. You cannot buy this deal anymore!')?>
	                    </div>
                    </td> 
                </tr>
                </table>

			 </div>
			 <?php else: ?>
			<div class="tip" style="clear: inherit;">
			      <span>
			   <?php echo $this->translate('You can not buy this deal!');?>
			   </span>
			           <div style="clear: both;"></div>
			    </div>
			 <?php endif; ?>




<script type="text/javascript">
	window.addEvent('domready',function(){
		update(1);
		var buyff = '<?php echo $this->buyff;
		 					?>';
		var giftss = '<?php if (isset($_SESSION['buygift']) && ($_SESSION['buygift']['buygift'] == 1)) {
								echo "1"; 
							}
							else {
								echo "0";						 
							}?>';
		var numbuy = '<?php if (isset($_SESSION['buygift']) && ($_SESSION['buygift']['numberbuy'] != '')) {
							echo $_SESSION['buygift']['numberbuy']; 
						}
						else {
							echo "0";						 
						}?>';						

		var maxbought = '<?php echo $item->max_bought;?>';
		if (maxbought != 0) {
			var checkbought = '<?php echo $item->getMaxBought($this->viewer)?>';
			if  (checkbought == 0) {
				update(0);
				if ($('buygift')) {
					$('buygift').hide();
				}
				if ($('paypalbutton')) {
					$('paypalbutton').hide();
				}
				if ($('codbutton')) {
					$('codbutton').hide();
				}
				if ($('backbutton')) {
					$('backbutton').hide();
				}
	            if ($('virtualmoney')) {
	                $('virtualmoney').hide();
	            }
	            if ($('checkoutbutton')) {
	                $('checkoutbutton').hide();
	            }
				if (($('maxmsg')) && $('maxmsg').getStyle('display') == 'none') {
					$('maxmsg').show();
				}
			}
		}
		else {
			var checkbought = 1;
		}
		if (buyff == 1 && giftss == 0 && checkbought != 0) {
			 
			 var sb_url = '<?php echo $this->url(array('deal_id'=> $item->deal_id,'action'=>'buygift'), 'groupbuy_general', true)?>';
			    Smoothbox.open(sb_url);
			    
			 }
		else if (buyff == 2 && checkbought != 0) {
				var sb_url = '<?php echo $this->url(array('deal_id'=> $item->deal_id,'action'=>'buygift','method'=>'2'), 'groupbuy_general', true)?>';
			    Smoothbox.open(sb_url);
			}
		if (numbuy != 0) {
				$('number').value = numbuy;
				update(numbuy);
			}

	});
</script>
