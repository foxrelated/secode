<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfundraising
 * @author     YouNet Company
 */
?>

<?php
/*
 * Display campaign information
 */
?>
<?php
	echo $this->partial ( '_menu.tpl', array () );
?>

<ul class="ynfundraising_campaigns_browse">
	<li>
		<div class='ynfundraising_campaigns_browse_photo'>
            <?php echo $this->htmlLink($this->campaign->getHref(), $this->itemPhoto($this->campaign, 'thumb.profile'))?>
     	</div>
		<div class='ynfundraising_campaigns_browse_info'>
			<div class='ynfundraising_campaigns_browse_info_title'>
				<b><?php echo $this->htmlLink($this->campaign->getHref(), $this->campaign->getTitle()) ?></b>
				- <font class="ynfundraising_campaigns_browse_info_status_">
                   <?php echo $this->translate(ucfirst($this->campaign->status));?>
                </font>
			</div>
			<div class=''>
				<?php echo $this->campaign->short_description?>
            </div>
		</div>
	</li>
</ul>

<?php
$viewer = Engine_Api::_ ()->user ()->getViewer ();

?>
<div class="setting">
	<form class="global_form" action="<?php echo $this->formUrl?>" class="global_form">
		<div>
			<div>
				<h3><?php echo $this->translate('Summary Donation Information')?></h3>
				<div class="form-elements">
						<?php if ($viewer->getIdentity() > 0  || $this->donation->guest_name != '') :?>
						<div class="form-wrapper">
							<div class="form-label">
								<label><?php echo $this->translate("Full Name")?></label>
							</div>
							<div class="form-element">
							<?php
							if ($viewer->getIdentity() > 0) {
								echo ($viewer->displayname != '')?$viewer->displayname:$viewer->username;
							}
							else if ($this->donation->guest_name != '') {
							 	echo  $this->donation->guest_name;
							 }
							?>
							</div>
						</div>
						<?php endif; ?>

						<?php if ($viewer->getIdentity() > 0  || $this->donation->guest_email != '') :?>
						<div class="form-wrapper">
							<div class="form-label">
								<label><?php echo $this->translate("Email")?></label>
							</div>
							<div class="form-element">
							<?php
							if ($viewer->getIdentity() > 0) {
								echo $viewer->email;
							}
							else if ($this->donation->guest_email != '') {
							 	echo  $this->donation->guest_email;
							 }
							?>
							</div>
						</div>
						<?php endif; ?>

					<div class="form-wrapper">
						<div class="form-label">
							<label><?php echo $this->translate("Amount")?></label>
						</div>
						<div class="form-element"><?php echo $this->currencyfund($this->configRequest['amount'], $this->campaign->currency)?></div>
					</div>

					<input type="hidden" name="no_shipping" value="1"/>
					<input TYPE="hidden" NAME="cmd" VALUE="_xclick">
					<input TYPE="hidden" NAME="item_name" VALUE=" <?php echo $this->configRequest['item_name'];?>">
					<input TYPE="hidden" NAME="business" VALUE=" <?php echo $this->configRequest['account_username'];?>">
					<input TYPE="hidden" NAME="amount" VALUE="<?php echo $this->configRequest['amount'];?>">
					<input TYPE="hidden" NAME="currency_code" VALUE="<?php echo $this->configRequest['currency'];?>">
					<input type="hidden" name="notify_url" value="<?php echo $this->configRequest['notify_url'];?>"/>
					<input type="hidden" name="return" value="<?php echo $this->configRequest['return_url'] ?>"/>
					<input type="hidden" name="cancel_return" value="<?php echo $this->configRequest['cancel_url'] ?>"/>
					<input type="hidden" name="rm" value="1" />

					<div id="donate-submit_wrapper" class="form-wrapper">
						<div class="form-label"></div>
						<div class="form-element">
							<button type="submit">
								<?php echo $this->translate("Donate")?>
							</button>
							<?php
								echo $this->htmlLink(array(
									'route' => 'ynfundraising_extended',
									'controller' => 'donate',
									'action' => 'edit',
									'donation_id' => $this->donation->getIdentity(),
									'campaign_id' => $this->campaign->getIdentity(),
									'is_agreed' => $this->is_agreed,
								), $this->translate('Edit'),
								array('id' => 'donate-edit_wrapper'));
							?>
							<span>
								<?php
									echo $this->translate(' or ');
									echo $this->htmlLink($this->configRequest['return_url'], $this->translate('cancel'));
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>