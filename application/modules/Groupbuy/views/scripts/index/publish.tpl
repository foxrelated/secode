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
 	$item = $this->deal;      
?> 
 <?php 
 $viewer = Engine_Api::_()->user()->getViewer(); 
 $account = Groupbuy_Api_Cart::getFinanceAccount($viewer->getIdentity(),2);
 if($account['account_username'] == ''): ?>
    <div class="tip" style="clear: inherit;">
      <span>
      <?php echo $this->translate('You can not publish this deal. '); ?>
      <a href="<?php echo $this->url(array('action'=> 'edit'),'groupbuy_account'); ?>">
      <?php echo $this->translate('Click here'); ?></a> <?php echo $this->translate('  to add seller account.'); ?>
    </span>
           <div style="clear: both;"></div>
 </div>
<?php elseif($this->canSell == true):?>
  <h2>
    <?php echo $this->translate('Deal information');?>
  </h2>
<div class="table">    
                <table width="100%">
                      <tr>
                          <td valign='top' width='1' class="nomobile" style=' text-align: center; padding-top:6px;  padding-bottom:6px; text-align: center;'>
                           <a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>"><img src="<?php if($item->getPhotoUrl("thumb.normal") != ""): echo $item->getPhotoUrl("thumb.normal"); else: echo 'application/modules/Groupbuy/externals/images/nophoto_deal_thumb_profile.png'; endif;?>" style = "max-width:200px;max-height:150px" /></a>
                        </td>
                      <td valign='top' class="contentbox" style="width: auto; padding-left: 20px;">
                      <strong id="title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </strong>
                         <div class="visible-xs">
                            <a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>"><img src="<?php if($item->getPhotoUrl("thumb.normal") != ""): echo $item->getPhotoUrl("thumb.normal"); else: echo 'application/modules/Groupbuy/externals/images/nophoto_deal_thumb_profile.png'; endif;?>" style = "max-width:200px;max-height:150px" /></a>
                          </div>
                         <div id="body" style="padding-top:5px;" class="auction_list_description">
                          <?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>350) echo "..."; ?>
                          </div>
                          <?php 
                          $user = Engine_Api::_()->user()->getViewer();
                          $freeF = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $user, 'free_fee');
                           $freeP = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $user, 'free_display');
                           if($freeP == 1 && $freeF == 1):
                              $item->total_fee = 0;  
                           endif;
                          ?> 
                         <?php if($item->total_fee > 0): ?>
                       <div style="padding-top:5px;" >
                       <span style="font-weight: bold;"> <?php echo $this->translate('Total fee: ');?></span> <font color="red" style="font-weight: bold;"><?php echo $this->currencyadvgroup($item->total_fee,Engine_Api::_()->groupbuy()->getDefaultCurrency());?> <?php //echo $item->currency ?></font>
                       </div>
                      
                       <?php endif; ?>
                        <div style="padding-top: 5px;">
                       <span style="font-weight: bold;"><?php echo $this->translate('Available Amount: ') ?></span><font color="red" style="font-weight: bold;" ><?php //echo $itcurrency->symbol;?><span id="current_amount"><?php echo $this->currencyadvgroup($this->current_amount, $item->currency);?> </span> <?php //echo $item->currency ?></font>
                       </div>
                        <br/>


                      <div class="yngroupbuy-deal-button-form">

                        <?php if($item->total_fee > 0): ?>
                         <?php $virtual = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.virtualmoney', 0);
                         $currency = Engine_Api::_()->groupbuy()->getDefaultCurrency();  
                            if ($virtual == 1 && $this->current_amount >= $item->total_fee && $currency == $this->currency) : ?>   
                                 
                                <form onsubmit="return check();" action="<?php echo $this->url(array('action'=>'publishmoney', 'deal' => $item->deal_id),'groupbuy_general') ?>" method="POST" name="cart_form">
                                   <button  name="buydeal" id = "virtualmoney" type="submit" title="<?php echo $this->translate('Pay with account money');?>" >
                                     <a href="javascript:;" style="text-decoration: none;"> 
                                           <span style="display: block; float: left;"><?php echo $this->translate('Pay with money');?></span>
                                     </a>
                                   </button>
                                   <input TYPE="hidden" id="amount1" NAME="amount1" VALUE="<?php echo $item->total_fee;?>"/> 
                               </form>
                                  
                           <?php endif;?>
                        <form class="for-yncredit"></form>
                        <?php foreach( $this->gateways as $gatewayInfo ):
			              $gateway = $gatewayInfo['gateway'];
			              $plugin = $gatewayInfo['plugin'];
			              ?>
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
						          </div>
						        </div>
						      </div>
						    </div>
						  </form> 
					  <?php endforeach; ?> 
                    <?php else: ?>
                    <form action="<?php echo $this->url(array('action'=>'publish-free', 'deal' => $item->deal_id),"groupbuy_general") ?>" method="POST" name="cart_form">
                    <button class="p_4" style="width: 127px; text-decoration: none; margin-left: -8px" title="<?php echo $this->translate('Publish free');?>">
                           <span class="icon_groupbuy_publish" name="publish" type="submit" style="float: left; height: 26px; display: block; margin-top: -3px" title="<?php echo $this->translate('Publish free');?>" > </span>
                           <a style="text-decoration: none;" href="<?php echo $this->url(array('action'=>'publish-free', 'deal' => $item->deal_id),"groupbuy_general") ?>" >
                           <?php echo $this->translate('Publish free');?>
                           </a>  
                           </button>
                    </form>
                    <?php endif; ?>
                    
                  </div>

                    </br>
                     <div style="margin-top: 7px; *margin-left: -5px; margin-top: 5px; line-height: 25px;">
                           <?php echo $this->htmlLink(array(
                              'action' => 'manage-selling',
                                'route' => 'groupbuy_general',
                            ), $this->translate('Cancel'), array(
                              'style' => 'font-weight: bold; text-decoration: none;',
                              'class'=>'icon_groupbuy_back'
                            )) ?>
                            </div>
                    </td>           
                </tr>
                </table>
 </div>
 <?php else: ?>
 <div class="tip" style="clear: inherit;">
      <span>
<?php  echo $this->translate('You can not publish this deal!');?>
 </span>
           <div style="clear: both;"></div>
    </div>
 <?php endif; ?>