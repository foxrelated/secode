<div class="ynFRaising_PromoteWrapper">
	<div class="ynFRaising_donateCode">
		<h3><?php echo $this->translate("Donate Box Code")?></h3>
		<textarea readonly="readonly" class="ynFRaising_boxCode" id="box_code"><iframe src="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array('route' => 'ynfundraising_general', 'action' => 'campaign-badge', 'campaignId' => $this->campaign->getIdentity(), 'status' => 3));?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true">;</iframe>
		</textarea>
		<h3><?php echo $this->translate("Options to show")?>:</h3>
		<input checked="true" type="checkbox" onchange="changeDonate(this)" /> <?php echo $this->translate("Donate Button")?> <br />
		<input checked="true" type="checkbox" onchange="changeDonors(this)" /> <?php echo $this->translate("Donors")?>
	</div>

	<div class="ynFRaising_campaign_PricePromote ynFRaising_subProperty">
		<?php echo $this->htmlLink($this->campaign->getHref(), $this->string()->truncate($this->campaign->getTitle(), 28), array('title' => $this->string()->stripTags($this->campaign->getTitle()), 'class' => 'ynFRaising_campaignTitle','target'=> '_blank')) ?>
		<p class="ynFRaising_ownerStat">
			<?php echo $this->translate("Created by");?>
			<a target="_blank" href="<?php echo $this->campaign->getOwner()->getHref()?>"><?php echo $this->campaign->getOwner()->getTitle();?> </a>
		</p>
		<p class="ynFRaising_ownerStat ynFRaising_statictis">
				<?php echo $this->translate(array('%s donor','%s donors',$this->campaign->getTotalDonors()),$this->campaign->getTotalDonors() );
					echo " - ".$this->translate(array('%s like ','%s likes', $this->campaign->like_count), $this->campaign->like_count);
					echo " - ".$this->translate(array('%s view','%s views',$this->campaign->view_count),$this->campaign->view_count);
				?>
		</p>

		<div class ='ynFRaising_campaign_photoColRight'>
			<a target="_blank" href="<?php echo $this->campaign->getHref()?>"><?php echo $this->itemPhoto($this->campaign, 'thumb.profile') ?></a>
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

	   <?php if($this->campaign->expiry_date && $this->campaign->expiry_date != "0000-00-00 00:00:00" && $this->campaign->expiry_date != "1970-01-01 00:00:00" && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS): ?>
			<div class="ynfundraising-time">
				<img src="" class="ynfundraising_timeClockIcon"/>
				<span class="ynfundraising_timeInner"><?php echo $this->campaign->getLimited();?> </span>
			</div>
		<?php endif;?>

	   <div class="ynfundraising_donors ynFRaising_DonorPromote" id="donors">
			<div class="ynFRaising_thumbavatarDonors">
				<?php if (count($this->donors) > 0): ?>
					<?php foreach( $this->donors as $donor ):
						if($donor->user_id > 0):
							$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id )?>
							<span>
							<?php if(Engine_Api::_()->getApi('core', 'ynfundraising')->getLatestAnonymous($donor->user_id, $this->campaign->campaign_id)->is_anonymous == 0):?>
								<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(),'target'=> '_blank')) ?>
							<?php else: ?>
								<a href="javascript:void(0);" >
									<img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png"
										class="thumb_icon item_photo_user item_nophoto"
										title='<?php echo $this->translate("Anonymous")?>'>
								</a>
							</span>
							<?php endif; ?>
						<?php else: ?>
							<?php
							$title = $this->translate("Anonymous");
							if(Engine_Api::_()->getApi('core', 'ynfundraising')->getGuestAnonymous($donor->guest_name, $donor->guest_email, $this->campaign->campaign_id)->is_anonymous == 0):
								$title = ($donor->guest_name == "")?$this->translate("Guest"):$donor->guest_name;
							?>
							<?php endif;?>
							<a href="javascript:void(0);" >
								<img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png"
									class="thumb_icon item_photo_user item_nophoto"
									title='<?php echo $title ?>' >
							</a>
						<?php endif; ?>
					 <?php endforeach; ?>
				 <?php endif; ?>
			</div>
		</div>

		<p class="ynfundraising_campaign_description">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->campaign->short_description), 115);?>
		</p>
		<?php if($this->campaign->published == 1 && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS):?>
			<div class="ynfundraising-donate" id="donate">
				<div id="sign_now">
					<?php
							echo $this->htmlLink(
								array('route' => 'ynfundraising_extended', 'controller' => 'donate', 'action' => 'index', 'campaign_id' => $this->campaign->getIdentity()),
								$this->translate('Donate'),
								array('class' => '', 'target'=> '_blank')
						);
						?>
				</div>
			</div>
		<?php endif;?>
	</div>
</div>
<script type="text/javascript">
    var donate = 1;
    var donor = 2;
    var status = 3;
	var changeDonate = function(obj)
	{
		if($('donate') !== null && $('donate') !== undefined)
		{
			if(obj.checked == false)
			{
				$('donate').hide();
				donate = 0;
			}
			else
			{
				$('donate').show();
				donate = 1;
			}
		}
		status = donor + donate;
		var html = '<iframe src="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array('route' => 'ynfundraising_general', 'action' => 'campaign-badge', 'campaignId' => $this->campaign->getIdentity()));?>/status/'+ status +'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true">;</iframe>';
		$('box_code').value = html;
	}
	var changeDonors = function(obj)
	{
		if($('donors') !== null && $('donors') !== undefined)
		{
			if(obj.checked == false)
			{
				$('donors').hide();
				donor = 0;
			}
			else
			{
				$('donors').show();
				donor = 2;
			}
		}
		status = donor + donate;
		var html = '<iframe src="<?php echo "http://".$_SERVER['SERVER_NAME'].$this->url(array('route' => 'ynfundraising_general', 'action' => 'campaign-badge', 'campaignId' => $this->campaign->getIdentity()));?>/status/'+ status +'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true">;</iframe>';
		$('box_code').value = html;
	}
</script>