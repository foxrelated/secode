<div class="ynFRaising_campaign_PriceIColRight ynFRaising_subProperty">	
	<?php echo $this->htmlLink($this->campaign->getHref(), $this->string()->truncate($this->campaign->getTitle(), 25), array('title' => $this->string()->stripTags($this->campaign->getTitle()), 'class' => 'ynFRaising_campaignTitle')) ?>	
	<p class="ynFRaising_ownerStat">
		<?php echo $this->translate("Created by %s",$this->campaign->getOwner());?>
	</p>	
	<p class="ynFRaising_ownerStat ynFRaising_statictis">
			<?php echo $this->translate(array('%s donor','%s donors',$this->campaign->getTotalDonors()),$this->campaign->getTotalDonors() );
					echo " - ".$this->translate(array('%s like ','%s likes', $this->campaign->like_count), $this->campaign->like_count);
					echo " - ".$this->translate(array('%s view','%s views',$this->campaign->view_count),$this->campaign->view_count);
				?>
	</p>	
	
	<div class ='ynFRaising_campaign_photoColRight'>
	  <a href="<?php echo $this->campaign->getHref()?>"><?php echo $this->itemPhoto($this->campaign, 'thumb.profile') ?></a>
	</div>	
	
	<div class="ynFRaising_CampParentPriceRaised">
		<div class="ynFRaising_FeatureRaisedOf">
			<?php echo $this->translate("%1s Raised of %2s Goal", $this->currencyfund($this->total_amount?$this->total_amount:'0',$this->campaign->currency),$this->currencyfund($this->goal,$this->campaign->currency))?>
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
 
   <?php if($this->campaign->expiry_date && $this->campaign->expiry_date != "0000-00-00 00:00:00" && $this->campaign->expiry_date != "1970-01-01 00:00:00"):?>
		<div class="ynfundraising-time">
			<img src="" class="ynfundraising_timeClockIcon"/>
			<span class="ynfundraising_timeInner"><?php echo $this->campaign->getLimited();?></span>
		</div>
    <?php endif;?>
	
	<?php if(count($this->donors)):?>
		<div class="ynfundraising_donors">
			<div class="ynFRaising_thumbavatarDonors">
				<?php foreach( $this->donors as $donor ): 
					$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id )?>
					<?php if(Engine_Api::_()->getApi('core', 'ynfundraising')->getLatestAnonymous($donor->user_id, $this->campaign->campaign_id)->is_anonymous == 0):?>
						<span>
						  <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle())) ?>
						</span>
					<?php endif;?>
				 <?php endforeach; ?>
			</div>
		</div>
	<?php endif;?>
	
	<p class="ynfundraising_campaign_description">
		<?php echo $this->campaign->short_description;?>
	</p>
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
