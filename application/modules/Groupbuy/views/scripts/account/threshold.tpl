<!--  <?php
$currency = Engine_Api::_() -> groupbuy() -> getDefaultCurrency();
$viewer = Engine_Api::_()->user()->getViewer();
$commission= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $viewer, 'commission');

$this -> headScript() -> appendFile($this -> baseUrl() . '/application/modules/Groupbuy/externals/scripts/groupbuy_function.js');
?>
<form method="post" action="<?php echo $this->url(array('format'=>'smoothbox'))?>">
	<input type="hidden" name="cmd" value="request-ney" />
	<div id="request">
		<div class="table" style="padding:20px; width: 450px">
			<div class="groupbuy_table_left" style="font-weight: bold;">
				<?php echo $this -> translate("Amount");?>
			</div>
			<div class="table_right" style="margin-bottom: 10px;">
				<input type="text" name="currentmoney" id="txtrequest_money"/>
				<?php echo $currency ?>
				<br />
				<?php //echo $this->translate('Commission '),  $commission , '%'; ?>
			</div>
			<div class="groupbuy_table_left" style="font-weight: bold;">
				<?php echo $this -> translate("Reason");?>
			</div>
			<div class="groupbuy_table_right">
				<textarea  name="textarea_request" id="textarea_request" style="height:60px; width: 250px;"></textarea>
	</div>	</div>
		<div class="groupbuy_table_clear">
			<button  name="submit">
				<?php echo $this -> translate('Request money');?>
			</button>
		</div>
	</div>
</form> -->