<?php
	 $total_amount =  $this->campaign->total_amount?$this->campaign->total_amount:'0';
	 $status = $this->campaign->status; ?>
<div class="ynfundraising_campaign_widget">
	<a class="thumbs_photo" href="<?php echo $this->campaign->getHref()?>">
		<span style="background-image: url(<?php echo $this->campaign->getPhotoUrl('thumb.normal'); ?>);"></span>


		<?php if($this->campaign->is_featured == 1 && $status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
		<div class="ynFRaising_featuredLabelWrapper">
			<div class="ynFRaising_featuredLabel"> <?php echo $this->translate("Featured Campaign");	?> </div>
			<div class="ynFRaising_featuredLabelCorner">
				<div class="ynFRaising_featuredLabelLeftCorner"> </div>
				<div class="ynFRaising_featuredLabelRightCorner"> </div>
			</div>
		</div>
		<?php elseif($status != Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
			<div class="ynFRaising_featuredLabelWrapper <?php echo $status?>">
				<div class="ynFRaising_featuredLabel"> <?php 
						if($status == 'closed'){
							echo $this->translate("Closed Campaign");
						}
						else{
							$status = $this->translate(ucfirst($status));	
							echo $this->translate("%s Campaign",$status);
						}
						?>  
				</div>
				<div class="ynFRaising_featuredLabelCorner">
					<div class="ynFRaising_featuredLabelLeftCorner"> </div>
					<div class="ynFRaising_featuredLabelRightCorner"> </div>
				</div>
			</div>
		<?php endif;?>
	</a>
	<div class="ynFRaising_raised ynFRaising_raisedTime">
		<div class="ynFRaising_title"><?php echo $this->translate("Raised")?></div>
		<div class="ynFRaising_raisedDesc"><?php echo $this->currencyfund($total_amount,$this->campaign->currency)?></div>
	</div>
	<div class ="ynfundraising_campaign_limited ynFRaising_raisedTime">
		<div class="ynFRaising_title"><?php echo $this->translate("Time Left")?></div>
		<div class="ynFRaising_raisedDesc">
			<?php
			if($status != Ynfundraising_Plugin_Constants::CAMPAIGN_EXPIRED_STATUS) {
				if ($this->campaign->expiry_date != '0000-00-00 00:00:00' && $this->campaign->expiry_date != '1970-01-01 00:00:00') {
					echo $this->campaign->getLimited()?$this->campaign->getLimited():$this->translate("Expired");
				}
				else {
					echo $this->translate("Unlimited");
				}
			}
			else {
				echo $this->translate("Expired");
			}
			?></div>
	</div>
	<p class="ynfundraising_campaignInfo thumbs_info">
		<span class="thumbs_title">
			<?php echo $this->htmlLink($this->campaign->getHref(), $this->string()->truncate($this->campaign->getTitle(), 50), array('title' => $this->string()->stripTags($this->campaign->getTitle()))) ?>
		</span>
		<?php
		$url = "<a href='".$this->campaign->getOwner()->getHref()."'>".$this->string()->truncate(strip_tags($this->campaign->getOwner()->getTitle()) ,12)."</a>";
		 echo $this->translate("Created by %s",$url);?><br/>
	</p>
</div>