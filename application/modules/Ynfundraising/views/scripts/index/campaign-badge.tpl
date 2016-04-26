<div class="ynFRaising_campaign_PricePromote ynFRaising_subProperty">
	<?php echo $this->htmlLink($this->campaign->getHref(), $this->string()->truncate($this->campaign->getTitle(), 28), array('title' => $this->string()->stripTags($this->campaign->getTitle()), 'class' => 'ynFRaising_campaignTitle','target'=> '_blank')) ?>
	<p class="ynFRaising_ownerStat">
		<?php echo $this->translate("Created by ");?>
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


   <?php if($this->status == 3 || $this->status == 2):?>
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
	<?php endif;?>

	<p class="ynfundraising_campaign_description">
		<?php echo $this->string()->truncate($this->string()->stripTags($this->campaign->short_description), 120);?>
	</p>
	<?php if($this->campaign->published == 1 && $this->campaign->status == Ynfundraising_Plugin_Constants::CAMPAIGN_ONGOING_STATUS && ($this->status == 3 || $this->status == 1)):?>
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

<style type="text/css">
/* PROMOTE CAMPAIGN */
.ynFRaising_PromoteWrapper
{
	width: 540px;
	padding: 20px 0px 20px 20px;
}
.ynFRaising_PromoteWrapper .ynFRaising_donateCode > h3, .ynFRaising_campaign_PricePromote
{
	margin: 5px 0px 8px 0px;
}
.ynFRaising_PromoteWrapper .ynFRaising_donateCode > h3
{
	border-bottom: 1px #DFDFDF solid;
	padding: 0px 0px 5px 0px;
	font-size: 16px;
	color: #333;
	font-weight: normal;
}
.ynFRaising_campaign_PricePromote
{
	float: left;
}
.ynFRaising_donateCode
{
    margin-right: 30px;
    width: 290px;
}
.ynFRaising_campaign_PricePromote
{
	width: 195px;
}
.ynFRaising_DonorPromote
{
	margin-top: 5px;
}
a.ynFRaising_campaignTitle:link, a.ynFRaising_campaignTitle:visited, .ynfundraising_campaign_description
{
	color: #FFF;
}
a.ynFRaising_campaignTitle:link, a.ynFRaising_campaignTitle:visited
{
	display: block;
	font-size: 15px;
	font-weight: 700;
	padding: 20px 0 7px 5px;
	text-decoration: none;
}
.ynFRaising_campaign_PricePromote
{
	background-color: #216DA1;
}
.ynFRaising_PromoteWrapper .ynFRaising_donateCode > h3, .ynFRaising_campaign_PricePromote {
    margin: 5px 0 8px;
}
.ynFRaising_subProperty {
    padding: 0 5px;
}
.ynFRaising_ownerStat, .ynFRaising_ownerStat > a:link, .ynFRaising_ownerStat > a:visited {
    color: #A3C2D7;
    font-size: 13px;
    text-decoration: none;
}
.ynFRaising_ownerStat 
{
	margin: 0;
    padding-left: 5px;
}
.ynFRaising_statictis {
    padding-bottom: 10px;
}
.ynFRaising_campaign_photoColRight > a > img {
    width: 99%;
}
img.main, img.thumb_normal, img.thumb_profile, img.thumb_icon {
    border: 1px solid #DDDDDD;
}
img.thumb_profile {
    max-height: 400px;
    max-width: 200px;
}
div.ynFRaising_CampParentPriceRaised {
    margin-bottom: 5px;
}
div, td {
    color: #555555;
    font-size: 10pt;
    text-align: left;
}
.ynFRaising_FeaturePriceRaised > div, .ynFRaising_DonorsLabel, .ynfundraising-time, .ynFRaising_CampParentPriceRaised > div {
    font-weight: 700;
}
.ynFRaising_FeaturePriceRaised > div, .ynfundraising-info, .ynfundraising_donors > div:first-child, .ynfundraising-time, .ynFRaising_CampParentPriceRaised > div {
    color: #FFFFFF;
}
.ynFRaising_FeaturePriceRaised > div, .ynfundraising-info, .ynfundraising-time, .ynFRaising_CampParentPriceRaised > div {
    text-align: center;
}
.ynFRaising_FeatureRaisedOf, .ynFRaising_DonorsLabel {
    font-size: 14px;
}
.ynfundraising-highligh-detail {
    color: #FFFFFF;
    font-size: 12px;
}
.ynfundraising-highligh-detail .meter-wrap-l {
    height: 22px;
    margin: 0 auto;
    position: relative;
    width: 170px;
}
.ynfundraising-highligh-detail .meter-wrap-l {
    background-position: 0 -2px;
    background-repeat: no-repeat;
    padding-left: 3px;
}
.ynfundraising-highligh-detail .meter-wrap-r {
    background-position: 100% -55px;
    background-repeat: no-repeat;
    padding-right: 2px;
    width: 171px;
}

.ynfundraising-highligh-detail .meter-value {
    background: url("<?php echo $this->baseUrl();?>/application/modules/Ynfundraising/externals/images/nl-s.jpg") no-repeat scroll 0 0 transparent;
    color: #FFFFFF;
    font-size: 9px;
    font-weight: bold;
    height: 15px;
    padding-top: 2px;
    text-align: center;
}
.ynfundraising-highligh-detail .meter-wrap-l, .ynfundraising-highligh-detail .meter-wrap-r, .ynfundraising-highligh-detail .meter-wrap {
    background: url("<?php echo $this->baseUrl();?>/application/modules/Ynfundraising/externals/images/nl-sm.png") repeat-x scroll 0 0 transparent;
}
.ynfundraising-highligh-detail .meter-wrap {
    background-position: 0 -27px;
    height: 22px;
    padding-top: 3px;
    width: 171px;
}
.ynfundraising_campaign_description {
    padding: 0 0 15px;
    text-align: justify;
    margin: 0;
}
.ynfundraising-donate {
    background: url("<?php echo $this->baseUrl();?>/application/modules/Ynfundraising/externals/images/fr.png?c=20") no-repeat scroll 0 -169px transparent;
    height: 48px;
    margin: 0 20px;
    padding-bottom: 20px;
}
.ynfundraising-donate a:link, .ynfundraising-donate a:visited {
    color: #003366;
    display: block;
    font-size: 14px;
    font-style: italic;
    font-weight: bold;
    line-height: 28px;
    margin-left: 45px;
    text-decoration: none;
}
</style>