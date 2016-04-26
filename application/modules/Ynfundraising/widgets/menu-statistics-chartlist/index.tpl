<div class="quicklinks" >
	<ul class="navigation ynfundraising_quicklinks_menu">
		<li>
			<?php echo $this->htmlLink(array('route'=>'ynfundraising_extended', 'controller'=>'campaign', 'action'=>'view-statistics-chart', 'campaign_id' => $this->campaign->campaign_id),$this->translate("Chart"))?>
		</li>
		<li>
			<?php echo $this->htmlLink(array('route'=>'ynfundraising_extended', 'controller'=>'campaign', 'action'=>'view-statistics-list', 'campaign_id' => $this->campaign->campaign_id),$this->translate("List"), array('class'=>'active'))?>
		</li>
	</ul>
</div>
