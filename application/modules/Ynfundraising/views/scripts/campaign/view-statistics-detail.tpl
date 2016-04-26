<!--
<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>

<?php echo $this->htmlLink(array('route'=>'ynfundraising_extended', 'controller'=>'campaign', 'action'=>'view-statistics-list', 'campaign_id' => $this->campaign->campaign_id),$this->translate("Back"),array('class'=>'buttonlink ynFRaising_icon_back'))?>
-->
<ul class="global_form_popup">
<h3><?php echo $this->translate("Donation Details")?></h3>
<?php if (count($this->transactions) > 0) : ?>
<?php foreach($this->transactions as $item):?>

<h4><span><?php echo $this->translate('Order details')?></span></h4>

<div class="ynFRaising_DetailWrapper">
	<div class="ynFRaising_DetailLabel"><?php echo $this->translate("Donor")?></div>
	<div class="ynFRaising_DetailContent">
	<?php
		// owner
		if ($item->user_id > 0) {
			$user = Engine_Api::_ ()->getItem ( 'user', $item->user_id );
			echo $this->htmlLink ( $user->getHref (), $user->getTitle (), array('target' => '_blank') );
		} else {
			if ($item->guest_name) {
				echo $item->guest_name;
			} else {
				echo $this->translate ( 'Anonymous' );
			}
		}

		?>
	</div>
</div>
<div class="ynFRaising_DetailWrapper">
	<div class="ynFRaising_DetailLabel"><?php echo $this->translate("Campaign Name")?></div>
	<div class="ynFRaising_DetailContent">
		<?php
		$campaign = Engine_Api::_ ()->getItem ( 'ynfundraising_campaign', $item->campaign_id );
		echo $this->htmlLink ( $campaign->getHref (), $campaign->getTitle (), array('target' => '_blank') );
		?>
	</div>
</div>

<div class="ynFRaising_DetailWrapper">
	<div class="ynFRaising_DetailLabel"> <?php echo $this->translate ( 'Donation Date'); ?>
	</div>
	<div class="ynFRaising_DetailContent"> <?php echo $this->locale ()->toDateTime ( $item->donation_date ) ?></div>
</div>
<div class="ynFRaising_DetailWrapper">
	<div class="ynFRaising_DetailLabel"><?php echo $this->translate ( 'Description'); ?>
	</div>
	<div class="ynFRaising_DetailContent"><?php echo $campaign->short_description ?></div>
</div>

<h4 class="ynFRaising_DetailH4"><span><?php echo $this->translate('Payment Details')?></span></h4>

<div class="ynFRaising_DetailWrapper">
	<div class="ynFRaising_DetailLabel"> <?php echo $this->translate ( 'Donation Amount'); ?></div>
	<div class="ynFRaising_DetailContent"> <?php echo $this->currencyfund( $item->amount, $item->currency )?></div>
</div>
<div class="ynFRaising_DetailWrapper">
	<div class="ynFRaising_DetailLabel"> <?php echo $this->translate ( 'Transaction ID'); ?>
	</div>
	<div class="ynFRaising_DetailContent"><?php echo $item->transaction_id ?>	</div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</ul>