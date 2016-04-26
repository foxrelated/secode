<?php
$viewer = Engine_Api::_()->user()->getViewer();
$commission = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $viewer, 'commission');
if ($commission == "") {
   $mtable = Engine_Api::_()->getDbtable('permissions', 'authorization');
   $maselect = $mtable->select()
           ->where("type = 'groupbuy_deal'")
           ->where("level_id = ?", $viewer->level_id)
           ->where("name = 'commission'");
   $mallow_a = $mtable->fetchRow($maselect);
   if (!empty($mallow_a))
      $commission = $mallow_a['value'];
   else
      $commission = 0;
}
$this->headScript()
        ->appendFile($this->baseUrl() . '/application/modules/Groupbuy/externals/scripts/groupbuy_function.js');
?>
<div class="headline">
   <h2>
      <?php echo $this->translate('GroupBuy'); ?>
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
$account = Groupbuy_Api_Cart::getFinanceAccount($viewer->getIdentity(), 2);
if ($account['currency']):
   $currency = $account['currency'];
else:
   $currency = Engine_Api::_()->groupbuy()->getDefaultCurrency();
endif;
//$virtual = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.virtualmoney', 0);
if (!$account):
   ?>
   <div class="tip" style="clear: inherit;">
      <span>
   <?php echo $this->translate('You do not have any finance account yet. '); ?>
   <a href="<?php echo $this->url(array('action'=>'create'),'groupbuy_account');  ?>"><?php echo $this->translate('Click here'); ?></a> <?php echo $this->translate('  to add your account.'); ?>
      </span>
      <div style="clear: both;"></div>
   </div>
   <?php else: ?>
   <img src='./application/modules/Groupbuy/externals/images/account.jpg' width="48px" height="48px" border='0' class='groupbuy_icon_big' style="margin-bottom: 15px;">
   <div class='groupbuy_page_header'><?php echo $this->translate('My Account'); ?></div>
   <div>
   <?php echo $this->translate('Seller Account Management.'); ?><span><a href="<?php echo $this->url(array(),'groupbuy_mytransaction', true); ?>"> <?php echo $this->translate('View my transaction history'); ?></a></span><br />
   </div>
   <?php $info_user = $this->info_user; ?>
   <div class="space-line"></div>
   <div class="container row">
        <div class="col-sm-6 panel-body">         
          <form method="post" action="<?php echo $this->url(array('action'=>'edit'),'groupbuy_account'); ?>">
             <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr style="background:#E9F4FA none repeat scroll 0 0;">
                   <th align="center" style="padding:5px; color:#717171;"><?php echo $this->translate('User Information'); ?></th>
                </tr>
                <tr>
                   <td class="ynaffiliate_account" >
                      <span class="ynaffiliate_account_bold"><?php echo $this->translate('Username'); ?></span>: <?php echo $info_user['username'] ?>
                   </td>
                </tr>
                <tr>
                   <td class="ynaffiliate_account">
                      <span class="ynaffiliate_account_bold"><?php echo $this->translate('Full name'); ?></span>: <?php echo $info_user['displayname'] ?>
                   </td>
                </tr>
                <tr>
                   <td class="ynaffiliate_account">
                      <span class="ynaffiliate_account_bold"><?php echo $this->translate('Email'); ?></span>: <?php echo $info_user['email'] ?>
                   </td>
                </tr>
                <tr>
                   <td class="ynaffiliate_account">
                      <span class="ynaffiliate_account_bold"><?php echo $this->translate('Status'); ?></span>: 
						<?php 
						if ($info_user['status'] != ''){
							echo strip_tags($info_user['status']);
						}else{
							echo $this->translate('Not update');
						} ?>
                   </td>
                </tr>
<?php if ($account):
if ($this->info_account['payment_type'] != 1): ?> 
                      <tr>
                         <td align="right" class="ynaffiliate_account">
                            <div class="p_4">
                               <button type="submit" name="editperionalinfo"><?php echo $this->translate('Edit account'); ?> </button>
                            </div>
                         </td>
                      </tr>
<?php endif; ?>     
<?php endif; ?>
             </table>
          </form>
           <div align="left" class = "groupbuy_message" style="">
              <a href="javascript:loadMessageFromRequest(<?php echo $this->info_user['user_id'] ?>,'<?php echo $this->url(array(),'default'); ?>')"><?php echo $this->translate('Click here to view message from admin with your request(s)'); ?></a>
           </div>
           <div align="left"  id="message_request_<?php echo $this->info_user['user_id'] ?>">
           </div>
        </div>
        <div class="col-sm-6 panel-body">
          <table cellpadding="0" cellspacing="0" border="0" width="100%">
             <tr style="background:#E9F4FA none repeat scroll 0 0;">
                <th align="center" style="padding:5px; color:#717171;"><?php echo $this->translate('Summary'); ?> </th>
             </tr>
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Seller Account'); ?></span>:
                   <?php if ($this->info_account): ?>
<?php
echo $this->htmlLink(
      $this->url(array('action' => 'transaction'), 'groupbuy_general'), $this->info_account["account_username"], array('class' => ''));
?>   
<?php endif; ?>
                </td>
             </tr>
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Available Amount'); ?></span>: <span id="current_money_money" style="color: red;font-weight: bold;"><?php echo $this->currencyadvgroup($this->current_amount, $currency); ?></span>  <?php ?>
                </td>
             </tr>
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Current Request'); ?></span>: <span id="current_request_money" style="color: blue;font-weight: bold;">
                      <?php echo $this->currencyadvgroup($this->requested_amount, $currency); ?></span> <?php ?>
                </td>
             </tr>
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Currency'); ?></span>: <span id="current_request_money" style="color: blue;font-weight: bold;">
<?php echo $currency; ?></span> <?php ?>
                </td>
             </tr>
             <tr>
                <td align="right" class="ynaffiliate_account">
                   <div class="p_4">
                      <div style="float:left; padding-right: 10px;">
                         <?php if ($this->allow_request == 0):
                            if ($this->info_account['payment_type'] != 1): ?>  
                               <a class="smoothbox" href="<?php echo $this->url(array('user_id' => '1'), 'groupbuy_payment_threshold') ?>" title="<?php echo $this->translate('Request'); ?>" ><button  name="request"><?php echo $this->translate('Request'); ?></button></a>
                            <?php endif; ?>
                         <?php else: ?>
                            <?php if ($this->info_account['payment_type'] != 1): ?>  
                               <a class="smoothbox" href="<?php echo $this->url(array('user_id' => '1'), 'groupbuy_payment_threshold') ?>" title="<?php echo $this->translate('Request'); ?>" ><button  name="request"><?php echo $this->translate('Request'); ?></button></a>
                         <?php else: ?>
                            <?php echo $this->translate("You're admin.You cannot request money"); ?>
                         <?php endif; ?>
<?php endif; ?>
                      </div>    
<?php if (!$this->info_account): ?>
                         <a title="Add account" onclick="window.location.href=this.href;" href="<?php echo $this->url(array('action'=>'create'),'groupbuy_account'); ?>"  > <button name="addaccount"><?php echo $this->translate('Add account'); ?></button></a> 
<?php endif; ?>
                   </div>
                </td>
             </tr>
          </table>
          <div align="left" style="">
             <a id = "show_detail" href="javascript:showDetail()"><?php echo $this->translate('View details'); ?></a>
          </div>
          <script type="text/javascript">
             function showDetail() {
                if ($('requestdetail').getStyle('display') == "none") 
                {
                   $('requestdetail').show();
                   $('show_detail').innerHTML = '<?php echo $this->translate('Hide details'); ?>';
                }
                else if  ($('requestdetail').getStyle('display') == "block") 
                {
                   $('requestdetail').hide();
                   $('show_detail').innerHTML = "<?php echo $this->translate('View details'); ?>";
                }
             }
          </script>
          <table id="requestdetail" cellpadding="0" cellspacing="0" border="0" style="display: none">
             <tr style="background:#E9F4FA none repeat scroll 0 0;">
                <th align="center" style="padding:5px; color:#717171; width: 100%;"><?php echo $this->translate('Details'); ?> </th>
             </tr>
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Total Amount'); ?></span>: <span id="current_money" style="color: red;font-weight: bold;" > <?php echo $this->currencyadvgroup($this->info_account['total_amount'], $currency) ?> </span> 
                </td>
             </tr>
          
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Commission Fee'); ?></span>: <span id="" style="color: red;font-weight: bold;" > <?php echo round($commission, 2); ?>% </span> 
                </td>
             </tr>
       
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Minimum to request'); ?></span>: <span style="color: red;font-weight: bold;"> <?php echo $this->currencyadvgroup($this->min_payout, $currency); ?>  <?php //echo $currency  ?>  </span>
                </td>
             </tr>
             <tr>
                <td class="ynaffiliate_account" align="left">
                   <span class="ynaffiliate_account_bold"><?php echo $this->translate('Maximum to request'); ?></span>: <span style="color: red;font-weight: bold;"> <?php if ($this->max_payout == -1): echo $this->translate('Unlimited');
else: echo $this->currencyadvgroup($this->max_payout, $currency);
endif; ?></span>
                </td>
             </tr>
          </table>
        </div>
   </div>
<?php endif; ?>
