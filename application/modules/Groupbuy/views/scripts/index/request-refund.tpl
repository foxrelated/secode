<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Groupbuy/externals/scripts/groupbuy_function.js');
 ?>
       <?php
  if (!$this->canRefund):?>
<div class="tip" style="clear: inherit;">
      <span>
<?php echo $this->translate('You do not have any finance account yet. '); echo $this->translate('You should add your account to request refund.'); ?>
 </span>
           <div style="clear: both;"></div>
    </div>
 <?php else: ?>
<div id="request">
<div class="table" style="padding:20px; width: 450px">
 <input type="hidden" name="txtrequest_money" id="txtrequest_money" value="<?php echo $this->total_price ?>"/>
 <input type="hidden" name="txtrequest_buy" id="txtrequest_buy" value="<?php echo $this->buy_id ?>"/>
<div class="groupbuy_table_left" style="font-weight: bold;"><?php echo $this->translate("Reason"); ?></div>
<div class="groupbuy_table_right"><textarea  name="textarea_request" id="textarea_request" style="height:60px; width: 250px;"></textarea></div>
</div>
<div class="groupbuy_table_clear">
<button  name="submit"  onclick="requestRefund()" ><?php echo $this->translate('Request refund'); ?></button>
</div>
</div>
<?php endif;?>