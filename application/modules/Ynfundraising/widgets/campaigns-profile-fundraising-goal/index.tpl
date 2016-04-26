<div class="ynFRaising_campaign_PriceColRight">		
	<div class="ynFRaising_FeaturePriceRaised">
		<div class="ynFRaising_FeaturePrice">
			<?php echo  $this->currencyfund($this->total_amount,$this->campaign->currency)?>
		</div>
		<div class="ynFRaising_FeatureRaisedOf">
			<?php echo $this->translate("Raised of %s Goal",$this->currencyfund($this->goal,$this->campaign->currency))?>
		</div>
	</div>
	<div class="ynfundraising-highligh-detail">
		<div class="meter-wrap-l">
			<div class="meter-wrap-r">
				<div class="meter-wrap">
					<div class="meter-value" style="width: <?php echo ($this->percent/100)*170?>px">
						<?php echo $this->percent."%"; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if($this->campaign->expiry_date && $this->campaign->expiry_date != "0000-00-00 00:00:00" && $this->campaign->expiry_date != "1970-01-01 00:00:00" && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS): ?>
		<div class="ynfundraising-time">
			<img src="" class="ynfundraising_timeClockIcon"/>
			<span class="ynfundraising_timeInner"><?php echo $this->campaign->getLimited();?></span>
		</div>		
	<?php endif;?>		
	
	<div class="ynfundraising-info">
		<?php echo $this->translate(array('%s donor','%s donors',$this->campaign->getTotalDonors()),$this->campaign->getTotalDonors() );
					echo " - ".$this->translate(array('%s like ','%s likes', $this->campaign->like_count), $this->campaign->like_count);
					echo " - ".$this->translate(array('%s view','%s views',$this->campaign->view_count),$this->campaign->view_count);
				?>
	</div>
	 <?php if($this->campaign->published == 1 && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
		<div class="ynfundraising-donate">
			<div id="sign_now">
				<?php
					echo $this->htmlLink(
						array('route' => 'ynfundraising_extended', 'controller' => 'donate', 'action' => 'index', 'campaign_id' => $this->campaign->getIdentity()),
						$this->translate('Donate'),
						array('class' => '')
				);
				?>
			</div>
		</div>
	<?php endif;?>
</div>
