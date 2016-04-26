<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu')
?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">

<div class="profile_fields">
<h4><span><?php echo $this->translate('Account');?></span></h4>
	<ul>
		<?php		
		$account = $this->store->getAccount();
		if(is_object($account)):
		?>
		<li>
			<span><?php echo $this->translate("Gateway") ?></span>
			<span><?php echo ucfirst($account->gateway_id) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Display Name") ?></span>
			<span><?php echo $account->name ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Email Address") ?></span>
			<span><?php echo $account->account_username ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Last Modified") ?></span>
			<span><?php echo $this->timestamp($account->modified_date) ?></span>
		</li>
		<li>
			<a href="<?php echo $this->url(array('action'=>'configure')) ?>"><?php echo $this->translate("Edit Account");?></a>
		</li>
		<?php else: ?>
		<li>
			<div class="tip">
				<span>
					<?php echo $this->translate("Please click <a href='%s'>here</a> configure your account at first.", $this->url(array('action'=>'configure')))?>
				</span>
			</div>
		</li>
		<?php endif; ?>
	</ul>
</div>
<div class="profile_fields">
<h4><span><?php echo $this->translate('Information');?></span></h4>
	<ul>
		<li>
			<span><?php echo $this->translate("Sold Amount") ?></span>
			<span><?php echo $this->currency($this->store->getTotalAmount(), $this->currency) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Commission") ?></span>
			<span><?php echo $this->currency($this->store->getCommissionAmount(), $this->currency) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Remain Amount") ?></span>
			<span><?php echo $this->currency($this->store->getRemainAmount(), $this->currency) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Received Amount") ?></span>
			<span><?php echo $this->currency($this->store->getReceivedAmount(), $this->currency) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Waiting Amount")?></span>
			<span><?php echo $this->currency($this->store->getWaitingAmount(), $this->currency) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Pending Amount")?></span>
			<span><?php echo $this->currency($this->store->getPendingAmount(), $this->currency) ?></span>
		</li>
		<li>
			<span><?php echo $this->translate("Available Amount") ?></span>
			<span><?php echo $this->currency($this->store->getAvailableAmount(), $this->currency) ?></span>
		</li>
		 <?php // if($this->store->ownerCanRequest()): ?>
		<li>
			<a href="<?php echo $this->url(array('action'=>'send-request')) ?>"><?php echo $this->translate('Request Money');?></a>			
		</li>
		<?php // endif; ?>
	</ul>
</div>
<?php if(count($this->recentRequests)): ?>
<div class="profile_fields">
	<h4><span><?php echo $this->translate("Recent Requests")?></span></h4>
	<br />
	<table class="admin_table">
		<thead>
			<tr>
				<th style = "text-align: right;">
					<?php echo $this->translate("Amount") ?>
				</th>
				<th>
					<?php echo $this->translate("Status") ?>
				</th>
				<th>
					<?php echo $this->translate("Request Date") ?>
				</th>
				<th>
					<?php echo $this->translate("Request Message") ?>
				</th>
				<th>
					<?php echo $this->translate("Response Date") ?>
				</th>
				<th>
					<?php echo $this->translate("Response Message") ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->recentRequests as $item): ?>
			<tr>
				<td style = "text-align: right;">
					<?php echo $this->currency($item->request_amount) ?>
				</td>
				<td>
					<?php echo $this->translate(ucfirst($item->request_status)) ?>
				</td>
				<td>
					<?php echo $item->request_date ?>
				</td>
				<td>
					<?php echo $item->request_message ?>
				</td>
				<td>
					<?php if($item->response_date) : ?>
						<?php echo $item->response_date ?>
					<?php else: ?>
						#
					<?php endif; ?>
				</td>
				<td>
					<?php if($item->response_message) : ?>
						<?php echo $item->response_message?>
					<?php else: ?>
						#
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php endif; ?>

</div>
<style type="text/css">
	 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}
</style>