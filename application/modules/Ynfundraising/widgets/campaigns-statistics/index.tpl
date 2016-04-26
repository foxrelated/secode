<ul class = "global_form_box">
	<li>
		<span> <?php echo $this->translate('On Going:');?> </span>
		<div>
			<?php echo $this->translate(array('%s campaign','%s campaigns',Engine_Api::_() -> ynfundraising() -> getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS)),$this->locale()->toNumber(Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS)));?>			
		</div>
	</li>
	<li>
		<span> <?php echo $this->translate('Reached goal:');?> </span>
		<div>
			<?php echo $this->translate(array('%s campaign','%s campaigns',Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_REACHED_STATUS)),$this->locale()->toNumber(Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_REACHED_STATUS)));?>		
		</div>
	</li>
	<li>
		<span> <?php echo $this->translate('Expired:');?> </span>
		<div>
			<?php echo $this->translate(array('%s campaign','%s campaigns',Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_EXPIRED_STATUS)),$this->locale()->toNumber(Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_EXPIRED_STATUS)));?>		
		</div>
	</li>	
	<li>
		<span> <?php echo $this->translate('Closed:');?> </span>
		<div>
			<?php echo $this->translate(array('%s campaign','%s campaigns',Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_CLOSED_STATUS)),$this->locale()->toNumber(Engine_Api::_() -> ynfundraising() ->getTotalCampaigns(Ynfundraising_Plugin_Constants::CAMPAIGN_CLOSED_STATUS)));?>		
		</div>
	</li>	
</ul>