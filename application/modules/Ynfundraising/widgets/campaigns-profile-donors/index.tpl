<?php if (count ($this->donors) > 0): ?>
<ul class="ynFRaising_LRH3ULLi">
  <?php foreach( $this->donors as $donor ):
    if($donor->user_id > 0):
  	$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id )?>
    <li>
		<?php if(Engine_Api::_()->getApi('core', 'ynfundraising')->getLatestAnonymous($donor->user_id, $this->campaign->campaign_id)->is_anonymous == 0):?>
			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(), 'class' => 'ynFRaising_LRH3ULLi_thumb')) ?>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title'=>$user->getTitle())) ?></div>
				<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->currencyfund($donor->total_amount, $this->campaign->currency);?></div>
			</div>
		<?php else:?>
			<a href="javascript:;" class="ynFRaising_LRH3ULLi_thumb"><img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png" class="thumb_icon item_photo_user item_nophoto"></a>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo $this->translate("Anonymous")?></div>
				<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->currencyfund($donor->total_amount, $this->campaign->currency);?></div>
			</div>
	   	<?php endif;?>
    </li>
  <?php else:?>
  	 <li>
		<?php if(Engine_Api::_()->getApi('core', 'ynfundraising')->getGuestAnonymous($donor->guest_name, $donor->guest_email, $this->campaign->campaign_id)->is_anonymous == 0):?>
			<a href="javascript:;" class="ynFRaising_LRH3ULLi_thumb"><img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png" class="thumb_icon item_photo_user item_nophoto"></a>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo ($donor->guest_name == "")?$this->translate("Guest"):$donor->guest_name; ?></div>
				<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->currencyfund($donor->total_amount, $this->campaign->currency);?></div>
			</div>
		<?php else:?>
			<a href="javascript:;" class="ynFRaising_LRH3ULLi_thumb"><img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png" class="thumb_icon item_photo_user item_nophoto"></a>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo $this->translate("Anonymous")?></div>
				<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->currencyfund($donor->total_amount, $this->campaign->currency);?></div>
			</div>
	   	<?php endif;?>
    </li>
  <?php endif;?>
  <?php endforeach; ?>
</ul>
  <?php endif; ?>
<div class="ynFRaising_rightColViewAll ynFRaising_viewAll">
	<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-donors', 'campaignId' => $this->campaign->getIdentity(),'route'=>'ynfundraising_extended'), "<span>&rsaquo;</span>".$this->translate("View All"))?>
</div>