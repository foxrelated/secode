<div>
	<?php echo $this->owner;?>
	<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.profile'), array('class'=>'ynFRaising_campaignOwner')); ?>
	 <div class="ynFRaising_allCamp ynFRaising_campaignRate"><?php echo $this->translate("All Campaign (%s)",$this->totalCampaigns)?></div> 
	 <div class="ynFRaising_campaignRate">
	        <?php for($i = 1; $i <= 5; $i++): ?>
	          <img id="user_rate_<?php print $i;?>" src="application/modules/Ynfundraising/externals/images/<?php if ($i <= $this->avgrating): ?>star_full.png<?php elseif( $i > $this->avgrating &&  ($i-1) <  $this->avgrating): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
	        <?php endfor; ?>
	  </div>
	 <div class="ynFRaising_campaignRate"><?php echo $this->translate(array("(%s rate)","(%s rates)",$this->totalRates),$this->totalRates)?></div>
</div>
