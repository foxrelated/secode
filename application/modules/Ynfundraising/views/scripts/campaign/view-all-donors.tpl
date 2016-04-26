<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>
<?php if($this->campaign):?>
<div class="layout_left">
	<?php echo $this->htmlLink($this->campaign->getHref(), $this->translate("Back to Campaign"),array('class'=>'buttonlink ynFRaising_icon_back'))?>
	<h3>
		<?php echo $this->translate(array("%s Donor With","%s Donors With",$this->donors->getTotalItemCount()),$this->donors->getTotalItemCount())?>
	</h3>
</div>
<div class="ynfundraising_create_right_menu">
	<div class="quicklinks" >
		<ul class="navigation ynfundraising_quicklinks_menu">
			<li>
				<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-donors', 'campaignId' => $this->campaign->getIdentity(),'route'=>'ynfundraising_extended'),$this->translate("Donors"), array('class'=>'active'))?>
			</li>
			<li>
				<?php echo $this->htmlLink(array('controller'=>'campaign','action'=>'view-all-supporters', 'campaignId' => $this->campaign->getIdentity(),'route'=>'ynfundraising_extended'),$this->translate("Supporters"))?>
			</li>
		</ul>
	</div>
</div>
<?php else:?>
<div class="layout_left" style="padding-bottom: 10px">
	<h3>
		<?php echo $this->translate("All Donors")?>
	</h3>
</div>
<?php endif;?>
<?php echo $this->form->render($this);?>
<?php if(count($this->donors) > 0):?>
<ul class="ynFRaising_viewGeneralUL ynFRaising_viewAllUL">
<?php foreach( $this->donors as $donor ):
  	 if($donor->user_id > 0):
  	$user = Engine_Api::_ ()->getItem ( 'user', $donor->user_id )?>
    <li>
    	<?php if(!$this->campaign):
   			$total = Engine_Api::_()->getApi('core', 'ynfundraising')->getTotalCampaignForDonor($donor->user_id);?>
   			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(), 'class' => 'ynFRaising_LRH3ULLi_thumb')) ?>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('title'=>$user->getTitle())) ?></div>
				<div class='ynFRaising_LRH3ULLi_date'><?php echo $this->translate(array("Donated in %s campaign", "Donated in %s campaigns", $total), $total);?></div>
			</div>
		<?php else: ?>
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
	  <?php endif;?>
    </li>
  <?php else:?>
  	 <li>
		<?php if(Engine_Api::_()->getApi('core', 'ynfundraising')->getGuestAnonymous($donor->guest_name, $donor->guest_email, $this->campaign->campaign_id)->is_anonymous == 0):?>
			<a href="javascript:;" class="ynFRaising_LRH3ULLi_thumb"><img src="./application/modules/User/externals/images/nophoto_user_thumb_icon.png" class="thumb_icon item_photo_user item_nophoto"></a>
			<div class='ynFRaising_LRH3ULLi_info'>
				<div class='ynFRaising_LRH3ULLi_name'><?php echo ($donor->guest_name == "")?$this->translate("Guests"):$donor->guest_name; ?></div>
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
 <?php elseif($this->formValues['name']): ?>
	 	<?php if($this->campaign):?>
	    <div class="tip">
	      <span>
	        <?php echo $this->translate('This campaign does not have any donors that match your search criteria.');?>
	      </span>
	    </div>
	    <?php else: ?>
	    	<div class="tip">
	      <span>
	        <?php echo $this->translate('There are no donors that match your search criteria.');?>
	      </span>
	    </div>
	    <?php endif; ?>
  <?php else: ?>
  	<?php if($this->campaign):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This campaign does not have any donors.');?>
      </span>
    </div>
    <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no donors.');?>
      </span>
    </div>
    <?php endif; ?>
  <?php endif; ?>
 <?php echo $this->paginationControl($this->donors, null, null,array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>

<script type="text/javascript">
en4.core.runonce.add(function(){
    if($('name')){
      new OverText($('name'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
 });

</script>